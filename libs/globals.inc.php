<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
ini_set('html_errors', true);

# PATH DEFINITION
define( 'LIBS_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR ); // TODO use __DIR__ with PHP 5.3
define( 'ROOT_PATH', LIBS_PATH . '../' );

# Load usefull functions
require_once ( LIBS_PATH . 'functions.inc.php');

# Start your engine
require_once ( LIBS_PATH . 'init.inc.php' );

# DEV ONLY : Load credentials from local un-commit ini file // TODO To be removed
if ( !file_exists(LIBS_PATH . '../dev.ini') ) {
	throw new Exception('INI FILE DOES NOT EXISTS');
}
$creds = parse_ini_file( LIBS_PATH . '../dev.ini' );
foreach ($creds as $key => $value) {
	define(strtoupper($key), $value);
}
# /DEV

# Localization related
/// POINT is the default decimal separator
_('.');
/// COMMA is the default thousand separator
_(',');
/// Default date formatting
_('Y-m-d');

?>