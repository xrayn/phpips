<?php
// define the path to your
define("PATH_TO_ROOT", "/var/www/webservers/www.ra23.net/documents/phpips/trunk/" );
//define("PATH_TO_ROOT", "/your/path/to/webserver/doc/root/phpips" );
// use phpids shipped with this package
set_include_path  (get_include_path().":".PATH_TO_ROOT."phpids-0.6.4/lib/");

//define the request array
$request = array("GET" => $_GET, "POST" => $_POST, "COOKIE" => $_COOKIE);
//include the init Class from phpips
if($_GET["reset_session"]=="doit"){
	session_start();
	session_destroy();
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
<p>
This is a demo page demonstrating basic usage of the phpips system.<br/>
You can do different thing here.<br/>
First of all, you are able to reset your current session, this is only for testing, cause in a real world this makes no sense :)<br/>
<br/>
You can use the textarea to insert malicious code and submit it to the system.<br/>
<br/>
If you are in simulation mode, the page tells you what the underlying commands do.<br/>
If you switch the simulation mode to off, the page reacts based on your input you inserted.<br/>
<br/>
When no malicious input is found, the ips system isn't loaded.
You can try to insert code that breaks the html of the site, e.g. <?php  echo htmlspecialchars("\"</textarea>");?><br/>
<br/>
Well I don't care. In a real world example you would not run phpips in this way. It's just to show you, what is currently possible.<br/>
So have fun, playing around!
<br/>
</p>
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
?>
<textarea rows="20" cols="50" readonly="readonly">
<?php
if (isset($registry)){
if ($result->getImpact()){
	echo "Found an impact of: ".$result->getImpact()."\n";
	echo "Your current Session impact is: \n";
	foreach ($_SESSION["IPSDATA"] as $tag=>$impact){
		echo $tag."=>".$impact."\n";
	}
}
	echo "\nSimulation Output:\n";
	echo $registry->get("SimulationOutputBuffer");
}
?>
</textarea>
<?php
?>
</html>
