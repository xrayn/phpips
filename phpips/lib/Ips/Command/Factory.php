<?php



class Ips_Command_Factory{

	/**
	 * Builds a command object of given name
	 * @param string $commandName
	 * @throws Exception
	 * @return IpsCommandAbstract
	 */
	public static function createCommand($commandName){
		// get a registry Object!
		$registry =Ips_Registry::getInstance();


		//format the commandname to match our layout
		$commandName=strtolower($commandName);
		$firstChar=strtoupper(substr($commandName,0,1));
		//$commandName=$firstChar.substr($commandName, 1);

		$cmdName=$registry->getCommandModulePrefix().$firstChar.substr($commandName, 1);
		//$cmdName=$prefix."_".$firstChar.substr($commandName, 1);
		
		//check if the Command exists
		
		return $cmdName::getInstance();

	}

	/**
	 * Use this if your php is < 5.3
	 * You have to register your commands within here for yourself
	 * @param string $commandName
	 * @throws Exception
	 * @return IpsCommandAbstract
	 */
	public static function createCommandOldWay($commandName){
		require_once (PATH_TO_ROOT . "phpips/lib/classes/class.IpsLogCommand.inc.php");
		require_once (PATH_TO_ROOT . "phpips/lib/classes/class.IpsKickCommand.inc.php");
		require_once (PATH_TO_ROOT . "phpips/lib/classes/class.IpsBanCommand.inc.php");
		require_once (PATH_TO_ROOT . "phpips/lib/classes/class.IpsWarnCommand.inc.php");
		require_once (PATH_TO_ROOT . "phpips/lib/classes/class.IpsMailCommand.inc.php");

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