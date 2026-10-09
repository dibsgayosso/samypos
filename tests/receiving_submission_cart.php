<?php
// Execute the submission cleanup from the controller against a session-backed cart.
$source=file_get_contents(dirname(__DIR__).'/application/controllers/Receivings.php');
$start=strpos($source,"$"."this->session->unset_userdata('receiving_submission_key');");
$end=strpos($source,"$"."this->session->set_flashdata('supervisor_result'",$start);
$cleanup=substr($source,$start,$end-$start);
class SubmissionSession {
 public $data=array('receiving_submission_key'=>'submitted');
 function unset_userdata($key){unset($this->data[$key]);}
}
class SubmissionCart {
 public $items=array('captured item'),$receiving_id=12,$session;
 function destroy(){$this->items=array();$this->receiving_id=NULL;}
 function save(){$this->session->data['receiving']=array('items'=>$this->items,'receiving_id'=>$this->receiving_id);}
}
class SubmissionFixture {
 public $session,$cart;
 function run($cleanup){
  $this->session=new SubmissionSession();$this->cart=new SubmissionCart();$this->cart->session=$this->session;
  $this->cart->save();eval($cleanup);
  if($this->session->data['receiving']['items']!==array() || $this->session->data['receiving']['receiving_id']!==NULL || isset($this->session->data['receiving_submission_key']))throw new RuntimeException('Submitted cart survived in session');
 }
}
(new SubmissionFixture())->run($cleanup);
echo "Submitted receiving cart is persisted empty before redirect\n";
