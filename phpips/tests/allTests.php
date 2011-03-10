<?php

error_reporting(E_ALL | E_STRICT | @E_DEPRECATED);
//require_once 'PHPUnit/Framework/TestSuite.php';
//require_once 'PHPUnit/TextUI/TestRunner.php';
//require_once 'PHPUnit/Util/Filter.php';
define("PATH_TO_ROOT", "/var/www/eclipse-workspaces/eclipse_helios/php-ips/" );
define("PATH_TO_PHPIDS", PATH_TO_ROOT."phpids-0.6.5/lib/");
define("PATH_TO_PHPIPS", PATH_TO_ROOT."phpips/");
set_include_path  (get_include_path().":".PATH_TO_PHPIDS);

if (!defined('PHPUnit_MAIN_METHOD')) {
	define('PHPUnit_MAIN_METHOD', 'allTests');
}


class allTests
{
	public static function main()
	{
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}

	public static function suite()
	{
        $suite = new PHPUnit_Framework_TestSuite('PHPIPS');
        require_once 'IPS/VersionTest.php';
        $suite->addTestSuite('IPS_VersionTest');
        require_once 'IPS/AttackTest.php';
        $suite->addTestSuite('IPS_AttackTest');
        return $suite;
	}
}

if (PHPUnit_MAIN_METHOD == 'allTests') {
	allTests::main();
}
