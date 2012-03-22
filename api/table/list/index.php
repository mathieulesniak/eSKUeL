<?php

require_once('../../../libs/globals.inc.php');

$output = new APICall();
$output->message = "SHOW TABLES;";
$output->return_code = 0;
$output->data = new stdClass();
$output->data->header = array('name', 'rows', 'engine', 'collation', 'size');
$output->data->data = array();

$tables = array('table', 'tbl', 'forum', 'users', 'posts', 'banner', 'blocks', 'channel', 'ip', 'topics', 'meta', 'options', 'comments', 'ping', 'logs');
$max = rand(8, count($tables));
$names = array();
for ($i=1; $i < $max; $i++)
{
	array_push($names, sprintf('%s_%s', $tables[array_rand($tables)], $i));
}
array_push($names, 'datable');
sort($names);
foreach ($names as $name)
{
	array_push($output->data->data, array(
		sprintf('<a class="btn btn-small" href="#%s"><i class="icon-large icon-list-alt"></i> %s</a> ', $name, $name),
		rand(8, 10000),
		'innoDB',
		'utf8_general_ci',
		sprintf('%s Kb', rand(8, 10000))
		)
	);
}

echo $output;

?>