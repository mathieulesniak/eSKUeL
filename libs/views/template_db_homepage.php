<table class="dblisting">
<tr>
	<th><?= __('Table'); ?></th>
	<th><?= __('Rows'); ?></th>
	<th><?= __('Size'); ?></th>
	<th><?= __('Comment'); ?></th>
	<th>Action</th>
</tr>
<?php
$tt = 41;
define('TEST', $tt);

foreach ( $table_list as $table ) {
	echo '<tr>' . "\n";
	echo '	<td>' . $table->name . '</td>' . "\n";
	echo '	<td>' . number_format($table->rows, 0) . '</td>' . "\n";
	echo '	<td>' . convert_from_bytes($table->data_length + $table->index_length) . '</td>' . "\n";
	echo '	<td>' . $table->comment . '</td>' . "\n";
	echo '	<td></td>' . "\n";
	echo '</tr>' . "\n";
}
?>
</table>