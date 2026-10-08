<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Owner_dashboard extends CI_Model
{
    public function allowed_locations($person)
    {
        if (!$this->Employee->has_module_permission('reports',$person)) return array();
        $allowed=array();
        foreach ($this->Employee->get_authenticated_location_ids($person) as $location) {
            if ($this->Employee->has_module_action_permission('reports','view_owner_dashboard',$person,$location)) $allowed[]=(int)$location;
        }
        return array_values(array_unique($allowed));
    }
    public static function empty_totals()
    {
        return array('sales_total'=>0,'sales_count'=>0,'receiving_total'=>0,'receiving_count'=>0,'pending_count'=>0,'pending_total'=>0,'expenses_total'=>0,'expenses_count'=>0,'credit_balance'=>0,'credit_customers'=>0,'credit_in_favor'=>0);
    }
    public static function rollup($branches)
    {
        $totals=self::empty_totals(); $payments=array(); $last_sale=NULL;
        foreach ($branches as $branch) {
            foreach ($totals as $key=>$value) $totals[$key]+=$branch[$key];
            foreach ($branch['payments'] as $payment) {
                $label=$payment['label'];
                if (!isset($payments[$label])) $payments[$label]=array('label'=>$label,'operations'=>0,'total'=>0);
                $payments[$label]['operations']+=$payment['operations']; $payments[$label]['total']+=$payment['total'];
            }
            if ($branch['last_sale']!==NULL && ($last_sale===NULL || $branch['last_sale']>$last_sale)) $last_sale=$branch['last_sale'];
        }
        return array('totals'=>$totals,'payments'=>array_values($payments),'last_sale'=>$last_sale);
    }
    public function snapshot($person,$payment_loader)
    {
        $branches=array(); $timezone=date_default_timezone_get();
        try {
            foreach ($this->allowed_locations($person) as $id) {
                $location=$this->Location->get_info($id);
                if (!$location || !$location->location_id || $location->deleted) continue;
                $zone=$location->timezone ?: $timezone;
                if (!in_array($zone,timezone_identifiers_list(),TRUE)) $zone=$timezone;
                date_default_timezone_set($zone);
                $day=array(date('Y-m-d 00:00:00'),date('Y-m-d 00:00:00',strtotime('+1 day')));
                $branch=$this->branch($id,$day,$person);
                $payments=call_user_func($payment_loader,$id);
                $branch['sales_total']=$payments['sales_total']; $branch['sales_count']=$payments['sales_count']; $branch['payments']=$payments['rows'];
                $branch['name']=$location->name; $branch['id']=$id; $branch['day']=date('d/m/Y'); $branch['timezone']=$zone;
                $branches[]=$branch;
            }
        } finally { date_default_timezone_set($timezone); }
        return array_merge(self::rollup($branches),array('branches'=>$branches,'updated_at'=>time(),'receiving_ready'=>$this->db->table_exists('receiving_requests')));
    }
    protected function branch($id,$day,$person)
    {
        $result=self::empty_totals();
        // A debt payment and a return are not the most recent merchandise sale.
        $last=$this->db->select('UNIX_TIMESTAMP(MAX(sale_time)) AS last_sale',FALSE)->from('sales')->where('location_id',$id)
            ->where('deleted',0)->where('suspended',0)->where('store_account_payment',0)->where('total >=',0)->get()->row_array();
        $result['last_sale']=$last['last_sale']===NULL?NULL:(int)$last['last_sale'];
        $received=$this->db->select('COUNT(*) AS operations, COALESCE(SUM(total),0) AS amount',FALSE)->from('receivings')
            ->where('location_id',$id)->where('deleted',0)->where('suspended',0)->where('store_account_payment',0)->where('total_quantity_received >',0)
            ->where('receiving_time >=',$day[0])->where('receiving_time <',$day[1])->get()->row_array();
        $result['receiving_count']=(int)$received['operations']; $result['receiving_total']=(float)$received['amount'];
        if ($this->db->table_exists('receiving_requests')) {
            // Pending inventory has not been applied and is excluded from received totals.
            $pending=$this->db->select('COUNT(*) AS operations, COALESCE(SUM(total),0) AS amount',FALSE)->from('receiving_requests')
                ->where('location_id',$id)->where('status','pending')->get()->row_array();
            $result['pending_count']=(int)$pending['operations']; $result['pending_total']=(float)$pending['amount'];
        }
        $expenses=$this->db->select('COALESCE('.$this->db->dbprefix('expenses_categories').'.name, \'Sin categoría\') AS category, COUNT(*) AS operations, COALESCE(SUM(expense_amount+expense_tax),0) AS amount',FALSE)
            ->from('expenses')->join('expenses_categories','expenses_categories.id=expenses.category_id','left')->where('expenses.location_id',$id)->where('expenses.deleted',0)
            ->where('expense_date >=',$day[0])->where('expense_date <',$day[1])->group_by('expenses.category_id')->group_by('expenses_categories.name')->get()->result_array();
        $result['expenses']=$expenses;
        foreach ($expenses as $expense) { $result['expenses_count']+=(int)$expense['operations']; $result['expenses_total']+=(float)$expense['amount']; }
        // Match store-account reports: current balance belongs to the customer's assigned branch.
        $credit=$this->db->select('COALESCE(SUM(CASE WHEN balance>0 THEN balance ELSE 0 END),0) AS debt, COALESCE(SUM(CASE WHEN balance<0 THEN -balance ELSE 0 END),0) AS in_favor, COALESCE(SUM(CASE WHEN balance>0 THEN 1 ELSE 0 END),0) AS customers',FALSE)
            ->from('customers')->where('location_id',$id)->where('deleted',0)->get()->row_array();
        $result['credit_balance']=(float)$credit['debt']; $result['credit_in_favor']=(float)$credit['in_favor']; $result['credit_customers']=(int)$credit['customers'];
        $result['can_view_difference']=$this->Employee->has_module_action_permission('reports','view_register_difference',$person,$id);
        $result['last_close']=$result['can_view_difference']?$this->Register->get_last_closed_register_summary($id):FALSE;
        return $result;
    }
}
