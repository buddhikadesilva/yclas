<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Api_Orders extends Api_Auth {

    /**
     * Handle GET requests.
     */
    public function action_index()
    {
        try
        {
            if (is_numeric($this->request->param('id')))
            {
                $this->action_get();
            }
            else
            {
                $output = array();

                $orders = new Model_Order();

                //filter results by param, verify field exists and has a value and sort the results
                $orders->api_filter($this->_filter_params)->api_sort($this->_sort);

                //how many? used in header X-Total-Count
                $count = $orders->count_all();

                //by default sort by created date
                if(empty($this->_sort))
                    $this->_sort['created'] = 'desc';

                //after counting sort values
                $orders->api_sort($this->_sort);

                //pagination with headers
                $pagination = $orders->api_pagination($count,$this->_params['items_per_page']);

                $orders = $orders->cached()->find_all();

                //as array
                foreach ($orders as $order)
                    $output[] = self::get_order_array($order);

                $this->rest_output(array('orders' => $output),200,$count,($pagination!==FALSE)?$pagination:NULL);
            }
        }
        catch (Kohana_HTTP_Exception $khe)
        {
            $this->_error($khe);
        }
    }

    /**
     * Handle GET requests.
     */
    public function action_get()
    {
        try
        {
            $order = new Model_order();

            if (is_numeric($id_order = $this->request->param('id')))
            {
                $order = new Model_order($id_order);
            }
            
            if ($order->loaded())
                $this->rest_output(array('order' => self::get_order_array($order)));
            else
                $this->_error(__('Order not found'),404);
           
        }
        catch (Kohana_HTTP_Exception $khe)
        {
            $this->_error($khe);
        }
    }

    public function action_create()
    {
        try
        {
            if (!is_numeric(core::request('id_ad'))  OR !is_numeric(core::request('id_product')) OR !is_numeric(core::request('id_user')))
                $this->_error(__('Missing parameters'),501);
            else
            {
                $user = new Model_User(core::request('id_user'));
                $ad   = new Model_Ad(core::request('id_ad'));

                if($user->loaded() AND $ad->loaded())
                {
                    $id_product = core::request('id_product');
                    $amount = core::request('amount');

                    //in case not set by request
                    if (!is_numeric($amount))
                    {
                        //get original price for the product
                        switch ($id_product) {
                            case Model_Order::PRODUCT_CATEGORY:
                                    $amount = $ad->category->price;
                                break;
                            case Model_Order::PRODUCT_TO_TOP:
                                    $amount = core::config('payment.pay_to_go_on_top');
                                break;
                            case Model_Order::PRODUCT_TO_FEATURED:
                                    $amount = Model_Order::get_featured_price(core::request('featured_days'));
                                break;
                            case Model_Order::PRODUCT_AD_SELL:
                                    $amount =$ad->price;
                                break;
                            default:
                                $plan = new Model_Plan($id_product);

                                $amount = ($plan->loaded())?$plan->price:0;
                                break;
                        }
                    }

                    $order = Model_Order::new_order($ad, $user, $id_product, $amount, 
                                                    core::request('currency'), Model_Order::product_desc(core::request('id_product')), core::request('featured_days'));

                    $order->confirm_payment(core::request('paymethod','API'), core::request('txn_id'));

                    $order->save();


                    $this->rest_output(array('order' => self::get_order_array($order)));
                } 
                else
                    $this->_error(__('User or Ad not loaded'),501);
            }
            
            
        }
        catch (Kohana_HTTP_Exception $khe)
        {
            $this->_error($khe);
        }
    }


    /**
     * Handle GET requests.
     */
    public function action_products()
    {
        try
        {
            $this->rest_output(array('products' => Model_Order::products()));
        }
        catch (Kohana_HTTP_Exception $khe)
        {
            $this->_error($khe);
        }
    }


    public static function get_order_array($order)
    {
        $o = $order->as_array();
        $o['user']['id'] = $order->user->id_user;
        $o['user']['email'] = $order->user->email;
        $o['product'] = Model_Order::product_desc($order->id_product);
        $o['coupon'] = ($order->coupon->loaded())?$order->coupon->as_array():NULL;

        return $o;
    }



} // END