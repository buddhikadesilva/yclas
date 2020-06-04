<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Payfast class
 *
 * @package Open Classifieds
 * @subpackage Core
 * @category Payment
 * @author Oliver <oliver@open-classifieds.com>
 * @license GPL v3
 */

class Controller_Payfast extends Controller{
	
    public function action_itn()
    { 
        $this->auto_render = FALSE;

        $id_order = Core::request('m_payment_id');

        //retrieve info for the item in DB
        $order = new Model_Order();
        $order = $order->where('id_order', '=', $id_order)
                       ->where('status', '=', Model_Order::STATUS_CREATED)
                       ->limit(1)->find();

        if ($order->loaded())
        {
            //its a fraud...lets let him know
            if ($order->is_fraud() === TRUE)
            {
                Alert::set(Alert::ERROR, __('We had, issues with your transaction. Please try paying with another paymethod.'));
                $this->redirect(Route::url('default', array('controller'=>'ad','action'=>'checkout','id'=>$order->id_order)));
            }

            //same amount
            if (Core::request('amount_gross') == number_format($order->amount, 2, '.', ''))
            {
                //correct payment?
                if (payfast::validate_itn()) 
                {
                    //mark as paid
                    $order->confirm_payment('payfast',Core::request('signature'));
                }
                else
                {
                    Kohana::$log->add(Log::ERROR, 'A payment has been made but is flagged as INVALID');
                    $this->response->body('KO');
                }
            }
            else //trying to cheat....
            {
                Kohana::$log->add(Log::ERROR, 'Attempt illegal actions with transaction');
                $this->response->body('KO');
            }
        }
        else
        {
            Kohana::$log->add(Log::ERROR, 'Order not loaded');
            $this->response->body('KO');
        }
    }

}
