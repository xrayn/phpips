<?php

class IpsClassLoader {

	public static function autoload($className){
		$filename=str_replace("_", "/", $className).".php";
		if (self::exists($className)){
			@include_once $filename;
		}
		else {
			throw new Exception("Classloader: Class ".$className." not Found!");
		}
		Ips_Debugger::debug($filename);
	}

	/**
	 * Check if a Class exists
	 * @param unknown_type $className
	 * @return boolean
	 */
	public static function exists($className){
		$filename=str_replace("_", "/", $className).".php";
		
		if (file_exists(PATH_TO_ROOT."phpips/lib/".$filename)) {
			return true;
		}
		else {
			return false;
		}
	}
}