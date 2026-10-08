<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Creditreportmailer extends MY_Controller {
 public function cron() {
  if (!$this->input->is_cli_request()) {show_error('Solo CLI',403);return;}
  $this->load->model('Credit_reports'); echo 'Informes enviados: '.(int)$this->Credit_reports->run()."\n";
 }
}
