<?php

/*--------------------------------------------------------------------------+
 This file is part of eStudy.
 phpids/phpids_init.inc.php
 - Modulgruppe:  PHPIDS
 - Beschreibung: Initialize and run PHP-IDS
 - Version:      0.1, 05-06-2010
 - Autor(en):    Andre Rein <andre.rein@mni.fh-giessen.de>
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

class IpsDebugger {


	public static function debug($var){
		global $phpids_settings;
		//check if debugging is enabled!
		if ($phpids_settings["debug_activated"]){
		//
		require_once("phar://Zend.phar/Zend/Log/Writer/Firebug.php");
		require_once("phar://Zend.phar/Zend/Log.php");

		$writer = new Zend_Log_Writer_Firebug();
		$logger = new Zend_Log($writer);

		$request = new Zend_Controller_Request_Http();
		$response = new Zend_Controller_Response_Http();
		$channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
		$channel->setRequest($request);
		$channel->setResponse($response);

		// Ausgabe buffering starten
		ob_start();

		// Jetzt können Aufrufe an den Logger durchgeführt werden
		$logger->log($var, Zend_Log::DEBUG);

		// Logdaten an den Browser senden
		$channel->flush();
		$response->sendHeaders();
		}
	}
}