<?php
/**
  * Copyright © 2017, www.zhijian.cc 北京至简未来科技有限公司版权所有
  * 至简PHP项目文档生成器
  * PHP框架版本：至简PHP开源框架初学版
  * @link https://www.zhijian.cc
  * @date 2017-07-31
  * @version 4.0
  */
class Doc{

	private $doc_url;
	private $base_url;
	private $doc_path;
	private $code_path;
	private $doc;

	public $stat_private   = false;
	public $stat_protected = false;
	public $debug		   = false;
	public $debug_form_url = false;

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
		$this->doc_docs_url   = $doc_url.'/docs/';
		$this->base_url  = $base_url;
		$this->doc_path  = $doc_path;
		$this->doc_docs_path  = $doc_path.'/docs/';
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
		return file_get_contents($doc_path.'_zhijianlog/task_log.txt');
	}

	/**
	 * 创建文档HTML文件
	 */
	private function create_doc_html(){
		$this->read_doc_file();
		$this->create_doc_index();
	}

	/**
	 * 生成文档的html代码
	 * @param  string $file 文件名
	 * @param  string $path 文件路径
	 * @return NULL
	 */
	private function create_doc_html_code($file, $path){
		if(preg_match('/^\/?params\/.*$/', $path) > 0){
			return true;
		}
		$file_html  = file_get_contents(__DIR__.'/temp/head.html');
		$file_html  = str_replace('{doc_url}', $this->doc_url, $file_html);
		$file_html  = str_replace('{doc_title}', $path.$file, $file_html);
		$file_html .= '<div id="page">';
		$file_html .= file_get_contents($this->doc_path.$path.$file);
		$file_html .= '</div>';
		$file_html .= '<nav id="menu" class="">';
		$file_html .= $this->get_doc_nav(NULL, $this->doc_url.$path.$file);
		$file_html .= '</nav>';
		$file_html .= file_get_contents(__DIR__.'/temp/bottom.html');
		if( ! empty($this->check_login)){
			$file_html = '<?php include_once "'.$this->doc_path.'/login.php";?>'.$file_html;
		}
		file_put_contents($this->doc_path.$path.$file, $file_html);
	}

	/**
	 * 创建文档首页
	 * @return NULL
	 */
	private function create_doc_index(){
		$file_html  = file_get_contents(__DIR__.'/temp/head.html');
		$file_html  = str_replace('{doc_url}', $this->doc_url, $file_html);
		$file_html  = str_replace('{doc_title}', '文档首页-至简PHP文档生成器', $file_html);
		$file_html .= '<div id="page">';
		$file_html .= file_get_contents(__DIR__.'/temp/index.html');
		$file_html .= '</div>';
		$file_html .= '<nav id="menu" class="">';
		$file_html .= $this->get_doc_nav(NULL, 'index');
		$file_html .= '</nav>';
		$file_html .= file_get_contents(__DIR__.'/temp/bottom.html');

		if( ! empty($this->check_login)){
			copy(__DIR__.'/temp/login.php', $this->doc_path.'/login.php');
			$file_html = '<?php include_once "'.$this->doc_path.'/login.php";?>'.$file_html;
		}
		$config['doc_url'] = $this->doc_url;
		file_put_contents($this->doc_path.'/index'.$this->ext, $file_html);
		if( ! empty($this->debug)){
			copy(__DIR__.'/temp/debug.php', $this->doc_path.'/debug.php');
			copy(__DIR__.'/temp/form.php', $this->doc_path.'/form.php');
			$config['debug_form_url'] = $this->debug_form_url;
			$config['debug_sign'] = $this->debug_sign;
			
		}
		if( ! empty($this->check_login) ||  ! empty($this->debug)){
			$this->write_file($this->doc_path.'/config.php', '$config = '.var_export($config, true).";\n");
		}
	}

	/**
	 * 读取文档文件
	 * @param  string $path 文件路径
	 */
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

	/**
	 * 获取文档导航树
	 * @param  string $doc 文档路径
	 * @param  string $url 文档的访问地址
	 * @return string      生成的HTML代码
	 */
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
				$url = 'http://'.str_replace(array('http://', '//'), array('', '/'), $url);
				$v['path'] = 'http://'.str_replace(array('http://', '//'), array('', '/'), $v['path']);
				$nav_html .= '<li '.($url==$v['path'] ? ' class="Selected"' : '').'><span><a href="'.$v['path'].($url==$v['path'] ? '#doc_home' : '').'">'.$v['class']."</a></span>\n";
				$nav_html .= '<ul>';
				foreach ($v['function'] as $key => $val) {
					$nav_html .= '<li><a href="'.$v['path'].'#'.$val['ename'].'_function">'.$val['name'].'</a></li>'."\n";
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
		$src = $src === NULL ? __DIR__.'/static' : $src;
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


	/**
	 * 创建文档文件
	 * @param  string $file_path     文件名
	 * @param  string $relative_path 子集目录
	 * @return array                 导航数组
	 */
	private function create_doc_file($file_path, $relative_path){
		if( ! empty($this->check_login)){
			$ext = '.php';
		}else{
			$ext = '.html';
		}
		$this->ext = $ext;
		$doc_file_log 			= str_replace('//', '/', $this->doc_docs_path.$relative_path.$file_path);
		file_put_contents($this->doc_path.'_zhijianlog/task_log.txt', file_get_contents($this->doc_path.'_zhijianlog/task_log.txt')."正在生成：".$doc_file_log."<br>");
		$php_code_arr 			= file($this->code_path.$relative_path.'/'.$file_path);
		
		$code_arr 				= array();
		$comment_start 			= 0; //用于标记注释开始
		$class_start 			= 0; //用于标记类是否开始
		$function_start			= 0;
		$class_count			= 0;
		$function_count			= 1;
		$c_left_brace_count 	= 0; //CLASS 左大括号的数量
		$c_right_brace_count 	= 0; //CALSS 右大括号的数量

		$f_left_brace_count 	= 0; //FUNCTION 左大括号的数量
		$f_right_brace_count 	= 0;//FUNCTION 右大括号的数量

		$first_class_name 		= '';

		$file_html 				= '';
		foreach ($php_code_arr as $k => $v) {
			//判断标识注释块开始
			if(preg_match('/\/\*/', $v) > 0){
				$code_arr = array();
				$comment_start = 1;
			}

			if($comment_start === 1){
				$code_arr[] = $v;
			}

			//判断标识注释块结束
			if(preg_match('/\*\//', $v) > 0 && preg_match('/\/\*/', $v) === 0){
				$comment_start = 0;
			}

			//捉对匹配大括号，以判别类或者方法或者函数
			if(preg_match_all('/(\{|\})/', $v) > 0){

				if($comment_start == 0){
					//过滤掉行内注释中的大括号
					$tmp    			 = preg_replace('/^(.*)\/\/.*$/', '$1', $v);

					$left   			 = preg_match_all('/{/', $tmp);
					$right  			 = preg_match_all('/}/', $tmp);

					//统计类中左右大括号数量；用于计算一个类是否结束
					$c_left_brace_count += $left;
					$c_right_brace_count += $right;

					//统计方法中左右大括号数量；用于计算一个方法是否结束
					$f_left_brace_count += $left;
					$f_right_brace_count += $right;

					if($c_left_brace_count > 0 && $c_left_brace_count == $c_right_brace_count){
					 	$class_start = 0;
					 	$function_start	 = 0;
					}

					if($f_left_brace_count > 0 && $f_left_brace_count == $f_right_brace_count && ($f_left_brace_count+$f_right_brace_count) % 2 == 0){
					 	$code_arr = array();
					 	$function_start	 = 0;
					}
					if($function_start == 0){
					 	$f_left_brace_count  = 0;
						$f_right_brace_count = 0;

					}
				}
			}
			if(preg_match('/\s?class\s/', $v) > 0 || preg_match('/\s?function\s/', $v) > 0){
				if($comment_start === 1){
					continue;
				}
				$tmp   = preg_replace('/^(.*)\/\/.*$/', '$1', $v);
				$left  = preg_match_all('/{/', $tmp);
				$right = preg_match_all('/}/', $tmp);
				
				$params = array();
				if( ! empty($code_arr)){
					$this->filter_doc($code_arr);
					if( ! empty($code_arr['params'])){
						$params = $code_arr['params'];
						unset($code_arr['params']);
					}
				}
				if(preg_match('/\s?function\s/', $v) > 0){
					
					if($function_start == 1){
						$f_left_brace_count  += $left;
						$f_right_brace_count += $right;
						continue;
					}else{
						$f_left_brace_count  = $left;
						$f_right_brace_count = $right;
					}

					if(( ! $this->stat_private && preg_match('/private/', $v) > 0) || ( ! $this->stat_protected && preg_match('/protected/', $v) > 0)){
						$function_start = 0;
						$f_left_brace_count  = $left;
						$f_right_brace_count = $right;
						continue;
					}
					$name 		   = trim(preg_replace('/^.*function\s+([^\(]*).*/', '$1', $v));
					if(preg_match('/^__.*$/', $name) > 0){
						continue;
					}
					$function_name = $name;
					if( ! empty($code_arr['name'])){
						$function_name = $code_arr['name'];
						unset($code_arr['name']);
					}

					$fun_arr['name']   = $function_name;
					$fun_arr['ename']  = ( ! empty($class_name) ? $class_name : '').$name;
					$doc['function'][] = $fun_arr;
					$file_html .= '<div class="panel panel-default" id="'.$fun_arr['ename'].'_function"><div class="panel-heading"><h4 class="panel-title"><strong>'.$function_count.'. '.$function_name.'</strong>'.( ! empty($this->debug) && preg_match('/(private)|(protected)/', $v) === 0 ? '<a class="pull-right" target="_debug" href="'.$this->doc_url.'/form.php?class='.$class_name.'&method='.$name.'&relative_path='.$relative_path.'&file='.$relative_path.$file_path.'"><i class="fa fa-wrench"></i>测试</a>' : '').'</h4></div><div class="panel-body">';

					if( ! empty($code_arr['brief'])){
						$file_html .= $code_arr['brief']."\r\n";
						unset($code_arr['brief']);
					}
					if($class_start === 0){
						$file_html .= '<div class="doc_block"><label class="label label-info">属性:</label> 自定义函数</div>';
					}else{
						if(preg_match('/private/', $v) > 0){
							$file_html .= '<div class="doc_block"><label class="label label-info">权限:</label> 私有</div>';
						}elseif(preg_match('/protected/', $v) > 0){
							$file_html .= '<div class="doc_block"><label class="label label-info">权限:</label> 受保护</div>';
						}else{
							$file_html .= '<div class="doc_block"><label class="label label-info">权限:</label> 公共</div>';
						}
					}
					$function_count++;
					if(preg_match('/\s?function\s/', $v) > 0 && preg_match('/(private)|(protected)/', $v) === 0 && ! empty($class_name) && $class_start == 1 && empty($code_arr['url'])){
						$file_html .= '<div class="doc_block"><label class="label label-info">链接:</label> <a target="zjtest" href="'.$this->base_url.$relative_path.$class_name.'/'.$name.'">'.$this->base_url.$relative_path.$class_name.'/'.$name.'</a></div>';
						$param_file_contents['url'] = $this->base_url.$relative_path.$class_name.'/'.$name;
					}elseif( ! empty($code_arr['url_arr']) && preg_match('/(private)|(protected)/', $v) === 0 ){
						$param_file_contents['url'] = $code_arr['url_arr'];
					}
					$function_start = 1;

					if( ! empty($param_file_contents['url']) && ! empty($this->debug)){
						
						$param_file_contents['method'] = empty($code_arr['method_str']) ? 'GET' : $code_arr['method_str'];
						$param_file_contents['params'] = $params;
						$param_file_contents['brief'] = ! empty($code_arr['brief_text']) ? $code_arr['brief_text'] : '';
						$this->write_file($this->doc_path.'/params/'.$relative_path.$file_path, '$params[\''.$class_name.'/'.$name.'\'] = '.var_export($param_file_contents, true).";\n");
					}
				}else{
					$class_name  = trim(preg_replace('/^.*class\s+([A-Za-z0-9_]*)(([\s]+)|\{).*/', '$1', $v));
					$file_html  .= '<div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><strong>模块：'.$relative_path.$class_name.'</strong></h4></div><div class="panel-body">'."\n";

					if( ! empty($code_arr['brief'])){
						$file_html .= $code_arr['brief']."\r\n";
						unset($code_arr['brief']);
					}
					$class_start = 1;
					$c_left_brace_count  = $left;
					$c_right_brace_count = $right;
					if(empty($first_class_name)){
						$first_class_name = $class_name;
					}
					$class_count++;
					$function_count = 1;
				}
				unset($code_arr['method_str']);
				unset($code_arr['brief_text']);
				unset($code_arr['url_arr']);
				if( ! empty($code_arr)){
					foreach ($code_arr as $key => $val) {
						if( ! empty($val)){
							if(is_array($val)){
								$val = implode("\r\n", $val);
							}
							$file_html .= $val."\r\n";
						}
					}
				}
				if( ! empty($file_html) && $function_start == 1){
					$file_html .= '</div></div>';
				}
				$code_arr = array();
			}

			if($class_start == 0 && $class_count > 0){
				$file_html .= '</div></div>';
				$class_count = 0;
				$function_count = 1;
			}
		}
		if(empty($file_html) || empty($doc) || count($doc['function']) < 1){
			return false;
		}
		if( ! is_dir($this->doc_docs_path.'/'.$relative_path)){
			mkdir($this->doc_docs_path.'/'.$relative_path, 0777, true);
		}
		$doc['class'] = $first_class_name;
		$doc['path'] = $this->doc_docs_url.$relative_path.strtolower(preg_replace('/^(?:.*\/)?([^.]+).*$/', '$1'.$ext, basename($file_path)));
		if(($c_left_brace_count + $c_right_brace_count) % 2 > 0 || ($f_left_brace_count + $f_right_brace_count) % 2 > 0){
			$err = '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">错误：</span>文件 &lt;'.$file_path.'&gt; 中存在语法错误,可能造成统计不准确</div>';
			$file_html = $err.$file_html;
		}
		file_put_contents($this->doc_docs_path.'/'.$relative_path.strtolower(preg_replace('/^(?:.*\/)?([^.]+).*$/', '$1'.$ext, basename($file_path))), '<div id="doc_home" class="col-md-12">'.$file_html.'</div>');
		return $doc;
	}


	/**
	 * 解析文档
	 * @param  array &$code_arr 文档注释数组
	 */
	private function filter_doc(&$code_arr){
		foreach ($code_arr as $k => $v) {
			if(preg_match('/\/\*/', $v) > 0){
				$v = preg_replace('/^.*(\/\*.*)$/', '$1', ltrim($v));
				$v = trim(trim($v), '/');
				$v = trim($v, '*');
			}elseif(preg_match('/\*\//', $v) > 0){
				$v = trim(trim($v), '/');
				$v = trim($v, '*');
			}elseif(preg_match('/^\s+\*.*$/', $v) > 0){
				$v = ltrim(trim($v), '*');
				$v = preg_replace('/^\s(.*)$/', '$1', $v);
			}

			if( ! empty($v)){
				$arr[] = $v;
			}
		}
		$code_arr = array();
		$key_name = '';
		
		foreach ($arr as $k => $v) {
			if(preg_match('/^copyright\s(.*)$/', strtolower($v), $t) > 0){
				$code_arr['copyright'][] = $v;
			}else
			if(preg_match('/^@param\s(.*)$/', strtolower($v), $t) > 0){
				$key_name = 'param';
				$code_arr['param'][] = $v;
			}else
			if(preg_match('/^@([^\s]+)\s(.*)$/', $v, $t) > 0){
				$key_name = $t[1];
				$function_name = 'get_doc_'.$key_name;
				if(method_exists($this, $function_name)){
					$code_arr[$key_name][] = $this->$function_name($t[2]);
				}else{
					$code_arr[$key_name][] = $t[2];
				}
			}else
			if( ! empty($key_name)){
				$function_name = 'get_doc_'.$key_name;
				if(method_exists($this, $function_name)){
					$code_arr[$key_name][] = $this->$function_name($v);
				}else{
					$code_arr[$key_name][] = $v;
				}
			}else{
				$code_arr['brief'][] = $v;
			}
		}
		if( ! empty($code_arr['param'])){
			$code_arr['params'] = $this->get_doc_params($code_arr['param']);
			if( ! empty($code_arr['params'])){
				$code_arr['param'] = array();
				$code_arr['param'][0]  = '<table class="table table-bordered table-hover"><thead><tr><th width="10%">数据类型</th><th width="15%">变量名称</th><th width="10%">是否必填</th><th width="*">注释说明</th></tr></thead><tbody>';
				foreach ($code_arr['params'] as $k => $v) {
					$code_arr['param'][0] .= '<tr><td>'.$v['type'].'</td><td>'.$v['name'].'</td><td>'.($v['mandatory'] == 1 ? '是' : '否').'</td><td>'.$v['text'].( ! empty($v['desc']) ? '<br>详细说明：<pre>'.implode("\r\n", $v['desc']).'</pre>' : '').'</td></tr>';
				}
				$code_arr['param'][0]  .= '</tbody></table>';
			}
		}
		$arr = array();
		if( ! empty($code_arr)){
			if(isset($code_arr['return'])){
				$arr['return'] = '<div class="doc_block"><label class="label label-info">结果:</label><pre>'.implode("\r\n", $code_arr['return']).'</pre></div>';
			}
			if(isset($code_arr['throws'])){
				$arr['throws'] = '<div class="doc_block"><label class="label label-info">异常:</label><table class="table table-bordered table-hover"><thead><tr><th>代码</th><th>注释</th></tr></thead><tbody>'.implode("\r\n", $code_arr['throws']).'</tbody></table></div>';
			}
			if(isset($code_arr['scene'])){
				$arr['scene'] = '<div class="doc_block"><label class="label label-info">场景:</label><br>'.implode("<br>", $code_arr['scene']).'</div>';
			}
			if(isset($code_arr['method'])){
				$arr['method_str'] = $code_arr['method'][0];
				$arr['method'] = '<div class="doc_block"><label class="label label-info">请求:</label> '.implode(" ", $code_arr['method']).'</div>';
			}
			if(isset($code_arr['date'])){
				$arr['date'] = '<div class="doc_block"><label class="label label-info">时间:</label> '.implode(" ", $code_arr['date']).'</div>';
			}
			if(isset($code_arr['author'])){
				$arr['author'] = '<div class="doc_block"><label class="label label-info">作者:</label> '.implode(" ", $code_arr['author']).'</div>';
			}
			if(isset($code_arr['version'])){
				$arr['version'] = '<div class="doc_block"><label class="label label-info">版本:</label> '.implode(" ", $code_arr['version']).'</div>';
			}
			if(isset($code_arr['copyright'])){
				$arr['copyright'] = '<div class="doc_block"><label class="label label-info">版权:</label> '.implode("<br>", $code_arr['copyright']).'</div>';
			}
			if(isset($code_arr['url'])){
				$arr['url_arr'] = implode('<br>', $code_arr['url']);
				$arr['url'] = '<div class="doc_block"><label class="label label-info">链接:</label>';
				foreach ($code_arr['url'] as $k => $v) {
					$arr['url'] .= ' <a href="'.$v.'" target="_blank">'.$v.'</a>';
				}
				$arr['url'] .= '</div>';
			}
			if(isset($code_arr['link'])){
				$arr['link'] = '<div class="doc_block"><label class="label label-info">官网:</label>';
				foreach ($code_arr['link'] as $k => $v) {
					$arr['link'] .= ' <a href="'.$v.'" target="_blank">'.$v.'</a>';
				}
				$arr['link'] .= '</div>';
			}
			if(isset($code_arr['name'])){
				$tmp = $code_arr['name'];
				$arr['name'] = $code_arr['name'][0];
				unset($tmp[0]);
				foreach ($tmp as $k => $v) {
					$code_arr['brief'][] = $v;	
				}
			}
			if(isset($code_arr['brief'])){
				$arr['brief'] 	= implode("<br>", $code_arr['brief']);
				$arr['brief_text'] 	= implode("<br>", $code_arr['brief']);
			}
			if(isset($code_arr['param'])){
				$arr['param'] 	= '<div class="doc_block"><label class="label label-info">参数:</label> '.implode("\n", $code_arr['param']).'</div>';
			}
			if(isset($code_arr['params'])){
				$arr['params'] 	= $code_arr['params'];
			}
		}
		$code_arr = array();
		$code_arr['brief']   	= ! empty($arr['brief']) ? '<div class="doc_block"><label class="label label-info">摘要:</label><br>'.$arr['brief'].'</div>' : '';
		$code_arr['name']   	= isset($arr['name']) ? $arr['name'] : '';
		$code_arr['url_arr']   	= isset($arr['url_arr']) ? $arr['url_arr'] : '';
		$code_arr['url']   		= isset($arr['url']) ? $arr['url'] : '';
		$code_arr['method']   	= isset($arr['method']) ? $arr['method'] : '';
		$code_arr['brief_text'] = isset($arr['brief_text']) ? $arr['brief_text'] : '';
		$code_arr['scene']  	= isset($arr['scene']) ? $arr['scene'] : '';

		$code_arr['link'] 		= isset($arr['link']) ? $arr['link'] : '';
		$code_arr['copyright']  = isset($arr['copyright']) ? $arr['copyright'] : '';
		$code_arr['date']  		= isset($arr['date']) ? $arr['date'] : '';
		$code_arr['author']  	= isset($arr['author']) ? $arr['author'] : '';
		$code_arr['version']  	= isset($arr['version']) ? $arr['version'].'<br>' : '';
		$code_arr['param']  	= isset($arr['param']) ? $arr['param'] : '';
		$code_arr['params']  	= isset($arr['params']) ? $arr['params'] : '';
		$code_arr['return'] 	= isset($arr['return']) ? $arr['return'] : '';
		$code_arr['throws'] 	= isset($arr['throws']) ? $arr['throws'] : '';
		$code_arr['method_str'] = isset($arr['method_str']) ? $arr['method_str'] : '';
		$code_arr['tag']		= '<br>';
	}

	/**
	 * 获取参数
	 * @param  array $arr 参数数组
	 * @return array 参数数组
	 */
	private function get_doc_params($arr){
		$params = array();
		foreach ($arr as $k => $v) {
			if(preg_match('/^@param\s+([^\s]*)\s+([^\s]*)\s+([^\s]*)\s*(.*)$/', strtolower($v), $t) > 0){
				if(stristr($t[2], '$') !== false){
					$param_name = ltrim($t[2], '$');
				}
				if(empty($param_name)){
					continue;
				}
				$params[$param_name]['type'] = $t[1];
				switch ($t[1]) {
					case 'integer':
					case 'int':
						$params[$param_name]['input_type'] = 'range';
						break;
					
					default:
						$params[$param_name]['input_type'] = 'text';
						break;
				}
				$params[$param_name]['name'] = $param_name;
				$params[$param_name]['mandatory'] = $t[3] == 'null' ? 0 : 1;
				$params[$param_name]['text'] = ($t[3] == 'null' ? $t[4] : $t[3]);
			}elseif(preg_match('/^@param\s.*$/', strtolower($v)) < 1){
				if(empty($param_name)){
					continue;
				}
				$params[$param_name]['desc'][] = $v;
			}
		}
		return $params;
	}

	/**
	 * 获取异常文档
	 * @param  string $str 异常文档
	 * @return string 解析后适用于页面的HTML源码
	 */
	private function get_doc_throws($str){
		if(preg_match('/^\s*([^\s]+)\s(.*)$/', $str, $arr) > 0){
			unset($arr[0]);
			$str = '<tr>';
			foreach ($arr as $k => $v) {
				$str .= '<td '.($k == 1 ? 'class="s"' : '').'>'.$v.'</td>';
			}
			$str .= '</tr>';
		}
		return $str;
	}

	/**
	 * 写入文件
	 * @param  string $file_path 文件路径
	 * @param  string $content   需要写入的内容
	 * @param  bool   $is_cover	 是否覆盖
	 * @return bool
	 */
	private function write_file($file_path, $content, $is_cover = false){
		if( ! is_dir(dirname($file_path))){
			mkdir(dirname($file_path), 0777, true);
		}

		if( ! file_exists($file_path)){
			$content = "<?php\r\n".$content;
		}
		$h = fopen($file_path, ! empty($is_cover) ? 'w' : 'a');
		if( ! $h){
			return false;
		}
		fwrite($h, $content);
		fclose($h);
		return true;
	}
}