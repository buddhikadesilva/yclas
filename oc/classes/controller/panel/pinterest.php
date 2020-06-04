<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Panel_Pinterest extends Auth_Controller {

    public function action_token()
    {
        Social::include_vendor_pinterest();

        if(Core::get('code'))
        {
            $pinterest = new \DirkGroenen\Pinterest\Pinterest(core::config('advertisement.pinterest_app_id'), core::config('advertisement.pinterest_app_secret'));

            $token = $pinterest->auth->getOAuthToken(Core::get('code'));

            Model_Config::set_value('advertisement', 'pinterest_access_token', $token->access_token);
        }

        HTTP::redirect(Route::url('oc-panel', ['controller' => 'settings', 'action' => 'form']));
    }

}//end of controller
