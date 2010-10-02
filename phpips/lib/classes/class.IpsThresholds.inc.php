<?php


class IpsThresholds {


	/**
	 * 
	 * @var IpsConfiguration
	 */
	private $_actionConfiguration=null;
	/**
	 * Current Tags from PHPIds
	 * SQLI -> SQL Injection
	 * XSS -> Cross site scripting
	 * RCE -> Remote code execution
	 * DOS -> Denial of service
	 * CSRF -> Cross site request forgery
	 * ID -> Information Disclosure
	 * LFI -> Local file inclusion
	 * RFE -> Remote file execution
	 * DT -> Directory traversal
	 */
	// setup default values
	/*
	* @lookhere: has to be dynamically initialized
	*
	*/
	protected $_tags=array("sqli","xss","rce","dos","csrf","id","lfi","rfe","dt");

	protected $_threshold = null;
	/**
	 * 
	 * @var IpsRegistry
	 */
	protected $_registry=null;
	
	public function __construct() {
		$this->_registry=IpsRegistry::getInstance();
		$this->_actionConfiguration=$this->_registry->getActionConfiguration();
		try {
			$this->_initThreshholds();
		} catch (Exception $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}

	/**
	 * @desc initialize thresholds from setting variables, throws an Exception if a setting variable is missing
	 * @throws Exception
	 */
	private function _initThreshholds() {
		$this->_threshold=array();
		IpsDebugger::debug($this->_actionConfiguration);
		foreach ($this->_actionConfiguration->getActionConfig()  as $actionName=>$actionConfig){
			
			$configThreshold=$actionConfig["thresholds"];
		
			foreach ($this->_tags as $tagName){
				$this->_threshold[$tagName][$actionName]=$configThreshold[$tagName];
			}
		}
		IpsDebugger::debug($this->_threshold);
	}

	/**
	 * @param String $vectorname
	 * @return array
	 * @desc get the single array for a vectorname (tag), if vectorname isn't found, throws an Exception
	 * @throws Exception
	 */
	public function getThresholdsByName($vectorname) {
		if (array_key_exists($vectorname, $this->_threshold)) {
			return $this->_threshold[$vectorname];
		} else {
			IpsDebugger::debug($this);
			throw new Exception("no such threshold found");
		}
	}

	/**
	 * @param String $vectorname
	 * @param Integer $vectorvalue
	 * @return array
	 * @desc creates following data-structure:
	 * array("vectorname"=>, "vectorvalue"=>, "lastactio"=>)
	 *
	 * lastaction is the the last action which was exceeded by the impact.
	 * e.g. if tag=xss and impactvalue is 20 then lastaction="warn"
	 */
	public function getMaxThresholdHit($vectorname, $vectorvalue) {
		$thresholds = $this->getThresholdsByName($vectorname);
		$lastMatch = array();
		$max = 0;
		$lastAction = null;
		foreach ($thresholds as $key => $value) {
			if ($value < $vectorvalue && $vectorvalue > 0) {
				$max = $value;
				$lastAction = $key;
			}
		}
		//disabled debug fb(array("vector: ".$vectorname." vectorvalue: ".$vectorvalue." LAST ACTION=>".$lastAction));
		return array("vectorname" => $vectorname, "vectorvalue" => $vectorvalue, "lastaction" => $lastAction);

	}

	/**
	 * evalueates the the give intrusion, which mostly have diff tags and
	 * each tag has diff thresholds. gives the highest action which they
	 * cause
	 * @param array $vectorList with the particular tags of a intrusion
	 * @param int $vectorvalue impact value
	 * @return string highest action
	 * @deprecated is not used!
	 */
	public function evaluateIntrusion ($vectorList, $vectorvalue) {
		throw new Exception("Deprecated method, isnt used in the system. Is hardcoded anyway");
		die();
		IpsDebugger::debug(array("evaluateIntrusion"=>$vectorList));
		$highestAction = "";
		foreach ($vectorList as $vectorname) {
			$maxHit = $this->getMaxThresholdHit ($vectorname, $vectorvalue);
			$action = $maxHit["lastaction"];

			switch ($highestAction) {
				case "":
					$highestAction = $action;
					break;
				case "logAction":
					if ($action == "warnAction" || $action == "kickAction")
					$highestAction = $action;
					break;
				case "warnAction":
					if ($action == "kickAction")
					$highestAction = $action;
					break;
			}
		}
		return $highestAction;
	}

}