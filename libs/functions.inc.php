<?php

 /**
  * Return a post parameter value
  *
  * @param string $name of the requested value
  * @param mixed $default value
  * @return mixed value of the post parameter, or default value, or false
  **/
 function getPostParam($name, $default = false)
 {
 	if( array_key_exists($name, $_POST) )
	{
 		return $_POST[$name];
	}
 	return $default;
 }


/**
 * Return a post or get parameter value
 *
 * @param string $name of the requested value
 * @param mixed $default value
 * @return mixed value of the post or get parameter, or default value, or false
 **/
function getParam($name, $default = false)
{
	if( $post = getPostParam($name) )
	{
		return $post;
	}
	else if( array_key_exists($name, $_GET) )
	{
		return $_GET[$name];
	}
	return $default;
}

/**
 * Detect if a post or get parameter exists
 *
 * @param string $name of the requested value
 * @return boolean
 **/
function hasParam($name)
{
	return ( getParam($name) !== false );
}


function _p($var) {
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}

function convert_from_bytes($bytes) {
   $unit = array(_('b'), _('Kb'),_('Mb'),_('Gb'),_('Tb'),_('Pb'),_('Eb'));
   $precision = 1;

   return number_format(round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision),
						$precision,
						_('NUMBER_DECIMAL_SEPARATOR'),
						_('NUMBER_THOUSAND_SEPARATOR'))
		. ' ' . $unit[$i];
}

?>