<?php
class Custom_Command_Module_Test_Ban extends Ips_Command_Abstract {

	private static $_instance=null;

	public static function getInstance() {
		if (self::$_instance==null)
		self::$_instance=new self();
		return self::$_instance;
	}

	protected function realExecute() {
		//global $phpids_settings;

		Ips_Debugger::debug(array("CALLED CUSTOM REALEXECUTE"=>$this));
		//BAN THE USER HOWEVER
		
		//Then just die()
		die("You sent a malicious request to the Application. I'm dying now for you! Bye!");
	}

	protected function realSimulate($fileHandle) {

		Ips_Debugger::debug(array("CALLED CUSTOM REALSIMULATE"=>$this));
		
		$logText = "\n-------\n";
		$logText.= "SIMULATING BAN COMMAND\n";
		$logText.= "Banning User from System\n";
		$logText.= "-------\n";
		fwrite($fileHandle, $logText);

		return false;
	}
}