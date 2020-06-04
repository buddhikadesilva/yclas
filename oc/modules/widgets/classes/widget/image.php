<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Share widget
 *
 * @author      Chema <chema@open-classifieds.com>
 * @package     Widget
 * @copyright   (c) 2009-2013 Open Classifieds Team
 * @license     GPL v3
 */


class Widget_Image extends Widget
{

	public function __construct()
	{	

		$this->title = __('Image');
		$this->description = __('Add an image/banner to your website.');

		$this->fields = array(	
								'text_title'  => array(	'type'		=> 'text',
						 		  						'display'	=> 'text',
                                                        'default'   => '',
						 		  						'label'		=> __('Title displayed'),
														'required'	=> FALSE),

                                'image_url' => array( 'type'     => 'text',
                                                        'display'   => 'text',
                                                        'default'   => '',
                                                        'label'     => __('Enter the image URL'),
                                                        'required'  => FALSE),

                                'redirect_url' => array( 'type'     => 'text',
                                                        'display'   => 'text',
                                                        'default'   => '',
                                                        'label'     => __('URL to redirect when clicked (Optional)'),
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
        return parent::title($this->text_title);
    }

}