<?php

class IpsCommandFactory{


	/**
	 * Builds a command object of given name
	 * @param string $commandName
	 * @throws Exception
	 * @return IpsCommandAbstract
	 */
	public static function createCommand($commandName){

		//format the commandname to match our layout
		$commandName=strtolower($commandName);
		$firstChar=strtoupper(substr($commandName,0,1));
		$commandName=$firstChar.substr($commandName, 1);
		$cmdName="Ips".$commandName."Command";
		$fileName="class.".$cmdName.".inc.php";

		if(file_exists(PATH_TO_ROOT . "phpips/lib/classes/".$fileName)){
			if (intval(substr(PHP_VERSION,0,1)<5) ){
				throw new Exception("U really need to update your php! go away!");
					
			}else {
				if(intval(substr(PHP_VERSION,2,1)<3)){
					throw new Exception("Cannot use this implementation with php-version < 5.3, plz update or use other Factory");
				}
			}
				
			/*
			 * this can only be used if we have php 5.3
			 */
			require_once(PATH_TO_ROOT . "phpips/lib/classes/".$fileName);

			return $cmdName::getInstance();
				
		}
		else {
			throw new Exception("There is no Command:".$commandName);
		}
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