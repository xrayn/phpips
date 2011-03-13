<?php
class Module_Wordpress_Command_Log extends Ips_Command_Abstract {
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
		$this->dbInsert();
	}

	protected function realSimulate($fileHandle) {
		$this->dbInsert();
		$logText = "\n-------\n";
		$logText.= "SIMULATING LOGGING COMMAND\n";
		$logText.= "Logging to Database\n";
		$logText.= "-------\n";
		$this->_registry->add("SimulationOutputBuffer", $this->_registry->get("SimulationOutputBuffer").$logText);
		fwrite($fileHandle, $logText);

	}
	private function dbInsert(){

		$host=$this->_registry->getCommandConfigFrom("mysql","Host");
		$db=$this->_registry->getCommandConfigFrom("mysql","Database");
		$table="LOGTABLE";
		$user=$this->_registry->getCommandConfigFrom("mysql","Username");
		$pass=$this->_registry->getCommandConfigFrom("mysql","Password");
		$pdo = new PDO("mysql:host=$host;dbname=$db", $user,"$pass", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$idsReport=$this->_registry->getidsReport();
		$session_impact=$this->_registry->getHighestSessionImpact();
		$impact=$idsReport->getImpact();
	
		$attacker_ip=$_SERVER['REMOTE_ADDR'];
		$affected_tags=$idsReport->getTags();
		$affected_tags_text="";
		foreach ($affected_tags as $tag){
			$affected_tags_text.="[".$tag."]";
		}
		/*
		 * CAUTION CAUTION CAUTION CAUTION CAUTION CAUTION CAUTION CAUTION CAUTION
		 * 
		 * CAUTION CAUTION CAUTION CAUTION CAUTION CAUTION CAUTION CAUTION CAUTION
		 */
		
		
		/* in a productionsystem this should not be the real vector, ENCODE IT!!!!!!!.
		 * if vector is prepared well, and attacker know you load the vector from within phpmyadmin e.g. just viewing can
		 * present you a nice pesistent XSS.
		 * 
		 * Same goes for any backends, secure the displaying of the vector. Make sure the attack cannot be injected in your backend!
		 * 
		 */
		  
		/*
		 * THIS IS JUST FOR TESTING. DON NOT USE IN PRODUCTIVE SYSTEM!
		 */
		$values=array($impact, $affected_tags_text, $session_impact, $attacker_ip, $this->_getEventsToJson());
		
		/*
		 * USE THIS IN PRODUCTIVE SYSTEM INSTEAD!
		 */
		//$values=array($impact, $affected_tags_text, $session_impact, $attacker_ip, base64_encode($this->_getEventsToJson()));
		
		
		$insert=$pdo->prepare("INSERT INTO ".$table." ( impact,
														affected_tags,
														session_impact,
														attacker_ip,
														jsonevent
														) 
														values (?,?,?,?,?);")->execute($values);
	}
	
	
	private function _getEventsToArray(){
		$events=array();
		$idsReport=$this->_registry->getidsReport();
		foreach ($idsReport->getIterator() as $event){
			array_push($events, array("name"=>$event->getName(),"value"=>$event->getValue()));
		}
		return $events;
	}
	private function _getEventsToJson(){
		return json_encode($this->_getEventsToArray());
	}
}