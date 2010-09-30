<?php

/*
 * extending the kick command implies loading it before, cause this cannot be resolved from the factory
 */
require_once (PATH_TO_ROOT . "phpips/lib/classes/class.IpsKickCommand.inc.php");


class IpsBanCommand extends IpsKickCommand {
	private static $_instance=null;
	protected $_isInstantBan=true;		// use KickCommand as Ban

	public static function getInstance(){
		if (self::$_instance==null)
			self::$_instance=new IpsBanCommand();

		return self::$_instance;
	}
}