<?php


interface Ips_Command_Interface {

	public function enableExecute();

	public function addData($data);
	public static function getInstance();
	//public function realSimulate();
	public function simulate();
	public function execute();
	

}