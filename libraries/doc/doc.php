<?php
/**
  * Copyright (c) 2015, www.php32.com Inc. All Rights Reserved
  * 至简PHP文档生成器核心类库，此类库可以单独移出整合到项目中使用
  * PHP框架版本：至简PHP开源框架初学版
  * 官方网站：http://www.php32.com/doc
  * 日期：2015-05-01
  */
class Doc{

	private $doc_url;
	private $base_url;
	private $doc_path;
	private $code_path;
	private $doc;

	/**
	 * 开始创建DOC文件
	 * @param  string $base_url  基础URL
	 * @param  string $doc_path  生成文档的路径
	 * @param  string $code_path 项目路径
	 * @return bool
	 */
	function startTask($doc_url, $base_url, $doc_path, $code_path){
		if(empty($doc_url) || empty($doc_path) || empty($doc_path) || empty($code_path)){
			return '参数错误';
		}

		if( ! is_dir($code_path)){
			return '项目路径错误';
		}

		if( ! is_readable($code_path)){
			return '项目路径不可读';
		}

		if( ! is_dir($doc_path)){
			if( ! is_writable(dirname($doc_path))){
				return 'DOC目录不可写';
			}
			if( ! mkdir($path, 0777, true)){
				return '创建DOC目录失败';
			}
		}elseif( ! is_writable($doc_path)){
			return 'DOC目录不可写';
		}

		$this->doc_url   = $doc_url;
		$this->base_url  = $base_url;
		$this->doc_path  = $doc_path;
		$this->code_path = $code_path;

		//删除文档目录下所有文件和文件夹
		$this->del_doc_dir($doc_path);
		mkdir($this->doc_path.'_zhijianlog');
		file_put_contents($this->doc_path.'_zhijianlog/task_log.txt', '开始生成<br>');
		//读取项目目录，并生成项目文件
		$this->doc = $this->read_code_dir();

		$this->create_doc_html();
		//复制静态文件
		$this->copy_doc_static_dir($doc_path.'/static/' );

		file_put_contents($this->doc_path.'_zhijianlog/task_log.txt', file_get_contents($this->doc_path.'_zhijianlog/task_log.txt')."执行完毕<br>");
		return true;
	}

	/**
	 * 获取任务日志
	 * @return string
	 */
	function getTaskLog($doc_path){
		return file_get_contents($doc_path.'_zhijianlog/task_log.txt');
	}

	private function create_doc_html(){
		$this->read_doc_file();
		$this->create_doc_index();
	}

	private function create_doc_html_code($file, $path){
		$file_html  = file_get_contents(dirname(__FILE__).'/temp/head.html');
		$file_html  = str_replace('{doc_url}', $this->doc_url, $file_html);
		$file_html  = str_replace('{doc_title}', $path.$file, $file_html);
		$file_html .= '<div id="page">';
		$file_html .= file_get_contents($this->doc_path.$path.$file);
		$file_html .= '</div>';
		$file_html .= '<nav id="menu" class="">';
		$file_html .= $this->get_doc_nav(NULL, $this->doc_url.$path.$file);
		$file_html .= '</nav>';
		$file_html .= file_get_contents(dirname(__FILE__).'/temp/bottom.html');
		file_put_contents($this->doc_path.$path.$file, $file_html);
	}

	function create_doc_index(){
		$file_html  = file_get_contents(dirname(__FILE__).'/temp/head.html');
		$file_html  = str_replace('{doc_url}', $this->doc_url, $file_html);
		$file_html  = str_replace('{doc_title}', '文档首页-至简PHP文档生成器', $file_html);
		$file_html .= '<div id="page">';
		$file_html .= file_get_contents(dirname(__FILE__).'/temp/index.html');
		$file_html .= '</div>';
		$file_html .= '<nav id="menu" class="">';
		$file_html .= $this->get_doc_nav(NULL, 'index');
		$file_html .= '</nav>';
		$file_html .= file_get_contents(dirname(__FILE__).'/temp/bottom.html');
		file_put_contents($this->doc_path.'/index.html', $file_html);
	}

	private function read_doc_file($path = '/'){
		$dir = dir($this->doc_path.$path);
		while($file = $dir->read())
		{
			if((is_dir("$this->doc_path$path$file")) AND ($file != ".") AND ($file != "..")) 
			{
				$tmp = $path.$file.'/';
				$this->read_doc_file($tmp);
			}
			else {
				if($file != '.' && $file != '..' && preg_match('/^[\.].*$/', $file) == 0){
					if($path != '/_zhijianlog/'){
						$this->create_doc_html_code($file, $path);
					}
				}
			}
		}
		$dir->close();
	}

	private function get_doc_nav($doc = NULL, $url){

		if($doc == NULL){
			$doc = $this->doc;
		}
		if( ! is_array($doc)){
			return '';
		}
		$nav_html = '<ul  class="" >'."\n";
		foreach ($doc as $k => $v) {
			if(is_int($k)){
				$nav_html .= '<li '.($url==$v['path'] ? ' class="Selected"' : '').'><span><a href="'.$v['path'].'">'.$v['class']."</a></span>\n";
				$nav_html .= '<ul>';
				foreach ($v['function'] as $key => $val) {
					$nav_html .= '<li><a href="'.$v['path'].'#'.$val.'_function">'.$val.'</a></li>'."\n";
				}
				$nav_html .= '</ul></li>';
			}else{
				$nav_html .= '<li><span>'.$k."</span>\n";
				$nav_html .= $this->get_doc_nav($v, $url);
				$nav_html .= '</li>';
			}
		}
		$nav_html .= '</ul>'."\n";
		return $nav_html;
	}

