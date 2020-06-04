<?php defined('SYSPATH') or die('No direct script access.');
/**
 * User model
 *
 * @author      Chema <chema@open-classifieds.com>
 * @package     OC
 * @copyright   (c) 2009-2013 Open Classifieds Team
 * @license     GPL v3
 * *
 */
class Model_User extends ORM {

    /**
     * Status constants
     */
    const STATUS_INACTIVE       = 0;    // Inactive
    const STATUS_ACTIVE         = 1;   // Active (normal status) (displayed in SERP and can post/login)
    const STATUS_SPAM           = 5;   // tagged as spam

    /**
     * Table name to use
     *
     * @access  protected
     * @var     string  $_table_name default [singular model name]
     */
    protected $_table_name = 'users';

    /**
     * Column to use as primary key
     *
     * @access  protected
     * @var     string  $_primary_key default [id]
     */
    protected $_primary_key = 'id_user';

    protected $_has_many = array(
        'ads' => array(
            'model'       => 'ad',
            'foreign_key' => 'id_user',
        ),
        'reviews' => array(
            'model'       => 'review',
            'foreign_key' => 'id_user',
        ),
    );

    /**
     * @var  array  ORM Dependency/hirerachy
     */
    protected $_belongs_to = array(
        'role' => array(
                'model'       => 'role',
                'foreign_key' => 'id_role',
            ),
        'location' => array(
                'model'       => 'location',
                'foreign_key' => 'id_location',
            ),
    );


    /**
     * Rule definitions for validation
     *
     * @return array
     */
    public function rules()
    {
        return array(
                        'id_user'       => array(array('numeric')),
                        'name'          => array(array('not_empty'), array('min_length', array(':value', 1)), array('max_length', array(':value', 145)), ),
                        'email'         => array(
                                                    array('not_empty'),
                                                    array('email'),
                                                    array(array($this, 'unique'), array('email', ':value')),
                                                    array('max_length', array(':value', 145))
                                        ),
                        'password'      => array(array('not_empty'), array('max_length', array(':value', 64)), ),
                        'status'        => array(array('numeric')),
                        'id_role'       => array(array('numeric')),
                        'id_location'   => array(),
                        'created'       => array(),
                        'last_modified' => array(),
                        'logins'        => array(),
                        'last_login'    => array(),
                        'last_ip'       => array(),
                        'user_agent'    => array(),
                        'description'   => array(),
                        'token'         => array(array('max_length', array(':value', 40))),
                        'token_created' => array(),
                        'token_expires' => array(),
                        'has_image'    => array(array('numeric')),
                        'last_failed'   => array(),
                        'failed_attempts'   => array(),
                        'phone'         => array(
                                                    array('phone'),
                                                    array(array($this, 'unique'), array('phone', ':value')),
                                                    array('max_length', array(':value', 30))
                                        ),
                        'latitude'      => array(array('regex', array(':value', '/^-?+(?=.*[0-9])[0-9]*+'.preg_quote('.').'?+[0-9]*+$/D'))),
                        'longitude'     => array(array('regex', array(':value', '/^-?+(?=.*[0-9])[0-9]*+'.preg_quote('.').'?+[0-9]*+$/D'))),
                    );
    }



    /**
     * Label definitions for validation
     *
     * @return array
     */
    public function labels()
    {
        return array(
                        'id_user'       => 'Id',
                        'name'          => __('Name'),
                        'email'         => __('Email'),
                        'password'      => __('Password'),
                        'status'        => __('Status'),
                        'id_role'       => __('Role'),
                        'id_location'   => __('Location'),
                        'created'       => __('Created'),
                        'description'   => __('Description'),
                        'last_modified' => __('Last modified'),
                        'last_login'    => __('Last login'),
                        'has_image'     => __('Has image'),
                    );
    }

    /**
     * Filters to run when data is set in this model. The password filter
     * automatically hashes the password when it's set in the model.
     *
     * @return array Filters
     */
    public function filters()
    {
        return array(
                'password' => array(
                                array(array(Auth::instance(), 'hash'))
                              ),
                'seoname' => array(
                                array(array($this, 'gen_seo_title'))
                              ),
        );
    }

    /**
     * global Model User instance get from controller so we can access from anywhere like Model_User::current()
     * @var Model_User
     */
    protected static $_current = NULL;

    /**
     * returns the current user used when navidating the site, not the current loged user!
     * @return Model_User
     */
    public static function current()
    {
        //we don't have so let's retrieve
        if (self::$_current === NULL AND
            Request::current()->param('seoname') != NULL AND
            strtolower(Request::current()->action())=='profile' AND
            strtolower(Request::current()->controller())=='user' )
        {
            self::$_current = new self;
            self::$_current = self::$_current->where('seoname','=', Request::current()->param('seoname'))
             ->where('status','=', Model_User::STATUS_ACTIVE)
             ->limit(1)->cached()->find();
        }

        return self::$_current;
    }


