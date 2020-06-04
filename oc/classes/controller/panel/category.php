<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Panel_Category extends Auth_Crud {

	/**
	* @var $_index_fields ORM fields shown in index
	*/
	protected $_index_fields = array('name','order','price', 'id_category', 'id_category_parent');

	/**
	 * @var $_orm_model ORM model name
	 */
	protected $_orm_model = 'category';


    /**
     * overwrites the default crud index
     * @param  string $view nothing since we don't use it
     * @return void
     */
    public function action_index($view = NULL)
    {
        //HTTP::redirect(Route::url('oc-panel',array('controller'  => 'category','action'=>'dashboard')));
        //template header
        $this->template->title  = __('Categories');

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Categories')));
        $this->template->styles  = array('css/sortable.css' => 'screen',
                                         '//cdn.jsdelivr.net/bootstrap.tagsinput/0.3.9/bootstrap-tagsinput.css' => 'screen');

        $this->template->scripts['footer'][] = 'js/jquery-sortable-min.js';
        $this->template->scripts['footer'][] = 'js/oc-panel/categories.js';
        $this->template->scripts['footer'][] = '//cdn.jsdelivr.net/bootstrap.tagsinput/0.3.9/bootstrap-tagsinput.min.js';

        $cats  = Model_Category::get_as_array();
        $order = Model_Category::get_multidimensional();
        $hide_homepage_categories = json_decode(core::config('general.hide_homepage_categories'), TRUE);

        $this->template->content = View::factory('oc-panel/pages/categories/index',array('cats' => $cats,'order'=>$order,'hide_homepage_categories'=>$hide_homepage_categories));
    }

    /**
     * CRUD controller: CREATE
     */
    public function action_create()
    {

        $this->template->title = __('New').' '.__($this->_orm_model);
        $this->template->styles = array('css/fontawesome-iconpicker.min.css' => 'screen');
        $this->template->scripts['footer'][] = 'js/oc-panel/fontawesome-iconpicker.min.js';
        $this->template->styles = array('css/fontawesome-iconpicker.min.css' => 'screen');
        $this->template->scripts['footer'][] = 'js/oc-panel/fontawesome-iconpicker.min.js';
        $this->template->scripts['footer'][] = 'js/oc-panel/category_new.js';

        $form = new FormOrm($this->_orm_model);

        if ($this->request->post())
        {
            if ( $success = $form->submit() )
            {
                //category is different than himself, cant be his ow father!!!
                if ($form->object->id_category == $form->object->id_category_parent)
                {
                    Alert::set(Alert::INFO, __('You can not set as parent the same category'));
                    $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'create')));
                }

                //check if the parent is loaded/exists avoiding errors
                $parent_cat = new Model_Category($form->object->id_category_parent);
                if (!$parent_cat->loaded())
                {
                    Alert::set(Alert::INFO, __('You are assigning a parent category that does not exist'));
                    $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'create')));
                }

                $form->object->description = Kohana::$_POST_ORIG['formorm']['description'];

                try {
                    $form->object->save();
                } catch (Exception $e) {
                    throw HTTP_Exception::factory(500,$e->getMessage());
                }

                $this->action_deep();
                Model_Category::cache_delete();

                Alert::set(Alert::SUCCESS, __('Category created'));

                $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller())));
            }
            else
            {
                Alert::set(Alert::ERROR, __('Check form for errors'));
            }
        }

        return $this->render('oc-panel/pages/categories/create', array('form' => $form));
    }

    /**
     * CRUD controller: UPDATE
     */
    public function action_update()
    {
        $this->template->title = __('Update').' '.__($this->_orm_model).' '.$this->request->param('id');
        $this->template->styles = array('css/sortable.css' => 'screen');
        $this->template->styles = array('css/fontawesome-iconpicker.min.css' => 'screen');
        $this->template->scripts['footer'][] = 'js/oc-panel/fontawesome-iconpicker.min.js';
        $this->template->scripts['footer'][] = 'js/oc-panel/category_edit.js';

        $form = new FormOrm($this->_orm_model,$this->request->param('id'));
        $category = new Model_Category($this->request->param('id'));

        $fields = Model_Field::get_all();
        $category_fields = array();
        $selectable_fields = array();

        // get selectable fields
        foreach ($fields as $field => $values)
        {
            if ( ! (is_array($values['categories']) AND in_array($category->id_category,$values['categories'])))
                $selectable_fields[$field] = $values;

            else
                $category_fields[$field] = $values;
        }

        if ($this->request->post())
        {
            if ( $success = $form->submit() )
            {
                //category is different than himself, cant be his own father!!!
                if ($form->object->id_category == $form->object->id_category_parent)
                {
                    Alert::set(Alert::INFO, __('You can not set as parent the same category'));
                    $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'update','id'=>$form->object->id_category)));
                }

                //check if the parent is loaded/exists avoiding errors
                $parent_cat = new Model_Category($form->object->id_category_parent);
                if (!$parent_cat->loaded())
                {
                    Alert::set(Alert::INFO, __('You are assigning a parent category that does not exist'));
                    $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'update','id'=>$form->object->id_category)));
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
                if($category->has_image AND ($category->seoname != $form->object->seoname))
                    $category->rename_icon($form->object->seoname);

                Model_Category::cache_delete();

                Alert::set(Alert::SUCCESS, __('Item updated'));
                $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller())));
            }
            else
            {
                Alert::set(Alert::ERROR, __('Check form for errors'));
            }
        }

        return $this->render('oc-panel/pages/categories/update', compact('form', 'category', 'category_fields', 'selectable_fields'));
    }


    /**
     * saves the category in a specific order and change the parent
     * @return void
     */
    public function action_saveorder()
    {
        $this->auto_render = FALSE;
        $this->template = View::factory('js');

        $cat = new Model_Category(core::get('id_category'));

        //check if the parent is loaded/exists avoiding errors
        $parent_cat = new Model_Category(core::get('id_category_parent'));

        if ($cat->loaded() AND $parent_cat->loaded())
        {
            //saves the current category
            $cat->id_category_parent = $parent_cat->id_category;
            $cat->parent_deep        = core::get('deep');

            //saves the categories in the same parent the new orders
            $order = 0;
            foreach (core::get('brothers') as $id_cat)
            {
                $id_cat = substr($id_cat,3);//removing the li_ to get the integer

                //not the main category so loading and saving
                if ($id_cat!=core::get('id_category'))
                {
                    $c = new Model_Category($id_cat);
                    $c->order = $order;

                    try {
                        $c->save();
                    } catch (Exception $e) {
                        Kohana::$log->add(Log::ERROR, 'Controller Category saveorder id_category: '.$c->id_category.'- URL:'.URL::current());
                    }
                }
                else
                {
                    //saves the main category
                    $cat->order  = $order;
                    $cat->save();

                    try {
                        $cat->save();
                    } catch (Exception $e) {
                        Kohana::$log->add(Log::ERROR, 'Controller Category saveorder id_category: '.$cat->id_category.'- URL:'.URL::current());
                    }
                }
                $order++;
            }

            //recalculating the deep of all the categories
            $this->action_deep();
            Model_Category::cache_delete();
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

        $categories = array();

        if ($id_category = $this->request->param('id'))
            $categories[] = $id_category;
        elseif (core::post('categories'))
            $categories = core::post('categories');

        if (core::count($categories) > 0)
        {
            foreach ($categories as $id_category)
            {
                $category = new Model_Category($id_category);

                //update the elements related to that ad
                if ($category->loaded())
                {
                    //check if the parent is loaded/exists avoiding errors, if doesnt exist to the root
                    $parent_cat = new Model_Category($category->id_category_parent);
                    if ($parent_cat->loaded())
                        $id_category_parent = $category->id_category_parent;
                    else
                        $id_category_parent = 1;


                    //update all the siblings this category has and set the category parent
                    $query = DB::update('categories')
                                ->set(array('id_category_parent' => $id_category_parent))
                                ->where('id_category_parent','=',$category->id_category)
                                ->execute();

                    //update all the ads this category has and set the category parent
                    $query = DB::update('ads')
                                ->set(array('id_category' => $id_category_parent))
                                ->where('id_category','=',$category->id_category)
                                ->execute();

                    try
                    {
                        $category_name = $category->name;
                        $category->delete();
                        $this->template->content = 'OK';

                        //recalculating the deep of all the categories
                        $this->action_deep();
                        Model_Category::cache_delete();
                        Alert::set(Alert::SUCCESS, sprintf(__('Category %s deleted'), $category_name));

                    }
                    catch (Exception $e)
                    {
                         Alert::set(Alert::ERROR, $e->getMessage());
                    }
                }
                else
                     Alert::set(Alert::ERROR, __('Category not deleted'));
            }
        }

        HTTP::redirect(Route::url('oc-panel',array('controller'  => 'category','action'=>'index')));

    }

    /**
     * Creates multiple categories just with name
     * @return void
     */
    public function action_multy_categories()
    {
        $this->auto_render = FALSE;

        //update the elements related to that ad
        if ($_POST)
        {
            // d($_POST);
            if(core::post('multy_categories') !== "")
            {
                $multy_cats = explode(',', core::post('multy_categories'));
                $obj_category = new Model_Category();
                $categories_array = array();

                if (is_array($multy_cats))
                {
                    $execute = FALSE;
                    $insert = DB::insert('categories', array('name', 'seoname', 'id_category_parent'));
                    foreach ($multy_cats as $name)
                    {
                        if ( ! empty($name) AND ! in_array($seoname = $obj_category->gen_seoname($name), $categories_array))
                        {
                            $execute = TRUE;
                            $insert = $insert->values(array($name, $seoname, 1));

                            $categories_array[] = $seoname;
                        }
                    }

                    // Insert everything with one query.
                    if ($execute==TRUE)
                    {
                        $insert->execute();
                        Model_Category::cache_delete();
                    }
                }
            }
            else
                Alert::set(Alert::INFO, __('Select some categories first.'));
        }

        HTTP::redirect(Route::url('oc-panel',array('controller'  => 'category','action'=>'index')));
    }

    /**
     * recalculating the deep of all the categories
     * @return [type] [description]
     */
    public function action_deep()
    {
        Model_Category::cache_delete();

        //getting all the cats as array
        $cats_arr = Model_Category::get_as_array();

        $cats = new Model_Category();
        $cats = $cats->order_by('order','asc')->find_all()->cached()->as_array('id_category');
        foreach ($cats as $cat)
        {
            $deep = 0;

            //getin the parent of this category
            $id_category_parent = $cats_arr[$cat->id_category]['id_category_parent'];

            //counting till we find the begining
            while ($id_category_parent != 1 AND $id_category_parent != 0 AND $deep<10)
            {
                $id_category_parent = $cats_arr[$id_category_parent]['id_category_parent'];
                $deep++;
            }

            //saving the category only if different deep
            if ($cat->parent_deep != $deep)
            {
                $cat->parent_deep = $deep;
                $cat->save();
            }

        }

        //Alert::set(Alert::INFO, __('Success'));
        //HTTP::redirect(Route::url('oc-panel',array('controller'  => 'location','action'=>'index')));
    }

	public function action_icon()
	{
		//get icon
        if (isset($_FILES['category_icon']))
            $icon = $_FILES['category_icon']; //file post
        else
            $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'index')));

		$category = new Model_Category($this->request->param('id'));

		if (core::config('image.aws_s3_active'))
        {
            require_once Kohana::find_file('vendor', 'amazon-s3-php-class/S3','php');
            $s3 = new S3(core::config('image.aws_access_key'), core::config('image.aws_secret_key'));
        }

		if (core::post('icon_delete') AND $category->delete_icon()==TRUE)
		{
            Alert::set(Alert::SUCCESS, __('Icon deleted.'));
            $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'update','id'=>$category->id_category)));

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
				$this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'update','id'=>$category->id_category)));
            }
            if( ! Upload::size($icon, core::config('image.max_image_size').'M'))
            {
                Alert::set(Alert::ALERT, $icon['name'].' '.sprintf(__('Is not of valid size. Size is limited to %s MB per image'),core::config('image.max_image_size')));
            $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'update','id'=>$category->id_category)));
            }
            Alert::set(Alert::ALERT, $icon['name'].' '.__('Image is not valid. Please try again.'));
            $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'update','id'=>$category->id_category)));
        }
        else
        {
            if ($icon != NULL) // sanity check
            {
                // saving/uploading img file to dir.
                $path = 'images/categories/';
                $root = DOCROOT.$path; //root folder
                $icon_name = $category->seoname.'.png';

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

                    // update category info
                    $category->has_image = 1;
                    $category->last_modified = Date::unix2mysql();
                    $category->save();

                    Alert::set(Alert::SUCCESS, $icon['name'].' '.__('Icon is uploaded.'));
                }
                else
                    Alert::set(Alert::ERROR, $icon['name'].' '.__('Icon file could not been saved.'));

                $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'update','id'=>$category->id_category)));
            }

        }
    }

    /**
    * deletes all the categories
    * @return void
    */
    public function action_delete_all()
    {
        if(core::post('confirmation'))
        {
            //delete categories icons
            $categories = new Model_Category();
            $categories = $categories->where('id_category','!=','1')->find_all();

            foreach ($categories as $category)
                $category->delete_icon();

            //set home category to all the ads
            $query = DB::update('ads')
                        ->set(array('id_category' => '1'))
                        ->execute();

            //delete all categories
            $query = DB::delete('categories')
                        ->where('id_category','!=','1')
                        ->execute();

            //delete subscribtions
            DB::delete('subscribers')->where('id_category', '!=','1')->execute();

            Model_Category::cache_delete();

            Alert::set(Alert::SUCCESS, __('All categories were deleted.'));

        }
        else {
            Alert::set(Alert::ERROR, __('You did not confirmed your delete action.'));
        }

        HTTP::redirect(Route::url('oc-panel',array('controller'=>'category', 'action'=>'index')));
    }

    /**
     * Updates general.hide_homepage_categories config
     * @return void
     */
    public function action_hide_homepage_categories()
    {
        if ($hide_homepage_categories = $this->request->post('hide_homepage_categories')
            AND is_array($hide_homepage_categories))
        {
            $hide_homepage_categories = json_encode($hide_homepage_categories);
            Model_Config::set_value('general', 'hide_homepage_categories', $hide_homepage_categories);

            Alert::set(Alert::SUCCESS, __('Updated hidden categories from homepage'));
        }

        HTTP::redirect(Route::url('oc-panel',array('controller'=>'category', 'action'=>'index')));
    }

    /**
     * Updates translations
     * @return void
     */
    public function action_update_translations()
    {
        $category = new Model_Category($this->request->param('id'));

        if (Theme::get('premium') != 1)
        {
            Alert::set(Alert::INFO, __('Translations is only available in the PRO version!') . ' ' . __('Upgrade your Yclas site to activate this feature.'));
            $this->redirect(Route::url('oc-panel', array('controller' => 'category', 'action' => 'update', 'id' => $category->id_category)));
        }

        if ($this->request->post() AND $category->loaded())
        {
            $category->translations = json_encode($this->request->post('translations'));

            try {
                $category->save();
            } catch (Exception $e) {
                throw HTTP_Exception::factory(500, $e->getMessage());
            }
        }

        $this->redirect(Route::url('oc-panel', array('controller' => 'category', 'action' => 'update', 'id' => $category->id_category)));
    }

}
