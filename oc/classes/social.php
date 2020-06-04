<?php
/**
 * Social auth class
 *
 * @package    OC
 * @category   Core
 * @author     Chema <chema@open-classifieds.com>, Slobodan <slobodan@open-classifieds.com>
 * @copyright  (c) 2009-2013 Open Classifieds Team
 * @license    GPL v3
 */

use DirkGroenen\Pinterest\Pinterest;

class Social {

    public static function get()
    {
        $config = json_decode(core::config('social.config'), TRUE);

        if (!is_array($config))
        {
            return [];
        }

        // limit Facebook permission scope
        $config['providers']['Facebook']['scope'] = ['email', 'public_profile'];

        // limit Google permission scope
        $config['providers']['Google']['scope'] = 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email';

        return $config;
    }

    public static function get_providers()
    {
        $providers = self::get();

        return (isset($providers['providers']))?$providers['providers']:array();
    }

    public static function enabled_providers()
    {
        $providers         = self::get();
        $enabled_providers = array();

        if (isset($providers['providers']))
        {
            foreach ($providers['providers'] as $k => $provider) {
                if ($provider['enabled'])
                {
                    $enabled_providers[$k] = $providers['providers'][$k];
                }
            }
        }

        return $enabled_providers;
    }

    public static function include_vendor()
    {
        require_once Kohana::find_file('vendor', 'hybridauth/hybridauth/Hybrid/Auth','php');
        require_once Kohana::find_file('vendor', 'hybridauth/hybridauth/Hybrid/Endpoint','php');
        require_once Kohana::find_file('vendor', 'hybridauth/hybridauth/Hybrid/Logger','php');
        require_once Kohana::find_file('vendor', 'hybridauth/hybridauth/Hybrid/Exception','php');
        // require_once Kohana::find_file('vendor', 'hybridauth/vendor/autoload','php'); will uncomment on upgrade
    }

    public static function include_vendor_twitter()
    {
        require_once Kohana::find_file('vendor/', 'codebird-php/codebird');
    }

    public static function include_vendor_instagram()
    {
        require_once Kohana::find_file('vendor/', 'Instagram-API/vendor/autoload');
    }

    public static function include_vendor_pinterest()
    {
        require_once Kohana::find_file('vendor/', 'Pinterest/vendor/dirkgroenen/pinterest-api-php/autoload');
    }

    public static function social_post_featured_ad(Model_Ad $ad)
    {
        if($ad->status == Model_Ad::STATUS_PUBLISHED AND core::config('advertisement.social_post_only_featured') == TRUE)
        {
            if(core::config('advertisement.twitter'))
                self::twitter($ad);

            if(core::config('advertisement.facebook'))
                self::facebook($ad);

            if(core::config('advertisement.instagram'))
                self::instagram($ad);

            if(core::config('advertisement.pinterest'))
                self::pinterest($ad);
        }
    }

    public static function post_ad(Model_Ad $ad)
    {
        if($ad->status == Model_Ad::STATUS_PUBLISHED AND core::config('advertisement.social_post_only_featured') == FALSE)
        {
            if(core::config('advertisement.twitter'))
                self::twitter($ad);

            if(core::config('advertisement.facebook'))
                self::facebook($ad);

            if(core::config('advertisement.instagram'))
                self::instagram($ad);

            if(core::config('advertisement.pinterest'))
                self::pinterest($ad);

        }
    }

