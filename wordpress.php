<?php
//read base path out of config

// define the path to your
//define("PATH_TO_ROOT", "/var/www/webservers/www.ra23.net/documents/phpips/trunk/" );
define("PATH_TO_ROOT", "/home/ar/eclipse-workspaces/eclipse_helios/php-ips/");
/*
 * here: relative from PATH_TO_ROOT
 */
define("PATH_TO_PHPIDS", PATH_TO_ROOT."phpids-0.6.5/lib/");
define("PATH_TO_PHPIPS", PATH_TO_ROOT."phpips/");

// use phpids shipped with this package
set_include_path  (get_include_path().":".PATH_TO_PHPIDS);

//define the request array
$request = array("GET" => $_GET, "POST" => $_POST, "COOKIE" => $_COOKIE);
//include the init Class from phpips
if($_GET["reset_session"]=="doit"){
	session_start();
	session_destroy();
}


if (file_exists(PATH_TO_PHPIDS."/IDS/Init.php")){
	require_once(PATH_TO_PHPIDS."/IDS/Init.php");
}
else {
	throw new Exception("PHPIDS not found");
}

// load PHPIDS
session_start();
$init=IDS_Init::init(PATH_TO_PHPIDS."IDS/Config/Config.ini.php");
$ids = new IDS_Monitor($request, $init);

//get the result object from PHPIDS
$result = $ids->run();


//check if something badly is found
	//if something is found

	// include the IPS Init Class
	require_once (PATH_TO_PHPIPS . "lib/Ips/Init.php");

	//initialise the system
	$IpsInit=Ips_Init::init("phpips/etc/System.ini");
	$registry=Ips_Registry::getInstance();
	if ($registry->isSimulationEnabled()){
		$registry->enableSimulation();
	} else {
		$registry->disableSimulation();
	}
	//var_dump(Ips_Registry::getInstance());
	//die();
	//run the IPS System
	$ips=Ips_System::getInstance($result);
	$ips->run();

?>
