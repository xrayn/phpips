<?php
class Module_Example_Command_Ban extends Ips_Command_Abstract {
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
		session_destroy();
		die("You sent a malicious request to the Application. I'm dying now for you! Bye!<br> Session destroyed!");
		
	}

	protected function realSimulate($fileHandle) {

		Ips_Debugger::debug(array("CALLED CUSTOM REALSIMULATE"=>$this));
		
		$logText = "\n-------\n";
		$logText.= "SIMULATING BAN COMMAND\n";
		$logText.= "Banning User from System\n";
		$logText.= "Destroy Session\n";
		$logText.= "dying()!\n";
		$logText.= "-------\n";
		fwrite($fileHandle, $logText);
		$this->_registry->add("SimulationOutputBuffer", $this->_registry->get("SimulationOutputBuffer").$logText);
		return false;
	}
}
