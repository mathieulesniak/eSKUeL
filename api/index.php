<?php
require '../libs/globals.inc.php';

$sql_handler = new SQLHandler(HOST, USERNAME, PASSWORD);

$controller = new JsonController($sql_handler);
/*
$str = array(
             'path' => '/tbl/query',
             'db' => 'ma_db',
             'tbl' => 'blog_comments',
             'query' => 'SELECT * FROM blog_comments',
             'from' => 0, 
             'nb_records' => 2
             );

$str = array(
             'path' => '/db/get_tbl',
             'db' => 'ma_db'
             );

$_POST['json'] = json_encode($str);*/
$controller->receive();

?>