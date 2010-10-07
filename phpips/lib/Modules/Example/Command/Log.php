<?php
class Module_Example_Command_Log extends Ips_Command_Abstract {
	private static $_instance=null;

	public static function getInstance() {
		if (self::$_instance==null)
		self::$_instance=new self();
		return self::$_instance;
	}

	protected function realExecute() {
		
	}

	protected function realSimulate($fileHandle) {


	}

}