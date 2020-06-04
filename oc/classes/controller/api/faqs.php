<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api_Faqs extends Api_Auth {


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

                //FAQ CMS
                $faqs =  new Model_Content();
                $faqs = $faqs->where('type','=','help')->where('status','=','1');

                //filter results by param, verify field exists and has a value
                $faqs->api_filter($this->_filter_params);

                //how many? used in header X-Total-Count
                $count = $faqs->count_all();

                //by default sort by published date
                if(empty($this->_sort))
                    $this->_sort['order'] = 'asc';

                //after counting sort values
                $faqs->api_sort($this->_sort);

                $faqs = $faqs->cached()->find_all();

                //as array
                foreach ($faqs as $f)
                    $output[] = $this->get_array($f);

                $this->rest_output(array('faqs' => $output),200,$count);
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
                $faq =  new Model_Content();
                $faq = $faq ->where('type','=','help')
                            ->where('status','=','1')
                            ->where('id_content','=',$id_content)
                            ->find();

                if ($faq->loaded())     
                    $this->rest_output(array('faq' => $this->get_array($faq)));
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


    public static function get_array($faq)
    {
        //I do not want to return this fields...
        $hidden_fields =  array('from_email','type','status');

        $res = $faq->as_array();
        $res['url'] = Route::url('faq', array('seotitle'=>$faq->seotitle));

        //remove the hidden fields
        foreach ($res as $key => $value) 
        {
            if(in_array($key,$hidden_fields))
                unset($res[$key]);
        }

        return $res;
    }

} // END