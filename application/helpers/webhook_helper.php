<?php
function do_webhook($data,$url,$headers=false,$return=false)
{
	$curl = curl_init();

	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers ? $headers : array('Content-Type:application/json'));
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5); 
	curl_setopt($curl, CURLOPT_TIMEOUT, 5);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	//Don't verify ssl...just in case a server doesn't have the ability to verify
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

	$response = curl_exec($curl);

    curl_close($curl);

	if($return){
		return $response;
	}
}