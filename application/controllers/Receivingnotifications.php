<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Receivingnotifications extends MY_Controller
{
    public function cron($base_url='')
    {
        if (!$this->input->is_cli_request()) { show_error('Solo CLI',403); return; }
        if (!filter_var($base_url,FILTER_VALIDATE_URL) || !in_array(parse_url($base_url,PHP_URL_SCHEME),array('https'),TRUE)) { echo "Indica la URL del sistema para crear enlaces de revisión.\n"; return; }
        $this->config->set_item('base_url',$base_url);
        $this->load->model('Receiving_push');
        // Recover notifications not queued because of an interrupted submission.
        if ($this->Receiving_push->ready()) {
            $requests=$this->db->dbprefix('receiving_requests');
            $missing=$this->db->query("SELECT r.id FROM `$requests` r WHERE r.status='pending' ORDER BY r.id")->result_array();
            foreach ($missing as $request) $this->Receiving_push->enqueue($request['id']);
            $sent=$this->Receiving_push->deliver(20);
            echo 'Avisos enviados: '.(int)$sent."\n";
        }
    }
}
