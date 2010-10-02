<?php


class Ips_Command_Mail extends Ips_Command_Abstract {
	
	private static $_instance=null;


	public static function getInstance(){
		if (self::$_instance==null)
		self::$_instance=new Ips_Command_Mail();
		return self::$_instance;
	}

	private function getLastAction() {
		$action = "kick";

		foreach($this->_data as $key => $value) {
			if ($value["lastaction"] == "ban") {
				$action = "ban";
				break;
			}
		}

		return $action;
	}

	protected function realExecute() {
		
			Ips_Debugger::debug(array("executed Mail Command"=>$this->_data));
			global $db, $settings, $phpids_settings;

			//check lastaction
			$action = $this->getLastAction();

			// GET the IMPACT DATA FROM SESSION...
			$sessiondata = $_SESSION["IDSDATA"];

//			$to = ""; //An alle Admins
//			$sql = "SELECT Nachname, Vorname, ID FROM user WHERE usergroup=1";
//			//$admins = $db->get_results($sql);
//			//			foreach ($admins as $admin) {
//			//				$to .= $admin->Nachname . "," . $admin->Vorname . "," . $admin->ID . ";";
//			//			}
//
//			//$user, $time_logout, $time_login, $link_user_stat sind Platzhalter
//			//$user = Der böse Benutzer
//			//$time_logout = Zeitpunkt des Beginns der Sperre
//			//$time_login = Zeitpunkt des Endes der Sperre
//			//$time_lock = Zeitpunkt der Sperrung des Benutzers
//			//$link_user_stat = Link zur Statistik des bösen Benutzers
//			$logoutTime = time();
//			$expireTime = $logoutTime + $phpids_settings["kick_seconds"];
//			$link = $settings["estudy_base_url"] . "phpids/phpids_statistics.php?selUser=" . $_SESSION["userid"] . " \n";
//			// Check if user will be kicked or banned
//			if ($action == "ban") {
//				// User will be banned
//				$subject = $phpids_settings["ban_mail_subject"];
//				$message = $phpids_settings["ban_mail_message"];
//				$message = str_replace('$time_lock', Output::echoDate("d.m.Y H:i", $logoutTime), $message);
//			} else {
//				// User will be kicked
//				$subject = $phpids_settings["kick_mail_subject"];
//				$message = $phpids_settings["kick_mail_message"];
//				$message = str_replace('$time_logout', Output::echoDate("d.m.Y H:i", $logoutTime), $message);
//				$message = str_replace('$time_login', Output::echoDate("d.m.Y H:i", $expireTime), $message);
//			}
//			$message = str_replace('$user', $_SESSION["Vorname"] . " " . $_SESSION["Nachname"], $message);
//			$message = str_replace('$link_user_stat', $link, $message);
//			
//			foreach($this->_data as $singleMessage){
//				$message.=print_r($singleMessage,true);
//			}
//
//			$rootSql = "SELECT ID FROM user WHERE Login='root'";
//			$root = $db->get_row($rootSql);
//			$messaging = new Messaging($root->ID);
//			$messaging->sendMessage($to, $subject, $message, true, false);
	}

	protected function realSimulate($fileHandle) {
		$action = $this->getLastAction();

		$logText = Output::echoDate("Y-m-d H:i:s", time()).": ";
		$logText.= "Mailing to admins, ".$action." of ";

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