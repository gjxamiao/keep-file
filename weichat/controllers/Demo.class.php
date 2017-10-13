<?php
namespace controllers;
use libs\Controller;
use models\Joke;
use libs\Configure;
class Demo extends Controller{

	public function show(){
		$model = new Joke();
		$flag = $model->where(['id'=>2])->save(['username'=>'同耗也湿了']);
		var_dump($flag);
	}

	public function config(){
		$data = C('weixin');
		var_dump($data);
	}
}