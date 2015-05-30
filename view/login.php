<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="/static/images/z.ico">
<link href="/static/css/bootstrap.min.css" type="text/css" rel="stylesheet" charset="utf-8" />
<link href="/static/css/bootstrap-theme.min.css" type="text/css" rel="stylesheet" charset="utf-8" />
<link href="/static/css/docs.min.css" type="text/css" rel="stylesheet" charset="utf-8" />
<script src="/static/js/jquery.js"></script>
<script src="/static/js/bootstrap.min.js"></script>
<title>登录-至简PHP项目(API)文档生成器-www.php32.com</title>
<style>
  .input-group{
    margin-bottom: 10px;
  }
  .alert{display: none;}
</style>
</head>
<body>
<div class="container">
<div style="background-color: #FFFFFF; padding: 20px; width: 400px; height: 250px; margin: 100px auto; color:#666; border-radius: 4px;">
  <form class="" method="POST" action="/home/login" autocomplete="off" onsubmit="return false;">
    <h4>至简PHP项目(API)文档生成器</h4>
        <div class="input-group  input-group-lg">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input type="text" class="form-control" id="doc_url" name="username" placeholder="用户名" value="" required="required">
        </div>
        <div class="input-group  input-group-lg">
            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
            <input type="password" class="form-control" id="doc_url" name="password" placeholder="密码" value="" required="required">
        </div>
        <div class="alert alert-danger" id=""><span class="glyphicon glyphicon-info-sign"></span><span id="error_msg"></span></div>
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
        $('.alert').hide();
        $('#error_msg').html('');
        submit_flag = 1;
        var sub_button = $(this).find('button[type=submit]');
        sub_button.html('登录中...');
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