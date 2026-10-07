<?php
require __DIR__.'/owner_dashboard.php';
require APPPATH.'models/Credit_reports.php';
check(Credit_reports::next_run(1,'08:00','America/Mexico_City',strtotime('2026-10-05 14:00:00 UTC'),1)==='2026-10-12 14:00:00','Weekly run at local Monday 08:00');
check(Credit_reports::next_run(1,'08:00','America/Mexico_City',strtotime('2026-10-05 14:00:00 UTC'),2)==='2026-10-19 14:00:00','Two-week periodicity must skip intervening week');
check(Credit_reports::next_run(5,'08:00','America/Mexico_City',strtotime('2026-10-07 14:00:00 UTC'),4,TRUE)==='2026-10-09 14:00:00','Initial schedule uses next selected weekday');
check(Credit_reports::next_run(7,'08:00','America/New_York',strtotime('2026-10-25 12:00:00 UTC'),1)==='2026-11-01 13:00:00','Keep local hour across daylight-saving transition');
check(Credit_reports::analysis(80,100)['difference']===-20.0,'Credit decrease');
check(Credit_reports::analysis(120,100)['percent']===20.0,'Credit increase percentage');
check(Credit_reports::analysis(100,0)['percent']===NULL,'Never divide by zero');
check(Credit_reports::analysis(100,NULL)['difference']===NULL,'Missing history must not imply an increase from zero');
check(!Credit_reports::older_than_30('2026-09-07','2026-10-07'),'Exactly 30 days is not more than 30');
check(Credit_reports::older_than_30('2026-09-06','2026-10-07'),'31 overdue days flagged');
check(!Credit_reports::older_than_30(NULL,'2026-10-07'),'No due date must not be classified as overdue');
$branch=array('name'=>'Sucursal <script>','total'=>100,'previous'=>200,'analysis'=>Credit_reports::analysis(100,200),'clients'=>array(array('name'=>'Cliente <script>','balance'=>100,'previous'=>200)),'invoices_ready'=>TRUE,'overdue'=>array(array('name'=>'Cliente <script>','invoice_id'=>8,'due_date'=>'2026-09-01','days'=>36,'balance'=>50)));
$report=array('captured'=>'2026-10-07 08:00:00','previous_date'=>'2026-09-30 08:00:00','branches'=>array($branch));
$service=new Credit_reports();
$service->config=new class { function item($key) {return 'Samy';} };
$service->load=new class($service) {
 private $service; function __construct($service) {$this->service=$service;}
 function view($name,$data,$return) { extract($data); $this->config=$this->service->config; ob_start();require APPPATH.'views/'.$name.'.php';return ob_get_clean(); }
};
$pdf=$service->pdf($report);
check(substr($pdf,0,5)==='%PDF-' && strlen($pdf)>1000,'Generate an actual PDF from the existing runtime');
file_put_contents('/tmp/credit-report-test.pdf',$pdf);
$rendered=$service->load->view('credit_reports/pdf',array('report'=>$report,'branch'=>$branch),TRUE);
check(strpos($rendered,'<script>')===FALSE && strpos($rendered,'&lt;script&gt;')!==FALSE,'Escape customer and branch names in PDF');
echo "Credit reporting schedules, comparison, overdue boundaries and PDF generation tests passed\n";
class CreditWorkerDB {
 public $database='fixture',$job,$schedule;
 function __construct($report) {
  $this->schedule=array('person_id'=>10,'enabled'=>1,'revision'=>1,'timezone'=>'America/Mexico_City');
  $report['scope']=array(1);
  $this->job=array('id'=>1,'person_id'=>10,'revision'=>1,'recipient'=>'owner@example.test','payload'=>json_encode($report),'status'=>'pending','attempts'=>0,'next_attempt'=>gmdate('Y-m-d H:i:s'));
 }
 function table_exists($table) {return TRUE;}
 function query($sql,$params) {return new ResultStub(array('held'=>1));}
 function where($key,$value) {return $this;} function order_by($key) {return $this;} function limit($n) {return $this;}
 function get($table) {
  $rows=$table==='credit_report_jobs' && $this->job['status']==='pending'?array($this->job):array();
  return new class($rows) {private $rows; function __construct($rows) {$this->rows=$rows;} function result_array() {return $this->rows;}};
 }
 function get_where($table,$where) {return new ResultStub($this->schedule);}
 function update($table,$fields) {$this->job=array_merge($this->job,$fields);return TRUE;}
}
class CreditEmailSpy {
 public $calls=0,$ok=FALSE,$recipient;
 function clear($all){} function initialize($options){} function from($email,$name){} function to($email){$this->recipient=$email;}
 function subject($text){} function message($html){} function attach($bytes,$type,$name,$mime){check($mime==='application/pdf' && substr($bytes,0,5)==='%PDF-','Attach actual PDF bytes');}
 function send(){$this->calls++;return $this->ok;}
}
class CreditWorkerSpy extends Credit_reports {
 public $locations=array(1),$revoke_after_pdf=FALSE;
 function scope($person) {return $this->locations;}
 function pdf($report) {if ($this->revoke_after_pdf) $this->locations=array(); return '%PDF-test';}
}
function credit_worker($report) {
 $s=new CreditWorkerSpy();$s->db=new CreditWorkerDB($report);$s->email=new CreditEmailSpy();
 $s->Employee=new class {function get_info($person) {return (object)array('email'=>'owner@example.test');}};
 $s->load=new class {function library($name){}};
 $s->config=new class {function item($key){return $key==='smtp_user'?'noreply@example.test':'Samy';}};return $s;
}
$s=credit_worker($report);check($s->run()===0 && $s->db->job['attempts']===1 && $s->db->job['status']==='pending','SMTP failure persists retry');
check(strtotime($s->db->job['next_attempt'].' UTC')>time(),'Retry has future UTC date');
$s->email->ok=TRUE;check($s->run()===1 && $s->db->job['status']==='sent' && $s->email->recipient==='owner@example.test','Successful SMTP marks sent to owner');
$calls=$s->email->calls;check($s->run()===0 && $s->email->calls===$calls,'Sent job is not sent again');
$s=credit_worker($report);$s->db->schedule['enabled']=0;check($s->run()===0 && $s->email->calls===0 && $s->db->job['status']==='canceled','Disabled schedule cancels pending report');
$s=credit_worker($report);$s->locations=array(2);check($s->run()===0 && $s->email->calls===0,'Branch access revoked prevents delivery');
$s=credit_worker($report);$s->db->schedule['revision']=2;check($s->run()===0 && $s->email->calls===0,'Changed schedule cancels older occurrence');
$s=credit_worker($report);$s->revoke_after_pdf=TRUE;check($s->run()===0 && $s->email->calls===0,'Revalidate permissions after PDF generation before SMTP');
echo "Credit report recipient, permission revocation and retry tests passed\n";
