<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Api_Profile extends Api_User {


    /**
     * Handle GET requests.
     */
    public function action_index()
    {
        $url = Route::url('api',array('controller'=>'user',
                                                'action'  => 'index',
                                                'id'=>$this->request->param('id')));

        $this->_error($url,404);
    }



    /**
     * Handle PUT requests.
     */
    public function action_update()
    {
        try
        {
            //set values of the user
            $this->user->values($this->_post_params);

            try
            {
                $this->user->save();
                $this->rest_output('User updated');
            }
            catch (ORM_Validation_Exception $e)
            {
                $errors = '';
                $e = $e->errors('user');

                foreach ($e as $f => $err) 
                    $errors.=$err.' - ';

                $this->_error($errors,400);
            }
        }
        catch (Kohana_HTTP_Exception $khe)
        {
            $this->_error($khe);
        }
       
    }

    public function action_picture()
    {
        //get image
        $image = $_FILES['profile_image']; //file post

        $result = $this->user->upload_image($image);

        if ($result === TRUE)
            $this->rest_output(TRUE);
        else 
            $this->_error($result);
    }


    /**
     * Delete picture action.
     * 
     * Optionally deletes a specific image by number using param 'num_image'.
     * If 'num_image' is not submitted, the primary image will be deleted instead.
     */
    public function action_picture_delete()
    {
        try
        {
            $num_image = null;

            if (isset($this->_post_params['num_image'])) {
                $num_image = $this->_post_params['num_image'];
            }

            if (!is_numeric($num_image)) {
                $num_image = 1;
            }

            if ($this->user->delete_image($num_image)==TRUE ) {
                $this->rest_output(TRUE);
            } else {
                $this->_error(FALSE);
            }
        }
        catch (Kohana_HTTP_Exception $khe)
        {
            $this->_error($khe);
        }
    }


} // END