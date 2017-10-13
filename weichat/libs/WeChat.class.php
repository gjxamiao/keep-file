<?php
namespace libs;
class WeChat{
	public $textTpl = "<xml>
				 <ToUserName><![CDATA[%s]]></ToUserName>
				 <FromUserName><![CDATA[%s]]></FromUserName>
				 <CreateTime>%s</CreateTime>
				 <MsgType><![CDATA[text]]></MsgType>
				 <Content><![CDATA[%s]]></Content>
				 </xml>";
	public $header_doc = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[news]]></MsgType>
				<ArticleCount>%s</ArticleCount>
				<Articles>";

	public $item_doc = "<item>
				<Title><![CDATA[%s]]></Title> 
				<Description><![CDATA[%s]]></Description>
				<PicUrl><![CDATA[%s]]></PicUrl>
				<Url><![CDATA[%s]]></Url>
				</item>";
	public $end_doc = "</Articles></xml>";

	public function __construct(){
		$flag = $this->signal();
		if($flag && isset($_GET['echostr'])){
			echo $_GET['echostr'];
		}else{
			echo '';
		}
	}
	public function signal(){
		if(isset($_GET['echostr'])){
			$timestamp = $_GET['timestamp']; // 时间戳
			$nonce = $_GET['nonce']; // 随机数
			$signature = $_GET['signature'];
			// 第一步生成签名
			$arr = [TOKEN,$timestamp,$nonce];
			sort($arr,SORT_STRING);
			$str = sha1(implode('',$arr));
			if($str == $signature){
				return true;
			}else{
				return false;
			}
		}
		return true;
	}
	// 获取token值
	public function getAccessToken(){
		if(file_exists("./accesstoken") && time()-filemtime("./accesstoken")<7200){
			$accessToken = file_get_contents("./accesstoken");
		}else{
			$accessToken = Http::getInfoByUrl("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET);
			file_put_contents("./accesstoken", $accessToken);
		}
		$accessToken = json_decode($accessToken);
		return $accessToken;
	}

	public function createMenu(){
		// 第一步获取accesstoken
		$accesstoken = $this->getAccessToken();
		// 第二步进行菜单的提交操作
		//  https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$accesstoken['accesstoken']
		// post
		// 数据是一个json格式
		$data = '{
     "button":[
     {	
          "type":"click",
          "name":"首页",
          "key": "home"
      },
      {	
          "type":"click",
          "name":"简介",
          "key": "intro"
      },
      {
           "name":"菜单",
           "sub_button":[
           {	
               "type":"view",
               "name":"我的位置",
               "url":"http://47.93.226.132/ymm/weixin2017-08-22-1/index.php/Weixin/map"
            },
            {
               "type":"view",
               "name":"我的商城",
               "url":"http://47.93.226.132/ymm/weixin2017-08-22-1/index.php/shop/index"
            }]
       }]
 }';

 	$res = Http::getInfoByUrl("https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$accesstoken->access_token,[CURLOPT_CUSTOMREQUEST=>"POST",CURLOPT_POSTFIELDS=>$data,CURLOPT_SSL_VERIFYPEER=>false]);
 	var_dump($res);

	}


	public function deleteMenu(){
		$accesstoken = $this->getAccessToken();
		$res = Http::getInfoByUrl("https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$accesstoken->access_token);
		var_dump($res);
	}

	// 获取用户资料
	public function getUserInfoById($openid){
		$accesstoken = $this->getAccessToken();
		$res = Http::getInfoByUrl("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accesstoken->access_token."&openid=".$openid."&lang=zh_CN");
		return json_decode($res);
	}

	// 获取关注该公众号的所有用户并进行存储
	public function getUserList(){
		if(file_exists("./userlist")){
			$res = file_get_contents("./userlist");
		}else{
			$accesstoken = $this->getAccessToken();
			$res = Http::getInfoByUrl("https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$accesstoken->access_token."&next_openid=");
			file_put_contents("./userlist", $res);
		}
		return json_decode($res,true);
	}


	public function sendAll(){
		$tagid = isset($_GET['tagid']) ? intval($_GET['tagid']) : 0;

		$accesstoken = $this->getAccessToken();
		if($tagid){
			$data= '{
			   "filter":{
			      "is_to_all":false,
			      "tag_id":'.$tagid.'
			   },
			   "text":{
			      "content":"我就是我，不一样的MM"
			   },
			    "msgtype":"text"
			}';
			$res = Http::getInfoByUrl("https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=".$accesstoken->access_token,[
			CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$data]);
		}else{


				$userlist = $this->getUserList();
				//var_dump($userlist);
				$users = implode(',',$userlist['data']['openid']);
				//var_dump($users);
				$data = '{"touser":[';
				foreach($userlist['data']['openid'] as $openid){
					$data.='"'.$openid.'",';
				}
				$data = rtrim($data,',');
				$data.='],
		    "msgtype": "text",
		    "text": { "content": "我亲爱的们，你们都是我的宝贝！"}
		}';
			$res = Http::getInfoByUrl("https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=".$accesstoken->access_token,[
			CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$data]);
		}
		
		var_dump($res);

	}

	// 创建标签
	public function createTag(){
		$data = '{
  			"tag" : {
    			"name" : "php1505c"
  			}
		}';
		$accesstoken = $this->getAccessToken();
		$res = Http::getInfoByUrl("https://api.weixin.qq.com/cgi-bin/tags/create?access_token=".$accesstoken->access_token,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$data
			]);
		var_dump($res);
	}

	// 获取标签
	public function getTags(){
		$accesstoken = $this->getAccessToken();
		$res = Http::getInfoByUrl("https://api.weixin.qq.com/cgi-bin/tags/get?access_token=".$accesstoken->access_token);
		var_dump($res);
	}


	// 给所有用户打上标签
	public function setUserTag(){
		// 拼接$data
		$data = '{
  "openid_list" : [';
  		$userlist = $this->getUserList();
  		foreach($userlist['data']['openid'] as $key=>$openid){
  			if($key>48) break;
			$data.='"'.$openid.'",';
		}
		$data = rtrim($data,',');
		$data.='],"tagid" : 100}';
		$accesstoken = $this->getAccessToken();
		$res= Http::getInfoByUrl("https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=".$accesstoken->access_token,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$data]);
		var_dump($res);
	}
	// 消息解释
	public function parseMessage(){
		// 类型 MsgType 进行不同的解释
		// 接收信息
		// 第一种方式  $GLOBAL['HTTP_RAW_POST_DATA'];
		// 第二种方式 流式读取   php://input 得到原始的数据
		$data = file_get_contents("php://input");
		//echo $data;
		if($data){
			// 如何解析XML
			// 1. 使用SimpleXml
				//new SimpleXMLElement
			// 2. DOMDocument
			// 3. XMLReader  当内存小的情况，它是首选
			// 4. simplexml_load_string simplexml_load_file
			$postObj = simplexml_load_string($data);
			// 得到消息
            $type = trim($postObj->MsgType);
            $response = '';
            // 根据消息类型进行解析和响应
            switch($type){
            	case 'event':
            		$response =	$this->handleEvent($postObj);
            		break;
            	case 'text':
            		$response = $this->handleText($postObj->ToUserName,$postObj->FromUserName,$postObj->Content);
            		break;
            	case 'image':
            		$response = $this->handleImage($postObj);
            		break;
            	case 'voice':
            		$response = $this->handleVoice($postObj);
            		break;
            	case 'location':
            		$response = $this->handleLocation($postObj);
            		break;
            	case 'link':
            		$response = $this->handleLink($postObj);
            		break;
            	default:
            }
            echo $response;
		}
	}
	// 处理事件
	public function handleEvent($postObj){
		switch($postObj->Event){
            		case "subscribe":
            			$userInfo = $this->getUserInfoById($postObj->FromUserName);
            			$msg = [
            				[
            					'title'=>'欢迎你,亲爱的'.$userInfo->nickname,
            					'description'=>'我们一起撸一撸',
            					'picurl'=>'https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=3300272572,3842317353&fm=26&gp=0.jpg',
            					'url'=>'http://www.baidu.com',
            				],	
            				[
            					'title'=>'欢迎你,亲爱的'.$userInfo->nickname,
            					'description'=>'我们一起撸一撸',
            					'picurl'=>$userInfo->headimgurl,
            					'url'=>'http://www.baidu.com',
            				],	
            			];
            			$response = $this->reponseText($postObj->ToUserName,$postObj->FromUserName,$msg);
            			break;

            			case "CLICK":
	            			if($postObj->EventKey == "postus"){
	            				$response = $this->reponseText($postObj->ToUserName,$postObj->FromUserName,"<a href='http://www.baidu.com'>百度</a>");
	            			}elseif($postObj->EventKey == "intro"){
	            				$msg = [
            					[
	            					'title'=>'八维研修学院',
	            					'description'=>'北京八维是一个...,此处省略一亿万字',
	            					'picurl'=>'https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=3300272572,3842317353&fm=26&gp=0.jpg',
	            					'url'=>'http://www.bwei.net/',
            					]
            					];
            					$response = $this->reponseText($postObj->ToUserName,$postObj->FromUserName,$msg);
	            			}

            			break;
            			case "VIEW":

            			// 统计
            			
            			break;
            		}

            return $response;			
	}
	// 发送文本信息
	public function reponseText($fromUser,$toUser,$message){
		if(is_array($message)){
			$str = sprintf($this->header_doc,$toUser,$fromUser,time(),count($message));
			foreach($message as $row){
				$str.= sprintf($this->item_doc,$row['title'],$row['description'],$row['picurl'],$row['url']);
			}
			$str.=$this->end_doc;
		}else{
			$str = sprintf($this->textTpl,$toUser,$fromUser,time(),$message);
		}

		return  $str;
	} 

	public function handleText($fromUser,$toUser,$message){
		$msg = "我还不知道怎么回答你...宝宝正在努力学习！";
		switch($message){
			case '周国强':
				$msg = "全八维最帅气的讲师!";
				break;
			case '新闻':
				// 
				$msg = [
					[
						'title'=>'全八维最有气质,长得最英俊的老湿',
						'description'=>'想都不用想，就是1505c的讲师',
						'picurl'=>'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1502937497&di=7aacb7a183dfeb4935272f2084730ffc&imgtype=jpg&er=1&src=http%3A%2F%2Fs4.sinaimg.cn%2Forignal%2F4a86f72a59d6bf452dcb3',
						'url'=>'http://pcedu.pconline.com.cn/964/9643082.html'
					],
				];
				break;
			case '个人信息':
				$msg = $this->getUserInfoById($toUser);
				$msg = "你的昵称为: ".$msg->nickname;
				break;
			default:
				// 正则匹配
			    if(preg_match("/^测八字#(.*)/", $message,$match)){
			    	// 进行接口的访问
			    	$msg = Util::getFortune($match[1]);
			    }
			    if(preg_match("/^天气#(.*)$/",$message,$match)){
			    	$city = trim($match[1]);
			    	if($city){
			    		$msg = Util::getWeather($city);
			    	}
			    }
				
		}
		return $this->reponseText($fromUser,$toUser,$msg);
	}
	// 发送图片信息
	public function handleImage($dataObj){
		$msg = $dataObj->PicUrl;
		return $this->reponseText($dataObj->ToUserName,$dataObj->FromUserName,$msg);
	}

	public function handleVoice($dataObj){
		$msg = $dataObj->MediaId;
		return $this->reponseText($dataObj->ToUserName,$dataObj->FromUserName,$msg);
	}

	public function handleLocation($dataObj){
		$msg = "你的地址为: 经度为".$dataObj->Location_Y.",纬度为".$dataObj->Location_X;
		return $this->reponseText($dataObj->ToUserName,$dataObj->FromUserName,$msg);
	}
	public function handleLink($dataObj){
	$msg = "发送的链接为: ".$dataObj->Url;
	return $this->reponseText($dataObj->ToUserName,$dataObj->FromUserName,$msg);
	}


	public function getJsApi(){
		$jsapi = '';
		if(file_exists('./jsapi') && time() - filemtime("./jsapi")<7200){
			$jsapi = file_get_contents("./jsapi");
		}else{
			$accesstoken = $this->getAccessToken();
			$jsapi = Http::getInfoByUrl("https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$accesstoken->access_token."&type=jsapi");
			file_put_contents("./jsapi", $jsapi);
		}
		$jsapi = json_decode($jsapi);
		return $jsapi;
	}

	public function createLoginUrl(){
  		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=".urlencode(REDIRECT_URL)."&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
  		return $url;
	}

	public function getWebAccess($code){
		  	$filename = "./webaccess";
		  	// 2. 通过code获取用户的accesstoken
		  	// if(file_exists($filename) && time()-filemtime($filename) < 7200){
		  	// 	$accessTokenStr = file_get_contents($filename);
		  	// }else{
		  		$accessTokenStr = Http::getInfoByUrl("https://api.weixin.qq.com/sns/oauth2/access_token?appid=".APPID."&secret=".APPSECRET."&code=".$code."&grant_type=authorization_code");
		  		//file_put_contents($filename, $accessTokenStr);
		  	//}
		  	$accesstoken = json_decode($accessTokenStr,true);
		  	return $accesstoken;
	}

}