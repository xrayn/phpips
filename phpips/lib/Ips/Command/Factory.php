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
		// this is a php < 5.3 fix
		return call_user_func(array($cmdName, "getInstance"));
		//return $cmdName::getInstance();

	}
}