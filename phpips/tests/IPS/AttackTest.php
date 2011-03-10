<?php

class IPS_AttackTest extends PHPUnit_Framework_TestCase
{

	private function fireIPS($vector, $simulation) {

		/*
		 * here: relative from PATH_TO_ROOT
		 */


		$_POST["attack"]=$vector;
		$_POST["simulation"]=$simulation;
		$_SERVER['REMOTE_ADDR']="127.0.0.1";
		$request = array("GET" => $_GET, "POST" => $_POST, "COOKIE" => $_COOKIE);
		if (file_exists(PATH_TO_PHPIDS."/IDS/Init.php")){
			require_once(PATH_TO_PHPIDS."/IDS/Init.php");
		}
		else {
			throw new Exception("PHPIDS not found");
		}
		$init=IDS_Init::init(PATH_TO_PHPIDS."IDS/Config/Config.ini.php");
		$ids = new IDS_Monitor($request, $init);
		$result = $ids->run();

		if (!$result->isEmpty()) {
			//if something is found

			// include the IPS Init Class
			require_once (PATH_TO_PHPIPS . "lib/Ips/Init.php");

			//initialise the system
			$IpsInit=Ips_Init::init("phpips/etc/System.ini");
			$registry=Ips_Registry::getInstance();
			if ($_POST["simulation"]!="on"){
				$registry->disableSimulation();

			} else {
				$registry->enableSimulation();
			}
			//var_dump(Ips_Registry::getInstance());
			//die();
			//run the IPS System
			$ips=Ips_System::getInstance($result);
			$ips->run();
				
		}
	}



	public function testAttack2Real() {
		session_start();

		//session_start();
		for ($i=4;$i<40; $i+=4) {
			$this->fireIPS("'>XXX", "on");
			$this->assertEquals($_SESSION["IPSDATA"]["xss"], $i);
		}
		session_destroy();
		var_dump($_SESSION);
		var_dump(session_id());

	}
	public function testAttack1Simulation() {

		session_start();

		var_dump(session_id());
		session_unset();
		var_dump($_SESSION);
		//		$this->fireIPS("'><a onclick='alert()'", "off");
		//$this->assertEquals($_SESSION["IPSDATA"]["xss"], 20);
		//		$this->fireIPS("'>XXX", "off");
		//		$this->fireIPS("'>XXX OR 1=1; SELECT DISTINCT ../../../../../", "off");
		//		$this->assertEquals($_SESSION["IPSDATA"]["xss"], 56);
		//		session_destroy();

	}


}
?>