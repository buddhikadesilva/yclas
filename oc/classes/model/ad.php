<?php defined('SYSPATH') or die('No direct script access.');
/**
 * description...
 *
 * @author		Chema <chema@open-classifieds.com>
 * @package		OC
 * @copyright	(c) 2009-2013 Open Classifieds Team
 * @license		GPL v3
 *
 */
class Model_Ad extends ORM {

    /**
     * Table name to use
     *
     * @access	protected
     * @var		string	$_table_name default [singular model name]
     */
    protected $_table_name = 'ads';

    /**
     * Column to use as primary key
     *
     * @access	protected
     * @var		string	$_primary_key default [id_ad]
     */
    protected $_primary_key = 'id_ad';

    protected $_belongs_to = array(
        'user'		 => array('foreign_key' => 'id_user'),
        'category'	 => array('foreign_key' => 'id_category'),
    	'location'	 => array('foreign_key' => 'id_location'),
    );


    /**
     * @var  array  ORM Dependency/hirerachy
     */
    protected $_has_many = array(
        'visits' => array(
            'model'       => 'visit',
            'foreign_key' => 'id_ad',
        ),
        'favorites' => array(
            'model'       => 'favorite',
            'foreign_key' => 'id_ad',
        ),
    );

    /**
     * status constants
     */
    const STATUS_NOPUBLISHED = 0; //first status of the item, not published. This status send ad to moderation always. Until it gets his status changed
    const STATUS_PUBLISHED   = 1; // ad it's available and published
    const STATUS_UNCONFIRMED = 20; // this status is for advertisements that need to be confirmed by email,
    const STATUS_SPAM        = 30; // mark as spam
    const STATUS_SOLD        = 40; // mark as sold
    const STATUS_UNAVAILABLE = 50; // item unavailable but previously was / expired


    /**
     * moderation status
     */
    const POST_DIRECTLY         = 0; // create new ad directly
    const MODERATION_ON         = 1; // new ad after creation goes to moderation
    const PAYMENT_ON            = 2; // redirects to payment and after paying there is no moderation
    const EMAIL_CONFIRMATION    = 3; // sends email to confirm ad, until then is in moderation
    const EMAIL_MODERATION      = 4; // sends email to confirm, but admin needs also to validate
    const PAYMENT_MODERATION    = 5; // even after payment, admin still needs to validate

    //this are the moderation statuses that makes moderation link appear
    public static $moderation_status = array(self::MODERATION_ON,
                                            self::EMAIL_MODERATION ,
                                            self::PAYMENT_MODERATION);

    /**
     * global Model Ad instance get from controller so we can access from anywhere like Model_Ad::current()
     * @var Model_Ad
     */
    protected static $_current = NULL;

    /**
     * Require validation
     * @var boolean
     */
    protected $_validation_required = TRUE;

    /**
     * returns the current ad
     * @return Model_Ad
     */
    public static function current()
    {
        //we don't have so let's retrieve
        if (self::$_current === NULL)
        {
            self::$_current = new self();

            if( strtolower(Request::current()->controller()=='Ad')
                AND strtolower(Request::current()->action()) == 'view'
                AND Request::current()->param('seotitle')!==NULL )
            {
                self::$_current = self::$_current->where('seotitle', '=', Request::current()->param('seotitle'))
                                                    ->limit(1)->cached()->find();
            }
        }

        return self::$_current;
    }

    /**
     * Rule definitions for validation
     *
     * @return array
     */
    public function rules()
    {
        if (! $this->validation_required())
        {
            return [];
        }

    	$rules = array(
				        'id_ad'		    => array(array('numeric')),
				        'id_user'		=> array(array('numeric')),
				        'id_category'	=> array(array('not_empty'),array('digit')),
				        'id_location'   => array(array('digit')),
				        'type'			=> array(),
				        'title'			=> array(array('not_empty'), array('min_length', array(':value', 2)), array('max_length', array(':value', 145))),
				        'description'	=> array(array('not_empty'), array('min_length', array(':value', 5)), array('max_length', array(':value', 65535)), ),
				        'address'		=> array(array('max_length', array(':value', 145)), ),
                        'website'       => array(array('max_length', array(':value', 200)), ),
				        'phone'			=> array(array('max_length', array(':value', 30)), ),
				        'status'		=> array(array('numeric')),
				        'has_images'	=> array(array('numeric')),
				        'last_modified'	=> array(),
                        'price'         => array(array('price')),
                        'latitude'      => array(array('regex', array(':value', '/^-?+(?=.*[0-9])[0-9]*+'.preg_quote('.').'?+[0-9]*+$/D'))),
                        'longitude'     => array(array('regex', array(':value', '/^-?+(?=.*[0-9])[0-9]*+'.preg_quote('.').'?+[0-9]*+$/D'))),
                        'locale'        => array(),
				    );

        if (core::config('advertisement.description') == FALSE)
            $rules['description'] = array(array('min_length', array(':value', 5)), array('max_length', array(':value', 65535)), );

        if (core::config('payment.stock')==1)
            $rules['stock'] =  array(array('numeric'));

        if (Core::config('general.multilingual') == 1)
            $rules['locale'] =  array(array('not_empty'));

        return $rules;
    }

    /**
     * Label definitions for validation
     *
     * @return array
     */
    public function labels()
    {
    	return array(
			        'id_ad'		    => 'Id ad',
			        'id_user'		=> __('User'),
			        'id_category'	=> __('Category'),
			        'id_location'	=> __('Location'),
			        'type'			=> __('Type'),
			        'title'			=> __('Title'),
			        'seotitle'		=> __('SEO title'),
			        'description'	=> __('Description'),
			        'address'		=> __('Address'),
			        'price'			=> __('Price'),
			        'phone'			=> __('Phone'),
			        'ip_address'	=> __('Ip address'),
			        'created'		=> __('Created'),
			        'published'		=> __('Published'),
			        'status'		=> __('Status'),
			        'has_images'	=> __('Has images'),
			        'last_modified'	=> __('Last modified'),
			    );
    }

    /**
     *
     * formmanager definitions
     * @param $form
     * @return $insert
     */
    public function form_setup($form)
    {
        $insert = DB::insert('ads', array('title', 'description'))
                            ->values(array($form['title'], $form['description']))
                            ->execute();
                            return $insert;
    }

    /**
     * Get or set validation required
     * @param  bool|null $required
     * @return self
     */
    public function validation_required($required = NULL)
    {
        if ($required === NULL)
        {
            return $this->_validation_required;
        }

        $this->_validation_required = (bool) $required;

        return $this;
    }

    /**
     * generate seo title. return the title formatted for the URL
     *
     * @param string title
     * @return $seotitle (unique string)
     */

