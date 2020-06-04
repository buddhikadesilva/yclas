<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Locations widget reader
 *
 * @author      Chema <chema@open-classifieds.com>
 * @package     Widget
 * @copyright   (c) 2009-2013 Open Classifieds Team
 * @license     GPL v3
 */


class Widget_Locations extends Widget
{

	public function __construct()
	{

		$this->title = __('Locations');
		$this->description = __('Display Locations');

		$this->fields = array(
						 		'locations_title'  => array(	'type'		=> 'text',
						 		  						'display'	=> 'text',
						 		  						'label'		=> __('Locations title displayed'),
						 		  						'default'   => __('Locations'),
														'required'	=> FALSE),
                                'locations' => array(  'type'      => 'text',
                                                        'display'   => 'select',
                                                        'label'     => __('Locations'),
                                                        'options'   => array('0'    => __('FALSE'),
                                                                             'popular'   => __('TRUE'),
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
        return parent::title($this->locations_title);
    }

	/**
	 * Automatically executed before the widget action. Can be used to set
	 * class properties, do authorization checks, and execute other custom code.
	 *
	 * @return  void
	 */
	public function before()
	{
		$loc = new Model_Location();

		// loaded locations
        if (Model_Location::current()->loaded())
        {
    	    $location = Model_Location::current()->id_location; // id_location

    	    //list of children of current location
            // if list_loc dosent have siblings take brothers //
    	    $list_loc = $loc->where('id_location_parent','=',$location)->order_by('order','asc')->cached()->find_all();
    	    if(core::count($list_loc) == 0)
            {
                $list_loc = $loc->where('id_location_parent','=',Model_Location::current()->id_location_parent)->order_by('order','asc')->cached()->find_all();
            }

            //parent of current location
    	   	$loc_parent_deep = $loc->where('id_location','=',Model_Location::current()->id_location_parent)->limit(1)->find();

            // array with name and seoname of a location and his parent. Is to build breadcrumb in widget
    	   	$current_and_parent = array('name'			=> Model_Location::current()->name,
    	    					        'translate_name'=> Model_Location::current()->translate_name(),
    	    					        'id'			=> Model_Location::current()->id_location,
    	    					        'seoname'		=> Model_Location::current()->seoname,
    	    					        'parent_name'	=> $loc_parent_deep->name,
    	    					        'parent_translate_name'	=> $loc_parent_deep->translate_name(),
    	    					        'id_parent'     => $loc_parent_deep->id_location_parent,
    	    					        'parent_seoname'=> $loc_parent_deep->seoname);
       	}
        else
        {
			$list_loc = $loc->where('id_location_parent','=',1)->order_by('order','asc')->cached()->find_all();
			$current_and_parent = NULL;
        }
        $this->locations = $this->locations;
		$this->loc_items = $list_loc;
		$this->loc_breadcrumb = $current_and_parent;
        $this->cat_seoname = URL::title(__('all'));

        if (Model_Category::current()->loaded())
            $this->cat_seoname = Model_Category::current()->seoname;

	}


}