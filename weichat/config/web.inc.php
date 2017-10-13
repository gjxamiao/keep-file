<?php
// 全局配置
$db = require('database.inc.php');
$config = array(

	'weixin'=>[
		'token'=>'ymm',
		'appid'=>'',
		'appsecret'=>'',
	],
	'enableCacheFile'=>false,
	'cacheLifeTime'=>0,

	'templateParseStr'=>[
		'CSSPATH'=>__STATIC__.'css'.DS,
		'JSPATH'=>__STATIC__.'js'.DS,
	]
);
return array_merge($db,$config);