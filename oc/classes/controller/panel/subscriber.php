<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Panel_Subscriber extends Auth_CrudAjax {

    /**
     * @var $_orm_model ORM model name
     */
    protected $_orm_model = 'subscribe';

    /**
     *
     * list of possible actions for the crud, you can modify it to allow access or deny, by default all
     * @var array
     */
    //public $crud_actions = array('delete');

    protected $_fields_caption = array( 'id_user'       => array('model'=>'user','caption'=>'email'),
                                        'id_category'       => array('model'=>'category','caption'=>'name'),
                                        'id_location'       => array('model'=>'location','caption'=>'name'),
                                         );

    function __construct(Request $request, Response $response)
    {

        $this->_filter_fields = array(  'id_user'    => 'INPUT', 
                                        'created'    => 'DATE', 
                                        'id_category'    => array('type'=>'SELECT','table'=>'categories','key'=>'id_category','value'=>'seoname'),
                                        'id_location'    => array('type'=>'SELECT','table'=>'locations','key'=>'id_location','value'=>'seoname'),
                                        );
        
        parent::__construct($request, $response);


    } 

}