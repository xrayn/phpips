<?php



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