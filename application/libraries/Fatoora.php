<?php

use Automattic\WooCommerce\HttpClient\Response;

defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "libraries/zatca-xml/vendor/autoload.php");

// define('ZATCA_SDK_URL', "http://localhost/Zatca-SDK-API/index.php/api/");
define ('ZATCA_API_TEST_URL', 'https://gw-fatoora.zatca.gov.sa/e-invoicing/developer-portal');
define ('ZATCA_API_LIVE_URL', 'https://gw-fatoora.zatca.gov.sa/e-invoicing/core');

define('ZATCA_SDK_URL_SERVER', "https://zatca.phppointofsale.com/index.php/api/");
define('ZATCA_SDK_URL_LOCAL', "https://3c6b-186-79-64-237.ngrok-free.app/Zatca-SDK-API/index.php/api/");
define('ZATCA_SDK_URL', ZATCA_SDK_URL_SERVER);

class Fatoora
{
    public function __construct($data)
    {
    }

    static public function api_generate_csr($data, $url = 0)
    {
        $curl = curl_init();

        $sdk_url = ZATCA_SDK_URL . 'generate_csr';
        if($url == 1){
            $sdk_url = ZATCA_SDK_URL_SERVER . 'generate_csr';
        } else if($url == 2){
            $sdk_url = ZATCA_SDK_URL_LOCAL . 'generate_csr';
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $sdk_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('data' => json_encode($data)),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $ret = json_decode($response, true);
        return $ret;
    }

    static public function api_sign_xml_invoice($data, $sdk_config, $url = 0)
    {
        $curl = curl_init();

        // $url = ZATCA_SDK_URL . rawurlencode('-sign -invoice <input_file> -generatedCsr <output_file_1> -privateKey <output_file_2>');
        $sdk_url = ZATCA_SDK_URL . 'sign_xml_invoice';
        if($url == 1){
            $sdk_url = ZATCA_SDK_URL_SERVER . 'sign_xml_invoice';
        } else if($url == 2){
            $sdk_url = ZATCA_SDK_URL_LOCAL . 'sign_xml_invoice';
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $sdk_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'data' => json_encode($data),
                'sdk_config' => json_encode($sdk_config),
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $ret = json_decode($response, true);
        return $ret;
    }

    static public function api_validate_xml_invoice($data, $sdk_config, $url = 0)
    {
        $curl = curl_init();

        $sdk_url = ZATCA_SDK_URL . 'validate_xml_invoice';
        if($url == 1){
            $sdk_url = ZATCA_SDK_URL_SERVER . 'validate_xml_invoice';
        } else if($url == 2){
            $sdk_url = ZATCA_SDK_URL_LOCAL . 'validate_xml_invoice';
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $sdk_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'data' => json_encode($data),
                'sdk_config' => json_encode($sdk_config),
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $ret = json_decode($response, true);
        return $ret;
    }

    static public function api_generate_invoice_qr($data, $sdk_config, $url = 0)
    {
        $curl = curl_init();

        $sdk_url = ZATCA_SDK_URL . 'generate_invoice_qr';
        if($url == 1){
            $sdk_url = ZATCA_SDK_URL_SERVER . 'generate_invoice_qr';
        } else if($url == 2){
            $sdk_url = ZATCA_SDK_URL_LOCAL . 'generate_invoice_qr';
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $sdk_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'data' => json_encode($data),
                'sdk_config' => json_encode($sdk_config),
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $ret = json_decode($response, true);
        return $ret;
    }

    static public function api_request_xml_invoice($data, $url = 0)
    {
        $curl = curl_init();

        $sdk_url = ZATCA_SDK_URL . 'request_xml_invoice';
        if($url == 1){
            $sdk_url = ZATCA_SDK_URL_SERVER . 'request_xml_invoice';
        } else if($url == 2){
            $sdk_url = ZATCA_SDK_URL_LOCAL . 'request_xml_invoice';
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $sdk_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('data' => json_encode($data)),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $ret = json_decode($response, true);
        return $ret;
    }

    static public function generate_csr($data)
    {

        $return_csr = array(
            'state' => 0,
            'message' => 0,
            'csr' => "",
        );

        $output = null;
        $retval = 0;
        $sale_id = $data['sale_id'];

        $temp_path = APPPATH . "libraries/zatca-xml/zatca-logs/"; //check temp folder permission r/w
        $csr_path = $temp_path . $sale_id;

        if (!file_exists($csr_path)) {
            mkdir($csr_path, 0777, true);
        } else {
            $files = glob($csr_path . "/*"); // get all file names
            foreach ($files as $file) { // iterate files
                if (is_file($file)) {
                    unlink($file); // delete file
                }
            }
        }

        $orign_working_directory = getcwd();
        chdir($csr_path);

        $create_config_file = $csr_path . '/config.properties';
        $file = fopen($create_config_file, 'w');

        fwrite($file, 'csr.common.name=' . $data['csr.common.name']);
        fwrite($file, PHP_EOL);
        fwrite($file, 'csr.serial.number=' . $data['csr.serial.number']);
        fwrite($file, PHP_EOL);
        fwrite($file, 'csr.organization.identifier=' . $data['csr.organization.identifier']);
        fwrite($file, PHP_EOL);
        fwrite($file, 'csr.organization.unit.name=' . $data['csr.organization.unit.name']);
        fwrite($file, PHP_EOL);
        fwrite($file, 'csr.organization.name=' . $data['csr.organization.name']);
        fwrite($file, PHP_EOL);
        fwrite($file, 'csr.country.name=' . $data['csr.country.name']);
        fwrite($file, PHP_EOL);
        fwrite($file, 'csr.invoice.type=' . $data['csr.invoice.type']);
        fwrite($file, PHP_EOL);
        fwrite($file, 'csr.location.address=' . $data['csr.location.address']);
        fwrite($file, PHP_EOL);
        fwrite($file, 'csr.industry.business.category=' . $data['csr.industry.business.category']);
        fwrite($file, PHP_EOL);
        fclose($file);

        $command = "fatoora -csr -csrConfig " . $create_config_file;
        $output = null;
        $retval = 0;
        $ret = exec($command, $output, $retval);
        if (!$ret) {
            $return_csr['state'] = 0;
            $return_csr['message'] = "Please check SDK(fatoora) installation and CSR configuration properties";
            return $return_csr;
        } else {
            $return_csr['message'] = $ret;
        }

        $file_list = scandir($csr_path);
        $file_csr = "";
        $file_private_key = "";
        foreach ($file_list as $file) {
            if (strpos($file, "generated-csr") > -1) {
                $file_csr = $file;
            } else if (strpos($file, "generated-private-key") > -1) {
                $file_private_key = $file;
            }
        }

        $str_csr = "";
        if ($file_csr) {
            $f_csr_handle = fopen($file_csr, 'r');
            if ($f_csr_handle) {
                while (($line = fgets($f_csr_handle)) !== false) {
                    // process the line read.
                    $str_csr = $str_csr . $line;
                }

                fclose($f_csr_handle);
            }
        }

        chdir($orign_working_directory);

        $return_csr['state'] = 1;
        $return_csr['message'] = "success";
        $return_csr['csr'] = $str_csr;
        return $return_csr;
    }

    static public function sign_xml_invoice($sale_id)
    {

        $temp_path = APPPATH . "libraries/zatca-xml/zatca-logs/" . $sale_id; //check temp folder permission r/w
        $orign_working_directory = getcwd();
        chdir($temp_path);

        //clean old "invoice_signed"
        $files = glob($temp_path . "/*"); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file) && strpos($file, "invoice_signed")) {
                unlink($file); // delete file
            }
        }

        $command = " fatoora -sign -invoice invoice.xml ";
        $output = null;
        $retval = 0;
        $ret = exec($command, $output, $retval);

        $files = glob($temp_path . "/*"); // get all file names
        $str_sign = "";
        foreach ($files as $file) { // iterate files
            if (is_file($file) && strpos($file, "invoice_signed")) {
                $f_request_handle = fopen($file, 'r');
                if ($f_request_handle) {
                    while (($line = fgets($f_request_handle)) !== false) {
                        // process the line read.
                        $str_sign = $str_sign . $line;
                    }

                    fclose($f_request_handle);
                }
                break;
            }
        }

        $ret = array(
            'signed_xml' => $str_sign
        );

        chdir($orign_working_directory);
        return $ret;
    }

    static public function check_xml_invoice($sale_id)
    {

        $temp_path = APPPATH . "libraries/zatca-xml/zatca-logs/" . $sale_id; //check temp folder permission r/w
        $orign_working_directory = getcwd();
        chdir($temp_path);

        $command = " fatoora -validate -invoice invoice_signed.xml ";
        $output = null;
        $retval = 0;
        $ret = exec($command, $output, $retval);

        $pass_pos = strpos($ret, "GLOBAL VALIDATION RESULT = PASSED");

        chdir($orign_working_directory);
        if ($pass_pos > -1) return true;
        return false;
    }

    static public function request_xml_invoice($sale_id)
    {

        $temp_path = APPPATH . "libraries/zatca-xml/zatca-logs/" . $sale_id; //check temp folder permission r/w
        $orign_working_directory = getcwd();
        chdir($temp_path);

        //clean old "generated-json-request"
        $files = glob($temp_path . "/*"); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file) && strpos($file, "generated-json-request")) {
                unlink($file); // delete file
            }
        }

