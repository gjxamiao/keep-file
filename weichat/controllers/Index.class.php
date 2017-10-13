<?php

namespace controllers;
use libs\Controller;
use models\Joke;
use libs\Page;
class  Index extends Controller {

	public function index(){

		// $model = new Joke;

		// $data = $model->select(); 
		// $page = new Page(count($data),4);
		// // 进行实际的数据提取
		// $data = $model->limit($page->pageSize,$page->limit())->select();
		// $pageStr = $page->show();
		// $this->assign(['data'=>$data,'pagestr'=>$pageStr]);
		// $this->display();
	}


	public function add(){
		// 判断方式是不是post方式
		if($_SERVER['REQUEST_METHOD'] == "POST"){
			// 进行处理
			$data = $_POST['joke'];
			$data['ctime'] = time();
			// 进行插入
			if((new Joke)->insert($data)){
				echo "<script>alert('添加成功');location.href='".__ROOT__."Index/index';</script>";
			}else{
				echo "error";
			}

		}else{
			$this->display();
		}
	}

}