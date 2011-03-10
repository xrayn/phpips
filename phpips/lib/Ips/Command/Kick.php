<?php

class Ips_Command_Kick extends Ips_Command_Abstract {
	
	private static $_instance=null;

	public static function getInstance(){
		if (self::$_instance==null)
			self::$_instance=new Ips_Command_Kick();

		return self::$_instance;
	}

	private function getNumberOfKicks() {
		global $db;

		//Get Number of Kicks
		$user = $db->get_row("SELECT num_kicks_by_ips FROM user WHERE ID='".$_SESSION["userid"]."'");
		$num_kicks = $user->num_kicks_by_ips + 1;

		return $num_kicks;
	}

	protected function realExecute() {
			Ips_Debugger::debug(array("executed Kick Command"=>$this->_data));
			Ips_Debugger::debug(array("CALLED REALEXECUTE"=>$this));
	}

	protected function realSimulate($fileHandle) {

		return true;
	}
}