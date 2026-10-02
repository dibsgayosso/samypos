<?php
class MY_Lang extends CI_Lang
{
    function __construct()
    {
        parent::__construct();
    }
    
    function switch_to($idiom)
    {
        $CI =& get_instance();
        if(is_string($idiom))
        {
            $CI->config->set_item('language',$idiom);
            $loaded = $this->is_loaded;
            $this->is_loaded = array();
                
            foreach(array_keys($loaded) as $file)
            {
                $this->load(str_replace('_lang.php','',$file));    
            }
        }
    }
	
    public function line($line, $log_errors = TRUE)
    {
        $CI =& get_instance();
		$language_value = parent::line($line,$log_errors);
		
		$replacements = array(
			'%BRANDING_NAME%' => $CI->config->item('branding')['name'],
			'%BRANDING_SHORT_NAME%' => $CI->config->item('branding')['short_name'],
			'%BRANDING_DOMAIN%' => $CI->config->item('branding')['domain'],
			
		);
		
		return str_replace(array_keys($replacements),array_values($replacements),$language_value);
	}
	
}

?>
