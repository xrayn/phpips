<?php


class Ips_Command_Log extends Ips_Command_Abstract {
	private static $_instance=null;

	public static function getInstance() {
		if (self::$_instance==null)
		self::$_instance=new Ips_Command_Log();
		return self::$_instance;
	}

	protected function realExecute() {

		//$this->_registry->getCommandConfigFrom($commandName="mail",$key="Email");
		
		Ips_Debugger::debug(array("CALLED REALEXECUTE"=>$this));
		// we don't need to log to the db twice so we just log to the file
		Ips_Debugger::debug(array("executed Log Command"=>$this->_data));

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
		global $phpids_settings;

		$logText = "-------\n";
		$logText.= Output::echoDate("Y-m-d H:i:s", time()).": ";
		$logText.= "Logging to '".$phpids_settings["command_logfile"]."', ";

		if(isset($_SESSION["Vorname"])) {
			$logText.= "attacker: ".$_SESSION["Vorname"]." ".$_SESSION["Nachname"];
		} else {
			$logText.= "attacker: ".$_SERVER['REMOTE_ADDR'];
		}

		$logText.= "\n";

		fwrite($fileHandle, $logText);

		return false;
	}
}

