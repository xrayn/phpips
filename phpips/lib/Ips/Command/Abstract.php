<?php

abstract class Ips_Command_Abstract implements Ips_Command_Interface {
	protected $_data=array();
	//private static $_instance=null;
	protected $_isExecuted=false;
	protected $_execute=false;
	protected $_registry=null;

	public function enableExecute() {
		$this->_execute=true;
		return $this;
	}

	protected function __construct() {
		$this->_registry=Ips_Registry::getInstance();
	}

	public function addData($data) {

		array_push($this->_data,$data);
		return $this;
	}

	abstract protected function realExecute();

	abstract protected function realSimulate($fileHandle);

	public function execute() {
		//IpsDebugger::debug(array("COMMAND EXECUTED"=>$this));

		if(!$this->_isExecuted && $this->_execute) {
			$this->_isExecuted = true;
			$this->realExecute();
		}
	}

	public function simulate() {
		global $phpids_settings;
		$exitSystem = false;

		if(!$this->_isExecuted && $this->_execute) {
			$this->_isExecuted = true;
			$logfile = $phpids_settings["simulation_logfile"];

			if(!empty($logfile)) {
				$logfile = realpath(PATH_TO_ROOT) . "/" . $logfile;
				$fh = fopen($logfile, "a+");
				$exitSystem = $this->realSimulate($fh);
			}
		}

		return $exitSystem;
	}

}
