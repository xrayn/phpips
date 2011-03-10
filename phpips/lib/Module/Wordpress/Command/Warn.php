<?php
class Module_Wordpress_Command_Warn extends Ips_Command_Abstract {
	private static $_instance=null;

	public static function getInstance() {
		if (self::$_instance==null)
		self::$_instance=new self();
		return self::$_instance;
	}

	protected function realExecute() {
	
		die("You sent a malicious request to the Application. Your mother has been sent an note! <br>!");
	}

	protected function realSimulate($fileHandle) {

		$logText = "\n-------\n";
		$logText.= "SIMULATING WARN COMMAND\n";
		$logText.= "-------\n";
		fwrite($fileHandle, $logText);
		$this->_registry->add("SimulationOutputBuffer", $this->_registry->get("SimulationOutputBuffer").$logText);
		return false;
	}
	
}
