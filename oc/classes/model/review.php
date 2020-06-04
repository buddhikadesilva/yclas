<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Product reviews
 *
 * @author      Chema <chema@open-classifieds.com>
 * @package     Core
 * @copyright   (c) 2009-2014 Open Classifieds Team
 * @license     GPL v3
 */

class Model_Review extends ORM {

    /**
     * status constants
     */
    const STATUS_NOACTIVE = 0; 
    const STATUS_ACTIVE   = 1; 

    const RATE_MAX   = 5; 
    
    /**
     * @var  string  Table name
     */
    protected $_table_name = 'reviews';

    /**
     * @var  string  PrimaryKey field name
     */
    protected $_primary_key = 'id_review';

    /**
     * @var  array  ORM Dependency/hirerachy
     */
    protected $_belongs_to = array(
        'ad' => array(
                'model'       => 'ad',
                'foreign_key' => 'id_ad',
            ),
        'user' => array(
                'model'       => 'user',
                'foreign_key' => 'id_user',
            ),
    );



    public function exclude_fields()
    {
        return array('created');
    }

    /**
     * 
     * formmanager definitions
     * 
     */
    public function form_setup($form)
    {   

        $form->fields['id_ad']['display_as']      = 'text'; 
        $form->fields['id_user']['display_as']    = 'text';
        $form->fields['description']['display_as']    = 'textarea';
    }

    /**
     * returns the ad rate from all the reviews
     * @param  Model_Ad $ad [description]
     * @return [type]                 [description]
     */
    public static function get_ad_rate(Model_Ad $ad)
    {
        $query = DB::select(DB::expr('AVG(rate) rate'))
                        ->from('reviews')
                        ->where('id_ad','=',$ad->id_ad)
                        ->where('status','=',Model_Review::STATUS_ACTIVE)
                        ->group_by('id_ad')
                        ->execute();

        $rates = $query->as_array();

        return (isset($rates[0]))?round($rates[0]['rate'],2):FALSE;

    }

    /**
     * returns amount of rates of an ad
     * @param  Model_Ad $ad [description]
     * @return [type]                 [description]
     */
    public static function get_ad_count_rates(Model_Ad $ad)
    {
        $query = DB::select(DB::expr('COUNT(rate) rate'))
                        ->from('reviews')
                        ->where('id_ad','=',$ad->id_ad)
                        ->where('status','=',Model_Review::STATUS_ACTIVE)
                        ->group_by('id_ad')
                        ->execute();

        $rates = $query->as_array();

        return empty($rates) ? 0 : $rates[0]['rate'];

    }

    /**
     * returns the user rate from all the reviews
     * @param  Model_User $user [description]
     * @return [type]                 [description]
     */
    public static function get_user_rate(Model_User $user)
    {
        $db_prefix  = Database::instance('default')->table_prefix();

        $query = DB::select(DB::expr('AVG('.$db_prefix.'reviews.rate) rates'))
                            ->from('reviews')
                            ->join('ads','RIGHT')
                        ->using('id_ad')
                            ->where('ads.id_user','=',$user->id_user)
                            ->where('reviews.status','=',Model_Review::STATUS_ACTIVE)
                        ->execute();

        $rates = $query->as_array();

        return (isset($rates[0]))?round($rates[0]['rates'],2):FALSE;
    }

    /**
     * returns the user reviews that have received
     * @param  Model_User $user [description]
     * @return [type]                 [description]
     */
    public static function get_user_reviews(Model_User $user)
    {
        $reviews = new Model_Review();
        $reviews = $reviews->join('ads','RIGHT')
                        ->using('id_ad')
                        ->where('ads.id_user','=',$user->id_user)
                        ->where('review.status','=',Model_Review::STATUS_ACTIVE)
                    ->cached()->find_all();

        return $reviews;
    }

    protected $_table_columns =  
array (
  'id_review' => 
  array (
    'type' => 'int',
    'min' => '0',
    'max' => '4294967295',
    'column_name' => 'id_review',
    'column_default' => NULL,
    'data_type' => 'int unsigned',
    'is_nullable' => false,
    'ordinal_position' => 1,
    'display' => '10',
    'comment' => '',
    'extra' => 'auto_increment',
    'key' => 'PRI',
    'privileges' => 'select,insert,update,references',
  ),
  'id_user' => 
  array (
    'type' => 'int',
    'min' => '0',
    'max' => '4294967295',
    'column_name' => 'id_user',
    'column_default' => NULL,
    'data_type' => 'int unsigned',
    'is_nullable' => false,
    'ordinal_position' => 2,
    'display' => '10',
    'comment' => '',
    'extra' => '',
    'key' => 'MUL',
    'privileges' => 'select,insert,update,references',
  ),
  'id_ad' => 
  array (
    'type' => 'int',
    'min' => '0',
    'max' => '4294967295',
    'column_name' => 'id_ad',
    'column_default' => NULL,
    'data_type' => 'int unsigned',
    'is_nullable' => false,
    'ordinal_position' => 3,
    'display' => '10',
    'comment' => '',
    'extra' => '',
    'key' => 'MUL',
    'privileges' => 'select,insert,update,references',
  ),
  'rate' => 
  array (
    'type' => 'int',
    'min' => '0',
    'max' => '4294967295',
    'column_name' => 'rate',
    'column_default' => '0',
    'data_type' => 'int unsigned',
    'is_nullable' => false,
    'ordinal_position' => 4,
    'display' => '2',
    'comment' => '',
    'extra' => '',
    'key' => '',
    'privileges' => 'select,insert,update,references',
  ),
  'description' => 
  array (
    'type' => 'string',
    'column_name' => 'description',
    'column_default' => NULL,
    'data_type' => 'varchar',
    'is_nullable' => false,
    'ordinal_position' => 5,
    'character_maximum_length' => '1000',
    'collation_name' => 'utf8_general_ci',
    'comment' => '',
    'extra' => '',
    'key' => '',
    'privileges' => 'select,insert,update,references',
  ),
  'created' => 
  array (
    'type' => 'string',
    'column_name' => 'created',
    'column_default' => 'CURRENT_TIMESTAMP',
    'data_type' => 'timestamp',
    'is_nullable' => false,
    'ordinal_position' => 6,
    'comment' => '',
    'extra' => '',
    'key' => '',
    'privileges' => 'select,insert,update,references',
  ),
  'ip_address' => 
  array (
    'type' => 'int',
    'min' => '-9223372036854775808',
    'max' => '9223372036854775807',
    'column_name' => 'ip_address',
    'column_default' => NULL,
    'data_type' => 'bigint',
    'is_nullable' => true,
    'ordinal_position' => 7,
    'display' => '20',
    'comment' => '',
    'extra' => '',
    'key' => '',
    'privileges' => 'select,insert,update,references',
  ),
  'status' => 
  array (
    'type' => 'int',
    'min' => '-128',
    'max' => '127',
    'column_name' => 'status',
    'column_default' => '0',
    'data_type' => 'tinyint',
    'is_nullable' => false,
    'ordinal_position' => 8,
    'display' => '1',
    'comment' => '',
    'extra' => '',
    'key' => '',
    'privileges' => 'select,insert,update,references',
  ),
);
}