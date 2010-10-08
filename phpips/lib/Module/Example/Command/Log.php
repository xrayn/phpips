<?php
class Module_Example_Command_Log extends Ips_Command_Abstract {
	private static $_instance=null;

	private $_dbPath=null;
	private $_dbTableName=null;
	public static function getInstance() {
		if (self::$_instance==null)
		self::$_instance=new self();
		return self::$_instance;
	}
	
	
	
	protected function realExecute() {
		$this->_dbPath=$this->_registry->getBasePath()."/examples/db/logger.db";
		$this->_dbTableName="loggertable";
		
	}

	protected function realSimulate($fileHandle) {
		$this->_dbPath=$this->_dbPath=$this->_registry->getBasePath()."phpips/examples/db/logger.db";
		$this->_dbTableName="loggertable";
		Ips_Debugger::debug($this->_dbPath);
		$db=new PDO('sqlite:'.$this->_dbPath);
		
		$idsReport=$this->_registry->getidsReport();
		$impact=$idsReport->getImpact();
		$session_impact=$this->_registry->getHighestSessionImpact();
		
		$affected_tags=$idsReport->getTags();
		$affected_tags_text="";
		foreach ($affected_tags as $tag){
			$affected_tags_text.="[".$tag."]";
		}
		Ips_Debugger::debug(array("impact"=>$impact,"session_impact"=>$session_impact,"affected_tags"=>$affected_tags_text));
		Ips_Debugger::debug($this->_registry);
		//Ips_Debugger::debug($idsReport->getImpact());
		$insert = "INSERT INTO ".$this->_dbTableName." (impact,affected_tags,session_impact) 
			VALUES ($impact,'$affected_tags_text',$session_impact);";
		//$stmt=$pdo->prepare("INSERT INTO ".$this->_dbTableName." values(impact=".$idsReport->getImpact());
		if ($insert==false){
			Ips_Debugger::debug($db->errorInfo());
		}
		else {
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			
			//Ips_Debugger::debug($insert);
			$res=$db->query($insert);
			Ips_Debugger::debug($res->execute());			
		
			//Ips_Debugger::debug($insert);
		}
		$logText = "\n-------\n";
		$logText.= "SIMULATING LOGGING COMMAND\n";
		$logText.= "Logging to file /tmp/mylog\n";
		$logText.= "-------\n";
		$this->_registry->add("SimulationOutputBuffer", $this->_registry->get("SimulationOutputBuffer").$logText);
		fwrite($fileHandle, $logText);

	}

}