<?php

class ObjectException extends Exception
{
	const UNKNOWN_PROPERTY			= 1;
	const WRONG_SCOPE				= 2;
	const TEMPLATE_NOT_FOUND		= 3;
	const GET_TABLES_FAIL			= 4;
	const MISSING_TRANSLATION_FILE	= 5;
	const MISSING_TRANSLATION		= 6;

	function __construct()
	{
		$args = func_get_args();
		$code = array_shift($args);
		$message = vsprintf( $this->getMessageFromCode($code), $args );
		parent::__construct( $message, $code );
	}

	private function getMessageFromCode( $code = false )
	{
		switch ( $code )
		{
			case self::UNKNOWN_PROPERTY :
				return _("Unknown property %s");
				break;
			case self::WRONG_SCOPE :
				return _("Wrong scope");
				break;
			case self::TEMPLATE_NOT_FOUND :
				return _("Template %s not found");
				break;
			case self::GET_TABLES_FAIL :
				return _("Unable to load table list from %s");
				break;
			case self::MISSING_TRANSLATION_FILE :
				return _("Missing translation file for locale %s");
				break;
			case self::MISSING_TRANSLATION :
				return _("Missing translation for '%s'");
				break;
			default:
				return _('Unknown Object Exception');
				break;
		}
	}
}

?>