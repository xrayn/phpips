<?php
class Custom_Command_Module_Test_Warn extends Ips_Command_Abstract {
	private static $_instance=null;

	public static function getInstance() {
		if (self::$_instance==null)
		self::$_instance=new self();
		return self::$_instance;
	}

	protected function realExecute() {
		//global $phpids_settings;
		header("Location: warning.php");
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