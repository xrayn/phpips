<?php

require_once (PATH_TO_ROOT . "phpips/lib/classes/iface.IpsCommand.inc.php");
require_once (PATH_TO_ROOT . "phpips/lib/classes/class.IpsCommandAbstract.inc.php");
//require_once (PATH_TO_ROOT."common/init.inc.php"); // fuer db handle

class IpsKickCommand extends IpsCommandAbstract {
	
	private static $_instance=null;

	public static function getInstance(){
		if (self::$_instance==null)
			self::$_instance=new IpsKickCommand();

		return self::$_instance;
	}

	private function getNumberOfKicks() {
		global $db;

		//Get Number of Kicks
		$user = $db->get_row("SELECT num_kicks_by_ips FROM user WHERE ID='".$_SESSION["userid"]."'");
		$num_kicks = $user->num_kicks_by_ips + 1;

		return $num_kicks;
	}

	protected function realExecute() {
		
			IpsDebugger::debug(array("executed Kick Command"=>$this->_data));
			IpsDebugger::debug(array("CALLED REALEXECUTE"=>$this));
			global $db, $settings, $phpids_settings;
			
//			$actTime = time();
//			$expireTime = $actTime + $phpids_settings["kick_seconds"];
//			
//			//Get Root-ID
//			$rootSql = "SELECT ID FROM user WHERE Login='root'";
//			$root = $db->get_row($rootSql);
//
//			//Get Number of Kicks
//			$num_kicks = $this->getNumberOfKicks();
//
//			//Save User in BannedUser-Table
//			$ban = new UserBanning($_SESSION["userid"]);
//			
//			if ($num_kicks <= 2 && !$this->_isInstantBan) {
//				//Kick User
//				$message = $phpids_settings["kick_message"];
//				$timestamp = $expireTime;
//			} else {
//				//Ban User
//				$num_kicks = 0;		//reset counter
//				$message = $phpids_settings["ban_message"];
//				$timestamp = 0;
//			}
//			$ban->banUserByTimestamp($root->ID, $message, $timestamp);
//
//			// Save Logout-Time and 'num_kicks_by_ips' in User-Table 
//			$db->query("UPDATE user SET lastlogout='".$actTime."', sessionID=NULL, num_kicks_by_ips='".$num_kicks."' WHERE ID='".$_SESSION["userid"]."'");
//
//			// Close Session
//			unset($_SESSION["gUser"]);
//			unset($GLOBALS["gConnectedUser"]);
//			@session_unset();
//			@session_destroy();
//			
//			//Message for User, the same message as with the login
//			$bannedBy = $db->get_row("SELECT Vorname, Nachname FROM user WHERE ID='".$ban->getBannedBy() ."'");
//			$bannedBy = Data::toHTML($bannedBy->Vorname." ".$bannedBy->Nachname, false);
//			if ($ban->getExpire()) {
//				$expire = "bis zum <strong>".date("d.m.Y H:i", $expireTime)."</strong>";
//			}
//			else {
//				$expire = "auf unbestimmte Zeit";
//			}
//			if (!$ban->getReason()) 
//				$reason = "gesperrt.";
//			else 
//				$reason = "aus folgendem Grund gesperrt:<br /><br />".Data::toHTML($ban->getReason(), false);
//			$message = "Ihr Login wurde von <strong>$bannedBy</strong><br />$expire $reason";
//			$message .= "<br /><br />Die aktuelle Server-Zeit ist " . date("d.m.Y H:i", $actTime) . ".";
//			
//			//New Session to "save" kick message
//			session_start();
//			$_SESSION["kick_message"] = $message;
//			$url = $settings["estudy_base_url"];
//			header("Location: " . $url . "login.php?ban=1");
//			exit();
		
	}

	protected function realSimulate($fileHandle) {
		global $db;

		$logText = Output::echoDate("Y-m-d H:i:s", time()).": ";

		$num_kicks = $this->getNumberOfKicks();		//last kick num + 1

		if ($num_kicks>=3 || $this->_isInstantBan) {
			$logText.= "Banning of ";
			$num_kicks = 0;				//reset counter
		} else {
			$logText.= "Kicking of ";
		}

		if(isset($_SESSION["Vorname"])) {
			$logText.= "attacker: ".$_SESSION["Vorname"]." ".$_SESSION["Nachname"];
		} else {
			$logText.= "attacker: ".$_SERVER['REMOTE_ADDR'];
		}

		$logText.= "\n";

		fwrite($fileHandle, $logText);

		// Simulate logout:
		unset($_SESSION["IDSDATA"]);
		unset($_SESSION["IPSDATA"]);

		// Save 'num_kicks_by_ips' in User-Table 
		$db->query("UPDATE user SET num_kicks_by_ips='".$num_kicks."' WHERE ID='".$_SESSION["userid"]."'");

		return true;
	}
}