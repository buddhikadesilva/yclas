<?php defined('SYSPATH') or die('No direct script access.');

/**
* serfinsa class
*
* @package Open Classifieds
* @subpackage Core
* @category Payment
* @author Oliver <oliver@open-classifieds.com>
* @license GPL v3
*/

class Controller_Serfinsa extends Controller{

    protected $base_url;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);

        if (Core::config('payment.serfinsa_sandbox'))
        {
            $this->base_url = 'https://test.serfinsacheckout.com:8080/';
        }
        else
        {
            $this->base_url = 'https://www.serfinsacheckout.com/';
        }
    }

	public function action_pay()
	{
		$this->auto_render = FALSE;
        $this->template = View::factory('js');
        $this->response->headers('Content-Type', 'application/json');

		$order = (new Model_Order())
            ->where('id_order','=',$this->request->param('id'))
            ->where('status','=',Model_Order::STATUS_CREATED)
            ->limit(1)->find();

        if (! $order->loaded())
        {
            Alert::set(Alert::INFO, __('Order could not be loaded'));
            $this->redirect(Route::url('default'));
        }

        $request = Request::factory($this->base_url . 'api/PayApi/TokeyTran')
            ->headers('Content-Type', 'application/json')
            ->method(Request::POST)
            ->body(json_encode([
                    'TokeyComercio' => Core::config('payment.serfinsa_token'),
                    'Monto' => $order->amount,
                    'IdTransaccionCliente' => $order->id_order,
                ]
            ));

        $request->client()->options([
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
        ]);

        $response = $request->execute();

        $transaction = json_decode($response->body());

        if($transaction->Satisfactorio)
        {
            $transaction_id  = $transaction->Datos->Id;
            $path = $this->base_url . "Pay/GateWay?token=". Core::config('payment.serfinsa_token') . "&idTransaccion=$transaction_id";

            $this->response->status('200');

            $this->template->content = json_encode([
                'status' => 1,
                'path' => $path
            ]);

            return;
        }

        $this->response->status('403');

        $this->template->content = json_encode([
            'status' => 2,
            'path' => ''
        ]);

        return;
	}

    public function action_result()
    {
        $this->auto_render = FALSE;
        $this->template = View::factory('js');
        $this->response->headers('Content-Type', 'text/html');

        $request = json_decode(file_get_contents('php://input'));

        if(! isset($request)) {
            $this->template->content = '';
            $this->response->status('403');

            return;
        }

        $id_order = $request->ClientIdTransaction;

        //retrieve info for the item in DB
        $order = new Model_Order();
        $order = $order->where('id_order', '=', $id_order)
                       ->where('status', '=', Model_Order::STATUS_CREATED)
                       ->limit(1)->find();

       if(! $order->loaded())
       {
            Alert::set(Alert::INFO, __('Order could not be loaded'));
            $this->redirect(Route::url('default', array('controller'=>'ad','action'=>'checkout','id'=>$order->id_order)));
       }

       if ($order->is_fraud() === TRUE )
        {
            Alert::set(Alert::ERROR, __('We had, issues with your transaction. Please try paying with another paymethod.'));
            $this->redirect(Route::url('default', array('controller'=>'ad','action'=>'checkout','id'=>$order->id_order)));
        }

        //correct payment?
        if (! $request->SatisFactorio)
        {
            Alert::set(Alert::INFO, __('Transaction not successful!'));
            $this->redirect(Route::url('default', array('controller'=>'ad','action'=>'checkout','id'=>$order->id_order)));
        }

        //mark as paid
        $order->confirm_payment('serfinsa', $request->NumeroAutorizacion);

        $this->template->content = 'Webhook Handled';
        $this->response->status('200');

        return;
    }
}
