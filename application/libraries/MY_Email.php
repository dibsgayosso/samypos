<?php

require __DIR__ . '/google_api_client/vendor/autoload.php';
define('GMAIL_API_FILE_PATH', __DIR__ . '/../libraries/google_api_client/');
use Google\Client;
use Google\Service\Gmail;


class MY_Email extends CI_Email 
{	
	public $gmail_api_data = array();
	public function __construct($config = array())
	{
		if ($this->is_email_configured_in_store_config())
		{
			$CI =& get_instance();
			
			$email_config = array(
				'smtp_crypto'=>$CI->config->item('smtp_crypto') ? $CI->config->item('smtp_crypto') : '',
				'protocol'=>$CI->config->item('protocol'),
				'smtp_host'=>$CI->config->item('smtp_host'),
				'smtp_user'=>$CI->config->item('smtp_user'),
				'smtp_pass'=>$CI->config->item('smtp_pass'),
				'smtp_port'=>$CI->config->item('smtp_port'),
				'email_charset'=>$CI->config->item('email_charset') ? $CI->config->item('email_charset') : 'utf-8',
				'newline'=>$CI->config->item('newline') ? $CI->config->item('newline') : "\n",
				'crlf'=>$CI->config->item('crlf') ? $CI->config->item('crlf') : "\n",
				'smtp_timeout'=>$CI->config->item('smtp_timeout') ? $CI->config->item('smtp_timeout') : 5,
			);
			
			parent::__construct($email_config);
		}
		else
		{
			parent::__construct($config);
		}
	}

