<?php

include_once __DIR__.'/config.php';//载入配置文件

/*
|----------------------------------------------------------------------------------------
| 设置登录的用户名和密码，您可以在$user中添加多个用户，key是用户名，value是密码，密码是md5加密后的值
|----------------------------------------------------------------------------------------
*/ 
$user['admin'] = 'b5ff867850de9ec3e3c00dd765fbceca';




/*
|----------------------------------------------------------------------------------------
| 登录验证，默认将登录信息存储在SESSION中
|----------------------------------------------------------------------------------------
*/ 
session_start();
if(empty($_SESSION['login']) && ! empty($_POST['username']) && ! empty($_POST['password'])){
    if( ! empty($user[$_POST['username']]) && md5($_POST['password']) == $user[$_POST['username']]){
        $_SESSION['login'] = 1;
        echo json_encode(array('code'=>1));
    }else{
        echo json_encode(array('code'=>2, 'msg'=>'用户名或密码错误'));
    }
    die;
}elseif(empty($_SESSION['login'])){
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
<title>登录-至简PHP项目(API)文档生成器-www.zhijian.cc/doc</title>
<style>
  .input-group{
    margin-bottom: 10px;
  }
  .alert{display: none;}
</style>
</head>
<body>
<div class="container">
<div style="background-color: #FFFFFF; padding: 20px; width: 400px; height: 250px; margin: 200px auto; color:#666; border-radius: 4px;">
  <form class="bs-callout bs-callout-info bs-example-form" method="POST" action="?" autocomplete="off" onsubmit="return false;">
    <h5>至简PHP项目(API)文档生成器</h5>
        <div class="input-group  input-group-lg">
            <span class="input-group-addon"><i class="fa fa-user"></i></span>
            <input type="text" class="form-control" id="doc_url" name="username" placeholder="用户名" value="" required="required">
        </div>
        <div class="input-group  input-group-lg">
            <span class="input-group-addon"><i class="fa fa-lock" style="margin-right: 2px;"></i></span>
            <input type="password" class="form-control" id="doc_url" name="password" placeholder="密码" value="" required="required">
        </div>
        <div class="alert alert-danger" id=""><span class="glyphicon glyphicon-info-sign"></span>警告：<span id="error_msg"></span></div>
        <button type="submit" class="btn btn-warning btn-lg btn-block">登 录</button>
  </form>
</div>
</div>
<script type="text/javascript">
$(function(){
    var submit_flag = 0;
    $('form').submit(function(){
        if(submit_flag == 1){
            return false;
        }
        submit_flag = 1;
        var sub_button = $(this).find('button[type=submit]');
        sub_button.html('<i class="fa fa-spinner fa-spin"></i> 登录中...');
        $.post(this.action,$(this).serialize(),function(msg){
            submit_flag = 0;
            if(msg.code == 1){
                window.location.reload();
                return;
            }else{
                $('.alert').show();
                $('#error_msg').html(msg.msg);
                sub_button.html('登 录');
            }
        }, 'json').error(function(msg){
            submit_flag = 0;
            $('.alert').show();
            $('#error_msg').html('登陆失败');
            sub_button.html('登 录');
        });
    });
});
</script>
</body>
</html>
<?php
die;
}