<!DOCTYPE html>
<html lang="zh-CN">
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  
  <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
  
  <script src="//cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
  <script src="//cdn.bootcss.com/notify.js/3.0.0/notify.min.js"></script>
  <script src="/static/js/Project.js"></script>

  <style>
    .container {}
    .project {}
    .selected { background-color:#bbb; }
    .row { margin-top:3px;  cursor:text; }
    .expand { float:left; margin-right:5px; width:14px; height:14px; }
    .dot { float:left; margin-right:5px; width:20px; height:20px; background-image:url(/static/images/dot.svg); cursor:pointer; background-color:transparent; border-radius:12px; }
    .dot-has-children { background-color:#ccc; }
    .dot-hover { background-color:#aaa; }
    .title { float:left; margin-right:5px; outline:0px; min-width:50px; }
    .members { float:right; margin-right:5px; outline:0px; width:200px; border:0px; padding:0px; }
    .status { float:right; margin-left:5px; display:none; }
    .children { margin-left:13px; padding-left:18px;  border-left:1px solid #ebebeb; display:none; }
    .completed {}
    .completed .title { text-decoration:line-through; color:#bbb; }
    .completed a { color:#bbb; }
  </style>
</head>
<body>
  <div style="height:50px;">
    <button id="hide_members">隐藏/显示成员列</button>
    <button id="tomato" data-s="0">番茄</button>
  </div>
  <div class="container" id="breadcrumb" style="display:none;">Home<hr /></div>
  <div class="container" id="container"></div>
  <script id="project" type="tmp">
    <div class="project">
        <div class="row">
          <div class="expand"><span style="cursor:pointer;display:none;" class="glyphicon glyphicon-plus"></span></div>
          <div class="dot">&nbsp;</div>
          <div class="title" contenteditable></div>
          <div class="status"><span class="glyphicon glyphicon-pause"></span><span class="glyphicon glyphicon-stop"></span></div>
          <div class="members" contenteditable></div>
        </div>
        <div class="children"></div>
    </div>
  </script>
  
  <script src="/static/js/API.js"></script>

  <script>$(function () { $('#hide_members').click(function () { if ($('.members').is(':visible')) {$('.members').hide();} else {$('.members').show();} }); });</script>
  <script>
      $(function () { 
          $('#tomato').click(function () {
              // 停止
              if ($(this).data('s')>0) {
                  $(this).html('番茄').data('s', 0);
                  clearTimeout($(this).data('timer'));
                  return false;
              }
              
              // 启动
              $('#tomato').data('s', 26);
              (function () {
                  var s = $('#tomato').data('s')-1;
                  if (s>0) { 
                      // 循环 1分钟
                      $('#tomato').data('timer', setTimeout(arguments.callee, 60000));
                  } else if (s==0) {
                      $('#tomato').after('<span class="glyphicon glyphicon-plane"></span>');
                      notify('叮叮叮', '休息时间休息时间', {timeout: 20});
                  }
                  $('#tomato').html('番茄'+(s?' '+s:'')).data('s', s);
              })();
          });
      });
  </script>

  <script>
    API.url = "<?php echo $apiurl; ?>";
    API.token = "<?php echo $token; ?>";
    $(function () {
        API.get('xlist', {}, function (rs) {
             Xlist.bootstrap(rs.data, $('#container'));
        });

        // 定时提交改变
        setInterval(function () {
            Xlist.batchUpdate();
        }, 3000);
    });

    function notify(title, body, options) {
        if (!Notify.needsPermission || Notify.isSupported()) {
            Notify.requestPermission(function () {
                // body timeout closeOnClick notifyShow notifyClose notifyClick notifyError
                var myNotification = new Notify(title, $.extend({ body: body, timeout: 5, closeOnClick: true }, options||{}));
                myNotification.show();
            }, function () { console.log('不允许通知'); });
        }
    }
  </script>
</body>
</html>