    public function gen_seo_title($title)
    {
        $ad = new self;

        $title = URL::title($title);
        $seotitle = $title;

        //find a ad same seotitle
        $a = $ad->where('seotitle', '=', $seotitle)->and_where('id_ad', '!=', $this->id_ad)->limit(1)->find();

        if($a->loaded())
        {
            $cont = 1;
            $loop = TRUE;
            do {
                $attempt = $title.'-'.$cont;
                $ad = new self;
                unset($a);
                $a = $ad->where('seotitle', '=', $attempt)->limit(1)->find();

                if(!$a->loaded())
                {
                    $loop = FALSE;
                    $seotitle = $attempt;
                }
                else $cont++;
            } while ( $loop );
        }

        return $seotitle;
    }


    /**
     *  returns the count of visits
     *
     */
    public function count_ad_hit()
    {
        if ($this->loaded() AND $this->status==Model_Ad::STATUS_PUBLISHED )
        {
            return Model_Visit::count_all_visits($this->id_ad);
        }

        return 0;
    }

    /**
     * Gets all images
     * @return [array] [array with image names]
     */
    public function get_images()
    {
        $image_path = array();

        if($this->loaded() AND $this->has_images > 0)
        {

            $base = Core::S3_domain();
            $route      = $this->image_path();
            $folder     = DOCROOT.$route;
            $seotitle   = $this->seotitle;
            $version    = $this->last_modified ? '?v='.Date::mysql2unix($this->last_modified) : NULL;

            for ($i=1; $i <= $this->has_images; $i++)
            {
                $filename_thumb = 'thumb_'.$seotitle.'_'.$i.'.jpg';
                $filename_original = $seotitle.'_'.$i.'.jpg';
                $image_path[$i]['image'] = $base.$route.$filename_original.$version;
                $image_path[$i]['thumb'] = $base.$route.$filename_thumb.$version;
            }
        }

        return $image_path;
    }

    /**
     * Gets the first image, and checks type of $type
     * @param  string $type [type of image (image or thumb) ]
     * @return string       [image path]
     */
    public function get_first_image($type = 'thumb')
    {
        $images = $this->get_images();

        if(core::count($images) > 0)
            $first_image = reset($images);

        return (isset($first_image[$type])) ? $first_image[$type] : NULL ;
    }


