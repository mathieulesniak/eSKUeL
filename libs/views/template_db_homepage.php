<table class="dblisting">
<tr>
	<th><?= _('Table'); ?></th>
	<th><?= _('Rows'); ?></th>
	<th><?= _('Size'); ?></th>
	<th><?= _('Comment'); ?></th>
	<th><?= _('Action'); ?></th>
</tr>
<?php
foreach ( $table_list as $table ) {
	echo '<tr>' . "\n";
	echo '	<td>' . $table->name . '</td>' . "\n";
	echo '	<td>' . number_format( $table->rows, 0, _('.'), _(',') ) . '</td>' . "\n";
	echo '	<td>' . convert_from_bytes($table->data_length + $table->index_length) . '</td>' . "\n";
	echo '	<td>' . $table->comment . '</td>' . "\n";
	echo '	<td></td>' . "\n";
	echo '</tr>' . "\n";
}
echo '<tr>'."\n";
echo '	<td></td>'."\n";
echo '	<td>' . number_format( $total_rows, 0, _('.'), _(',') ) . '</td>' . "\n";
echo '	<td>' . convert_from_bytes($total_size) . '</td>' . "\n";
echo '	<td></td>' . "\n";
echo '<tr>'."\n";
?>
</table>