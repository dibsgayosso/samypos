<?php
function character_limiter($str, $n = 500, $end_char = '&#8230;')
{
	if (strlen($str ? $str : '') < $n)
	{
		return $str;
	}

	if (function_exists('mb_substr'))
	{
		return mb_substr($str,0, $n).$end_char;
	}
	
	return substr($str,0, $n).$end_char;
}

function replace_newline($string) 
{
	return (string)str_replace(array("\r", "\r\n", "\n"), '', $string);
}

function number_pad($number,$n) 
{
	return str_pad($number,$n,"0",STR_PAD_LEFT);
}

function H($input)
{
	return html_escape($input, FALSE);
}

//From http://stackoverflow.com/a/26537463/627473
function escape_full_text_boolean_search($search)
{
	$CI =& get_instance();

	$innodb_ft_min_token_size = 3;
	
	$innodb_ft_min_token_size_query = 'SHOW variables WHERE Variable_name="innodb_ft_min_token_size"';
	$innodb_ft_min_token_size_row = $CI->db->query($innodb_ft_min_token_size_query)->row_array();
	
	if (isset($innodb_ft_min_token_size_row['Value']))
	{
		$innodb_ft_min_token_size = $innodb_ft_min_token_size_row['Value'];
	}

	
	$search = trim(preg_replace('/[+\-><*\(\)~\"@]+/', ' ', trim($search)));
	
	//Replace all white space with just 1 extra space. This cleans up searches with lots of spaces between chars
	$search = preg_replace('/\s+/', ' ',$search);
	if(trim($search) == "")
	{
		//If we have no search return a bar character is this prevents fatal error
		$search = '|';
	}
	
	$search_terms = explode(' ',$search);
	$ft_search = '';
	
	foreach($search_terms as $search_term)
	{		
		//TODO do NOT hardcode 3 base it on innodb_ft_min_token_size
		$ft_search.= (strlen($search_term <=$innodb_ft_min_token_size) ? '+' : '').$search_term.'*';
	}
	
	return $ft_search;
}

function does_contain_only_digits($string)
{
	return (preg_match('/^[0-9]+$/', $string));
}

function clean_string($string) 
{	
	$CI =& get_instance();
	return $CI->input->clean_string($string);
}

function boolean_as_string($val)
{
	if ($val)
	{
		return lang('common_yes');		
	}
	return lang('common_no');
}

function get_full_category_path($val)
{
	$CI =& get_instance();
	$CI->load->model('Category');
	return $CI->Category->get_full_path($val);
}

function item_name_formatter($val,$data)
{
	$CI =& get_instance();
	
	$return = '';
	$link = '<a class="'.$data['low_inventory_class'].'" href="'.site_url('home/view_item_modal').'/'.$data['item_id']."?redirect=".strtok($CI->uri->uri_string(),'/').'" data-toggle="modal" data-target="#myModal">'.H($val).'</a>';
	$return.=$link;
	
	if ($data['variation_count'])
	{
		$return.='&nbsp;<span class="ion-ios-toggle-outline"></span>';
	}
	
	return $return;
}

function item_kit_name_formatter($val, $data)
{
	$CI =& get_instance();
	return '<a href="'.site_url('home/view_item_kit_modal').'/'.$data['item_kit_id']."?redirect=".strtok($CI->uri->uri_string(),'/').'" data-toggle="modal" data-target="#myModal">'.H($val).'</a>';
}

function item_kit_name_data_function($item_kit)
{	
	$CI =& get_instance();
	$data = array();
	
	$data['item_kit_id'] = $item_kit->item_kit_id;
	
	return $data;
}

function item_quantity_data_function($item)
{
	$CI =& get_instance();
	$data = array();
	
	$data['item_id'] = $item->item_id;
	$data['low_inventory_class']='';
	$data['is_service'] = $item->is_service;
	$data['variation_count'] = $item->variation_count;
	if($CI->config->item('highlight_low_inventory_items_in_items_module') && $item->quantity !== NULL && ($item->quantity<=0 || $item->quantity <= $item->reorder_level))
	{
		$data['low_inventory_class'] = "text-danger";
	}
	
	return $data;
}

function item_number_data_function($item)
{
	$CI =& get_instance();
	$data = array();
	
	$data['item_number'] = $item->item_number;
	
	return $data;
}

function item_number_formatter($val, $data)
{
	return $val;
}

function commission_to_amount($item)
{
	if ($item->commission_fixed)
	{
		return $item->commission_fixed;
	}
	elseif($item->commission_percent)
	{
		if ($item->commission_percent_type == 'selling_price')
		{
			return ($item->commission_percent/100)*$item->unit_price;
		}
		elseif($item->commission_percent_type == 'profit')
		{
			return ($item->commission_percent/100)*($item->unit_price - $item->cost_price);			
		}
	}
	
	return 0;
	
}

