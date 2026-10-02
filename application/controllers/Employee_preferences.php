<?php
require_once("Secure_area.php");

class Employee_preferences extends Secure_area {
	function __construct() {
		parent::__construct();
		$this->lang->load('module');

		$this->load->model('Employee_appconfig');

	}

	function get_transaction_filters() {
		echo $this->Employee_appconfig->get('coreclear_transaction_filters');
	}

	function save_transaction_filters() {
		if ($this->input->post('transaction_filters')) {
			$this->Employee_appconfig->save('coreclear_transaction_filters', $this->input->post('transaction_filters'));
		} else {
			$this->Employee_appconfig->delete('coreclear_transaction_filters');
		}
	}
}

?>
