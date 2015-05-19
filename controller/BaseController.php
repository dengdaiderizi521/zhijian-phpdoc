<?php
/**
  * Copyright (c) 2015, www.php32.com Inc. All Rights Reserved
  * 至简PHP开源框架
  * 基础控制器，包含设置模版变量，载入模版，调取模型
  * @version 至简初学版
  * @link http://www.php32.com
  * @date 2015-05-01
  * 
  */
class BaseController{

	private $viewVariable = array(); //定义类属性，用来保存视图变量

	/**
	  * @name  设置需要抛到模版上的变量
	  *	@param string $name 视图接收时的变量名
	  *	@param string $value 视图变量的值
	  */
	protected function set($name,$value){
		$this->viewVariable[$name] = $value;
	}

	/**
	  * @name  加载视图
	  *	@param $fileName 视图文件名不包含文件扩展名，可包含路径
	  */
	protected function view($fileName){
		foreach ($this->viewVariable as $k => $v) {
			$$k = $v;
		}
		//判断控制器文件是否存在
		if(!file_exists(VIEWDIR.$fileName.VIEWEXT)){
			die("视图文件不存在：".VIEWDIR.$fileName.VIEWEXT);
		}
		require_once VIEWDIR.$fileName.VIEWEXT;
	}

	/**
	  * @name  加载模型
	  *	@param $modelName 模型文件名不包含文件扩展名，可包含路径
	  * @return object 返回实例化对象
	  */
	protected function getModel($modelName){
		$model = $modelName;
		if(isset($this->$model)){
			return $this->$model;
		}
		$modelName = ucfirst(strtolower($modelName)).'Model';

		//判断控制器文件是否存在
		if(!file_exists(MODELDIR.$modelName.PHPEXT)){
			die("模型文件不存在：".MODELDIR.$modelName.PHPEXT);
		}
		
		require_once MODELDIR.$modelName.PHPEXT;
		return $this->$model = new $modelName();
	}
}
?>