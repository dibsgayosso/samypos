<?php
function is_phppos_update_available()
{
    $CI =& get_instance();  
	$branding = $CI->config->item('branding');
	$domain = $branding['domain'];
	$staging_domain = $branding['staging_domain'];
	
	$url = (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? "http://$staging_domain/current_version.php?build_timestamp=1": "http://$domain/current_version.php?build_timestamp=1";
	
   $ch = curl_init($url);
//Don't verify ssl...just in case a server doesn't have the ability to verify
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
   
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  	$current_build = curl_exec($ch);
  	curl_close($ch);

	return ($current_build != '' && (BUILD_TIMESTAMP != $current_build));
}
?>