function item_id_data_function($item)
{
	$CI =& get_instance();
	$data = array();
	
	$data['item_id'] = $item->item_id;
	return $data;
}


function commission_amount_format($item,$data)
{
	return to_currency($data);
}

function dimensions_format($item,$data)
{
	if ($data['length'] && $data['width'] && $data['height'])
	{
		return to_quantity($data['length']).' x '.to_quantity($data['width']). ' x '.to_quantity($data['height']);
	}
	return lang('common_not_set');
}

function dimensions_data($item)
{
	$CI =& get_instance();
	$data = array();
	
	$data['length'] = $item->length;
	$data['width'] = $item->width;
	$data['height'] = $item->height;
	return $data;
}

function item_quantity_format($val,$data)
{
	 $val = to_quantity($val);
	 
	 if (!$data['is_service'])
	 {
		 return '<a class="'.$data['low_inventory_class'].'" href="'.site_url('items/inventory').'/'.$data['item_id'].'?redirect=items&quick_edit=1">'.H($val).'</a>';
	 }
	 return lang('common_na');
}

function to_percent($val)
{
	$val = to_quantity($val, false);
	
	if ($val!=='')
	{
		return $val."%";
	}
	
	return lang('common_not_set');
}

function commission_percent_type_formater($val)
{
	if ($val == 'selling_price')
	{
		return lang('common_unit_price');
	}
	elseif($val == 'profit')
	{
		return lang('common_profit');		
	}
	
	return lang('common_not_set');
}

function strsame($val)
{
	return $val;
}

function add_quotes_and_escape($str) 
{
		$CI =& get_instance();
		$return = $CI->db->escape($str);
		return $return;
}

function to_currency_and_edit_item_price($val,$data)
{
	$item_id = $data['item_id'];
	return anchor("items/pricing/$item_id?redirect=items&quick_edit=1", to_currency($val));
}

function to_currency_and_edit_location_item_price($val,$data)
{
	$item_id = $data['item_id'];
	return anchor("items/location_settings/$item_id?redirect=items&quick_edit=1", to_currency($val));
}

function to_currency_and_edit_item_kit_price($val,$data)
{
	$item_kit_id = $data['item_kit_id'];
	return anchor("item_kits/pricing/$item_kit_id?redirect=item_kits&quick_edit=1", to_currency($val));
}

function to_currency_and_edit_location_item_kit_price($val,$data)
{
	$item_kit_id = $data['item_kit_id'];
	return anchor("item_kits/location_settings/$item_kit_id?redirect=item_kits&quick_edit=1", to_currency($val));
}

function boolean_as_string_variation($val,$data)
{
	$item_id = $data['item_id'];
	return anchor("items/variations/$item_id?redirect=items&quick_edit=1", boolean_as_string($val));
}

function to_quantity_variation($val,$data)
{
	$item_id = $data['item_id'];
	return anchor("items/variations/$item_id?redirect=items&quick_edit=1", to_quantity($val));
}

//TODO doesn't get country right
function addressToParts($address)
{
	require_once (APPPATH."libraries/AddressHelper/ObjectBase.php");
	require_once (APPPATH."libraries/AddressHelper/ParseAddress.php");
	require_once (APPPATH."libraries/AddressHelper/AddressHelper.php");

	$address = new ParseAddress($address);
	$parts = $address->toArray();
	
	$return = array();
	
	$return['zip'] = $parts['postal_code'];
	$return['city'] = $parts['city'];
	$return['state'] = $parts['state'];
	$return['street'] = $parts['street_addr'];
	$return['country'] = $parts['country'];
	
	return $return;
}

function mystrtoupper($string)
{
	if (function_exists('mb_strtoupper'))
	{
		return mb_strtoupper($string);
	}
	
	return strtoupper($string);
	
}

function mystrtolower($string)
{
	if (function_exists('mb_strtolower'))
	{
		return mb_strtolower($string);
	}
	
	return strtolower($string);	
}

function split_name($name) 
{
    $name = trim($name);
    $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
    $first_name = trim( preg_replace('#'.$last_name.'#', '', $name ) );
    return array($first_name, $last_name);
}

function clean_html($dirty_html)
{
	require_once (APPPATH."libraries/htmlpurifier/library/HTMLPurifier.auto.php");
	$config = HTMLPurifier_Config::createDefault();
	$purifier = new HTMLPurifier($config);
	return $purifier->purify($dirty_html);
}

