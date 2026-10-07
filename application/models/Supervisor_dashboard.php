<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Supervisor_dashboard extends CI_Model
{
    public function ready() { return $this->db->field_exists('supervisor_status','receivings') && $this->db->table_exists('receiving_authorizations'); }
    public function pending($location)
    {
        if (!$this->ready()) return array();
        return $this->db->select('receiving_id, receiving_time, total, employee_id, supervisor_revision')
            ->from('receivings')->where('location_id',$location)->where('deleted',0)
            ->where('suspended',0)->where('supervisor_status','pending')->order_by('receiving_id','DESC')->get()->result_array();
    }
    public function authorize($id, $revision, $location, $employee)
    {
        if (!$this->ready()) return FALSE;
        $this->db->trans_begin();
        $this->db->where('receiving_id',$id)->where('location_id',$location)->where('deleted',0)
            ->where('suspended',0)->where('supervisor_status','pending')->where('supervisor_revision',$revision)
            ->update('receivings',array('supervisor_status'=>'authorized'));
        if ($this->db->affected_rows() !== 1) { $this->db->trans_rollback(); return FALSE; }
        $this->db->insert('receiving_authorizations',array('receiving_id'=>$id,'revision'=>$revision,
            'employee_id'=>$employee,'authorized_at'=>date('Y-m-d H:i:s')));
        if (!$this->db->trans_status()) { $this->db->trans_rollback(); return FALSE; }
        $this->db->trans_commit();
        return TRUE;
    }
    public function activity($location)
    {
        $last = $this->db->select_max('sale_time','last_sale')->from('sales')->where('location_id',$location)
            ->where('deleted',0)->where('suspended',0)->get()->row_array();
        $expenses = $this->db->select('COUNT(*) AS operations, COALESCE(SUM(expense_amount),0) AS amount, COALESCE(SUM(expense_tax),0) AS tax',FALSE)
            ->from('expenses')->where('location_id',$location)->where('deleted',0)
            ->where('expense_date >=',date('Y-m-d 00:00:00'))->where('expense_date <',date('Y-m-d 00:00:00',strtotime('+1 day')))->get()->row_array();
        return array('last_sale'=>$last['last_sale'],'expenses'=>$expenses);
    }
}
