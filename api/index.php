<?php
require '../libs/globals.inc.php';

$db_host = 'localhost';
$db_username = 'root';
$db_password = 'to_be_defined';

$sql_handler = new sql_handler($db_host, $db_username, $db_password);

$controller = new json_controller($sql_handler);
/*
$str = array(
             'path' => '/server/processlist',
             'db' => 'param1',
             'tbl' => 'param2'
             );

$_POST['json'] = json_encode($str);*/
$controller->receive();

?>