function make_marked_string_bold_italic_underline($string){
	/*
	**string** for bold
	~~string~~ for italic
	||string|| for underline
	*/
	$bold = '#\*{2}(.*?)\*{2}#';
	$string = preg_replace($bold, '<b>$1</b>', $string);

	$italic = '#\~{2}(.*?)\~{2}#';
	$string = preg_replace($italic, '<i>$1</i>', $string);

	$underline = '#\|{2}(.*?)\|{2}#';
	$string = preg_replace($underline, '<u>$1</u>', $string);

	return $string;
}

function base64UrlEncode($inputStr)
{
	return strtr(base64_encode($inputStr), '+/=', '-_,');
}

function base64UrlDecode($inputStr)
{
	return base64_decode(strtr($inputStr, '-_,', '+/='));
}	


function safe_b64encode($string) 
{
    $data = base64_encode($string);
    $data = str_replace(array('+','/','='),array('-','_',''),$data);
    return $data;
}

function safe_b64decode($string) 
{
    $data = str_replace(array('-','_'),array('+','/'),$string);
    $mod4 = strlen($data) % 4;
    if ($mod4) 
	 {
        $data .= substr('====', $mod4);
    }
    return base64_decode($data);
}

function do_encrypt($simple_string,$encryption_key = NULL) 
{
	$ciphering = "AES-128-CTR";
	$iv_length = openssl_cipher_iv_length($ciphering);
	$options = 0;
  
	// Non-NULL Initialization Vector for encryption
	$encryption_iv = '1234567891011121';
  
	// Store the encryption key
	if (!$encryption_key)
	{
		$encryption_key = "GeeksforGeeks";
	}
    $ciphertext = openssl_encrypt($simple_string, $ciphering,$encryption_key, $options, $encryption_iv);

    return trim(safe_b64encode($ciphertext)); 
} 

function do_decrypt($encryption,$decryption_key = NULL) 
{
	$ciphering = "AES-128-CTR";
	$options = 0;
	
	$encryption = safe_b64decode($encryption);
	// Non-NULL Initialization Vector for decryption
	$decryption_iv = '1234567891011121';
  
	// Store the decryption key
	if (!$decryption_key)
	{
		$decryption_key = "GeeksforGeeks";
	}
	
	// Use openssl_decrypt() function to decrypt the data
	return openssl_decrypt ($encryption, $ciphering, $decryption_key, $options, $decryption_iv);
		

}

//FROM https://stackoverflow.com/a/14167216
function format_phone_number($phoneNumber)
{
    $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);

       if(strlen($phoneNumber) > 10) 
	   {
           $countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
           $areaCode = substr($phoneNumber, -10, 3);
           $nextThree = substr($phoneNumber, -7, 3);
           $lastFour = substr($phoneNumber, -4, 4);

           $phoneNumber = '+'.$countryCode.$areaCode.$nextThree.$lastFour;
       }
       else if(strlen($phoneNumber) == 10) 
	   {
           $areaCode = substr($phoneNumber, 0, 3);
           $nextThree = substr($phoneNumber, 3, 3);
           $lastFour = substr($phoneNumber, 6, 4);

           $phoneNumber = ''.$areaCode.'-'.$nextThree.'-'.$lastFour;
       }
       else if(strlen($phoneNumber) == 7) 
	   {
           $nextThree = substr($phoneNumber, 0, 3);
           $lastFour = substr($phoneNumber, 3, 4);

           $phoneNumber = $nextThree.'-'.$lastFour;
       }

       return $phoneNumber;
  }

function to_cost_code($val)
{
	$CI =& get_instance();
	$config_cost_code = str_split($CI->config->item('cost_code'));
	if(count($config_cost_code) == 10 && $val != ''){
		$cost_code = '';
		foreach(str_split((int)$val) as $n){
			$cost_code .= $n == 0 ? $config_cost_code[9] : $config_cost_code[$n-1];
		}
		return $cost_code;
	}

	return NULL;
}

function get_text_between_delimiters($string, $start, $end)
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function replace_text_between_delimiters($str, $needle_start, $needle_end, $replacement) 
{
    $pos = strpos($str, $needle_start);
    $start = $pos === false ? 0 : $pos + strlen($needle_start);

    $pos = strpos($str, $needle_end, $start);
    $end = $pos === false ? strlen($str) : $pos;

    return substr_replace($str, $replacement, $start, $end - $start);
}



function isHTML($string){
	return $string != strip_tags($string) ? true : false;
}

 function alphanum($str)
 {
 	return preg_replace( '/[^A-Za-z0-9]/', '', $str);
 }
 
 function alphanumplus($str)
 {
 	return preg_replace( '/[^A-Za-z0-9\+]/', '', $str);
 }
 
 if (function_exists('mb_strlen')) {
     function get_bytes($string) {
         return mb_strlen($string, '8bit');
     }
 } else {
     function get_bytes($string) {
         return strlen($string);
     }
 }
?>