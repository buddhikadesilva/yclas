<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Languages widget selector
 *
 * @author      Chema <chema@open-classifieds.com>
 * @package     Widget
 * @copyright   (c) 2009-2013 Open Classifieds Team
 * @license     GPL v3
 */


class Widget_Currency extends Widget
{

	public function __construct()
	{	

		$this->title = __('Currency Converter');
		$this->description = __('Choose currency. <a target="_blank" href="https://docs.yclas.com/currency-converter-widget/"><i class="fa fa-question-circle"></i></a>');

		$this->fields = array(	
						 		'currency_title'  => array(	'type'		=> 'text',
						 		  						'display'	=> 'text',
						 		  						'label'		=> __('Currency name displayed'),
						 		  						'default'   => __('Currencies'),
														'required'	=> FALSE),
                                'apikey' => array(      'type'      => 'text',
                                                        'display'   => 'text',
                                                        'label'     => __('Enter your API key.'),
                                                        'required'  => FALSE),
                                'default' => array(  	'type'      => 'text',
                                                        'display'   => 'text',
                                                        'label'     => __('Choose the default currency (e.g. USD)'),
                                                        'required'  => FALSE),
                                'currencies' => array(  'type'      => 'text',
                                                        'display'   => 'text',
                                                        'label'     => __('Currencies to display coma separated'),
                                                        'required'  => FALSE),
						 		);
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


    /**
     * get the title for the widget
     * @param string $title we will use it for the loaded widgets
     * @return string 
     */
    public function title($title = NULL)
    {
        return parent::title($this->currency_title);
    }
	

}