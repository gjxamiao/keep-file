<?php
namespace libs;
class Upload extends SplFileInfo{
	// 图像上传类 
    // 文件类型 文件大小  文件上传目录  文件的命名方式

    protected $ext='';
    protected $size;
    protected $uploaddir = './Upload';
    protected $fileext='';
    protected $unique = true;
    protected $rule = ''; // 文件的命名规则

    public function __construct($config=[]){


    }

    // 单文件上传
    public function uploadFile(){


    }

    // 多文件上传
    public function uploadMutilFile(){

    }

    // 文件后缀的判断
    public function 
    // 通过外部调用set 和 get获取值
    public function __set($key,$value){
    	$this->$key = $value;
    }

    public function __get($key){
    	if(isset($this->$key)){
    		return $this->$key;
    	}
    	return null;
    }

}