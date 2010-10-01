<?php
$phpids_settings["debug_activated"]=true;
$phpids_settings['simulation_activated']=false;
$phpids_settings["actionConfig"]=array(
										"banAction"=>array(	"thresholds"=>array(
																				"sqli" =>  50,
																				"xss" => 50,
																				"rce"=>50,
																				"dos" => 50,
																				"csrf" => 50,
																				"id" =>50,
																				"lfi" =>50,
																				"rfe" => 50,
																				"dt" => 50
																				),

															"commandList"=>array("log","mail","ban"),
															"priority"=>6,
															),
										"kickAction"=>array("thresholds"=>array( // to be done for any tag/any action
																				"sqli" =>  40,
																				"xss" => 40,
																				"rce"=>40,
																				"dos" => 40,
																				"csrf" => 40,
																				"id" =>40,
																				"lfi" =>40,
																				"rfe" => 40,
																				"dt" => 40
																				),
															"commandList"=>array("log","mail","kick"),
															"priority"=>5
															),
										"warnAction"=>array("thresholds"=>array( // to be done for any tag/any action
																				"sqli" =>  20,
																				"xss" => 20,
																				"rce"=>20,
																				"dos" => 20,
																				"csrf" => 20,
																				"id" =>20,
																				"lfi" =>20,
																				"rfe" => 20,
																				"dt" => 20
																				),
															"commandList"=>array("log","mail","warn"),
															"priority"=>4
															),
										"logAction"=>array(	"thresholds"=>array( // to be done for any tag/any action
																				"sqli" =>  10,
																				"xss" => 10,
																				"rce"=>10,
																				"dos" => 10,
																				"csrf" => 10,
																				"id" =>10,
																				"lfi" =>10,
																				"rfe" => 10,
																				"dt" => 10
																				),
															"commandList"=>array("log"),
															"priority"=>1
															)
									);


define("PATH_TO_ROOT", "/var/www/eclipse-workspaces/eclipse_helios/php-ips/" );
set_include_path  (PATH_TO_ROOT."phpids-0.6.4/lib/");

$request = array("GET" => $_GET, "POST" => $_POST, "COOKIE" => $_COOKIE);
if (file_exists(PATH_TO_ROOT."phpids-0.6.4/lib/IDS/Init.php")){
	require_once(PATH_TO_ROOT."phpids-0.6.4/lib/IDS/Init.php");
}
else {
	throw new Exception("PHPIDS not found");
}

?>
<?php
/*
 * initiate the IDS
 *
 */
$init=IDS_Init::init(PATH_TO_ROOT."phpids-0.6.4/lib/IDS/Config/Config.ini.php");
$ids = new IDS_Monitor($request, $init);
/**
 * @var IDS_Report
 */
$result = $ids->run();
if (!$result->isEmpty()) {
	// Take a look at the result object with the ips system
	//echo $result;
	//require_once 'phpips/ips_init.inc.php';
	require_once (PATH_TO_ROOT . "phpips/lib/classes/class.IpsSystem.inc.php");
	$ips=IpsSystem::getInstance($result,$phpids_settings);
	$ips->run();
}



?>


<html>
<form action="example.php" method="post"><textarea name="data" rows="10"
	cols="50"><?php if(isset($_POST["data"]))echo $_POST["data"]?></textarea>
<br />
<input type="submit" /></form>
</html>