    public static function pinterest(Model_Ad $ad)
    {
        $file = $ad->get_first_image('image');

        if($file !== NULL)
        {
            self::include_vendor_pinterest();

            $url_ad = Route::url('ad', array('category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle));

            $caption = $ad->title;

            if($ad->category->id_category_parent != 1 AND $ad->category->parent->loaded())
                $caption .= ' - '.$ad->category->parent->name;

            // Pinterest caption characters limit is 500
            $caption .= '-'.$ad->category->name;

            if($ad->id_location != 1 AND $ad->location->loaded())
            {
                if($ad->location->id_location_parent != 1 AND $ad->location->parent->loaded())
                    $caption .= ', '.$ad->location->parent->name;

                $caption .= '-'.$ad->location->name;
            }

            if($ad->price>0)
                $caption .= ', '.html_entity_decode(i18n::money_format($ad->price, $ad->currency()));

            $caption .= ' - '.$url_ad;

            $pinterest = new Pinterest(core::config('advertisement.pinterest_app_id'), core::config('advertisement.pinterest_app_secret'));

            try
            {
                $loginurl = $pinterest->auth->getLoginUrl(core::config('general.base_url'), array('read_public', 'write_public'));

                $pinterest->auth->setOAuthToken(core::config('advertisement.pinterest_access_token'));
                $me = $pinterest->users->me();

                $pinterest->pins->create(array(
                    "note"          => $caption,
                    "image_url"     => core::imagefly($file,400,600),
                    "board"         => core::config('advertisement.pinterest_board')
                ));
            } catch (Exception $e) {
                Kohana::$log->add(Log::ERROR, 'Pinterest auto-post: ' . $e->getMessage());
            }
        }
    }

    public static function instagram(Model_Ad $ad)
    {

        if(!method_exists('Core','yclas_url') AND core::config('advertisement.instagram_username')!='' AND core::config('advertisement.instagram_password')){

            $file = $ad->get_first_image('image');

            if($file !== NULL)
            {
                self::include_vendor_instagram();

                $url_ad = Route::url('ad', array('category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle));

                $caption = $ad->title;

                if($ad->category->id_category_parent != 1 AND $ad->category->parent->loaded())
                    $caption .= ' - '.$ad->category->parent->name;

                // Instagram caption characters limit is 2200 !!
                $caption .= '-'.$ad->category->name;

                if($ad->id_location != 1 AND $ad->location->loaded())
                {
                    if($ad->location->id_location_parent != 1 AND $ad->location->parent->loaded())
                        $caption .= ', '.$ad->location->parent->name;

                    $caption .= '-'.$ad->location->name;
                }

                if($ad->price>0)
                    $caption .= ', '.html_entity_decode(i18n::money_format($ad->price, $ad->currency()));

                $caption .= ' - '.Text::limit_chars(Text::removebbcode($ad->description), 100, NULL, TRUE);
                $caption .= ' - '.$url_ad;
                $caption .= self::GenerateHashtags($ad);

                // image path needs to be the path on the disk
                $img_path = substr_replace(APPPATH, '', -3).$ad->image_path().substr($file, strrpos($file, '/') + 1);

                $i = new \InstagramAPI\Instagram();

                try {
                    $i->login(core::config('advertisement.instagram_username'), core::config('advertisement.instagram_password'));
                    $photo = new \InstagramAPI\Media\Photo\InstagramPhoto($img_path);
                    $i->timeline->uploadPhoto($photo->getFile(), ['caption' => $caption]);
                } catch (Exception $e) {
                    echo 'Error posting to Instagram: ' . $e->getMessage();
                }

            }
        }
    }

    public static function twitter(Model_Ad $ad)
    {
        self::include_vendor_twitter();

        \Codebird\Codebird::setConsumerKey(core::config('advertisement.twitter_consumer_key'), core::config('advertisement.twitter_consumer_secret'));
        $cb = \Codebird\Codebird::getInstance();
        $cb->setToken(core::config('advertisement.access_token'), core::config('advertisement.access_token_secret'));

        $message = Text::limit_chars($ad->title, 17, NULL, TRUE).', ';

        $message .= Text::limit_chars($ad->category->name, 17, NULL, TRUE);

        if($ad->id_location != 1 AND $ad->location->loaded())
        {
            $message .= ' - '.Text::limit_chars($ad->location->name, 17, NULL, TRUE);
        }

        if($ad->price>0)
            $message .= ', '.html_entity_decode(i18n::money_format($ad->price, $ad->currency()));

        $url_ad = Route::url('ad', array('category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle));
        $message .= ' - '.$url_ad;
        $message .= ' - '.self::GenerateHashtags($ad);

        $params = array(
            'status' => $message
        );

        if(isset($ad->latitude) AND $ad->latitude!='' AND isset($ad->longitude) AND $ad->longitude!=''){
            $params['lat'] = $ad->latitude;
            $params['long'] = $ad->longitude;
        }

        $reply = $cb->statuses_update($params);

        if (isset($reply->errors))
        {
            Kohana::$log->add(Log::ERROR, 'Twitter auto-post: ' . $reply->errors[0]->message);
        }
    }

    public static function facebook(Model_Ad $ad)
    {
        $page_access_token = core::config('advertisement.facebook_access_token');
        $page_id = core::config('advertisement.facebook_id');
        $app_secret = core::config('advertisement.facebook_app_secret');

        $appsecret_proof = hash_hmac('sha256', $page_access_token, $app_secret);

        $url_ad = Route::url('ad', array('category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle));

        $description = $ad->description;

        if($ad->price>0)
            $description .= ' - '.__('Price').': '.html_entity_decode(i18n::money_format($ad->price, $ad->currency()));

        $message = $ad->title;

        if($ad->category->id_category_parent != 1 AND $ad->category->parent->loaded())
            $message .= ', '.$ad->category->parent->name;

        $message .= ' - '.$ad->category->name;

        if($ad->id_location != 1)
        {
            if($ad->location->id_location_parent != 1 AND $ad->location->parent->loaded())
                $message .= ', '.$ad->location->parent->name;

            $message .= ' - '.$ad->location->name;
        }

        $data['link'] = $url_ad;
        $data['message'] = $message.' - '.$description.' '.self::GenerateHashtags($ad);
        $data['caption'] = core::config('general.base_url').' | '.core::config('general.site_name');

        $data['access_token'] = $page_access_token;

        $post_url = 'https://graph.facebook.com/'.$page_id.'/feed?appsecret_proof='.$appsecret_proof.'&scope=publish_stream,status_update';
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $post_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $return = curl_exec($ch);
        curl_close($ch);

        if(strpos($return,'error') AND Auth::instance()->logged_in() AND Auth::instance()->get_user()->is_admin())
            Alert::set(Alert::ALERT, $return);

    }

    public static function GetAccessToken()
    {
        if(core::config('advertisement.facebook') == 1
            OR !empty(core::config('advertisement.facebook_app_id'))
            OR !empty(core::config('advertisement.facebook_app_secret'))
            OR !empty(core::config('advertisement.facebook_access_token'))
            OR !empty(core::config('advertisement.facebook_id')))
        {
            $token_url = "https://graph.facebook.com/oauth/access_token?client_id=".core::config('advertisement.facebook_app_id')."&client_secret=".core::config('advertisement.facebook_app_secret')."&grant_type=fb_exchange_token&fb_exchange_token=".core::config('advertisement.facebook_access_token');

            $c = curl_init();
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($c, CURLOPT_URL, $token_url);
            $contents = curl_exec($c);
            $err  = curl_getinfo($c,CURLINFO_HTTP_CODE);
            curl_close($c);

            $paramsfb = null;
            parse_str($contents, $paramsfb);

            $paramsfb = json_decode($contents, true);

            if(isset($paramsfb['access_token']) AND !empty($paramsfb['access_token'])){
                Model_Config::set_value('advertisement','facebook_access_token',$paramsfb['access_token']);
            }
        }
    }

    public static function GenerateHashtags(Model_Ad $ad)
    {
        $hashtag1 = '#'.str_replace([' ', "'", '"', '!', '+', '$', '%', '^', '&', '*', '+', '.', ','], '', core::config('general.site_name'));
        $hashtag2 = '#'.str_replace([' ', "'", '"', '!', '+', '$', '%', '^', '&', '*', '+', '.', ','], '', $ad->category->name);
        $hashtag3 = '#'.str_replace([' ', "'", '"', '!', '+', '$', '%', '^', '&', '*', '+', '.', ','], '', $ad->location->name);

        return $hashtag1.' '.$hashtag2.' '.$hashtag3;
    }

}
