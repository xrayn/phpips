<?php
class Custom_Command_Module_Test_Log extends Ips_Command_Abstract {
	private static $_instance=null;

	public static function getInstance() {
		if (self::$_instance==null)
		self::$_instance=new self();
		return self::$_instance;
	}

	protected function realExecute() {
		//global $phpids_settings;

		Ips_Debugger::debug(array("CALLED CUSTOM REALEXECUTE"=>$this));
		// we don't need to log to the db twice so we just log to the file
		Ips_Debugger::debug(array("executed CUSTOM Log Command"=>$this->_data));

		$logfile = "/tmp/mylog";

		if(!empty($logfile)) {
			//$logfile = realpath(PATH_TO_ROOT) . "/" . $logfile;
			$fh = fopen($logfile, "a+");
		}

		fwrite($fh, "----------\n");
		fwrite($fh, "Date: ".date("Y-m-d H:i:s",time())."\n");
		fwrite($fh, "Action: Log Triggered\n");
		fwrite($fh, "Attacker IP: ".$_SERVER['REMOTE_ADDR']."\n");

		foreach ($this->_data as $data){
			fwrite($fh,print_r($data,true));
		}
	}

	protected function realSimulate($fileHandle) {

		Ips_Debugger::debug(array("CALLED CUSTOM REALSIMULATE"=>$this));

		$logText = "\n-------\n";
		$logText.= "SIMULATING LOGGING COMMAND\n";
		$logText.= "Logging to file /tmp/mylog\n";
		$logText.= "-------\n";
		fwrite($fileHandle, $logText);

		return false;
	}

}