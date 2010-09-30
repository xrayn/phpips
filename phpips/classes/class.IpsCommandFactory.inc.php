<?php

require_once (PATH_TO_ROOT . "phpids/classes/class.IpsLogCommand.inc.php");
require_once (PATH_TO_ROOT . "phpids/classes/class.IpsKickCommand.inc.php");
require_once (PATH_TO_ROOT . "phpids/classes/class.IpsBanCommand.inc.php");
require_once (PATH_TO_ROOT . "phpids/classes/class.IpsWarnCommand.inc.php");
require_once (PATH_TO_ROOT . "phpids/classes/class.IpsMailCommand.inc.php");
//require_once (PATH_TO_ROOT . "phpids/classes/class.IpsFinalMailCommand.inc.php");


class IpsCommandFactory{

	public static function createCommand($commandName){
		switch (strtolower($commandName)){
			case "log":
				$command=IpsLogCommand::getInstance();
				break;
			case "mail":
				$command=IpsMailCommand::getInstance();
				break;
			case "warn":
				$command=IpsWarnCommand::getInstance();
				break;
			case "kick":
				$command=IpsKickCommand::getInstance();
				break;
			case "ban":
				$command=IpsBanCommand::getInstance();
				break;
			default: 
				throw new Exception("There is no Command:".$commandName);
		}
		return $command;
	}
}