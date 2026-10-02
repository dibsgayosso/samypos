<?php
require_once("Secure_area.php");

class Mx extends Secure_area
{
    function __construct()
    {
        parent::__construct('sales');
        
        $this->lang->load('module');
        $this->lang->load('mx');
        
        $this->load->model('Mx_connect');
    }
    
    function get_statement_detail()
    {
        $statement_id = $this->input->get('statement_id');
        $statement_type = $this->input->get('statement_type');
        
        echo json_encode($this->Mx_connect->get_statement_detail($statement_id, $statement_type));
    }
    
    function get_statements()
    {
        if ($this->_is_coreclear_enabled()) {
            if ($this->_is_coreclear_fully_setup()) {
                $date_range = $this->input->get('date_range');
                
                echo json_encode($this->Mx_connect->get_statements($this->_get_account_numbers(), $date_range));
            }
            else {
                echo json_encode(array('warning' => true, 'message' => lang('mx_setup_warning')));
            }
        }
        else {
            echo json_encode(array('success' => false));
        }
    }
    
    function get_stats()
    {
        if ($this->_is_coreclear_enabled()) {
            if ($this->_is_coreclear_fully_setup()) {
                $date_range = $this->input->get('date_range');
                
                echo json_encode($this->Mx_connect->get_stats($this->_get_account_numbers(), $date_range));
            }
            else {
                echo json_encode(array('warning' => true, 'message' => lang('mx_setup_warning')));
            }
        }
        else {
            echo json_encode(array('success' => false));
        }
    }
    
    function get_disputes()
    {
        if ($this->_is_coreclear_enabled()) {
            if ($this->_is_coreclear_fully_setup()) {
                $date_range = $this->input->get('date_range');
                
                echo json_encode($this->Mx_connect->get_disputes($this->_get_account_numbers(), $date_range));
            }
            else {
                echo json_encode(array('warning' => true, 'message' => lang('mx_setup_warning')));
            }
        }
        else {
            echo json_encode(array('success' => false));
        }
    }
    
    function get_ach_funding()
    {
        if ($this->_is_coreclear_enabled()) {
            if ($this->_is_coreclear_fully_setup()) {
                $start_date = $this->input->get('start_date');
                $end_date = $this->input->get('end_date');
                $sort = $this->input->get('sort');
                $limit = $this->input->get('limit');
                $offset = $this->input->get('offset');
                
                echo json_encode($this->Mx_connect->get_ach_funding($this->_get_account_numbers(), $start_date, $end_date, $sort, $limit, $offset));
            }
            else {
                echo json_encode(array('warning' => true, 'message' => lang('mx_setup_warning')));
            }
        }
        else {
            echo json_encode(array('success' => false));
        }
    }
    
    function _get_account_numbers()
    {
        $account_numbers = [];
        
        if ($this->Location->get_info_for_key('coreclear_merchant_id')) {
            $account_numbers[] = $this->Location->get_info_for_key('coreclear_merchant_id');
        }
        
        return $account_numbers;
    }
    
    function _is_coreclear_enabled()
    {
        return $this->Location->get_info_for_key('enable_credit_card_processing') && $this->Location->get_info_for_key('credit_card_processor') == 'coreclear2';
    }
    
    function _is_coreclear_fully_setup()
    {
        return $this->Location->get_info_for_key('blockchyp_api_key') && $this->Location->get_info_for_key('blockchyp_bearer_token') && $this->Location->get_info_for_key('blockchyp_signing_key') && $this->Location->get_info_for_key('coreclear_merchant_id');
    }
}

?>