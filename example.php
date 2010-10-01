<?php
$phpids_settings["debug_activated"]=true;
$phpids_settings['simulation_activated']=false;
define("PATH_TO_ROOT", "/var/www/eclipse-workspaces/eclipse_helios/php-ips/" );
set_include_path  ("/var/www/eclipse-workspaces/eclipse_helios/php-ips/phpids-0.6.4/lib/");

$request = array("GET" => $_GET, "POST" => $_POST, "COOKIE" => $_COOKIE);
if (file_exists("/var/www/eclipse-workspaces/eclipse_helios/php-ips/phpids-0.6.4/lib/IDS/Init.php")){
	require_once '/var/www/eclipse-workspaces/eclipse_helios/php-ips/phpids-0.6.4/lib/IDS/Init.php';
}
else {
	echo "PHPIDS not found";
}

?>
<?php
/*
 * initiate the IDS
 *
 */
$init=IDS_Init::init("/var/www/eclipse-workspaces/eclipse_helios/php-ips/phpids-0.6.4/lib/IDS/Config/Config.ini.php");
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
	$ips=new IpsSystem($result);
	$ips->run();
}	



?>


<html>
<form action="example.php" method="post"><textarea name="data" rows="10"
	cols="50"><?php if(isset($_POST["data"]))echo $_POST["data"]?></textarea> <br />
<input type="submit" /></form>
</html>
