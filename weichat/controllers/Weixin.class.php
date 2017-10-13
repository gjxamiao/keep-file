<?php
namespace controllers;
use libs\Controller;
use libs\WeChat;
use libs\Http;
use models\Users;
class  Weixin extends Controller {
	private $weixin;

	public function init(){
		$this->weixin = new WeChat;
	}
	public function index(){
		// 调用对应的方法实现与微信通
		$this->weixin->parseMessage();
	}

	public function menu(){
		// 调用对应的方法实现与微信通
		$this->weixin->createMenu();
	}

  public function delete(){
  
    $this->weixin->deleteMenu();
  }
	public function map(){
		$jsapi = $this->weixin->getJsApi(); // 获取ticket值
		$jsticket = $jsapi->ticket;
		$appid = APPID;
		$timestamp = time();
		$nonceStr = $this->createNonceStr();

	// 确保地址动态生成 
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    	$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$signature = sha1("jsapi_ticket=".$jsticket."&noncestr=".$nonceStr."&timestamp=".$timestamp."&url=".$url);
		$this->assign(['appid'=>$appid,'timestamp'=>$timestamp,'noncestr'=>$nonceStr,'signature'=>$signature]);
		
		$this->display();

	}


	private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  public function login(){
  	$url = $this->weixin->createLoginUrl();
  	header("Location: ".$url);
  }


  public function doLogin(){
  	$code = $_GET['code'];
  	$accesstoken = $this->weixin->getWebAccess($code);
  	//echo $accesstoken['openid'];
  	//exit;
  	$userinfo = Http::getInfoByUrl("https://api.weixin.qq.com/sns/userinfo?access_token=".$accesstoken['access_token']."&openid=".$accesstoken['openid']."&lang=zh_CN");
  	$userinfo = json_decode($userinfo,true);
  	//var_dump($userinfo);
  	//exit;
  	$model = new Users;
  	$user = $model->where(['sid'=>$userinfo['openid'],'fromsite'=>'wechat'])->select();
  	if(!$user){
  		// 添加进去
  		$model->insert(['nickname'=>$userinfo['nickname'],'sid'=>$userinfo['openid'],'fromsite'=>'wechat','username'=>"wx_".uniqid().time(),'passwd'=>md5('123456'),'avatar'=>$userinfo['headimgurl']]);
  		$user = $model->where(['sid'=>$userinfo['openid'],'fromsite'=>'wechat'])->select();
  	}
  	// 
  	$this->assign($user[0]);
  	$this->display();
  }
}