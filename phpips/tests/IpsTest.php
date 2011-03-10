<?php
class IpsTest extends PHPUnit_Framework_TestCase
{

	private function fireIPS($vector, $simulation) {
		define("PATH_TO_ROOT", "/var/www/eclipse-workspaces/eclipse_helios/php-ips/" );
		/*
		 * here: relative from PATH_TO_ROOT
		 */
		define("PATH_TO_PHPIDS", PATH_TO_ROOT."phpids-0.6.5/lib/");
		define("PATH_TO_PHPIPS", PATH_TO_ROOT."phpips/");
		set_include_path  (get_include_path().":".PATH_TO_PHPIDS);
		$_POST["attack"]="<?1OR1=1";
		$_POST["simulation"]="on";
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

	public function testOne() {
	}



}
?>