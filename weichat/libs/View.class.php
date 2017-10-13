<?php

namespace libs;

class View {

	// 属性
	public $templateFile; // 默认为当前控制器文件夹下方法名.html
	public $data=array(); 
	private $T_P=array();
	private $T_R=array();

	public function __construct(){
		$this->T_P[]="#\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(\['.*'\])*)\}#"; 
		$this->T_P[]="#\{(loop|foreach) \\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}#i"; 
		$this->T_P[]="#\{\/(loop|foreach|if)\}#i"; 
		//$this->T_P[]="#\{([k|v](\['.*'\])*)\}#"; // key 和 value
		$this->T_P[]="#\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(.*?))\}#"; // 匹配常量
		$this->T_P[]="#\{if (.* ?)\}#i"; // if
		$this->T_P[]="#\{(else if|elseif) (.* ?)\}#i"; // elseif
		$this->T_P[]="#\{else\}#i"; // 匹配else
		$this->T_P[]="#\{(\#|\* )(.* ?)(\#|\* )\}#";  // 注释
		$this->T_R[]="<?php echo \$\\1;?>"; 
		$this->T_R[]="<?php foreach((array)\$\\2 as \$k=>\$v){ ?>"; 
		$this->T_R[]="<?php } ?>"; 
		/*$this->T_R[]="<?php echo \$\\1;?>"; */
		$this->T_R[]="<?php echo \\1;?>";
		$this->T_R[]="<?php if(\\1){ ?>"; 
		$this->T_R[]="<?php }else if(\\2){ ?>"; 
		$this->T_R[]="<?php }else{ ?>"; 
		$this->T_R[]=""; 

		if(Configure::getConfigs('templateParseStr')){
			$templateParseStr = Configure::getConfigs('templateParseStr');
			foreach((array)$templateParseStr as $key=>$value){
				define($key,$value);
			}
		}

	}
 	public function assign($key,$value=''){
 		if(is_array($key)){
 			$this->data = array_merge($this->data,$key);
 		}else{
 			$this->data[$key] = $value;
 		}
	}
	// 可以缓存
	public function display($templateFile=''){
		$filename = md5($templateFile);
		$cacheFile = RUNTIME.$filename.'.php.html';
		if(C('enableCacheFile') && file_exists($cacheFile) && time()-filemtime($cacheFile) < C('cacheLifeTime')){
			echo file_get_contents($cacheFile);
		}else{
			extract($this->data); //打散函数
			$fileContent = file_get_contents(VIEWS.$templateFile);
			$file = $this->parseTemplate($fileContent,$filename);
			//var_dump(C('enableCacheFile'));
			//exit;
			if(C('enableCacheFile')){
				ob_start();
				include $file;
				$data = ob_get_clean();
				file_put_contents($cacheFile, $data);
				echo $data;
			}else{
				include $file;
			}
			
		}
	}

	public function parseTemplate($content,$filename){
		$filename = RUNTIME.$filename.'.php';
			// 解析文件
			// 其他规则放到前面
			$content = preg_replace($this->T_P,$this->T_R,$content);
			file_put_contents($filename,$content);
		
		return $filename;
	}


}