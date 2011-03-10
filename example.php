<?php

//read base path out of config

// define the path to your
//define("PATH_TO_ROOT", "/var/www/webservers/www.ra23.net/documents/phpips/trunk/" );

define("PATH_TO_ROOT", "/var/www/eclipse-workspaces/eclipse_helios/php-ips/" );
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


?>




<html>
<body style="font-family: Verdana,Geneva,Arial,Helvetica,sans-serif; font-size:0.75em;">
<div>
<h1>IPS Demo Page</h1>
<div style="float: left;">
<div id="vector_form" style="background-color: #D9FFDD; width: 430px; padding: 10px;">
<?php
if ($_POST["simulation"]!="on"){
	echo "Real Mode<br/>";

}
else {
	echo "Simulation Mode<br/>";

}
if($_GET["reset_session"]=="doit"){
	echo "Session destroyed<br>";
}

?>

<form action="example.php" method="get"><input type="hidden"
	name="reset_session" value="doit" /> <input type="submit"
	value="Reset Session"></form>
<form action="example.php" method="post">
<p>
</p>
<label for="data">Insert a vector here:</label><br/>
<textarea name="data" rows="5" cols="50"><?php if(isset($_POST["data"]))echo $_POST["data"]?></textarea>
<br /><br/>
<input type="checkbox" id="simulation" name="simulation" <?php echo ($_POST["simulation"]=="on" || sizeof($_POST)==0)? "checked='checked'":""?>/>
<label for="simulation"> enable Simulation-Mode</label>
<br/>
<br/>
<input type="submit" /></form>
<?php
?>
</div>
<div style="background-color: #FFAA9A; width: 430px; padding: 10px; margin-top: 20px;">
<label for="simulation_output">Output from IPS</label></br>
<textarea name="simulation_output" rows="28" cols="50" readonly="readonly">
<?php
if ($result->getImpact()){
	echo "PHPIDS found an impact of: ".$result->getImpact()."\n";
}
else {
}
	echo "Your current session impact is: \n\n";
if (isset($_SESSION["IPSDATA"] )){
		foreach ($_SESSION["IPSDATA"] as $tag=>$impact){
			echo $tag."=>".$impact."\n";
		}
}
else {
	echo "0\n";

}	
echo "\nSimulation Output:\n";

if (isset($registry)){
	echo $registry->get("SimulationOutputBuffer");
}
?>
</textarea>
</div>
</div>
<?php
?>
</div>
<div style="float: left; margin-left: 15px; width: 600px;">
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
</div>
</div>
</body>
</html>
