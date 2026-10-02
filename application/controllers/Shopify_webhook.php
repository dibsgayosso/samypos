<?php
require_once (APPPATH."models/cart/PHPPOSCartSale.php");

class Shopify_webhook extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->cart = new PHPPOSCartSale();
        $this->load->model('Appconfig');
        $this->load->model('Shopify');
		
		if (!$this->config->item('ecommerce_realtime'))
		{
			die('webhooks disabled');
		}
    }

	public function item_webhook_create_product()
	{
		$ecommerce_cron_sync_operations_settings = unserialize($this->config->item('ecommerce_cron_sync_operations'));
		if(in_array('import_ecommerce_items_into_phppos', $ecommerce_cron_sync_operations_settings))
		{
        	$item_request = json_decode(file_get_contents('php://input'), TRUE);
        	$this->_save_item($item_request);
        }
		http_response_code(200);		
	}
	
    public function item_webhook_delete_product()
    {
		$ecommerce_cron_sync_operations_settings = unserialize($this->config->item('ecommerce_cron_sync_operations'));
		if(in_array('import_ecommerce_items_into_phppos', $ecommerce_cron_sync_operations_settings))
		{
        	$item_request = json_decode(file_get_contents('php://input'), TRUE);
        	$this->_delete_item($item_request['id']);
		}
		http_response_code(200);
    }
	
	
	public function item_webhook_update_product()
	{
		$ecommerce_cron_sync_operations_settings = unserialize($this->config->item('ecommerce_cron_sync_operations'));
		if(in_array('import_ecommerce_items_into_phppos', $ecommerce_cron_sync_operations_settings))
		{
        	$item_request = json_decode(file_get_contents('php://input'), TRUE);
        	$this->_save_item($item_request);
		}
		http_response_code(200);		
	}
	
    public function order_webhook_create()
    {
		$ecommerce_cron_sync_operations_settings = unserialize($this->config->item('ecommerce_cron_sync_operations'));
		if(in_array('import_ecommerce_orders_into_phppos', $ecommerce_cron_sync_operations_settings))
		{
    		$order_request = json_decode(file_get_contents('php://input'), TRUE);
        	$this->_save_order($order_request, true);
		}
		http_response_code(200);
    }
	
	public function order_webhook_update()
    {
		$ecommerce_cron_sync_operations_settings = unserialize($this->config->item('ecommerce_cron_sync_operations'));
		if(in_array('import_ecommerce_orders_into_phppos', $ecommerce_cron_sync_operations_settings))
		{
        	$order_request = json_decode(file_get_contents('php://input'), TRUE);
			$is_new_order = false;
			if ($this->config->item('ecommerce_only_sync_completed_orders'))
			{
				if ($order_request['fulfillment_status'] == 'fulfilled')
				{
					$sale_id = $this->Sale->get_sale_id_for_ecommerce_order_id($order_request['id']);
					
					if (!$sale_id)
					{
						$is_new_order = TRUE;
					}
				}
			}
			
			$sale_id = $this->Sale->get_sale_id_for_ecommerce_order_id($order_request['id']);
						
        	$this->_save_order($order_request, $is_new_order);
			
        }
		http_response_code(200);
    }
	
	public function order_webhook_edit()
	{
		//Do nothing for now
	}
	
    public function order_webhook_delete()
    {
		$ecommerce_cron_sync_operations_settings = unserialize($this->config->item('ecommerce_cron_sync_operations'));
		if(in_array('import_ecommerce_orders_into_phppos', $ecommerce_cron_sync_operations_settings))
		{
        	$order_request = json_decode(file_get_contents('php://input'), TRUE);
        	$this->_delete_order($order_request['id']);
		}
		http_response_code(200);
    }

    private function _save_item($item_request)
    {
		$this->load->helper('command');
		$this->load->helper('text');
		$db_user= $this->db->username;
		$account = str_replace($db_user.'_','',$this->db->database);
		$domain = $this->config->item('branding')['domain'];
		$phppos_url = "https://".$account.'.'.$domain.'/';
		
		$phppos_url_encoded=base64UrlEncode(json_encode($phppos_url));
		$item_request_encoded = base64UrlEncode(json_encode($item_request));
		$command = 'php '.FCPATH."index.php shopify_webhook save_item_in_background $phppos_url_encoded $account $item_request_encoded";
		
		run_command_in_background($command);
    }
	
	public function save_item_in_background($base_url_encoded = '', $db_override = '',$item_request_encoded)
	{
		if (!is_cli())
		{
			die('must be cli');
		}
		
		$this->load->helper('text');
		$base_url = json_decode(base64UrlDecode($base_url_encoded));
		$item_request = json_decode(base64UrlDecode($item_request_encoded), TRUE);
        $this->Shopify->import_ecommerce_item_into_phppos($item_request);
	}

    private function _save_order($sale_request,$is_new_order)
    {		
		$ecommerce_cron_sync_operations_settings = unserialize($this->config->item('ecommerce_cron_sync_operations'));
		
		if ($this->config->item('ecommerce_only_sync_completed_orders'))
		{			
			if ($sale_request['fulfillment_status'] != 'fulfilled')
			{
				return;
			}
		}
		
		if (!$is_new_order)
		{
			$sale_id = $this->Sale->get_sale_id_for_ecommerce_order_id($sale_request['id']);
			
			if ($sale_id)
			{		
				if(in_array('sync_inventory_changes', $ecommerce_cron_sync_operations_settings))
				{
					$this->Shopify->update_inventory_from_sale($sale_request, TRUE);
		
				}
			}
		}
		
        $this->Shopify->process_import_order($sale_request);
		
		if ($is_new_order)
		{
			if(in_array('sync_inventory_changes', $ecommerce_cron_sync_operations_settings))
			{
				$this->Shopify->update_inventory_from_sale($sale_request);
			}
		}		
	}
	
    private function _delete_item($ecommerce_id)
    {
        $this->db->from('items');
		$this->db->where('ecommerce_product_id', (string)$ecommerce_id);
		$result = $this->db->get();
		if ($result->num_rows() == 1)
		{
			$item=$result->row_array();
			$item_id = $item['item_id'];

            $this->db->where('item_id', $item_id)->update('items', array('deleted' => 1, 'last_modified' => date('Y-m-d H:i:s')));
		}
    }

    private function _delete_order($ecommerce_id)
    {
        $this->load->model('Sale');

        $this->db->from('sales');
		$this->db->where('ecommerce_order_id', (string)$ecommerce_id);
		$result = $this->db->get();
		if ($result->num_rows() == 1)
		{
			$sale = $result->row_array();
			$sale_id = $sale['sale_id'];

            if ($sale && $sale_id && !$sale['deleted']) {
                $this->Sale->delete($sale_id);
            }
		}
    }

    private function get_item_id_for_ecommerce_product($ecommerce_product_id)
	{
		$this->db->from('items');
		$this->db->where('ecommerce_product_id', (string)$ecommerce_product_id);
		$result = $this->db->get();
		if ($result->num_rows() >= 1)
		{
			$item=$result->row_array();
			return $item['item_id'];
		}
		else
		{
			return $this->Item->create_or_update_ecommerce_item();
		}
		
		return null;
	}
}