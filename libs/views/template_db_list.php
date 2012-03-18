<div class="database_list">
<ul>
<?php 
foreach ( $db_list as $current_database ) {
	echo '<li><a href="?db=' . $current_database . '">' . $current_database . '</a>';
	if ( $selected_db == $current_database && is_array($table_list) ) {
		echo '<ul>' . "\n";
		foreach ( $table_list as $current_table ) {
			echo '<li><a href="?db=' . $current_database . '&tbl=' . $current_table->name . '">' . $current_table->name . '</a></li>' . "\n";
		}
		echo '</ul>' . "\n";
	}
	echo '</li>'."\n";
	
}
?>
</ul>
</div>