<?php


require_once (PATH_TO_ROOT . "phpips/lib/classes/class.IpsSystem.inc.php");

$IpsSystem = new IpsSystem($result);
$sessiondata = $IpsSystem->getSessionData();

//$firephp = FirePHP::getInstance(true);
IpsDebugger::debug($sessiondata);
IpsDebugger::debug($IpsSystem->getImpact());

if ($IpsSystem->getImpact() > 1) {
	//add each impact to sessiondata
	foreach ($IpsSystem->getTags() as $value) {
		//disabled debug fb($value."".$IpsSystem->getImpact());
		if(!isset($sessiondata[$value]))
			$sessiondata[$value] = 0;

		$sessiondata[$value] += $IpsSystem->getImpact();
		IpsDebugger::debug($sessiondata[$value]);
		
		//disabled debug fb($sessiondata[$value]);
	}
	//disabled debug fb($sessiondata);
	$IpsSystem->saveSessionData($sessiondata);
	IpsDebugger::debug($sessiondata);
}

if ($IpsSystem->checkSessionImpact()) {
	IpsDebugger::debug("Checking session Impact");
	// one or more impacts in session reached critical value

	// Enable commands to each last action
	$vector = $IpsSystem->getSessionImpactError();
	//$IpsSystem->saveSessionData($sessiondata);

	while (is_array($vector)) {
		$IpsSystem->actionDispatcher($vector);
		$vector = $IpsSystem->getSessionImpactError();
	}

	// Execute enabled commands of action with highest priority
	$IpsSystem->finalExecuteDispatcher();
}


