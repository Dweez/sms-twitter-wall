<?php

/**
 * Mode debug, désactivé par défaut
 */
if (!defined('STW_DEBUG')) {
	define('STW_DEBUG',0);
}

if (STW_DEBUG) {
	ini_set('html_errors', 1);
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL | E_STRICT);
}
/**
 * Smswall : common code
 */

if ($_SERVER['SCRIPT_FILENAME'] === __FILE__) {
	die('Dead end');
}

/**
 * Live data directory
 */
if (!defined('SMSWALL_DATADIR')) {
	define('SMSWALL_DATADIR',dirname(__FILE__).DIRECTORY_SEPARATOR.'data');
}

/**
 * Check installation
 */
try {
	if (!is_dir(SMSWALL_DATADIR)) {
		/* mkdir(SMSWALL_DATADIR,umask()|0700,true); */
		die("data : directory not found");
	}
	$db = new PDO('sqlite:'.SMSWALL_DATADIR.DIRECTORY_SEPARATOR.'wall.sqlite');
}
catch(PDOException $e){
	echo $e->getMessage();
}