    /**
     * complete the login for a user
     * incrementing the logins and saving login timestamp
     * @param integer $lifetime Regenerates the token used for the autologin cookie
     *
     */
    public function complete_login($lifetime=NULL)
    {
        if ($this->_loaded)
        {
            //want to remember the login using cookie
            if (is_numeric($lifetime))
                $this->create_token($lifetime);

            // Update the number of logins
            $this->logins = new Database_Expression('logins + 1');

            // Set the last login date
            $this->last_login = Date::unix2mysql(time());

            // Set the last ip address
            $this->last_ip = ip2long(Request::$client_ip);

            try
            {
                // Save the user
                $this->update();
            }
            catch (ORM_Validation_Exception $e)
            {
                Form::set_errors($e->errors(''));
            }
            catch(Exception $e)
            {
                throw HTTP_Exception::factory(500,$e->getMessage());
            }

        }
    }

    /**
     * Creates a unique token for the autologin
     * @param integer $lifetime token alive
     * @return string
     */
    public function create_token($lifetime=NULL)
    {
        if ($this->_loaded)
        {
            //we need to be sure we have a lifetime
            if ($lifetime==NULL)
            {
                $config = Kohana::$config->load('auth');
                $lifetime = $config['lifetime'];
            }

            //we assure the token is unique
            do
            {
                $this->token = sha1(uniqid(Text::random('alnum', 32), TRUE));
            }
            while(ORM::factory('user', array('token' => $this->token))->limit(1)->loaded());

            // user Token data
            $this->user_agent    = sha1(Request::$user_agent);
            $this->token_created = Date::unix2mysql(time());
            $this->token_expires = Date::unix2mysql(time() + $lifetime);

            try
            {
                $this->update();
            }
            catch(ORM_Validation_Exception $e)
            {
                foreach ($e->errors('models') as $error)
                    Kohana::$log->add(Log::ERROR, 'Error: ' . $error);
                throw HTTP_Exception::factory(500,$e->getMessage());
            }
        }


    }


