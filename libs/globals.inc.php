<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
ini_set('html_errors', true);

# PATH DEFINITION
define( 'LIBS_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR ); // TODO use __DIR__ with PHP 5.3
define( 'ROOT_PATH', LIBS_PATH . '../' );

require_once ( LIBS_PATH . 'init.inc.php' );

require_once ( LIBS_PATH . 'functions.inc.php');

?>