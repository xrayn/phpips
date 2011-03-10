<?php
class Module_Wordpress_Command_Ban extends Ips_Command_Abstract {
	private static $_instance=null;

	public static function getInstance() {
		if (self::$_instance==null)
		self::$_instance=new self();
		return self::$_instance;
	}

	protected function realExecute() {
		

		Ips_Debugger::debug(array("CALLED CUSTOM REALEXECUTE"=>$this));
		//BAN THE USER HOWEVER		
		die("Congraz you are banned! Your mother has been sent an note, no kidding! I'm dying now for you! Bye!<br>!");
		
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
