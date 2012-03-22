<?php
require '../libs/globals.inc.php';

$sql_handler = new sql_handler(HOST, USERNAME, PASSWORD);

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