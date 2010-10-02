<?php
require_once 'phpips/lib/classes/class.IpsConfigurationAbstract.inc.php';

class IpsConfigurationIni extends IpsConfigurationAbstract{


	public function initActionConfig($path=null){
		$this->_config[self::ACTION_CONFIG_NAME]=parse_ini_file(PATH_TO_ROOT."phpips/lib/Config/ActionConfig.ini",TRUE);
	}
	


}