    /**
     * image_path make unique dir path with a given date and id
     * @return string path
     */
    public function image_path()
    {
        if (!$this->loaded())
            return FALSE;

        $obj_date = new DateTime($this->created);

        $path = 'images/'.$obj_date->format('Y/m/d').'/'.$this->id_ad.'/';

        //check if path is a directory
        if ( ! is_dir(DOCROOT.$path) )
        {
            //not a directory, try to create it
            if (! @mkdir(DOCROOT.$path, 0755, TRUE))
                return FALSE;//failed creation :()
        }

        return $path;
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

        $seotitle = $this->seotitle;

        if (
        ! Upload::valid($image) OR
        ! Upload::not_empty($image) OR
        ! Upload::type($image, explode(',',core::config('image.allowed_formats'))) OR
        ! Upload::size($image, core::config('image.max_image_size').'M'))
        {
            if (Upload::not_empty($image) && ! Upload::type($image, explode(',',core::config('image.allowed_formats')))){
                Alert::set(Alert::ALERT, $image['name'].' '.sprintf(__('Is not valid format, please use one of this formats "%s"'),core::config('image.allowed_formats')));
                return FALSE;
            }
            if( ! Upload::size($image, core::config('image.max_image_size').'M')){
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
            $path           = $this->image_path();
            $directory      = DOCROOT.$path;
            if ($file = Upload::save($image, NULL, $directory))
            {
                return $this->save_image_file($file,$this->has_images+1);
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

        return $this->save_image_file($image_tmp_uri, $this->has_images+1);
    }

    /**
     * saves image in the disk
     * @param  string  $file
     * @param  integer $num  number of the image
     * @return bool        success?
     */
    public function save_image_file($file,$num=0)
    {
        if(core::config('image.aws_s3_active'))
        {
            require_once Kohana::find_file('vendor', 'amazon-s3-php-class/S3','php');
            $s3 = new S3(core::config('image.aws_access_key'), core::config('image.aws_secret_key'));
        }

        $path    = $this->image_path();

        if ($path === FALSE)
        {
            Alert::set(Alert::ERROR, 'model\ad.php:save_image(): '.__('Image folder is missing and cannot be created with mkdir. Please correct to be able to upload images.'));
            return FALSE;
        }

        $directory      = DOCROOT.$path;
        $image_quality  = core::config('image.quality');
        $width          = core::config('image.width');
        $width_thumb    = core::config('image.width_thumb');
        $height_thumb   = core::config('image.height_thumb');
        $height         = core::config('image.height');

        if( ! is_numeric($height)) // when installing this field is empty, to avoid crash we check here
            $height         = NULL;
        if( ! is_numeric($height_thumb))
            $height_thumb   = NULL;

        $filename_thumb     = 'thumb_'.$this->seotitle.'_'.$num.'.jpg';
        $filename_original  = $this->seotitle.'_'.$num.'.jpg';

        /*WATERMARK*/
        if(core::config('image.watermark')==TRUE AND is_readable(core::config('image.watermark_path')))
        {
            $mark = Image::factory(core::config('image.watermark_path')); // watermark image object
            $size_watermark = getimagesize(core::config('image.watermark_path')); // size of watermark

            if(core::config('image.watermark_position') == 0) // position center
            {
                $wm_left_x = $width/2-$size_watermark[0]/2; // x axis , from left
                $wm_top_y = NULL; // centers the y offset
            }
            elseif (core::config('image.watermark_position') == 1) // position bottom
            {
                $wm_left_x = $width/2-$size_watermark[0]/2; // x axis , from left
                $wm_top_y = $height-10; // y axis , from top
            }
            elseif(core::config('image.watermark_position') == 2) // position top
            {
                $wm_left_x = $width/2-$size_watermark[0]/2; // x axis , from left
                $wm_top_y = 10; // y axis , from top
            }
        }
        /*end WATERMARK variables*/


        //if original image is bigger that our constants we resize
        try {
            $image_size_orig = getimagesize($file);
        } catch (Exception $e) {
            return FALSE;
        }


        if($image_size_orig[0] > $width || $image_size_orig[1] > $height)
        {
            if(core::config('image.watermark') AND is_readable(core::config('image.watermark_path'))) // watermark ON
            {
                Image::factory($file)
                    ->orientate()
                    ->resize($width, $height, Image::AUTO)
                    ->watermark( $mark, $wm_left_x, $wm_top_y) // CUSTOM FUNCTION (kohana)
                    ->save($directory.$filename_original,$image_quality);
            }
            else
            {
                Image::factory($file)
                    ->orientate()
                    ->resize($width, $height, Image::AUTO)
                    ->save($directory.$filename_original,$image_quality);
            }
        }
        //we just save the image changing the quality and different name
        else
        {
            if(core::config('image.watermark') AND is_readable(core::config('image.watermark_path')))
            {
                Image::factory($file)
                    ->orientate()
                    ->watermark( $mark, $wm_left_x, $wm_top_y) // CUSTOM FUNCTION (kohana)
                    ->save($directory.$filename_original,$image_quality);
            }
            else
            {
                Image::factory($file)
                    ->orientate()
                    ->save($directory.$filename_original,$image_quality);
            }
        }

        //creating the thumb and resizing using the the biggest side INVERSE
        Image::factory($file)
            ->orientate()
            ->resize($width_thumb, $height_thumb, Image::INVERSE)
            ->save($directory.$filename_thumb,$image_quality);

        //check if the height or width of the thumb is bigger than default then crop
        if ($height_thumb!==NULL)
        {
            $image_size_orig = getimagesize($directory.$filename_thumb);
            if ($image_size_orig[1] > $height_thumb || $image_size_orig[0] > $width_thumb)
            {
                Image::factory($directory.$filename_thumb)
                            ->crop($width_thumb, $height_thumb)
                            ->save($directory.$filename_thumb);
            }
        }

        // put image and thumb to Amazon S3
        if(core::config('image.aws_s3_active'))
        {
            $s3->putObject($s3->inputFile($directory.$filename_original), core::config('image.aws_s3_bucket'), $path.$filename_original, S3::ACL_PUBLIC_READ);
            $s3->putObject($s3->inputFile($directory.$filename_thumb), core::config('image.aws_s3_bucket'), $path.$filename_thumb, S3::ACL_PUBLIC_READ);
        }

        // Delete the temporary file
        @unlink($file);


        $this->has_images++;

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
        $img_path    = $this->image_path();
        $img_seoname = $this->seotitle;

        if ($type=='thumb')
            $type = 'thumb_';

        return $img_path.$type.$img_seoname.'_'.$id.'.jpg';
    }

    /**
     * Deletes image from edit ad
     * @return bool
     */

    public function delete_images()
    {
        if (!$this->loaded())
            return FALSE;

        $img_path = DOCROOT.$this->image_path();

        if(core::config('image.aws_s3_active') AND $this->has_images > 0)
        {
            require_once Kohana::find_file('vendor', 'amazon-s3-php-class/S3','php');
            $s3 = new S3(core::config('image.aws_access_key'), core::config('image.aws_secret_key'));

            for ($i=1; $i <= $this->has_images; $i++)
            {
                $s3->deleteObject(core::config('image.aws_s3_bucket'), $this->image_name($i));
                $s3->deleteObject(core::config('image.aws_s3_bucket'), $this->image_name($i, 'thumb'));
            }
        }

        if (!is_dir($img_path))
            return FALSE;

        File::delete($img_path);

        return TRUE;
    }

    /**
     * [delete_image description]
     * @param  integer $deleted_image
     * @return void
     */
    public function delete_image($deleted_image)
    {
        $img_path = $this->image_path();

        // delete image from Amazon S3
        if (core::config('image.aws_s3_active'))
        {
            require_once Kohana::find_file('vendor', 'amazon-s3-php-class/S3','php');
            $s3 = new S3(core::config('image.aws_access_key'), core::config('image.aws_secret_key'));

            //delete original image
            $s3->deleteObject(core::config('image.aws_s3_bucket'), $this->image_name($deleted_image));
            //delete formated image
            $s3->deleteObject(core::config('image.aws_s3_bucket'), $this->image_name($deleted_image,'thumb'));

            //re-ordering image file names
            for($i = $deleted_image; $i < $this->has_images; $i++)
            {
                //rename original image
                $s3->copyObject(core::config('image.aws_s3_bucket'), $this->image_name(($i+1)), core::config('image.aws_s3_bucket'), $this->image_name($i), S3::ACL_PUBLIC_READ);
                $s3->deleteObject(core::config('image.aws_s3_bucket'), $this->image_name(($i+1)));
                //rename formated image
                $s3->copyObject(core::config('image.aws_s3_bucket'), $this->image_name(($i+1),'thumb'), core::config('image.aws_s3_bucket'), $this->image_name($i,'thumb'), S3::ACL_PUBLIC_READ);
                $s3->deleteObject(core::config('image.aws_s3_bucket'), $this->image_name(($i+1),'thumb'));
            }
        }

        //delete image from local filesystem
        if (!is_dir($img_path))
            return FALSE;
        else
        {
            //delete original image
            @unlink($this->image_name($deleted_image));
            //delete formated image
            @unlink($this->image_name($deleted_image,'thumb'));

            //re-ordering image file names
            for($i = $deleted_image; $i < $this->has_images; $i++)
            {
                @rename($this->image_name(($i+1)), $this->image_name($i));
                @rename($this->image_name(($i+1),'thumb'), $this->image_name($i,'thumb'));
            }
        }
        $this->has_images = ($this->has_images > 0) ? $this->has_images-1 : 0;
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
     * Delete video from edit ad
     * @param  string $video_field
     * @return bool
     */

    public function delete_video($video_field)
    {
        if (!$this->loaded())
            return FALSE;

        if(! isset($this->{$video_field}))
        {
            return FALSE;
        }

        if(core::config('advertisement.cloudinary_api_key') AND core::config('advertisement.cloudinary_api_secret'))
        {
            $video_attributes = json_decode($this->{$video_field});

            if(isset($video_attributes->public_id))
            {
                require_once Kohana::find_file('vendor', 'cloudinary_php/autoload','php');

                \Cloudinary::config([
                    'cloud_name' => core::config('advertisement.cloudinary_cloud_name'),
                    'api_key' => core::config('advertisement.cloudinary_api_key'),
                    'api_secret' => core::config('advertisement.cloudinary_api_secret'),
                    'secure' => true,
                ]);

                $result = \Cloudinary\Uploader::destroy($video_attributes->public_id, ['resource_type' => 'video']);
            }
        }

        $this->{$video_field} = NULL;
        $this->save();

        return TRUE;
    }

    /**
     * Delete videos from edit ad
     * @return bool
     */

    public function delete_videos()
    {
        if(! $this->loaded())
        {
            return FALSE;
        }

        $custom_fields = Model_Field::get_all(FALSE);

        if(! isset($custom_fields))
        {
            return FALSE;
        }

        foreach($this->_table_columns as $value)
        {
            //we want only those that are custom fields
            if(strpos($value['column_name'], 'cf_') !== FALSE)
            {
                $field_column_name  = $value['column_name'];
                $field_name  = str_replace('cf_', '', $field_column_name);

                if (isset($custom_fields->{$field_name}) AND $custom_fields->{$field_name}->type == 'video')
                {
                    $this->delete_video($field_column_name);
                }
            }
        }

        return TRUE;
    }

    /**
     * Set primary image by swapping ids
     * @param  integer $primary_image
     * @return void
     */
    public function set_primary_image($primary_image)
    {
        // if ad doesn't have at least two images do nothing
        if ($this->has_images < 2)
            return;

        $img_path = $this->image_path();

        // delete image from Amazon S3
        if (core::config('image.aws_s3_active'))
        {
            require_once Kohana::find_file('vendor', 'amazon-s3-php-class/S3','php');
            $s3 = new S3(core::config('image.aws_access_key'), core::config('image.aws_secret_key'));

            //re-ordering image file names
            $s3->copyObject(core::config('image.aws_s3_bucket'), $this->image_name('1'), core::config('image.aws_s3_bucket'), $this->image_name('1_old'), S3::ACL_PUBLIC_READ);
            $s3->deleteObject(core::config('image.aws_s3_bucket'), $this->image_name('1'));
            $s3->copyObject(core::config('image.aws_s3_bucket'), $this->image_name('1', 'thumb'), core::config('image.aws_s3_bucket'), $this->image_name('1_old', 'thumb'), S3::ACL_PUBLIC_READ);
            $s3->deleteObject(core::config('image.aws_s3_bucket'), $this->image_name('1', 'thumb'));

            $s3->copyObject(core::config('image.aws_s3_bucket'), $this->image_name($primary_image), core::config('image.aws_s3_bucket'), $this->image_name('1'), S3::ACL_PUBLIC_READ);
            $s3->deleteObject(core::config('image.aws_s3_bucket'), $this->image_name($primary_image));
            $s3->copyObject(core::config('image.aws_s3_bucket'), $this->image_name($primary_image, 'thumb'), core::config('image.aws_s3_bucket'), $this->image_name('1', 'thumb'), S3::ACL_PUBLIC_READ);
            $s3->deleteObject(core::config('image.aws_s3_bucket'), $this->image_name($primary_image, 'thumb'));

            $s3->copyObject(core::config('image.aws_s3_bucket'), $this->image_name('1_old'), core::config('image.aws_s3_bucket'), $this->image_name($primary_image), S3::ACL_PUBLIC_READ);
            $s3->deleteObject(core::config('image.aws_s3_bucket'), $this->image_name('1_old'));
            $s3->copyObject(core::config('image.aws_s3_bucket'), $this->image_name('1_old', 'thumb'), core::config('image.aws_s3_bucket'), $this->image_name($primary_image, 'thumb'), S3::ACL_PUBLIC_READ);
            $s3->deleteObject(core::config('image.aws_s3_bucket'), $this->image_name('1_old', 'thumb'));
        }

        //re-ordering image file names
        @rename($this->image_name('1'), $this->image_name('1_old'));
        @rename($this->image_name('1', 'thumb'), $this->image_name('1_old', 'thumb'));

        @rename($this->image_name($primary_image), $this->image_name('1'));
        @rename($this->image_name($primary_image, 'thumb'), $this->image_name('1', 'thumb'));

        @rename($this->image_name('1_old'), $this->image_name($primary_image));
        @rename($this->image_name('1_old', 'thumb'), $this->image_name($primary_image, 'thumb'));

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
     * tells us if this ad can be contacted
     * @return bool
     */
    public function can_contact()
    {
        if($this->loaded())
        {
            if ($this->status == self::STATUS_PUBLISHED AND core::config('advertisement.contact') != FALSE )
            {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * prints the map script from the view
     * @return string HTML or false in case not loaded
     */
    public function map()
    {
        if($this->loaded())
        {
            if (core::config('advertisement.map')==1 AND $this->latitude AND $this->longitude)
            {
                return View::factory('pages/ad/map',array('ad'=>$this))->render();
            }
        }

        return FALSE;
    }

    /**
     * prints the QR code script from the view
     * @return string HTML or false in case not loaded
     */
    public function qr()
    {
        if($this->loaded())
        {
            if ($this->status == self::STATUS_PUBLISHED AND core::config('advertisement.qr_code')==1 )
            {
                return core::generate_qr(Route::url('ad', array('controller'=>'ad','category'=>$this->category->seoname,'seotitle'=>$this->seotitle)));
            }
        }

        return FALSE;
    }

    /**
     * return button to flag/report the ad
     * @return string html
     */
    public function flagad()
    {
        if($this->loaded() AND $this->status == self::STATUS_PUBLISHED )
        {
            return View::factory('pages/ad/flag',array('ad'=>$this))->render();
        }
    }

    /**
     * prints product structured data
     * @return string html
     */
    public function structured_data()
    {
        if (core::config('advertisement.rich_snippets')
            AND $this->loaded()
            AND $this->status == self::STATUS_PUBLISHED)
        {
            return View::factory('pages/ad/json-ld', ['ad' => $this])->render();
        }
    }

    /**
     * prints the comments script from the view
     * @return string HTML or false in case not loaded
     */
    public function comments()
    {
        if($this->loaded())
        {
            // Publisher doesn't want comments
            if (isset($this->cf_commentsdisabled) AND (bool) $this->cf_commentsdisabled)
                return FALSE;

            return $this->fbcomments().$this->disqus();
        }

        return FALSE;
    }

    /**
     * prints the disqus script from the view
     * @return string HTML or false in case not loaded
     */
    public function fbcomments()
    {
        if($this->loaded())
        {
            if ($this->status == self::STATUS_PUBLISHED AND strlen(core::config('advertisement.fbcomments'))>0 )
            {
                return View::factory('pages/ad/fbcomments',
                                array(  'fbcomments'=>core::config('advertisement.fbcomments'),
                                        'datahref'=>Route::url('ad', array('controller'=>'ad','category'=>$this->category->seoname,'seotitle'=>$this->seotitle))))
                        ->render();
            }
        }

        return FALSE;
    }

    /**
     * prints the disqus script from the view
     * @return string HTML or false in case not loaded
     */
    public function disqus()
    {
        if($this->loaded())
        {
            if ($this->status == self::STATUS_PUBLISHED AND strlen(core::config('advertisement.disqus'))>0 )
            {
                return View::factory('pages/disqus',
                                array('disqus'=>core::config('advertisement.disqus')))
                        ->render();
            }
        }

        return FALSE;
    }


   /**
    * returns a list with custom field values of this ad
    * @param  boolean $show_listing only those fields that needs to be displayed on the list of ads show_listing===TRUE
    * @return array else false
    */
    public function custom_columns($show_listing = FALSE, $edit_ad = FALSE)
    {
        if($this->loaded())
        {
            //is the admin getting the CF fields?
            $is_admin = FALSE;
            if (Auth::instance()->logged_in())
                if (Auth::instance()->get_user()->is_admin())
                    $is_admin = TRUE;

            //custom fields config, label, name and order
            $cf_config = Model_Field::get_all(FALSE);

            if(!isset($cf_config))
                return array();

            //getting the custom fields this advertisement has and his value
            $active_custom_fields = array();
            foreach($this->_table_columns as $value)
            {
                //we want only those that are custom fields
                if(strpos($value['column_name'],'cf_') !== FALSE)
                {
                    $cf_name  = str_replace('cf_', '', $value['column_name']);

                    // find field group
                    $cf_group_name = strtolower(explode('_', $cf_name)[0]);

                    if (isset($cf_config->$cf_group_name) AND $cf_config->$cf_group_name->type == 'checkbox_group')
                    {
                        $cf_config->$cf_name = clone $cf_config->$cf_group_name;
                        $cf_config->$cf_name->label = $cf_config->$cf_group_name->grouped_values->$cf_name;
                        $cf_config->$cf_name->parent = $cf_config->$cf_group_name;
                    }

                    $cf_column_name = $value['column_name'];
                    $cf_value = $this->$cf_column_name;

                    //if the CF has value need to be only seen by admin
                    $display = FALSE;

                    if ($is_admin === TRUE)
                        $display = TRUE;
                    elseif (isset($cf_config->$cf_name->admin_privilege))
                    {
                        if ($cf_config->$cf_name->admin_privilege==FALSE)
                            $display = TRUE;
                    }

                    if (in_array($cf_column_name, Model_Field::fields_to_hide()) AND $edit_ad == FALSE )
                        $display = FALSE;

                    if(isset($cf_value) AND $display )
                    {
                        //formating the value depending on the type
                        switch ($cf_config->$cf_name->type)
                        {
                            case 'checkbox_group':
                                $cf_value = ($cf_value) ? 'checkbox_' . $cf_value : NULL;
                                break;
                            case 'checkbox':
                                $cf_value = ($cf_value)?'checkbox_'.$cf_value:NULL;
                                break;
                            case 'radio':
                                $cf_value = isset($cf_config->$cf_name->values[$cf_value-1]) ? $cf_config->$cf_name->values[$cf_value-1] : NULL;
                                break;
                            case 'date':
                                if(strtolower(Request::current()->controller()) != 'myads' AND strtolower(Request::current()->action()) != 'update')
                                    $cf_value = Date::format($cf_value, core::config('general.date_format'));
                                break;
                            case 'file':
                            case 'file_dropbox':
                            case 'file_gpicker':
                                $cf_value = '<a'.HTML::attributes(['class' => 'btn btn-success', 'href' => $cf_value]).'>'.__('Download').'</a>';
                                break;
                            case 'url':
                                if ($edit_ad == FALSE)
                                    $cf_value = '<a'.HTML::attributes(['href' => $cf_value, 'title' => $cf_config->$cf_name->tooltip, 'data-toggle' => 'tooltip', 'target' => '_blank']).'>'.$cf_config->$cf_name->label.'</a>';
                                break;
                            case 'textarea_bbcode':
                                $cf_value = Text::bb2html($cf_value, TRUE);
                                break;
                            case 'money':
                                $cf_value = i18n::money_format($cf_value, $this->currency());
                                break;
                            case 'video':
                                $video_attributes = json_decode($cf_value);

                                if($edit_ad == FALSE AND isset($video_attributes->url))
                                {
                                    $cf_value = '<div'.HTML::attributes(['class' => '']).'>'.'<video'.HTML::attributes(['controls' => 'controls', 'class' => 'img-responsive']).'>'.'<source'.HTML::attributes(['src' => $video_attributes->url, 'type' => 'video/mp4']).'>'.'</video>'.'</div>';
                                }

                                break;
                        }

                        //should it be added to the listing? //I added the isset since those who update may not have this field ;)
                        if ($show_listing == TRUE AND isset($cf_config->$cf_name->show_listing))
                        {
                            //only to the listing
                            if ($cf_config->$cf_name->show_listing===TRUE)
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
            $ad_custom_vals = array();
            foreach ($cf_config as $name => $value)
            {
                if(isset($active_custom_fields[$name]))
                {
                    if ($edit_ad == TRUE OR $value->type != 'url')
                    {
                        if ($value->type == 'checkbox_group')
                        {
                            $ad_custom_vals[Model_field::translate_label((array) $value->parent)] = '';
                        }

                        $ad_custom_vals[Model_field::translate_label((array) $value)] = $active_custom_fields[$name];
                    }
                    else
                    {
                        if ($value->type == 'checkbox_group')
                        {
                            $ad_custom_vals[Model_field::translate_label((array) $value->parent)] = '';
                        }

                        $ad_custom_vals[] = $active_custom_fields[$name];
                    }
                }
            }

            return $ad_custom_vals;
        }

        return array();
    }


    /**
     * returns related ads
     * @return view
     */
    public function related()
    {
        if ($this->loaded() AND core::config('advertisement.related') > 0 )
        {
            $ads = new self();
            $ads->where('id_ad', '!=', $this->id_ad)
                ->where('status', '=', self::STATUS_PUBLISHED);

            // filter by language
            if (Core::config('general.multilingual') == 1)
            {
                $ads->where('locale', '=', i18n::$locale);
            }

            //if ad have passed expiration time dont show
            if((New Model_Field())->get('expiresat'))
            {
                $ads->where_open()
                ->or_where(DB::expr('DATE(cf_expiresat)'), '>', Date::unix2mysql())
                ->or_where('cf_expiresat','IS',NULL)
                ->where_close();
            }
            elseif (core::config('advertisement.expire_date') > 0)
            {
                $ads->where(DB::expr('DATE_ADD( published, INTERVAL '.core::config('advertisement.expire_date').' DAY)'), '>', Date::unix2mysql());
            }

            //if the ad has passed event date don't show
            if((New Model_Field())->get('eventdate'))
            {
                $ads->where_open()
                ->or_where(DB::expr('cf_eventdate'), '>', Date::unix2mysql())
                ->or_where('cf_eventdate','IS',NULL)
                ->where_close();
            }

            $ads->limit(core::config('advertisement.related'))
                ->order_by(DB::expr('RAND()'));

            $related_ads = clone $ads;
            $related_ads = $related_ads->where('id_category', '=', $this->id_category)
                ->where('id_location', '=', $this->id_location)
                ->cached()
                ->find_all();

            if (core::count($related_ads) == 0)
            {
                $related_ads = clone $ads;
                $related_ads = $related_ads->where_open()
                    ->or_where('id_category', '=', $this->id_category)
                    ->or_where('id_location', '=', $this->id_location)
                    ->where_close()
                    ->cached()
                    ->find_all();
            }

            return View::factory('pages/ad/related',array('ads' => $related_ads))->render();
        }

        return FALSE;
    }



    public function sale (Model_Order $order)
    {
        if ($this->loaded())
        {
            // decrease limit of ads, if 0 deactivate
            if (core::config('payment.stock')==1 AND ($this->stock > 0 OR $this->stock == NULL))
            {
                $this->stock = $this->stock !== NULL ? $this->stock - $order->quantity : $this->stock;

                //deactivate the ad
                if ($this->stock == 0 OR $this->stock == NULL)
                {
                    $this->sold();

                    //send email to owner that he run out of stock
                    $url_edit = $this->user->ql('oc-panel', array( 'controller' => 'myads',
                                                                   'action'     => 'update',
                                                                   'id'         => $this->id_ad), TRUE);

                    $email_content = array( '[URL.EDIT]' => $url_edit,
                                            '[AD.TITLE]' => $this->title);

                    // send email to ad OWNER
                    $this->user->email('out-of-stock', $email_content);
                }

            }
            else
            {
                $this->sold();
            }

            try {
                $this->save();
            } catch (Exception $e) {
                throw HTTP_Exception::factory(500,$e->getMessage());
            }


            $url_ad = Route::url('ad', array('category'=>$this->category->seoname,'seotitle'=>$this->seotitle));

            $buyer_instructions = NULL;

            if (isset($this->cf_buyer_instructions))
                $buyer_instructions = $this->cf_buyer_instructions;

            if (isset($this->cf_file_download))
                $buyer_instructions .= '<a'.HTML::attributes(['href' => $this->cf_file_download]).'>'.__('Download').'</a>';

            $email_content = array( '[URL.AD]'     => $url_ad,
                                    '[AD.TITLE]'   => $this->title,
                                    '[AD.DESCRIPTION]'   => $this->description,
                                    '[AD.URL]'   => $url_ad,
                                    '[ORDER.ID]'   => $order->id_order,
                                    '[ORDER.AMOUNT]'   => i18n::money_format($order->amount, $order->currency),
                                    '[PRODUCT.ID]' => $order->id_product,
                                    '[BUYER.INSTRUCTIONS]' => $buyer_instructions,
                                    '[VAT.COUNTRY]'    => (isset($order->VAT) AND $order->VAT > 0)?$order->VAT_country:'',
                                    '[VAT.NUMBER]'     => (isset($order->VAT) AND $order->VAT > 0)?$order->VAT_number:'',
                                    '[VAT.PERCENTAGE]' => (isset($order->VAT) AND $order->VAT > 0)?$order->VAT:'',
                                    '[CUSTOMER.NAME]' => $order->user->name,
                                    '[CUSTOMER.EMAIL]' => $order->user->email,
                                    '[CUSTOMER.PHONE]' => $order->user->phone,
                                    '[CUSTOMER.ADDRESS]' => $order->user->address);

            // send email to BUYER
            $order->user->email('ads-purchased', $email_content);

            // send email to ad OWNER
            $this->user->email('ads-sold', $email_content);


        }


    }

    /**
     * tops up an advertisement
     * @return void
     */
    public function to_top()
    {
        if($this->loaded())
        {
            $this->published = Date::unix2mysql();
            try {
                $this->save();
            } catch (Exception $e) {
                throw HTTP_Exception::factory(500,$e->getMessage());
            }
        }
    }

    /**
     * features an advertisement
     * @param $days days to be featured
     * @return void
     */
    public function to_feature($days = NULL)
    {

        if($this->loaded())
        {
            if (!is_numeric($days))
            {
                $plans = Model_Order::get_featured_plans();
                $days  = array_keys($plans);
                $days  = reset($days);
            }

            $this->featured = Date::unix2mysql(time() + ($days * 24 * 60 * 60));
            try {
                $this->save();
                Social::social_post_featured_ad($this);
            } catch (Exception $e) {
                throw HTTP_Exception::factory(500,$e->getMessage());
            }
        }
    }

    /**
     * unfeatures an advertisement
     * @return void
     */
    public function unfeature()
    {
        if($this->loaded())
        {
            $this->featured = NULL;
            try {
                $this->save();
            } catch (Exception $e) {
                throw HTTP_Exception::factory(500,$e->getMessage());
            }
        }
    }

    /**
     * paid for a category, notify user and publish ad if needed
     * @return void
     */
    public function paid_category()
    {
        if($this->loaded())
        {
            $moderation = core::config('general.moderation');

            if($moderation == Model_Ad::PAYMENT_ON)
            {
                $this->published = Date::unix2mysql();
                $this->status    = Model_Ad::STATUS_PUBLISHED;

                try {
                    $this->save();
                } catch (Exception $e) {
                    throw HTTP_Exception::factory(500,$e->getMessage());
                }

                //notify ad is published
                $url_cont = $this->user->ql('contact', array());
                $url_ad = $this->user->ql('ad', array('category'=>$this->category->seoname,
                                                    'seotitle'=>$this->seotitle));

                $ret = $this->user->email('ads-user-check',array('[URL.CONTACT]'  =>$url_cont,
                                                            '[URL.AD]'      =>$url_ad,
                                                            '[AD.NAME]'     =>$this->title));

            }
            elseif($moderation == Model_Ad::PAYMENT_MODERATION)
            {
                //he paid but stays in moderation
                $url_ql = $this->user->ql('oc-panel',array( 'controller'=> 'myads',
                                                      'action'    => 'update',
                                                      'id'        => $this->id_ad));

                $ret = $this->user->email('ads-notify',array('[URL.QL]'=>$url_ql,
                                                       '[AD.NAME]'=>$this->title));
            }
        }
    }

    /**
     * returns and order for the given product, great to check if was paid or not
     * @param  int  $id_product Model_Order::PRODUCT_
     * @return boolean/Model_Order             false if not found, Model_Order if found
     */
    public function get_order($id_product = Model_Order::PRODUCT_CATEGORY)
    {
        if ($this->loaded())
        {
            //get if theres an unpaid order for this product and this ad
            $order = new Model_Order();
            $order->where('id_ad',      '=', $this->id_ad)
                  ->where('id_user',    '=', $this->user->id_user)
                  ->where('id_product', '=', $id_product)
                  ->limit(1)->find();

            return ($order->loaded())?$order:FALSE;
        }
        return FALSE;
    }

    /**
     * saves the ads review rates recalculating it
     * @return [type] [description]
     */
    public function recalculate_rate()
    {
        if($this->loaded())
        {
            //get all the rates and divide by them
            $this->rate = Model_Review::get_ad_rate($this);
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

        $this->delete_videos();

        $this->delete_images();

        //delete favorites
        DB::delete('favorites')->where('id_ad', '=',$this->id_ad)->execute();

        //delete reviews
        DB::delete('reviews')->where('id_ad', '=',$this->id_ad)->execute();

        //delete orders
        DB::update('orders')->set(array('id_ad' => NULL))->where('id_ad', '=',$this->id_ad)->execute();

        //remove visits ads
        DB::delete('visits')->where('id_ad', '=',$this->id_ad)->execute();

        //remove messages ads
        DB::update('messages')->set(array('id_ad' => NULL))->where('id_ad', '=',$this->id_ad)->execute();

        parent::delete();
    }


    /**
     * saves an ad changes status etc...
     * @param  array $data
     * @return array
     */
    public function save_ad($data)
    {
        $return_message = '';
        $checkout_url   = '';

        if ($this->loaded())
        {
            //save original category to see if was changed
            $original_category = $this->category;

            $this->last_modified = Date::unix2mysql(); //TODO review doesnt break anything

            $this->values($data);

            // update status on re-stock
            if (isset($data['stock']) AND is_numeric($data['stock']))
            {
                if ($data['stock'] == 0)
                    $this->status = Model_Ad::STATUS_UNAVAILABLE;
                elseif ($data['stock'] > 0 AND in_array($this->status, [Model_Ad::STATUS_UNAVAILABLE, Model_Ad::STATUS_SOLD]))
                    $this->status = Model_Ad::STATUS_PUBLISHED;
            }

            try {
                $this->save();
            }
            catch (ORM_Validation_Exception $e)
            {
                return array('validation_errors' => $e->errors('ad'));
            }
            catch (Exception $e)
            {
                return array('error' => $e->getMessage(),'error_type'=>Alert::ALERT);
            }

            $moderation = core::config('general.moderation');

            //payment for category only if category changed
            if( (   $moderation == Model_Ad::PAYMENT_ON
                    OR $moderation == Model_Ad::PAYMENT_MODERATION
                )
                AND isset ($data['id_category']) AND $data['id_category'] !== $original_category->id_category )
            {
                $amount = 0;
                $new_cat = new Model_Category($data['id_category']);

                // check category price, if 0 check parent
                if($new_cat->price == 0)
                {
                    $cat_parent = new Model_Category($new_cat->id_category_parent);

                    //category without price
                    if($cat_parent->price == 0)
                    {
                        //swapping moderation since theres no price :(
                        if ($moderation == Model_Ad::PAYMENT_ON)
                            $moderation = Model_Ad::POST_DIRECTLY;
                        elseif($moderation == Model_Ad::PAYMENT_MODERATION)
                            $moderation = Model_Ad::MODERATION_ON;
                    }
                    else
                        $amount = $cat_parent->price;
                }
                else
                    $amount = $new_cat->price;

                //only process apyment if you need to pay
                if ($amount > 0)
                {
                    try {
                        $this->status = Model_Ad::STATUS_NOPUBLISHED;

                        $this->save();
                    }
                    catch (Exception $e){
                        throw HTTP_Exception::factory(500,$e->getMessage());
                    }

                    $order = Model_Order::new_order($this, $this->user, Model_Order::PRODUCT_CATEGORY, $amount, NULL, Model_Order::product_desc(Model_Order::PRODUCT_CATEGORY).' '.$new_cat->name);
                    // redirect to invoice
                    $return_message = __('Please pay before we publish your advertisement.');
                    $checkout_url = Route::url('default', array('controller'=> 'ad','action'=>'checkout' , 'id' => $order->id_order));
                    return array('message'=>$return_message,'checkout_url'=>$checkout_url);
                }

            }

            // ad edited but we have moderation on, so goes to moderation queue unless you are admin
            if( ($moderation == Model_Ad::MODERATION_ON
                OR $moderation == Model_Ad::EMAIL_MODERATION
                OR $moderation == Model_Ad::PAYMENT_MODERATION) AND (Auth::instance()->logged_in() AND !Auth::instance()->get_user()->is_admin()) )
            {
                //notify admins new ad
                $this->notify_admins();

                $return_message =  __('Advertisement is updated, but first administrator needs to validate. Thank you for being patient!');
                $this->status = Model_Ad::STATUS_NOPUBLISHED;
                $this->save();
            }
            else
            {
                $return_message =  __('Advertisement is updated');
            }

        }

        return array('message'=>$return_message,'checkout_url'=>$checkout_url);

    }


    /**
     * creates a new ad
     * @param  array $data
     * @param  model_user $user
     * @return array
     */
    public static function new_ad($data,$user)
    {
        $return_message = '';
        $checkout_url   = '';

        //akismet spam filter
        if( isset($data['title']) AND  isset($data['description']) AND core::akismet($data['title'], $user->email, $data['description']) == TRUE)
        {
            // is user marked as spammer? Make him one :)
            if(core::config('general.black_list'))
               $user->user_spam();

            return array('error' => __('This post has been considered as spam! We are sorry but we can not publish this advertisement.'),
                         'error_type' => Alert::ALERT);
        }//akismet

        $ad = new Model_Ad();
        $ad->id_user = $user->id_user;
        $ad->values($data);
        $ad->seotitle   = $ad->gen_seo_title($ad->title);
        $ad->created    = Date::unix2mysql();

        try {
            $ad->save();
        }
        catch (ORM_Validation_Exception $e)
        {
            return array('validation_errors' => $e->errors('ad'));
        }
        catch (Exception $e)
        {
            return array('error'        => $e->getMessage(),
                         'error_type'   => Alert::ALERT);
        }


        /////////// NOTIFICATION Emails,messages to user and Status of the ad

        // depending on user flow (moderation mode), change usecase
        $moderation = core::config('general.moderation');

        //calculate how much he needs to pay in case we have payment on
        if (in_array($moderation, [Model_Ad::PAYMENT_ON, Model_Ad::PAYMENT_MODERATION]))
        {
            // check category price
            $amount = $ad->category->price > 0 ? $ad->category->price : $ad->category->parent->price;

            //swapping moderation since theres no price :(
            if ($amount == 0)
            {
                if ($moderation == Model_Ad::PAYMENT_MODERATION)
                    $moderation = Model_Ad::MODERATION_ON;
                else
                    $moderation = Model_Ad::POST_DIRECTLY;
            }
        }

        //where and what we say to the user depending ont he moderation
        switch ($moderation)
        {
            case Model_Ad::PAYMENT_ON:
            case Model_Ad::PAYMENT_MODERATION:

                    $ad->status = Model_Ad::STATUS_NOPUBLISHED;
                    $order = Model_Order::new_order($ad, $user, Model_Order::PRODUCT_CATEGORY, $amount, NULL, Model_Order::product_desc(Model_Order::PRODUCT_CATEGORY).' '.$ad->category->name);
                    // redirect to invoice
                    $return_message = __('Please pay before we publish your advertisement.');
                    $checkout_url = Route::url('default', array('controller'=> 'ad','action'=>'checkout' , 'id' => $order->id_order));
                break;

            case Model_Ad::EMAIL_MODERATION:
            case Model_Ad::EMAIL_CONFIRMATION:

                    $ad->status = Model_Ad::STATUS_UNCONFIRMED;
                    $url_ql = $user->ql('oc-panel',array( 'controller'=> 'myads',
                                                  'action'    => 'confirm',
                                                  'id'        => $ad->id_ad));

                    $user->email('ads-confirm',array('[URL.QL]'=>$url_ql,
                                                    '[AD.NAME]'=>$ad->title));
                    $return_message = __('Advertisement is posted but first you need to activate. Please check your email!');
                break;

            case Model_Ad::MODERATION_ON:

                    $ad->status = Model_Ad::STATUS_NOPUBLISHED;
                    $url_ql = $user->ql('oc-panel',array( 'controller'=> 'myads',
                                                  'action'    => 'update',
                                                  'id'        => $ad->id_ad));

                    $user->email('ads-notify',array('[URL.QL]'       =>$url_ql,
                                                   '[AD.NAME]'      =>$ad->title,)); // email to notify user of creating, but it is in moderation currently
                    $return_message = __('Advertisement is received, but first administrator needs to validate. Thank you for being patient!');
                break;

            case Model_Ad::POST_DIRECTLY:
            default:

                    $ad->status = Model_Ad::STATUS_PUBLISHED;
                    $ad->published = $ad->created;

                    $url_cont = $user->ql('contact');
                    $url_ad = $user->ql('ad', array('category'=>$ad->category->seoname,
                                                    'seotitle'=>$ad->seotitle));

                    $user->email('ads-user-check',array('[URL.CONTACT]'  =>$url_cont,
                                                                '[URL.AD]'      =>$url_ad,
                                                                '[AD.NAME]'     =>$ad->title,
                                                                ));

                    Model_Subscription::new_ad($ad->user);
                    Model_Subscribe::notify($ad);

                    $return_message = __('Advertisement is posted. Congratulations!');
                break;
        }

        //save the last changes on status
        $ad->save();

        //notify admins new ad
        $ad->notify_admins();


        return array('message'=>$return_message,'checkout_url'=>$checkout_url,'ad'=>$ad);
    }


    /**
     * notify admins of new ad
     * @return void
     */
    public function notify_admins()
    {
        //NOTIFY ADMIN
        // new ad notification email to admin (notify_email), if set to TRUE
        if(core::config('email.new_ad_notify') == TRUE)
        {
            $url_ad = Route::url('ad', array('category'=>$this->category->seoname,'seotitle'=>$this->seotitle));

            $replace = array('[URL.AD]'        =>$url_ad,
                             '[AD.TITLE]'      =>$this->title,
                             '[USER.OWNER]'    =>$this->user->name,
                         );

            Email::content(Email::get_notification_emails(),
                                core::config('general.site_name'),
                                core::config('email.notify_email'),
                                core::config('general.site_name'),
                                'ads-to-admin',
                                $replace);
        }
    }

    /**
     * Set values from an array with support for one-one relationships.  This method should be used
     * for loading in post data, etc.
     *
     * @param  array $values   Array of column => val
     * @param  array $expected Array of keys to take from $values
     * @return ORM
     */
    public function values(array $values, array $expected = NULL)
    {
        //some work on the data ;)
        if (isset($values['title']))
            $values['title']          = Text::banned_words($values['title']);
        if (isset($values['description']))
            $values['description']    = Text::banned_words($values['description']);
        if (isset($values['price']))
            $values['price']          = floatval(str_replace(',', '.', $values['price']));   //TODO this is ugly as hell!


        // append to $values new custom values
        foreach ($values as $name => $field)
        {
            // get by prefix
            if (strpos($name,'cf_') !== false)
            {
                //checkbox when selected return string 'on' as a value
                if($field == 'on')
                    $values[$name] = 1;
                if($field == '0000-00-00' OR $field == "" OR $field == NULL OR empty($field))
                    $values[$name] = NULL;
            }
        }

        return parent::values($values, $expected);
    }

    /**
     * changes the status of an ad to deactivated
     * @return bool
     */
    public function deactivate()
    {
        if ($this->loaded() AND $this->status != Model_Ad::STATUS_UNAVAILABLE)
        {
            try
            {
                $this->status = Model_Ad::STATUS_UNAVAILABLE;
                $this->save();

                return TRUE;
            }
            catch (Exception $e)
            {
                throw HTTP_Exception::factory(500,$e->getMessage());
            }
        }

        return FALSE;

    }

    /**
     * changes the status of an ad to deactivated and set stock = 0
     * @return bool
     */
    public function sold()
    {
        if ($this->loaded() AND $this->status != Model_Ad::STATUS_SOLD)
        {
            try
            {
                $this->status = Model_Ad::STATUS_SOLD;
                $this->stock = 0;
                $this->save();

                return TRUE;
            }
            catch (Exception $e)
            {
                throw HTTP_Exception::factory(500,$e->getMessage());
            }
        }

        return FALSE;

    }

    /**
     * returns the paypal account of the ad, used in controller paypal
     * @return string email
     */
    public function paypal_account()
    {
        if ($this->loaded())
        {
            //1st if paypal custom field set on the ad
            if (isset($this->cf_paypalaccount) AND Valid::email($this->cf_paypalaccount))
                return $this->cf_paypalaccount;
            //2nd paypal custom field from user
            elseif(isset($this->user->cf_paypalaccount) AND Valid::email($this->user->cf_paypalaccount))
                return $this->user->cf_paypalaccount;
            //3rd and default use the email of the user
            else
                return $this->user->email;
        }

        return NULL;
    }


    public static function count_all_ads()
    {
        $ads = new Model_Ad();
        return $ads->count_all();
    }

    /**
     * returns the shipping price of the ad
     * @return string shipping price
     */
    public function shipping_price()
    {
        if ($this->loaded())
        {
            if(isset($this->cf_shipping) AND Valid::price($this->cf_shipping) AND $this->cf_shipping > 0)
                return $this->cf_shipping;
        }

        return NULL;
    }

    /**
     * returns the shipping price of the ad
     * @return string shipping price
     */
    public function shipping_pickup()
    {
        if ($this->loaded())
        {
            if(isset($this->cf_shipping_pickup) AND $this->cf_shipping_pickup > 0)
                return TRUE;
        }

        return FALSE;
    }

    /**
     * returns the currency of the ad (GBP, USD, EUR)
     * @return string
     */
    public function currency()
    {
        if ($this->loaded())
        {
            if(isset($this->cf_currency) AND $this->cf_currency != '')
                return $this->cf_currency;
        }

        if(core::config('general.number_format') == '%n')
            return core::config('payment.paypal_currency');
        else
            return core::config('general.number_format');
    }

    /**
     * return btc address and QR code view
     */
    public function btc()
    {
        if($this->loaded() AND $this->status == self::STATUS_PUBLISHED AND
            ((isset($this->cf_bitcoinaddress) AND !empty($this->cf_bitcoinaddress)) OR
            (isset($this->user->cf_bitcoinaddress) AND !empty($this->user->cf_bitcoinaddress))))
        {
            return View::factory('pages/ad/btc',array('ad'=>$this))->render();
        }
    }

} // END Model_ad
