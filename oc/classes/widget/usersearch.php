<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * userSearch widget reader
 *
 * @author      Slobodan <slobodan@open-classifieds.com>
 * @package     Widget
 * @copyright   (c) 2009-2013 Open Classifieds Team
 * @license     GPL v3
 */


class Widget_UserSearch extends Widget
{

	public function __construct()
	{	

		$this->title 		= __('User Search');
		$this->description 	= __('Advanced User Search');

        $this->fields = array(	'text_title'    => array( 'type'      => 'text',
                                                        'display'   => 'text',
                                                        'default'   => __('Search'),
                                                        'label'     => __('Title displayed'),
                                                        'required'  => FALSE),

                                'custom'      => array(  'type'      => 'text',
                                                        'display'   => 'select',
                                                        'label'     => __('Custom fields in search'),
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
        if($this->custom != FALSE)
        {
            $fields = Model_UserField::get_all();
            $this->custom_fields = $fields;
        }
    }


}