<?php


class IpsThresholds {

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
	protected $_threshold = array(
				"sqli" => array(
						"logAction" => 5, "warnAction" => 10, "kickAction" => 20, "banAction" => 50
						),
				"xss" => array(
						"logAction" => 5, "warnAction" => 20, "kickAction" => 30, "banAction" => 50
						),
				"rce" => array(
						"logAction" => 10, "warnAction" => 20, "kickAction" => 30, "banAction" => 50
						),
				"dos" => array(
						"logAction" => 10, "warnAction" => 20, "kickAction" => 30, "banAction" => 50
						),
				"csrf" => array(
						"logAction" => 10, "warnAction" => 20, "kickAction" => 30, "banAction" => 50
						),
				"id" => array(
						"logAction" => 10, "warnAction" => 20, "kickAction" => 30, "banAction" => 50
						),
				"lfi" => array(
						"logAction" => 10, "warnAction" => 20, "kickAction" => 30, "banAction" => 50
						),
				"rfe" => array(
						"logAction" => 10, "warnAction" => 20, "kickAction" => 30, "banAction" => 50
						),
				"dt" => array(
						"logAction" => 10, "warnAction" => 20, "kickAction" => 30, "banAction" => 50
						)
			);

	public function __construct() {
		//init thresholds from db
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
//		global $phpids_settings;
//		
//		/* countermeasure types */
//		$cm_types = array("log", "warn", "kick", "ban");
//		
//		/* initialize thresholds by setting values */
//		foreach ($this->_threshold as $type => $thresholds) {
//			if (empty($phpids_settings["ips_threshold_" . $type])) {
//				throw new Exception("Thresholds for $type not found in settings!");
//			} else {
//				$settings_thresholds = explode(",", $phpids_settings["ips_threshold_" . $type]);
//				foreach ($cm_types as $i => $cm_type) {
//					$this->_threshold[$type][$cm_type] = intval(trim($settings_thresholds[$i]));
//				}
//			}
//		}
//		
	/* echo "<pre>";print_r($this->_threshold);echo "<pre>"; */
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
	 */
	public function evaluateIntrusion ($vectorList, $vectorvalue) {
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
				case "warn":
					if ($action == "kickAction")
						$highestAction = $action;
					break;
			}
		}
		return $highestAction;
	}

}