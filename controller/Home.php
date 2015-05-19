<?php
/**
  * Copyright (c) 2015, www.php32.com Inc. All Rights Reserved
  * 至简PHP项目文档生成器
  * PHP框架版本：至简PHP开源框架初学版
  * @link http://www.php32.com/doc
  * @date 2015-05-15
  * @version 2.0
  */
class Home extends BaseController{

	/**
	 * @name 至简PHPDOC首页
	 */
	function index  (){
		//载入配置文件
		$this->_init_config();

		//载入模版
		$this->view('home');
	}

	/**
	 * @name 生成DOC文件主方法
	 */
	function create_doc(){
		ignore_user_abort();
		set_time_limit(0);
		if(empty($_POST) || empty($_POST['doc_url']) || empty($_POST['base_url']) || empty($_POST['doc_path']) || empty($_POST['code_path'])){
			echo json_encode(array('msg'=>'参数错误'));die;
		}
		//载入类库主文件
		require_once BASEPATH.'libraries/doc/doc.php';
		$obj = new Doc();
		$obj->startTask($_POST['doc_url'], $_POST['base_url'], $_POST['doc_path'], $_POST['code_path']);
	}

	/**
	 * @name 读取生成日志
	 */
	final function task_log(){

		if(empty($_POST) || empty($_POST['doc_path'])){
			echo json_encode(array('msg'=>'参数错误'));die;
		}
		require_once BASEPATH.'libraries/doc/doc.php';
		$obj = new Doc();
		echo $obj->getTaskLog($_POST['doc_path']);
	}

	/**
	 * @name 初始化配置
	 */
	private function _init_config(){
		//载入配置文件
		require_once BASEPATH.'config/conf.php';

		if(empty($conf['base_url'])){
			$conf['base_url'] = 'http://'.$_SERVER['HTTP_HOST'];
		}

		if(empty($conf['doc_url'])){
			$conf['doc_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/doc';
		}

		if(empty($conf['doc_path'])){
			$conf['doc_path'] = BASEPATH.'doc/';
		}

		if(empty($conf['code_path'])){
			$conf['code_path'] = BASEPATH.'controller/';
		}

		foreach ($conf as $k => $v) {
			$this->set($k, $v);
		}

	}
}
?>