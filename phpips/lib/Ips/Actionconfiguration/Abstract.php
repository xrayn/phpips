<?php
abstract class Ips_Actionconfiguration_Abstract {


	protected $_config=array();
	const ACTION_CONFIG_NAME="ActionConfig";

	
	public function __construct($path=null, $options=null){

		$this->_config[self::ACTION_CONFIG_NAME]=array();
	}

	abstract public function initActionConfig($path=null);
	
	public function getActionConfig(){
		return $this->_config[self::ACTION_CONFIG_NAME];
	}
}