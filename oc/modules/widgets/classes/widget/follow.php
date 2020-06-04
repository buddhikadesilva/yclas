<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Share widget
 *
 * @author      Chema <chema@open-classifieds.com>
 * @package     Widget
 * @copyright   (c) 2009-2013 Open Classifieds Team
 * @license     GPL v3
 */


class Widget_Follow extends Widget
{

	public function __construct()
	{	

		$this->title = __('Follow');
        $this->description = __('Add social media follow buttons.');

		$this->fields = array(	
								'text_title'  => array(	'type'		=> 'text',
						 		  						'display'	=> 'text',
                                                        'default'   => __('Follow us'),
						 		  						'label'		=> __('Title displayed'),
														'required'	=> FALSE),

                                'facebook' => array( 'type'     => 'text',
                                                        'display'   => 'text',
                                                        'label'     => __('Facebook URL'),
                                                        'required'  => FALSE),
                                'twitter' => array( 'type'     => 'text',
                                                        'display'   => 'text',
                                                        'label'     => __('Twitter URL'),
                                                        'required'  => FALSE),
                                'instagram' => array( 'type'     => 'text',
                                                        'display'   => 'text',
                                                        'label'     => __('Instagram URL'),
                                                        'required'  => FALSE),
                                'pinterest' => array( 'type'     => 'text',
                                                        'display'   => 'text',
                                                        'label'     => __('Pinterest URL'),
                                                        'required'  => FALSE),
                                'googleplus' => array( 'type'     => 'text',
                                                        'display'   => 'text',
                                                        'label'     => __('Google+ URL'),
                                                        'required'  => FALSE),
                                'linkedin' => array( 'type'     => 'text',
                                                        'display'   => 'text',
                                                        'label'     => __('Linkedin URL'),
                                                        'required'  => FALSE),
                                'youtube' => array( 'type'     => 'text',
                                                        'display'   => 'text',
                                                        'label'     => __('Youtube URL'),
                                                        'required'  => FALSE),
                                'flickr' => array( 'type'     => 'text',
                                                        'display'   => 'text',
                                                        'label'     => __('Flickr URL'),
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