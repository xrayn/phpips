<?php

class Ips_Registry {
	/**
	 *
	 * @var Ips_Registry
	 */
	private static $_instance=null;

	protected $_values=array();

	const KEY_SIMULATION_CONFIG_MODE="SimulationMode";
	const KEY_DEBUGGER_CONFIG_MODE="DebuggerMode";
	const KEY_CONFIGURATION="ActionConfiguration";
	const KEY_TAG_NAMES = "TagNames";

	protected function __construct() {
		//set default values
		$this->_values[self::KEY_DEBUGGER_CONFIG_MODE]=false;
		$this->_values[self::KEY_SIMULATION_CONFIG_MODE]=true;
		$this->_values[self::KEY_TAG_NAMES]=array("sqli","xss","rce","dos","csrf","id","lfi","rfe","dt");
	}
	private function __clone(){
	}
	public static function getInstance(){
		if (self::$_instance==null)
		self::$_instance=new Ips_Registry();

		return self::$_instance;
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
	public function getTags(){
		return $this->_values[self::KEY_TAG_NAMES];
	}
	public function setActionConfiguration(Ips_Configuration_Abstract $config){
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
			$this->_delete($key);
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