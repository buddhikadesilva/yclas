<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Panel_Location extends Auth_Crud {



	/**
	 * @var $_index_fields ORM fields shown in index
	 */
	protected $_index_fields = array('id_location','name','id_location_parent');

	/**
	 * @var $_orm_model ORM model name
	 */
	protected $_orm_model = 'location';


    /**
     * overwrites the default crud index
     * @param  string $view nothing since we don't use it
     * @return void
     */
    public function action_index($view = NULL)
    {
        //template header
        $this->template->title  = __('Locations');

        $this->template->styles  = array('css/sortable.css' => 'screen',
                                         '//cdn.jsdelivr.net/bootstrap.tagsinput/0.3.9/bootstrap-tagsinput.css' => 'screen');
        $this->template->scripts['footer'][] = 'js/jquery-sortable-min.js';
        $this->template->scripts['footer'][] = 'js/oc-panel/locations.js';
        $this->template->scripts['footer'][] = '//cdn.jsdelivr.net/bootstrap.tagsinput/0.3.9/bootstrap-tagsinput.min.js';

        if (intval(Core::get('id_location', 1)) > 0)
        {
            $location = new Model_Location(intval(Core::get('id_location', 1)));

            if ($location->loaded())
            {
                if ($location->parent->loaded() AND $location->parent->id_location != 1)
                {
                    Breadcrumbs::add(Breadcrumb::factory()->set_title($location->parent->name)->set_url(Route::url('oc-panel',array('controller'=>'location','action'=>''.'?id_location='.$location->parent->id_location))));
                }

                $locs = new Model_Location();
                $locs = $locs->where('id_location_parent','=',Core::get('id_location', 1))->order_by('order','asc')->find_all()->cached()->as_array('id_location');
            }
            else
            {
                Alert::set(Alert::ERROR, __('You are selecting a location that does not exist'));
                $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller())));
            }
        }

        $this->template->content = View::factory('oc-panel/pages/locations/index',array('locs' => $locs,'location' => $location));
    }

    /**
     * CRUD controller: CREATE
     */
    public function action_create()
    {

        $this->template->title = __('New').' '.__($this->_orm_model);

        $this->template->scripts['footer'][] = 'js/gmaps.min.js';
        $this->template->scripts['footer'][] = 'js/oc-panel/locations-gmap.js';
        $this->template->scripts['async_defer'][] = '//maps.google.com/maps/api/js?libraries=geometry&v=3&key='.core::config("advertisement.gm_api_key").'&callback=initLocationsGMap';

        $location = new Model_Location();

        if ($post = $this->request->post())
        {
            //check if the parent is loaded/exists avoiding errors
            $post['id_location_parent'] = $post['id_location_parent'] != '' ? $post['id_location_parent'] : 1;
            $parent_loc = new Model_Location($post['id_location_parent']);
            if ( ! $parent_loc->loaded())
            {
                Alert::set(Alert::INFO, __('You are assigning a parent location that does not exist'));
                $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'create')));
            }

            foreach ($post as $name => $value)
            {
                //for description we accept the HTML as comes...a bit risky but only admin can
                if ($name=='description')
                {
                    $location->description = Kohana::$_POST_ORIG['description'];
                }
                elseif($name != 'submit')
                {
                    $location->$name = $value;
                }
            }

            if( ! isset($post['seoname']))
            {
                $location->seoname = $location->gen_seotitle($post['seoname']);
            }
            else
            {
                $location->seoname = $post['seoname'];
            }

            try
            {
                $location->save();
            }
            catch (Exception $e)
            {
                throw HTTP_Exception::factory(500,$e->getMessage());
            }

            $this->action_deep();
            Model_Location::cache_delete();

            Alert::set(Alert::SUCCESS, __('Location created'));

            $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller())).(Core::post('id_location_parent') ? '?id_location='.Core::post('id_location_parent') : NULL));

       }

        $locations = array('' => '');

        foreach (Model_Location::get_as_array() as $location)
        {
            $locations[$location['id']] = $location['name'];
        }

        return $this->render('oc-panel/pages/locations/create', compact('locations'));

    }
    /**
     * CRUD controller: UPDATE
     */
    public function action_update()
    {
        $this->template->title = __('Update').' '.__($this->_orm_model).' '.$this->request->param('id');

        $this->template->scripts['footer'][] = 'js/gmaps.min.js';
        $this->template->scripts['footer'][] = 'js/oc-panel/locations-gmap.js';
        $this->template->scripts['async_defer'][] = '//maps.google.com/maps/api/js?libraries=geometry&v=3&key='.core::config("advertisement.gm_api_key").'&callback=initLocationsGMap&language='.i18n::get_gmaps_language(i18n::$locale);

        $form = new FormOrm($this->_orm_model,$this->request->param('id'));
        $location = new Model_Location($this->request->param('id'));

        if ($this->request->post())
        {
            if ( $success = $form->submit() )
            {
                if ($form->object->id_location == $form->object->id_location_parent)
                {
                    Alert::set(Alert::INFO, __('You can not set as parent the same location'));
                    $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'update','id'=>$form->object->id_location)));
                }

                //check if the parent is loaded/exists avoiding errors
                $parent_loc = new Model_Location($form->object->id_location_parent);
                if (!$parent_loc->loaded())
                {
                    Alert::set(Alert::INFO, __('You are assigning a parent location that does not exist'));
                    $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'create')));
                }

                $form->object->description = Kohana::$_POST_ORIG['formorm']['description'];

                try {
                    $form->object->save();
                } catch (Exception $e) {
                    throw HTTP_Exception::factory(500,$e->getMessage());
                }

                $form->object->parent_deep =  $form->object->get_deep();

                try {
                    $form->object->save();
                } catch (Exception $e) {
                    throw HTTP_Exception::factory(500,$e->getMessage());
                }

                $this->action_deep();

                //rename icon name
                if($location->has_image AND ($location->seoname != $form->object->seoname))
                    $location->rename_icon($form->object->seoname);

                Model_Location::cache_delete();

                Alert::set(Alert::SUCCESS, __('Item updated'));
                $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller())));
            }
            else
            {
                Alert::set(Alert::ERROR, __('Check form for errors'));
            }
        }

        return $this->render('oc-panel/pages/locations/update', array('form' => $form, 'location' => $location));
    }

    /**
     * saves the location in a specific order and change the parent
     * @return void
     */
    public function action_saveorder()
    {
        $this->auto_render = FALSE;
        $this->template = View::factory('js');

        $loc = new Model_Location(core::get('id_location'));

        //check if the parent is loaded/exists avoiding errors
        $parent_loc = new Model_Location(core::get('id_location_parent'));

        if ($loc->loaded() AND $parent_loc->loaded())
        {
            //saves the current location
            $loc->id_location_parent = core::get('id_location_parent');
            $loc->parent_deep        = core::get('deep');


            //saves the locations in the same parent the new orders
            $order = 0;
            foreach (core::get('brothers') as $id_loc)
            {
                $id_loc = substr($id_loc,3);//removing the li_ to get the integer

                //not the main location so loading and saving
                if ($id_loc!=core::get('id_location'))
                {
                    $c = new Model_Location($id_loc);
                    $c->parent_deep     = core::get('deep');
                    $c->order           = $order;

                    try {
                        $c->save();
                    } catch (Exception $e) {
                        Kohana::$log->add(Log::ERROR, 'Controller Location saveorder id_location: '.$c->id_location.'- URL:'.URL::current());
                    }
                }
                else
                {
                    //saves the main location
                    $loc->order  = $order;
                    try {
                        $loc->save();
                    } catch (Exception $e) {
                        Kohana::$log->add(Log::ERROR, 'Controller Location saveorder id_location: '.$loc->id_location.'- URL:'.URL::current());
                    }
                }
                $order++;
            }

            //update deep for all the locations
            $this->action_deep();
            Model_Location::cache_delete();
            $this->template->content = __('Saved');
        }
        else
            $this->template->content = __('Error');
    }

    /**
     * CRUD controller: DELETE
     */
    public function action_delete()
    {
        $this->auto_render = FALSE;

        $locations = array();

        if ($id_location = $this->request->param('id'))
            $locations[] = $id_location;
        elseif (core::post('locations'))
            $locations = core::post('locations');

        if (core::count($locations) > 0)
        {
            foreach ($locations as $id_location)
            {
                $location = new Model_Location($id_location);

                //update the elements related to that ad
                if ($location->loaded())
                {
                    //check if the parent is loaded/exists avoiding errors, if doesnt exist to the root
                    $parent_loc = new Model_Location($location->id_location_parent);
                    if ($parent_loc->loaded())
                        $id_location_parent = $location->id_location_parent;
                    else
                        $id_location_parent = 1;

                    //update all the siblings this location has and set the location parent
                    $query = DB::update('locations')
                                ->set(array('id_location_parent' => $id_location_parent))
                                ->where('id_location_parent','=',$location->id_location)
                                ->execute();

                    //update all the ads this location has and set the location parent
                    $query = DB::update('ads')
                                ->set(array('id_location' => $id_location_parent))
                                ->where('id_location','=',$location->id_location)
                                ->execute();

                    try
                    {
                        $location_name = $location->name;
                        $location->delete();
                        $this->template->content = 'OK';

                        //recalculating the deep of all the categories
                        $this->action_deep();
                        Model_Location::cache_delete();
                        Alert::set(Alert::SUCCESS, sprintf(__('Location %s deleted'), $location_name));
                    }
                    catch (Exception $e)
                    {
                         Alert::set(Alert::ERROR, $e->getMessage());
                    }
                }
                else
                     Alert::set(Alert::SUCCESS, __('Location not deleted'));
            }
        }

        HTTP::redirect(Route::url('oc-panel',array('controller'  => 'location','action'=>'index')));

    }

    /**
     * Creates multiple locations just with name
     * @return void
     */
    public function action_multy_locations()
    {
        $this->auto_render = FALSE;

        //update the elements related to that ad
        if ($_POST)
        {
            if(core::post('multy_locations') !== "")
            {
                $multy_locs = explode(',', core::post('multy_locations'));
                $obj_location = new Model_Location();
                $locations_array = array();

                if (is_array($multy_locs))
                {
                    $execute = FALSE;
                    $insert = DB::insert('locations', array('name', 'seoname', 'id_location_parent'));
                    foreach ($multy_locs as $name)
                    {
                        if ( ! empty($name) AND ! in_array($seoname = $obj_location->gen_seoname($name), $locations_array))
                        {
                            $execute = TRUE;
                            $insert = $insert->values(array($name, $seoname, Core::get('id_location', 1)));

                            $locations_array[] = $seoname;
                        }
                    }

                    // Insert everything with one query.
                    if ($execute==TRUE)
                    {
                        $insert->execute();
                        Model_Location::cache_delete();
                    }
                }
            }
            else
                Alert::set(Alert::INFO, __('Select some locations first.'));
        }

        HTTP::redirect(Route::url('oc-panel',array('controller'  => 'location','action'=>'index')).'?id_location='.Core::get('id_location', 1));
    }

    /**
     * Import multiple locations from geonames
     * @return void
     */
    public function action_geonames()
    {
        $this->template->title  = __('Geonames');

        $this->template->scripts['footer'][] = 'js/oc-panel/locations-geonames.js';

        $location = NULL;

        if (intval(Core::get('id_location')) > 0)
        {
            $location = new Model_Location(Core::get('id_location'));

            if ($location->loaded())
            {
                Breadcrumbs::add(Breadcrumb::factory()->set_title($location->name)->set_url(Route::url('oc-panel',array('controller'=>'location','action'=>'geonames')).'?id_location='.$location->id_location));
            }
            else
            {
                Alert::set(Alert::ERROR, __('You are selecting a location that does not exist'));
                $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller())));
            }
        }

        //update the elements related to that ad
        if (core::post('geonames_locations') !== "")
        {

            $geonames_locations = json_decode(core::post('geonames_locations'));

            if (core::count($geonames_locations) > 0)
            {
                $obj_location = new Model_Location();
                $locations_array = array();

                $insert = DB::insert('locations', array('name', 'seoname', 'id_location_parent', 'latitude', 'longitude', 'id_geoname', 'fcodename_geoname', 'order'));

                $i = 1;
                $execute = FALSE;
                foreach ($geonames_locations as $location)
                {
                    if ( !empty($location->name) AND ! in_array($location->seoname = $obj_location->gen_seoname($location->name), $locations_array))
                    {
                        $execute = TRUE;
                        $insert = $insert->values(array($location->name,
                                                        $location->seoname,
                                                        Core::get('id_location', 1),
                                                        isset($location->lat)?$location->lat:NULL,
                                                        isset($location->long)?$location->long:NULL,
                                                        isset($location->id_geoname)?$location->id_geoname:NULL,
                                                        isset($location->fcodename_geoname)?$location->fcodename_geoname:NULL,
                                                        $i));

                        $locations_array[] = $location->seoname;

                        $i++;
                    }
                }

                // Insert everything with one query.
                if ($execute==TRUE)
                {
                    $insert->execute();
                    Model_Location::cache_delete();
                }

                HTTP::redirect(Route::url('oc-panel',array('controller'  => 'location','action'=>'index')).'?id_location='.Core::get('id_location', 1));
            }

        }
        else
            Alert::set(Alert::INFO, __('Select some locations first.'));


        $this->template->content = View::factory('oc-panel/pages/locations/geonames',array('location' => $location));
    }

    /**
     * recalculating the deep of all the locations
     * @return [type] [description]
     */
    public function action_deep()
    {
        //clean the cache so we get updated results
        Model_Category::cache_delete();

        //getting all the cats as array
        $locs_arr  = Model_Location::get_as_array();

        $locs = new Model_Location();
        $locs = $locs->order_by('order','asc')->find_all()->cached()->as_array('id_location');
        foreach ($locs as $loc)
        {
            $deep = 0;

            //getin the parent of this location
            $id_location_parent = $locs_arr[$loc->id_location]['id_location_parent'];

            //counting till we find the begining
            while ($id_location_parent != 1 AND $id_location_parent != 0 AND $deep<10)
            {
                $id_location_parent = $locs_arr[$id_location_parent]['id_location_parent'];
                $deep++;
            }

            //saving the location only if different deep
            if ($loc->parent_deep != $deep)
            {
                $loc->parent_deep = $deep;
                $loc->save();
            }
        }
        //Alert::set(Alert::INFO, __('Success'));
        //HTTP::redirect(Route::url('oc-panel',array('controller'  => 'location','action'=>'index')));
    }

	public function action_icon()
	{
        //get icon
        if (isset($_FILES['location_icon']))
            $icon = $_FILES['location_icon']; //file post
        else
            $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'index')));

        $location = new Model_Location($this->request->param('id'));

        if (core::config('image.aws_s3_active'))
        {
            require_once Kohana::find_file('vendor', 'amazon-s3-php-class/S3','php');
            $s3 = new S3(core::config('image.aws_access_key'), core::config('image.aws_secret_key'));
        }

        if (core::post('icon_delete')  AND $location->delete_icon()==TRUE )
        {
            Alert::set(Alert::SUCCESS, __('Icon deleted.'));
            $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'update','id'=>$location->id_location)));
        }// end of icon delete

        if (
            ! Upload::valid($icon) OR
            ! Upload::not_empty($icon) OR
            ! Upload::type($icon, explode(',',core::config('image.allowed_formats'))) OR
            ! Upload::size($icon, core::config('image.max_image_size').'M'))
        {
        	if ( Upload::not_empty($icon) && ! Upload::type($icon, explode(',',core::config('image.allowed_formats'))))
            {
                Alert::set(Alert::ALERT, $icon['name'].' '.sprintf(__('Is not valid format, please use one of this formats "%s"'),core::config('image.allowed_formats')));
				$this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'update','id'=>$location->id_location)));
            }
            if ( ! Upload::size($icon, core::config('image.max_image_size').'M'))
            {
                Alert::set(Alert::ALERT, $icon['name'].' '.sprintf(__('Is not of valid size. Size is limited to %s MB per image'),core::config('image.max_image_size')));
				$this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'update','id'=>$location->id_location)));
            }
            Alert::set(Alert::ALERT, $icon['name'].' '.__('Image is not valid. Please try again.'));
            $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'update','id'=>$location->id_location)));
        }
        else
        {
            if ($icon != NULL) // sanity check
            {
                // saving/uploading img file to dir.
                $path = 'images/locations/';
                $root = DOCROOT.$path; //root folder
                $icon_name = $location->seoname.'.png';

                // if folder does not exist, try to make it
               	if ( ! file_exists($root) AND ! @mkdir($root, 0775, true)) { // mkdir not successful ?
                        Alert::set(Alert::ERROR, __('Image folder is missing and cannot be created with mkdir. Please correct to be able to upload images.'));
                        return; // exit function
                };

                // save file to root folder, file, name, dir
                if ($file = Upload::save($icon, $icon_name, $root))
                {
                    // put icon to Amazon S3
                    if (core::config('image.aws_s3_active'))
                        $s3->putObject($s3->inputFile($file), core::config('image.aws_s3_bucket'), $path.$icon_name, S3::ACL_PUBLIC_READ);

                    // update location info
                    $location->has_image = 1;
                    $location->last_modified = Date::unix2mysql();
                    $location->save();

                    Alert::set(Alert::SUCCESS, $icon['name'].' '.__('Icon is uploaded.'));
                }
                else
                    Alert::set(Alert::ERROR, $icon['name'].' '.__('Icon file could not been saved.'));

                $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'update','id'=>$location->id_location)));
            }

        }
	}

    /**
     * deletes all the locations
     * @return void
     */
    public function action_delete_all()
    {
        if(core::post('confirmation'))
        {
            //delete location icons
            $locations = new Model_Location();

            if ($id_location = intval(Core::post('id_location')) AND $id_location > 0)
            {
                $selected_location = new Model_Location($id_location);
                $locations->where('id_location', 'in', $selected_location->get_siblings_ids())
                    ->where('id_location','!=',$selected_location->id_location);
            }
            else
                $locations->where('id_location','!=','1')->find_all();

            $locations = $locations->find_all();

            foreach ($locations as $location)
                $location->delete_icon();

            $query_update = DB::update('ads');
            $query_delete = DB::delete('locations');

            if ($id_location = intval(Core::post('id_location')) AND $id_location > 0)
            {
                $query_update->set(array('id_location' => $selected_location->id_location));
                $query_delete->where('id_location', 'in', $selected_location->get_siblings_ids())
                    ->where('id_location','!=',$selected_location->id_location);
            }
            else
            {
                $query_update->set(array('id_location' => '1'));
                $query_delete->where('id_location','!=','1');
            }

            $query_update->execute();
            $query_delete->execute();

            //delete subscribtions
            DB::delete('subscribers')->where('id_location','!=','1')->execute();

            Model_Location::cache_delete();

            Alert::set(Alert::SUCCESS, __('All locations were deleted.'));

        }
        else {
            Alert::set(Alert::ERROR, __('You did not confirmed your delete action.'));
        }

        HTTP::redirect(Route::url('oc-panel',array('controller'=>'location', 'action'=>'index')));
    }

    /**
     * Updates translations
     * @return void
     */
    public function action_update_translations()
    {
        $location = new Model_Location($this->request->param('id'));

        if (Theme::get('premium') != 1)
        {
            Alert::set(Alert::INFO, __('Translations is only available in the PRO version!') . ' ' . __('Upgrade your Yclas site to activate this feature.'));
            $this->redirect(Route::url('oc-panel', array('controller' => 'location', 'action' => 'update', 'id' => $location->id_category)));
        }

        if ($this->request->post() AND $location->loaded())
        {
            $location->translations = json_encode($this->request->post('translations'));

            try {
                $location->save();
            } catch (Exception $e) {
                throw HTTP_Exception::factory(500, $e->getMessage());
            }
        }

        $this->redirect(Route::url('oc-panel', array('controller' => 'location', 'action' => 'update', 'id' => $location->id_location)));
    }
}
