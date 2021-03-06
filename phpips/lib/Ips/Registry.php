<?php

class Ips_Registry {
	/**
	 *
	 * @var Ips_Registry
	 */
	private static $_instance=null;

	protected $_values=array();
	const KEY_BASE_PATH="BasePath";
	const KEY_SIMULATION_CONFIG_MODE="SimulationMode";
	const KEY_SIMULATION_CONFIG_LOGFILE="SimulationLogfile";
	const KEY_DEBUGGER_CONFIG_MODE="DebuggerMode";
	const KEY_CONFIGURATION="ActionConfiguration";
	const KEY_TAG_NAMES = "TagNames";
	const KEY_COMMAND_MODULE_NAME="CommandsModuleName";
	const KEY_USE_CUSTOM_COMMANDS="UseCustomCommands";
	const KEY_ADDITIONAL_COMMAND_CONFIG="AdditionalCommandConfig";
	const KEY_EXTERNAL_SESSION_MANAGER_MODE="ExternalSessionManagerMode";
	const KEY_EXTERNAL_SESSION_MANAGER_CLASS="ExternalSessionManagerClass";
	const KEY_EXTERNAL_SESSION_MANAGER_METHOD="ExternalSessionManagerMethod";
	const KEY_SIMULATION_OUTPUT_BUFFER="SimulationOutputBuffer";
	const KEY_IDS_REPORT="IdsReport";
	const KEY_SESSION_IMPACT="SessionImpact";
	protected function __construct() {
		//set default values
		$this->_values[self::KEY_DEBUGGER_CONFIG_MODE]=false;
		$this->_values[self::KEY_SIMULATION_CONFIG_MODE]=true;
		$this->_values[self::KEY_SIMULATION_CONFIG_LOGFILE]="/dev/null";
		$this->_values[self::KEY_TAG_NAMES]=array("sqli","xss","rce","dos","csrf","id","lfi","rfe","dt");
		$this->_values[self::KEY_USE_CUSTOM_COMMANDS]=false;
		$this->_values[self::KEY_COMMAND_MODULE_NAME]="Default";
		$this->_values[self::KEY_ADDITIONAL_COMMAND_CONFIG]=array();
		$this->_values[self::KEY_EXTERNAL_SESSION_MANAGER_MODE]=false;
		$this->_values[self::KEY_EXTERNAL_SESSION_MANAGER_CLASS]=null;
		$this->_values[self::KEY_EXTERNAL_SESSION_MANAGER_METHOD]=null;
		$this->_values[self::KEY_SIMULATION_OUTPUT_BUFFER]="";
		$this->_values[self::KEY_IDS_REPORT]=null;

	}
	private function __clone(){
	}

	public function setBasePath($path){
		$this->_values[self::KEY_BASE_PATH]=$path;
		return $this;
	}
	public function getBasePath(){
		return $this->_values[self::KEY_BASE_PATH];

	}

	public static function getInstance(){
		if (self::$_instance==null)
		self::$_instance=new Ips_Registry();

		return self::$_instance;
	}
	public function setSessionImpact($impact){
		$this->_values[self::KEY_SESSION_IMPACT]=$impact;
		return $this;
	}
	public function getSessionImpact($impact){
		return $this->_values[self::KEY_SESSION_IMPACT];
	}
	public function getHighestSessionImpact(){
		if ($this->_values[self::KEY_SESSION_IMPACT]==null){
			throw new Exception('$this->_values['.self::KEY_SESSION_IMPACT.'] was null, something is wrong with the session!');
		}
		$highestImpact=0;
		foreach ($this->_values[self::KEY_SESSION_IMPACT] as $tag=>$impact){
			if($impact>$highestImpact){
				$highestImpact=$impact;
			}
		}
		$result=$highestImpact;
		return $result;
	}
	public function setExternalSessionManager($className="self",$methodName=null){
		$this->_values[self::KEY_EXTERNAL_SESSION_MANAGER_MODE]=true;
		$this->_values[self::KEY_EXTERNAL_SESSION_MANAGER_CLASS]=$className;
		$this->_values[self::KEY_EXTERNAL_SESSION_MANAGER_METHOD]=$methodName;
		return $this;
	}
	public function setIdsReport(IDS_Report $report){
		$this->_values[self::KEY_IDS_REPORT]=$report;
		return $this;
	}
	/**
	 *
	 * @return IDS_Report
	 */
	public function getidsReport(){
		return $this->_values[self::KEY_IDS_REPORT];
	}
	//	public function enableExternalSessionManagerMode(){
	//		$this->_values[self::KEY_EXTERNAL_SESSION_MANAGER_MODE]=true;
	//	}
	public function setSimulationLogFile($logfile){
		$this->_values[self::KEY_SIMULATION_CONFIG_LOGFILE]=$logfile;
		return $this;
	}
	public function getSimulationLogFile(){
		return $this->_values[self::KEY_SIMULATION_CONFIG_LOGFILE];
	}
	public function disableExternalSessionManagerMode(){
		$this->_values[self::KEY_EXTERNAL_SESSION_MANAGER_MODE]=false;

	}
	public function isExternalSessionManagerEnabled(){
		return $this->_values[self::KEY_EXTERNAL_SESSION_MANAGER_MODE];
	}
	public function getExternalSessionManager(){
		if ($this->_values[self::KEY_EXTERNAL_SESSION_MANAGER_MODE]==true){
			return array( "className"=>$this->_values[self::KEY_EXTERNAL_SESSION_MANAGER_CLASS],
	 				  "methodName"=>$this->_values[self::KEY_EXTERNAL_SESSION_MANAGER_METHOD]);
		}
		else{
			throw new Exception("Cannot load ExternalSessionManager when not enabled!");
		}
	}
	public function addCommandConfigValue($key,$value){
		$this->_values[self::KEY_ADDITIONAL_COMMAND_CONFIG][$key]=$value;
		return $this;
	}
	/**
	 * Get the config of the Command
	 *
	 * If $commandName is null, return complete CommandConfig
	 * If $key==null, return complete Array of CommandName
	 * @param string $commandName
	 * @param string $key
	 * @throws Exception
	 * @return mixed. Can be anything
	 */
	public function getCommandConfigFrom($commandName=null,$key=null){
		if ($commandName===null){
			return	$this->_values[self::KEY_ADDITIONAL_COMMAND_CONFIG];
		}
		else {
			if (isset($this->_values[self::KEY_ADDITIONAL_COMMAND_CONFIG][$commandName])){
				$configPointer=&$this->_values[self::KEY_ADDITIONAL_COMMAND_CONFIG][$commandName];
			}
			else {
				throw new Exception("REGISTRY: Command [".$commandName."] not found in ".self::KEY_ADDITIONAL_COMMAND_CONFIG);
			}
		}
		if ($key===null){
			return $configPointer;
		}
		else if (isset($configPointer[$key])){
			return $configPointer[$key];
		}
		else {
			throw new Exception("REGISTRY: Key [".$key."] not found in ".self::KEY_ADDITIONAL_COMMAND_CONFIG);
		}

	}

