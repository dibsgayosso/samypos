<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Receiving_notifications extends CI_Model
{
    public function ready() { return $this->db->table_exists('receiving_notification_queue'); }
    private function eligible($employee,$request)
    {
        return $employee && !$employee->deleted && !$employee->inactive
            && (int)$employee->person_id !== (int)$request['employee_id']
            && filter_var($employee->email,FILTER_VALIDATE_EMAIL)
            && in_array($request['location_id'],$this->Employee->get_authenticated_location_ids($employee->person_id))
            && $this->Employee->has_module_permission('receivings',$employee->person_id)
            && $this->Employee->has_module_action_permission('receivings','authorize_receivings',$employee->person_id,$request['location_id']);
    }
    public function enqueue($id)
    {
        if (!$this->ready()) return FALSE;
        $request=$this->db->get_where('receiving_requests',array('id'=>$id,'status'=>'pending'))->row_array();
        if (!$request) return FALSE;
        $seen=array();
        $employees=$this->db->select('person_id')->from('employees')->where('deleted',0)->where('inactive',0)->get()->result_array();
        foreach ($employees as $row) {
            $employee=$this->Employee->get_info($row['person_id']);
            if (!$this->eligible($employee,$request) || isset($seen[strtolower($employee->email)])) continue;
            $seen[strtolower($employee->email)]=TRUE;
            $table=$this->db->dbprefix('receiving_notification_queue');
            $this->db->query("INSERT IGNORE INTO `$table` (request_id,recipient_id,review_url,next_attempt_at) VALUES (?,?,?,?)",
                array($id,$employee->person_id,site_url('home/receiving_request/'.$id),gmdate('Y-m-d H:i:s')));
        }
        return count($seen);
    }
    public function summary($id)
    {
        if (!$this->ready()) return 'Falta aplicar la migración de avisos por correo.';
        $rows=$this->db->get_where('receiving_notification_queue',array('request_id'=>$id))->result_array();
        if (!$rows) return 'Sin supervisor con correo válido configurado para esta sucursal.';
        $sent=0; $pending=0;
        foreach ($rows as $row) { if ($row['status']==='sent') $sent++; elseif (in_array($row['status'],array('pending','sending'))) $pending++; }
        if ($sent) return 'Correo enviado a '.$sent.' supervisor(es).';
        return $pending ? 'Aviso en cola; se reintentará si el correo falla.' : 'No se entregó el correo. Revisar SMTP y permisos del supervisor.';
    }
    public function deliver($limit=10,$request_id=NULL)
    {
        if (!$this->ready()) return 0;
        $now=gmdate('Y-m-d H:i:s');
        // Recover workers interrupted after claiming a message. Delivery is at least once.
        $this->db->where('status','sending')->where('claimed_at <',gmdate('Y-m-d H:i:s',time()-300))
            ->update('receiving_notification_queue',array('status'=>'pending'));
        $this->db->from('receiving_notification_queue')->where('status','pending')->where('next_attempt_at <=',$now);
        if ($request_id!==NULL) $this->db->where('request_id',$request_id);
        $rows=$this->db->order_by('id')->limit(max(1,min(50,(int)$limit)))->get()->result_array();
        $sent=0;
        foreach ($rows as $row) {
            $this->db->where('id',$row['id'])->where('status','pending')->update('receiving_notification_queue',array('status'=>'sending','claimed_at'=>$now));
            if ($this->db->affected_rows()!==1) continue;
            $request=$this->db->get_where('receiving_requests',array('id'=>$row['request_id']))->row_array();
            $employee=$this->Employee->get_info($row['recipient_id']);
            if (!$request || $request['status']!=='pending' || !$this->eligible($employee,$request)) {
                $this->db->where('id',$row['id'])->update('receiving_notification_queue',array('status'=>'canceled')); continue;
            }
            $ok=FALSE;
            try {
                $from=$this->config->item('smtp_user') ?: $this->Location->get_info_for_key('email',$request['location_id']);
                if (filter_var($from,FILTER_VALIDATE_EMAIL)) {
                    $this->load->library('email');
                    $this->email->clear(TRUE);
                    $this->email->initialize(array('mailtype'=>'html','charset'=>'utf-8'));
                    $this->email->from($from,$this->config->item('company'));
                    $this->email->to($employee->email);
                    $this->email->subject('Mercancía pendiente de autorización #'.$request['id']);
                    $location=$this->Location->get_info($request['location_id']);
                    $requester=$this->Employee->get_info($request['employee_id']);
                    $body='<h2>Mercancía pendiente de autorización</h2><p>Sucursal: '.html_escape($location->name).'<br>Capturó: '.html_escape(trim($requester->first_name.' '.$requester->last_name)).'<br>Solicitud #'.(int)$request['id'].'<br>Importe: '.html_escape(to_currency($request['total'])).'</p><p>Verifica la mercancía física, cantidades y costos antes de autorizar.</p><p><a href="'.html_escape($row['review_url']).'">Revisar solicitud</a></p><p>Debes iniciar sesión. El enlace no autoriza automáticamente. El inventario permanece sin cambios hasta la autorización.</p>';
                    $this->email->message($body);
                    $ok=$this->email->send();
                }
            } catch (Throwable $error) { log_message('error','Receiving supervisor notification delivery failed'); }
            $attempts=(int)$row['attempts']+1;
            $fields=array('attempts'=>$attempts,'status'=>$ok ? 'sent' : ($attempts>=10 ? 'failed' : 'pending'),
                'sent_at'=>$ok ? gmdate('Y-m-d H:i:s') : NULL,'last_error'=>$ok ? NULL : 'SMTP no entregó el aviso; revisar configuración.',
                'next_attempt_at'=>gmdate('Y-m-d H:i:s',time()+min(3600,60*pow(2,min(6,$attempts-1)))));
            $this->db->where('id',$row['id'])->update('receiving_notification_queue',$fields);
            if ($ok) $sent++;
        }
        return $sent;
    }
}
