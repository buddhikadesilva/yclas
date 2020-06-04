<?php defined('SYSPATH') or die('No direct script access.');

/**
 * zenith class
 *
 * @package Open Classifieds
 * @subpackage Core
 * @category Payment
 * @author Oliver <oliver@open-classifieds.com>
 * @license GPL v3
 */

class Controller_Zenith extends Controller{

    public function action_result()
    {
        $this->auto_render = FALSE;

        $id_order = Core::request('txnref');

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
                Alert::set(Alert::ERROR, __('We had, issues with your transaction. Please try paying with another paymethod.'));
                $this->redirect(Route::url('default', array('controller'=>'ad','action'=>'checkout','id'=>$order->id_order)));
            }

            //correct payment?
            if ( ($result = zenith::check_result($order)) !== FALSE AND $result['payment_status'] == 'Payment Status: successful')
            {
                //mark as paid
                $order->confirm_payment('zenith', $result['transaction_reference_number']);

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
                Alert::set(Alert::INFO, implode('<br>', $result));

                $this->redirect(Route::url('oc-panel', ['controller' => 'profile', 'action' => 'orders']));
            }
            else
            {
                Alert::set(Alert::INFO, __('Transaction not successful!'));
                Alert::set(Alert::WARNING, implode('<br>', $result));

                $this->redirect(Route::url('default', ['controller' => 'ad', 'action' => 'checkout', 'id' => $order->id_order]));
            }
        }
        else
        {
            Alert::set(Alert::INFO, __('Order could not be loaded'));
            $this->redirect(Route::url('default', array('controller'=>'ad','action'=>'checkout','id'=>$order->id_order)));
        }
    }

}
