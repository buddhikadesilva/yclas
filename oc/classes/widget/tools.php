<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Tools ads widget reader
 *
 * @author      Slobodan <slobodan@open-classifieds.com>
 * @package     Widget
 * @copyright   (c) 2009-2013 Open Classifieds Team
 * @license     GPL v3
 */


class Widget_Tools extends Widget
{

	public function __construct()
	{	

		$this->title = __('Tools');
		$this->description = __('Admin Tools to editing ads');

		$this->fields = array('tools_title'  => array(  'type'      => 'text',
                                                        'display'   => 'text',
                                                        'label'     => __('Tools title displayed'),
                                                        'default'   => __('Ad Tools'),
                                                        'required'  => FALSE),
        );
	}


    /**
     * get the title for the widget
     * @param string $title we will use it for the loaded widgets
     * @return string 
     */
    public function title($title = NULL)
    {
        return parent::title($this->tools_title);
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
        $user_ads = clone $ad;

        //get current ad do not filter by user since admin also can see
        $ad->where('seotitle','=',Request::current()->param('seotitle'))
           ->limit(1)
           ->find();

        if($ad->loaded() AND Auth::instance()->logged_in())  
        {
            $user = Auth::instance()->get_user();

            if($user->is_admin() OR $user->id_user == $ad->id_user)
            {
                $this->ad = $ad;
                $this->user_ads = $user_ads->where('id_user', '=', $ad->id_user)->find_all();
            }   
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