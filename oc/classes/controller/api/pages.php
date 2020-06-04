<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api_Pages extends Api_Auth {


    /**
     * Handle GET requests.
     */
    public function action_index()
    {
        try
        {
            if (is_numeric($this->request->param('id')))
            {
                $this->action_get();
            }
            else
            {
                $output = array();

                //Page CMS
                $pages =  new Model_Content();
                $pages = $pages->where('type','=','page')->where('status','=','1');

                //filter results by param, verify field exists and has a value
                $pages->api_filter($this->_filter_params);

                //how many? used in header X-Total-Count
                $count = $pages->count_all();

                //by default sort by published date
                if(empty($this->_sort))
                    $this->_sort['order'] = 'asc';

                //after counting sort values
                $pages->api_sort($this->_sort);

                $pages = $pages->cached()->find_all();

                //as array
                foreach ($pages as $f)
                    $output[] = $this->get_array($f);

                $this->rest_output(array('pages' => $output),200,$count);
            }
        }
        catch (Kohana_HTTP_Exception $khe)
        {
            $this->_error($khe);
        }
    }

    public function action_get()
    {
        try
        {
            if (is_numeric($id_content = $this->request->param('id')))
            {
                $page =  new Model_Content();
                $page = $page ->where('type','=','page')
                            ->where('status','=','1')
                            ->where('id_content','=',$id_content)
                            ->find();

                if ($page->loaded())     
                    $this->rest_output(array('page' => $this->get_array($page)));
                else
                    $this->_error(__('FAQ not found'),404);
            }
            else
                $this->_error(__('FAQ not found'),404);

        }
        catch (Kohana_HTTP_Exception $khe)
        {
            $this->_error($khe);
        }

    }


    public static function get_array($page)
    {
        //I do not want to return this fields...
        $hidden_fields =  array('from_email','type','status');

        $res = $page->as_array();
        $res['url'] = Route::url('page', array('seotitle'=>$page->seotitle));

        //remove the hidden fields
        foreach ($res as $key => $value) 
        {
            if(in_array($key,$hidden_fields))
                unset($res[$key]);
        }

        return $res;
    }

} // END