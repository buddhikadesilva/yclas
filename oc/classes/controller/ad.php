<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ad extends Controller {


	/**
	 * Publis all adver.-s without filter
	 */
	public function action_listing()
	{
		if(Theme::get('infinite_scroll'))
		{
			$this->template->scripts['footer'][] = '//cdn.jsdelivr.net/jquery.infinitescroll/2.1/jquery.infinitescroll.js';
			$this->template->scripts['footer'][] = 'js/listing.js';
		}
		if(core::config('general.auto_locate') OR core::config('advertisement.map'))
		{
            Theme::$scripts['async_defer'][] = '//maps.google.com/maps/api/js?libraries=geometry,places&v=3&key='.core::config("advertisement.gm_api_key").'&callback=initLocationsGMap&language='.i18n::get_gmaps_language(i18n::$locale);
		}
        $this->template->scripts['footer'][] = 'js/jquery.toolbar.js';
		$this->template->scripts['footer'][] = 'js/sort.js';
		Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url(Route::url('default')));


        /**
         * we get the model of category and location from controller to filter and generate urls titles etc...
         */

        $location = NULL;
        $location_parent = NULL;
        $location_name = NULL;

        if (Model_Location::current()->loaded())
        {
        	$location = Model_Location::current();
            if($location->id_location != 1)
            $location_name = $location->translate_name();

            //adding the location parent
            if ($location->id_location_parent!=1 AND $location->parent->loaded())
                $location_parent = $location->parent;
        }


        $category = NULL;
        $category_parent = NULL;
        $category_name = NULL;

        if (Model_Category::current()->loaded())
        {
            $category = Model_Category::current();
            if($category->id_category != 1)
                $category_name = $category->translate_name();
            //adding the category parent
            if ($category->id_category_parent!=1 AND $category->parent->loaded())
                $category_parent = $category->parent;
        }

        //base title
        if ($category!==NULL)
        {
            //category image
            if(( $icon_src = $category->get_icon() )!==FALSE )
                Controller::$image = $icon_src;

            $this->template->title = $category_name;

            if ($category->translate_description() != '')
				$this->template->meta_description = $category->translate_description();
            else
				$this->template->meta_description = __('All').' '.$category_name.' '.__('in').' '.core::config('general.site_name');
		}
        else
        {
			$this->template->title = __('all');
			if ($location!==NULL)
				if ($location->translate_description() != '')
					$this->template->meta_description = $location->translate_description();
				else
					$this->template->meta_description = __('List of all postings in').' '.$location_name;
			else
				$this->template->meta_description = __('List of all postings in').' '.core::config('general.site_name');
        }

        //adding location titles and breadcrumbs
        if ($location!==NULL)
        {
            //in case we dont have the category image we use the location
            if(( $icon_src = $location->get_icon() )!==FALSE AND Controller::$image===NULL)
                Controller::$image = $icon_src;

            $this->template->title .= ' - '.$location->translate_name();

            if ($location_parent!==NULL)
            {
                $this->template->title .=' ('.$location_parent->translate_name() .')';
                Breadcrumbs::add(Breadcrumb::factory()->set_title($location_parent->translate_name())->set_url(Route::url('list', array('location'=>$location_parent->seoname))));
            }

            Breadcrumbs::add(Breadcrumb::factory()->set_title($location->translate_name())->set_url(Route::url('list', array('location'=>$location->seoname))));

            if ($category_parent!==NULL)
                Breadcrumbs::add(Breadcrumb::factory()->set_title($category_parent->translate_name())
                    ->set_url(Route::url('list', array('category'=>$category_parent->seoname,'location'=>$location->seoname))));

            if ($category!==NULL)
                Breadcrumbs::add(Breadcrumb::factory()->set_title($category->translate_name())
                    ->set_url(Route::url('list', array('category'=>$category->seoname,'location'=>$location->seoname))));
        }
        else
        {
            if ($category_parent!==NULL)
            {
                $this->template->title .=' ('.$category_parent->translate_name() .')';
                Breadcrumbs::add(Breadcrumb::factory()->set_title($category_parent->translate_name())
                    ->set_url(Route::url('list', array('category'=>$category_parent->seoname))));
            }

            if ($category!==NULL)
                Breadcrumbs::add(Breadcrumb::factory()->set_title($category->translate_name())
                    ->set_url(Route::url('list', array('category'=>$category->seoname))));
        }


        $data = $this->list_logic($category, $location);

        //if home page is the listing
        if ( ($landing = json_decode(core::config('general.landing_page'))) != NULL
            AND $landing->controller == 'ad'
            AND $landing->action == 'listing'
            AND (isset($data['pagination']) AND $data['pagination']->current_page == 1)
            AND $location === NULL AND $category === NULL )
        {
            //only show site title
            $this->template->title = NULL;

            // if we have site description lets use that ;)
            if (core::config('general.site_description') != '')
                $this->template->meta_description = core::config('general.site_description');
        }

		$this->template->bind('content', $content);
		$this->template->content = View::factory('pages/ad/listing',$data);
 	}

    /**
     * gets data to the view and filters the ads
     * @param  Model_Category $category
     * @param  Model_Location $location
     * @return array
     */
	public function list_logic($category = NULL, $location = NULL)
	{

		//user recognition
		$user = (Auth::instance()->get_user() == NULL) ? NULL : Auth::instance()->get_user();

		$ads = new Model_Ad();

		//filter by category or location
        if ($category!==NULL)
        {
            $ads->where('id_category', 'in', $category->get_siblings_ids());
        }

        if ($location!==NULL)
        {
            $ads->where('id_location', 'in', $location->get_siblings_ids());
        }

		//only published ads
        $ads->where('status', '=', Model_Ad::STATUS_PUBLISHED);

        // filter by language
        if (Core::config('general.multilingual') == 1)
        {
            $ads->where('locale', '=', i18n::$locale);
        }

        //if ad have passed expiration time dont show
        if((New Model_Field())->get('expiresat'))
        {
            $ads->where_open()
            ->or_where(DB::expr('cf_expiresat'), '>', Date::unix2mysql())
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

            //if sort by event date
            if (core::request('sort',core::config('advertisement.sort_by')) == 'event-date')
            {
                $ads->where('cf_eventdate','IS NOT',NULL);
            }
        }

        //if sort by distance
        if ((core::request('sort',core::config('advertisement.sort_by')) == 'distance' OR core::request('userpos') == 1) AND Model_User::get_userlatlng())
        {
            $ads->select(array(DB::expr('degrees(acos(sin(radians('.$_COOKIE['mylat'].')) * sin(radians(`latitude`)) + cos(radians('.$_COOKIE['mylat'].')) * cos(radians(`latitude`)) * cos(radians(abs('.$_COOKIE['mylng'].' - `longitude`))))) * 111.321'), 'distance'))
            ->where('latitude','IS NOT',NULL)
            ->where('longitude','IS NOT',NULL);
        }

        if (core::request('userpos') == 1 AND Model_User::get_userlatlng())
        {
            if (is_numeric(Core::cookie('mydistance')) AND Core::cookie('mydistance') <= 500)
                $location_distance = Core::config('general.measurement') == 'imperial' ? (Num::round(Core::cookie('mydistance') * 1.60934)) : Core::cookie('mydistance');
            else
                $location_distance = Core::config('general.measurement') == 'imperial' ? (Num::round(Core::config('advertisement.auto_locate_distance') * 1.60934)) : Core::config('advertisement.auto_locate_distance');
            $ads->where(DB::expr('degrees(acos(sin(radians('.$_COOKIE['mylat'].')) * sin(radians(`latitude`)) + cos(radians('.$_COOKIE['mylat'].')) * cos(radians(`latitude`)) * cos(radians(abs('.$_COOKIE['mylng'].' - `longitude`))))) * 111.321'),'<=',$location_distance);
        }

        // featured ads
        $featured = NULL;
        if(Theme::get('listing_slider') == 2)
        {
                $featured = clone $ads;
                $featured = $featured->where('featured', '>=', Date::unix2mysql())
                                ->order_by('featured','desc')
                                ->limit(Theme::get('num_home_latest_ads', 4))
                                ->find_all();
        }
        //random featured
        elseif(Theme::get('listing_slider') == 3)
        {
                $featured = clone $ads;
                $featured = $featured->where('featured', '>=', Date::unix2mysql())
                                ->order_by(DB::expr('RAND()'))
                                ->limit(Theme::get('num_home_latest_ads', 4))
                                ->cached()->find_all();
        }

        $res_count = clone $ads;
		$res_count = $res_count->count_all();

		// check if there are some advet.-s
		if ($res_count > 0)
		{

       		// pagination module
       		$pagination = Pagination::factory(array(
                    'view'           	=> 'pagination',
                    'total_items'    	=> $res_count,
                    'items_per_page'    => core::request('items_per_page',core::config('advertisement.advertisements_per_page')),
     	    ))->route(Route::get('list'))
              ->route_params(array(
                    'category' 			=> ($category!==NULL)?$category->seoname:URL::title(__('all')),
                    'location'			=> ($location!==NULL)?$location->seoname:NULL,
    	    ));

     	    Breadcrumbs::add(Breadcrumb::factory()->set_title(__("Page ").$pagination->current_page));

            /**
             * order depending on the sort parameter
             */
            switch (core::request('sort',core::config('advertisement.sort_by')))
            {
                //title z->a
                case 'title-asc':
                    $ads->order_by('title','asc')->order_by('published','desc');
                    break;
                //title a->z
                case 'title-desc':
                    $ads->order_by('title','desc')->order_by('published','desc');
                    break;
                //cheaper first
                case 'price-asc':
                    $ads->order_by('price','asc')->order_by('published','desc');
                    break;
                //expensive first
                case 'price-desc':
                    $ads->order_by('price','desc')->order_by('published','desc');
                    break;
                //featured
                case 'featured':
                    $ads->order_by('featured','desc')->order_by('published','desc');
                    break;
                //rating
                case 'rating':
                    $ads->order_by('rate','desc')->order_by('published','desc');
                    break;
                //favorited
                case 'favorited':
                    $ads->order_by('favorited','desc')->order_by('published','desc');
                    break;
                //distance
                case 'distance':
                    if (Model_User::get_userlatlng() AND core::config('general.auto_locate'))
                    $ads->order_by('distance','asc')->order_by('published','asc');
                    break;
                //oldest first
                case 'published-asc':
                    $ads->order_by('published','asc');
                    break;
                //event date
                case 'event-date':
                    if((New Model_Field())->get('eventdate'))
                    {
                        $ads->order_by('cf_eventdate','asc');
                    }
                    break;
                //newest first
                case 'published-desc':
                default:
                    $ads->order_by('published','desc');
                    break;
            }


     	    //we sort all ads with few parameters
       		$ads = $ads ->limit($pagination->items_per_page)
        	            ->offset($pagination->offset)
        	            ->find_all();
		}
		else
		{
			// array of categories sorted for view
			return array('ads'			=> NULL,
						 'pagination'	=> NULL,
						  'user'        => $user,
                         'category'     => $category,
                         'location'     => $location,
                         'featured'		=> NULL);
		}

		// array of categories sorted for view
		return array('ads'			=> $ads,
					 'pagination'	=> $pagination,
					 'user'			=> $user,
					 'category'		=> $category,
					 'location'		=> $location,
					 'featured'		=> $featured);
	}

	/**
	 *
	 * Display single advert.
	 * @throws HTTP_Exception_404
	 *
	 */
	public function action_view()
	{
        if ((Core::config('advertisement.login_to_view_ad')) AND ! Auth::instance()->logged_in())
        {
            Alert::set(Alert::INFO, __('Please, login before to continue.'));
            HTTP::redirect(Route::url('oc-panel', ['controller' => 'auth', 'action' => 'login']).'?auth_redirect='.URL::current());
        }

		$seotitle = $this->request->param('seotitle',NULL);

		if ($seotitle!==NULL)
		{
			$ad = new Model_Ad();
			$ad->where('seotitle','=', $seotitle)
                ->where('status','!=',Model_Ad::STATUS_SPAM)
				->limit(1)->cached()->find();

			if ($ad->loaded())
			{
                //throw 404
                if (in_array($ad->status, [Model_Ad::STATUS_UNAVAILABLE, Model_Ad::STATUS_NOPUBLISHED]))
                    throw HTTP_Exception::factory(404,__('This advertisement doesn´t exist, or is not yet published!'));

                Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url(Route::url('default')));

                $location = NULL;
                $location_parent = NULL;
                if ($ad->location->loaded() AND $ad->id_location!=1)
                {
                    $location = $ad->location;
                    //adding the location parent
                    if ($location->id_location_parent!=1 AND $location->parent->loaded())
                        $location_parent = $location->parent;
                }


                $category = NULL;
                $category_parent = NULL;
                if ($ad->category->loaded())
                {
                    $category = $ad->category;
                    //adding the category parent
                    if ($category->id_category_parent!=1 AND $category->parent->loaded())
                        $category_parent = $category->parent;

                }

                //base category  title
                if ($category!==NULL)
                    $this->template->title = $category->translate_name();
                else
                    $this->template->title = '';

                //adding location titles and breadcrumbs
                if ($location!==NULL)
                {
                    $this->template->title .= ' - '.$location->translate_name();

                    if ($location_parent!==NULL)
                    {
                        $this->template->title .=' ('.$location_parent->translate_name() .')';
                        Breadcrumbs::add(Breadcrumb::factory()->set_title($location_parent->translate_name())->set_url(Route::url('list', array('location'=>$location_parent->seoname))));
                    }

                    Breadcrumbs::add(Breadcrumb::factory()->set_title($location->translate_name())->set_url(Route::url('list', array('location'=>$location->seoname))));

                    if ($category_parent!==NULL)
                        Breadcrumbs::add(Breadcrumb::factory()->set_title($category_parent->translate_name())
                            ->set_url(Route::url('list', array('category'=>$category_parent->seoname,'location'=>$location->seoname))));

                    if ($category!==NULL)
                        Breadcrumbs::add(Breadcrumb::factory()->set_title($category->translate_name())
                            ->set_url(Route::url('list', array('category'=>$category->seoname,'location'=>$location->seoname))));
                }
                else
                {
                    if ($category_parent!==NULL)
                    {
                        $this->template->title .=' ('.$category_parent->translate_name() .')';
                        Breadcrumbs::add(Breadcrumb::factory()->set_title($category_parent->translate_name())
                            ->set_url(Route::url('list', array('category'=>$category_parent->seoname))));
                    }


                    if ($category!==NULL)
                        Breadcrumbs::add(Breadcrumb::factory()->set_title($category->translate_name())
                            ->set_url(Route::url('list', array('category'=>$category->seoname))));
                }



                $this->template->title = $ad->title.' - '. $this->template->title;

				Breadcrumbs::add(Breadcrumb::factory()->set_title($ad->title));


                $this->template->meta_description = $ad->title.' '.__('in').' '.$category->translate_name() .' '.__('on').' '.core::config('general.site_name');

				$permission = TRUE; //permission to add hit to advert and give access rights.
				$auth_user = Auth::instance();
                if(!$auth_user->logged_in() OR
					($auth_user->get_user()->id_user != $ad->id_user AND (! $auth_user->get_user()->is_admin() AND ! $auth_user->get_user()->is_moderator())) OR
					(! $auth_user->get_user()->is_admin() AND ! $auth_user->get_user()->is_moderator()))
				{

					$permission = FALSE;
					$user = NULL;
				}
                else
                    $user = $auth_user->get_user()->id_user;

                Model_Visit::hit_ad($ad->id_ad);
                $hits = $ad->count_ad_hit();

				$captcha_show = core::config('advertisement.captcha');


                if($ad->get_first_image() !== NULL)
                    Controller::$image = $ad->get_first_image();

                $view_file = 'pages/ad/single';

                if (Core::get('amp') == '1')
                {
                    //disable newrelic
                    if (function_exists('newrelic_disable_autorum'))
                        newrelic_disable_autorum();

                    $this->template = 'amp/main';
                    $this->before();
                    $this->template->canonical = Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle));
                    $this->template->structured_data = $ad->structured_data();
                    $view_file = 'amp/pages/ad/single';
                }

                $cf_list = $ad->custom_columns();
                $this->template->amphtml = Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle)).'?amp=1';

                $this->template->bind('content', $content);
                $this->template->content = View::factory($view_file, compact('ad',
                                                                             'permission',
                                                                             'hits',
                                                                             'captcha_show',
                                                                             'user',
                                                                             'cf_list'
                                                                            ));

			}
			//not found in DB
			else
			{
				//throw 404
				throw HTTP_Exception::factory(404,__('Page not found'));
			}

		}
		else//this will never happen
		{
			//throw 404
			throw HTTP_Exception::factory(404,__('Page not found'));
		}
	}

    /**
     *
     * Display reviews advert.
     * @throws HTTP_Exception_404
     *
     */
    public function action_reviews()
    {
        $seotitle = $this->request->param('seotitle',NULL);

        if ($seotitle!==NULL AND Core::config('advertisement.reviews')==1)
        {
            $ad = new Model_Ad();
            $ad->where('seotitle','=', $seotitle)
                ->where('status','!=',Model_Ad::STATUS_SPAM)
                ->limit(1)->cached()->find();

            if ($ad->loaded())
            {
                $errors = NULL;

                //adding a new review
                if($this->request->post() AND Auth::instance()->logged_in() )
                {
                    $user = Auth::instance()->get_user();

                    //only able to review if bought the product
                    if (Core::config('advertisement.reviews_paid')==1)
                    {
                        $order = new Model_Order();
                        $order->where('id_ad','=',$ad->id_ad)
                                ->where('id_user','=',$user->id_user)
                                ->where('id_product','=',Model_Order::PRODUCT_AD_SELL)
                                ->where('status','=',Model_Order::STATUS_PAID)
                                ->find();

                        if (!$order->loaded())
                        {
                            Alert::set(Alert::ERROR, __('You can only add a review if you bought this product'));
                            $this->redirect(Route::url('ad-review',array('seotitle'=>$ad->seotitle)));
                        }
                    }

                    //not allowing to review to yourself
                    if ($user->id_user == $ad->id_user)
                    {
                        Alert::set(Alert::ERROR, __('You can not review yourself.'));
                        $this->redirect(Route::url('ad-review',array('seotitle'=>$ad->seotitle)));
                    }

                    $review = new Model_Review();
                    $review->where('id_ad','=',$ad->id_ad)
                            ->where_open()
                            ->or_where('id_user','=',$user->id_user)
                            ->or_where('ip_address','=',ip2long(Request::$client_ip))
                            ->where_close()
                            ->find();
                            //d($review);
                    if (!$review->loaded())
                    {
                        if (captcha::check('review'))
                        {
                            $validation = Validation::factory($this->request->post())->rule('rate', 'numeric')
                                                        ->rule('description', 'not_empty')->rule('description', 'min_length', array(':value', 5))
                                                        ->rule('description', 'max_length', array(':value', 1000));
                            if ($validation->check())
                            {
                                $rate = core::post('rate');
                                if ($rate>Model_Review::RATE_MAX)
                                    $rate = Model_Review::RATE_MAX;
                                elseif ($rate<0)
                                    $rate = 0;

                                $review = new Model_Review();
                                $review->id_user        = $user->id_user;
                                $review->id_ad          = $ad->id_ad;
                                $review->description    = core::post('description');
                                $review->status         = Model_Review::STATUS_ACTIVE;
                                $review->ip_address     = ip2long(Request::$client_ip);
                                $review->rate           = $rate;
                                $review->save();
                                //email product owner?? notify him of new review
                                $ad->user->email('ad-review',
                                             array('[AD.TITLE]'     =>$ad->title,
                                                    '[RATE]'        =>$review->rate,
                                                    '[DESCRIPTION]' =>$review->description,
                                                    '[URL.QL]'      =>$ad->user->ql('ad-review',array('seotitle'=>$ad->seotitle))));

                                $ad->recalculate_rate();
                                $ad->user->recalculate_rate();
                                Alert::set(Alert::SUCCESS, __('Thanks for your review!'));
                            }
                            else{
                                $errors = $validation->errors('ad');
                                foreach ($errors as $f => $err)
                                    {
                                    Alert::set(Alert::ALERT, $err);
                                }
                            }
                        }
                        else
                            Alert::set(Alert::ERROR, __('Wrong Captcha'));
                    }
                    else
                        Alert::set(Alert::ERROR, __('You already added a review'));
                }

                $this->template->scripts['footer'][] = 'js/jquery.raty.min.js';
                $this->template->scripts['footer'][] = 'js/review.js';

                Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url(Route::url('default')));
                Breadcrumbs::add(Breadcrumb::factory()->set_title($ad->title)->set_url(Route::url('ad',array('seotitle'=>$ad->seotitle,'category'=>$ad->category->seoname))));

                $this->template->title = $ad->title.' - '. __('Reviews');

                Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Reviews')));


                $this->template->meta_description = text::removebbcode($ad->description);

                $permission = TRUE; //permission to add hit to advert and give access rights.
                $auth_user = Auth::instance();
                if(!$auth_user->logged_in() OR
                    ($auth_user->get_user()->id_user != $ad->id_user AND (! $auth_user->get_user()->is_admin() AND ! $auth_user->get_user()->is_moderator())) OR
                    (! $auth_user->get_user()->is_admin() AND ! $auth_user->get_user()->is_moderator()))
                {

                    $permission = FALSE;
                    $user = NULL;
                }
                else
                    $user = $auth_user->get_user()->id_user;


                $captcha_show = core::config('advertisement.captcha');


                if($ad->get_first_image() !== NULL)
                    Controller::$image = $ad->get_first_image();

                $reviews = new Model_Review();
                $reviews = $reviews->where('id_ad','=',$ad->id_ad)
                                ->where('status', '=', Model_Review::STATUS_ACTIVE)->find_all();

                $this->template->bind('content', $content);
                $this->template->content = View::factory('pages/ad/reviews',array('ad'               =>$ad,
                                                                                   'permission'     =>$permission,
                                                                                   'captcha_show'   =>$captcha_show,
                                                                                   'user'           =>$user,
                                                                                   'reviews'         =>$reviews,
                                                                                   'errors'         =>$errors
                                                                                   ));

            }
            //not found in DB
            else
            {
                //throw 404
                throw HTTP_Exception::factory(404,__('Page not found'));
            }

        }
        else//this will never happen
        {
            //throw 404
            throw HTTP_Exception::factory(404,__('Page not found'));
        }
    }

	/**
	 * [action_to_top] [pay to go on top, and make order]
	 *
	 */
	public function action_to_top()
	{
        //check pay to go top is enabled
        if(core::config('payment.to_top') == FALSE)
            throw HTTP_Exception::factory(404,__('Page not found'));

        $id_product = Model_Order::PRODUCT_TO_TOP;

        //check ad exists
        $id_ad  = $this->request->param('id');
        $ad     = new Model_Ad($id_ad);
        if ($ad->loaded())
        {
            //case when payment is set to 0, it goes to top without payment, no generating order
            if(core::config('payment.pay_to_go_on_top') <= 0)
            {
                $ad->published = Date::unix2mysql();
                try {
                    $ad->save();
                } catch (Exception $e) {
                    throw HTTP_Exception::factory(500,$e->getMessage());
                }

                $this->redirect(Route::url('list'));
            }

            $amount     = core::config('payment.pay_to_go_on_top');
            $currency   = core::config('payment.paypal_currency');

            $order = Model_Order::new_order($ad, $ad->user, $id_product, $amount, $currency);

            // redirect to payment
            $this->redirect(Route::url('default', array('controller' =>'ad','action'=>'checkout' ,'id' => $order->id_order)));
        }
        else
            throw HTTP_Exception::factory(404,__('Page not found'));
	}

    /**
     * [action_to_featured] [pay to go in featured]
     *
     */
    public function action_to_featured()
    {
        //check pay to featured top is enabled
        if(core::config('payment.to_featured') == FALSE)
            throw HTTP_Exception::factory(404,__('Page not found'));

        $id_product = Model_Order::PRODUCT_TO_FEATURED;

        //check ad exists
        $id_ad  = $this->request->param('id');

        //how many days
        if (!is_numeric($days = Core::request('featured_days')))
        {
            $plans = Model_Order::get_featured_plans();
            $days  = array_keys($plans);
            $days  = reset($days);
        }

        //get price for the days
        $amount = Model_Order::get_featured_price($days);

        $ad     = new Model_Ad($id_ad);
        if ($ad->loaded())
        {
            //case when payment is set to 0,gets featured for free...
            if($amount <= 0)
            {
                $ad->featured = Date::unix2mysql(time() + ($days * 24 * 60 * 60));
                try {
                    $ad->save();
                } catch (Exception $e) {
                    throw HTTP_Exception::factory(500,$e->getMessage());
                }

                $this->redirect(Route::url('list'));
            }

            $currency   = core::config('payment.paypal_currency');

            $order = Model_Order::new_order($ad, $ad->user, $id_product, $amount, $currency, NULL, $days);

            // redirect to payment
            $this->redirect(Route::url('default', array('controller' =>'ad','action'=>'checkout' ,'id' => $order->id_order)));
        }
        else
            throw HTTP_Exception::factory(404,__('Page not found'));
    }

    /**
     * [action_buy] Pay for ad, and set new order
     *
     */
    public function action_buy()
    {
        //check pay to featured top is enabled check stripe config too
        if(core::config('payment.paypal_seller') == FALSE AND Core::config('payment.stripe_connect')==FALSE  AND Core::config('payment.escrow_pay')==FALSE)
            throw HTTP_Exception::factory(404,__('Page not found'));

        $id_product = Model_Order::PRODUCT_AD_SELL;

        //check ad exists
        $id_ad  = $this->request->param('id');
        $ad     = new Model_Ad($id_ad);

        //loaded published and with stock if we control the stock.
        if($ad->loaded() AND $ad->status==Model_Ad::STATUS_PUBLISHED
            AND (core::config('payment.stock')==0 OR ($ad->stock > 0 AND core::config('payment.stock')==1)) )
        {

            //guest checkout since is not logged in
            if (!Auth::instance()->logged_in())
            {
                $this->redirect(Route::url('default', array('controller' =>'ad','action'=>'guestcheckout' ,'id' => $id_ad)));
            }
            else
            {
                $quantity   = Core::request('quantity', 1);
                $amount     = $ad->price * $quantity;
                $currency   = (isset($ad->cf_currency) AND $ad->cf_currency != '')?$ad->cf_currency:core::config('payment.paypal_currency');

                if (core::config('payment.stock') == 1 AND $ad->stock < $quantity)
                {
                    Alert::set(Alert::INFO, __('There is not enough stock; please choose another quantity.'));
                    $this->redirect(Route::url('ad', [
                        'controller' => 'ad',
                        'category' => $ad->category->seoname,
                        'seotitle' => $ad->seotitle
                    ]));
                }

                if ($ad->shipping_price() AND $ad->shipping_pickup() AND Core::request('shipping_pickup'))
                    $amount = $ad->price * $quantity;
                elseif ($ad->shipping_price())
                    $amount = ($ad->price * $quantity) + $ad->shipping_price();

                $order = Model_Order::new_order($ad, $this->user, $id_product, $amount, $currency, __('Purchase').': '.$ad->seotitle, NULL, $quantity);

                $this->redirect(Route::url('default', array('controller' =>'ad','action'=>'checkout' ,'id' => $order->id_order)));
            }

        }
        else
            throw HTTP_Exception::factory(404,__('Page not found'));

    }

    /**
     * guestcheckout for non registered users
     * @return [type] [description]
     */
    public function action_guestcheckout()
    {
        $id_ad  = $this->request->param('id');

        //only for not logued in users
        if (Auth::instance()->logged_in())
            $this->redirect(Route::url('default', array('controller' =>'ad','action'=>'buy' ,'id' => $id_ad)));

        //check ad exists
        $ad     = new Model_Ad($id_ad);

        //loaded published and with stock if we control the stock.
        if($ad->loaded() AND $ad->status==Model_Ad::STATUS_PUBLISHED
            AND (core::config('payment.stock')==0 OR ($ad->stock > 0 AND core::config('payment.stock')==1))
            AND (core::config('payment.paypal_seller')==1 OR core::config('payment.stripe_connect')==1 OR core::config('payment.escrow_pay')==1)
            )
        {
            if($quantity = (int) core::get('quantity', 1))
            {
                $ad->price = $ad->price * $quantity;
            }

            // Calculate VAT
            if(isset($ad->cf_vatnumber) AND $ad->cf_vatnumber AND isset($ad->cf_vatcountry) AND $ad->cf_vatcountry){
                $vatcountry = $ad->cf_vatcountry;
                $vatnumber = $ad->cf_vatnumber;
            } elseif(isset($ad->user->cf_vatnumber) AND $ad->user->cf_vatnumber AND isset($ad->user->cf_vatcountry) AND $ad->user->cf_vatcountry) {
                $vatcountry = $ad->user->cf_vatcountry;
                $vatnumber = $ad->user->cf_vatnumber;
            } else{
                $vatcountry = NULL;
                $vatnumber = NULL;
            }

            if(isset($vatcountry) AND isset($vatnumber)){
                if(euvat::is_eu_country($vatcountry))
                    $vat = euvat::vat_by_country($vatcountry);
                elseif(isset($ad->cf_vatcountry) AND isset($ad->cf_vatnoneu) AND $ad->cf_vatnoneu > 0 AND $ad->cf_vatnoneu!=NULL)
                    $vat = $ad->cf_vatnoneu;
                elseif(isset($ad->user->cf_vatcountry) AND isset($ad->user->cf_vatnoneu) AND $ad->user->cf_vatnoneu > 0 AND $ad->user->cf_vatnoneu!=NULL)
                    $vat = $ad->user->cf_vatnoneu;
                else
                    $vat = 0;
            } else {
                $vat = 0;
            }

            //template header
            $this->template->title              = __('Checkout').' '.Model_Order::product_desc(Model_Order::PRODUCT_AD_SELL);
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url(Route::url('default')));
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->template->title ));

            Controller::$full_width = TRUE;

            $this->template->bind('content', $content);

            $this->template->content = View::factory('pages/ad/guestcheckout',array('ad' => $ad));
        }
        else
        {
            //throw 404
            throw HTTP_Exception::factory(404,__('Page not found'));
        }
    }


    /**
     * pay an invoice, renders the paymenthods button, anyone with an ID of an order can pay it, we do not have control
     * @return [type] [description]
     */
    public function action_checkout()
    {
        $order = new Model_Order($this->request->param('id'));

        if ($order->loaded())
        {
            //hack jquery paymill
            Paymill::jquery();

            //if paid...no way jose
            if ($order->status != Model_Order::STATUS_CREATED)
            {
                Alert::set(Alert::INFO, __('This order was already paid.'));
                $this->redirect(Route::url('default'));
            }

            // Only if zenith payment is configured
            if (Core::config('payment.zenith_merchantid') != ''
                AND Core::config('payment.zenith_uid') != ''
                AND Core::config('payment.zenith_pwd') != ''
                AND Auth::instance()->logged_in()
                AND empty(Auth::instance()->get_user()->phone))
            {
                Alert::set(Alert::INFO, __('Please, enter your phone first'));
                $this->redirect(Route::url('oc-panel', array('controller'=>'profile','action'=>'edit')));
            }

            //checks coupons or amount of featured days
            $order->check_pricing();

            //adds VAT to the amount
            $order->add_VAT();

            //template header
            $this->template->title              = __('Checkout').' '.Model_Order::product_desc($order->id_product);
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url(Route::url('default')));
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->template->title ));

            Controller::$full_width = TRUE;

            $this->template->bind('content', $content);

            $this->template->content = View::factory('pages/ad/checkout',array('order' => $order));
        }
        else
        {
            //throw 404
            throw HTTP_Exception::factory(404,__('Page not found'));
        }
    }


    /**
     * confirms a checkout when its a free order
     * @return [type] [description]
     */
    public function action_checkoutfree()
    {
        if (Auth::instance()->logged_in() OR ! $this->request->post())
        {
            $order = new Model_Order($this->request->param('id'));
            $ad = new Model_Ad($order->id_ad);
        }
        else
        {
            //we need an email before create user
            $validation =   Validation::factory(['email' => core::post('email')])
                ->rule('email', 'not_empty')
                ->rule('email', 'email');

            if (! $validation->check())
            {
                $errors = $validation->errors('user');

                foreach ($errors as $error)
                    Alert::set(Alert::ALERT, $error);

                $this->redirect(Route::url('default', array('controller' =>'ad','action'=>'buy' ,'id' => $this->request->param('id'))));
            }

            $ad = new Model_Ad($this->request->param('id'));

            //create user if does not exists, if not will return the user
            $user = Model_User::create_email(core::post('email'));

            //new order
            $order = Model_Order::new_order($ad, $user, Model_Order::PRODUCT_AD_SELL,
                $ad->price, core::config('payment.paypal_currency'), __('Purchase').': '.$ad->seotitle);
        }

        if (! $order->loaded())
        {
            //throw 404
            throw HTTP_Exception::factory(404,__('Page not found'));
        }

        //if paid...no way jose
        if ($order->status != Model_Order::STATUS_CREATED)
        {
            Alert::set(Alert::INFO, __('This order was already paid.'));
            $this->redirect(Route::url('default'));
        }

        //checks coupons or amount of featured days
        $order->check_pricing();

        //adds VAT to the amount
        $order->add_VAT();

        //he needs to pay...little prick
        if ($order->amount > 0)
        {
            $this->redirect(Route::url('default', array('controller' =>'ad','action'=>'checkout' ,'id' => $order->id_order)));
        }

        $order->confirm_payment('cash');

        $moderation = core::config('general.moderation');

        if ($moderation == Model_Ad::PAYMENT_MODERATION
            AND $order->id_product == Model_Order::PRODUCT_CATEGORY)
        {
            Alert::set(Alert::INFO, __('Advertisement is received, but first administrator needs to validate. Thank you for being patient!'));
            $this->redirect(Route::url('default', ['action' => 'thanks', 'controller' => 'ad', 'id' => $ad->id_ad]));
        }

        if ($moderation == Model_Ad::PAYMENT_ON
            AND $order->id_product == Model_Order::PRODUCT_CATEGORY)
        {
            $this->redirect(Route::url('default', ['action' => 'thanks', 'controller' => 'ad', 'id' => $ad->id_ad]));
        }

        if (Auth::instance()->logged_in())
        {
            $this->redirect(Route::url('oc-panel', ['controller' => 'profile', 'action' => 'orders']));
        }

        if ($order->id_product == Model_Order::PRODUCT_AD_SELL)
        {
            $this->redirect(Route::url('ad', ['controller' => 'ad', 'category' => $ad->category->seoname, 'seotitle' => $ad->seotitle]));
        }

        $this->redirect(Route::url('default'));
    }


    /**
     * thanks for publish
     * @return [type] [description]
     */
    public function action_thanks()
    {
        $ad = new Model_Ad($this->request->param('id'));

        if ($ad->loaded())
        {
            $page = Model_Content::get_by_title(Core::config('advertisement.thanks_page'));

            //template header
            $this->template->title              = ($page->loaded())?$page->title:__('Thanks');
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url(Route::url('default')));
            Breadcrumbs::add(Breadcrumb::factory()->set_title($ad->title)->set_url(Route::url('ad',array('category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))));
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->template->title));

            $this->template->bind('content', $content);

            $this->template->content = View::factory('pages/ad/thanks',array('ad' => $ad,'page'=>$page));
        }
        else
        {
            //throw 404
            throw HTTP_Exception::factory(404,__('Page not found'));
        }
    }

	public function action_advanced_search()
	{
        if (Theme::get('infinite_scroll'))
        {
            $this->template->scripts['footer'][] = '//cdn.jsdelivr.net/jquery.infinitescroll/2.1/jquery.infinitescroll.js';
            $this->template->scripts['footer'][] = 'js/listing.js';
        }
        if(core::config('general.auto_locate') OR core::config('advertisement.map'))
        {
            Theme::$scripts['async_defer'][] = '//maps.google.com/maps/api/js?libraries=geometry,places&v=3&key='.core::config("advertisement.gm_api_key").'&callback=initLocationsGMap&language='.i18n::get_gmaps_language(i18n::$locale);
        }
        $this->template->scripts['footer'][] = 'js/jquery.toolbar.js';
        $this->template->scripts['footer'][] = 'js/sort.js';

		//template header
		$this->template->title           	= __('Advanced Search');
		$this->template->meta_description	= __('Search in').' '.core::config('general.site_name');

		//breadcrumbs
		Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url(Route::url('default')));
        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->template->title ));

		$pagination = NULL;
		$ads        = NULL;
		$res_count  = NULL;
		$user       = $this->user ? $this->user : NULL;

		if($this->request->query()) // after query has detected
		{
        	// variables
        	$search_advert 	= core::get('title');
        	$search_loc 	= core::get('location');

        	// filter by each variable
        	$ads = new Model_Ad();

            //if sort by distance
            if ((core::request('sort',core::config('advertisement.sort_by')) == 'distance' OR core::request('userpos') == 1) AND Model_User::get_userlatlng())
            {
                $ads->select(array(DB::expr('degrees(acos(sin(radians('.$_COOKIE['mylat'].')) * sin(radians(`latitude`)) + cos(radians('.$_COOKIE['mylat'].')) * cos(radians(`latitude`)) * cos(radians(abs('.$_COOKIE['mylng'].' - `longitude`))))) * 111.321'), 'distance'))
                ->where('latitude','IS NOT',NULL)
                ->where('longitude','IS NOT',NULL);
            }

        	// early filter
	        $ads = $ads->where('status', '=', Model_Ad::STATUS_PUBLISHED);

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

            if (core::request('userpos') == 1 AND Model_User::get_userlatlng())
            {
                if (is_numeric(Core::cookie('mydistance')) AND Core::cookie('mydistance') <= 500)
                    $location_distance = Core::config('general.measurement') == 'imperial' ? (Num::round(Core::cookie('mydistance') * 1.60934)) : Core::cookie('mydistance');
                else
                    $location_distance = Core::config('general.measurement') == 'imperial' ? (Num::round(Core::config('advertisement.auto_locate_distance') * 1.60934)) : Core::config('advertisement.auto_locate_distance');
                $ads->where(DB::expr('degrees(acos(sin(radians('.$_COOKIE['mylat'].')) * sin(radians(`latitude`)) + cos(radians('.$_COOKIE['mylat'].')) * cos(radians(`latitude`)) * cos(radians(abs('.$_COOKIE['mylng'].' - `longitude`))))) * 111.321'),'<=',$location_distance);
            }

	        if(!empty($search_advert) OR (core::get('search')!==NULL AND strlen(core::get('search'))>=3))
	        {
	        	// if user is using search from header
	        	if(core::get('search'))
	        		$search_advert = core::get('search');

	        	if(core::config('general.search_by_description') == TRUE)
                        $ads->where_open()
                            ->where('title', 'like', '%'.$search_advert.'%')
                            ->or_where('description', 'like', '%'.$search_advert.'%')
                            ->where_close();
                else
                    $ads->where('title', 'like', '%'.$search_advert.'%');
	        }

            //cf filter arrays
        	$cf_fields      = array();
            $cf_user_fields = array();
            foreach ($this->request->query() as $name => $field)
            {
                if (isset($field) AND $field != NULL)
                {
                	// get field group name
                    $cf_group_name = str_replace('cf_', '', $name);

                    if (core::count(explode('_', $cf_group_name)) > 1)
                    {
                        $cf_group_name = strtolower(explode('_', $cf_group_name)[0]);
                    }

                    // get by prefix cf
                    if (strpos($name,'cf_') !== FALSE
                        AND array_key_exists(str_replace('cf_','', $name), Model_Field::get_all()) )
                    {
                        $cf_fields[$name] = $field;
                        //checkbox when selected return string 'on' as a value
                        if($field == 'on')
                            $cf_fields[$name] = 1;
                        elseif(empty($field)){
                            $cf_fields[$name] = NULL;
                        }
                    }
                    // get by prefix cf group
                    elseif (strpos($name,'cf_') !== FALSE
                        AND array_key_exists($cf_group_name, Model_Field::get_all()) )
                    {
                        $cf_fields[$name] = $field;
                        //checkbox when selected return string 'on' as a value
                        if($field == 'on')
                            $cf_fields[$name] = 1;
                        elseif(empty($field)){
                            $cf_fields[$name] = NULL;
                        }
                    }
                    // get by prefix user cf
                    elseif (strpos($name,'cfuser_') !== FALSE
                        AND array_key_exists(str_replace('cfuser_','',$name), Model_UserField::get_all()) )
                    {
                        $name = str_replace('cfuser_','cf_',$name);
                        $cf_user_fields[$name] = $field;
                        //checkbox when selected return string 'on' as a value
                        if($field == 'on')
                            $cf_user_fields[$name] = 1;
                        elseif(empty($field)){
                            $cf_user_fields[$name] = NULL;
                        }

                    }
                }
        	}

	        $category = NULL;
	        $location = NULL;

            if (core::config('general.search_multi_catloc') AND Theme::$is_mobile === FALSE) //mobile native menus don't support multiple selection
            {
                //filter by category
                if (is_array(core::get('category')))
                {
                    $cat_siblings_ids = array();

                    foreach (core::get('category') as $cat)
                    {
                        if ($cat!==NULL)
                        {
                            $category = new Model_Category();
                            $category->where('seoname','=',$cat)->cached()->limit(1)->find();
                            if ($category->loaded())
                                $cat_siblings_ids = array_merge($cat_siblings_ids,$category->get_siblings_ids());
                        }
                    }

                    if (core::count($cat_siblings_ids) > 0)
                        $ads->where('id_category', 'IN', $cat_siblings_ids);
                }

                //filter by location
                if (is_array(core::get('location')))
                {
                    $loc_siblings_ids = array();

                    foreach (core::get('location') as $loc)
                    {
                        if ($loc!==NULL)
                        {
                            $location = new Model_location();
                            $location->where('seoname','=',$loc)->cached()->limit(1)->find();
                            if ($location->loaded())
                                $loc_siblings_ids = array_merge($loc_siblings_ids,$location->get_siblings_ids());
                        }
                    }

                    if (core::count($loc_siblings_ids) > 0)
                        $ads->where('id_location', 'IN', $loc_siblings_ids);
                }
            }
            else
            {
                if (core::get('category')!==NULL)
                {
                    $category = new Model_Category();
                    $category->where('seoname',(is_array(core::get('category'))?'in':'='),core::get('category'))->cached()->limit(1)->find();
                    if ($category->loaded())
                        $ads->where('id_category', 'IN', $category->get_siblings_ids());
                }

                $location = NULL;
                //filter by location
                if (core::get('location')!==NULL)
                {
                    $location = new Model_location();
                    $location->where('seoname',(is_array(core::get('location'))?'in':'='),core::get('location'))->cached()->limit(1)->find();
                    if ($location->loaded())
                        $ads->where('id_location', 'IN', $location->get_siblings_ids());
                }
            }

            //filter by price(s)
            if (is_numeric($price_min = str_replace(',','.',core::get('price-min')))) // handle comma (,) used in some countries for prices
                $price_min = (float)$price_min; // round((float)$price_min,2)
            if (is_numeric($price_max = str_replace(',','.',core::get('price-max')))) // handle comma (,) used in some countries for prices
                $price_max = (float)$price_max; // round((float)$price_max,2)

            if (is_numeric($price_min) AND is_numeric($price_max))
            {
                // swap 2 values
                if ($price_min > $price_max)
                {
                    $aux = $price_min;
                    $price_min = $price_max;
                    $price_max = $aux;
                    unset($aux);
                }

                $ads->where('price', 'BETWEEN', array($price_min,$price_max));
            }
            elseif (is_numeric($price_min)) // only min price has been provided
            {
                $ads->where('price', '>=', $price_min);
            }
            elseif (is_numeric($price_max)) // only max price has been provided
            {
                $ads->where('price', '<=', $price_max);
            }

            // filter by language
            if (Core::config('general.multilingual') == 1 AND Core::get('locale') !== NULL)
            {
                $ads->where('locale', '=', Core::get('locale'));
            }

            //filter by CF ads
	        if (core::count($cf_fields)>0)
            {
                foreach ($cf_fields as $key => $value)
    	        {
                    //filter by range
                    if(array_key_exists(str_replace('cf_','',$key), Model_Field::get_all())
                        AND Model_Field::get_all()[str_replace('cf_','',$key)]['type'] == 'range')
                    {
                        $cf_min = isset($value[0]) ? $value[0] : NULL;
                        $cf_max = isset($value[1]) ? $value[1] : NULL;

                        if (is_numeric($cf_min = str_replace(',','.',$cf_min))) // handle comma (,) used in some countries
                            $cf_min = (float)$cf_min;
                        if (is_numeric($cf_max = str_replace(',','.',$cf_max))) // handle comma (,) used in some countries
                            $cf_max = (float)$cf_max;

                        if (is_numeric($cf_min) AND is_numeric($cf_max))
                        {
                            // swap 2 values
                            if ($cf_min > $cf_max)
                            {
                                $aux = $cf_min;
                                $cf_min = $cf_max;
                                $cf_max = $aux;
                                unset($aux);
                            }

                            $ads->where($key, 'BETWEEN', array($cf_min,$cf_max));
                        }
                        elseif (is_numeric($cf_min)) // only min cf has been provided
                            $ads->where($key, '>=', $cf_min);
                        elseif (is_numeric($cf_max)) // only max cf has been provided
                            $ads->where($key, '<=', $cf_max);
                    }
    	        	elseif(is_numeric($value))
    	        		$ads->where($key, '=', $value);
    	        	elseif(is_string($value))
    	        		$ads->where($key, 'like', '%'.$value.'%');
                    elseif(is_array($value))
                    {
                        if ( ! empty($value = array_filter($value)))
                            $ads->where($key, 'IN', $value);
                    }
    	        }
            }

            //filter by user
            if (core::count($cf_user_fields)>0)
            {
                $users = new Model_User();

                foreach ($cf_user_fields as $key => $value)
                {
                    if(is_numeric($value))
                        $users->where($key, '=', $value);
                    elseif(is_string($value))
                        $users->where($key, 'like', '%'.$value.'%');
                    elseif(is_array($value))
                    {
                        if ( ! empty($value = array_filter($value)))
                            $ads->where($key, 'IN', $value);
                    }
                }

                $users = $users->find_all();
                if ($users->count()>0)
                    $ads->where('id_user','in',$users->as_array());
                else
                    $ads->where('id_user','=',0);
            }

	        // count them for pagination
			$res_count = $ads->count_all();

			if($res_count>0)
			{

				// pagination module
                $pagination = Pagination::factory(array(
                        'view'              => 'pagination',
                        'total_items'       => $res_count,
                        'items_per_page'    => core::request('items_per_page',core::config('advertisement.advertisements_per_page')),
                ))->route_params(array(
                        'controller'        => $this->request->controller(),
                        'action'            => $this->request->action(),
                        'category'          => ($category!==NULL)?$category->seoname:NULL,
                ));

		        Breadcrumbs::add(Breadcrumb::factory()->set_title(__("Page ") . $pagination->current_page));

                /**
                 * order depending on the sort parameter
                 */
                switch (core::request('sort',core::config('advertisement.sort_by')))
                {
                    //title z->a
                    case 'title-asc':
                        $ads->order_by('title','asc')->order_by('published','desc');
                        break;
                    //title a->z
                    case 'title-desc':
                        $ads->order_by('title','desc')->order_by('published','desc');
                        break;
                    //cheaper first
                    case 'price-asc':
                        $ads->order_by('price','asc')->order_by('published','desc');
                        break;
                    //expensive first
                    case 'price-desc':
                        $ads->order_by('price','desc')->order_by('published','desc');
                        break;
                    //featured
                    case 'featured':
                        $ads->order_by('featured','desc')->order_by('published','desc');
                        break;
                    //rating
                    case 'rating':
                        $ads->order_by('rate','desc')->order_by('published','desc');
                        break;
                    //favorited
                    case 'favorited':
                        $ads->order_by('favorited','desc')->order_by('published','desc');
                        break;
                    //distance
                    case 'distance':
                        if (Model_User::get_userlatlng() AND core::config('general.auto_locate'))
                        $ads->order_by('distance','asc')->order_by('published','asc');
                        break;
                    //oldest first
                    case 'published-asc':
                        $ads->order_by('published','asc');
                        break;
                    //newest first
                    case 'published-desc':
                    default:
                        $ads->order_by('published','desc');
                        break;
                }

                //we sort all ads with few parameters
                $ads = $ads ->limit($pagination->items_per_page)
                            ->offset($pagination->offset)
                            ->find_all();
			}
            else
            {
                $ads = NULL;
            }

		}

		$this->template->bind('content', $content);

		$this->template->content = View::factory('pages/ad/advanced_search', array('ads'		      => $ads,
        																		   'categories'	      => Model_Category::get_as_array(),
        																		   'order_categories' => Model_Category::get_multidimensional(),
        																		   'locations'	      => Model_Location::get_as_array(),
        																		   'order_locations'  => Model_Location::get_multidimensional(),
        																		   'pagination'	      => $pagination,
        																		   'user'		      => $user,
        																		   'fields' 		  => Model_Field::get_all(),
																				   'total_ads' 		  => $res_count
        																		   ));


	}


}// End ad controller
