<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Widget Seller Informatiom
 *
 * @author      Constantinos <constantinos@open-classifieds.com>
 * @package     Widget
 * @copyright   (c) 2009-2017 Open Classifieds Team
 * @license     GPL v3
 */


class Widget_Seller extends Widget
{

	public function __construct()
	{	

		$this->title 		= __('Seller information');
		$this->description 	= __('Display seller information in the ad page');

        $this->fields = array(	'text_title'    => array( 'type'      => 'text',
                                                        'display'   => 'text',
                                                        'default'   => __('Seller information'),
                                                        'label'     => __('Title displayed'),
                                                        'required'  => FALSE),

                                'contact'      => array(  'type'      => 'text',
                                                        'display'   => 'select',
                                                        'label'     => __('Show contact form'),
                                                        'options'   => array('0'   => __('FALSE'),
                                                                             '1'   => __('TRUE'),
                                                                            ), 
                                                        'default'   => 0,
                                                        'required'  => TRUE),

                                'description'      => array(  'type'      => 'text',
                                                        'display'   => 'select',
                                                        'label'     => __('Show seller description'),
                                                        'options'   => array('0'   => __('FALSE'),
                                                                             '1'   => __('TRUE'),
                                                                            ), 
                                                        'default'   => 0,
                                                        'required'  => TRUE),

                                'last_login'      => array(  'type'      => 'text',
                                                        'display'   => 'select',
                                                        'label'     => __('Show the last login date'),
                                                        'options'   => array('0'   => __('FALSE'),
                                                                             '1'   => __('TRUE'),
                                                                            ), 
                                                        'default'   => 0,
                                                        'required'  => TRUE),

                                'location'      => array(  'type'      => 'text',
                                                        'display'   => 'select',
                                                        'label'     => __('Show seller location'),
                                                        'options'   => array('0'   => __('Hidden'),
                                                                             '1'   => __('Location on Map'),
                                                                             '2'   => __('Address'),
                                                                            ), 
                                                        'default'   => 0,
                                                        'required'  => TRUE),

                                'custom_fields'      => array(  'type'      => 'text',
                                                        'display'   => 'select',
                                                        'label'     => __('Show seller custom fields'),
                                                        'options'   => array('0'   => __('FALSE'),
                                                                             '1'   => __('TRUE'),
                                                                            ), 
                                                        'default'   => 0,
                                                        'required'  => TRUE),
						 		);
	}

	/**
     * get the title for the widget
     * @param string $title we will use it for the loaded widgets
     * @return string 
     */
    public function title($title = NULL)
    {
        return parent::title($this->text_title);
    }

    /**
     * Automatically executed before the widget action. Can be used to set
     * class properties, do authorization checks, and execute other custom code.
     *
     * @return  void
     */
    public function before()
    {
        $ad = new Model_Ad();
        $ad->where('seotitle','=',Request::current()->param('seotitle'))
           ->limit(1)
           ->find();

        if($ad->loaded())
        {
            $this->ad = $ad;

            $user = new Model_User();
            $user->where('id_user','=',$ad->id_user)
                ->limit(1)
                ->find();

            $this->user = $user;

            $ads = new Model_Ad();
            $ads->where('id_user', '=', $user->id_user)
                ->where('status', '=', Model_Ad::STATUS_PUBLISHED);
            $this->user_ads = $ads->count_all();
        }

    }

    /**
     * renders the widget view with the data
     * @return string HTML 
     */     
    public function render()
    {
        //only in view ad single
        if ($this->loaded AND strtolower(Request::current()->controller())=='ad' AND Request::current()->action()=='view' )
        {
            $this->before();

            //get the view file (check if exists in the theme if not default), and inject the widget
            $out = View::factory('widget/'.strtolower(get_class($this)),array('widget' => $this));

            $this->after();

            return $out;
        }
        
        return FALSE;
    }


}