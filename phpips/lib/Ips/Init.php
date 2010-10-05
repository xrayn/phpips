<?php
//require_once 'phpips/lib/classes/class.IpsRegistry.inc.php';
require_once(PATH_TO_ROOT."phpips/lib/IpsClassLoader.php");

class Ips_Init {
	private static $_instance=null;
	protected $_configFile=null;
	/**
	 *
	 * @var Ips_Registry
	 */
	protected $_registry=null;
	public static function init($configFile=null){
		if (self::$_instance==null)
		self::$_instance=new Ips_Init($configFile);

		return self::$_instance;
	}

	protected function __construct($configFile){
		//first of all load autoloader
		spl_autoload_register(array("IpsClassLoader","autoload"));
		$this->__init($configFile);

	}

	protected function __clone(){}

	protected function __init($configFile=null){
		if ($configFile!=null && file_exists(PATH_TO_ROOT.$configFile)){
			$this->_configFile=PATH_TO_ROOT.$configFile;
		}
		else {
			throw new Exception("File ".PATH_TO_ROOT.$configFile." was not found");
		}
		//create new Registry instance!
		$this->_registry=Ips_Registry::getInstance();

		//load configuration
		// later do this from an ini file!
		if ($this->_configFile==null){
			// do some default things.

			$IpsActionConfig=Ips_Configuration_Factory::createConfig("ini",PATH_TO_ROOT."phpips/lib/Config/ActionConfig.ini");
			$this->_registry->setActionConfiguration($IpsActionConfig);
			$this->_registry->setTags(array("sqli","xss","rce","dos","csrf","id","lfi","rfe","dt"));
			$this->_registry->enableDebug();
			$this->_registry->disableSimulation();
		}
		else {
			$this->_parseConfig();
		}
	}
	protected function _parseConfig(){
		//readin configuration

		/*
		 *
		 * caution here, the order is significant. some things must be loaded before
		 * another. e.g. Command Prefix Path must be set earlier than ActionConfig, cause this one loads
		 * Command Classes
		 *
		 */
		require_once PATH_TO_ROOT.'phpips/lib/external/Zend/Config/Ini.php';
		$config=new external_Zend_Config_Ini($this->_configFile);
		
		$config_array=$config->get("BaseConfig");
		//$config_array=parse_ini_file($this->_configFile,true);
		
		if (preg_match("/^[Oo][Nn]$/",$config_array->get("DebbuggingMode"))){
			$this->_registry->enableDebug();
		}
		else {
			$this->_registry->disableDebug();
		}
		if ($config_array->get("BasePath")!=""){
			$this->_registry->setBasePath($config_array->get("BasePath"));
		}
		else {
			throw new Exception("INIT: No BasePath set in System.ini. Cannot proceed!");
		}
		
		if (preg_match("/^[Oo][Nn]$/",$config_array->get("SimulationMode"))){
			$this->_registry->enableSimulation();
		}
		else {
			$this->_registry->disableSimulation();
		}
		if ($config_array->get("DefinedTags")!=""){
			$this->_registry->setTags(explode(",", strtolower($config_array->get("DefinedTags"))));
		}
		if ($config_array->get("ExternalSessionManagementMode")=="On"){
			//enable externalSession Manager
			//use defined static method to manage sessions
			$this->_registry->
			setExternalSessionManager(
				$config_array->get(ExternalSessionManagement)->get("Class"),
				$config_array->get(ExternalSessionManagement)->get("Method")
			);
		} 
		if ($config_array->get("UseCustomCommands")=="On"){
			if ($config_array->get("CustomCommandModuleName")==""){
				// if this is not set use Default Module instead
				$this->_registry->disableCustomCommands();
					
			}
			else {
				$this->_registry->setCommandModule($config_array->get("CustomCommandModuleName"));
			}
		}


		else {
			$this->_registry->setTags(array("sqli","xss","rce","dos","csrf","id","lfi","rfe","dt"));
		}
		if ($config_array->get("ActionConfig")->get("Type")!=""){
			/*
			 * ActionConfiguration is a bit trickier, handle it in a sepaerate method.
			 */
			Ips_Debugger::debug($this->_registry);
			$this->_parseActionConfig($config_array->get("ActionConfig")->toArray());
		}

		//handle the CommandConfig in seperate method

		if ($config_array->get("CommandConfig")){
			$this->_parseCommandConfig($config_array->get("CommandConfig"));
		}

		Ips_Debugger::debug($this->_registry);

	}
	protected function _parseActionConfig($actionConfig=null){
		// Caller Method gives us an array!

		//till now we only can use ini files so only handle this.
		if (preg_match("/^[Ii][Nn][Ii]$/", $actionConfig["Type"])){
			$path=$actionConfig["Path"];
			if (isset($actionConfig["Path"]) && file_exists($this->_registry->getBasePath().$path)){
				Ips_Debugger::debug($path);
				
				$IpsActionConfig=Ips_Configuration_Action_Factory::createConfig("ini",array("path"=>$path));
				$this->_registry->setActionConfiguration($IpsActionConfig);
			}
			else {
				throw new Exception("File ".$this->_registry->getBasePath().$path." could not be found");
			}
		}
		else {
			throw new Exception("IPS_INIT: Currently the type ".$actionConfig["Type"]." is not Supported.");
		}
		Ips_Debugger::debug($actionConfig);

	}

	/**
	 * Add all key=>value pairs found in the config.
	 * This is used to configure variables which should be accessed in the commands l8ter.
	 * @param unknown_type $commandConfig
	 */
	protected function _parseCommandConfig($commandConfig){
		foreach ($commandConfig as $name=>$value){
			$this->_registry->addCommandConfigValue($name,$value);
		}
		//var_dump($this->_registry->getCommandConfigFrom(""));
	}

}