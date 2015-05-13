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
<title>至简PHP文档生成器</title>
<style type="text/css">
    .bs-callout-success, .bs-callout-info{border-left-color: #eee; border-left-width: 1px;}
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
          <a class="navbar-brand" href="/">至简PHP文档生成器</a>
        </div>
    </div>
</div>
<div class="container theme-showcase" style="margin-top:45px;">
    <form class="bs-callout bs-callout-info bs-example-form" method="POST" action="/home/create_doc" autocomplete="off" onsubmit="return false;">
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">文档URL</span>
            <input type="url" class="form-control" id="doc_url" name="doc_url" placeholder="基础URL" value="<?=$doc_url?>" required="required">
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">文档路径</span>
            <input type="text" class="form-control" id="doc_path" name="doc_path" placeholder="文档存放路径" value="<?=$doc_path?>" required="required">
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">项目URL</span>
            <input type="url" class="form-control" id="base_url" name="base_url" placeholder="基础URL" value="<?=$base_url?>" required="required">
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">项目路径</span>
            <input type="text" class="form-control" id="code_path" name="code_path" placeholder="项目路径" value="<?=$code_path?>" required="required">
        </div>
        <br>
        <button type="submit" id="form_submit" class="btn btn-primary">生成文档</button>
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
                data :{
                    base_url:$('#base_url').val(), 
                    doc_path:$('#doc_path').val(), 
                    code_path:$('#code_path').val(), 
                    doc_url:$('#doc_url').val()
                },  //请求所传参数，json格式
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
        
    })
</script>    
</body>
</html>