<?php
class Module_Example_Command_Log extends Ips_Command_Abstract {
	/*
	 * This is the sample command descripted implementing in my blog.
	 * http://ra23.net/wop/category/phpips/
	 * 
	 */
	
	private static $_instance=null;

	private $_dbPath=null;
	private $_dbTableName=null;
	public static function getInstance() {
		if (self::$_instance==null)
		self::$_instance=new self();
		return self::$_instance;
	}



	protected function realExecute() {
		#$this->dbInsert();
	}

	protected function realSimulate($fileHandle) {
		
		$logText = "\n-------\n";
		$logText.= "SIMULATING LOGGING COMMAND\n";
		$logText.= "Logging to Database\n";
		$logText.= "-------\n";
		$this->_registry->add("SimulationOutputBuffer", $this->_registry->get("SimulationOutputBuffer").$logText);
		fwrite($fileHandle, $logText);

	}
	private function dbInsert(){
		$this->_dbPath=$this->_dbPath=$this->_registry->getBasePath()."phpips/examples/db/logger.db";
		$this->_dbTableName="loggertable";
		$db=new PDO('sqlite:'.$this->_dbPath);

		$idsReport=$this->_registry->getidsReport();
		$session_impact=$this->_registry->getHighestSessionImpact();
		$impact=$idsReport->getImpact();
		$attacker_ip=$_SERVER['REMOTE_ADDR'];
		$affected_tags=$idsReport->getTags();
		$affected_tags_text="";
		foreach ($affected_tags as $tag){
			$affected_tags_text.="[".$tag."]";
		}
		$values=array($impact,$affected_tags_text,$session_impact,$attacker_ip);

		$insert=$db->prepare("INSERT INTO ".$this->_dbTableName." ( impact,
																	affected_tags,
																	session_impact,
																	attacker_ip
																	) 
																	values (?,?,?,?);"
																	)->execute($values);
	}
}