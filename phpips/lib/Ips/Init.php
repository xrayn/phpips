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
		$config_array=parse_ini_file($this->_configFile,true);

		if (preg_match("/^[Oo][Nn]$/",$config_array["BaseConfig"]["DebbuggingMode"])){
			$this->_registry->enableDebug();
		}
		else {
			$this->_registry->disableDebug();
		}
		if (isset($config_array["BaseConfig"]["BasePath"]) && $config_array["BaseConfig"]["BasePath"]!=""){
			$this->_registry->setBasePath($config_array["BaseConfig"]["BasePath"]);
		}
		else {
			throw new Exception("INIT: No BasePath set in System.ini. Cannot proceed!");
		}
		
		if (preg_match("/^[Oo][Nn]$/",$config_array["BaseConfig"]["SimulationMode"])){
			$this->_registry->enableSimulation();
		}
		else {
			$this->_registry->disableSimulation();
		}
		if ($config_array["BaseConfig"]["DefinedTags"]!=""){
			$this->_registry->setTags(explode(",", strtolower($config_array["BaseConfig"]["DefinedTags"])));
		}
		if ($config_array["BaseConfig"]["ExternalSessionManagementMode"]=="On"){
			//enable externalSession Manager
			//use defined static method to manage sessions
			$this->_registry->
			setExternalSessionManager(
				$config_array["BaseConfig"]["ExternalSessionManagement"]["Class"],
				$config_array["BaseConfig"]["ExternalSessionManagement"]["Method"]
			);
		} 
		if ($config_array["BaseConfig"]["UseCustomCommands"]=="On"){
			if ($config_array["BaseConfig"]["CustomCommandModuleName"]==""){
				// if this is not set use Default Module instead
				$this->_registry->disableCustomCommands();
					
			}
			else {
				$this->_registry->setCommandModule($config_array["BaseConfig"]["CustomCommandModuleName"]);
			}
		}


		else {
			$this->_registry->setTags(array("sqli","xss","rce","dos","csrf","id","lfi","rfe","dt"));
		}
		if ($config_array["BaseConfig"]["ActionConfig"]["Type"]!=""){
			/*
			 * ActionConfiguration is a bit trickier, handle it in a sepaerate method.
			 */
			$this->_parseActionConfig($config_array["BaseConfig"]["ActionConfig"]);
		}

		//handle the CommandConfig in seperate method

		if (isset($config_array["CommandConfig"])){
			$this->_parseCommandConfig($config_array["CommandConfig"]);
		}

		Ips_Debugger::debug($this->_registry);

	}
	protected function _parseActionConfig($actionConfig=null){
		// Caller Method gives us an array!

		//till now we only can use ini files so only handle this.
		if (preg_match("/^[Ii][Nn][Ii]$/", $actionConfig["Type"])){
			$path=$actionConfig["Path"];
			if (isset($actionConfig["Path"]) && file_exists($path)){
				Ips_Debugger::debug($path);
				$IpsActionConfig=Ips_Actionconfiguration_Factory::createConfig("ini",array("path"=>$path));
				$this->_registry->setActionConfiguration($IpsActionConfig);
			}
			else {
				throw new Exception("File ".$path." could not be found");
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