	/**
	 * 读取目录，并根据项目目录结构对应生成文本
	 * @param  string $relative_path  相对路径
	 * @return array  $doc 		 项目目录
	 */
	private function read_code_dir($relative_path = '/'){
		$doc = array();
		$dir = dir($this->code_path.$relative_path);
		$i = 0; 
		while($file = $dir->read())
		{ 
			if((is_dir("$this->code_path$relative_path$file")) AND ($file != ".") AND ($file != "..")) 
			{
				$tmp = $relative_path.$file.'/';
				$doc[$file] = $this->read_code_dir($tmp);
			}
			else {
				if($file != '.' && $file != '..' && preg_match('/^[\.].*$/', $file) == 0){
					$tmp = $this->create_doc_file($file, $relative_path);
					if( ! empty($tmp)){
						$doc[$i] = $tmp;
						$i++;
					}
				}
			}
		}
		$dir->close();
		return $doc;
	}

	/**
	 * 复制静态文件到文档目录
	 * @param  string $dst 文档目录
	 * @return NULL
	 */
	private function copy_doc_static_dir($dst, $src = NULL){
		$src = $src === NULL ? dirname(__FILE__).'/static' : $src;
		$dir = dir($src);
	    @mkdir($dst, 0777, true);
	    while(false !== ( $file = $dir->read()) ) {
	        if (( $file != '.' ) && ( $file != '..' )) {
	            if ( is_dir($src. '/' .$file) ) {
	                $this->copy_doc_static_dir($dst.'/'. $file, $src . '/' . $file);
	            }
	            else {
	            	if(preg_match('/^[\.].*$/', $file) == 0){
	                	copy($src . '/' . $file, $dst . '/' . $file);
	            	}
	            }
	        }
	    }
	    $dir->close();
	}

	/**
	 * 删除文档下的目录和文件
	 * @param  string $path 需要删除的路径
	 * @return NULL
	 */
	private function del_doc_dir($path){
		
		//先删除目录下的文件：
		$dir = dir($path);
		while ($file = $dir->read()) {

			if($file != '.' && $file != '..') {

				$fullpath = $path.'/'.$file;

				if(!is_dir($fullpath)) {
					unlink($fullpath);
				} else {
					$this->del_doc_dir($fullpath);
					rmdir($fullpath);
				}
			}
		}

		$dir->close();
	}


	private function create_doc_file($file_path, $relative_path){
		$doc_file_log = str_replace('//', '/', $this->doc_path.$relative_path.$file_path);
		file_put_contents($this->doc_path.'_zhijianlog/task_log.txt', file_get_contents($this->doc_path.'_zhijianlog/task_log.txt')."正在生成：".$doc_file_log."<br>");
		$str = file_get_contents($this->code_path.$relative_path.'/'.$file_path);
		$str = explode("\n", $str);
		
		foreach ($str as $k => $v) {
			if(stristr($v, '*') === false && stristr($v, 'function ') === false && stristr($v, 'class ') === false){
				unset($str[$k]);
			}
		}
		if(empty($str)){
			return false;
		}
		$i = 0;
		$arr = array();
		foreach ($str as $k => $v) {
			$s = ltrim(ltrim(trim($v), '*'), '/');
			$t = trim($s);
			if(strlen($t) > 1 && $t[0] == '@'){
				$s = ' '.trim($s);
			}
			if( ! empty($t) && $s != '**'){

				if(preg_match('/^\s*class.*$/', $s) > 0){
					if(empty($doc['class'])){
						$class['class'] = ! empty($arr) ? $arr : array();
						$name = preg_replace('/^.*class\s+([A-Za-z0-9_]*)(([\s]+)|\{).*/', '$1', $s);
						$doc['class'] = $name;
						$doc['path'] = $this->doc_url.$relative_path.strtolower($name).'.html';
						$doc['url'] = $this->base_url.$relative_path.$name;
					}
				}elseif(stristr($s, 'function ') !== false && stristr($s, 'private ') === false){
					$name = preg_replace('/^.*function\s+([^\(]*).*/', '$1', $s);
					if(stristr($name, ' ') === false){
						$doc['function'][] = $name;
						$arr['name'] = $name;
						if(stristr($s, 'protected ') !== false){
							$arr[] = ' 访问权限：受保护的';
							$arr['public'] = 0;
						}else{
							$arr[] = ' 访问权限：公共的';
							$arr['public'] = 1;
						}
						$function[] = $arr;
					}
				}else{
					$arr[] = $s;
				}
			}elseif($s=='**'){
				$arr = array();
			}
		}
		$file_html  = '<h4>模块：'.$relative_path.$doc['class']."</h4>\n";
		if( ! empty($class['class'])){
			$file_html .= '<pre>';
		}
		foreach ($class['class'] as $k => $v) {
			if($k !== 'class'){
				$file_html .= $v."\n";
			}
		}
		if( ! empty($class['class'])){
			$file_html .= '</pre>';
		}
		foreach ($function as $k => $v) {
			$file_html .= '<div id="'.$v['name'].'_function">方法名：'.$v['name']."</div>";
			$file_html .= '<pre>';
			
			foreach ($v as $key => $val) {
				if($key !== 'public' && $key !== 'name'){
					$file_html .= $val."\n";
				}
			}

			if($v['public'] === 1){
				$file_html .= ' URL:'.$this->base_url.$relative_path.$doc['class'].'/'.$v['name'];
			}
			$file_html .= '</pre>';
		}
		if( ! is_dir($this->doc_path.'/'.$relative_path)){
			mkdir($this->doc_path.'/'.$relative_path, 0777, true);
		}
		file_put_contents($this->doc_path.'/'.$relative_path.strtolower(preg_replace('/^(?:.*\/)?([^.]+).*$/', '$1.html', basename($file_path))), $file_html);
		return $doc;
	}
}