<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
ini_set('html_errors', true);

# PATH DEFINITION
define( 'LIBS_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR ); // TODO use __DIR__ with PHP 5.3
define( 'ROOT_PATH', LIBS_PATH . '../' );

require_once ( LIBS_PATH . 'init.inc.php' );

require_once ( LIBS_PATH . 'functions.inc.php');

/*
require_once 'object_simple_object.inc.php';
require_once 'object_controller.inc.php';
require_once 'functions.inc.php';
require_once 'db_layers/interface.inc.php';
require_once 'db_layers/mysql.inc.php';

require_once 'object_database.inc.php';
require_once 'object_table.inc.php';
require_once 'object_field.inc.php';
require_once 'object_index.inc.php';
*/

?>