    /**
     * Check the actual controller and action request and validates if the user has access to it
     * @todo    code something that you can show to your mom.
     * @param   string  $action
     * @return  boolean
     */
    public function has_access($controller, $action='index', $directory='')
    {
        $controller = strtolower($controller);
        $action     = strtolower($action);
        $directory  = strtolower($directory);

        $this->get_access_controllers();
        $this->get_access_actions();

        $granted = $this->get_access_actions();

        if((in_array('*.*', $granted)) OR (in_array($controller.'.*', $granted))
            OR (in_array($controller.'.'.$action, $granted)))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }

    }

    /**
     *
     * returns an array with all the actions that the backuser can do
     */
    private function get_access_actions()
    {
        $granted = Session::instance()->get('granted_actions');
        if( ! isset($granted))
        {
            $access = $this->role->access->find_all()->as_array();
            $granted = array();

            foreach($access as $k=>$v)
            {
                $granted[] = $v->access;
            }

            //$granted[] = 'auth.*';
            $granted[] = 'home.*';

            Session::instance()->set('granted_actions', $granted);
        }

        return $granted;
    }

    /**
     *
     * returns an array with the controllers within the user has any right
     */
    private function get_access_controllers()
    {
        $granted = Session::instance()->get('granted_controllers');
        if( ! isset($granted))
        {
            $access = $this->role->access->find_all()->as_array();
            $granted = array();


            foreach($access as $k=>$v)
            {
                //only woks in php 5.3 or higher
                //$granted[] = strstr($v->access, '.', TRUE);
                $granted[] = substr($v->access, 0, strpos($v->access, '.'));
            }

            Session::instance()->set('granted_controllers', $granted);
        }
        return $granted;
    }

    /**
     * Rudimentary access control list
     * @todo    code something that you can show to your mom.
     * @param   string  $action
     * @return  boolean
     */
    public function has_access_to_any($list)
    {
        $granted = $this->get_access_controllers();
        $controllers = explode(',',$list);
        $out = array_intersect($granted, $controllers);
        if(( ! empty($out) ) OR (in_array('*', $granted)))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * sends email to the current user replacing tags
     * @param  string $seotitle from Model_Content
     * @param  array $replace
     * @param  array $file  file to be uploaded
     * @return boolean
     */
    public function email($seotitle, array $replace = NULL, $from = NULL, $from_name =NULL, $file=NULL, $to = NULL)
    {
        if ($this->loaded() AND $this->subscriber == 1)
        {
            return Email::content(($to == NULL)?$this->email:$to,$this->name,$from,$from_name,$seotitle,$replace, $file);
        }
        return FALSE;
    }

    /**
     * return TRUE if user is spammer
     *
     * @param  string $email
     * @return bool
     */
    public static function is_spam($email = NULL)
    {

        //if he is login we can check if its an spammer
        if ( Auth::instance()->logged_in() === TRUE )
        {
            if (Auth::instance()->get_user()->status == Model_User::STATUS_SPAM)
                return TRUE;
        }
        //not loged in so only way to see it is after he posted with his email
        elseif(Valid::email($email))
        {
            $spammer = new Model_User();
            $spammer->where('email','=',$email)
                    ->where('status','=',Model_User::STATUS_SPAM)
                    ->find();

            if ($spammer->loaded())
                return TRUE;
        }

        return FALSE;
    }
    /**
     * change status of user to spam, if not admin or moderator
     *
     * @param  string $email
     */
    public function user_spam($email = NULL)
    {

        if($email != NULL)//if $this is not loaded
        {
            $user = Model_User::find_by_email($email);
        }
        else $user = $this;

        if($user->loaded())
        {
            if ( ! $user->is_admin() AND ! $user->is_moderator() AND ! $user->is_translator())
            {
                $user->status = self::STATUS_SPAM;

                try {
                    $user->save();
                    Alert::set(Alert::ALERT, $user->email.' '.__('has been disable for posting, due to recent spam content!'));
                } catch (Exception $e) {
                    Kohana::$log->add(Log::ERROR, 'Error: ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * get url with auto QL login and redirect
     * @param  string  $route
     * @param  array  $params
     * @param  boolean $regenerate_token
     * @return string
     */
    public function ql($route = 'default', array $params = NULL, $regenerate_token = FALSE)
    {
        if ($this->loaded())
        {
            if ($regenerate_token==TRUE)//regenerating the token, for security or new user...
                $this->create_token();

            $ql = Auth::instance()->ql_encode($this->token,Route::url($route,$params));
            return Route::url('oc-panel',array('controller' => 'auth', 'action' => 'ql', 'id' =>$ql));
        }
        return NULL;
    }


    public function form_setup($form)
    {
        if(Request::current()->action() != 'update'){
            $form->fields['password']['display_as'] = 'password';
        }
        $form->fields['email']['caption'] = 'email';
        $form->fields['status']['display_as'] = 'select';
        $form->fields['status']['options'] = array('0','1','5');
        $form->fields['id_role']['caption'] = 'name';
    }

    public function exclude_fields()
    {
        $exclude_fields = array('logins','last_login','hybridauth_provider_uid','last_modified','created','salt', 'ip_created', 'last_ip','token','token_created','token_expires','user_agent','id_location','seoname','has_image','failed_attempts','last_failed');

        if (Request::current()->action() == 'update')
            array_push($exclude_fields, 'password');

        return $exclude_fields;
    }

    /**
     * return the title formatted for the URL
     *
     * @param  string $seoname
     *
     */
    public function gen_seo_title($seoname)
    {
        //in case seoname is really small or null
        if (strlen($seoname)<3)
        {
            if (Valid::email($this->email))
                $seoname = substr($this->email, 0, strpos($this->email, '@'));
            elseif (strlen($this->name)>=3)
                $seoname = $this->name;
            else
                $seoname = __('user').'-'.$seoname;
        }

        $seoname = URL::title($seoname);

        if ($seoname != $this->seoname)
        {
            $user = new self;
            //find a user same seotitle
            $s = $user->where('seoname', '=', $seoname)->where('id_user', '!=', $this->id_user)->limit(1)->find();

            //found, increment the last digit of the seotitle
            if ($s->loaded())
            {
                $cont = 2;
                $loop = TRUE;
                while($loop)
                {
                    $attempt = $seoname.'-'.$cont;
                    $user = new self;
                    unset($s);
                    $s = $user->where('seoname', '=', $attempt)->where('id_user', '!=', $this->id_user)->limit(1)->find();
                    if(!$s->loaded())
                    {
                        $loop = FALSE;
                        $seoname = $attempt;
                    }
                    else
                    {
                        $cont++;
                    }
                }
            }
        }

        return $seoname;
    }

    /**
     * creates a user from email if exists doesn't, sends welcome email
     * @param  string $email
     * @param  string $name
     * @param  string $password
     * @return Model_User
     */
    public static function create_email($email,$name=NULL,$password=NULL)
    {
        $user = Model_User::find_by_email($email);

        //only if didnt exists
        if (!$user->loaded())
        {
            if ($password === NULL)
                $password  = Text::random('alnum', 8);

            $user = self::create_user($email,$name,$password);

            $url = $user->ql('oc-panel',array('controller' => 'profile',
                                                      'action'     => 'edit'),TRUE);

            $user->email('auth-register',array('[USER.PWD]'=>$password,
                                                        '[URL.QL]'=>$url)
                                                );
        }

        return $user;
    }

    /**
     * creates a user from email if exists doesn't...
     * @param  string $email
     * @param  string $name
     * @param  string $password
     * @return Model_User
     */
    public static function create_user($email,$name=NULL,$password=NULL)
    {
        $user = Model_User::find_by_email($email);

        if (!$user->loaded())
        {
            if ($password === NULL)
                $password       = Text::random('alnum', 8);

            $user->email        = $email;
            $user->name         = ($name===NULL OR !isset($name) OR empty($name))? substr($email, 0, strpos($email, '@')):$name;
            $user->name         = UTF8::substr($user->name, 0, 145);
            $user->status       = self::STATUS_ACTIVE;
            $user->id_role      = Model_Role::ROLE_USER;;
            $user->seoname      = $user->gen_seo_title($user->name);
            $user->password     = $password;
            $user->subscriber   = 1;
            try
            {
                $user->save();
            }
            catch (ORM_Validation_Exception $e)
            {
                throw $e;
            }
            catch (Exception $e)
            {
                throw HTTP_Exception::factory(500,$e->getMessage());
            }

            //add to elasticemail
            if ( Core::config('email.elastic_listname')!='' )
                ElasticEmail::subscribe(Core::config('email.elastic_listname'),$user->email,$user->name);
        }

        return $user;
    }

    /**
     * creates a User from social data
     * @param  string $email
     * @param  string $name
     * @param  string $provider
     * @param  mixed $identifier
     * @return Model_User
     */
    public static function create_social($email,$name=NULL,$provider, $identifier)
    {
        //get the user or create it
        try
        {
            $user = self::create_email($email,$name);
        }
        catch (ORM_Validation_Exception $e)
        {
            throw HTTP_Exception::factory(500,$e->errors('models'));
        }

        //always we set this values even if user existed
        $user->hybridauth_provider_name = $provider;
        $user->hybridauth_provider_uid  = $identifier;
        try
        {
            $user->save();
        }
        catch (ORM_Validation_Exception $e)
        {
            throw HTTP_Exception::factory(500,$e->errors(''));
        }
        catch (Exception $e)
        {
            throw HTTP_Exception::factory(500,$e->getMessage());
        }

        return $user;
    }

    /**
     * reurns the url of the users profile image
     * @return string url
     */
    public function get_profile_image()
    {
        if ($this->has_image) {
            if(core::config('image.aws_s3_active'))
            {
                $protocol = Core::is_HTTPS() ? 'https://' : 'http://';
                $version = $this->last_modified ? '?v='.Date::mysql2unix($this->last_modified) : NULL;

                return $protocol.core::config('image.aws_s3_domain').'images/users/'.$this->id_user.'.png'.$version;
            }
            else
                return URL::base().'images/users/'.$this->id_user.'.png'
                        .(($this->last_modified) ? '?v='.Date::mysql2unix($this->last_modified) : NULL);
        }
        else
        {
            if(Theme::get('default_profile_image'))
                return Theme::get('default_profile_image');
            else
                return '//www.gravatar.com/avatar/'.md5(strtolower(trim($this->email))).'?s=250';
        }
    }

    /**
     * Gets all profile images
     * @return string url
     */
    public function get_profile_images()
    {
        $images = array();

        if ($this->has_image)
        {
            $base       = Core::S3_domain();
            $route      = 'images/users/';
            $folder     = DOCROOT.$route;
            $version    = $this->last_modified ? '?v='.Date::mysql2unix($this->last_modified) : NULL;

            for ($i=1; $i <= $this->has_image; $i++)
            {
                if ($i == 1)
                    $filename = $this->id_user.'.png';
                else
                    $filename = $this->id_user.'_'.$i.'.png';

                $images[$i] = $base.$route.$filename.$version;
            }
        }
        else
        {
            if (Theme::get('default_profile_image'))
                $images = [Theme::get('default_profile_image')];
            else
                $images = ['//www.gravatar.com/avatar/'.md5(strtolower(trim($this->email))).'?s=250'];
        }

        return $images;
    }


    /**
     * Deletes image from user
     * @return bool
     */
    public function delete_images()
    {
        if (!$this->loaded())
            return FALSE;

        for ($i=1; $i <= $this->has_image; $i++)
            $this->delete_image($i);

        return TRUE;
    }

    /**
     * deletes the image of the user
     * @param  integer $deleted_image
     * @return void
     */
    public function delete_image($deleted_image)
    {
        if ( ! $this->_loaded)
            throw new Kohana_Exception('Cannot delete :model model because it is not loaded.', array(':model' => $this->_object_name));

        if(core::config('image.aws_s3_active'))
        {
            require_once Kohana::find_file('vendor', 'amazon-s3-php-class/S3','php');
            $s3 = new S3(core::config('image.aws_access_key'), core::config('image.aws_secret_key'));

            $s3->deleteObject(core::config('image.aws_s3_bucket'), $this->image_name($deleted_image));

            //re-ordering image file names
            for($i = $deleted_image; $i < $this->has_image; $i++)
            {
                //rename original image
                $s3->copyObject(core::config('image.aws_s3_bucket'), $this->image_name(($i+1)), core::config('image.aws_s3_bucket'), $this->image_name($i), S3::ACL_PUBLIC_READ);
                $s3->deleteObject(core::config('image.aws_s3_bucket'), $this->image_name(($i+1)));
            }
        }

        $img_path = DOCROOT.'images/users/'; //root folder

        if (!is_dir($img_path))
            return FALSE;
        else
        {
            //delete photo
            @unlink($this->image_name($deleted_image));

            //re-ordering image file names
            for($i = $deleted_image; $i < $this->has_image; $i++)
            {
                @rename($this->image_name(($i+1)), $this->image_name($i));
            }
        }

        // update user info
        $this->has_image = ($this->has_image > 0) ? $this->has_image-1 : 0;
        $this->last_modified = Date::unix2mysql();
        $this->save();

        try
        {
            $this->save();
            return TRUE;
        }
        catch (Exception $e)
        {
            throw HTTP_Exception::factory(500,$e->getMessage());
        }

        return FALSE;
    }

    /**
     * upload an image to the user
     * @param  file $image
     * @return bool/message
     */
    public function upload_image($image)
    {
        if (!$this->loaded())
            return FALSE;

        if(core::config('image.aws_s3_active'))
        {
            require_once Kohana::find_file('vendor', 'amazon-s3-php-class/S3','php');
            $s3 = new S3(core::config('image.aws_access_key'), core::config('image.aws_secret_key'));
        }

        if (
            ! Upload::valid($image) OR
            ! Upload::not_empty($image) OR
            ! Upload::type($image, explode(',',core::config('image.allowed_formats'))) OR
            ! Upload::size($image, core::config('image.max_image_size').'M'))
        {
            if ( Upload::not_empty($image) && ! Upload::type($image, explode(',',core::config('image.allowed_formats'))))
            {
                return $image['name'].' '.sprintf(__('Is not valid format, please use one of this formats "%s"'),core::config('image.allowed_formats'));
            }
            if( ! Upload::size($image, core::config('image.max_image_size').'M'))
            {
                return $image['name'].' '.sprintf(__('Is not of valid size. Size is limited to %s MB per image'),core::config('image.max_image_size'));
            }
            return $image['name'].' '.__('Image is not valid. Please try again.');
        }
        else
        {
            if($image != NULL) // sanity check
            {
                // saving/uploading zip file to dir.
                $path = 'images/users/'; //root folder
                $root = DOCROOT.$path; //root folder
                $image_name = $this->id_user.'.png';
                $width = core::config('image.width'); // @TODO dynamic !?
                $height = core::config('image.height');// @TODO dynamic !?
                $image_quality = core::config('image.quality');

                // if folder does not exist, try to make it
                if ( ! file_exists($root) AND ! @mkdir($root, 0775, true)) { // mkdir not successful ?
                    return __('Image folder is missing and cannot be created with mkdir. Please correct to be able to upload images.');
                };

                // save file to root folder, file, name, dir
                if($file = Upload::save($image, $image_name, $root))
                {
                    // resize uploaded image
                    Image::factory($file)
                        ->orientate()
                        ->resize($width, $height, Image::AUTO)
                        ->save($root.$image_name,$image_quality);

                    // put image to Amazon S3
                    if(core::config('image.aws_s3_active'))
                        $s3->putObject($s3->inputFile($file), core::config('image.aws_s3_bucket'), $path.$image_name, S3::ACL_PUBLIC_READ);

                    // update user info
                    $this->has_image = 1;
                    $this->last_modified = Date::unix2mysql();
                    try {
                        $this->save();
                        return TRUE;
                    } catch (Exception $e) {
                        return $e->getMessage();
                    }
                }
                else
                    return $image['name'].' '.__('Icon file could not been saved.');
            }

        }
    }

    /**
     * gets the api_token and regenerates if needed
     * @param  boolean $regenerate forces regenerate
     * @return string
     */
    public function api_token($regenerate = FALSE)
    {
        if($this->loaded())
        {
            //first time force the token generation
            if ($this->api_token==NULL)
                $regenerate = TRUE;

            if ($regenerate === TRUE)
            {
                //we assure the token is unique
                do
                {
                    $this->api_token = sha1(uniqid(Text::random('alnum', 32), TRUE));
                }
                while(ORM::factory('user', array('api_token' => $this->api_token))->limit(1)->loaded() AND $this->api_token!= Core::config('general.api_key'));

                try
                {
                    $this->update();
                }
                catch(Exception $e)
                {
                    throw HTTP_Exception::factory(500,$e->getMessage());
                }
            }

            return $this->api_token;
        }

        return FALSE;
    }

    /**
     * sends a push notification to this user if has a device
     * @param  string $title
     * @param  string $message
     * @param  array $data extra info to send
     * @return bool
     */
    public function push_notification($title, $message, $data = NULL)
    {
        if ($this->loaded() AND isset($this->device_id) )
            {
            return Core::push_notification($this->device_id, $title, $message, $data);
            }

        return FALSE;
    }

    /**
     * get a google_authenticator QR code to be scanned-
     * @return string
     */
    public function google_authenticator_qr()
    {
        if ($this->google_authenticator!='')
        {
            require Kohana::find_file('vendor', 'GoogleAuthenticator');

            $ga = new PHPGangsta_GoogleAuthenticator();
            return $ga->getQRCodeGoogleUrl(core::config('general.site_name'), $this->google_authenticator);
        }

        return FALSE;
    }

    /**
     * Check if the user is Admin.
     * @return  boolean
     */
    public function is_admin()
    {
        if ($this->loaded() AND $this->id_role==Model_Role::ROLE_ADMIN)
            return TRUE;

        return FALSE;
    }

    /**
     * Check if the user is Moderator.
     * @return  boolean
     */
    public function is_moderator()
    {
        if ($this->loaded() AND $this->id_role==Model_Role::ROLE_MODERATOR)
            return TRUE;

        return FALSE;
    }

    /**
     * Check if the user is Translator.
     * @return  boolean
     */
    public function is_translator()
    {
        if ($this->loaded() AND $this->id_role==Model_Role::ROLE_TRANSLATOR)
            return TRUE;

        return FALSE;
    }

    /**
     * Check if the user is verified.
     * @return  verified badge else false
     */
    public function is_verified_user()
    {
        if ($this->loaded() AND isset($this->cf_verifiedbadge) AND $this->cf_verifiedbadge==1 AND Theme::get('premium')==1)
            return '<i title="'.__('Verified!').'" class="fa fa-check-circle" aria-hidden="true"></i>';

        return '';
    }

    /**
     * saves the user review rates recalculating it
     * @return [type] [description]
     */
    public function recalculate_rate()
    {
        if($this->loaded())
        {
            //get all the rates and divide by them
            $this->rate = Model_Review::get_user_rate($this);
            $this->save();
            return $this->rate;
        }
        return FALSE;
    }

    /**
     * Deletes a single record while ignoring relationships.
     *
     * @chainable
     * @throws Kohana_Exception
     * @return ORM
     */
    public function delete()
    {
        if ( ! $this->_loaded)
            throw new Kohana_Exception('Cannot delete :model model because it is not loaded.', array(':model' => $this->_object_name));

        //remove ads, will remove reviews, images etc...
        $ads = new Model_Ad();
        $ads = $ads->where('id_user','=',$this->id_user)->find_all();

        foreach ($ads as $ad)
            $ad->delete();

        //bye profile pics
        $this->delete_images();

        //delete favorites
        DB::delete('favorites')->where('id_user', '=',$this->id_user)->execute();

        //delete reviews
        DB::delete('reviews')->where('id_user', '=',$this->id_user)->execute();

        //delete orders
        DB::delete('orders')->where('id_user', '=',$this->id_user)->execute();

        //delete subscribtions
        DB::delete('subscribers')->where('id_user', '=',$this->id_user)->execute();

        //delete posts
        DB::delete('posts')->where('id_user', '=',$this->id_user)->execute();

        //delete messages
        DB::delete('messages')->where('id_user_from', '=',$this->id_user)->or_where('id_user_to', '=',$this->id_user)->execute();

        //unsusbcribe from elasticemail
        if ( Core::config('email.elastic_listname')!='' )
            ElasticEmail::unsubscribe(Core::config('email.elastic_listname'),$this->email);

        parent::delete();
    }

    /**
     * get user ad contacts
     * @return array [description]
     */
    public function contacts()
    {
        if ($this->loaded())
        {
            $query = DB::select('a.id_ad')
                        ->select('a.title')
                        ->select('a.seotitle')
                        ->select('v.id_visit')
                        ->select('v.created')
                        ->from(array('ads', 'a'))
                        ->join(array('visits', 'v'),'INNER')
                        ->on('a.id_ad','=','v.id_ad')
                        ->where('a.id_user','=',$this->id_user)
                        ->where('v.contacts','>','0')
                        ->where('v.created','>', (is_null($this->notification_date))? 0:$this->notification_date)
                        ->order_by('v.created', 'DESC');

            if (is_null($this->notification_date))
                $query->limit(5);

            return $query->execute();
        }

        return FALSE;
    }

    /**
     * checks if we have stored user's lat/lng
     * @return array/boolean
     */
    public static function get_userlatlng()
    {
        if (isset($_COOKIE['mylat'])
            AND is_numeric($_COOKIE['mylat'])
            AND isset($_COOKIE['mylng'])
            AND is_numeric($_COOKIE['mylng']))
        {
            return array(   "lat" => $_COOKIE['mylat'],
                            "lng" => $_COOKIE['mylng'],
                        );
        }
        else return FALSE;
    }


   /**
    * returns a list with custom field values of this user
    * @param  boolean $show_profile only those fields that needs to be displayed on the user profile show_profile===TRUE
    * @param  boolean $hide_admin hide those fields that are reserved for the admin hide_admin===TRUE
    * @return array else false
    */
    public function custom_columns($show_profile = FALSE, $hide_admin = TRUE)
    {
        if($this->loaded())
        {
            //custom fields config, label, name and order
            $cf_config = Model_UserField::get_all(($hide_admin === TRUE)? TRUE:FALSE,FALSE);

            if(!isset($cf_config))
                return array();

            //getting the custom fields this uaser has and his value
            $active_custom_fields = array();
            foreach($this->_table_columns as $value)
            {
                //we want only those that are custom fields
                if(strpos($value['column_name'],'cf_') !== FALSE)
                {
                    $cf_name  = str_replace('cf_', '', $value['column_name']);
                    $cf_column_name = $value['column_name'];
                    $cf_value = $this->$cf_column_name;

                    if(isset($cf_value) AND isset($cf_config->$cf_name))
                    {
                        //formating the value depending on the type
                        switch ($cf_config->$cf_name->type)
                        {
                            case 'checkbox':
                                $cf_value = ($cf_value)?'checkbox_'.$cf_value:NULL;
                                break;
                            case 'radio':
                                $cf_value = isset($cf_config->$cf_name->values[$cf_value-1]) ? $cf_config->$cf_name->values[$cf_value-1] : NULL;
                                break;
                            case 'date':
                                $cf_value = Date::format($cf_value, core::config('general.date_format'));
                                break;
                        }

                        //should it be added to the profile?
                        if ($show_profile == TRUE AND isset($cf_config->$cf_name->show_profile))
                        {
                            //only to the profile
                            if ($cf_config->$cf_name->show_profile==TRUE)
                            {
                                $active_custom_fields[$cf_name] = $cf_value;
                            }
                        }
                        else
                            $active_custom_fields[$cf_name] = $cf_value;
                    }

                }
            }

            // sorting using json order
            $user_custom_vals = array();
            foreach ($cf_config as $name => $value)
            {
                if(isset($active_custom_fields[$name]))
                    $user_custom_vals[$value->label] = $active_custom_fields[$name];
            }


            return $user_custom_vals;

        }
        return array();
    }

    /**
     * get the current subscription of the user
     * @return Model_Subscription
     */
    public function subscription()
    {
        $s = new Model_Subscription();
        $s->where('id_user','=',$this->id_user)
            ->where('status','=',1)
            ->order_by('created','desc')
            ->find();

        return $s;
    }

    /**
     * The user has an expired subscription? is he expired? does need to renew? or no ads available
     * @return bool
     */
    public function expired_subscription($allows_new_user = FALSE)
    {
        //it's the feature enabled?
        if (Core::config('general.subscriptions') == TRUE AND
            Core::config('general.subscriptions_expire') == TRUE)
        {
            //if admin or moderator never need to pay
            if (Auth::instance()->logged_in() AND Auth::instance()->get_user()->is_admin() OR Auth::instance()->get_user()->is_moderator())
                return FALSE;

            //getting user last subscription no matter the status
            $subscription = new Model_Subscription();
            $subscription->where('id_user','=',$this->id_user)->order_by('created','desc')->limit(1)->find();

            //we allow the user to navigate the site with this extra param even if does not have a subscription
            if ($allows_new_user ==TRUE AND !$subscription->loaded())
                return FALSE;
            //he needs a subscription
            elseif(!$subscription->loaded())
                return TRUE;
            
            //verify expired since no ads or cron was not executed...
            if ($subscription->status = 0 OR 
                Date::mysql2unix($subscription->expire_date) < time() OR 
                ($allows_new_user == FALSE AND $subscription->amount_ads_left == 0) )
                return TRUE;
        }

        //by default nothing it's expired
        return FALSE;
    }

    /**
     * sends a push notification to this user
     * @param  string $message, what will be send
     * @param  string $channel, where will be send/to whom
     */
    public static function pusher($channel = NULL, $message = NULL, $content)
    {
        require_once Kohana::find_file('vendor', 'pusher/autoload');

        $options = array(
            'cluster' => Core::config('general.pusher_notifications_cluster'),
            'encrypted' => true
        );
        $pusher = new Pusher(
            Core::config('general.pusher_notifications_key'),
            Core::config('general.pusher_notifications_secret'),
            Core::config('general.pusher_notifications_app_id'),
            $options
        );

        if (core::config('general.messaging') AND strpos($content, 'messaging') !== FALSE) {
            $data['message'] = __('You got a new message.').'<br><br><a href="'.Route::url('oc-panel', array('controller'=>'messages')).'">'.__('Read more').'</a>';
        } else {
            $data['message'] = $message."<br><br>".__('Please check your email');
        }

        $pusher->trigger('user_'.$channel, 'my-event', $data);
    }

    public static function find_by_email($email)
    {
        return (new self())->where('email', '=', $email)->limit(1)->find();
    }

    /**
     * returns the images path name
     * @param  integer $id
     * @param  string  $type
     * @param  string  $version
     * @return string
     */
    public function image_name($id = 1, $type='')
    {
        if (!$this->loaded())
            return FALSE;

        // image variables
        $img_path    = DOCROOT.'images/users/';

        if ($id == 1)
            $filename = $this->id_user.'.png';
        else
            $filename = $this->id_user.'_'.$id.'.png';

        return $img_path.$filename;
    }

    /**
     * Set primary image by swapping ids
     * @param  integer $primary_image
     * @return void
     */
    public function set_primary_image($primary_image)
    {
        // if ad doesn't have at least two images do nothing
        if ($this->has_image < 2)
            return;

        $img_path = DOCROOT.'images/users/';

        // delete image from Amazon S3
        if (core::config('image.aws_s3_active'))
        {
            require_once Kohana::find_file('vendor', 'amazon-s3-php-class/S3','php');
            $s3 = new S3(core::config('image.aws_access_key'), core::config('image.aws_secret_key'));

            //re-ordering image file names
            $s3->copyObject(core::config('image.aws_s3_bucket'), $this->image_name('1'), core::config('image.aws_s3_bucket'), $this->image_name('old'), S3::ACL_PUBLIC_READ);
            $s3->deleteObject(core::config('image.aws_s3_bucket'), $this->image_name('1'));

            $s3->copyObject(core::config('image.aws_s3_bucket'), $this->image_name($primary_image), core::config('image.aws_s3_bucket'), $this->image_name('1'), S3::ACL_PUBLIC_READ);
            $s3->deleteObject(core::config('image.aws_s3_bucket'), $this->image_name($primary_image));

            $s3->copyObject(core::config('image.aws_s3_bucket'), $this->image_name('old'), core::config('image.aws_s3_bucket'), $this->image_name($primary_image), S3::ACL_PUBLIC_READ);
            $s3->deleteObject(core::config('image.aws_s3_bucket'), $this->image_name('old'));
        }

        //re-ordering image file names
        @rename($this->image_name('1'), $this->image_name('old'));
        @rename($this->image_name($primary_image), $this->image_name('1'));
        @rename($this->image_name('old'), $this->image_name($primary_image));

        $this->last_modified = Date::unix2mysql();

        try
        {
            $this->save();
            return TRUE;
        }
        catch (Exception $e)
        {
            throw HTTP_Exception::factory(500,$e->getMessage());
        }

        return FALSE;
    }

    /**
     * save_image upload images with given path
     *
     * @param array image
     * @return bool
     */
    public function save_image($image)
    {
        if (!$this->loaded())
            return FALSE;

        if (
        ! Upload::valid($image) OR
        ! Upload::not_empty($image) OR
        ! Upload::type($image, explode(',',core::config('image.allowed_formats'))) OR
        ! Upload::size($image, core::config('image.max_image_size').'M'))
        {
            if (Upload::not_empty($image) && ! Upload::type($image, explode(',',core::config('image.allowed_formats'))))
            {
                Alert::set(Alert::ALERT, $image['name'].' '.sprintf(__('Is not valid format, please use one of this formats "%s"'),core::config('image.allowed_formats')));
                return FALSE;
            }
            if( ! Upload::size($image, core::config('image.max_image_size').'M'))
            {
                Alert::set(Alert::ALERT, $image['name'].' '.sprintf(__('Is not of valid size. Size is limited to %s MB per image'),core::config('image.max_image_size')));
                return FALSE;
            }
            if( ! Upload::not_empty($image))
                return FALSE;
        }

        if (core::config('image.disallow_nudes') AND ! Upload::not_nude_image($image))
        {
            Alert::set(Alert::ALERT, $image['name'].' '.__('Seems a nude picture so you cannot upload it'));
            return FALSE;
        }

        if ($image !== NULL)
        {
            $directory      = DOCROOT.'images/users/';
            if ($file = Upload::save($image, NULL, $directory))
            {
                return $this->save_image_file($file, $this->has_image + 1);
            }
            else
            {
                Alert::set(Alert::ALERT, __('Something went wrong with uploading pictures, please check format'));
                return FALSE;
            }
        }
    }

    /**
     * save_base64_image upload images with given path
     *
     * @param string $image [base64 encoded image]
     * @return bool
     */
    public function save_base64_image($image)
    {
        if ( ! $this->loaded())
            return FALSE;

        // Temporary save image
        $image_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image));
        $image_tmp = tmpfile();
        $image_tmp_uri = stream_get_meta_data($image_tmp)['uri'];
        file_put_contents($image_tmp_uri, $image_data);

        $image = Image::factory($image_tmp_uri);

        if ( ! in_array($image->mime, explode(',','image/'.str_replace(",", ",image/", core::config('image.allowed_formats')))))
        {
            Alert::set(Alert::ALERT, $image->mime.' '.sprintf(__('Is not valid format, please use one of this formats "%s"'),core::config('image.allowed_formats')));
            return FALSE;
        }

        if (filesize($image_tmp_uri) > Num::bytes(core::config('image.max_image_size').'M'))
        {
            Alert::set(Alert::ALERT, $image->mime.' '.sprintf(__('Is not of valid size. Size is limited to %s MB per image'),core::config('image.max_image_size')));
            return FALSE;
        }

        if (core::config('image.disallow_nudes') AND $image->is_nude_image())
        {
            Alert::set(Alert::ALERT, $image->mime.' '.__('Seems a nude picture so you cannot upload it'));
            return FALSE;
        }

        return $this->save_image_file($image_tmp_uri, $this->has_image+1);
    }

    /**
     * saves image in the disk
     * @param  string  $file
     * @param  integer $num  number of the image
     * @return bool        success?
     */
    public function save_image_file($file, $num = 0)
    {
        if (core::config('image.aws_s3_active'))
        {
            require_once Kohana::find_file('vendor', 'amazon-s3-php-class/S3','php');
            $s3 = new S3(core::config('image.aws_access_key'), core::config('image.aws_secret_key'));
        }

        $directory      = DOCROOT.'images/users/';
        $image_quality  = core::config('image.quality');
        $width          = core::config('image.width');
        $height         = core::config('image.height');

        if( ! is_numeric($height)) // when installing this field is empty, to avoid crash we check here
            $height         = NULL;

        if ($num == 1)
            $filename_original = $this->id_user.'.png';
        else
            $filename_original = $this->id_user.'_'.$num.'.png';

        //if original image is bigger that our constants we resize
        try
        {
            $image_size_orig = getimagesize($file);
        }
        catch (Exception $e)
        {
            return FALSE;
        }

        if($image_size_orig[0] > $width || $image_size_orig[1] > $height)
        {
            Image::factory($file)
                ->orientate()
                ->resize($width, $height, Image::AUTO)
                ->save($directory.$filename_original, $image_quality);
        }
        //we just save the image changing the quality and different name
        else
        {
            Image::factory($file)
                ->orientate()
                ->save($directory.$filename_original, $image_quality);
        }

        // put image and thumb to Amazon S3
        if (core::config('image.aws_s3_active'))
        {
            $s3->putObject($s3->inputFile($directory.$filename_original), core::config('image.aws_s3_bucket'), $path.$filename_original, S3::ACL_PUBLIC_READ);
        }

        // Delete the temporary file
        @unlink($file);

        $this->has_image++;

        try
        {
            $this->save();
            return TRUE;
        }
        catch (Exception $e)
        {
            return FALSE;
        }

    }

} // END Model_User