        $command = " fatoora -invoiceRequest -invoice invoice_signed.xml ";
        $output = null;
        $retval = 0;
        $ret = exec($command, $output, $retval);

        $files = glob($temp_path . "/*"); // get all file names
        $str_request = "";
        foreach ($files as $file) { // iterate files
            if (is_file($file) && strpos($file, "generated-json-request")) {
                $f_request_handle = fopen($file, 'r');
                if ($f_request_handle) {
                    while (($line = fgets($f_request_handle)) !== false) {
                        // process the line read.
                        $str_request = $str_request . $line;
                    }

                    fclose($f_request_handle);
                }
                break;
            }
        }

        $ret = array(
            'data' => $str_request
        );

        chdir($orign_working_directory);
        return $ret;
    }

    static public function check_compliance_invoice($data)
    {

        $CI =& get_instance();
        $zatca_api_url = "";
        if($CI->config->item('use_saudi_tax_test_config')){
            $zatca_api_url = ZATCA_API_TEST_URL;
        }else{
            $zatca_api_url = ZATCA_API_LIVE_URL;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $zatca_api_url.'/compliance/invoices',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data['invoice']['invoice_request'],
            CURLOPT_HTTPHEADER => array(
                'Accept-Language: en',
                'Accept-Version: V2',
                'Authorization: Basic ' . base64_encode($data['ccsid']['binarySecurityToken'] . ":" . $data['ccsid']['secret']),
                'Content-Type: application/json'
            ),
        ));

        $response0 = curl_exec($curl);

        if($response0){
            $response = json_decode($response0, true);
        }else{
            $error_message = curl_error($curl);
            $ret = array(
                'state' => 0,
                'message' => $error_message,
                'data' => $response0,
            );
            return $ret;
        }

        curl_close($curl);

        $ret = array();
        if (isset($response['validationResults']) && $response['validationResults']['status'] == "PASS") {
            $ret = array(
                'state' => 1,
                'message' => "",
                'data' => $response0,
            );
        } else {
            $ret = array(
                'state' => 0,
                'message' => "",
                'data' => $response0,
            );
        }
        return $ret;
    }

    static public function clearance_api($data)
    {

        $CI =& get_instance();
        $zatca_api_url = "";
        if($CI->config->item('use_saudi_tax_test_config')){
            $zatca_api_url = ZATCA_API_TEST_URL;
        }else{
            $zatca_api_url = ZATCA_API_LIVE_URL;
        }

        $curl = curl_init();
        $clearance_status = 0;
        if ($CI->config->item('use_saudi_tax_test_config')) {
            $binarySecurityToken = "TUlJRDFEQ0NBM21nQXdJQkFnSVRid0FBZTNVQVlWVTM0SS8rNVFBQkFBQjdkVEFLQmdncWhrak9QUVFEQWpCak1SVXdFd1lLQ1pJbWlaUHlMR1FCR1JZRmJHOWpZV3d4RXpBUkJnb0praWFKay9Jc1pBRVpGZ05uYjNZeEZ6QVZCZ29Ka2lhSmsvSXNaQUVaRmdkbGVIUm5ZWHAwTVJ3d0dnWURWUVFERXhOVVUxcEZTVTVXVDBsRFJTMVRkV0pEUVMweE1CNFhEVEl5TURZeE1qRTNOREExTWxvWERUSTBNRFl4TVRFM05EQTFNbG93U1RFTE1Ba0dBMVVFQmhNQ1UwRXhEakFNQmdOVkJBb1RCV0ZuYVd4bE1SWXdGQVlEVlFRTEV3MW9ZWGxoSUhsaFoyaHRiM1Z5TVJJd0VBWURWUVFERXdreE1qY3VNQzR3TGpFd1ZqQVFCZ2NxaGtqT1BRSUJCZ1VyZ1FRQUNnTkNBQVRUQUs5bHJUVmtvOXJrcTZaWWNjOUhEUlpQNGI5UzR6QTRLbTdZWEorc25UVmhMa3pVMEhzbVNYOVVuOGpEaFJUT0hES2FmdDhDL3V1VVk5MzR2dU1ObzRJQ0p6Q0NBaU13Z1lnR0ExVWRFUVNCZ0RCK3BId3dlakViTUJrR0ExVUVCQXdTTVMxb1lYbGhmREl0TWpNMGZETXRNVEV5TVI4d0hRWUtDWkltaVpQeUxHUUJBUXdQTXpBd01EYzFOVGc0TnpBd01EQXpNUTB3Q3dZRFZRUU1EQVF4TVRBd01SRXdEd1lEVlFRYURBaGFZWFJqWVNBeE1qRVlNQllHQTFVRUR3d1BSbTl2WkNCQ2RYTnphVzVsYzNNek1CMEdBMVVkRGdRV0JCU2dtSVdENmJQZmJiS2ttVHdPSlJYdkliSDlIakFmQmdOVkhTTUVHREFXZ0JSMllJejdCcUNzWjFjMW5jK2FyS2NybVRXMUx6Qk9CZ05WSFI4RVJ6QkZNRU9nUWFBL2hqMW9kSFJ3T2k4dmRITjBZM0pzTG5waGRHTmhMbWR2ZGk1ellTOURaWEowUlc1eWIyeHNMMVJUV2tWSlRsWlBTVU5GTFZOMVlrTkJMVEV1WTNKc01JR3RCZ2dyQmdFRkJRY0JBUVNCb0RDQm5UQnVCZ2dyQmdFRkJRY3dBWVppYUhSMGNEb3ZMM1J6ZEdOeWJDNTZZWFJqWVM1bmIzWXVjMkV2UTJWeWRFVnVjbTlzYkM5VVUxcEZhVzUyYjJsalpWTkRRVEV1WlhoMFoyRjZkQzVuYjNZdWJHOWpZV3hmVkZOYVJVbE9WazlKUTBVdFUzVmlRMEV0TVNneEtTNWpjblF3S3dZSUt3WUJCUVVITUFHR0gyaDBkSEE2THk5MGMzUmpjbXd1ZW1GMFkyRXVaMjkyTG5OaEwyOWpjM0F3RGdZRFZSMFBBUUgvQkFRREFnZUFNQjBHQTFVZEpRUVdNQlFHQ0NzR0FRVUZCd01DQmdnckJnRUZCUWNEQXpBbkJna3JCZ0VFQVlJM0ZRb0VHakFZTUFvR0NDc0dBUVVGQndNQ01Bb0dDQ3NHQVFVRkJ3TURNQW9HQ0NxR1NNNDlCQU1DQTBrQU1FWUNJUUNWd0RNY3E2UE8rTWNtc0JYVXovdjFHZGhHcDdycVNhMkF4VEtTdjgzOElBSWhBT0JOREJ0OSszRFNsaWpvVmZ4enJkRGg1MjhXQzM3c21FZG9HV1ZyU3BHMQ==";
            $secret = "Xlj15LyMCgSC66ObnEO/qVPfhSbs3kDTjWnGheYhfSs=";
            $basic_auth = base64_encode($binarySecurityToken . ":" . $secret);
            $clearance_status = rand(0, 1);
        } else {
            $basic_auth = base64_encode($data['pcsid']['binarySecurityToken'] . ":" . $data['pcsid']['secret']);
        }

        $zatca_url = $zatca_api_url.'/invoices/clearance/single';
        curl_setopt_array($curl, array(
            CURLOPT_URL => $zatca_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data['invoice']['invoice_request'],
            CURLOPT_HTTPHEADER => array(
                'Accept-Language: en',
                'Accept-Version: V2',
                'Clearance-Status: ' . $clearance_status,
                'Authorization: Basic ' . $basic_auth,
                'Content-Type: application/json'
            ),
        ));

        $response0 = curl_exec($curl);
        $response = json_decode($response0, true);

        curl_close($curl);

        $ret = array();

        if($response == null){
            //"Clearance is deactiviated. Please use the /invoices/reporting/single endpoint instead."
            if(strpos($response0, "/invoices/reporting/single") > -1){
                $ret['state'] = 303;
                $ret['data'] = $response0;
                $ret['message'] = $response0;
                return $ret;
            }
        }

        $ret['state'] = 0;
        $ret['data'] = $response0;
        $ret['message'] = $response["clearanceStatus"];
        if ($response['validationResults']['status'] == "PASS") {
            $ret['state'] = 1;
        }
        return $ret;
    }

    static public function report_api($data)
    {

        $CI =& get_instance();
        $zatca_api_url = "";
        if($CI->config->item('use_saudi_tax_test_config')){
            $zatca_api_url = ZATCA_API_TEST_URL;
        }else{
            $zatca_api_url = ZATCA_API_LIVE_URL;
        }

        $curl = curl_init();
        $clearance_status = 1;
        if($CI->config->item('use_saudi_tax_test_config')){
            $binarySecurityToken = "TUlJRDFEQ0NBM21nQXdJQkFnSVRid0FBZTNVQVlWVTM0SS8rNVFBQkFBQjdkVEFLQmdncWhrak9QUVFEQWpCak1SVXdFd1lLQ1pJbWlaUHlMR1FCR1JZRmJHOWpZV3d4RXpBUkJnb0praWFKay9Jc1pBRVpGZ05uYjNZeEZ6QVZCZ29Ka2lhSmsvSXNaQUVaRmdkbGVIUm5ZWHAwTVJ3d0dnWURWUVFERXhOVVUxcEZTVTVXVDBsRFJTMVRkV0pEUVMweE1CNFhEVEl5TURZeE1qRTNOREExTWxvWERUSTBNRFl4TVRFM05EQTFNbG93U1RFTE1Ba0dBMVVFQmhNQ1UwRXhEakFNQmdOVkJBb1RCV0ZuYVd4bE1SWXdGQVlEVlFRTEV3MW9ZWGxoSUhsaFoyaHRiM1Z5TVJJd0VBWURWUVFERXdreE1qY3VNQzR3TGpFd1ZqQVFCZ2NxaGtqT1BRSUJCZ1VyZ1FRQUNnTkNBQVRUQUs5bHJUVmtvOXJrcTZaWWNjOUhEUlpQNGI5UzR6QTRLbTdZWEorc25UVmhMa3pVMEhzbVNYOVVuOGpEaFJUT0hES2FmdDhDL3V1VVk5MzR2dU1ObzRJQ0p6Q0NBaU13Z1lnR0ExVWRFUVNCZ0RCK3BId3dlakViTUJrR0ExVUVCQXdTTVMxb1lYbGhmREl0TWpNMGZETXRNVEV5TVI4d0hRWUtDWkltaVpQeUxHUUJBUXdQTXpBd01EYzFOVGc0TnpBd01EQXpNUTB3Q3dZRFZRUU1EQVF4TVRBd01SRXdEd1lEVlFRYURBaGFZWFJqWVNBeE1qRVlNQllHQTFVRUR3d1BSbTl2WkNCQ2RYTnphVzVsYzNNek1CMEdBMVVkRGdRV0JCU2dtSVdENmJQZmJiS2ttVHdPSlJYdkliSDlIakFmQmdOVkhTTUVHREFXZ0JSMllJejdCcUNzWjFjMW5jK2FyS2NybVRXMUx6Qk9CZ05WSFI4RVJ6QkZNRU9nUWFBL2hqMW9kSFJ3T2k4dmRITjBZM0pzTG5waGRHTmhMbWR2ZGk1ellTOURaWEowUlc1eWIyeHNMMVJUV2tWSlRsWlBTVU5GTFZOMVlrTkJMVEV1WTNKc01JR3RCZ2dyQmdFRkJRY0JBUVNCb0RDQm5UQnVCZ2dyQmdFRkJRY3dBWVppYUhSMGNEb3ZMM1J6ZEdOeWJDNTZZWFJqWVM1bmIzWXVjMkV2UTJWeWRFVnVjbTlzYkM5VVUxcEZhVzUyYjJsalpWTkRRVEV1WlhoMFoyRjZkQzVuYjNZdWJHOWpZV3hmVkZOYVJVbE9WazlKUTBVdFUzVmlRMEV0TVNneEtTNWpjblF3S3dZSUt3WUJCUVVITUFHR0gyaDBkSEE2THk5MGMzUmpjbXd1ZW1GMFkyRXVaMjkyTG5OaEwyOWpjM0F3RGdZRFZSMFBBUUgvQkFRREFnZUFNQjBHQTFVZEpRUVdNQlFHQ0NzR0FRVUZCd01DQmdnckJnRUZCUWNEQXpBbkJna3JCZ0VFQVlJM0ZRb0VHakFZTUFvR0NDc0dBUVVGQndNQ01Bb0dDQ3NHQVFVRkJ3TURNQW9HQ0NxR1NNNDlCQU1DQTBrQU1FWUNJUUNWd0RNY3E2UE8rTWNtc0JYVXovdjFHZGhHcDdycVNhMkF4VEtTdjgzOElBSWhBT0JOREJ0OSszRFNsaWpvVmZ4enJkRGg1MjhXQzM3c21FZG9HV1ZyU3BHMQ==";
            $secret = "Xlj15LyMCgSC66ObnEO/qVPfhSbs3kDTjWnGheYhfSs=";
            $basic_auth = base64_encode($binarySecurityToken . ":" . $secret);
        } else {
            $basic_auth = base64_encode($data['pcsid']['binarySecurityToken'] . ":" . $data['pcsid']['secret']);
        }

        $zatca_url = $zatca_api_url.'/invoices/reporting/single';

        if( isset($data['clearance_status']) ) {
            $clearance_status = $data['clearance_status'];
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $zatca_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data['invoice']['invoice_request'],
            CURLOPT_HTTPHEADER => array(
                'Accept-Language: en',
                'Accept-Version: V2',
                'Clearance-Status: ' . $clearance_status,
                'Authorization: Basic ' . $basic_auth,
                'Content-Type: application/json'
            ),
        ));

        $response0 = curl_exec($curl);
        $response = json_decode($response0, true);

        curl_close($curl);

        $ret = array();
        $ret['state'] = 0;
        $ret['data'] = $response0;
        $ret['message'] = $response["reportingStatus"];
        if ($response['validationResults']['status'] == "PASS") {
            $ret['state'] = 1;
        }
        return $ret;
    }

    static public function generate_pcsid($data)
    {

        $curl = curl_init();
        $CI =& get_instance();
        $zatca_api_url = "";
        if($CI->config->item('use_saudi_tax_test_config')){
            $zatca_api_url = ZATCA_API_TEST_URL;
        }else{
            $zatca_api_url = ZATCA_API_LIVE_URL;
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $zatca_api_url.'/production/csids',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "compliance_request_id": "' . $data['ccsid']['requestID'] . '"
            }',
            CURLOPT_HTTPHEADER => array(
                'Accept-Language: en',
                'Accept-Version: V2',
                'Authorization: Basic ' . base64_encode($data['ccsid']['binarySecurityToken'] . ":" . $data['ccsid']['secret']),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    static public function renew_pcsid($data)
    { // not working now

        $curl = curl_init();

        $basic_auth = base64_encode($data['pcsid']['binarySecurityToken'] . ":" . $data['pcsid']['secret']);

        $CI =& get_instance();
        $zatca_api_url = "";
        if($CI->config->item('use_saudi_tax_test_config')){
            $zatca_api_url = ZATCA_API_TEST_URL;
        }else{
            $zatca_api_url = ZATCA_API_LIVE_URL;
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $zatca_api_url.'/production/csids',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PATCH',
            CURLOPT_POSTFIELDS => '{
                "csr": "' . $data['csr'] . '"
            }',
            CURLOPT_HTTPHEADER => array(
                'OTP: ' . $data['renew_opt'],
                'Accept-Language: en',
                'Accept-Version: V2',
                'Authorization: Basic ' . $basic_auth,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    static public function generate_invoice($invoice_data, $log = 0)
    {
        $ret = array(
            'state' => 0,
            'message' => ""
        );

        if ($log == 0) {
            set_exception_handler(function ($exception) {
                $ret = array(
                    'state' => 0,
                    'message' => 'Generate XML invoice error.',
                    'data' => $exception->getMessage() . ", " . $exception->getFile() . ", " . $exception->getLine()
                );
                echo json_encode($ret);
                exit(1);
            });
        }

        $abs = 1;
        if($invoice_data['invoice_type_code'] == '381'){//credit notes
            $abs = -1;
        }

        $sign = (new \BaseetApp\UBL\SignatureInformation)
            ->setReferencedSignatureID("urn:oasis:names:specification:ubl:signature:Invoice")
            ->setID('urn:oasis:names:specification:ubl:signature:1');

        $ublDecoment = (new \BaseetApp\UBL\UBLDocumentSignatures)
            ->setSignatureInformation($sign);

        $extensionContent = (new \BaseetApp\UBL\ExtensionContent)
            ->setUBLDocumentSignatures($ublDecoment);

        $UBLExtension[] = (new \BaseetApp\UBL\UBLExtension)
            ->setExtensionURI('urn:oasis:names:specification:ubl:dsig:enveloped:xades')
            ->setExtensionContent($extensionContent);

        $UBLExtensions = (new \BaseetApp\UBL\UBLExtensions)
            ->setUBLExtensions($UBLExtension);

        $additionalDocumentReference1 = (new \BaseetApp\UBL\AdditionalDocumentReference)
            ->setId($invoice_data['additional_documnet_reference'][0]['id'])
            ->setUUID($invoice_data['additional_documnet_reference'][0]['UUID']);

        $additionalDocumentReference2 = (new \BaseetApp\UBL\AdditionalDocumentReference)
            ->setId($invoice_data['additional_documnet_reference'][1]['id'])
            ->setAttachment(
                (new \BaseetApp\UBL\Attachment)
                    ->setFileContent($invoice_data['additional_documnet_reference'][1]['attachment'])
            );

        $supplier_country = (new \BaseetApp\UBL\Country())
            ->setIdentificationCode($invoice_data['accounting_supplier_party']['postal_address']['country']);
        $supplier_postal_address = (new \BaseetApp\UBL\Address())
            ->setCountry($supplier_country)
            ->setStreetName($invoice_data['accounting_supplier_party']['postal_address']['street_name'])
            ->setBuildingNumber($invoice_data['accounting_supplier_party']['postal_address']['building_number'])
            ->setPlotIdentification($invoice_data['accounting_supplier_party']['postal_address']['plot_id'])
            ->setCitySubdivisionName($invoice_data['accounting_supplier_party']['postal_address']['city_subdivision_name'])
            ->setCityName($invoice_data['accounting_supplier_party']['postal_address']['city_name'])
            ->setPostalZone($invoice_data['accounting_supplier_party']['postal_address']['postal_zone']);

        $party_legal_entity = (new \BaseetApp\UBL\LegalEntity())
            ->setRegistrationName($invoice_data['accounting_supplier_party']['party_legal_entity']['registration_name']);

        $supplierCompany = (new \BaseetApp\UBL\Party())
            ->setPartyIdentification(
                (new \BaseetApp\UBL\PartyIdentification())
                    ->setId($invoice_data['accounting_supplier_party']['party_identification']['id'])
                    ->setSchemeID($invoice_data['accounting_supplier_party']['party_identification']['scheme_id'])
            )
            ->setPartyTaxScheme(
                (new \BaseetApp\UBL\PartyTaxScheme)
                    ->setCompanyId($invoice_data['accounting_supplier_party']['party_tax_scheme']['company_id'])
                    ->setTaxScheme((new \BaseetApp\UBL\TaxScheme)->setId('VAT'))
            )
            ->setPostalAddress($supplier_postal_address)
            ->setLegalEntity($party_legal_entity);

        $customer_country = (new \BaseetApp\UBL\Country())
            ->setIdentificationCode($invoice_data['accounting_supplier_party']['postal_address']['country']);
        $customer_postal_address = (new \BaseetApp\UBL\Address())
            ->setCountry($customer_country)
            ->setStreetName(isset($invoice_data['accounting_customer_party']['postal_address']['street_name']) ? $invoice_data['accounting_customer_party']['postal_address']['street_name'] : "")
            ->setBuildingNumber(isset($invoice_data['accounting_customer_party']['postal_address']['building_number']) ? $invoice_data['accounting_customer_party']['postal_address']['building_number'] : "")
            ->setPlotIdentification(isset($invoice_data['accounting_customer_party']['postal_address']['plot_id']) ? $invoice_data['accounting_customer_party']['postal_address']['plot_id'] : "")
            ->setCitySubdivisionName(isset($invoice_data['accounting_customer_party']['postal_address']['city_subdivision_name']) ? $invoice_data['accounting_customer_party']['postal_address']['city_subdivision_name'] : "")
            ->setCityName(isset($invoice_data['accounting_customer_party']['postal_address']['city_name']) ? $invoice_data['accounting_customer_party']['postal_address']['city_name'] : "")
            ->setPostalZone(isset($invoice_data['accounting_customer_party']['postal_address']['postal_zone']) ? $invoice_data['accounting_customer_party']['postal_address']['postal_zone'] : "");

        $clientCompany = (new \BaseetApp\UBL\Party())
            ->setPartyTaxScheme(
                (new \BaseetApp\UBL\PartyTaxScheme)
                    ->setTaxScheme((new \BaseetApp\UBL\TaxScheme)->setId('VAT'))
            );

        if (strlen(trim($invoice_data['accounting_customer_party']['party_identification']['id'])) > 0) {
            $clientCompany
                ->setPostalAddress($customer_postal_address)
                ->setPartyIdentification(
                    (new \BaseetApp\UBL\PartyIdentification())
                        ->setId(isset($invoice_data['accounting_customer_party']['party_identification']['id']) ? $invoice_data['accounting_customer_party']['party_identification']['id'] : "")
                        ->setSchemeID(isset($invoice_data['accounting_customer_party']['party_identification']['scheme_id']) ? $invoice_data['accounting_customer_party']['party_identification']['scheme_id'] : "")
                );
        }

        if (strlen(trim($invoice_data['accounting_customer_party']['party_tax_scheme']['company_id'])) > 0) {
            $clientCompany
            ->setPartyTaxScheme(
                (new \BaseetApp\UBL\PartyTaxScheme)
                    ->setCompanyId($invoice_data['accounting_customer_party']['party_tax_scheme']['company_id'])
                    ->setTaxScheme((new \BaseetApp\UBL\TaxScheme)->setId('VAT'))
            );
        }

        if (strlen(trim($invoice_data['accounting_customer_party']['party_legal_entity']['registration_name'])) > 0) {
            $party_legal_entity = (new \BaseetApp\UBL\LegalEntity())
            ->setRegistrationName($invoice_data['accounting_customer_party']['party_legal_entity']['registration_name']);
            $clientCompany->setLegalEntity($party_legal_entity);
        }

        $invoiceLines = array();
        foreach ($invoice_data['invoice_lines'] as $invoice_line) {

            $classifiedTaxCategoryList = array();
            foreach ($invoice_line['item']['classified_tax_category'] as $classified_tax_category) {
                $classifiedTaxCategoryList[] =
                    (new \BaseetApp\UBL\ClassifiedTaxCategory())
                    ->setId($classified_tax_category['id'])
                    ->setPercent($classified_tax_category['percent'])
                    ->setTaxScheme(
                        (new \BaseetApp\UBL\TaxScheme())
                            ->setId("VAT")
                    );
            }

            $invoiceLines[] = (new \BaseetApp\UBL\InvoiceLine())
                ->setId($invoice_line['id'])
                ->setUnitCode("PCE")
                ->setLineExtensionAmount($invoice_line['line_extension_amount'] * $abs)
                ->setItem(
                    (new \BaseetApp\UBL\Item())
                        ->setName($invoice_line['item']['name'])
                        ->setDescription($invoice_line['item']['description'])
                        // ->setSellersItemIdentification('SELLERID')
                        ->setClassifiedTaxCategoryList($classifiedTaxCategoryList)
                )
                // ->setInvoicePeriod($invoicePeriod)
                ->setPrice(
                    (new \BaseetApp\UBL\Price())
                        ->setPriceAmount($invoice_line['price']['price_amount'])
                        // ->setBaseQuantity(1)
                        // ->setUnitCode(\BaseetApp\UBL\UnitCode::UNIT)
                        ->setAllowanceCharges(
                            array(
                                (new \BaseetApp\UBL\AllowanceCharge)
                                    ->setChargeIndicator($invoice_line['price']['allowance_charge']['charge_indicator'])
                                    ->setAllowanceChargeReason($invoice_line['price']['allowance_charge']['allowance_charge_reason'])
                                    ->setAmount($invoice_line['price']['allowance_charge']['amount'] * $abs)
                            )
                        )
                )
                // ->setAccountingCostCode('Product 123')
                ->setTaxTotal(
                    (new \BaseetApp\UBL\TaxTotal())
                        ->setTaxAmount($invoice_line['tax_total']['tax_amount'] * $abs)
                        ->setRoundingAmount($invoice_line['tax_total']['rounding_amount'] * $abs)
                )
                ->setInvoicedQuantity($invoice_line['invoiced_quantity'] * $abs);
        }

        $legalMonetaryTotal = (new \BaseetApp\UBL\LegalMonetaryTotal())
            ->setLineExtensionAmount($invoice_data['legal_monetary_total']['line_extension_amount'] * $abs)
            ->setTaxExclusiveAmount($invoice_data['legal_monetary_total']['tax_exclusive_amount'] * $abs)
            ->setTaxInclusiveAmount($invoice_data['legal_monetary_total']['tax_inclusive_amount'] * $abs)
            ->setAllowanceTotalAmount($invoice_data['legal_monetary_total']['allowance_total_amount'] * $abs)
            ->setPrepaidAmount($invoice_data['legal_monetary_total']['prepaid_amount'] * $abs)
            ->setPayableAmount($invoice_data['legal_monetary_total']['payable_amount'] * $abs);

        // Tax scheme
        $taxScheme = (new \BaseetApp\UBL\TaxScheme())
            ->setId("VAT");

        $taxTotal = (new \BaseetApp\UBL\TaxTotal())
            ->setTaxAmount($invoice_data['tax_total']['tax_amount'] * $abs);

        foreach ($invoice_data['tax_total']['tax_subtotal'] as $tax_subtotal) {
            $taxCategory = (new \BaseetApp\UBL\TaxCategory())
                ->setPercent($tax_subtotal['tax_category']['percent'])
                ->setTaxScheme($taxScheme);

            $taxSubTotal = (new \BaseetApp\UBL\TaxSubTotal())
                ->setTaxableAmount($tax_subtotal['taxable_amount'] * $abs)
                ->setTaxAmount($tax_subtotal['tax_amount'] * $abs)
                ->setTaxCategory($taxCategory);

            $taxTotal->addTaxSubTotal($taxSubTotal);
        }

        $tax_category_list = array();
        foreach ($invoice_data['allowance_charge']['tax_category_list'] as $tax_category) {
            $tax_category_list[] =
                (new \BaseetApp\UBL\TaxCategory)
                ->setId(
                    $tax_category['id'],
                    array(
                        'schemeID' => "UN/ECE 5305",
                        'schemeAgencyID' => "6"
                    )
                )
                ->setPercent($tax_category['percent'])
                ->setTaxScheme(
                    (new \BaseetApp\UBL\TaxScheme)
                        ->setId("VAT")
                );
        }

        $invoice_allowance_charges = array();
        foreach ($invoice_data['allowance_charge']['allowance_92_list'] as $allowance_92) {
            $invoice_allowance_charges[] = (new \BaseetApp\UBL\AllowanceCharge)
                ->setChargeIndicator(false)
                ->setAllowanceChargeReason($allowance_92['reason'])
                ->setAmount($allowance_92['amount'])
                ->setTaxCategoryList($tax_category_list);
        }

        $invoice_payment_means = (new \BaseetApp\UBL\PaymentMeans)
        ->setPaymentMeansCode($invoice_data['payment_means']['PaymentMeansCode']);
        if($invoice_data['payment_means']['InstructionNote']){
            $invoice_payment_means->setInstructionNote($invoice_data['payment_means']['InstructionNote']);
        }

        $invoice = (new \BaseetApp\UBL\Invoice())
            ->setUBLExtensions($UBLExtensions)
            ->setUUID($invoice_data['UUID'])
            ->setId($invoice_data['id'])
            ->setInvoiceTypeCode($invoice_data['invoice_type_code'])
            ->setInvoiceSubType($invoice_data['invoice_subtype'])
            ->setIssueDate($invoice_data['issue_date'])
            ->setIssueTime($invoice_data['issue_time'])
            ->addAdditionalDocumentReference($additionalDocumentReference1)
            ->addAdditionalDocumentReference($additionalDocumentReference2)
            ->Signature(new \BaseetApp\UBL\Signature)
            ->setAccountingSupplierParty($supplierCompany)
            ->setAccountingCustomerParty($clientCompany)
            ->setPaymentMeans($invoice_payment_means)
            ->setAllowanceCharges($invoice_allowance_charges)
            ->setInvoiceLines($invoiceLines)
            ->setLegalMonetaryTotal($legalMonetaryTotal)
            ->setTaxTotal($taxTotal);

        if(isset($invoice_data['billing_reference'])){

            $issue_timestamp = strtotime($invoice_data['billing_reference']['invoice_document_reference']['issue_date']);
            $issue_datetime_object = new \DateTime();
            $issue_datetime_object->setTimestamp($issue_timestamp);

            $invoice_document_reference= (new \BaseetApp\UBL\InvoiceDocumentReference)
            ->setId($invoice_data['billing_reference']['invoice_document_reference']['id'])
            ->setIssueDate($issue_datetime_object);

            $invoice_billing_reference = (new \BaseetApp\UBL\BillingReference)->setInvoiceDocumentReference($invoice_document_reference);
            $invoice->setBillingReference($invoice_billing_reference);
        }
    
        $generator = new \BaseetApp\UBL\Generator();
        $outputXMLString = $generator->invoice($invoice);

        $dom = new \DOMDocument;
        // $dom->loadXML(mb_convert_encoding($outputXMLString, 'HTML-ENTITIES', 'UTF-8'));
        $dom->loadXML($outputXMLString);

        $temp_path = APPPATH . "libraries/zatca-xml/zatca-logs/" . $invoice_data['sale_id']; //check temp folder permission r/w
        if (!file_exists($temp_path)) {
            mkdir($temp_path, 0777, true);
        }

        $orign_working_directory = getcwd();
        chdir($temp_path);

        $dom->save('invoice.xml');

        chdir($orign_working_directory);

        $ret = array(
            'state' => 1,
            'message' => "Invoice xml successfully created.",
            'sale_id' => $invoice_data['sale_id'],
            'data' => $outputXMLString
        );

        if ($log == 0) {
            set_exception_handler('_exception_handler');
        }
        return $ret;
    }

    static public function getUUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
}
