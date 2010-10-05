<?php


class Ips_Configuration_Action_Ini extends Ips_Configuration_Action_Abstract{


	public function initActionConfig($path=null){
		$config=new external_Zend_Config_Ini(PATH_TO_ROOT.$path);
		
		$config_array=array();
		
		foreach ($config as $actionName=>$actionConfig){
			$config_array[$actionName]=array();
			// this consists of currently 3 things
			$treshholds=$actionConfig->get("thresholds");
			$priority=$actionConfig->get("priority");
			$commandList=$actionConfig->get("commandList");
			$config_array[$actionName]["thresholds"]=$treshholds->toArray();
			$config_array[$actionName]["priority"]=$priority;	
			$config_array[$actionName]["commandList"]=$commandList->toArray();
		}
		$this->_config[self::ACTION_CONFIG_NAME]=$config_array;
	}
	


}