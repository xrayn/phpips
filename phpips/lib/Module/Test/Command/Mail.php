<?php
class Module_Test_Command_Mail extends Ips_Command_Abstract {
	private static $_instance=null;

	public static function getInstance() {
		if (self::$_instance==null)
		self::$_instance=new self();
		return self::$_instance;
	}

	protected function realExecute() {
		Ips_Debugger::debug(array("CALLED CUSTOM REALEXECUTE"=>$this));
		//send a mail
		$mailto=$this->_registry->getCommandConfigFrom("mail","Email");
		mail($mailto,"IPS SYSTEM DETECTED AN ATTACK","INSPECT THE SYSTEM");
	}

	protected function realSimulate($fileHandle) {
		
		Ips_Debugger::debug(array("CALLED CUSTOM REALSIMULATE"=>$this));

		$logText = "\n-------\n";
		$logText.= "SIMULATING MAIL COMMAND\n";
		$logText.= "Sending E-Mail\n";
		$logText.= "-------\n";
		$this->_registry->add("SimulationOutputBuffer", $this->_registry->get("SimulationOutputBuffer").$logText);
		fwrite($fileHandle, $logText);

		return false;
	}


}