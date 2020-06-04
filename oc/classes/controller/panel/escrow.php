<?php

class Controller_Panel_Escrow extends Auth_Frontcontroller{

    protected $base_url;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);

        if (Core::config('payment.escrow_sandbox'))
        {
            $this->base_url = 'https://api.escrow-sandbox.com/';
        }
        else
        {
            $this->base_url = 'https://api.escrow.com/';
        }
    }

    public function action_update_api_key()
    {
        $user = Auth::instance()->get_user();

        if($this->request->post())
        {
            $user->escrow_email = core::post('escrow_email');
            $user->escrow_api_key = core::post('escrow_api_key');

            $request = Request::factory($this->base_url . '2017-09-01/customer/me')
                ->headers('Content-Type', 'application/json');

            $request->client()
                ->options([
                CURLOPT_USERPWD => $user->escrow_email . ':' . $user->escrow_api_key
            ]);

            $response = $request->execute();

            if($response->status() !== 200)
            {
                Alert::set(Alert::ERROR, 'We could not connect with Escrow.');
                $this->redirect(Route::url('oc-panel', array('controller'=>'profile','action'=>'edit')));
            }

            try {
                $user->save();
                Alert::set(Alert::INFO, __('Escrow Connected'));
            } catch (ORM_Validation_Exception $e) {
                $errors = $e->errors('models');
                foreach ($errors as $f => $err)
                {
                    Alert::set(Alert::ALERT, $err);
                }
            }

        }

        $this->redirect(Route::url('oc-panel', array('controller'=>'profile','action'=>'edit')));
    }

    public function action_ship()
    {
        $this->auto_render = FALSE;
        $user = Auth::instance()->get_user();

        $order = (new Model_Order())
            ->where('id_order','=',$this->request->param('id'))
            ->where('status','=',Model_Order::STATUS_PAID)
            ->limit(1)
            ->find();

        if (! $order->loaded() OR $order->ad->id_user !== $user->id_user)
        {
            Alert::set(Alert::INFO, __('Order could not be loaded'));
            $this->redirect(Route::url('oc-panel', ['controller' => 'profile', 'action' => 'sales']));
        }

        $escrow_response = $this->execute_transaction_action($user, json_decode($order->txn_id), 'ship');

        if ($escrow_response->status() !== 200)
        {
            Alert::set(Alert::ERROR, __('Escrow: ') . $escrow_response->body());
            $this->redirect(Route::url('oc-panel', ['controller'=>'profile', 'action'=>'sales']));
        }

        $this->update_order_transaction_status($order, json_decode($escrow_response->body()));

        $this->redirect(Route::url('oc-panel', ['controller'=>'profile', 'action'=>'sales']));
    }

    public function action_receive()
    {
        $this->auto_render = FALSE;
        $user = Auth::instance()->get_user();

        $order = (new Model_Order())
            ->where('id_order','=',$this->request->param('id'))
            ->where('status','=',Model_Order::STATUS_PAID)
            ->limit(1)
            ->find();

        if (! $order->loaded() OR $order->id_user !== $user->id_user)
        {
            Alert::set(Alert::INFO, __('Order could not be loaded'));
            $this->redirect(Route::url('oc-panel', ['controller' => 'profile', 'action' => 'sales']));
        }

        $escrow_response = $this->execute_transaction_action($user, json_decode($order->txn_id), 'receive');

        if ($escrow_response->status() !== 200)
        {
            Alert::set(Alert::ERROR, __('Escrow: ') . $escrow_response->body());
            $this->redirect(Route::url('oc-panel', ['controller'=>'profile', 'action'=>'orders']));
        }

        $this->update_order_transaction_status($order, json_decode($escrow_response->body()));

        $this->redirect(Route::url('oc-panel', ['controller'=>'profile', 'action'=>'orders']));
    }

    public function action_accept()
    {
        $this->auto_render = FALSE;
        $user = Auth::instance()->get_user();

        $order = (new Model_Order())
            ->where('id_order','=',$this->request->param('id'))
            ->where('status','=',Model_Order::STATUS_PAID)
            ->limit(1)
            ->find();

        if (! $order->loaded() OR $order->id_user !== $user->id_user)
        {
            Alert::set(Alert::INFO, __('Order could not be loaded'));
            $this->redirect(Route::url('oc-panel', ['controller' => 'profile', 'action' => 'sales']));
        }

        $escrow_response = $this->execute_transaction_action($user, json_decode($order->txn_id), 'accept');

        if ($escrow_response->status() !== 200)
        {
            Alert::set(Alert::ERROR, __('Escrow: ') . $escrow_response->body());
            $this->redirect(Route::url('oc-panel', ['controller'=>'profile', 'action'=>'orders']));
        }

        $this->update_order_transaction_status($order, json_decode($escrow_response->body()));

        $this->redirect(Route::url('oc-panel', ['controller'=>'profile', 'action'=>'orders']));
    }

    protected function execute_transaction_action($user, $transaction, $action)
    {
        if (empty($user->escrow_api_key))
        {
            Alert::set(Alert::INFO, __('Please, connect with Escrow'));
            $this->redirect(Route::url('oc-panel', ['controller' => 'profile', 'action' => 'edit']));
        }

        $request = Request::factory($this->base_url . '2017-09-01/transaction/' . $transaction->transaction_id)
            ->headers('Content-Type', 'application/json')
            ->method(Request::PATCH)
            ->body(json_encode([
                'action' => $action,
            ]));

        $request->client()
            ->options([
                CURLOPT_USERPWD => $user->escrow_email . ':' . $user->escrow_api_key
            ]);

        return $request->execute();
    }

    protected function update_order_transaction_status($order, $transaction)
    {
        $order->txn_id = json_encode([
            'transaction_id' => $transaction->id,
            'status' => [
                'received' => $transaction->items[0]->status->received,
                'rejected_returned' => $transaction->items[0]->status->rejected_returned,
                'rejected' => $transaction->items[0]->status->rejected,
                'received_returned' => $transaction->items[0]->status->received_returned,
                'shipped' => $transaction->items[0]->status->shipped,
                'accepted' => $transaction->items[0]->status->accepted,
                'shipped_returned' => $transaction->items[0]->status->shipped_returned,
                'accepted_returned' => $transaction->items[0]->status->accepted_returned,
                'canceled' => $transaction->items[0]->status->canceled,
            ],
        ]);

        $order->save();

        return $order;
    }
}