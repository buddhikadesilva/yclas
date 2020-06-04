<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Cron for software updates
 *
 * @author      Chema <chema@open-classifieds.com>
 * @package     Cron
 * @copyright   (c) 2009-2016 Open Classifieds Team
 * @license     GPL v3
 * 
 */
class Cron_Update {


    public static function notify()
    {
        //get latest versions
        Core::get_updates(TRUE);   

        $versions = core::config('versions');

        //version numbers in a key value
        $version_nums = array();
        foreach ($versions as $version=>$values)
            $version_nums[] = $version;

        //notify site owner since theres a newer version
        if (core::VERSION != current($version_nums))
        {
            $message = __('Update your site to latest version');

            return Email::send( core::config('email.notify_email'),
                                core::config('general.site_name'),
                                $message,
                                $message.'<br>'.Route::url('oc-panel',array('controller'=>'update','action'=>'confirm')),
                                core::config('email.notify_email'),
                                core::config('general.site_name'));
        }           

    }

}