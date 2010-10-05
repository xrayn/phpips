<?php
// define the path to your
define("PATH_TO_ROOT", "/var/www/eclipse-workspaces/eclipse_helios/php-ips/" );
//define("PATH_TO_ROOT", "/your/path/to/webserver/doc/root/phpips" );
// use phpids shipped with this package
set_include_path  (get_include_path().":".PATH_TO_ROOT."phpids-0.6.4/lib/");

//define the request array
$request = array("GET" => $_GET, "POST" => $_POST, "COOKIE" => $_COOKIE);
//include the init Class from phpips
if($_GET["reset_session"]=="doit"){
	session_start();
	session_destroy();
	echo "Session destroyed<br>";

}


if (file_exists(PATH_TO_ROOT."phpids-0.6.4/lib/IDS/Init.php")){
	require_once(PATH_TO_ROOT."phpids-0.6.4/lib/IDS/Init.php");
}
else {
	throw new Exception("PHPIDS not found");
}

// load PHPIDS

$init=IDS_Init::init(PATH_TO_ROOT."phpids-0.6.4/lib/IDS/Config/Config.ini.php");
$ids = new IDS_Monitor($request, $init);

//get the result object from PHPIDS
$result = $ids->run();


//check if something badly is found
if (!$result->isEmpty()) {
	//if something is found

	// include the IPS Init Class
	require_once (PATH_TO_ROOT . "phpips/lib/Ips/Init.php");

	//initialise the system
	$IpsInit=Ips_Init::init("phpips/etc/System.ini");
	$registry=Ips_Registry::getInstance();
	if ($_POST["simulation_mode"]!="off"){
		$registry->enableSimulation();

	} else {
		$registry->disableSimulation();
	}
	//var_dump(Ips_Registry::getInstance());
	//die();
	//run the IPS System
	$ips=Ips_System::getInstance($result);
	$ips->run();
}


?>




<html>

<?php
if ($_POST["simulation_mode"]!="off"){
	echo "Simulation Mode<br/>";

}
else {
	echo "REAL MODE<br/>";

}
if($_GET["reset_session"]=="doit"){
	echo "Session destroyed<br>";
}


?>

<form action="example.php" method="get"><input type="hidden"
	name="reset_session" value="doit" /> <input type="submit"
	value="Reset Session"></form>
<form action="example.php" method="post">
<p><input type="radio" name="simulation_mode" value="on"
<?php echo ($_POST["simulation_mode"]!="off")? "checked='checked'":""?> />
Simulation On<br>
<input type="radio" name="simulation_mode" value="off"
<?php echo ($_POST["simulation_mode"]=="off")? "checked='checked'":""?> />
Simulation Off<br>
</p>

<textarea name="data" rows="10" cols="50"><?php if(isset($_POST["data"]))echo $_POST["data"]?></textarea>
<br />
<input type="submit" /></form>
<?php
if ($_POST["simulation_mode"]!="off"):
?>
<textarea rows="20" cols="50" readonly="readonly">
<?php
if (isset($registry)){
	echo $registry->get("SimulationOutputBuffer");
}
?>
</textarea>
<?php
endif
?>
</html>
