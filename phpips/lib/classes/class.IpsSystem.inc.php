<?php
/*--------------------------------------------------------------------------+
 This file is part of eStudy.
 phpids/classes/class.IpsSystem.inc.php
 - Modulgruppe:  PHPIDS
 - Beschreibung: Main IPS Class (Intrusion Prevention System)
 - Version:      0.01, 07-11-2010
 - Autor(en):    Andre Rein <andre.rein@mni.fh-giessen.de>
 Philipp Promeuschel <philipp.promeuschel@mni.fh-giessen.de>
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

require_once (PATH_TO_ROOT . "phpips/lib/classes/class.IpsThresholds.inc.php");
require_once (PATH_TO_ROOT . "phpips/lib/classes/class.IpsCommandFactory.inc.php");
require_once (PATH_TO_ROOT . "phpips/lib/classes/class.IpsDebugger.inc.php");


class IpsSystem {

	protected $_idsResult;

	protected $_tags = null;

	protected $_sessiondata;

	protected $_impactError = array();

	protected $_threshold = null;

	protected $_actions = array();

	/**
	 * @var IpsCommand
	 */
	protected $_finalAction = null;

	/**
	 * @param String $name
	 * @param array of (IpsCommand $command)
	 * @desc adds actions to the local action buffer, which can be executed
	 */
	private function addAction($name, $command) {
		$this->_actions[$name] = $command;
	}

	public function __construct(IDS_Report $idsResult) {
		$this->setIdsResult($idsResult);
		$this->_init();
	}

	private function _init() {
		/*
		* Adding the actions and initialize singleton Commands.
		* Actions have to be added in priority order:
		* First added --> highest priority; last added --> lowest priority.
		* If tags reach thresholds for different actions, the one with highest priority is used.
		* Also important:
		* Commands 'warn', 'kick' and 'ban' exit the system after executed.
		* They each have to be the last array element of an action!
		*/
		$this->addAction("ban", array(IpsCommandFactory::createCommand("log"), IpsCommandFactory::createCommand("mail"), IpsCommandFactory::createCommand("ban")));
		$this->addAction("kick", array(IpsCommandFactory::createCommand("log"), IpsCommandFactory::createCommand("mail"), IpsCommandFactory::createCommand("kick")));
		//$this->addAction("mail", array(IpsCommandFactory::createCommand("log"), IpsCommandFactory::createCommand("mail")));
		$this->addAction("warn", array(IpsCommandFactory::createCommand("log"), IpsCommandFactory::createCommand("warn")));
		$this->addAction("log", array(IpsCommandFactory::createCommand("log")));

		// Load thresholds for tags
		$this->_threshold = new IpsThresholds();

		// we need this for logging/action information
		if (!isset($_SESSION["IDSDATA"])) {
			$_SESSION["IDSDATA"] = $this->getIdsResult();
		}

		if (!isset($_SESSION["IPSDATA"])) {
			$_SESSION["IPSDATA"] = array();
		}

		IpsDebugger::debug(array("Object initialized"=>$_SESSION["IPSDATA"]));
		$this->_sessiondata = $_SESSION["IPSDATA"];
	}

	/**
	 * @param array $vector
	 * @desc analyses the $impactVector and extracts the lastaction which matched the highest threshold.
	 * For this "lastaction" it enables the defined commands
	 */
	public function actionDispatcher($impactVector) {
		$lastaction = $impactVector["lastaction"];

		foreach ($this->_actions as $actionName => $commands) {
			if ($actionName==$lastaction) {
				foreach ($commands as $command){
					$command->addData($impactVector)->enableExecute();
				}

				break;
			}
		}
	}

	/**
	 * @desc Execute all enabled commands (enabled by actionDispatcher()).
	 * Commands of action with highest priority are executed first.
	 */
	public function finalExecuteDispatcher() {
		global $phpids_settings;

		foreach($this->_actions as $key => $commands){
			foreach($commands as $command){
				if($phpids_settings['simulation_activated']) {
					if($command->simulate()) {
						return;		//Exit IPS, but not rest of script
					}
				}
				else {
					$command->execute();
				}
			}
		}
	}

	/**
	 * @return Boolean
	 * @desc Checks the current session data for any impacts. If an impact is to high, #
	 * this attackclass is inserted in the error array $this->_impactError, which acts
	 * like a buffer for later action triggering
	 * returns true if any defined impact value is exceeded
	 *
	 */
	public function checkSessionImpact() {
		IpsDebugger::debug("checkSessionImpact");
		IpsDebugger::debug(array("THIS SESSION DATA"=>$this->_sessiondata));
		foreach ($this->_sessiondata as $key => $value) {			
			// should be switch case later when we have the matrix
			//$this->actionResolver($this->_threshold->getMaxThresholdHit($key,$value));
			$maxThresholdHit = $this->_threshold->getMaxThresholdHit($key, $value);
			if ($maxThresholdHit["lastaction"] != null) {
				array_push($this->_impactError, $maxThresholdHit);
			}
		}

		//disabled debug fb($this->_impactError);
		if (sizeof($this->_impactError) === 0) {
			//disabled debug fb("true");
			return false;
		} else {
			//disabled debug fb("false");
			return true;
		}
	}

	public function doSomething() {	//var_dump($this->_idsResult);
	}

	public function getIdsResult() {
		return $this->_idsResult;
	}

	/**
	 * @param IDS_Report $idsResult
	 * @desc Sets the current idsResult from the ids System
	 */
	public function setIdsResult(IDS_Report $idsResult) {
		$this->_idsResult = $idsResult;
	}

	/**
	 * @return Integer
	 * @desc Gets the total Impact value of the idsResult-Object
	 */

	public function getImpact() {
		return $this->_idsResult->getImpact();
	}

	/**
	 * @return array
	 * @desc Gets the affected Tags of the idsResult-Object
	 */
	public function getTags() {
		return $this->_idsResult->getTags();
	}

	/**
	 * @return array
	 * @desc Gets the current sessiondata
	 */

	public function getSessionData() {
		return $this->_sessiondata;
	}

	/**
	 * @param array $sessiondata
	 * @desc saves the current sessiondata
	 */
	public function saveSessionData($sessiondata) {
		$_SESSION["IPSDATA"] = $sessiondata;
		$this->_sessiondata = $sessiondata;
		IpsDebugger::debug("SAVE SESSION DATA!!!!");
		IpsDebugger::debug(array("SAVE SESSION DATA"=>$_SESSION["IPSDATA"]));
	}

	/**
	 * @return array|NULL
	 * @desc returns a single impactError, can be used in a loop till buffer $this->_impactError is empty
	 */
	public function getSessionImpactError() {
		//disabled debug fb("size of _impacterror".sizeof($this->_impactError));
		if (sizeof($this->_impactError) > 0) {
			return array_pop($this->_impactError);
		} else {
			return null;
		}
	}
}