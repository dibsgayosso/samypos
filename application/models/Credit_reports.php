<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Credit_reports extends CI_Model {
 public function ready() {return $this->db->table_exists('credit_report_schedules') && $this->db->table_exists('credit_report_jobs');}
 public static function next_run($weekday,$time,$timezone,$after,$weeks=1,$first=FALSE) {
  $zone=new DateTimeZone($timezone); $now=(new DateTimeImmutable('@'.$after))->setTimezone($zone);
  $candidate=$now->setTime((int)substr($time,0,2),(int)substr($time,3,2));
  $days=((int)$weekday-(int)$now->format('N')+7)%7; $candidate=$candidate->modify('+'.$days.' days');
  if ($candidate->getTimestamp()<=$after) $candidate=$candidate->modify('+'.($first?1:$weeks).' weeks');
  return $candidate->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
 }
 public function save_schedule($person,$data) {
  if (!$this->ready()) throw new RuntimeException('Aplica la migración de informes de crédito.');
  $weekday=filter_var($data['weekday'] ?? NULL,FILTER_VALIDATE_INT); $weeks=filter_var($data['interval_weeks'] ?? NULL,FILTER_VALIDATE_INT);
  $time=$data['send_time'] ?? ''; $zone=$data['timezone'] ?? '';
  if ($weekday<1 || $weekday>7 || $weeks<1 || $weeks>4 || !is_string($time) || !is_string($zone) || !preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/D',$time) || !in_array($zone,timezone_identifiers_list(),TRUE)) throw new RuntimeException('Revisa el día, hora, periodicidad y zona horaria.');
  $employee=$this->Employee->get_info($person);
  if (!$this->scope($person) || !filter_var($employee->email,FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Configura tu correo en Empleados y el permiso de propietario.');
  $table=$this->db->dbprefix('credit_report_schedules');
  $this->db->query("INSERT INTO `$table` (person_id,enabled,weekday,send_time,interval_weeks,timezone,next_run) VALUES (?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE enabled=VALUES(enabled),weekday=VALUES(weekday),send_time=VALUES(send_time),interval_weeks=VALUES(interval_weeks),timezone=VALUES(timezone),next_run=VALUES(next_run),revision=revision+1",array($person,empty($data['enabled'])?0:1,$weekday,$time,$weeks,$zone,self::next_run($weekday,$time,$zone,time(),$weeks,TRUE)));
 }
 public function scope($person) {
  $this->load->model('Owner_dashboard'); $employee=$this->Employee->get_info($person);
  return !$employee || $employee->deleted || $employee->inactive ? array() : $this->Owner_dashboard->allowed_locations($person);
 }
 public static function analysis($current,$previous) {
  if ($previous===NULL) return array('difference'=>NULL,'percent'=>NULL,'text'=>'Sin historial suficiente para comparar.');
  $difference=round($current-$previous,2);
  return array('difference'=>$difference,'percent'=>$previous>0?round(100*$difference/$previous,2):NULL,
   'text'=>abs($difference)<.005?'La cuenta no cambió.':($difference>0?'La cuenta aumentó.':'La cuenta disminuyó.'));
 }
 public static function older_than_30($date,$asof) {
  if (!$date || $date==='0000-00-00') return FALSE;
  $due=DateTimeImmutable::createFromFormat('!Y-m-d',substr($date,0,10)); $today=DateTimeImmutable::createFromFormat('!Y-m-d',$asof);
  return $due && $today && $due<$today && (int)$due->diff($today)->format('%a')>30;
 }
 public function capture($person) {
  $this->db->query('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
  $this->db->trans_begin();
  try {
   $report=$this->build_capture($person);
   if (!$this->db->trans_status()) throw new RuntimeException('No se pudo capturar el informe.');
   $this->db->trans_commit(); return $report;
  } catch (Throwable $error) {$this->db->trans_rollback(); throw $error;}
 }
 protected function build_capture($person) {
  $locations=$this->scope($person); if (!$locations) throw new RuntimeException('Sin sucursales autorizadas.');
  $captured=date('Y-m-d H:i:s'); $previous=date('Y-m-d H:i:s',strtotime('-7 days'));
  $report=array('captured'=>$captured,'timezone'=>date_default_timezone_get(),'previous_date'=>$previous,'branches'=>array(),'scope'=>$locations);
  $customers=$this->db->dbprefix('customers'); $people=$this->db->dbprefix('people'); $ledger=$this->db->dbprefix('store_accounts');
  foreach ($locations as $id) {
   $location=$this->Location->get_info($id); if ($location->deleted) continue;
   // Latest balance uses date + sequence to disambiguate same-second movements.
   $rows=$this->db->query("SELECT c.person_id,c.balance,CONCAT(p.first_name,' ',p.last_name) AS name, (SELECT a.balance FROM `$ledger` a WHERE a.customer_id=c.person_id AND a.date<=? ORDER BY a.date DESC,a.sno DESC LIMIT 1) AS previous_balance FROM `$customers` c JOIN `$people` p ON p.person_id=c.person_id WHERE c.location_id=? AND c.deleted=0 ORDER BY p.first_name,p.last_name",array($previous,$id))->result_array();
   $total=0; $old=0; $unknown=0; $clients=array();
   foreach ($rows as $row) {
    $balance=max(0,(float)$row['balance']); $total+=$balance;
    if ($row['previous_balance']===NULL) {if ($balance>0) $unknown++;} else $old+=max(0,(float)$row['previous_balance']);
    if ($balance>0 || (float)$row['previous_balance']>0) $clients[]=array('name'=>$row['name'],'balance'=>$balance,'previous'=>$row['previous_balance']===NULL?NULL:max(0,(float)$row['previous_balance']));
   }
   $overdue=array(); $invoices_ready=$this->db->table_exists('customer_invoices');
   if ($invoices_ready) {
    $invoices=$this->db->dbprefix('customer_invoices');
    $invoice_rows=$this->db->query("SELECT i.invoice_id,i.customer_id,i.due_date,i.balance,CONCAT(p.first_name,' ',p.last_name) AS name FROM `$invoices` i JOIN `$customers` c ON c.person_id=i.customer_id JOIN `$people` p ON p.person_id=c.person_id WHERE i.location_id=? AND i.deleted=0 AND c.deleted=0 AND c.balance>0 AND i.balance>0 ORDER BY i.due_date,i.invoice_id",array($id))->result_array();
    foreach ($invoice_rows as $invoice) if (self::older_than_30($invoice['due_date'],date('Y-m-d'))) {
     $invoice['days']=(new DateTimeImmutable($invoice['due_date']))->diff(new DateTimeImmutable(date('Y-m-d')))->days; $overdue[]=$invoice;
    }
   }
   $report['branches'][]=array('id'=>$id,'name'=>$location->name,'total'=>$total,'previous'=>$unknown?NULL:$old,'unknown'=>$unknown,'clients'=>$clients,'overdue'=>$overdue,'invoices_ready'=>$invoices_ready,'analysis'=>self::analysis($total,$unknown?NULL:$old));
  }
  return $report;
 }
 public function pdf($report) {
  require_once APPPATH.'libraries/tcpdf/tcpdf.php';
  $pdf=new TCPDF('P','mm','LETTER',TRUE,'UTF-8',FALSE); $pdf->setPrintHeader(FALSE); $pdf->setPrintFooter(TRUE);
  $pdf->SetCreator('SECOYT'); $pdf->SetTitle('Informe de créditos por sucursal'); $pdf->SetMargins(14,14,14); $pdf->SetAutoPageBreak(TRUE,16); $pdf->SetFont('dejavusans','',9);
  foreach ($report['branches'] as $branch) {
   $pdf->AddPage(); $html=$this->load->view('credit_reports/pdf',array('report'=>$report,'branch'=>$branch),TRUE); $pdf->writeHTML($html);
  }
  return $pdf->Output('creditos.pdf','S');
 }
 public function run() {
  if (!$this->ready()) return 0;
  // Single worker per database. An interrupted process releases its connection lock.
  $lock='creditreports_'.substr(hash('sha256',$this->db->database),0,40);
  $held=$this->db->query('SELECT GET_LOCK(?,0) AS held',array($lock))->row_array(); if (empty($held['held'])) return 0;
  $sent=0;
  try {
   $now=gmdate('Y-m-d H:i:s');
   foreach ($this->db->where('enabled',1)->where('next_run <=',$now)->get('credit_report_schedules')->result_array() as $schedule) {
    $employee=$this->Employee->get_info($schedule['person_id']);
    if (!$this->scope($schedule['person_id']) || !filter_var($employee->email,FILTER_VALIDATE_EMAIL)) continue;
    $this->db->trans_start(); $table=$this->db->dbprefix('credit_report_jobs');
    $this->db->query("INSERT IGNORE INTO `$table` (person_id,scheduled_for,revision,recipient,next_attempt) VALUES (?,?,?,?,?)",array($schedule['person_id'],$schedule['next_run'],$schedule['revision'],$employee->email,$now));
    $after=strtotime($schedule['next_run'].' UTC');
    $next=self::next_run($schedule['weekday'],$schedule['send_time'],$schedule['timezone'],$after,$schedule['interval_weeks']);
    // Skip obsolete missed occurrences after server downtime; send one recovery report.
    while ($next<=$now) $next=self::next_run($schedule['weekday'],$schedule['send_time'],$schedule['timezone'],strtotime($next.' UTC'),$schedule['interval_weeks']);
    $this->db->where('person_id',$schedule['person_id'])->where('revision',$schedule['revision'])->update('credit_report_schedules',array('next_run'=>$next)); $this->db->trans_complete();
   }
   foreach ($this->db->where('status','pending')->where('next_attempt <=',$now)->order_by('id')->limit(10)->get('credit_report_jobs')->result_array() as $job) {
    $schedule=$this->db->get_where('credit_report_schedules',array('person_id'=>$job['person_id']))->row_array();
    $employee=$this->Employee->get_info($job['person_id']); $scope=$this->scope($job['person_id']);
    if (!$schedule || !$schedule['enabled'] || $schedule['revision']!=$job['revision'] || !$scope || strcasecmp($employee->email,$job['recipient'])!==0) { $this->db->where('id',$job['id'])->update('credit_report_jobs',array('status'=>'canceled')); continue; }
    $zone=date_default_timezone_get(); $ok=FALSE;
    try {
     date_default_timezone_set($schedule['timezone']);
     $report=$job['payload']?json_decode($job['payload'],TRUE):$this->capture($job['person_id']);
     if (!$report || array_diff($report['scope'],$scope)) { $this->db->where('id',$job['id'])->update('credit_report_jobs',array('status'=>'canceled')); continue; }
     if (!$job['payload']) $this->db->where('id',$job['id'])->update('credit_report_jobs',array('payload'=>json_encode($report)));
     $pdf=$this->pdf($report);
     $current=$this->db->get_where('credit_report_schedules',array('person_id'=>$job['person_id']))->row_array();
     $current_employee=$this->Employee->get_info($job['person_id']);
     if (!$current || !$current['enabled'] || $current['revision']!=$job['revision'] || array_diff($report['scope'],$this->scope($job['person_id'])) || strcasecmp($current_employee->email,$job['recipient'])!==0) {
      $this->db->where('id',$job['id'])->update('credit_report_jobs',array('status'=>'canceled')); continue;
     }
     $from=$this->config->item('smtp_user');
     if (!filter_var($from,FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Configura el remitente SMTP.');
     $this->load->library('email'); $this->email->clear(TRUE); $this->email->initialize(array('mailtype'=>'html','charset'=>'utf-8'));
     $this->email->from($from,$this->config->item('company')); $this->email->to($job['recipient']); $this->email->subject('Créditos por sucursal · '.$report['captured']);
     $this->email->message('<p>Adjuntamos el informe de créditos, comparación semanal y documentos con más de 30 días de atraso.</p><p>Generado automáticamente para Samy y programado por Secoyt.</p>');
     $this->email->attach($pdf,'attachment','creditos-'.substr($report['captured'],0,10).'.pdf','application/pdf'); $ok=$this->email->send();
    } catch (Throwable $error) { log_message('error','Credit report generation or SMTP delivery failed.'); }
    finally {date_default_timezone_set($zone);}
    $attempts=(int)$job['attempts']+1;
    $this->db->where('id',$job['id'])->update('credit_report_jobs',array('attempts'=>$attempts,'status'=>$ok?'sent':($attempts>=10?'failed':'pending'),'sent_at'=>$ok?gmdate('Y-m-d H:i:s'):NULL,'last_error'=>$ok?NULL:'Revisar PDF, SMTP y configuración.','next_attempt'=>gmdate('Y-m-d H:i:s',time()+min(3600,60*pow(2,min(6,$attempts)))))); if ($ok) $sent++;
   }
  } finally { $this->db->query('SELECT RELEASE_LOCK(?)',array($lock)); }
  return $sent;
 }
}
