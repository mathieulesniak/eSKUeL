<div class="database_list">
<ul>
<?php
foreach ( $db_list as $current_database )
{
	$url = new URLBuilder();
	$url->addParam('db', $current_database);
	echo sprintf('<li><a href="%s">%s</a>', $url, $current_database);
	if ( $selected_db == $current_database && is_array($table_list) )
	{
		echo '<ul>' . "\n";
		foreach ( $table_list as $current_table )
		{
			$url->addParam('tbl', $current_table->name);
			echo sprintf('<li><a href="%s">%s</a></li>', $url, $current_table->name) . "\n";
		}
		echo '</ul>' . "\n";
	}
	echo '</li>'."\n";
}
?>
</ul>
</div>