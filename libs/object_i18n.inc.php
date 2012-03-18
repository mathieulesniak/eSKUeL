<?php
class i18n {
	private static $_instance = null;
	private $lang_code;
	private $translations = array();


	private function __construct($lang_code) {
		$this->lang_code = $lang_code;
		if ( is_file('libs/i18n/' . $lang_code . '/' . $lang_code . '.inc.php') ) {
			include 'libs/i18n/' . $lang_code . '/' . $lang_code . '.inc.php';
			foreach ( $translations['runtimes'] as $key=>$val ) {
				define('I18N_' . strtoupper($key), $val);
			}

			unset($translations['runtimes']);
			$this->translations = $translations;
		}
		else {
			throw new Exception("Missing translation file for locale " . $lang_code, 1);
			
		}
	}

	public static function get_instance($lang_code) {
		if( is_null(self::$_instance) ) {
       		self::$_instance = new i18n($lang_code);  
     	}
 
     	return self::$_instance;
   }

   function translate($string) {
   		if ( isset($this->translations[$string]) ) {
   			return $this->translations[$string];
   		}
   		else {
   			throw new Exception("Missing translation for '$string'", 1);
   		}
   }

}

function __($str) {
	// Temporary : hard coded lang
	$i18n = i18n::get_instance('fr');
	return $i18n->translate($str);
}
?>