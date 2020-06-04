<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Home extends Controller {

	public function action_index()
	{

        if (core::config('general.auto_locate'))
        {
            if ($user_location = Core::post('user_location'))
            {
                Cookie::set('user_location', $user_location);

                $this->auto_render = FALSE;
                $this->template = View::factory('js');
                $this->template->content = TRUE;

                return;
            }
            elseif (Core::get('user_location') == '0')
            {
                Cookie::delete('user_location');
            }

            Theme::$scripts['async_defer'][] = '//maps.google.com/maps/api/js?libraries=geometry&v=3&key='.core::config("advertisement.gm_api_key").'&callback=initAutoLocate&language='.i18n::get_gmaps_language(i18n::$locale);
        }

        if (core::config('general.add_to_home_screen'))
        {
            Theme::$scripts['footer'][] = 'js/add-to-home-screen.js';
        }

	    //template header
	    $this->template->title            = '';
	    // $this->template->meta_keywords    = 'keywords';
	    if(core::config('general.site_description') != '')
			$this->template->meta_description = core::config('general.site_description');
	    else
			$this->template->meta_description = core::config('general.site_name').' '.__('official homepage, get your post listed now.');

	    //setting main view/template and render pages

        // get user location if any
        $user_location = NULL;
        if (is_numeric($user_id_location = Cookie::get('user_location')))
        {
            $user_location = new Model_Location($user_id_location);

            if ( ! $user_location->loaded())
                $user_location = NULL;
        }

	    // swith to decide on ads_in_home
	    $ads = new Model_Ad();
        $ads->where('status','=', Model_Ad::STATUS_PUBLISHED);

        // filter by language
        if (Core::config('general.multilingual') == 1)
        {
            $ads->where('locale', '=', i18n::$locale);
        }

        if ($user_location)
            $ads->where('id_location', 'in', $user_location->get_siblings_ids());

        $ads_in_home = core::config('advertisement.ads_in_home');

        //in case we do not count visits we cant show popular
        if(core::config('advertisement.count_visits')==0 AND $ads_in_home==2)
            $ads_in_home = 0;

        switch ($ads_in_home)
        {
            case 2:
                $id_ads = array_keys(Model_Visit::popular_ads());
                if (core::count($id_ads)>0)
                    $ads->where('id_ad','IN', $id_ads);

                break;
            case 1:
                $ads->where('featured','IS NOT', NULL)
                ->where('featured', '>=', Date::unix2mysql())
                ->order_by('featured','desc');
                break;
            case 4:
                $ads->where('featured','IS NOT', NULL)
                ->where('featured', '>=', Date::unix2mysql())
                ->order_by(DB::expr('RAND()'));
                break;
            case 0:
            default:
                $ads->order_by('published','desc');
                break;
        }

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

        $ads = $ads->limit(Theme::get('num_home_latest_ads', 4))->cached()->find_all();

		$categs = Model_Category::get_category_count(TRUE, $user_location);

        $hide_categories = json_decode(Core::config('general.hide_homepage_categories'), TRUE);

        $auto_locats = NULL;
        $auto_location_distance = Core::config('general.measurement') == 'imperial' ? (Num::round(Core::config('advertisement.auto_locate_distance') * 1.60934)) : Core::config('advertisement.auto_locate_distance');
        if(core::config('general.auto_locate') AND !isset($_COOKIE['cancel_auto_locate']) AND Model_User::get_userlatlng()) {
                $auto_locats = new Model_Location();
                $auto_locats = $auto_locats ->select(array(DB::expr('degrees(acos(sin(radians('.$_COOKIE['mylat'].')) * sin(radians(`latitude`)) + cos(radians('.$_COOKIE['mylat'].')) * cos(radians(`latitude`)) * cos(radians(abs('.$_COOKIE['mylng'].' - `longitude`))))) * 111.321'), 'distance'))
                                            ->where('latitude','IS NOT',NULL)
                                            ->where('longitude','IS NOT',NULL)
                                            ->having('distance','<=',$auto_location_distance)
                                            ->order_by('distance','asc')
                                            ->find_all()
                                            ->as_array();
        }

        $this->template->bind('content', $content);

        $this->template->content = View::factory('pages/home', compact('ads', 'categs', 'auto_locats', 'user_location', 'hide_categories'));

	}

} // End Welcome
