<table class="dblisting">
<tr>
	<th><?= __('Table'); ?></th>
	<th><?= __('Rows'); ?></th>
	<th><?= __('Size'); ?></th>
	<th><?= __('Comment'); ?></th>
	<th>Action</th>
</tr>
<?php
foreach ( $table_list as $table ) {
	echo '<tr>' . "\n";
	echo '	<td>' . $table->name . '</td>' . "\n";
	echo '	<td>' . number_format($table->rows, 0, I18N_DECIMAL_SEPARATOR, I18N_THOUSAND_SEPARATOR) . '</td>' . "\n";
	echo '	<td>' . convert_from_bytes($table->data_length + $table->index_length) . '</td>' . "\n";
	echo '	<td>' . $table->comment . '</td>' . "\n";
	echo '	<td></td>' . "\n";
	echo '</tr>' . "\n";
}
echo '<tr>'."\n";
echo '	<td></td>'."\n";
echo '	<td>' . number_format($total_rows, 0, I18N_DECIMAL_SEPARATOR, I18N_THOUSAND_SEPARATOR) . '</td>' . "\n";
echo '	<td>' . convert_from_bytes($total_size) . '</td>' . "\n";
echo '	<td></td>' . "\n";
echo '<tr>'."\n";
?>
</table>