	private function is_email_configured_in_store_config()
	{
		$required = array(
			'smtp_host','smtp_user','smtp_pass','smtp_port'
		);
		
		$CI =& get_instance();
		foreach($required as $require_key)
		{
			if (!$CI->config->item($require_key))
			{
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	public function test_email($email)
	{
		$CI =& get_instance();
		$this->from($CI->Location->get_info_for_key('email') ? $CI->Location->get_info_for_key('email') : $CI->config->item('branding')['no_reply_email'], $CI->config->item('company'));
		$this->to($email); 
		
		$this->subject(lang('common_test'));
		$this->message(lang('common_this_is_a_test_email'));	
		if (!$this->send())
		{
			  ob_start();
			  echo $this->print_debugger();
				$output = ob_get_clean();
				return '<pre>'.auto_link(strip_tags($output),'url',TRUE).'</pre>';
		}
		
		return TRUE;
	}

	public function from($from, $name = '', $return_path = NULL)
	{
		$CI =& get_instance();
		
		if (is_on_phppos_host())
		{
			$this->reply_to($from, $name);
			$from = $CI->config->item('branding')['no_reply_email'];
		}
		elseif ($this->is_email_configured_in_store_config() && $CI->config->item('smtp_host')=='smtp.gmail.com')
		{	
			$from = $CI->config->item('smtp_user');
		}

		if ($this->is_configured_to_use_gmail_api()){
			$this->gmail_api_data['from'] = $from;
			$this->gmail_api_data['name'] = $name;
			$this->gmail_api_data['return_path'] = $return_path;
		}else{
			parent::from($from,$name,$return_path);
		}
	}
	
	/**
	 * SMTP Connect
	 *
	 * @return	string
	 */
	protected function _smtp_connect()
	{
		if (is_resource($this->_smtp_connect))
		{
			return TRUE;
		}
		$context = stream_context_create();
		$result = stream_context_set_option($context, 'ssl', 'verify_peer', false);
		$ssl = ($this->smtp_crypto === 'ssl') ? 'ssl://' : '';
		
		$this->_smtp_connect = stream_socket_client($ssl.$this->smtp_host . ':'.$this->smtp_port, $errno, $errstr, $this->smtp_timeout, STREAM_CLIENT_CONNECT, $context);
		if ( ! is_resource($this->_smtp_connect))
		{
			$this->_set_error_message('lang:email_smtp_error', $errno.' '.$errstr);
			return FALSE;
		}

		stream_set_timeout($this->_smtp_connect, $this->smtp_timeout);
		$this->_set_error_message($this->_get_smtp_data());


		if ($this->smtp_crypto === 'tls')
		{
			$this->_send_command('hello');
			$this->_send_command('starttls');

			$crypto = stream_socket_enable_crypto($this->_smtp_connect, TRUE, STREAM_CRYPTO_METHOD_TLS_CLIENT);

			if ($crypto !== TRUE)
			{
				$this->_set_error_message('lang:email_smtp_error', $this->_get_smtp_data());
				return FALSE;
			}
		}

		return $this->_send_command('hello');
	}
	
	private function is_configured_to_use_gmail_api()
	{
		//Check if we have gmail_access_token set
		$CI =& get_instance();
		if($CI->config->item('email_provider') == "Gmail API" && $CI->config->item("gmail_client_id") && $CI->config->item("gmail_api_token") && $CI->config->item("gmail_client_secret")){
			return true;
		}
		return false;
	}

	function send($auto_clear = TRUE)
	{
		if ($this->is_configured_to_use_gmail_api())
		{
			$CI =& get_instance();
			
			$client = $this->getClient();
			
			if (!$client)
			{
				return false;
			}
			
			$access_token = $CI->config->item("gmail_api_token");
			$google_api_token = json_decode($access_token, true);
			
			if ($client->isAccessTokenExpired())
			{
				$client->refreshToken($google_api_token['refresh_token']);
				$access_token=json_encode($client->getAccessToken());
				$CI->config->set_item('gmail_api_token',$access_token);
				$CI->Appconfig->save('gmail_api_token',$access_token);
			}
			
			if($CI->config->item("gmail_api_token")){
				$ret = $this->send_email_by_gmail_api(
					$this->gmail_api_data['to'],
					$this->gmail_api_data['subject'],
					$this->gmail_api_data['message'],
					$this->gmail_api_data['from'],
					$this->gmail_api_data['name'],
					isset($this->gmail_api_data['Cc'])?$this->gmail_api_data['Cc'] : false,
					isset($this->gmail_api_data['Bcc'])?$this->gmail_api_data['Bcc'] : false,
					isset($this->gmail_api_data['attachments'])?$this->gmail_api_data['attachments']: false
				);
				return $ret;
			}
		}
		else
		{
			return parent::send($auto_clear);
		}
	}

	/**
	* @param $service Google_Service_Gmail an authorized Gmail API service instance.
	* @param $userId string User's email address
	* @param $message Google_Service_Gmail_Message
	* @return null|Google_Service_Gmail_Message
	*/
	function sendMessage($service, $userId, $message) {
		try {
			$message = $service->users_messages->send($userId, $message);
			// print 'Message with ID: ' . $message->getId() . ' sent.';
			return true;
		} catch (Exception $e) {
			// print 'An error occurred: ' . $e->getMessage();
		}
		return false;
	}
 
	function getClient()
	{
		try
		{
			$CI =& get_instance();

			$client = new Client();
			$guzzleClient = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));
			$client->setHttpClient($guzzleClient);
		
			$client->setApplicationName($CI->config->item('branding_name'));
			// $client->setScopes('https://www.googleapis.com/auth/gmail.addons.current.message.readonly');
			$client->setScopes('https://www.googleapis.com/auth/gmail.send');

	        $config_credential = array( /*"redirect_uri"  => site_url( "configGmailApi/gmail_api_redirect" ),*/
	                         "client_id"     => $CI->config->item("gmail_client_id"),
	                         "client_secret" => $CI->config->item("gmail_client_secret"),
	        );

			$client->setAuthConfig($config_credential);
			$client->setAccessType('offline');

			$google_api_token = json_decode($CI->config->item("gmail_api_token"), true);
			if($google_api_token){
				$client->setAccessToken($google_api_token);
			}else{
				return false;
			}
			return $client;
		}
		catch(Exception $e)
		{
			return false;
		}
	}

	function initialize(array $config = array()){
		if ($this->is_configured_to_use_gmail_api()){
			$this->gmail_api_data['config'] = $config;
		}else{
			parent::initialize($config);
		}
	}

	function to($to){
		if ($this->is_configured_to_use_gmail_api()){
			$this->gmail_api_data['to'] = $to;
		}else{
			parent::to($to);
		}
	}

	function cc($cc){
		if ($this->is_configured_to_use_gmail_api()){
			$this->gmail_api_data['Cc'] = $cc;
		}else{
			parent::cc($cc);
		}
	}

	function bcc($bcc, $limit = ''){
		if ($this->is_configured_to_use_gmail_api()){
			$this->gmail_api_data['Bcc'] = $bcc;
			$this->gmail_api_data['Bcc_limit'] = $limit;
		}else{
			parent::bcc($bcc, $limit);
		}
	}

	function subject($subject){
		if ($this->is_configured_to_use_gmail_api()){
			$this->gmail_api_data['subject'] = $subject;
		}else{
			parent::subject($subject);
		}
	}

	public function attach($file, $disposition = '', $newname = NULL, $mime = ''){
		if ($this->is_configured_to_use_gmail_api()){

			$is_file = false;
			if ($mime === '')
			{
				$is_file = true;
				if (strpos($file, '://') === FALSE && ! file_exists($file))
				{
					$this->_set_error_message('lang:email_attachment_missing', $file);
					return FALSE;
				}
	
				if ( ! $fp = @fopen($file, 'rb'))
				{
					$this->_set_error_message('lang:email_attachment_unreadable', $file);
					return FALSE;
				}
	
				$file_content = stream_get_contents($fp);
				$mime = $this->_mime_types(pathinfo($file, PATHINFO_EXTENSION));
				fclose($fp);
			}
			else
			{
				$file_content =& $file; // buffered file
			}

			$this->gmail_api_data['attachments'][] =
			array(
				'name'		=> array($file, $newname),
				'disposition'	=> empty($disposition) ? 'attachment' : $disposition,  // Can also be 'inline'  Not sure if it matters
				'type'		=> $mime,
				'content'	=> $file_content,
				'multipart'	=> 'mixed',
				'is_file' => $is_file
			);
		}else{
			parent::attach($file, $disposition, $newname, $mime);
		}
	}

	function message($message){
		if ($this->is_configured_to_use_gmail_api()){
			$this->gmail_api_data['message'] = $message;
		}else{
			parent::message($message);
		}
	}

    public function send_email_by_gmail_api( string $email_to, string $subject, string $message, $email_from = false, $email_name = false, $Cc = false, $Bcc = false, $extras = array() )
    {
        $send = false;

		$client = $this->getClient();
		if (!$client)
		{
			return false;
		}
		
		$service = new Gmail($client);

        try {
            
            $mime = new Mail_mime();

            $mime->setSubject( $subject );
            $mime->setTXTBody( strip_tags( $message ) );
            $mime->setHTMLBody( $message );
            $mime->setFrom( $email_name. ' <'.$email_from.'>' );
            $mime->addTo( $email_to );

            if ( !empty( $Cc ) )
            {
                if ( is_array( $Cc ) )
                {
                    foreach ( $Cc as $ccEmail )
                    {
                        $mime->addCc( $ccEmail );
                    }
                } else 
                {
                    $mime->addCc( $Cc );
                }
            }

            if ( !empty( $Bcc ) )
            {
                if ( is_array( $Bcc ) )
                {
                    foreach ( $Bcc as $BccEmail )
                    {
                        $mime->addBcc( $BccEmail );
                    }
                } else 
                {
                    $mime->addCc( $Bcc );
                }
            }

            if ( !empty( $this->gmail_api_data['attachments'] ) && is_array( $this->gmail_api_data['attachments'] ) )
            {
                foreach ( $this->gmail_api_data['attachments'] as $attachment )
                {
					if($attachment['is_file']){
						$mime->addAttachment( 
							$attachment['name'][0],
							$attachment['type'],
							$attachment['name'][1],
							true,
							'base64',
							$attachment['disposition']
						);
					}else{
						$mime->addAttachment( 
							$attachment['content'],
							$attachment['type'],
							$attachment['name'][1],
							false,
							'base64',
							$attachment['disposition']
						);
					}

                }
            }

			$message_body = $mime->getMessage();

            $encodeMessage = $this->base64url_encode( $message_body );

            $msg = new Google_Service_Gmail_Message();
            $msg->setRaw( $encodeMessage );

            $send = $service->users_messages->send("me", $msg);

            if( empty( $send->getId() ))
            {
                log_message( "error", "Error: Sending email with gmail API." );
                log_message( "error", json_encode( $send ) );
				return FALSE;
            }
			return TRUE;

        } catch (Exception $e) {
            log_message( "error", "Error: Sending email with gmail API." );
            log_message( "error", $e->getMessage() );
        }
		return FALSE;
    }
    // Fancy Base encoding copied from stackoverflow
    public function base64url_encode( $data )
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // Fancy Base dencoding copied from stackoverflow
    public function base64url_decode( $data )
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
