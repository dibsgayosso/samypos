<?php
require_once (APPPATH."models/cart/PHPPOSCartSale.php");

class Wooauth extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->cart = new PHPPOSCartSale();
        $this->load->model('Appconfig');
        $this->load->model('Woo');
    }

    public function receive_woo_api_keys()
    {
        $this->load->model('Appconfig');

        $auth_response = json_decode(file_get_contents('php://input'), TRUE);

        $consumer_key = $auth_response['consumer_key'];
        $consumer_secret = $auth_response['consumer_secret'];

        $result = $this->Appconfig->save_woo_api_keys($consumer_key, $consumer_secret);
        
        if($result) {
            $this->Appconfig->save('ecommerce_realtime',1);

            if (is_https()) {
                $create_data = array(
                    [
                        'name' => 'Product created',
                        'topic' => 'product.created',
                        'status' => 'active',
                        'delivery_url' => site_url('woohooks/item_webhook_create_product')
                    ],
                    [
                        'name' => 'Product updated',
                        'topic' => 'product.updated',
                        'status' => 'active',
                        'delivery_url' => site_url('woohooks/item_webhook_update_product')
                    ],
                    [
                        'name' => 'Product deleted',
                        'topic' => 'product.deleted',
                        'status' => 'active',
                        'delivery_url' => site_url('woohooks/item_webhook_delete_product')
                    ],
                    [
                        'name' => 'Order created',
                        'topic' => 'order.created',
                        'status' => 'active',
                        'delivery_url' => site_url('woohooks/order_webhook_create')
                    ],
                    [
                        'name' => 'Order updated',
                        'topic' => 'order.updated',
                        'status' => 'active',
                        'delivery_url' => site_url('woohooks/order_webhook_update')
                    ],
                    [
                        'name' => 'Order deleted',
                        'topic' => 'order.deleted',
                        'status' => 'active',
                        'delivery_url' => site_url('woohooks/order_webhook_delete')
                    ],
                );
        		
				require_once APPPATH.'models/Woo_webhooks.php';
				$woo_webhooks = new Woo_webhooks($this->woo);
				
				$webhooks = $woo_webhooks->get_webhooks();
				$delete_data = array();
				foreach($webhooks as $hook)
				{
					//This a php pos hook
					if (strpos($hook['delivery_url'], 'woohooks/') !== false)
					{
						$delete_data[] = $hook['id'];
					}
				}
				
                $this->woo->batch_webhooks($create_data, array(), $delete_data);
            } else {
                echo lang('delivery_url_https_error');
            }
        } else {
            $this->Appconfig->save('woo_is_authenticated', 0);
        }
    }
}