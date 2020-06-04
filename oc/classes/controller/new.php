<?php defined('SYSPATH') or die('No direct script access.');
/**
 * CONTROLLER NEW
 */
class Controller_New extends Controller
{

    /**
     *
     * NEW ADVERTISEMENT
     *
     */
    public function action_index()
    {
        //advertisement.only_admin_post
        if( Core::config('advertisement.only_admin_post') == TRUE AND (
            !Auth::instance()->logged_in() OR
            (Auth::instance()->logged_in() AND ! in_array($this->user->id_role, [Model_Role::ROLE_MODERATOR, Model_Role::ROLE_ADMIN]))))
        {
            $this->redirect(Route::url('default'));
        }
        // redirect to login, if conditions are met
        elseif((Core::config('advertisement.login_to_post')  == TRUE
             OR Core::config('payment.escrow_pay')           == TRUE
             OR Core::config('payment.stripe_connect')       == TRUE
             OR Core::config('general.subscriptions')        == TRUE )
             AND !Auth::instance()->logged_in())
        {
            Alert::set(Alert::INFO, __('Please, login before posting advertisement!'));
            HTTP::redirect(Route::url('oc-panel',array('controller'=>'auth','action'=>'login')).'?auth_redirect='.URL::current());
        }
        //Detect early spam users, show him alert
        elseif (core::config('general.black_list') == TRUE AND Model_User::is_spam(Core::post('email')) === TRUE)
        {
            Alert::set(Alert::ALERT, __('Your profile has been disable for posting, due to recent spam content! If you think this is a mistake please contact us.'));
            $this->redirect(Route::url('default'));
        }
        // redirect to connect stripe
        elseif( Core::config('payment.stripe_connect') == TRUE  AND empty($this->user->stripe_user_id))
        {
            Alert::set(Alert::INFO, __('Please, connect with Stripe'));
            $this->redirect(Route::url('oc-panel',array('controller'=>'profile','action'=>'edit')));
        }
        // redirect to connect escrow
        elseif( Core::config('payment.escrow_pay') == TRUE  AND empty($this->user->escrow_api_key))
        {
            Alert::set(Alert::INFO, __('Please, connect with Escrow'));
            $this->redirect(Route::url('oc-panel',array('controller'=>'profile','action'=>'edit')));
        }
        //users subscriptions needs to login and have a valid plan
        elseif (Auth::instance()->logged_in() AND $this->user->expired_subscription())
        {
            Alert::set(Alert::INFO, __('Please, choose a plan first'));
            HTTP::redirect(Route::url('pricing'));
        }
        
        //validates captcha
        if (Core::post('ajaxValidateCaptcha'))
        {
            $this->auto_render = FALSE;
            $this->template = View::factory('js');

            if (captcha::check('publish_new', TRUE))
                $this->template->content = 'true';
            else
                $this->template->content = 'false';

            return;
        }

        Controller::$full_width = TRUE;

        //template header
        $this->template->title              = __('Publish new advertisement');
        $this->template->meta_description   = __('Publish new advertisement');


        $this->template->styles = array('css/jquery.sceditor.default.theme.min.css' => 'screen',
                                        'css/jasny-bootstrap.min.css' => 'screen',
                                        'css/dropzone.min.css' => 'screen',
                                        'css/jquery-ui-sortable.min.css' => 'screen',
                                        '//cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.1/css/selectize.bootstrap3.min.css' => 'screen',
                                        '//cdn.jsdelivr.net/sweetalert/1.1.3/sweetalert.css' => 'screen');

        $this->template->scripts['footer'][] = 'js/jquery.sceditor.bbcode.min.js';
        $this->template->scripts['footer'][] = 'js/jquery.sceditor.plaintext.min.js';
        $this->template->scripts['footer'][] = 'js/jasny-bootstrap.min.js';
        $this->template->scripts['footer'][] = 'js/dropzone.min.js';
        $this->template->scripts['footer'][] = Route::url('jslocalization', ['controller' => 'jslocalization', 'action' => 'dropzone']);
        $this->template->scripts['footer'][] = 'js/jquery-ui-sortable.min.js';
        $this->template->scripts['footer'][] = '//cdn.jsdelivr.net/sweetalert/1.1.3/sweetalert.min.js';
        $this->template->scripts['footer'][] = '//cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.1/js/standalone/selectize.min.js';
        $this->template->scripts['footer'][] = '//cdnjs.cloudflare.com/ajax/libs/ouibounce/0.0.12/ouibounce.min.js';
        $this->template->scripts['footer'][] = 'js/load-image.all.min.js';

        if(core::config('advertisement.map_pub_new'))
        {
            $this->template->scripts['async_defer'][] = '//maps.google.com/maps/api/js?libraries=geometry&v=3&key='.core::config("advertisement.gm_api_key").'&callback=initLocationsGMap&language='.i18n::get_gmaps_language(i18n::$locale);
        }

        if (core::config('advertisement.picker_api_key') AND core::config('advertisement.picker_client_id'))
            $this->template->scripts['async_defer'][] = '//apis.google.com/js/api.js?onload=onApiLoad';

        if (core::config('advertisement.cloudinary_cloud_name') AND core::config('advertisement.cloudinary_cloud_preset'))
            $this->template->scripts['footer'][] = 'https://widget.cloudinary.com/v2.0/global/all.js';

        if (core::config('advertisement.phone') != FALSE AND Core::config('general.sms_auth') == FALSE)
        {
            $this->template->styles = $this->template->styles + ['css/intlTelInput.css' => 'screen'];
            $this->template->scripts['footer'][] = 'js/intlTelInput.min.js';
            $this->template->scripts['footer'][] = 'js/utils.js';
        }

        $this->template->scripts['footer'][] = 'js/new.js?v='.Core::VERSION;

        $categories = new Model_Category;
        $categories = $categories->where('id_category_parent', '=', '1');

        // NO categories redirect ADMIN to categories panel
        if ($categories->count_all() == 0)
        {
            if(Auth::instance()->logged_in() AND Auth::instance()->get_user()->is_admin())
            {
                Alert::set(Alert::INFO, __('Please, first create some categories.'));
                $this->redirect(Route::url('oc-panel',array('controller'=>'category','action'=>'index')));
            }
            else
            {
                Alert::set(Alert::INFO, __('Posting advertisements is not yet available.'));
                $this->redirect(Route::url('default'));
            }
        }

        //get locations
        $locations = new Model_Location;
        $locations = $locations->where('id_location', '!=', '1');

        // bool values from DB, to show or hide this fields in view
        $form_show = array('captcha'     =>core::config('advertisement.captcha'),
                           'website'     =>core::config('advertisement.website'),
                           'phone'       =>core::config('advertisement.phone'),
                           'location'    =>core::config('advertisement.location'),
                           'description' =>core::config('advertisement.description'),
                           'address'     =>core::config('advertisement.address'),
                           'price'       =>core::config('advertisement.price'));


        $id_category = NULL;
        $selected_category = new Model_Category();
        //if theres a category by post or by get
        if (Core::request('category')!==NULL)
        {
            if (is_numeric(Core::request('category')))
                $selected_category->where('id_category','=',core::request('category'))->limit(1)->find();
            else
                $selected_category->where('seoname','=',core::request('category'))->limit(1)->find();

            if ($selected_category->loaded())
                $id_category = $selected_category->id_category;
        }

        $id_location = NULL;
        $selected_location = new Model_Location();
        //if theres a location by post or by get
        if (Core::request('location')!==NULL)
        {
            if (is_numeric(Core::request('location')))
                $selected_location->where('id_location','=',core::request('location'))->limit(1)->find();
            else
                $selected_location->where('seoname','=',core::request('location'))->limit(1)->find();

            if ($selected_location->loaded())
                $id_location = $selected_location->id_location;
        }

        //render view publish new
        $this->template->content = View::factory('pages/ad/new', array('form_show'          => $form_show,
                                                                       'id_category'        => $id_category,
                                                                       'selected_category'  => $selected_category,
                                                                       'id_location'        => $id_location,
                                                                       'selected_location'  => $selected_location,
                                                                       'fields'             => Model_Field::get_all()));
        if($this->request->post())
        {
            if(captcha::check('publish_new'))
            {
                $data = $this->request->post();

                $validation = Validation::factory($data);

                //validate location since its optional
                if(core::config('advertisement.location'))
                {
                    if ($locations->count_all() > 1)
                        $validation = $validation->rule('location', 'not_empty')
                        ->rule('location', 'digit');
                }

                //user is not logged in validate input
                if(!Auth::instance()->logged_in())
                {
                    $validation = $validation->rule('email', 'not_empty')
                    ->rule('email', 'email')
                    ->rule('email', 'email_domain')
                    ->rule('name', 'not_empty')
                    ->rule('name', 'min_length', array(':value', 2))
                    ->rule('name', 'max_length', array(':value', 145))
                    ->rule('cf_vatnumber', 'Valid::vies', array(':validation', array('cf_vatnumber', 'cf_vatcountry')));
                }

                // Optional banned words validation
                if (core::config('advertisement.validate_banned_words'))
                {
                    $validation = $validation->rule('title', 'no_banned_words');
                    $validation = $validation->rule('description', 'no_banned_words');
                }

                // check if eu vat number and country are valid
                if(core::post('cf_vatnumber') AND core::post('cf_vatcountry'))
                {
                    if (!euvat::verify_vies(core::post('cf_vatnumber'),core::post('cf_vatcountry')) AND euvat::is_eu_country(core::post('cf_vatcountry')))
                    {
                        Alert::set(Alert::ERROR, __('Invalid EU Vat Number, please verify number and country match'));
                        $this->redirect(Route::url('post_new'));
                    }
                }

                if($validation->check())
                {
                    // User detection, if doesnt exists create
                    if (!Auth::instance()->logged_in())
                    {
                        try
                        {
                            $user = Model_User::create_email(core::post('email'), core::post('name'));
                        }
                        catch (ORM_Validation_Exception $e)
                        {
                            foreach ($e->errors('models') as $error)
                                Alert::set(Alert::ALERT, $error);

                            return;
                        }

                        //add custom fields
                        $save_cf = FALSE;
                        foreach ($this->request->post() as $custom_field => $value)
                        {
                            if (strpos($custom_field,'ucf_')!==FALSE)
                            {
                                $user_custom_field = substr($custom_field, 1); //rename ucf_ to cf
                                $user->$user_custom_field = $value;
                                $save_cf = TRUE;
                                unset($data[$custom_field]);
                            }
                        }
                        //saves the user only if there was CF
                        if($save_cf === TRUE)
                            $user->save();
                    }
                    else
                        $user = Auth::instance()->get_user();

                    //to make it backward compatible with older themes: UGLY!!
                    if (isset($data['category']) AND is_numeric($data['category']))
                    {
                        $data['id_category'] = $data['category'];
                        unset($data['category']);
                    }

                    if (isset($data['location']) AND is_numeric($data['location']))
                    {
                        $data['id_location'] = $data['location'];
                        unset($data['location']);
                    }

                    //lets create!!
                    $return = Model_Ad::new_ad($data,$user);


                    //there was an error on the validation
                    if (isset($return['validation_errors']) AND is_array($return['validation_errors']))
                    {
                        foreach ($return['validation_errors'] as $f => $err)
                            Alert::set(Alert::ALERT, $err);
                    }
                    //another error
                    elseif (isset($return['error']))
                    {
                        Alert::set($return['error_type'], $return['error']);
                    }
                    //success!!!
                    elseif (isset($return['message']) AND isset($return['ad']))
                    {
                        $new_ad = $return['ad'];

                        // IMAGE UPLOAD
                        $filename = NULL;

                        if (Core::post('ajax'))
                        {
                            for ($i = 0; $i < core::config('advertisement.num_images'); $i++)
                            {
                                $files = Arr::re_array_multiple_file_uploads($_FILES['file']);

                                if (isset($files[$i]))
                                {
                                    $filename = $new_ad->save_image($files[$i]);
                                }
                            }
                        }
                        else
                        {
                            for ($i = 0; $i < core::config('advertisement.num_images'); $i++)
                            {
                                if (Core::post('base64_image' . $i))
                                {
                                    $filename = $new_ad->save_base64_image(Core::post('base64_image' . $i));
                                }
                                elseif (isset($_FILES['image' . $i]))
                                {
                                    $filename = $new_ad->save_image($_FILES['image' . $i]);
                                }
                            }
                        }

                        // Post on social media
                        Social::post_ad($new_ad, $filename);

                        Alert::set(Alert::SUCCESS, $return['message']);

                        //redirect user
                        if (Core::post('ajax'))
                        {
                            $this->auto_render = FALSE;
                            $this->template = View::factory('js');
                            $this->response->headers('Content-Type', 'application/json');
                            $this->response->status('200');

                            if (isset($return['checkout_url']) AND !empty($return['checkout_url']))
                                $this->template->content = json_encode(['redirect_url' => $return['checkout_url']]);
                            else
                                $this->template->content = json_encode(['redirect_url' => Route::url('default', ['action' => 'thanks', 'controller' => 'ad', 'id' => $new_ad->id_ad])]);

                            return;
                        }
                        else
                        {
                            if (isset($return['checkout_url']) AND !empty($return['checkout_url']))
                                $this->redirect($return['checkout_url']);
                            else
                                $this->redirect(Route::url('default', array('action'=>'thanks','controller'=>'ad','id'=>$new_ad->id_ad)));
                        }
                    }
                }
                else
                {
                    $errors = $validation->errors('ad');
                    foreach ($errors as $f => $err)
                    {
                        Alert::set(Alert::ALERT, $err);
                    }
                }
            }
            else
            {
                Alert::set(Alert::ALERT, __('Captcha is not correct'));
            }

            if (Core::post('ajax'))
            {
                $this->auto_render = FALSE;
                $this->template = View::factory('js');
                $this->response->headers('Content-Type', 'application/json');
                $this->response->status('400');
                $this->template->content = json_encode(['redirect_url' => Route::url('post_new')]);

                return;
            }
        }

    }
}
