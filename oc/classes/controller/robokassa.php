<?php defined('SYSPATH') or die('No direct script access.');

/**
 * robokassa class
 *
 * @package Open Classifieds
 * @subpackage Core
 * @category Payment
 * @author Chema Garrido <chema@open-classifieds.com>
 * @license GPL v3
 */

class Controller_Robokassa extends Controller{
	

    public function action_result()
    { 
        $this->auto_render = FALSE;

        $id_order = Core::request('InvId');

        //retrieve info for the item in DB
        $order = new Model_Order();
        $order = $order->where('id_order', '=', $id_order)
                       ->where('status', '=', Model_Order::STATUS_CREATED)
                       ->limit(1)->find();

        if ($order->loaded())
        {
            //its a fraud...lets let him know
            if ( $order->is_fraud() === TRUE )
            {
                die(__('We had, issues with your transaction. Please try paying with another paymethod.'));
            }
           
            //correct payment?
            if( ($result = robokassa::check_result($order))!==FALSE ) 
            {
                //mark as paid
                $order->confirm_payment('robokassa',$result);
                die('OK');
            }
            else
            {
                // The card has been declined
                die( __('Please fill your card details.'));
            }        
        }
        else
        {
            die( __('Order could not be loaded'));
        }
    }


    public function action_success()
    { 
        $this->auto_render = FALSE;

        $id_order = Core::request('InvId');

        //retrieve info for the item in DB
        $order = new Model_Order();
        $order = $order->where('id_order', '=', $id_order)
                       ->where('status', '=', Model_Order::STATUS_PAID)
                       ->limit(1)->find();

        if ($order->loaded())
        {
            //correct payment?
            if( ($result = robokassa::check_result($order,'success'))!==FALSE ) 
            {
                $moderation = core::config('general.moderation');

                if ($moderation == Model_Ad::PAYMENT_MODERATION
                    AND $order->id_product == Model_Order::PRODUCT_CATEGORY)
                {
                    Alert::set(Alert::INFO, __('Advertisement is received, but first administrator needs to validate. Thank you for being patient!'));
                    $this->redirect(Route::url('default', ['action' => 'thanks', 'controller' => 'ad', 'id' => $order->id_ad]));
                }

                if ($moderation == Model_Ad::PAYMENT_ON
                    AND $order->id_product == Model_Order::PRODUCT_CATEGORY)

                {
                    $this->redirect(Route::url('default', ['action' => 'thanks', 'controller' => 'ad', 'id' => $order->id_ad]));
                }

                //redirect him to his ads
                Alert::set(Alert::SUCCESS, __('Thanks for your payment!'));
                $this->redirect(Route::url('oc-panel', array('controller'=>'profile','action'=>'orders')));
            }
            else
            {
                // The card has been declined
                Alert::set(Alert::INFO, __('Please fill your card details.'));
                $this->redirect(Route::url('default', array('controller'=>'ad','action'=>'checkout','id'=>$order->id_order)));
            }        
        }
        else
        {
            Alert::set(Alert::INFO, __('Order could not be loaded'));
            $this->redirect(Route::url('default'));
        }
    }


    public function action_fail()
    { 
        $this->auto_render = FALSE;

        $id_order = Core::request('InvId');

        //retrieve info for the item in DB
        $order = new Model_Order();
        $order = $order->where('id_order', '=', $id_order)
                       ->where('status', '=', Model_Order::STATUS_CREATED)
                       ->limit(1)->find();

        if ($order->loaded())
        {
            // The card has been declined
            Alert::set(Alert::INFO, __('Please fill your card details.'));
            $this->redirect(Route::url('default', array('controller'=>'ad','action'=>'checkout','id'=>$order->id_order)));
        }
        else
        {
            Alert::set(Alert::INFO, __('Order could not be loaded'));
            $this->redirect(Route::url('default'));
        }
    }


	
}