	protected function set($key,$value){
		$this->_values[$key]=$value;
		return $this;
	}
	//	protected function get($key){
	//		return $this->_values[$key];
	//	}
	public function setTags($tags=null){
		if ($tags!=null && is_array($tags)){
			//if null setup all tags
			$this->_values[self::KEY_TAG_NAMES]=$tags;
		}
		return $this;
	}

	public function setCommandModule($moduleName="Default"){
		if ($moduleName!="default"){
			$this->_values[self::KEY_USE_CUSTOM_COMMANDS]=true;
		} else {
			$this->_values[self::KEY_USE_CUSTOM_COMMANDS]=false;
		}

		$this->_values[self::KEY_COMMAND_MODULE_NAME]=$moduleName;
		return $this;
	}
	/**
	 * This is used in the factory which creates the commands
	 * The factory get a Command Name as a Value and loads the apropriate Class and the singleton Object.
	 *
	 *
	 * @return string
	 */
	public function getCommandModulePrefix() {
		if ($this->_values[self::KEY_COMMAND_MODULE_NAME]=="Default"){
			return "Ips_Command_";
		}
		else {
			return "Module_".$this->_values[self::KEY_COMMAND_MODULE_NAME]."_Command_";
		}
	}
	public function disableCustomCommands(){
		$this->_values[self::KEY_USE_CUSTOM_COMMANDS]=false;
		$this->_values[self::KEY_COMMAND_MODULE_NAME]=="Default";
		return $this;
	}

	public function getTags(){
		return $this->_values[self::KEY_TAG_NAMES];
	}
	public function setActionConfiguration(Ips_Configuration_Action_Abstract $config){
		$this->_values[self::KEY_CONFIGURATION]=$config;
		return $this;
	}
	public function getActionConfiguration(){
		return $this->_values[self::KEY_CONFIGURATION];
	}

	public function enableDebug(){
		$this->_values[self::KEY_DEBUGGER_CONFIG_MODE]=true;
		return $this;
	}
	public function disableDebug(){
		$this->_values[self::KEY_DEBUGGER_CONFIG_MODE]=false;
		return $this;
	}
	public function enableSimulation(){
		$this->_values[self::KEY_SIMULATION_CONFIG_MODE]=true;
		return $this;
	}
	public function disableSimulation(){
		$this->_values[self::KEY_SIMULATION_CONFIG_MODE]=false;
		return $this;
	}

	public function setDebuggingMode($enable=false){
		if ($enabled===false){
			$this->_values[self::KEY_DEBUGGER_CONFIG_MODE]=false;
		}
		else {
			$this->_values[self::KEY_DEBUGGER_CONFIG_MODE]=true;
		}
		return $this;
	}
	public function setSimulationMode($enable=true){
		if ($enabled===false){
			$this->_values[self::KEY_SIMULATION_CONFIG_MODE]=false;
		}
		else {
			$this->_values[self::KEY_SIMULATION_CONFIG_MODE]=true;
		}
		return $this;
	}
	public function isDebugEnabled(){
		return $this->_values[self::KEY_DEBUGGER_CONFIG_MODE];
	}
	public function isSimulationEnabled(){
		return $this->_values[self::KEY_SIMULATION_CONFIG_MODE];
	}

	public function add($key,$value){
		if (!isset($this->_values[$key])){
			$this->_values[$key]=$value;
		}
		else {
			//delete the value first than add, in delete we can check that no predefined values are deleted
			$this->delete($key);
			$this->_values[$key]=$value;
		}
		return $this;
	}
	protected function delete($key){
		if ($key!=self::KEY_DEBUGGER_CONFIG_MODE && $key != self::KEY_SIMULATION_CONFIG_MODE){
			unset($this->_values[$key]);
		}
		else {
			throw new Exception("It is not possible to delete $key, for setting use the apropriate setter Method");
		}
		return $this;
	}
	public function remove($key){
		$this->delete($key);
		return $this;
	}
	public function get($key){
		if (isset($this->_values[$key])){
			return $this->_values[$key];
		}
		else {
			throw new Exception("REGISTRY: value ".$key." not found in registry!");
		}
	}
}