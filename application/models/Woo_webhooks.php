<?php
require_once(APPPATH.'models/MY_Woo.php');

class Woo_webhooks extends MY_Woo
{
	const get_endpoint = "webhooks/<id>";
	const get_endpoint_list = "webhooks";
	const post_endpoint = "webhooks";
	const delete_endpoint = "webhooks/<id>";
	const batch_endpoint="webhooks/batch";
	
	public function __construct($woo)
	{
		parent::__construct($woo);
	}

    protected function reset()
	{
		parent::reset();
	}
	
	private static function delete_endpoint($webhook_id)
	{
		return str_replace("<id>", $webhook_id, self::delete_endpoint);
	}
	
	public function get_webhooks() 
	{
        $this->reset();
		$this->response = parent::do_get(self::get_endpoint_list);
		
		return $this->response;		
	}
	
		
	public function get_webhook() 
	{
        $this->reset();
		$this->response = parent::do_get(self::get_endpoint);
		
		return $this->response;		
	}
	
	public function delete_webhook($webhook_id)
	{
        $this->reset();

		try
		{
			$this->parameters['force'] = true;
			$this->response = parent::do_delete(self::delete_endpoint($webhook_id), $this->parameters);
			
			return $this->response['id'];
		}
		catch(Exception $e)
		{
			$this->woo->log("*******".lang('common_EXCEPTION').": ".var_export($e->getMessage(),TRUE));
		}
		
		return NULL;
	}
	
	public function save_webhook($webhook_data)
	{
        $this->reset();

		try
		{
			$this->data = $webhook_data;
						
			$this->response = parent::do_post(self::post_endpoint);
			
			return $this->response['id'];
		}
		catch(Exception $e)
		{
			$this->woo->log("*******".lang('common_EXCEPTION').": ".var_export($e->getMessage(),TRUE));
		}
		
		return NULL;
	}

	public function batch_webhooks($create_array = array(), $update_array = array(), $delete_array = array())
	{
        $this->reset();
        
        if(!empty($create_array)) {
            $this->data['create'] = $create_array;
        }

        if(!empty($update_array)) {
            $this->data['update'] = $update_array;
        }

        if(!empty($delete_array)) {
            $this->data['delete'] = $delete_array;
        }

		try
		{
			$this->response = parent::do_batch(self::batch_endpoint);
		}
		catch(Exception $e)
		{
			$this->woo->log("*******".lang('common_EXCEPTION').": ".var_export($e->getMessage(),TRUE));
		}
	}
	
}
?>