<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Supervisor_dashboard extends CI_Model
{
    public function allowed_locations($person)
    {
        if (!$this->Employee->has_module_permission('receivings',$person)) return array();
        $allowed=array();
        foreach ($this->Employee->get_authenticated_location_ids($person) as $location) {
            if ($this->Employee->has_module_action_permission('receivings','authorize_receivings',$person,$location)) $allowed[]=(int)$location;
        }
        return array_values(array_unique($allowed));
    }
    public function snapshot($person,$payment_loader)
    {
        $branches=array(); $zone=date_default_timezone_get();
        try {
            foreach ($this->allowed_locations($person) as $id) {
                $location=$this->Location->get_info($id);
                if (!$location || !$location->location_id || $location->deleted) continue;
                $timezone=$location->timezone ?: $zone;
                if (!in_array($timezone,timezone_identifiers_list(),TRUE)) $timezone=$zone;
                date_default_timezone_set($timezone);
                $activity=$this->activity($id);
                $activity['last_sale_at']=$activity['last_sale'] ? strtotime($activity['last_sale']) : NULL;
                $branches[]=array('id'=>$id,'name'=>$location->name,'day'=>date('d/m/Y'),
                    'activity'=>$activity,'pending'=>$this->pending($id),
                    'payments'=>call_user_func($payment_loader,$id));
            }
        } finally { date_default_timezone_set($zone); }
        return array('branches'=>$branches,'ready'=>$this->ready());
    }
    public function ready() { return $this->db->table_exists('receiving_requests'); }
    public function pending($location)
    {
        if (!$this->ready()) return array();
        return $this->db->select('id AS receiving_id, receiving_time, total, employee_id, supervisor_revision')
            ->from('receiving_requests')->where('location_id',$location)->where('status','pending')
            ->order_by('id','DESC')->get()->result_array();
    }
    public function stage($cart, $key)
    {
        if (!$this->ready() || !count($cart->get_items()) || !$key || !in_array($cart->get_mode(),array('receive','return','transfer'),TRUE)) return FALSE;
        $location=$this->Employee->get_logged_in_employee_current_location_id();
        $source=NULL;
        if ($cart->receiving_id) {
            $source=$this->db->get_where('receivings',array('receiving_id'=>$cart->receiving_id,'location_id'=>$location,'deleted'=>0))->row_array();
            if (!$source || !(int)$source['suspended'] || (float)$source['total_quantity_received'] != 0) return FALSE;
        }
        $existing=$this->db->get_where('receiving_requests',array('submission_key'=>$key,'location_id'=>$location))->row_array();
        if ($existing) return $existing['id'];
        if ($source) {
            $existing=$this->db->get_where('receiving_requests',array('source_receiving_id'=>$source['receiving_id']))->row_array();
            if ($existing) return $existing['id'];
        }
        $details=array();
        foreach ($cart->get_items() as $line) $details[]=array('name'=>$line->name,
            'quantity'=>$line->quantity,'unit_price'=>$line->unit_price,'total'=>$line->get_total(),
            'unit_quantity'=>$line->quantity_unit_quantity,'serialnumber'=>$line->serialnumber,'expire_date'=>isset($line->expire_date) ? $line->expire_date : NULL);
        $ok=$this->db->insert('receiving_requests',array('location_id'=>$location,'source_receiving_id'=>$source ? $source['receiving_id'] : NULL,
            'employee_id'=>$this->Employee->get_logged_in_employee_info()->person_id,
            'supplier_id'=>$cart->supplier_id ?: NULL,'receiving_time'=>date('Y-m-d H:i:s'),
            'total'=>$cart->get_total(),'cart_payload'=>base64_encode(serialize($cart)),
            'detail_json'=>json_encode($details),'submission_key'=>$key));
        return $ok ? $this->db->insert_id() : FALSE;
    }
    public function detail($id,$location)
    {
        if (!$this->ready()) return FALSE;
        return $this->db->get_where('receiving_requests',array('id'=>$id,'location_id'=>$location))->row_array();
    }
    public function own_requests($location,$employee)
    {
        if (!$this->ready()) return array();
        return $this->db->select('id,status,receiving_time,total,receiving_id,authorized_by,authorized_at,rejection_reason')
            ->from('receiving_requests')->where('location_id',$location)->where('employee_id',$employee)
            ->order_by('id','DESC')->limit(30)->get()->result_array();
    }
    public function can_print_request($request,$employee,$location,$supervisor)
    {
        return $request && $request['status']==='authorized' && (int)$request['receiving_id']>0
            && (int)$request['location_id']===(int)$location
            && ((int)$request['employee_id']===(int)$employee || $supervisor);
    }
    public function authorization_for_receipt($id)
    {
        if (!$this->ready()) return FALSE;
        return $this->db->get_where('receiving_requests',array('receiving_id'=>$id))->row_array();
    }
    public function authorize($id, $revision, $location, $employee)
    {
        if (!$this->ready() || !$this->Employee->has_module_action_permission('receivings','authorize_receivings',$employee)
            || (int)$location !== (int)$this->Employee->get_logged_in_employee_current_location_id()) return FALSE;
        $this->db->trans_begin();
        $table=$this->db->dbprefix('receiving_requests');
        $request=$this->db->query("SELECT * FROM `$table` WHERE id=? AND location_id=? AND status='pending' AND supervisor_revision=? FOR UPDATE",
            array($id,$location,$revision))->row_array();
        if (!$request || (int)$request['employee_id']===(int)$employee) { $this->db->trans_rollback(); return FALSE; }
        $this->load->model('Receiving');
        try { $receiving=$this->Receiving->apply_supervisor_request($request); }
        catch (Throwable $error) { $this->db->trans_rollback(); log_message('error','Supervisor receipt application failed'); return FALSE; }
        if ($receiving <= 0 || !$this->db->trans_status()) { $this->db->trans_rollback(); return FALSE; }
        $this->db->where('id',$id)->update('receiving_requests',array('status'=>'authorized',
            'authorized_by'=>$employee,'authorized_at'=>date('Y-m-d H:i:s'),'receiving_id'=>$receiving));
        if (!$this->db->trans_status()) { $this->db->trans_rollback(); return FALSE; }
        $this->db->trans_commit();
        return TRUE;
    }
    public function reject($id,$location,$employee,$reason)
    {
        if (!$this->ready() || !trim($reason) || !$this->Employee->has_module_action_permission('receivings','authorize_receivings',$employee)
            || (int)$location !== (int)$this->Employee->get_logged_in_employee_current_location_id()) return FALSE;
        $this->db->where('id',$id)->where('location_id',$location)->where('status','pending')
            ->update('receiving_requests',array('status'=>'rejected','authorized_by'=>$employee,
                'authorized_at'=>date('Y-m-d H:i:s'),'rejection_reason'=>trim($reason)));
        return $this->db->affected_rows() === 1;
    }
    public function activity($location)
    {
        $last = $this->db->select_max('sale_time','last_sale')->from('sales')->where('location_id',$location)
            ->where('deleted',0)->where('suspended',0)->where('store_account_payment',0)->where('total >=',0)->get()->row_array();
        $expenses = $this->db->select('COUNT(*) AS operations, COALESCE(SUM(expense_amount),0) AS amount, COALESCE(SUM(expense_tax),0) AS tax',FALSE)
            ->from('expenses')->where('location_id',$location)->where('deleted',0)
            ->where('expense_date >=',date('Y-m-d 00:00:00'))->where('expense_date <',date('Y-m-d 00:00:00',strtotime('+1 day')))->get()->row_array();
        return array('last_sale'=>$last['last_sale'],'expenses'=>$expenses);
    }
}
