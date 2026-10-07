<?php
require_once 'Secure_area.php';
class Creditreports extends Secure_area {
 public function __construct() { parent::__construct(); $this->load->model('Credit_reports'); if (!$this->Credit_reports->scope($this->session->userdata('person_id'))) { show_error('Sin permiso de propietario',403); exit; } }
 public function index() {
  $person=$this->session->userdata('person_id');
  if (!$this->session->userdata('credit_reports_csrf')) $this->session->set_userdata('credit_reports_csrf',bin2hex(random_bytes(32)));
  $ready=$this->Credit_reports->ready();
  $schedule=$ready?$this->db->get_where('credit_report_schedules',array('person_id'=>$person))->row_array():array();
  $job=$ready?$this->db->where('person_id',$person)->order_by('id','DESC')->limit(1)->get('credit_report_jobs')->row_array():array();
  $this->output->set_header('Cache-Control: no-store');
  $this->load->view('credit_reports/settings',array('ready'=>$ready,'schedule'=>$schedule,'job'=>$job,'email'=>$this->Employee->get_info($person)->email,'token'=>$this->session->userdata('credit_reports_csrf'),'timezone'=>$this->Location->get_info_for_key('timezone') ?: date_default_timezone_get()));
 }
 public function save() {
  $token=$this->session->userdata('credit_reports_csrf'); $posted=$this->input->post('token');
  if ($this->input->method()!=='post' || !$token || !is_string($posted) || !hash_equals($token,$posted)) {show_error('Sesión vencida. Recarga la página.',403);return;}
  try {$this->Credit_reports->save_schedule($this->session->userdata('person_id'),$this->input->post(NULL,TRUE)); $this->session->set_flashdata('credit_report_result','Programación guardada.');}
  catch (RuntimeException $error) {$this->session->set_flashdata('credit_report_result',$error->getMessage());}
  redirect('creditreports');
 }
 public function test($kind='') {
  $this->output->set_header('Cache-Control: no-store')->set_content_type('application/json');
  $token=$this->session->userdata('credit_reports_csrf'); $posted=$this->input->post('token');
  if ($this->input->method()!=='post' || !$token || !is_string($posted) || !hash_equals($token,$posted)) {
   $this->output->set_status_header(403)->set_output(json_encode(array('success'=>FALSE,'message'=>'Sesión vencida. Recarga la página.'))); return;
  }
  $last=(int)$this->session->userdata('credit_report_test_at');
  if (time()-$last<30) { $this->output->set_status_header(429)->set_output(json_encode(array('success'=>FALSE,'message'=>'Espera 30 segundos antes de repetir la prueba.'))); return; }
  $this->session->set_userdata('credit_report_test_at',time());
  try { $message=$this->Credit_reports->send_test($this->session->userdata('person_id'),$kind); $success=TRUE; }
  catch (RuntimeException $error) { $message=$error->getMessage(); $success=FALSE; }
  catch (Throwable $error) { log_message('error','Credit report manual test failed.'); $message='No se pudo completar la prueba. Revisa el servidor, el PDF y los ajustes del correo.'; $success=FALSE; }
  $this->output->set_output(json_encode(array('success'=>$success,'message'=>$message)));
 }
 public function preview() {
  $this->output->set_header('Cache-Control: no-store');
  try {$pdf=$this->Credit_reports->pdf($this->Credit_reports->capture($this->session->userdata('person_id')));}
  catch (Throwable $error) {show_error('No se pudo generar el PDF. Revisa la configuración del servidor.',500);return;}
  $this->output->set_content_type('application/pdf')->set_header('Content-Disposition: attachment; filename="creditos.pdf"')->set_output($pdf);
 }
}
