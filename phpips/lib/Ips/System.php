<?php


class Ips_System {
	private static $_instance=null;
	/**
	 *
	 * @var IpsConfiguration
	 */
	protected  $_ActionConfiguration=null;

	/**
	 *
	 * @var Ips_Registry
	 */
	protected $_registry=null;

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

	public static function getInstance($idsResult){
		if (self::$_instance==null){
			self::$_instance=new Ips_System($idsResult);
		}
			

		return self::$_instance;
	}
	/**
	 * @param String $name
	 * @param array of (IpsCommand $command)
	 * @desc adds actions to the local action buffer, which can be executed
	 */
	private function addAction($name, $command) {
		$this->_actions[$name] = $command;
	}

	private function __construct(IDS_Report $idsResult) {
		//init the registry!
		$this->_registry=Ips_Registry::getInstance();
		$this->setIdsResult($idsResult);
		$this->_registry->setIdsReport($idsResult);
		$this->_ActionConfiguration=$this->_registry->getActionConfiguration();

		$this->_init();
	}

	/**
	 * @desc check if there is already a session, the ips system makes less sense without any session
	 * if no session is found, start one. Further if a session is found regenerate the id so each request gets a new session_id
	 */
	private function __checkSession(){
		if ($this->_registry->isExternalSessionManagerEnabled()){
			$sessionManager=$this->_registry->getExternalSessionManager();
			$className=$sessionManager["className"];
			$methodName=$sessionManager["methodName"];
			$auth=call_user_func(array($className, $methodName));
				
			//$auth=$methodName::$foo;
			$auth->getStorage();
		}
		if (session_id()==""){
			session_start();
		}
		/*
		 * @lookhere: in very high performance applications this should be diabled. Or configurable!
		 */
		session_regenerate_id(TRUE);

	}

	private function _init() {
		$this->__checkSession();

		/*
		 * Adding the actions and initialize singleton Commands.
		 * Actions have to be added in priority order:
		 * First added --> highest priority; last added --> lowest priority.
		 * If tags reach thresholds for different actions, the one with highest priority is used.
		 * Also important:
		 * Commands 'warn', 'kick' and 'ban' exit the system after execution.
		 * They each have to be the last array element of an action!
		 */
		if ($this->_ActionConfiguration==null){
			throw new Exception("Obsolete!!!");
			$this->addAction("ban", array(IpsCommandFactory::createCommand("log"), IpsCommandFactory::createCommand("mail"), IpsCommandFactory::createCommand("ban")));
			$this->addAction("kick", array(IpsCommandFactory::createCommand("log"), IpsCommandFactory::createCommand("mail"), IpsCommandFactory::createCommand("kick")));
			//$this->addAction("mail", array(IpsCommandFactory::createCommand("log"), IpsCommandFactory::createCommand("mail")));
			$this->addAction("warn", array(IpsCommandFactory::createCommand("log"), IpsCommandFactory::createCommand("warn")));
			$this->addAction("log", array(IpsCommandFactory::createCommand("log")));
		}
		else {
			foreach ($this->_ActionConfiguration->getActionConfig() as $actionName=>$actionConfig){

				$commandList=array();
				foreach ($actionConfig["commandList"] as $key=>$singleActionConfig){

					array_push($commandList,Ips_Command_Factory::createCommand($singleActionConfig));

				}
				$this->addAction($actionName, $commandList);
			}
		}

		// Load thresholds for tags
		$this->_threshold = new Ips_Threshold();

		// we need this for logging/action information
		if (!isset($_SESSION["IDSDATA"])) {
			$_SESSION["IDSDATA"] = $this->getIdsResult();
		}

		if (!isset($_SESSION["IPSDATA"])) {
			$_SESSION["IPSDATA"] = array();
		}

		Ips_Debugger::debug(array("Object initialized"=>$_SESSION["IPSDATA"]));
		$this->_sessiondata = $_SESSION["IPSDATA"];
	}

