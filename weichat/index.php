<?php
	
	define('APP_PATH',realpath(dirname(__FILE__))); //项目定义的位置

	define('DS',DIRECTORY_SEPARATOR); //适应windows 和linux的斜杠
	define('LIBS',APP_PATH.DS.'libs'.DS); //主配置文件夹 核心
	define('VIEWS',APP_PATH.DS.'views'.DS); //视图
	define('RUNTIME',APP_PATH.DS.'runtime'.DS); // 缓存文件
	define('FUNDIR',APP_PATH.DS.'functions'.DS);
	define('__ROOT__',str_replace($_SERVER['DOCUMENT_ROOT'],'',APP_PATH).DS);
	define('__STATIC__',__ROOT__.'static'.DS);


	require(LIBS."Error.class.php"); // 错误接管

	//register_shutdown_function("\libs\Error::stopError");

	//set_error_handler("\libs\Error::userError");
	//set_exception_handler("\libs\Error::exceptionError");

	require(LIBS."AutoLoader.class.php"); // 实现自动加载
	//加载配置
	#print_r(APP_PATH);die;
	require(APP_PATH.DS.'config'.DS.'constance.php');
	require(FUNDIR.'common.php');
		
// echo $a
	//trigger_error('我的错啊。。。', E_USER_NOTICE);
	// echo $a;

	//var_dump($_SERVER['REQUEST_URI']);
	//var_dump($_SERVER['PATH_INFO']);
	//$urlInfo = parse_url($_SERVER['REQUEST_URI']);
	//var_dump($urlInfo);
	//exit;
	// 加载配置
	//var_dump($_SERVER['PATH_INFO']);
	if(new AutoLoader()){
		$router = libs\Router::getInstance(); //调用路由类 //单例模式的调用
		$controller = $router->getCon(); // 获取控制器
		$action = $router->getAc(); // 获取方法
		// 纯静态
		// 1. 判断一下对应的文件夹下是否存在对应的静态文件，存在的话直接读取并输出

		if($obj = new $controller){
			$obj->$action();
		}else{
			throw(new Exception('对不起，我们这里没有你说的这个mm'));
		}

		
	}else{
		echo "MVC文件加载错误!";
	}