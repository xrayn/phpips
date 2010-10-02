<?php

/*
 * extending the kick command implies loading it before, cause this cannot be resolved from the factory
 */

class Ips_Command_Ban extends Ips_Command_Kick {
	private static $_instance=null;
	protected $_isInstantBan=true;		// use KickCommand as Ban

	public static function getInstance(){
		if (self::$_instance==null)
			self::$_instance=new Ips_Command_Ban();

		return self::$_instance;
	}
}