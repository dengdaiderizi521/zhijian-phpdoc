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
<title>至简PHP项目(API)文档生成器</title>
<style type="text/css">
    .bs-callout-success, .bs-callout-info{border-left-color: #eee; border-left-width: 1px;}
    .form_tips{position: absolute; bottom: 14px; right: 0; background-color: #FFF; z-index: 12; display:none;}
    .bs-callout p{text-align: left; line-height: 22px;}
    .login-tips{bottom: 0; left: 0; width: 800px; z-index: 111;}
</style>
</head>
<body>
<div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/">至简PHP项目(API)文档生成器</a>
        </div>
    </div>
</div>
<div class="container theme-showcase" style="margin-top:45px;">
    <form class="bs-callout bs-callout-info bs-example-form" method="POST" action="/home/create_doc" autocomplete="off" onsubmit="return false;">
        <div class="input-group">
            <span class="input-group-addon">文档URL</span>
            <input type="url" class="form-control" id="doc_url" name="doc_url" placeholder="文档的访问地址" value="<?=$doc_url?>" required="required">
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-addon">文档路径</span>
            <input type="text" class="form-control" id="doc_path" name="doc_path" placeholder="文档存放路径" value="<?=$doc_path?>" required="required">
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-addon">项目URL</span>
            <input type="url" class="form-control" id="base_url" name="base_url" placeholder="项目的访问地址" value="<?=$base_url?>" required="required">
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-addon">项目路径</span>
            <input type="text" class="form-control" id="code_path" name="code_path" placeholder="需要生成文档的文件夹路径" value="<?=$code_path?>" required="required">
        </div>
        <div class="input-group">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="check_login" value="1">
                    开启登录验证 
                    <span>
                        <i class="glyphicon glyphicon-question-sign"></i>
                        <div class="bs-callout bs-callout-danger form_tips login-tips">
                            <h4>登录验证说明</h4>
                            <p>
                                您可以在 <code><?=$login_path?></code> 文件中添加账号和密码。我们给您默认了一个用户名 <code>admin</code> 和密码 <code>zhijianphp</code> 请注意，文件中保存的密码是MD5后的；同时您可以在文件中修改登录的验证方式，我们默认将登录信息存放在了SESSION中
                            </p>
                        </div>
                    </span>
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="private" value="1">
                    统计私有方法
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="protected" value="1">
                    统计受保护的方法
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="debug" id="debug" value="1">
                    开启接口测试模式
                </label>
            </div>
        </div>
        
        <div class="input-group" style="display: none;" id="debug_form_url">
            <span class="input-group-addon">表单地址</span>
            <input type="url" class="form-control" name="debug_form_url" placeholder="接口测试表单提交地址,需要一个完整的URL" value="<?= ! empty($debug_form_url) ? $debug_form_url : ''?>">
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-question-sign"></i>
                <div class="bs-callout bs-callout-danger form_tips">
                    <h4>接口测试表单地址说明</h4>
                    <p>
                        开启接口测试模式后，程序自动会将文档要求参数传递到此地址中。 您可以在此地址中将文档参数按照项目要求进行签名和封装。
                        <br>我们会将文档要求参数存放在 <code>params</code> 数组中，并同时传递 <code>relative_path</code> 、<code>class</code> 、<code>method</code> 、<code>http_method</code> 、<code>debug_sign</code> 参数
                        <br>它们分别代表：
                        <br>
                        接口相对路径、类名、方法名、HTTP请求方式和链接秘钥(用于确认请求是受信任的)
                    </p>
              </div>
            </span>
        </div>
        <br>
        <div class="input-group" style="display: none;" id="debug_sign">
            <span class="input-group-addon">链接秘钥</span>
            <input type="text" class="form-control" name="debug_sign" placeholder="秘钥会在提交数据时同时发送，以确保请求来源安全可靠" value="<?= ! empty($debug_sign) ? $debug_sign : ''?>">
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-question-sign"></i>
                <div class="bs-callout bs-callout-danger form_tips">
                    <h4>链接秘钥说明</h4>
                    <p>
                        秘钥会在提交数据时同时发送，以确保请求来源安全可靠。
                    </p>
              </div>
            </span>
        </div>
        <br>
        <button type="submit" id="form_submit" class="btn btn-warning">生成文档</button>
        <a href="<?=$doc_url?>" id="look_doc" target="zhijian-phpdoc" style="display: none;">点击查看文档</a>
    </form>
</div>
<div class="container theme-showcase" id="task_log_div" style="display: none;">
    <div class="panel panel-default">
        <div class="panel-body" id="task_log" style="background-color: #000; height: 200px; color: #00FF00; overflow-y: auto; overflow-x: hidden; word-wrap: break-word; ">
            准备开始
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        var showTashLog = function(){
            $('#task_log_div').show();
            $.post('/home/task_log', {doc_path:$('#doc_path').val()}, function(msg){
                $('#task_log').html(msg);
                $('#task_log').scrollTop($('#task_log')[0].scrollHeight+300);
                if(msg.indexOf('执行完毕') < 0){
                    setTimeout(showTashLog, 800);
                }else{
                    $('#look_doc').attr('href', $('#doc_url').val());
                    $('#look_doc').show();
                    is_submit = 0;
                    $('#form_submit').text('生成完毕');
                }
            });

        }
        $('#debug').click(function(){
            if($(this)[0].checked){
                $('#debug_form_url').show();
                $('#debug_sign').show();
                $('#debug_form_url input').attr('required', 'required');
                $('#debug_sign input').attr('required', 'required');
            }else{
                $('#debug_sign').hide();
                $('#debug_form_url').hide();
                $('#debug_form_url input').attr('required', false);
                $('#debug_sign input').attr('required', false);
            }
        });
        var is_submit = 0;
        $('form').submit(function(){
            $('#look_doc').hide();
            $('#form_submit').text('生成中...');
            if(is_submit == 1){
                return false;
            }
            is_submit = 1;
            var url =  $(this).attr('action');
            $.ajax({
                url:url,  //请求的URL
                timeout : 2000, //超时时间设置，单位毫秒
                type : 'post',  //请求方式，get或post
                data : $(this).serialize(),  //请求所传参数，json格式
                dataType:'json',//返回的数据格式
                success:function(json) {
                    is_submit = 0;
                    $('#form_submit').text('生成文档');
                    alert(json.msg);
                },
                error:function(data){ //请求成功的回调函数
                    showTashLog();
                }
            });
        });
        $('.glyphicon-question-sign').mouseover(function(){
            $(this).siblings('.form_tips').show();
        }).mouseout(function(){
            $(this).siblings('.form_tips').hide();
        });
    })
</script>    
</body>
</html>