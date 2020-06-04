<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Ads widget reader
 *
 * @author      Chema <chema@open-classifieds.com>
 * @package     Widget
 * @copyright   (c) 2009-2013 Open Classifieds Team
 * @license     GPL v3
 */


class Widget_Ads extends Widget
{

	public function __construct()
	{

		$this->title 		= __('Ads');
		$this->description 	= __('Ads reader');

		$this->fields = array(	'ads_type' => array(    'type'      => 'text',
                                                        'display'   => 'select',
                                                        'label'     => __('Type ads to display'),
                                                        'options'   => array('latest'    => __('Latest Ads'),
                                                                             'popular'   => __('Popular Ads last month'),
                                                                             'featured'  => __('Featured Ads'),
                                                                            ),
                                                        'default'   => 5,
                                                        'required'  => TRUE),

                                'ads_limit' => array( 	'type'		=> 'numeric',
														'display'	=> 'select',
														'label'		=> __('Number of ads to display'),
														'options'   => array_combine(range(1,50),range(1,50)),
														'default'	=> 5,
														'required'	=> TRUE),

						 		'ads_title'  => array(	'type'		=> 'text',
						 		  						'display'	=> 'text',
						 		  						'label'		=> __('Ads title displayed'),
						 		  						'default'   => __('Latest Ads'),
														'required'	=> FALSE),
						 		);
	}

	/**
     * get the title for the widget
     * @param string $title we will use it for the loaded widgets
     * @return string
     */
    public function title($title = NULL)
    {
        return parent::title($this->ads_title);
    }

	/**
	 * Automatically executed before the widget action. Can be used to set
	 * class properties, do authorization checks, and execute other custom code.
	 *
	 * @return  void
	 */
	public function before()
	{
        $ads = new Model_Ad();
        $ads->where('status','=', Model_Ad::STATUS_PUBLISHED);
        //if ad have passed expiration time dont show
        if((New Model_Field())->get('expiresat'))
        {
            $ads->where_open()
            ->or_where(DB::expr('DATE(cf_expiresat)'), '>', Date::unix2mysql())
            ->or_where('cf_expiresat','IS',NULL)
            ->where_close();
        }
        elseif(core::config('advertisement.expire_date') > 0)
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

        switch ($this->ads_type)
        {
            case 'popular':
                $id_ads = array_keys(Model_Visit::popular_ads());
                if (core::count($id_ads)>0)
                    $ads->where('id_ad','IN', $id_ads);

                break;
            case 'featured':
                $ads->where('featured','IS NOT', NULL)
                ->where('featured','>', Date::unix2mysql())
                ->order_by('featured','desc');
                break;
            case 'latest':
            default:
                $ads->order_by('published','desc');
                break;
        }

        $ads = $ads->limit($this->ads_limit)->cached()->find_all();
        //die(print_r($ads));
		$this->ads = $ads;
	}


    /**
     * renders the widget view with the data
     * @return string HTML
     */
    public function render()
    {
        $this->before();

        //only render if theres ads
        if (core::count($this->ads)>0)
        {
            //get the view file (check if exists in the theme if not default), and inject the widget
            $out = View::factory('widget/'.strtolower(get_class($this)),array('widget' => $this));

            $this->after();

            return $out;
        }

        return FALSE;
    }


}
