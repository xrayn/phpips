<?php

require_once (PATH_TO_ROOT . "phpips/lib/classes/iface.IpsCommand.inc.php");
require_once (PATH_TO_ROOT . "phpips/lib/classes/class.IpsCommandAbstract.inc.php");

//require_once (PATH_TO_ROOT . "common/init.inc.php"); // fuer db handle


class IpsWarnCommand extends IpsCommandAbstract {

	private static $_instance=null;

	public static function getInstance(){
		if (self::$_instance==null)
			self::$_instance=new IpsWarnCommand();
		return self::$_instance;
	}

	protected function realExecute() {
//		global $settings;
//
//		$url = $settings["estudy_base_url"];
//		header("Location: $url"."news/news.php?IDSWarning");
//		exit(0);
	}

	protected function realSimulate($fileHandle) {
		$logText = Output::echoDate("Y-m-d H:i:s", time()).": ";
		$logText.= "Warning ";

		if(isset($_SESSION["Vorname"])) {
			$logText.= "attacker: ".$_SESSION["Vorname"]." ".$_SESSION["Nachname"];
		} else {
			$logText.= "attacker: ".$_SERVER['REMOTE_ADDR'];
		}

		$logText.= "\n";

		fwrite($fileHandle, $logText);

		return true;
	}
}
