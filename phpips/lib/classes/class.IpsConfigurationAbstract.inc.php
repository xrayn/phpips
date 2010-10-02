<?php
abstract class IpsConfigurationAbstract {


	protected $_config=array();
	const ACTION_CONFIG_NAME="ActionConfig";
	const SIMULATION_CONFIG_MODE="SimulationMode";
	const DEBUGGER_CONFIG_MODE="DebuggerMode";
	
	
	public function __construct($path=null, $options=null){
		$this->_config[self::SIMULATION_CONFIG_MODE]=false;
		$this->_config[self::DEBUGGER_CONFIG_MODE]=true;
		$this->_config[self::ACTION_CONFIG_NAME]=array();
	}

	abstract public function initActionConfig($path=null);
	
	public function getActionConfig(){
		return $this->_config[self::ACTION_CONFIG_NAME];
	}
	


}