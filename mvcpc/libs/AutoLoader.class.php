<?php

class AutoLoader{

	public function __construct(){
		spl_autoload_register(array($this,"load"));  //自动加载 等同于__autoload
	}

	public function load($className){

		$className = str_replace("\\", DS, $className);
		$className.='.class.php';
		try{
		if(file_exists($className)){ //规定要检查的路径
			include $className;
		}else{
			throw(new Exception('对不起，我们这里没有你说的这个mm'));
			//exit;
		}
	}catch(Exception $e){
		echo $e->getMessage();
		
	}
	}
}