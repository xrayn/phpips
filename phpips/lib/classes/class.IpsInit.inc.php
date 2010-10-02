<?php
require_once 'phpips/lib/classes/class.IpsRegistry.inc.php';


class IpsInit {
	private static $_instance=null;

	public static function getInstance(){
		if (self::$_instance==null)
		self::$_instance=new IpsInit();

		return self::$_instance;
	}
	
	protected function __construct(){
	
		$this->__init();
	
	}
	
	protected function __clone(){}

	protected function __init(){
		//create new Registry instance!
		$registry=IpsRegistry::getInstance();
		
		//load configuration
		// later do this from an ini file!
		$IpsActionConfig=IpsConfigurationFactory::createConfig("ini",PATH_TO_ROOT."phpips/lib/Config/ActionConfig.ini");
		$registry->setActionConfiguration($IpsActionConfig);
		$registry->setTags(array("sqli","xss","rce","dos","csrf","id","lfi","rfe","dt"));
	}


}