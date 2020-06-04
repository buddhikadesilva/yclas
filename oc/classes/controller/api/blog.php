<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api_Blog extends Api_Auth {


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

                $posts = new Model_Post();
                $posts->where('status','=', Model_Post::STATUS_ACTIVE)->where('id_forum','IS',NULL);

                if (isset($this->_params['q']) AND strlen($this->_params['q']))
                {
                    $posts->where_open()
                         ->where('title','like','%'.$this->_params['q'].'%')->or_where('description','like','%'.$this->_params['q'].'%')
                         ->where_close();
                }

                //filter results by param, verify field exists and has a value
                $posts->api_filter($this->_filter_params);

                //how many? used in header X-Total-Count
                $count = $posts->count_all();

                //by default sort by published date
                if(empty($this->_sort))
                    $this->_sort['created'] = 'desc';

                //after counting sort values
                $posts->api_sort($this->_sort);

                //pagination with headers
                $pagination = $posts->api_pagination($count,$this->_params['items_per_page']);

                $posts = $posts->cached()->find_all();

                //as array
                foreach ($posts as $post)
                    $output[] = $this->get_array($post);

                $this->rest_output(array('posts' => $output),200,$count,($pagination!==FALSE)?$pagination:NULL);
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
            if (is_numeric($id_post = $this->request->param('id')))
            {
                $post = new Model_Post();
                $post->where('id_post','=',$id_post)
                        ->where('status','=', Model_Post::STATUS_ACTIVE)
                        ->where('id_forum','IS',NULL)
                        ->find();

                if ($post->loaded())     
                    $this->rest_output(array('post' => $this->get_array($post)));
                else
                    $this->_error(__('Blog post not found'),404);
            }
            else
                $this->_error(__('Blog post not found'),404);

        }
        catch (Kohana_HTTP_Exception $khe)
        {
            $this->_error($khe);
        }

    }


    public static function get_array($post)
    {
        //I do not want to return this fields...
        $hidden_fields =  array('id_forum','id_post_parent','ip_address','status');

        $res = $post->as_array();
        $res['url'] = Route::url('blog', array('seotitle'=>$post->seotitle));

        //remove the hidden fields
        foreach ($res as $key => $value) 
        {
            if(in_array($key,$hidden_fields))
                unset($res[$key]);
        }

        return $res;
    }

} // END