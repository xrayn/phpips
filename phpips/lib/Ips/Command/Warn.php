<?php




class Ips_Command_Warn extends Ips_Command_Abstract {

	private static $_instance=null;

	public static function getInstance(){
		if (self::$_instance==null)
			self::$_instance=new Ips_Command_Warn();
		return self::$_instance;
	}

	protected function realExecute() {
		Ips_Debugger::debug(array("CALLED REALEXECUTE"=>$this));
		// we don't need to log to the db twice so we just log to the file
		Ips_Debugger::debug(array("executed Warn Command"=>$this->_data));
	}

	protected function realSimulate($fileHandle) {


		return true;
	}
}
