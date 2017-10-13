<?php
namespace libs;
class Http {

	public static function getInfoByUrl($url,$config=[]){
		$_config = [
			CURLOPT_URL => $url,
		];
		$_config = $_config + $config;
		return self::customCurl($_config);
	}
	// 自定义通用的curl方法
	public static function customCurl($config){
		$_config = [
			CURLOPT_RETURNTRANSFER => true, // 将输出放到变量中
			CURLOPT_AUTOREFERER => true, // 是否自动加上来源地址
			CURLOPT_HEADER => false, // header头
		];

		$_config = $_config + $config;
		$ch = curl_init();
		curl_setopt_array($ch, $_config);
		$res = curl_exec($ch);
		curl_close($ch);
		return $res;
	}


}