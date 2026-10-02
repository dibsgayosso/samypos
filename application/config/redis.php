<?php
if (getenv('REDIS_HOST'))
{
	$config['redis_host'] = getenv('REDIS_PROTOCOL').'://'.getenv('REDIS_HOST').':'.getenv('REDIS_PORT');	
	$config['redis_auth'] = getenv('REDIS_AUTH') ? getenv('REDIS_AUTH') : '';	
}