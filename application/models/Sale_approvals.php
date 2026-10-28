<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sale_approvals extends CI_Model
{
    public function methods()
    {
        $methods=json_decode((string)$this->config->item('sale_approval_payment_methods'),TRUE);
        return is_array($methods) ? array_values(array_filter($methods,'is_string')) : array();
    }
    public function required($cart)
    {
        foreach ($cart->get_payments() as $payment) {
            $type=$payment->payment_type;
            foreach ($this->methods() as $method) {
                $labels=strpos($method,'common_')===0 ? get_all_language_values_for_key($method,'common') : array($method);
                foreach ($labels as $label) {
                    if ((float)$payment->payment_amount != 0 && ($type===$label || ($method==='common_giftcard' && strpos($type,$label.':')===0))) return TRUE;
                }
            }
        }
        return FALSE;
    }
    public function ready() { return $this->db->table_exists('sale_requests'); }
    public function can_authorize()
    {
        $employee=$this->Employee->get_logged_in_employee_info()->person_id;
        return $this->Employee->has_module_permission('sales',$employee) &&
            $this->Employee->has_module_action_permission('sales','authorize_sales',$employee);
    }
    public function stage($cart,$key)
    {
        if (!$this->ready() || !$this->required($cart) || !$key || !count($cart->get_items()) || $cart->sale_id || $cart->get_mode()==='estimate') return FALSE;
        $location=$this->Employee->get_logged_in_employee_current_location_id();
        $existing=$this->db->get_where('sale_requests',array('submission_key'=>$key,'location_id'=>$location))->row_array();
        if ($existing) return $existing['id'];
        $copy=unserialize(serialize($cart));
        $copy->cart_id=NULL;
        $copy->employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
        $copy->location_id=$location;
        $copy->register_id=$this->Employee->get_logged_in_employee_current_register_id();
        $copy->suspended=0;
        if ($copy->get_amount_due() > 0.00001) return FALSE;
        $details=array('items'=>array(),'payments'=>array());
        foreach ($copy->get_items() as $line) $details['items'][]=array('name'=>$line->name,'quantity'=>$line->quantity,'total'=>$line->get_total());
        foreach ($copy->get_payments() as $payment) $details['payments'][]=array('method'=>$payment->payment_type,'amount'=>$payment->payment_amount);
        $ok=$this->db->insert('sale_requests',array('employee_id'=>$copy->employee_id,'location_id'=>$location,
            'customer_id'=>$copy->customer_id ?: NULL,'sale_time'=>date('Y-m-d H:i:s'),'total'=>$copy->get_total(),'cart_payload'=>base64_encode(serialize($copy)),
            'detail_json'=>json_encode($details),'context_json'=>json_encode(array('cc_token'=>$this->session->userdata('cc_token'),'masked_account'=>$this->session->userdata('masked_account'),'cc_signature'=>$this->session->userdata('cc_signature'))),'submission_key'=>$key));
        return $ok ? $this->db->insert_id() : FALSE;
    }
    public function listing()
    {
        if (!$this->ready()) return array();
        $this->db->select('id,employee_id,status,sale_time,total,sale_id,rejection_reason')->from('sale_requests')
            ->where('location_id',$this->Employee->get_logged_in_employee_current_location_id());
        if (!$this->can_authorize()) $this->db->where('employee_id',$this->Employee->get_logged_in_employee_info()->person_id);
        return $this->db->order_by("status='pending'",'DESC',FALSE)->order_by('id','DESC')->limit(100)->get()->result_array();
    }
    public function detail($id)
    {
        if (!$this->ready()) return FALSE;
        $row=$this->db->get_where('sale_requests',array('id'=>$id,'location_id'=>$this->Employee->get_logged_in_employee_current_location_id()))->row_array();
        return $row && ($this->can_authorize() || (int)$row['employee_id']===(int)$this->Employee->get_logged_in_employee_info()->person_id) ? $row : FALSE;
    }
    public function decide($id,$employee,$reject,$reason='')
    {
        if (!$this->ready() || !$this->can_authorize() || (int)$employee!==(int)$this->Employee->get_logged_in_employee_info()->person_id || ($reject && !trim($reason))) return FALSE;
        $this->db->trans_begin();
        try {
            $table=$this->db->dbprefix('sale_requests');
            $row=$this->db->query("SELECT * FROM `$table` WHERE id=? AND location_id=? AND status='pending' FOR UPDATE",
                array($id,$this->Employee->get_logged_in_employee_current_location_id()))->row_array();
            if (!$row || (int)$row['employee_id']===(int)$employee) { $this->db->trans_rollback(); return FALSE; }
            $sale=NULL;
            if (!$reject) {
                $this->load->model('Sale');
                $sale=$this->Sale->apply_sale_request($row);
                if ($sale<=0) { $this->db->trans_rollback(); return FALSE; }
            }
            $this->db->where('id',$id)->update('sale_requests',array('status'=>$reject ? 'rejected' : 'authorized',
                'authorized_by'=>$employee,'authorized_at'=>date('Y-m-d H:i:s'),'sale_id'=>$sale,'rejection_reason'=>$reject ? trim($reason) : NULL));
            if (!$this->db->trans_status()) { $this->db->trans_rollback(); return FALSE; }
            $this->db->trans_commit();
            return TRUE;
        } catch (Throwable $error) { $this->db->trans_rollback(); log_message('error','Sale approval failed'); return FALSE; }
    }
}
