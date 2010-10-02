<?php

if (file_exists(PATH_TO_ROOT . "phpips/lib/external/FirePHPCore/FirePHP.class.php") && 
	file_exists(PATH_TO_ROOT . "phpips/lib/external/FirePHPCore/FirePHP.class.php")){
		require_once 'phpips/lib/external/FirePHPCore/fb.php';		
	}

class Ips_Debugger {

	public static function debug($var){
		//check if debugging is enabled!
		$registry=Ips_Registry::getInstance();
		
		if ($registry->isDebugEnabled()){
		//
			
			$fb=new FB();
			//$fb->log(debug_backtrace());
			$fb->log($var);
		}
	}
}