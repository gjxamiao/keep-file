<?php

// 使用C读取配置 
function C($key=''){
	if($key){
		return libs\Configure::getConfigs($key);
	}
	return libs\Configure::getConfigs();
}
