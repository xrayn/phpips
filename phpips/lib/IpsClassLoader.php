<?php

class IpsClassLoader {

	public static function autoload($className){
		$filename=str_replace("_", "/", $className).".php";
		@include_once $filename;
		//Ips_Debugger::debug($filename);
	}
}