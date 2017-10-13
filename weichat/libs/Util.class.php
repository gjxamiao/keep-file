<?php
namespace libs;
class Util{

	public static function getWeather($city){
		 $res = Http::getInfoByUrl("http://api.k780.com:88/?app=weather.future&weaid=".$city."&&appkey=10003&sign=b59bc3ef6191eb9f747dd4e83c99f2a4&format=json");
		 $res = json_decode($res,true);
		 $return = "对不起，当前接口不可用";
		 if($res['success']){
		 	$return = $res['result'][0]['weather'];
		 }
		 return $return;
	}

	public static function getFortune($name){
		$msg = Http::getInfoByUrl("http://www.d1xz.net/sm/xingming-test.aspx?ITxt=".urlencode($name));
		$return = "对不起，接口不可用";
    	if(preg_match('#<div class="zxsm_p">(.*?)</div>#', $msg,$match)){
    		$return = $match[1];
    		$return = preg_replace("#<[/]?p(.*?)>#","\n",$return);
    	}
    	return $return;
	}

}