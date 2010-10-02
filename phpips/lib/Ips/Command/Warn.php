<?php




class Ips_Command_Warn extends Ips_Command_Abstract {

	private static $_instance=null;

	public static function getInstance(){
		if (self::$_instance==null)
			self::$_instance=new Ips_Command_Warn();
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
