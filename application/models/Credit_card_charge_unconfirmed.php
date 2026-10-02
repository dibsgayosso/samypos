<?php
class Credit_card_charge_unconfirmed extends CI_Model
{
	function get($id)
	{
		$this->db->from('credit_card_transactions_unconfirmed');
		$this->db->where('id',$id);
		return $this->db->get()->row_array();
	}
	
	function insert($data)
	{
		return $this->db->insert('credit_card_transactions_unconfirmed',$data);
	}

	function delete($transaction_charge_id)
	{
		$this->db->where('transaction_charge_id',$transaction_charge_id);
		return $this->db->delete('credit_card_transactions_unconfirmed');
	}
	
	function get_all($cart = NULL)
	{
		$register_id = $this->Employee->get_logged_in_employee_current_register_id();
		$current_session_payment_ids = array();
		
		if ($cart)
		{
			$current_session_payment_ids = $cart->get_credit_card_payments_charge_ids();
		}
		
		if (empty($current_session_payment_ids))
		{
			$current_session_payment_ids[] = -1;
		}
		
		$this->db->from('credit_card_transactions_unconfirmed');		
		$this->db->where_in('register_id_of_charge',$register_id);
		$this->db->where_not_in('transaction_charge_id',$current_session_payment_ids);
		return $this->db->get()->result_array();
		
	}
}