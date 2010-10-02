<?php

if (file_exists(PATH_TO_ROOT . "phpips/lib/FirePHPCore/FirePHP.class.php") && 
	file_exists(PATH_TO_ROOT . "phpips/lib/FirePHPCore/FirePHP.class.php")){
		require_once 'phpips/lib/FirePHPCore/fb.php';		
	}

class Ips_Debugger {
	public static function debug($var){
		global $phpids_settings;
		//check if debugging is enabled!
		if ($phpids_settings["debug_activated"]){
		//
			
			$fb=new FB();
			//$fb->log(debug_backtrace());
			$fb->log($var);
		}
	}
}