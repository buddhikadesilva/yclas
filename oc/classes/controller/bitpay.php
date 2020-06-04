<?php defined('SYSPATH') or die('No direct script access.');

/**
* bitpay class
*
* @package Open Classifieds
* @subpackage Core
* @category Helper
* @author Chema <oliver@open-classifieds.com>
* @license GPL v3
*/

class Controller_Bitpay extends Controller
{

    public function action_ipn()
    {
        $this->auto_render = FALSE;

        $id_order = $this->request->param('id');

        require_once Kohana::find_file('vendor', 'bitpay/vendor/autoload', 'php');

        $raw_post_data = file_get_contents('php://input');

        if ($raw_post_data === FALSE) {
            Kohana::$log->add(Log::ERROR, 'Could not read from the php://input stream or invalid Bitpay IPN received.');
            return;
        }

        $ipn = json_decode($raw_post_data);

        if (empty($ipn) === TRUE) {
            Kohana::$log->add(Log::ERROR, 'Could not decode the JSON payload from BitPay.');
            return;
        }
        if (empty($ipn->id) === TRUE) {
            Kohana::$log->add(Log::ERROR, 'Invalid Bitpay payment notification message received - did not receive invoice ID.');
            return;
        }

        $client = new \Bitpay\Client\Client();
        $network = new \Bitpay\Network\Livenet();
        if (Core::config('payment.bitpay_sandbox') == 1)
            $network = new \Bitpay\Network\Testnet();

        $adapter = new \Bitpay\Client\Adapter\CurlAdapter();
        $client->setNetwork($network);
        $client->setAdapter($adapter);
        $token = new \Bitpay\Token();
        $token->setToken(Core::config('payment.bitpay_token')); // UPDATE THIS VALUE
        $client->setToken($token);

        $invoice = $client->getInvoice($ipn->id);
        $invoiceId = $invoice->getId();
        $invoiceStatus = $invoice->getStatus();
        $invoiceExceptionStatus = $invoice->getExceptionStatus();
        $invoicePrice = $invoice->getPrice();

        //retrieve info for the item in DB
        $order = new Model_Order();
        $order = $order->where('id_order', '=', $id_order)
                        ->where('status', '=', Model_Order::STATUS_CREATED)
                        ->limit(1)->find();

        if ($order->loaded()) {
            switch ($invoiceStatus) {
                case 'paid':
                    break;
                case 'confirmed':
                    Kohana::$log->add(Log::DEBUG, 'BitPay bitcoin payment confirmed. Awaiting network confirmation and completed status.');
                    // no break
                case 'complete':
                    //mark as paid
                    $order->confirm_payment('bitpay', $ipn->id);
                    $this->response->body('OK');
                    break;
                case 'invalid':
                    Kohana::$log->add(Log::ERROR, 'Bitcoin payment is invalid for this order! The payment was not confirmed by the network within 1 hour.');
                    break;
            }
        }

        $this->response->body('KO');
    }

    public function action_invoice() {
        require_once Kohana::find_file('vendor', 'bitpay/vendor/autoload', 'php');

        $id_order = $this->request->param('id');

        $order = new Model_Order();
        $order = $order->where('id_order', '=', $id_order)
            ->where('status', '=', Model_Order::STATUS_CREATED)
            ->limit(1)->find();

        if (! $order->loaded())
            HTTP::redirect(Route::url('default'));

        $client = new \Bitpay\Client\Client();
        $network = new \Bitpay\Network\Livenet();
        if (Core::config('payment.bitpay_sandbox') == 1)
            $network = new \Bitpay\Network\Testnet();

        $adapter = new \Bitpay\Client\Adapter\CurlAdapter();
        $client->setNetwork($network);
        $client->setAdapter($adapter);
        $token = new \Bitpay\Token();
        $token->setToken(Core::config('payment.bitpay_token')); // UPDATE THIS VALUE
        $client->setToken($token);

        $invoice = $client->getInvoice($order->txn_id);
        $request = $client->getRequest();
        $response = $client->getResponse();

        switch ($invoice->getStatus()) {
            case 'paid':
                break;
            case 'confirmed':
                Kohana::$log->add(Log::DEBUG, 'BitPay bitcoin payment confirmed. Awaiting network confirmation and completed status.');
            case 'complete':
                        //mark as paid
                $order->confirm_payment('bitpay', $order->txn_id);
                $this->response->body('OK');
                break;
            case 'invalid':
                Kohana::$log->add(Log::ERROR, 'Bitcoin payment is invalid for this order! The payment was not confirmed by the network within 1 hour.');
                break;
        }

        HTTP::redirect(Route::url('oc-panel', array('controller' => 'profile', 'action' => 'orders')));
    }
}
