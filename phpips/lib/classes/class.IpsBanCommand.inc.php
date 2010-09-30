<?php


//require_once (PATH_TO_ROOT."common/init.inc.php"); // fuer db handle

class IpsBanCommand extends IpsKickCommand {
	private static $_instance=null;
	protected $_isInstantBan=true;		// use KickCommand as Ban

	public static function getInstance(){
		if (self::$_instance==null)
			self::$_instance=new IpsBanCommand();

		return self::$_instance;
	}
}