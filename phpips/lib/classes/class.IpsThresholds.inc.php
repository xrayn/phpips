<?php
/*--------------------------------------------------------------------------+
 This file is part of eStudy.
 phpids/classes/class.IpsThresholds.inc.php
 - Modulgruppe:  PHPIDS
 - Beschreibung: Thresholds Class IPS (Intrusion Prevention System)
 - Version:      0.01, 07-11-2010
 - Autor(en):    Andre Rein <andre.rein@mni.fh-giessen.de>
 +---------------------------------------------------------------------------+
 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 as published by the Free Software Foundation; either version 2
 of the License, or any later version.
 +---------------------------------------------------------------------------+
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 +--------------------------------------------------------------------------*/

/* make sure phpids_settings are loaded and parsed from database */
//require_once (PATH_TO_ROOT . "phpids/phpids_settings.inc.php");

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
	protected $_threshold = array(
				"sqli" => array(
						"log" => 5, "warn" => 10, "kick" => 20, "ban" => 50
						),
				"xss" => array(
						"log" => 5, "warn" => 20, "kick" => 30, "ban" => 50
						),
				"rce" => array(
						"log" => 10, "warn" => 20, "kick" => 30, "ban" => 50
						),
				"dos" => array(
						"log" => 10, "warn" => 20, "kick" => 30, "ban" => 50
						),
				"csrf" => array(
						"log" => 10, "warn" => 20, "kick" => 30, "ban" => 50
						),
				"id" => array(
						"log" => 10, "warn" => 20, "kick" => 30, "ban" => 50
						),
				"lfi" => array(
						"log" => 10, "warn" => 20, "kick" => 30, "ban" => 50
						),
				"rfe" => array(
						"log" => 10, "warn" => 20, "kick" => 30, "ban" => 50
						),
				"dt" => array(
						"log" => 10, "warn" => 20, "kick" => 30, "ban" => 50
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
				case "log":
					if ($action == "warn" || $action == "kick")
						$highestAction = $action;
					break;
				case "warn":
					if ($action == "kick")
						$highestAction = $action;
					break;
			}
		}
		return $highestAction;
	}

}