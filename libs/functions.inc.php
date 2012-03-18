<?php
function _p($var) {
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}

function convert_from_bytes($bytes) {
   $unit = array(__('b'), __('Kb'),__('Mb'),__('Gb'),__('Tb'),__('Pb'),__('Eb'));
   $precision = 1;

   return number_format(round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision), 
   						$precision, 
   						I18N_DECIMAL_SEPARATOR,
   						I18N_THOUSAND_SEPARATOR)
   			. ' ' . $unit[$i];
}

?>