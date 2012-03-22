<?php

require_once('./libs/globals.inc.php');

$conf = array('server' => 'localhost', 'db' => 'mydb', 'table' => 'datable');
$max_rows = 50;

$datas = new stdClass();
$datas->data = new stdClass();
$datas->data->header = array('', 'id', 'amount', 'desc', 'ref', 'code');
$datas->data->data = array();
for ($i = 1; $i <= $max_rows; $i++)
{
	$datas->data->data[$i] = array();
	foreach ($datas->data->header as $name)
	{
		switch( $name )
		{
			case '':
				$arr = '<input type="checkbox" />';
				break;
			case 'desc':
				$arr = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
				break;
			case 'amount':
			case 'ref':
				$arr = $i*rand(0, $max_rows);
				break;
			case 'code':
				$arr = md5($i);
				break;
			default:
				$arr = $i;
				break;
		}
		array_push($datas->data->data[$i], $arr);
	}
}

$table = new TableBuilder($datas->data);
$table->setClass('datas editable table table-condensed table-striped');

$url = new URLBuilder();

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8 />
	<title><?php echo implode(' &rsaquo; ', $conf); ?></title>
	<link rel="stylesheet" type="text/css" media="screen" href="static/css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="static/css/font-awesome.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="static/css/eskuel.css" />
	<script type="text/javascript" src="static/js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="static/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="static/js/eskuel.js"></script>
	<!--[if IE]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>
	<div id="nav">
		<div id="actions">

			<a class="btn btn-large" href="#"><i class="icon-large icon-bookmark"></i> <?php echo $conf['server']; ?></a>

			<div class="btn-group">
				<a class="btn btn-large" href="#"><i class="icon-large icon-th"></i> <?php echo $conf['db']; ?></a>
				<a class="btn btn-large dropdown-toggle" data-toggle="dropdown" href="#"><span class="icon-chevron-down"></span></a>
				<ul class="dropdown-menu">
					<li><a href="#"><i class="icon-eye-open"></i> Views</a></li>
					<li><a href="#"><i class="icon-list-alt"></i> Insert table</a></li>
					<li class="divider"></li>
					<li><a href="#"><i class="icon-signout"></i> Export</a></li>
					<li><a href="#"><i class="icon-signin"></i> Import</a></li>
					<li class="divider"></li>
					<li><a href="#"><i class="icon-remove"></i> Drop</a></li>
					<li><a href="#"><i class="icon-cog"></i> Maintenance</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<a class="btn btn-large toggle" data-toggle="modal" data-target="#modal" data-icon="icon-th" data-header="<?php echo $conf['db']; ?> tables :" href="<?php echo $url->setURL('api/table/list/')->addParam('db', $conf['db']); ?>"><i class="icon-large icon-list-alt"></i> <?php echo $conf['table']; ?></a>
				<a class="btn btn-large dropdown-toggle" data-toggle="dropdown" href="#"><span class="icon-chevron-down"></span></a>
				<ul class="dropdown-menu">
					<li><a href="#"><i class="icon-pencil"></i> Structure</a></li>
					<li><a href="#"><i class="icon-plus"></i> Insert</a></li>
					<li class="divider"></li>
					<li><a href="#"><i class="icon-signout"></i> Export</a></li>
					<li><a href="#"><i class="icon-signin"></i> Import</a></li>
					<li class="divider"></li>
					<li><a href="#"><i class="icon-refresh"></i> Truncate</a></li>
					<li><a href="#"><i class="icon-remove"></i> Drop</a></li>
					<li><a href="#"><i class="icon-cog"></i> Maintenance</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<a class="btn btn-large" href="#" rel="tooltip" title="Search"><i class="icon-large icon-search"></i></a>
				<a class="btn btn-large" data-toggle="modal" href="#sql" rel="tooltip" title="Execute query"><i class="icon-large icon-edit"></i></a>
			</div>

			<div class="btn-group">
				<a class="btn btn-large" href="#" rel="tooltip" title="Previous page"><i class="icon-chevron-left"></i></a>
				<a class="btn btn-large" href="#" rel="tooltip" title="50 per page"><i class="icon-list"></i> 1/453</a>
				<a class="btn btn-large" href="#" rel="tooltip" title="Next page"><i class="icon-chevron-right"></i></a>
			</div>

			<div class="btn-group">
				<a class="btn btn-large" href="#" rel="tooltip" title="Manage users"><i class="icon-large icon-user"></i></a>
				<a class="btn btn-large" data-toggle="modal" data-target="#modal" data-icon="icon-bar-chart" data-header="Processlist" href="api/process/" rel="tooltip" title="Processlist"><i class="icon-large icon-bar-chart"></i></a>
			</div>

		</div>
	</div>
	<?php echo $table; ?>

	<div class="modal" id="sql">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">×</a>
			<h3><i class="icon-large icon-edit"></i> Execute query</h3>
		</div>
		<div class="modal-body">
			<textarea>SELECT * FROM datable WHERE 1;</textarea>
		</div>
		<div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal">Close</a>
			<a href="#" class="btn btn-primary" data-dismiss="modal">Execute</a>
		</div>
	</div>

	<div class="modal" id="modal">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">×</a>
			<h3></h3>
		</div>
		<div class="modal-body">
		</div>
		<div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal">Close</a>
		</div>
	</div>

</body>
</html>