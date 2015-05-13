<?php
/**
  * Copyright (c) 2015, www.php32.com Inc. All Rights Reserved
  * 至简PHP开源框架
  * 版本：至简初学版
  * 官方网站：http://www.php32.com
  * 日期：2015-05-01
  */
/**
  * 定义网站字符集
  */
	header('content-type:text/html;charset=utf-8');


/**
  *---------------------------------------------------------------
  * 定义网站时区
  *---------------------------------------------------------------
  */
	date_default_timezone_set('PRC');

/*
 * --------------------------------------------------------------------
 * 定义默认控制器和方法
 * --------------------------------------------------------------------
 */
	$defaultControllerName = 'home';
	$defaultMethodName = 'index';
	
/*
 * -------------------------------------------------------------------
 *  设置路径常量（根目录以及MVC目录）
 * -------------------------------------------------------------------
 */
	//项目根目录
	define('BASEPATH', str_replace("\\", "/",dirname(__FILE__)).'/');
	//控制器文件存放目录
	define('CONTROLLERDIR', BASEPATH.'controller/');
	//模型文件存放目录
	define('MODELDIR', BASEPATH.'model/');
	//视图文件存放目录
	define('VIEWDIR', BASEPATH.'view/');
	//PHP文件扩展名
	define('PHPEXT', '.php');
	//定义视图文件扩展名【可随意定义】
	define('VIEWEXT', '.php');
/*
 * --------------------------------------------------------------------
 * 载入核心文件
 * --------------------------------------------------------------------
 */
	require_once CONTROLLERDIR.'BaseController.php';
	require_once MODELDIR.'BaseModel.php';

/*
 * --------------------------------------------------------------------
 * 获取URL中传递的控制器名和方法名
 * --------------------------------------------------------------------
 */
	//获取URL中传递的控制器名和方法名，如果没有传递则使用设置的缺省值
	$controllerName = !empty($_GET['c'])?$_GET['c']:$defaultControllerName;
	$methodName = !empty($_GET['m'])?$_GET['m']:$defaultMethodName;	

	//转换控制器名和方法名,确保路径正确
	$controllerName = ucfirst(strtolower($controllerName));
	$methodName = ucfirst(strtolower($methodName));

/*
 * --------------------------------------------------------------------
 * 执行请求
 * --------------------------------------------------------------------
 */
	//判断控制器文件是否存在
	if(!file_exists(CONTROLLERDIR.$controllerName.PHPEXT)){
		die("您要访问的文件不存在：".CONTROLLERDIR.$controllerName.PHPEXT);
	}

	//载入控制器
	require_once CONTROLLERDIR.$controllerName.PHPEXT;

	//实例化控制器，获取控制器对象
	$obj = new $controllerName();

	//判断控制器是否存在要访问的方法
	if(!method_exists($obj , $methodName)){
		die("您要访问的页面不存在");
	}
	
	//执行页面
	$obj -> $methodName();