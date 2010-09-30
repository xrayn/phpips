<?php
/*--------------------------------------------------------------------------+
 This file is part of eStudy.
 phpids/classes/class.IpsWarnCommand.inc.php
 - Modulgruppe:  PHPIDS
 - Beschreibung: Main IPS Class (Intrusion Prevention System)
 - Version:      0.01, 17-11-2010
 - Autor(en):    Philipp Promeuschel <philipp.promeuschel@mni.fh-giessen.de>
 +---------------------------------------------------------------------------+
 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 as published by the Free Software Foundation; either version 2
 of the License, or any later version.
 +---------------------------------------------------------------------------+
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 +--------------------------------------------------------------------------*/

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