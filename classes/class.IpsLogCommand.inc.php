<?php
/*--------------------------------------------------------------------------+
 This file is part of eStudy.
 phpids/classes/class.IpsLogCommand.inc.php
 - Modulgruppe:  PHPIDS
 - Beschreibung: Main IPS Class (Intrusion Prevention System)
 - Version:      0.01, 17-11-2010
 - Autor(en):    Andre Rein <andre.rein@mni.fh-giessen.de>
 Philipp Promeuschel <philipp.promeuschel@mni.fh-giessen.de>
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
require_once(PATH_TO_ROOT."phpids/classes/iface.IpsCommand.inc.php");
require_once (PATH_TO_ROOT."phpids/classes/class.IpsCommandAbstract.inc.php");

class IpsLogCommand extends IpsCommandAbstract {
	private static $_instance=null;

	public static function getInstance() {
		if (self::$_instance==null)
		self::$_instance=new IpsLogCommand();
		return self::$_instance;
	}

	protected function realExecute() {
		global $phpids_settings;

		IpsDebugger::debug(array("CALLED REALEXECUTE"=>$this));
		// we don't need to log to the db twice so we just log to the file
		IpsDebugger::debug(array("executed Log Command"=>$this->_data));

		$logfile = $phpids_settings["command_logfile"];

		if(!empty($logfile)) {
			$logfile = realpath(PATH_TO_ROOT) . "/" . $logfile;
			$fh = fopen($logfile, "a+");
		}

		fwrite($fh, "----------\n");
		fwrite($fh, "Action: Log Triggered\n");
		fwrite($fh, "Attacker IP: ".$_SERVER['REMOTE_ADDR']."\n");

		foreach ($this->_data as $data){
			fwrite($fh,print_r($data,true));
		}
	}

	protected function realSimulate($fileHandle) {
		global $phpids_settings;

		$logText = "-------\n";
		$logText.= Output::echoDate("Y-m-d H:i:s", time()).": ";
		$logText.= "Logging to '".$phpids_settings["command_logfile"]."', ";

		if(isset($_SESSION["Vorname"])) {
			$logText.= "attacker: ".$_SESSION["Vorname"]." ".$_SESSION["Nachname"];
		} else {
			$logText.= "attacker: ".$_SERVER['REMOTE_ADDR'];
		}

		$logText.= "\n";

		fwrite($fileHandle, $logText);

		return false;
	}
}

