<?php
/**
 * Extended functionality for Kohana Valid
 *
 * @package    OC
 * @category   Security
 * @author     Chema <chema@open-classifieds.com>
 * @copyright  (c) 2009-2013 Open Classifieds Team
 * @license    GPL v3
 */

class Valid extends Kohana_Valid{

    /**
     * Check an email address for correct format.
     *
     * @param   string   email address
     * @param   boolean  valid domain with MX and not disposable email
     * @return  boolean
     */
    public static function email($email, $strict = FALSE)
    {
        //get the email to check up, clean it
        $email = filter_var($email,FILTER_SANITIZE_STRING);
        // 1 - check valid email format using RFC 822
        if (filter_var($email, FILTER_VALIDATE_EMAIL)===FALSE)
            return FALSE;

        //strict validation, MX domain and valid domain not disposable
        if ($strict===TRUE)
            return Valid::email_domain($email);

        // wow actually a real email! congrats ;)
        return TRUE;
    }

    /**
     * Validate the domain of an email address by checking if the domain has a
     * valid MX record and is nmot blaklisted as a temporary email
     *
     * @link  http://php.net/checkdnsrr  not added to Windows until PHP 5.3.0
     *
     * @param   string  $email  email address
     * @return  boolean
     */
    public static function email_domain($email)
    {
        if ( ! Valid::not_empty($email))
            return FALSE; // Empty fields cause issues with checkdnsrr()

        $domain = preg_replace('/^[^@]++@/', '', $email);

        if (core::config('general.black_list') == TRUE AND in_array($domain,self::get_banned_domains()))
            return FALSE;

        if (core::config('general.email_domains') !== NULL AND core::config('general.email_domains') != '' AND ! in_array($domain, explode(',', core::config('general.email_domains'))) AND ! in_array($email, Email::get_admins_emails()))
                return FALSE;

        // Check if the email domain has a valid MX record
        return (bool) checkdnsrr($domain, 'MX');
    }


    /**
     * gets the array of not allowed domains for emails, reads from json stores file for 1 week
     * @return array
     * @see banned domains https://github.com/ivolo/disposable-email-domains/blob/master/index.json
     * @return array
     */
    private static function get_banned_domains()
    {
        //where we store the banned domains
        $file = APPPATH.'banned_domains.json';

        //if the json file is not in local or the file exists but is older than 1 week, regenerate the json
        if (!file_exists($file) OR (file_exists($file) AND filemtime($file) < strtotime('-1 week')) )
        {
            $banned_domains = Core::curl_get_contents("https://rawgit.com/ivolo/disposable-email-domains/master/index.json",5);
            if ($banned_domains !== FALSE)
                File::write($file,$banned_domains);
            else
                $banned_domains = File::read($file);
        }
        else
        {
            //get the domains from the file
            $banned_domains = File::read($file);
        }

        $banned_domains = json_decode($banned_domains);

        if (!empty(core::config('general.disallowed_email_domains')))
        {
            $disallowed_email_domains = explode(',', core::config('general.disallowed_email_domains'));

            $banned_domains = $disallowed_email_domains + $banned_domains;
        }

        return $banned_domains;
    }

    /**
     * Checks whether a string is a valid price (negative and decimal numbers allowed).
     *
     * Uses {@link http://www.php.net/manual/en/function.localeconv.php locale conversion}
     * to allow decimal point to be locale specific.
     *
     * @param   string  $str    input string
     * @return  boolean
     */
    public static function price($str)
    {
        // Get the decimal point for the current locale
        list($decimal) = array_values(localeconv());

        if(core::config('general.number_format') == 'BTC'){
            $str = number_format($str,8);
        }

        // A lookahead is used to make sure the string contains at least one digit (before or after the decimal point)
        $result = (bool) preg_match('/^-?+(?=.*[0-9])[0-9]*+'.preg_quote($decimal).'?+[0-9]*+$/D', (string) $str);

        //failsafe using as decimal de '.'
        if ($result===FALSE)
            $result = (bool) preg_match('/^-?+(?=.*[0-9])[0-9]*+.?+[0-9]*+$/D', (string) $str);

        return $result;
    }

    /**
     * Checks whether a string has banned words.
     *
     * @param  string  $str    input string
     * @return boolean
     */
    public static function no_banned_words($str)
    {
        if (core::config('advertisement.banned_words') != NULL AND core::config('advertisement.banned_words') != '')
        {
            $banned_words = explode(',', core::config('advertisement.banned_words'));
            $banned_words = array_map('trim', $banned_words);

            // with provided array of baned words find words in string
            foreach ($banned_words as $word)
            {
                if (strpos($str, $word) !== FALSE)
                    return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * Checks if a phone number is valid.
     *
     * @param   string  $number     phone number to check
     * @param   array   $lengths
     * @return  boolean
     */
    public static function phone($number, $lengths = NULL)
    {
        if ( ! is_array($lengths))
        {
            $lengths = array(7, 9, 10, 11, 12, 13);
        }

        // Remove all non-digit characters from the number
        $number = preg_replace('/\D+/', '', $number);

        // Check if the number is within range
        return in_array(strlen($number), $lengths);
    }

    /**
     * Checks if a vat number is valid.
     *
     * @param   string  $number     vat number to check
     * @param   array   $lengths
     * @return  boolean
     */
    public static function vies($validation, $fields)
    {
        if (! empty($validation['cf_vatnumber']) AND ! empty($validation['cf_vatcountry']))
        {
            if ( ! euvat::verify_vies($validation['cf_vatnumber'], $validation['cf_vatcountry']))
            {
                return FALSE;
            }
        }

        TRUE;
    }
}