	/**
	 * @param array $vector
	 * @desc analyses the $impactVector and extracts the lastaction which matched the highest threshold.
	 * For this "lastaction" it enables the defined commands
	 */
	private function actionDispatcher($impactVector) {
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
	private function finalExecuteDispatcher() {

		if($this->_registry->isSimulationEnabled()){
			$info=$this->simulationModeInfoBuilder();
			$logfile=$this->_registry->getSimulationLogFile();
			$fh = fopen($logfile, "a+");
			fwrite($fh, $info["header"]);
				
		}

		foreach($this->_actions as $key => $commands){
			foreach($commands as $command){
				if($this->_registry->isSimulationEnabled()) {
					Ips_Debugger::debug("SIMULATION MODE");
					//log some info for simulation
					call_user_func(array($command,"simulate"));
					if(call_user_func(array($command,"simulate"))) {
						return;		//Exit IPS, but not rest of script
					}
				
				}
				else {
					$command->execute();
				}
			}
		}
	if($this->_registry->isSimulationEnabled()){
		fwrite($fh, $info["footer"]);
		}
		
	}

	private function simulationModeInfoBuilder(){
		$logheader="\n#################################\n";
		$logheader.="Date: ".date("Y-m-d H:i:s",time())."\n";
		$logheader.= "IMPACT FOUND -> STARTING SIMULATION\n";
		$logheader.= "Attacker: ".$_SERVER['REMOTE_ADDR']."\n";
		$logheader.="\n#################################\n";

		$logfooter="\n##################################\n";
		$logfooter.="           END\n";
		$logfooter.="##################################\n";
		return array("header"=>$logheader,"footer"=>$logfooter);
	}

	/**
	 * @return Boolean
	 * @desc Checks the current session data for any impacts. If an impact is to high, #
	 * this attackclass is inserted in the error array $this->_impactError, which acts
	 * like a buffer for later action triggering
	 * returns true if any defined impact value is exceeded
	 *
	 */
	private function checkSessionImpact() {
		Ips_Debugger::debug("checkSessionImpact");
		Ips_Debugger::debug(array("THIS SESSION DATA"=>$this->_sessiondata));
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

	private function doSomething() {	//var_dump($this->_idsResult);
	}

	private function getIdsResult() {
		return $this->_idsResult;
	}

	/**
	 * @param IDS_Report $idsResult
	 * @desc Sets the current idsResult from the ids System
	 */
	private function setIdsResult(IDS_Report $idsResult) {
		$this->_idsResult = $idsResult;
	}

	/**
	 * @return Integer
	 * @desc Gets the total Impact value of the idsResult-Object
	 */

	private function getImpact() {
		return $this->_idsResult->getImpact();
	}

	/**
	 * @return array
	 * @desc Gets the affected Tags of the idsResult-Object
	 */
	private function getTags() {
		return $this->_idsResult->getTags();
	}

	/**
	 * @return array
	 * @desc Gets the current sessiondata
	 */

	private function getSessionData() {
		return $this->_sessiondata;
	}

	/**
	 * @param array $sessiondata
	 * @desc saves the current sessiondata
	 */
	private function saveSessionData($sessiondata) {
		$_SESSION["IPSDATA"] = $sessiondata;
		$this->_sessiondata = $sessiondata;
		$this->_registry=$this->_registry->setSessionImpact($this->_sessiondata);
		Ips_Debugger::debug("SAVE SESSION DATA!!!!");
		Ips_Debugger::debug(array("SAVE SESSION DATA"=>$_SESSION["IPSDATA"]));
	}

	/**
	 * @return array|NULL
	 * @desc returns a single impactError, can be used in a loop till buffer $this->_impactError is empty
	 */
	private function getSessionImpactError() {
		//disabled debug fb("size of _impacterror".sizeof($this->_impactError));
		if (sizeof($this->_impactError) > 0) {
			return array_pop($this->_impactError);
		} else {
			return null;
		}
	}
	public function run(){

		if ($this->getImpact() > 1) {
			//add each impact to sessiondata
			foreach ($this->getTags() as $value) {
				if(!isset($this->_sessiondata[$value]))
				$this->_sessiondata[$value] = 0;
				$this->_sessiondata[$value] += $this->getImpact();
			}
		} 
		/*
		 * Always save current sessiondata back into session.
		 * When attack happens and user is banned e.g. , trying reloading with no
		 * vector we want all actions run again.
		 * So this triggers the same actions like before.
		 * 
		 * As soon as an attacker is recognized by the system, we record his session 
		 * until the session ends. (Currently there is no other way to do it)
		 * If an implementer decides no running if no attack is found, this is ok, so this part
		 * does not get executed, but in most systems without a login and so no real sessiondata, other than the
		 * attackdata, we have no mechanism to disallow the user reloading the page, hence we need to track
		 * them as long as the session is up.
		 *  
		 */	
		$this->saveSessionData($this->_sessiondata);
			
		if ($this->checkSessionImpact()) {
			Ips_Debugger::debug("Checking session Impact");
			// one or more impacts in session reached critical value

			// Enable commands to each last action
			$vector = $this->getSessionImpactError();
			//$IpsSystem->saveSessionData($sessiondata);

			while (is_array($vector)) {
				$this->actionDispatcher($vector);
				$vector = $this->getSessionImpactError();
			}

			// Execute enabled commands of action with highest priority
			$this->finalExecuteDispatcher();
		}
		//remove the classloader, we dont want to do anything after here! 
		//@todo: right place here?
		
		spl_autoload_unregister(array("IpsClassLoader","autoload"));
		
	}
}
