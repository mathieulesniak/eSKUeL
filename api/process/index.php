<?php

require_once('../../libs/globals.inc.php');

$output = new APICall();
$output->message = "SHOW PROCESSLIST;";
$output->return_code = 0;
$output->data = new stdClass();
$output->data->header = array("id","user","host","db","command","time","state","info");
$output->data->data = array(
		array(
			"5421869",
			"root",
			"localhost",
			"emailing",
			"Sleep",
			"3",
			"",
			null
		),
		array(
			"5421899",
			"root",
			"localhost",
			"emailing",
			"Sleep",
			"1",
			"",
			null
		),
		array(
			"5421901",
			"root",
			"localhost",
			"prod_meteosun_com",
			"Query",
			"0",
			"Sorting result",
			"SELECT , ( 6371  acos( cos( radians(36.5082) ) * cos( radians( lat ) ) * cos( radians( lon ) - rad"
		),
		array(
			"5421902",
			"root",
			"localhost",
			null,
			"Query",
			"0",
			null,
			"SHOW PROCESSLIST"
		)
	);

echo $output;

?>