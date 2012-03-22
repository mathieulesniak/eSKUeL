<?php

class APICall extends stdClass
{

	function __construct()
	{
	}

	function __toString()
	{
		header('Cache-Control: no-cache, must-revalidate');
		switch( getParam('format') )
		{
			case 'html':
				header('Content-type: text/html');
				return (string) new TableBuilder( $this->data );
				break;
			case 'json':
			default:
				header('Content-type: application/json');
				return json_encode( $this );
				break;
		}
	}

}

?>