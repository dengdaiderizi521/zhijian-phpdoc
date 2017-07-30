<?php
/**
  * Copyright © 2017, www.zhijian.cc 北京至简未来科技有限公司版权所有
  * 至简PHP项目文档生成器
  * PHP框架版本：至简PHP开源框架初学版
  * @link https://www.zhijian.cc
  * @date 2017-07-31
  * @version 4.0
  */
class Home extends BaseController{

	function __construct(){
		$this->check_login();
	}

	/**
	 * 登陆验证
	 */
	private function check_login(){
		session_start();
		if(empty($_SESSION['login']) && ( empty($_GET['m']) || $_GET['m'] != 'login')){
			$this->view('login');die;
		}
	}

	/**
	 * 登陆
	 */
	function login(){
		header('content-type:application/json;charset=utf-8');
		require_once BASEPATH.'config/conf.php';
		if(empty($_SESSION['login']) && ! empty($_POST['username']) && ! empty($_POST['password'])){
			if( ! empty($user[$_POST['username']]) && md5($_POST['password']) == $user[$_POST['username']]){
				$_SESSION['login'] = 1;
				echo json_encode(array('code'=>1));
			}else{
				echo json_encode(array('code'=>2, 'msg'=>'用户名或密码错误'));
			}
		}else{
			echo json_encode(array('code'=>2, 'msg'=>'用户名或密码错误'));
		}
	}
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
		if( ! empty($_POST['private'])){
			$obj->stat_private = true;
		}
		if( ! empty($_POST['protected'])){
			$obj->stat_protected = true;
		}
		if( ! empty($_POST['check_login'])){
			$obj->check_login = true;
		}
		if( ! empty($_POST['debug']) && ! empty($_POST['debug_form_url']) && ! empty($_POST['debug_sign'])){
			$obj->debug = true;
			$obj->debug_form_url = $_POST['debug_form_url'];
			$obj->debug_sign = $_POST['debug_sign'];
		}
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

		$conf['login_path'] = BASEPATH.'libraries/doc/temp/login.php';

		foreach ($conf as $k => $v) {
			$this->set($k, $v);
		}

	}
}
?>