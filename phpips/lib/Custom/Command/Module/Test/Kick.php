<?php
class Custom_Command_Module_Test_Kick extends Ips_Command_Abstract {
	private static $_instance=null;

	public static function getInstance() {
		if (self::$_instance==null)
		self::$_instance=new self();
		return self::$_instance;
	}

	protected function realExecute() {
		//global $phpids_settings;

		Ips_Debugger::debug(array("CALLED CUSTOM REALEXECUTE"=>$this));
		die("You sent a malicious request to the Application. I'm dying now for you! ");
	}

	protected function realSimulate($fileHandle) {

		Ips_Debugger::debug(array("CALLED CUSTOM REALSIMULATE"=>$this));

		$logText = "\n-------\n";
		$logText.= "SIMULATING KICK COMMAND\n";
		$logText.= "Kicking User from System\n";
		$logText.= "-------\n";
		fwrite($fileHandle, $logText);
		$this->_registry->add("SimulationOutputBuffer", $this->_registry->get("SimulationOutputBuffer").$logText);
		return false;
	}

}