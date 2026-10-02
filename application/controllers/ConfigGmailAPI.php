<?php
require_once ("Secure_area.php");
require __DIR__ . '/../libraries/google_api_client/vendor/autoload.php';
define('GMAIL_API_FILE_PATH', __DIR__ . '/../libraries/google_api_client/');
use Google\Client;

// POS app test credential
defined('GMAIL_API_CLIENT_ID')  OR define('GMAIL_API_CLIENT_ID', "915113938147-5fmkhld9l5muhmmond4qucctbn3uveps.apps.googleusercontent.com");
defined('GMAIL_API_CLIENT_SECRET')  OR define('GMAIL_API_CLIENT_SECRET', "RkFuh4_VO91inl5nPtgfdjSx");

class ConfigGmailAPI extends Secure_area 
{
	function __construct()
	{
		parent::__construct('config');
		$this->lang->load('config');
	}

	private function getClient(){

        $config_credential = array(
			"redirect_uris"  => array("https://phppointofsale.com/gmail_redirect.php"),
			"client_id"     => GMAIL_API_CLIENT_ID,
			"client_secret" => GMAIL_API_CLIENT_SECRET,
		);

		$client = new Client();
		$guzzleClient = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));
		$client->setHttpClient($guzzleClient);
		$client->setApplicationName($this->config->item('branding_name'));
		$client->setScopes('https://www.googleapis.com/auth/gmail.send');
		$client->setAuthConfig($config_credential);
		$client->setAccessType('offline');
		return $client;
	}

	public function gmail_api_credential(){

		try{

			$client = $this->getClient();

			$client->setPrompt('select_account consent');
			$state = site_url("configGmailAPI/gmail_api_save_token");
			$client->setState( $this->base64UrlEncode($state) );
			$authUrl = $client->createAuthUrl();
	
			echo json_encode(['status' => 1, 'authURL' => $authUrl]);
			exit();
	
        } catch (Exception $e) {

		}

		echo json_encode(['status' => 0, 'error' => 'Credential JSON Error! Please try again.']);
		exit();
	}

	public function gmail_api_redirect(){
		if (!isset($_GET['code'])) {
			echo "Error code!";
			exit();
		}else{
			$authCode = $_GET['code'];
		}
	
		$redirect_url = "";
		if(isset($_GET['state'])){
			$state = $this->base64UrlDecode($_GET['state']);
			$redirect_url = $state;
		}
		header('Location: '.$redirect_url.'?authCode='.$authCode);
	}

	public function gmail_api_save_token(){
		$authCode = $this->input->get('authCode');

		$client = $this->getClient();
		// Exchange authorization code for an access token. 
		$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
		$client->setAccessToken($accessToken);

		// Check to see if there was an error.
		if (array_key_exists('error', $accessToken)) {
			throw new Exception(join(', ', $accessToken));
		}

		$accessToken = json_encode($client->getAccessToken());

		
		$this->Appconfig->save('email_provider', 'Gmail API');
		$this->Appconfig->save('gmail_client_id', GMAIL_API_CLIENT_ID);
		$this->Appconfig->save('gmail_client_secret', GMAIL_API_CLIENT_SECRET);
		$this->Appconfig->save('gmail_api_token', $accessToken);

		echo ("<h3>".lang("gmail_api_token_registered")."<h3>");
		echo (
			'<script>
				setTimeout(function(){
					window.opener.authorize_dialog_message("success");
				}, 3000);
			</script>'
		);
		exit();
	}

	public function gmail_api_signout(){
		$this->Appconfig->save('gmail_api_token', "");
		echo json_encode(['status' => 1, 'message' => lang("gmail_api_token_removed")]);
		exit();
	}

	private function base64UrlEncode($inputStr)
	{
		return strtr(base64_encode($inputStr), '+/=', '-_,');
	}
	
	private function base64UrlDecode($inputStr)
	{
		return base64_decode(strtr($inputStr, '-_,', '+/='));
	}	
}

?>