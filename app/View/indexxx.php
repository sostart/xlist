<!DOCTYPE html>
<html lang="zh-CN">
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  
  <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
  
  <script src="//cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>

  <style>
    .container {}
    .row { margin-top:3px;  cursor:text; }
    .expand { float:left; margin-right:5px; width:14px; height:14px; }
    .dot { float:left; margin-right:5px; width:20px; height:20px; background-image:url(/static/images/dot.svg); cursor:pointer; background-color:transparent; border-radius:12px; }
    .dot-have-children { background-color:#ccc; }
    .dot-hover { background-color:#aaa; }
    .title { float:left; margin-right:5px; outline:0px; min-width:50px; }
    .members { float:right; margin-right:5px; outline:0px; width:200px; border:0px; padding:0px; }
    .status { float:right; margin-left:5px; display:none; }
    .children { margin-left:13px; padding-left:18px; display:none; border-left:1px solid #ebebeb; }
    .completed {}
    .completed .title { text-decoration:line-through; color:#bbb; }
    .completed a { color:#bbb; }
  </style>
</head>
<body>
  <div style="height:50px;">
    <button id="hide_members">隐藏/显示成员列</button>
    <script>$(function () { $('#hide_members').click(function () { if ($('.members').is(':visible')) {$('.members').hide();} else {$('.members').show();} }); });</script>
    <button id="tomato" data-s="0">番茄</button>
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
                        // 循环
                        $('#tomato').data('timer', setTimeout(arguments.callee, 60000));
                    } else if (s==0) {
                        $('#tomato').after('<span class="glyphicon glyphicon-plane"></span>');
                    }
                    $('#tomato').html('番茄'+(s?' '+s:'')).data('s', s);
                })();
            });
        });
    </script>
  </div>
  <div class="container"><?php echo View::render('row', ['lists'=>$lists]); ?></div>
  <script src="/static/js/API.js"></script>
  <script>
  API.url = "<?php echo $apiurl; ?>";
  API.token = "<?php echo $token; ?>";

  $(function () {
     // 改变待同步队列
    var changes = [];

    // 记录
    function record_changes(op, id, alt) {
        changes.push([op, id, alt]);
    }
    
    // 整理 changes
    function sort_out(changes) {
        var returns = {};
        $.each(changes, function (k,v) {
            // ID v[1]  操作 v[0] 修改 data
            if (v[0]=='delete') {
                returns[v[1]] = 'deleted';
            } else {
                returns[v[1]] = (returns[v[1]]==undefined) ? v[2] : $.extend(returns[v[1]], v[2]);
            }
        });
        return returns;
    }
    
    // 批量更新提交
    function batch_update() {
        var update = sort_out(changes);
        changes = [];

        $.each(update, function (k, v) {
            console.log(k, v);
        });

        if (!$.isEmptyObject(update)) {
            API.post('batch/update', {lists:update}, function (response) {
                console.log(response);
            });
        }
    }

    // 定时提交改变
    setInterval(batch_update, 5000);

    // 页面刷新时调用
    $(window).on('beforeunload', batch_update);
    
    // 初始化 绑定事件/展开/完成 project
    $('.project').each(function (k, v) {
        var project = $(v);
        bindEvent(project);
        if (project.data('expand')==1) {
            expand(project);
        }
    });
    
    // 列表为空时自动创建一条
    if ($('.container>.project').length<=0) {
        create($('.container'));
    }

    // 事件 project
    function bindEvent(project) {

        // 行色
        project.find('.members:first').hover(function () {
             project.find('.row:first').css('background-color', '#eee');    
        }, function () {
             project.find('.row:first').css('background-color', '#fff');
        });

        // completed
        if (project.data('completed')==1) {
            project.addClass('completed');
        }

        // 加号显示与隐藏
        project.find('.row:first .glyphicon:first').hide();
        project.find('.row:first').hover(function () {
            if ($(this).next().find('.project').length>0) {
                $(this).find('.expand:first>span:first').show();
            }
        }, function () {
            $(this).find('.expand:first>span:first').hide();
        });

        // 加号点击事件
        project.find('.expand:first .glyphicon:first').click(function () {
            if ($(this).hasClass('glyphicon-plus')) {
                expand(project);
            } else {
                packup(project);
            }
        });

        // 圆点
        project.find('.dot:first').hover(function () {
            $(this).addClass('dot-hover');
        }, function () {
            $(this).removeClass('dot-hover');
        }).click(function () {
            // @todo
        });
        
        // 标题
        project.find('.title:first').focus(function () {
            project.data('title', $(this).html());
        }).keydown(function (e) {
            if (e.keyCode==8) {
                if ($(this).html().replace(/&nbsp;/ig, "")=="") {
                    remove(project); // 删除
                }
            } else if (e.altKey==1) {
                if (e.keyCode==39) {
                    retract(project); // 缩进
                    return false;
                } else if(e.keyCode==37) {
                    extend(project); // 伸出
                    return false;
                } else if (e.keyCode==40) {
                    movedown(project); // 下移
                    return false;
                } else if (e.keyCode==38) {
                    moveup(project); // 上移
                    return false;
                }
            } else if (e.ctrlKey==1) {
                if (e.keyCode==40) {
                    expand(project); // 展开
                    return false;
                } else if (e.keyCode==38) {
                    packup(project); // 收起
                    return false;
                } else if (e.keyCode==83) { // 保存
                    $(this).change();
                    batch_update();
                    return false;
                } else if (e.keyCode==13) {
                    completed(project); // 完成
                    return false;
                }
            } else if (e.keyCode==38) {
                focusup(project); // 光标上
            } else if (e.keyCode==40) {
                focusdown(project); // 光标下
            } else if (e.keyCode==13) {
                create(project); // 新建
                return false;
            }
        }).blur(function () {
            $(this).change();
        }).change(function () {
            var html = $(this).html();
            if (html != project.data('title')) {
                project.data('title', html);
                console.log('标题 '+project.data('id'));
                record_changes('update', project.data('id'), {title:html});
            }
        });
        
        // 成员
        project.data('members', project.find('.members:first').html());
		project.find('.members:first').keydown(function (e) {
            if (e.keyCode==13) {
                create(project); // 新建
                return false;
			}  else if (e.altKey==1) {
                if (e.keyCode==39) {
                    retract(project); // 缩进
                    return false;
                } else if(e.keyCode==37) {
                    extend(project); // 伸出
                    return false;
                } else if (e.keyCode==40) {
                    movedown(project); // 下移
                    return false;
                } else if (e.keyCode==38) {
                    moveup(project); // 上移
                    return false;
                }
            } else if (e.ctrlKey==1) {
                if (e.keyCode==40) {
                    expand(project); // 展开
                    return false;
                } else if (e.keyCode==38) {
                    packup(project); // 收起
                    return false;
                } else if (e.keyCode==83) {
                    $(this).change();
                    batch_update();
                    return false;
                }
            }
		}).blur(function () {            
            $(this).change();
        }).change(function () {
            if (project.data('members')==$(this).html()) {
                return false;
            }
            var old_members = $.grep(project.data('members').split(','), function (v, k) { return $.trim(v).length > 0; });
			var new_members = $.grep($.unique($.trim($(this).html()).replace(/，/ig, ',').split(',')), function (v, k) { return $.trim(v).length > 0; });
            $(this).html(new_members.join(',')); project.data('members', new_members.join(','));

            var added = [];
            $.each(new_members, function (k, v) {
                if ($.inArray(v, old_members) == -1) {
                    added.push(v);
                }
            });

            var deleted = [];
            $.each(old_members, function (k, v) {
                if ($.inArray(v, new_members) == -1) {
                    deleted.push(v);
                }
            });

            if (added.length>0 || deleted.length>0) {
                console.log('成员 '+project.data('id'));
                record_changes('update', project.data('id'), {members:new_members.join(',')});
            
                // 修改影响父级
                //var parent = get_parent(project);
                //if (parent) {
                    //if (added.length>0) {
                        //add_members(parent, added);
                    //}
                    //if (deleted.length>0) {
                        //remove_members(parent, deleted);
                    //}
                    //parent.find('.members:first').change();
                //}
            }
        });
        
        return project;
    }

    // 展开
    function expand(project) {
        // 加减号
        project.find('.expand:first .glyphicon:first').removeClass('glyphicon-plus').addClass('glyphicon-minus');
        // 圆点
        project.find('.dot:first').removeClass('dot-have-children');
        // 展开
        project.find('.children:first').show();
        
        if (project.data('expand')==0) {
            console.log('展开 '+project.data('id'));
            project.data('expand', 1);
            record_changes('update', project.data('id'), {is_expand:1});
        }
    }

    // 收起
    function packup(project) {
        project.find('.expand:first .glyphicon:first').removeClass('glyphicon-minus').addClass('glyphicon-plus');
        project.find('.children:first').hide();
        project.data('expand', 0);

        // 原父级圆点状态及展开状态
        if (project.find('.children:first .project').length<=0) {
            project.find('.dot:first').removeClass('dot-have-children');
        } else {
            project.find('.dot:first').addClass('dot-have-children');
        }

        console.log('收起 '+project.data('id'));
        record_changes('update', project.data('id'), {is_expand:0});
    }

    // 新建 project
    function create(project) {
        var new_project = bindEvent($($('#project').html()));
        new_project.data('id', guid()); // 临时生成唯一ID
        if (project.hasClass('project')) {
            var children = project.find('>.children:first').find('.project:first');
            if (children.is(':visible')) {
                new_project.data('pid', project.data('id'));
                children.before(new_project);
            } else {
                var parent = get_parent(project);
                new_project.data('pid', parent?parent.data('id'):0);
                project.after(new_project);
            }
        } else {
            // 直接往 container 中添加
            new_project.data('pid', 0);
            project.append(new_project);
        }

        newsort(new_project);

        new_project.find('.title:first').focus();
        
        console.log('新建 '+new_project.data('id'));
        record_changes('create', new_project.data('id'), { pid: new_project.data('pid') });
    }
    
    // 完成 project
    function completed(project) {
        var completed = project.data('completed')==1?0:1;
        if (completed) {
            project.addClass('completed');
        } else {
            project.removeClass('completed');
        }
        project.data('completed', completed);
        record_changes('update', project.data('id'), { is_completed: completed });
    }
    
    // 设置新排序权重
    function newsort(project, exchange) {
        if (exchange==undefined) {
            var pre = Number(project.prev().data('sort'));
            var nex = Number(project.next().data('sort'));
            
            pre = isNaN(pre)?0:pre;
            nex = isNaN(nex)?1:nex;
            
            var space = nex-pre;

            if (space>0.001) {
                                                 var sort = pre+0.001;
            } else if (space>0.000001) {
                                                 var sort = pre+0.000001;
            } else if (space>0.000000001) {
                                                 var sort = pre+0.000000001;
            } else if (space>0.000000000001) {
                                                 var sort = pre+0.000000000001;
            } else if (space>0.0000000000000011) {
                                                 var sort = pre+0.000000000000001;
            } else {
                console.log('@todo 需要重新排序');
            }

            project.data('sort', sort);
        } else {
            var psort = project.data('sort');
            project.data('sort', exchange.data('sort'));
            exchange.data('sort', psort);

            record_changes('update', exchange.data('id'), { sort: exchange.data('sort') });    
        }

        record_changes('update', project.data('id'), { sort: project.data('sort') });
    }
    
    // 获得父级或返回false
    function get_parent(project) {
        var p = project.parent().parent();
        if (p.hasClass('project')) {
            return p;
        } else {
            return false;
        }
    }

    // 删除 project
    function remove(project) {
        if ($('.container').find('.project').length<=1) {
            console.log('默认保留一条不可删除');
            return false;
        }

        // 先移动光标
        focusup(project);
        
        // 删除前获取ID
        var project_id = project.data('id');
        // 删除前获得父级
        var parent = get_parent(project);
        // 删除
        project.remove();

        // 在删除后判断父级圆点状态
        if (parent && parent.find('.children:first .project').length<=0) {
            parent.find('.dot:first').removeClass('dot-have-children');
        }

        console.log('删除 '+project.data('id'));
        record_changes('delete', project_id, {});
    }
    
    // 缩进 project
    function retract(project) {
        var pre = project.prev();
        if (pre.hasClass('project')) {
            // 新父级展开
            expand(pre);
            // 缩进
            pre.find('.children:first').append(project);
            // 修改 pid
            project.data('pid', pre.data('id'));
            // 重新设置排序权重
            newsort(project);

            // 标题获得焦点
            project.find('.title:first').focus();
            
            // 记录
            console.log('缩进 '+project.data('id'));
            record_changes('update', project.data('id'), { pid: project.data('pid') });
            
            // 新父级增加成员, 并触发change事件
            // add_members(pre, project.find('.members:first').html().split(','));
            // pre.find('.members:first').change();
        }
    }

    // 伸出 project
    function extend(project) {
        var parent = get_parent(project);
        if (parent) {
            // 伸出
            parent.after(project);
            // 修改 pid
            var new_parent = get_parent(project);
            project.data('pid', new_parent?new_parent.data('id'):0);
            // 重新设置排序权重
            newsort(project);
            // 原父级圆点状态及展开状态
            if (parent.find('.children:first .project').length<=0) {
                packup(parent);
            }
            project.find('.title:first').focus();
            console.log('伸出 '+project.data('id'));
            record_changes('update', project.data('id'), { pid: project.data('pid') });

            //remove_members(parent, project.find('.members:first').html().split(','));
            //parent.find('.members:first').change();
        }
    }
    
    // 下移
    function movedown(project) {
        var nex = project.next();
        if (nex.hasClass('project')) {
            nex.after(project);
            newsort(project, nex);
            project.find('.title:first').focus();
            console.log('下移 '+project.data('id'));
        }
    }

    // 上移
    function moveup(project) {
        var pre = project.prev();
        if (pre.hasClass('project')) {
            pre.before(project);
            newsort(project, pre);
            project.find('.title:first').focus();
            console.log('上移 '+project.data('id'));
        }
    }

    // 成员+
    function add_members(o, added) {
        var m = o.find('.members:first');
        m.html($.grep($.unique(
            $.merge($.trim(m.html()).replace(/，/ig, ',').split(','), added)
        ), function (v, k) { return $.trim(v).length > 0; }).join(','));
    }

    // 成员-
    function remove_members(o, deleted) {
        var m = o.find('.members:first');
        members = $.trim(m.html()).replace(/，/ig, ',').split(',');

        $.each(deleted, function (k, v) {
            var index = $.inArray(v, members);
            if ( index != -1) {
                // 删除父级成员
                var all_members = [];
                o.find('.children:first').find('.members').each(function (k2, v2) {
                    $.merge(all_members, $(v2).html().split(','));
                });
                if ($.inArray(v, all_members) == -1) {
                    delete members[index];
                }
            }
        });

        m.html($.grep(members, function (v, k) { return $.trim(v).length > 0; }).join(','));
    }

    // 光标上
    function focusup(project) {
        var pre = pre_project(project);
        if (pre) {
            pre.find('.title:first').focus();
        }
    }
    
    // 光标下
    function focusdown(project) {
        var nex = nex_project(project);
        if (nex) {
            nex.find('.title:first').focus();
        }
    }
    
    // 上一个 project
    function pre_project(project) {
        var pre = project.prev();
        if (pre.hasClass('project')) {
            return pre;
        }
        return get_parent(project);
    }
    
    // 下一个 project
    function nex_project(project) {
        // 先找子
        if (project.find('.children:first').is(':visible')) {
            var nex = project.find('.children:first').find('.project:first');
            if (nex.length>0) {
                return nex;
            }
        }

        // 再找兄弟级
        var nex = project.next();
        if (nex.hasClass('project')) {
            return nex;
        }

        // 找父级兄弟
        var parent = project;
        while (true)
        {
            var parent = get_parent(parent);
            if (parent) {
                var pn = parent.next();
                if (pn.hasClass('project')) {
                    return pn;
                }
            } else {
                return false;
            }
        }
    }
  });

  function S4() {
     return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
  }
  function guid() {
     return (S4()+S4()+"-"+S4()+"-"+S4()+"-"+S4()+"-"+S4()+S4()+S4());
  }
  </script>
  <script id="project" type="tmp">
    <div class="project" data-id="" data-pid="" data-sort="" data-expand="0" data-completed="0">
        <div class="row">
          <div class="expand"><span style="cursor:pointer;" class="glyphicon glyphicon-plus"></span></div>
          <div class="dot">&nbsp;</div>
          <div class="title" contenteditable></div>
          <div class="status"><span class="glyphicon glyphicon-pause"></span><span class="glyphicon glyphicon-stop"></span></div>
          <div class="members" contenteditable></div>
        </div>
        <div class="children"></div>
    </div>
  </script>
</body>
</html>
