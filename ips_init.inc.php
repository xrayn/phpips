<?php
/*--------------------------------------------------------------------------+
 This file is part of eStudy.
 phpids/ips_init.inc.php
 - Modulgruppe:  PHPIDS
 - Beschreibung: Initialize and run IPS (Intrusion Prevention System)
 - Version:      0.01, 05-13-2010
 - Autor(en):    Andre Rein <andre.rein@mni.fh-giessen.de>
 +---------------------------------------------------------------------------+
 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 as published by the Free Software Foundation; either version 2
 of the License, or any later version.
 +---------------------------------------------------------------------------+
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 +--------------------------------------------------------------------------*/

require_once (PATH_TO_ROOT . "phpids/classes/class.IpsSystem.inc.php");

$IpsSystem = new IpsSystem($idsResult);
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


