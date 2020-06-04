<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Controller SETTINGS contains all basic configurations displayed to Admin.
 */


class Controller_Panel_Settings extends Auth_Controller {

    public function before($template = NULL)
    {
        parent::before();

       Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Settings'))->set_url(Route::url('oc-panel',array('controller' => 'settings', 'action' => 'general'))));

        $this->template->styles  = array('css/pnotify.custom.min.css' => 'screen');

        $this->template->scripts['footer'][]= 'js/jquery.validate.min.js';
        $this->template->scripts['footer'][]= 'js/pnotify.custom.min.js';
        $this->template->scripts['footer'][]= '/js/oc-panel/settings.js';
    }

    public function action_index()
    {
        HTTP::redirect(Route::url('oc-panel',array('controller'  => 'settings','action'=>'general')));
    }

    /**
     * Contains all data releated to new advertisment optional form inputs,
     * captcha, uploading text file
     * @return [view] Renders view with form inputs
     */
    public function action_form()
    {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Advertisement')));
        $this->template->title = __('Advertisement');

        // all form config values
        $advertisement = new Model_Config();
        $config = $advertisement->where('group_name', '=', 'advertisement')->find_all();
        $this->template->styles  = array(
            '//cdn.jsdelivr.net/bootstrap.tagsinput/0.3.9/bootstrap-tagsinput.css' => 'screen',
            'css/pnotify.custom.min.css' => 'screen');
        $this->template->scripts['footer'][] = '//cdn.jsdelivr.net/bootstrap.tagsinput/0.3.9/bootstrap-tagsinput.min.js';

        // save only changed values
        if($this->request->post())
        {
            $validation =   Validation::factory($this->request->post())
            ->rule('advertisements_per_page', 'not_empty')
            ->rule('advertisements_per_page', 'digit')
            ->rule('feed_elements', 'not_empty')
            ->rule('feed_elements', 'digit')
            ->rule('map_elements', 'not_empty')
            ->rule('map_elements', 'digit')
            ->rule('sort_by', 'not_empty')
            ->rule('ads_in_home', 'not_empty')
            ->rule('ads_in_home', 'range', array(':value', 0, 4))
            ->rule('login_to_post', 'range', array(':value', 0, 1))
            ->rule('only_admin_post', 'range', array(':value', 0, 1))
            ->rule('expire_date', 'not_empty')
            ->rule('expire_date', 'digit')
            ->rule('parent_category', 'range', array(':value', 0, 1))
            ->rule('map_pub_new', 'range', array(':value', 0, 1))
            ->rule('captcha', 'range', array(':value', 0, 1))
            ->rule('address', 'range', array(':value', 0, 1))
            ->rule('phone', 'range', array(':value', 0, 1))
            ->rule('website', 'range', array(':value', 0, 1))
            ->rule('location', 'range', array(':value', 0, 1))
            ->rule('price', 'range', array(':value', 0, 1))
            ->rule('upload_file', 'range', array(':value', 0, 1))
            ->rule('num_images', 'not_empty')
            ->rule('num_images', 'digit')
            ->rule('contact', 'range', array(':value', 0, 1))
            ->rule('login_to_contact', 'range', array(':value', 0, 1))
            ->rule('qr_code', 'range', array(':value', 0, 1))
            ->rule('map', 'range', array(':value', 0, 1))
            ->rule('count_visits', 'range', array(':value', 0, 1))
            ->rule('related', 'not_empty')
            ->rule('related', 'digit')
            ->rule('map_zoom', 'digit')
            ->rule('center_lat', 'regex', array(':value', '/^-?+(?=.*[0-9])[0-9]*+'.preg_quote('.').'?+[0-9]*+$/D'))
            ->rule('center_lon', 'regex', array(':value', '/^-?+(?=.*[0-9])[0-9]*+'.preg_quote('.').'?+[0-9]*+$/D'))
            ->rule('reviews', 'range', array(':value', 0, 1))
            ->rule('reviews_paid', 'range', array(':value', 0, 1))
            ->rule('auto_locate_distance', 'not_empty')
            ->rule('auto_locate_distance', 'digit');

            if ($validation->check()) {
                foreach ($config as $c)
                {
                    $config_res = $this->request->post($c->config_key);

                    if(isset($config_res))
                    {
                        if($config_res !== $c->config_value)
                        {
                            $c->config_value = $config_res;
                            try {
                                $c->save();
                            } catch (Exception $e) {
                                echo $e;
                            }
                        }
                    }
                }
            }
            else {
                $errors = $validation->errors('config');

                foreach ($errors as $error)
                    Alert::set(Alert::ALERT, $error);

                $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'form')));
            }

            Alert::set(Alert::SUCCESS, __('Advertisement Configuration updated'));
            $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'form')));

        }

        if(core::config('advertisement.pinterest'))
        {
            Social::include_vendor_pinterest();

            $pinterest = new \DirkGroenen\Pinterest\Pinterest(core::config('advertisement.pinterest_app_id'), core::config('advertisement.pinterest_app_secret'));

            $pinterest_login_url = $pinterest->auth->getLoginUrl(Route::url('oc-panel', ['controller' => 'pinterest', 'action' => 'token']), ['read_public', 'write_public']);
        }

        $this->template->content = View::factory('oc-panel/pages/settings/advertisement', ['config' => $config, 'pinterest_login_url' => $pinterest_login_url ?? NULL]);
    }

    public function action_emailtest()
    {
        if (Email::send(core::config('email.notify_email'),core::config('email.notify_name'),
                        'Test Email Sent','Test Email Sent from email service '.core::config('email.service'),
                        core::config('email.notify_email'),core::config('email.notify_name')))
            Alert::set(Alert::SUCCESS, __('Email succesfully sent.'));
        else
            Alert::set(Alert::ALERT, __('Email was not sent, please review your email configuration.'));

        $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'email')));
    }

    /**
     * Email configuration
     * @return [view] Renders view with form inputs
     */
    public function action_email()
    {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Email')));
        $this->template->title = __('Email');

        // all form config values
        $emailconf = new Model_Config();
        $config = $emailconf->where('group_name', '=', 'email')->find_all();

        // save only changed values
        if($this->request->post())
        {
            $validation =   Validation::factory($this->request->post())
            ->rule('notify_email', 'email')
            ->rule('notify_name', 'not_empty')
            ->rule('new_ad_notify', 'range', array(':value', 0, 1))
            ->rule('smtp_ssl', 'range', array(':value', 0, 1))
            ->rule('smtp_port', 'digit')
            ->rule('smtp_auth', 'range', array(':value', 0, 1));

            if ($validation->check()) {
                foreach ($config as $c)
                {
                    $config_res = $this->request->post($c->config_key);

                    if($config_res != $c->config_value)
                    {
                        $c->config_value = $config_res;
                        try {
                            $c->save();
                        } catch (Exception $e) {
                            throw HTTP_Exception::factory(500,$e->getMessage());
                        }
                    }
                }
            }
            else {
                $errors = $validation->errors('config');

                foreach ($errors as $error)
                    Alert::set(Alert::ALERT, $error);

                $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'email')));
            }

            // Cache::instance()->delete_all();
            Alert::set(Alert::SUCCESS, __('Email Configuration updated'));
            $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'email')));
        }

        $this->template->content = View::factory('oc-panel/pages/settings/email', array('config'=>$config));
    }

    /**
     * All general configuration related with configuring site.
     * @return [view] Renders view with form inputs
     */
    public function action_general()
    {
        $this->template->styles  = array(
            '//cdn.jsdelivr.net/bootstrap.tagsinput/0.3.9/bootstrap-tagsinput.css' => 'screen',
            'css/pnotify.custom.min.css' => 'screen');
        $this->template->scripts['footer'][] = '//cdn.jsdelivr.net/bootstrap.tagsinput/0.3.9/bootstrap-tagsinput.min.js';

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('General')));
        $this->template->title = __('General');

        // all form config values
        $generalconfig = new Model_Config();
        $config = $generalconfig->where('group_name', '=', 'general')->or_where('group_name', '=', 'i18n')->find_all();

        // config general array
        foreach ($config as $c)
        {
            $forms[$c->config_key] = $forms[$c->config_key] = array('key'=>$c->group_name.'['.$c->config_key.'][]', 'id'=>$c->config_key, 'value'=>$c->config_value);
        }

        //not updatable fields
        $do_nothing = array('base_url','menu','locale','allow_query_language','charset','translate','ocacu','minify','subscribe', 'blog', 'faq', 'forums', 'messaging', 'black_list', 'auto_locate', 'social_auth', 'adblock','subscriptions', 'add_to_home_screen', 'cron', 'hide_homepage_categories', 'algolia_powered_by_enabled');

        // save only changed values
        if($this->request->post())
        {
            foreach ($this->request->post('general') as $k => $v)
                $this->request->post('general_'.$k, $v[0]);

            $validation =   Validation::factory($this->request->post())
                            ->rule('general_maintenance', 'range', array(':value', 0, 1))
                            ->rule('general_private_site', 'range', array(':value', 0, 1))
                            ->rule('general_disallowbots', 'range', array(':value', 0, 1))
                            ->rule('general_cookie_consent', 'range', array(':value', 0, 1))
                            ->rule('general_site_name', 'not_empty')
                            ->rule('general_moderation', 'not_empty')
                            ->rule('general_moderation', 'range', array(':value', 0, 5))
                            ->rule('general_blog', 'range', array(':value', 0, 1))
                            ->rule('general_forums', 'range', array(':value', 0, 1))
                            ->rule('general_faq', 'range', array(':value', 0, 1))
                            ->rule('general_black_list', 'range', array(':value', 0, 1))
                            ->rule('general_search_by_description', 'range', array(':value', 0, 1))
                            ->rule('general_recaptcha_active', 'range', array(':value', 0, 1));

            if ($validation->check()) {
                //save general
                foreach ($config as $c)
                {
                    $config_res = $this->request->post();

                    if ( ! in_array($c->config_key, $do_nothing)
                        AND ($config_res[$c->group_name][$c->config_key][0] != $c->config_value
                            OR Kohana::$_POST_ORIG['general']['html_head'][0] != $c->config_value
                            OR Kohana::$_POST_ORIG['general']['html_footer'][0] != $c->config_value))
                    {
                        if ($c->config_key == 'html_head' OR $c->config_key == 'html_footer')
                            $c->config_value = Kohana::$_POST_ORIG[$c->group_name][$c->config_key][0];
                        else
                            $c->config_value = $config_res[$c->group_name][$c->config_key][0];

                        if ($c->config_key == 'maintenance' AND $c->config_value == 0)
                            Alert::del('maintenance');

                        if ($c->config_key == 'subscriptions_expire' AND $c->config_value == 1 AND Core::config('general.subscriptions') == TRUE)
                        {

                            $plan = new Model_Plan();
                            $plan->where('status','=',1)->find();

                            if (!$plan->loaded())
                            {
                                $url = Route::url('oc-panel',array('controller'=>'plan','action'=>'index'));
                                Alert::set(Alert::INFO, __('Please, <a href="'.$url.'">create a plan</a> first. More information <a href="//docs.yclas.com/membership-plans/#subscription-expire" target="_blank">here</a>'));
                            }
                        }

                        if ($c->config_key == 'sms_auth' AND $c->config_value == 1){

                            if(!empty(Kohana::$_POST_ORIG['general']['sms_clickatell_api'][0])
                                OR (!Kohana::$_POST_ORIG['general']['sms_clickatell_api'][0] == NULL)){

                                $test_sms_auth = SMS::testAPIkey(Kohana::$_POST_ORIG['general']['sms_clickatell_api'][0], Kohana::$_POST_ORIG['general']['sms_clickatell_two_way_phone'][0]);

                                if($test_sms_auth == FALSE){
                                    // disable sms_auth
                                    $c->config_value = 0;
                                    Alert::set(Alert::ALERT, '2 Step SMS Authentication was not enabled. Please configure <a href="//docs.yclas.com/2-step-sms-authentication/">Clickatell</a> to enable 2 Step SMS Authentication!');
                                } else {
                                    Alert::set(Alert::SUCCESS, '2 Step SMS Authentication activated');
                                }
                            } else {
                                $c->config_value = 0;
                                Alert::set(Alert::ALERT, '2 Step SMS Authentication was not enabled. Please configure <a href="//docs.yclas.com/2-step-sms-authentication/">Clickatell</a> to enable 2 Step SMS Authentication!');
                            }
                        }

                        if ($c->config_key == 'private_site' AND $c->config_value == 0)
                            Alert::del('private_site');

                        if ($c->config_key == 'multilingual' AND $c->config_value == 1)
                        {
                            //set i18n.locale as default language to all the ads
                            $query = DB::update('ads')
                                ->set(['locale' => core::config('i18n.locale')])
                                ->where('locale', 'IS', NULL)
                                ->execute();
                        }

                        Model_Config::set_value($c->group_name,$c->config_key,$c->config_value);
                    }

                }
            }
            else {
                $errors = $validation->errors('config');

                foreach ($errors as $error)
                    Alert::set(Alert::ALERT, $error);

                $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'general')));
            }

            Alert::set(Alert::SUCCESS, __('General Configuration updated'));
            $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'general')));
        }

        $this->template->content = View::factory('oc-panel/pages/settings/general', array('forms'=>$forms));
    }

    /**
     * Payment deatails and paypal configuration can be configured here
     * @return [view] Renders view with form inputs
     */
    public function action_payment()
    {

        //delete featured plan
        if (is_numeric(Core::get('delete_plan')))
        {
            Model_Order::delete_featured_plan(Core::get('delete_plan'));
            $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'payment')));
        }

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Payments')));
        $this->template->title = __('Payments');

        // all form config values
        $paymentconf = new Model_Config();
        $config = $paymentconf->where('group_name', '=', 'payment')->find_all();

        // save only changed values
        if($this->request->post())
        {
            if (is_numeric(Core::request('featured_days')) AND is_numeric(Core::request('featured_price')))
            {
                Model_Order::set_featured_plan(Core::request('featured_days'),Core::request('featured_price'),Core::request('featured_days_key'));

                Alert::set(Alert::SUCCESS, __('Featured plan updated'));
                $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'payment')));
            }

            $validation =   Validation::factory($this->request->post())
            ->rule('pay_to_go_on_top', 'not_empty')
            ->rule('pay_to_go_on_top', 'price')
            ->rule('stripe_appfee', 'numeric')
            ->rule('stripe_appfee', 'range', array(':value', 0, 100))
            ->rule('to_featured', 'range', array(':value', 0, 1))
            ->rule('to_top', 'range', array(':value', 0, 1))
            ->rule('sandbox', 'range', array(':value', 0, 1))
            ->rule('paypal_seller', 'range', array(':value', 0, 1))
            ->rule('stock', 'range', array(':value', 0, 1))
            ->rule('authorize_sandbox', 'range', array(':value', 0, 1))
            ->rule('stripe_address', 'range', array(':value', 0, 1));

            //not updatable fields
            $do_nothing = array('featured_days','pay_to_go_on_feature','featured_plans', 'bitpay_token', 'bitpay_private_key', 'bitpay_public_key');

            // VAT country and number is filled
            if(Core::request('vat_country') AND Core::request('vat_number')){
                // if is an eu country check if VAT number is valid
                if (euvat::is_eu_country(Core::request('vat_country')) AND !euvat::verify_vies(Core::request('vat_number'),Core::request('vat_country'))){
                    Alert::set(Alert::ERROR, __('Invalid EU Vat Number, please verify number and country match'));
                    $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'payment')));
                }
                // if is a non-eu country, check if VAT rate is filled and greater than 0
                elseif(!euvat::is_eu_country(Core::request('vat_country')) AND (!Core::request('vat_non_eu') OR Core::request('vat_non_eu') < 0)){
                    Alert::set(Alert::ERROR, __('Please enter a valid VAT rate.'));
                    $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'payment')));
                }
            }

            // Bitpay
            if (Core::request('bitpay_pairing_code') AND Core::request('bitpay_pairing_code') != Core::config('payment.bitpay_pairing_code'))
            {
                $this->redirect(Route::url('oc-panel', array('controller' => 'bitpay', 'action' => 'pair')) . '?code=' . Core::request('bitpay_pairing_code') . '&sandbox=' . Core::request('bitpay_sandbox'));
            }

            if ($validation->check()) {
                foreach ($config as $c)
                {
                    $config_res = $this->request->post($c->config_key);

                    if(!in_array($c->config_key, $do_nothing) AND $config_res != $c->config_value)
                    {
                        if ($c->config_key == 'pay_to_go_on_top')
                            $config_res = str_replace(',', '.', $config_res);

                        $c->config_value = $config_res;
                        try {
                            $c->save();
                        } catch (Exception $e) {
                            throw HTTP_Exception::factory(500,$e->getMessage());
                        }
                    }
                }
            }
            else {
                $errors = $validation->errors('config');

                foreach ($errors as $error)
                    Alert::set(Alert::ALERT, $error);

                $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'payment')));
            }

            Alert::set(Alert::SUCCESS, __('Payments Configuration updated'));
            $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'payment')));
        }

        $pages = array(''=>__('Deactivated'));
        foreach (Model_Content::get_pages() as $key => $value)
            $pages[$value->seotitle] = $value->title;

        $this->template->content = View::factory('oc-panel/pages/settings/payment', array('config'          => $config,
                                                                                           'pages'          => $pages,
                                                                                           'featured_plans' => Model_Order::get_featured_plans()));
    }

    /**
     * Image configuration
     * @return [view] Renders view with form inputs
     */
    public function action_image()
    {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Image')));
        $this->template->title = __('Image');

        // all form config values
        $imageconf = new Model_Config();
        $config = $imageconf->where('group_name', '=', 'image')
                            ->where('config_key','NOT LIKE','aws_%')
                            ->find_all();

        // save only changed values
        if($this->request->post())
        {
            foreach ($this->request->post('image') as $k => $v)
                $this->request->post('image_'.$k, $v[0]);

            $validation =   Validation::factory($this->request->post())
                            ->rule('image_max_image_size', 'not_empty')
                            ->rule('image_max_image_size', 'digit')
                            ->rule('image_height', 'digit')
                            ->rule('image_width', 'not_empty')
                            ->rule('image_width', 'digit')
                            ->rule('image_height_thumb', 'not_empty')
                            ->rule('image_height_thumb', 'digit')
                            ->rule('image_width_thumb', 'not_empty')
                            ->rule('image_width_thumb', 'digit')
                            ->rule('image_quality', 'not_empty')
                            ->rule('image_quality', 'digit')
                            ->rule('image_quality', 'range', array(':value', 1, 100))
                            ->rule('image_watermark', 'range', array(':value', 0, 1))
                            ->rule('image_watermark_position', 'not_empty')
                            ->rule('image_watermark_position', 'digit')
                            ->rule('image_watermark_position', 'range', array(':value', 0, 2))
                            ->rule('image_disallow_nudes', 'range', array(':value', 0, 1));

            if ($validation->check()) {
                foreach ($config as $c)
                {
                    $config_res = $this->request->post();

                    if (!array_key_exists('allowed_formats', $config_res[$c->group_name]))
                    {
                        Alert::set(Alert::ERROR, __('At least one image format should be allowed.'));
                        $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'image')));
                    }

                    if($config_res[$c->group_name][$c->config_key][0] != $c->config_value)
                    {
                        if($c->config_key == 'allowed_formats')
                        {
                          $allowed_formats = '';
                          foreach ($config_res[$c->group_name][$c->config_key] as $key => $value)
                          {
                              $allowed_formats .= $value.",";
                          }
                          $config_res[$c->group_name][$c->config_key][0] = $allowed_formats;
                        }

                        if($c->config_key == 'aws_s3_domain')
                        {
                            switch ($config_res[$c->group_name]['aws_s3_domain'][0])
                            {
                                case 'bn-s3':
                                    $s3_domain = $config_res[$c->group_name]['aws_s3_bucket'][0].'.s3.amazonaws.com';
                                    break;

                                case 'bn':
                                    $s3_domain = $config_res[$c->group_name]['aws_s3_bucket'][0];
                                    break;

                                default:
                                    $s3_domain = 's3.amazonaws.com/'.$config_res[$c->group_name]['aws_s3_bucket'][0];
                                    break;
                            }
                            $config_res[$c->group_name][$c->config_key][0] = $s3_domain.'/';
                        }

                        $c->config_value = $config_res[$c->group_name][$c->config_key][0];
                        Model_Config::set_value($c->group_name,$c->config_key,$c->config_value);
                    }
                }
            }
            else {
                $errors = $validation->errors('config');

                foreach ($errors as $error)
                    Alert::set(Alert::ALERT, $error);

                $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'image')));
            }

            Alert::set(Alert::SUCCESS, __('Image Configuration updated'));
            $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'image')));
        }

        $this->template->content = View::factory('oc-panel/pages/settings/image', array('config'=>$config));
    }

    /**
     * Plugins configuration
     * @return [view] Renders view with form inputs
     */
    public function action_plugins()
    {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Plugins')));
        $this->template->title = __('Plugins');

        // all form config values
        $generalconfig = new Model_Config();
        $config = $generalconfig->where('group_name', '=', 'general')->or_where('group_name', '=', 'i18n')->find_all();

        // config general array
        foreach ($config as $c)
        {
            $forms[$c->config_key] = $forms[$c->config_key] = array('key'=>$c->group_name.'['.$c->config_key.'][]', 'id'=>$c->config_key, 'value'=>$c->config_value);
        }

        // save only changed values
        if($this->request->post())
        {
            //d($this->request->post());
            foreach ($this->request->post('general') as $k => $v)
                $this->request->post('general_'.$k, $v[0]);

            $validation = Validation::factory($this->request->post());

            if ($validation->check()) {
                //save general
                foreach ($config as $c)
                {
                    $config_res = $this->request->post();

                    if (isset($config_res[$c->group_name][$c->config_key][0]) AND $config_res[$c->group_name][$c->config_key][0] != $c->config_value)
                    {
                        $c->config_value = $config_res[$c->group_name][$c->config_key][0];
                        Model_Config::set_value($c->group_name,$c->config_key,$c->config_value);
                    }
                }
            }
            else {
                $errors = $validation->errors('config');

                foreach ($errors as $error)
                    Alert::set(Alert::ALERT, $error);

                $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'plugins')));
            }

            Alert::set(Alert::SUCCESS, __('Plugins configuration updated'));
            $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'plugins')));
        }

        $this->template->content = View::factory('oc-panel/pages/settings/plugins', array('forms'=>$forms));
    }

}//end of controller
