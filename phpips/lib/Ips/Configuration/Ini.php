<?php


class Ips_Configuration_Ini extends Ips_Configuration_Abstract{


	public function initActionConfig($path=null){
		$this->_config[self::ACTION_CONFIG_NAME]=parse_ini_file(PATH_TO_ROOT.$path,TRUE);
	}
	


}