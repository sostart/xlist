<!DOCTYPE html>
<html lang="zh-CN">
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  
  <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
  
  <script src="//cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>

  <style>
    .container {
        width: 200px;
        height: 150px;
        position: absolute;
        left:0;
        right:0;
        top: 0;
        bottom: 0;
        margin: auto;
    }
  </style>
</head>
<body>
<div class="container">
    <input name="password" type="password" placeholder="请输入通行口令" />
</div>
<script>
$(function () {
    function showmsg(msg) {
        $('input').attr('placeholder', msg).val('');
    }

    $('input').keydown(function (e) {
        if (e.keyCode==13) {
            var password = $(this).val();
            var tmp = password.split('@', 2);
            if (tmp.length!=2) {
                showmsg('格式错误滴滴');
            }
            $.post('login', {password: password}, function (rs) {
                if (rs.data) {
                    window.location.href = "<?php echo path('/'); ?>";
                } else {
                    showmsg(rs.message);
                }
            });
        }
    });
});
</script>
</body>
</html>