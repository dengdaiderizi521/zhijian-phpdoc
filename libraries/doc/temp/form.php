<?php

if(file_exists(__DIR__."/params".$_GET['file'])){
	include_once __DIR__."/params".$_GET['file'];	
}
include_once __DIR__.'/config.php';
$form_param = ! empty($params[$_GET['class'].'/'.$_GET['method']]) && is_array($params[$_GET['class'].'/'.$_GET['method']]) ? $params[$_GET['class'].'/'.$_GET['method']] : array();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="<?=$config['doc_url']?>/static/images/z.ico">
<link href="<?=$config['doc_url']?>/static/css/bootstrap.min.css" type="text/css" rel="stylesheet" charset="utf-8" />
<link href="<?=$config['doc_url']?>/static/css/bootstrap-theme.min.css" type="text/css" rel="stylesheet" charset="utf-8" />
<link href="<?=$config['doc_url']?>/static/css/normalize.css" type="text/css" rel="stylesheet" charset="utf-8" />
<link href="<?=$config['doc_url']?>/static/font-awesome-4.2.0/css/font-awesome.min.css" type="text/css" rel="stylesheet" charset="utf-8" />
<script src="<?=$config['doc_url']?>/static/js/jquery.js"></script>
<script src="<?=$config['doc_url']?>/static/js/bootstrap.min.js"></script>
<link href="<?=$config['doc_url']?>/static/css/jquery.mmenu.all.css" type="text/css" rel="stylesheet" charset="utf-8" />
<link href="<?=$config['doc_url']?>/static/css/jquery.mmenu.widescreen.css" type="text/css" rel="stylesheet" charset="utf-8"  media="all and (min-width: 1430px)" />
<link href="<?=$config['doc_url']?>/static/css/layout.css" type="text/css" rel="stylesheet" charset="utf-8" />
<link href="<?=$config['doc_url']?>/static/css/prettify.css" type="text/css" rel="stylesheet" charset="utf-8" />
<link href="<?=$config['doc_url']?>/static/css/JSONtree.css" type="text/css" rel="stylesheet" charset="utf-8" />
<script type="text/javascript" src="<?=$config['doc_url']?>/static/js/layout.js"></script>
<script type="text/javascript" src="<?=$config['doc_url']?>/static/js/jquery.mmenu.min.all.js"></script>
<script src="<?=$config['doc_url']?>/static/js/prettify.js"></script>
<script src="<?=$config['doc_url']?>/static/js/JSONtree.js"></script>
<title>接口测试-至简PHP项目(API)文档生成器-www.php32.com</title>
<style type="text/css">
    .bs-callout-success, .bs-callout-info{border-left-color: #eee; border-left-width: 1px;}
    body{font-size: 14px;}
    .tips_desc{ position: relative;}
    .tips_desc_div{
    	position: absolute;
    	top: 0;
    	z-index: 1;
    	display: none;
    	margin-left: 24px;
    }
    pre{
    	text-align: left;
    	overflow: hidden;
    	width: 100%;
    	white-space: pre;
    	word-wrap: normal;
    	margin:0;
    	padding: 4px 10px ;
    }
    .input-group span{
    	cursor: pointer;
    }
</style>
</head>
<body>
<div class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">接口测试</a>
        </div>
    </div>
</div>
<div class="container">
<div class="row" style=" background-color: #FFF; color:#333; padding-top: 15px; padding-bottom: 100px;">
  	<div class="col-lg-5">
		<div>
			<label class="label label-info">接口名称：</label> <?=$_GET['relative_path'].$_GET['class'].'/'.$_GET['method']?><br>
			<label class="label label-info">接口地址：</label> <?=$form_param['url']?><br>
			<label class="label label-info">请求方式：</label> <?=$form_param['method']?><br>
			<?php if( ! empty($form_param['brief'])){?>
			<label class="label label-info">接口说明：</label> <?=$form_param['brief']?><br>
			<?php }?>
		</div>
	    <form class="bs-callout bs-callout-info bs-example-form" method="POST" action="<?=$config['doc_url']?>/debug.php" autocomplete="off" onsubmit="return false;">
	    	<input type="hidden" value="<?=$_GET['relative_path']?>" name="relative_path">
	    	<input type="hidden" value="<?=$_GET['class']?>" name="class">
	    	<input type="hidden" value="<?=$_GET['method']?>" name="method">
	    	<input type="hidden" value="<?=$form_param['method']?>" name="http_method">
	    	<input type="hidden" value="<?=$config['debug_sign']?>" name="debug_sign">
	    	<table class="table table-bordered table-hover">
	    		<thead>
	    			<tr>
	    				<th width="15%">参数</th>
	    				<th width="15%">类型</th>
	    				<th width="*">值</th>
	    			</tr>
	    		</thead>
	    		<tbody>
		    	<?php foreach ($form_param['params'] as $k => $v) {?>
		    	<tr>
		    		<td><?=$v['name']?><?=$v['mandatory'] == 1 ? '<font color="red">*</font>' : ''?></td>
		    		<td><?=$v['type']?></td>
					<td>
					<div class="input-group">
					  	<input type="text" class="form-control"  name="params[<?=$v['name']?>]"  placeholder="<?=$v['text']?>" <?=$v['mandatory'] == 1 ? 'required="required"' : ''?>>
					  	<span class="input-group-addon"><i class="fa fa-question-circle" id="tips_desc_<?=$k?>"></i>
					  		<div class="tips_desc_div" id="tips_desc_<?=$k?>_div">
				            	<pre><font color="#419641">参数说明：</font><?="\n".$v['text']?><?= ! empty($v['desc']) ? ":\n".implode("\n", $v['desc']) : ''?></pre>
				            </div>
				        </span>
					</div>
		        </td>
		        </tr>
		        <?php }?>
	        	</tbody>
	        </table>

	        <button type="submit" class="btn btn-warning pull-right" style="margin:0 auto;">提 交</button>
	    </form>
	</div>
	<div class="col-lg-7">
	<div class="panel panel-default">
		<div class="panel-heading"><h4 class="panel-title"><strong>测试结果</strong></h4></div>
		<div class="panel-body" id="api_request" style="min-height: 400px;"></div>
	</div>
</div>
</div>
<script type="text/javascript">
	$(function(){
		$('.input-group span').mouseover(function(){
			$('#'+$(this).find('i').attr('id')+'_div').show();
		});
		$('.input-group span').mouseout(function(){
			$('#'+$(this).find('i').attr('id')+'_div').hide();
		});

		function html_encode(str){   
		  var s = "";   
		  if (str.length == 0) return "";   
		  s = str.replace(/&/g, "&amp;");   
		  s = s.replace(/</g, "&lt;");   
		  s = s.replace(/>/g, "&gt;");   
		  s = s.replace(/ /g, "&nbsp;");   
		  s = s.replace(/\'/g, "&#39;");   
		  s = s.replace(/\"/g, "&quot;");   
		  s = s.replace(/\n/g, "<br>");   
		  return s;   
		} 
		var submit_flag = 0;
		$('form').submit(function(){
			if(submit_flag == 1){
				return false;
			}
			submit_flag = 1;
			var sub_button = $(this).find('button[type=submit]');
			sub_button.html('<i class="fa fa-spinner fa-spin"></i> 提交中...');
			$('#api_request').html('');
			$.post(this.action,$(this).serialize(),function(msg){
				submit_flag = 0;
				$('#api_request').html(JSONTree.create(msg));
				sub_button.html('提 交');
			}, 'json').error(function(msg){
				submit_flag = 0;
				$('#api_request').html(html_encode(msg.responseText));
				sub_button.html('提 交');
			});
		});
	});

</script>
</body>
</html>