<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Panel_Review extends Auth_CrudAjax {

    /**
     *
     * list of possible actions for the crud, you can modify it to allow access or deny, by default all
     * @var array
     */
    public $crud_actions = array('update');

    /**
     * @var $_index_fields ORM fields shown in index
     */
    protected $_index_fields = array('id_review','rate','id_ad','id_user','created','status');

    protected $_filter_fields = array(  'id_user'    => 'INPUT',
                                        'rate'       => array(1,2,3,4,5),
                                        'status'     => array(0,1),
                                        );

    protected $_fields_caption = array( 'id_user'       => array('model'=>'user','caption'=>'email'),
                                    'id_ad'     => array('model'=>'ad','caption'=>'title'),
                                     );

    /**
     * @var $_orm_model ORM model name
     */
    protected $_orm_model = 'review';
}