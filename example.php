<?php


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
	require_once (PATH_TO_ROOT . "phpips/lib/Ips/Init.php");
	$IpsInit=Ips_Init::init();
	$ips=Ips_System::getInstance($result);
	$ips->run();
}



?>


<html>
<form action="example.php" method="post"><textarea name="data" rows="10"
	cols="50"><?php if(isset($_POST["data"]))echo $_POST["data"]?></textarea>
<br />
<input type="submit" /></form>
</html>
