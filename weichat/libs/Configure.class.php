<?php

namespace libs;

class  Configure {

	public static $config;

	public static function getConfigs($key=""){
		if(!self::$config){
			self::$config = require APP_PATH.DS.'config'.DS."web.inc.php";
		}
		
		if($key){
			return isset(self::$config[$key]) ? self::$config[$key] : null;
		}
		return self::$config;
	}

}