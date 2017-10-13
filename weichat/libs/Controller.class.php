<?php

namespace libs;

class Controller {
	public $viewObject;
	public function __construct(){
		if(method_exists($this, "init")){
			$this->init();
		}
		$this->viewObject = new View;
	}

	public function display($fileName=''){
	
		// 获取得到模板路径
		if(empty($fileName)){ //是否有默认文件

			$c = Router::getInstance()->controller; //获取控制器
			$a = Router::getInstance()->action; //获取方法

			$fileName = strtolower($c).DS.strtolower($a).'.php'; //默认当前控制器文件夹下面的方法.html
		}
		
		$this->viewObject->display($fileName);  //文件百人百存在
	}

	public function assign($key,$value=''){
		$this->viewObject->assign($key,$value);
	}



}