<?php

abstract class Class_Loader
{
	static private $map;
	static private $baseDir;

   static public function autoload($class)
   {
		if (!isset(self::$map[$class]))
		{
		    return false;
		}

		require self::$baseDir . '/' . self::$map[$class];

		return true;
   }

   static public function loadClass($class)
   {
       if (!class_exists($class, false) && !interface_exists($class, false))
       {
           self::autoload($class);

           if (!class_exists($class, false) && !interface_exists($class, false))
           {
               throw new Class_Loader__ClassNotFound_Exception($class);
           }
       }
   }

   static public function register($baseDir, $mapPath)
   {
	   self::$baseDir = $baseDir;
       self::$map = require $mapPath;

       ini_set('unserialize_callback_func', 'spl_autoload_call');

       spl_autoload_register(array(__CLASS__, 'autoload'));
   }

   static public function unregister()
   {
       spl_autoload_unregister(array(__CLASS__, 'autoload'));
   }
}

class Class_Loader__Exception extends Exception {}
class Class_Loader__ClassNotFound_Exception extends Class_Loader__Exception {}

Class_Loader::register( LIBS_PATH, LIBS_PATH . 'autoload.php');

?>