<?php
class IpsConfigurationFactory {

	public static function createConfig($configType=null, $options=array()){

		switch ($configType){
			case "ini":
				require_once 'phpips/lib/classes/class.IpsConfigurationIni.inc.php';
				$configObject=new IpsConfigurationIni($options["path"]);
				$configObject->initActionConfig($options["path"]);
				return $configObject;
				break;
			default:
				throw new Exception("There in no configuationtype: ".$configType);
				break;


		}
	}


}