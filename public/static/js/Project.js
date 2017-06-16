root_id = 0;

$.fn.xlist = function () {
  if (this.hasClass('project')) {
    return new Project(this);
  }
  return this;
};

Project = function (jQobj) {
  if (jQobj==undefined) {
    $.extend(this, Project.create()); // 使用默认配置新建
  } else {
    if (jQobj instanceof jQuery) {
      $.extend(this, jQobj);
    } else {
      $.extend(this, Project.create(jQobj)); // 使用指定配置创建
    }
  }
}

// 新建
Project.prototype.create = function () {
  var newlist = Project.create();
  if (this.hasChildren() && this.domChildren().is(':visible')) {
    newlist.setData('pid', this.data('id')).prependTo(this.domChildren());
  } else if (this.getParent()) {
    this.after(newlist.setData('pid', this.getParent().data('id')));
  } else {
    this.after(newlist.setData('pid', root_id));
  }
  newlist.domTitle().focus();
  newlist.sorting();
  return newlist;
}

// 渲染
Project.prototype.render = function () {

  // 标题
  if (this.domTitle().html() != this.data('title')) {
    this.domTitle().html(this.data('title'));
  }

  // 成员
  if (this.domMembers().html() != this.data('members')) {
    this.domMembers().html(this.data('members'));
  }

  // 圆点
  if (this.hasChildren() && this.data('is_expand')==0 && !this.domDot().hasClass('dot-has-children')) {
    this.domDot().addClass('dot-has-children');
  }

  // 展开 或 收起
  if (this.data('is_expand')==1 && !this.domChildren().is(':visible')) {
    // 加号变减号 圆点 子项
    this.domPlus().removeClass('glyphicon-plus').addClass('glyphicon-minus');
    this.domDot().removeClass('dot-has-children');
    this.domChildren().show();
  } else if (this.data('is_expand')==0 && this.domChildren().is(':visible')) {
    // 减号变加号 圆点 子项
    this.domPlus().removeClass('glyphicon-minus').addClass('glyphicon-plus');
    if (this.hasChildren() && !this.domDot().hasClass('dot-has-children')) {
      this.domDot().addClass('dot-has-children');
    }
    this.domChildren().hide();
  }

  // 完成
  if (this.data('is_completed')==1 && !this.hasClass('completed')) {
    this.addClass('completed');
  } else if (this.data('is_completed')==0) {
    this.removeClass('completed');
  }

  if (this.data('is_deleted')==1) {
    this.hide();
  }

  return this;
}

// 绑定事件
Project.prototype.bindEvent = function () {
  var project = this;

  // 加号显示隐藏点击
  this.domRow().hover(function () {
    if (project.hasChildren()) {
      project.domPlus().show();
    }
  }, function () {
    project.domPlus().hide();
  });
  this.domPlus().click(function () {
    if (project.domPlus().hasClass('glyphicon-plus')) {
        project.expand();
    } else {
        project.packup();
    }
  });

  // 圆点
  var storage = [];
  this.domDot().hover(function () {
      $(this).addClass('dot-hover');
  }, function () {
      $(this).removeClass('dot-hover');
  }).click(function () {
      project.zoomin();
  });

  // 标题
  project.domTitle().keydown(function (e) {
      if (e.keyCode==8) {
          if ($(this).html().replace(/&nbsp;/ig, "")=="") {
              project.xremove(); // 删除
          }
      } else if (e.altKey==1) {
          if (e.keyCode==39) {
              project.indent(); // 缩进
              return false;
          } else if(e.keyCode==37) {
              project.outdent(); // 伸出
              return false;
          } else if (e.keyCode==40) {
              project.down(); // 下移
              return false;
          } else if (e.keyCode==38) {
              project.up(); // 上移
              return false;
          }
      } else if (e.ctrlKey==1) {
          if (e.keyCode==40) {
              project.expand(); // 展开
              return false;
          } else if (e.keyCode==38) {
              project.packup(); // 收起
              return false;
          } else if (e.keyCode==39) {
              project.zoomin();// zoomin
          } else if (e.keyCode==37) {
              project.zoomout();
          } else if (e.keyCode==83) { // 保存
              project.parse();
              $(this).change();
              Xlist.batchUpdate();
              return false;
          } else if (e.keyCode==13) {
              if (project.data('is_completed')==1) {
                project.uncompleted(); // 未完成
              } else {
                project.completed(); // 完成
              }
              return false;
          }
      } else if (e.keyCode==38) {
          project.focusUp(); // 光标上
      } else if (e.keyCode==40) {
          project.focusDown(); // 光标下
      } else if (e.keyCode==13) {
          project.create(); // 新建
          return false;
      }

  }).keyup(function () {
    $(this).change();
  }).blur(function () {
      $(this).change();
  }).change(function () {
      var html = $(this).html();
      if (html != project.data('title')) {
          project.setData('title', html);
      }
  });

  // 成员
  project.domMembers().keydown(function (e) {
      if (e.altKey==1) {
          if (e.keyCode==39) {
              project.indent(); // 缩进
              return false;
          } else if(e.keyCode==37) {
              project.outdent(); // 伸出
              return false;
          } else if (e.keyCode==40) {
              project.down(); // 下移
              return false;
          } else if (e.keyCode==38) {
              project.up(); // 上移
              return false;
          }
      } else if (e.ctrlKey==1) {
          if (e.keyCode==40) {
              project.expand(); // 展开
              return false;
          } else if (e.keyCode==38) {
              project.packup(); // 收起
              return false;
          } else if (e.keyCode==83) { // 保存
              project.parse();
              $(this).change();
              Xlist.batchUpdate();
              return false;
          } else if (e.keyCode==13) {
              if (project.data('is_completed')==1) {
                project.uncompleted(); // 未完成
              } else {
                project.completed(); // 完成
              }
              return false;
          }
      } else if (e.keyCode==38) {
          project.focusUp(); // 光标上
      } else if (e.keyCode==40) {
          project.focusDown(); // 光标下
      } else if (e.keyCode==13) {
          project.create(); // 新建
          return false;
      }

  }).blur(function () {
      $(this).change();
  }).change(function () {
      if (project.data('members')==$(this).html()) {
          return false;
      }

      var old_members = $.grep(project.data('members').split(','), function (v, k) { return $.trim(v).length > 0; });
			var new_members = $.grep($.unique($.trim($(this).html()).replace(/，/ig, ',').split(',')), function (v, k) { return $.trim(v).length > 0; });

      project.setData('members', new_members.join(','));

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
  });

  return this;
}

// --------------- DOM 相关 ---------------------------

// 行
Project.prototype.domRow = function () {
  return this.find('.row:first');
}

// 加号
Project.prototype.domPlus = function () {
  return this.find('.expand:first .glyphicon:first');
}

// 圆点
Project.prototype.domDot = function () {
  return this.find('.dot:first');
}

// 标题
Project.prototype.domTitle = function () {
  return this.find('.title:first');
}

// 成员
Project.prototype.domMembers = function () {
  return this.find('.members:first');
}

// 子项
Project.prototype.domChildren = function () {
  return this.find('.children:first');
}




// 上一个 Project
Project.prototype.getPrev = function () {
  var pre = this.prev();
  if (pre.length>0 && pre.is(':hidden')) {
      return pre.xlist().getPrev();
  }
  return pre.hasClass('project') ? pre.xlist() : false;
}

// 下一个 Project
Project.prototype.getNext = function () {
  var nex = this.next();
  if (nex.length>0 && nex.is(':hidden')) {
      return nex.xlist().getNext();
  }
  return nex.hasClass('project') ? nex.xlist() : false;
}

// 父级 Project
Project.prototype.getParent = function () {
  var parent = this.parent().parent();
  return parent.hasClass('project') ? parent.xlist() : false;
}

// 上一个可见元素
Project.prototype.preVisible = function () {
  var pre = this.getPrev();
  if (pre) {
      return pre;
  }
  return this.getParent();
}

// 下一个可见元素
Project.prototype.nexVisible = function () {
  // 先找子
  if (this.data('is_expand')==1) {
      var nex = false;
      $.each(this.domChildren().find('.project'), function (k, v) {
        if ($(v).css('display')!='none') {
          nex = $(v);
          return false;
        }
      });
      if (nex) {
          return nex.xlist();
      }
  }

  // 再找兄弟级
  var nex = this.getNext();
  if (nex) {
      return nex;
  }

  // 找父级兄弟
  var parent = this;
  while (true)
  {
      var parent = parent.getParent();
      if (parent) {
          var pn = parent.getNext();
          if (pn) {
              return pn;
          }
      } else {
          return false;
      }
  }
}

// 是否有子项
Project.prototype.hasChildren = function () {
  var has = false;
  $.each(this.domChildren().find('.project'), function (k, v) {
    if ($(v).css('display')!='none') {
      has = true;
      return false;
    }
  });
  return has;
}

// --------------- 操作 ---------------------------

// 修改
Project.prototype.setData = function (k, v) {
  this.data(k, v).render();

  var obj = {};
  obj[k] = v;
  Xlist.setData(this.data('id'), obj);

  return this;
}

//
Project.prototype.parse = function () {
  var title = this.domTitle().html(), result;

  var members = [];
  while (result = / ?@([^ ]+)/.exec(title)) {
    members.push(result[1]);
    title = title.replace(/ ?@([^ ]+)/, '');
  }

  if (members.length>0) {
    this.addMembers(members);
    this.domTitle().html(title);
  }
}

// 增成员
Project.prototype.addMembers = function (members) {
  var old_members = $.grep(this.data('members').split(','), function (v, k) { return $.trim(v).length > 0; });
  $.each(members, function (k, v) {
    if ($.inArray(v, old_members) == -1) {
      old_members.push(v);
    }
  });
  if (old_members.join(',') != this.data('members')) {
    this.setData('members', old_members.join(','));
  }
}

// 减成员
Project.prototype.removeMembers = function (members) {
  var old_members = $.grep(this.data('members').split(','), function (v, k) { return $.trim(v).length > 0; });
  $.each(members, function (k, v) {
    var index = $.inArray(v, old_members);
    if (index != -1) {
      delete old_members[index];
    }
  });
  if (old_members.join(',') != this.data('members')) {
    this.setData('members', old_members.join(','));
  }
}

// 展开
Project.prototype.expand = function () {
  return this.setData('is_expand', 1);
}

// 收起
Project.prototype.packup = function () {
  return this.setData('is_expand', 0);
}

// 删除
Project.prototype.xremove = function () {
  this.focusUp();
  this.setData('is_deleted', 1);
  var parent = this.getParent();
  if (parent) {
    parent.render();
  }
  return this;
}

// 缩进
Project.prototype.indent = function () {
  var pre = this.getPrev();
  if (pre) {
    pre.addChildren(this);
    pre.expand();
    this.domTitle().focus();

    this.setData('pid', pre.data('id'));
    this.sorting();
  }
  return this;
}

// 伸出
Project.prototype.outdent = function () {
  var parent = this.getParent();
  if (parent) {
    parent.after(this);
    this.domTitle().focus();

    this.setData('pid', parent.getParent() ? parent.getParent().data('id') : root_id);
    this.sorting();
  }
  return this;
}

// 上移
Project.prototype.up = function () {
  var pre = this.getPrev();
  if (pre) {
    this.after(pre);

    var sort = this.data('sort');
    var pre_sort = pre.data('sort');

    if (sort == pre_sort) {
      pre.sorting();
    } else {
      this.setData('sort', pre_sort);
      pre.setData('sort', sort);
    }
  }

  return this;
}

// 下移
Project.prototype.down = function () {
  var nex = this.getNext();
  if (nex) {
    this.before(nex);

    var sort = this.data('sort');
    var nex_sort = nex.data('sort');

    if (sort == nex_sort) {
      this.sorting();
    } else {
      this.setData('sort', nex_sort);
      nex.setData('sort', sort);
    }
  }

  return this;
}

// 完成
Project.prototype.completed = function () {
  this.setData('is_completed', 1);
  return this;
}

// 未完成
Project.prototype.uncompleted = function () {
  this.setData('is_completed', 0);
  return this;
}

// 排序
Project.prototype.sorting = function () {
  var project = this;

  var pre = Number(project.prev().data('sort'));
  var nex = Number(project.next().data('sort'));

  pre = isNaN(pre)?0:pre;
  nex = isNaN(nex)?1.0:nex;

  var space = Number((nex-pre).toFixed(15));

  var i = 0.1, l = 1;
  while (!(space > i)) {
    i = (i/10).toFixed(15); l++;
    if (l>15) {
      console.log('sort 大于15位 ');
      break;
    }
  }

  var sort = (pre+Number(i)).toFixed(15);

  //console.log('i', i , 'l', l, 'sort', sort);

  project.setData('sort', sort);

  return this;
}

// 光标上移
Project.prototype.focusUp = function () {
  var pre = this.preVisible();
  if (pre) {
      pre.domTitle().focus();
  }
  return this;
}

// 光标下移
Project.prototype.focusDown = function () {
  var nex = this.nexVisible();
  if (nex) {
      nex.domTitle().focus();
  }
  return this;
}

// 增加子项
Project.prototype.addChildren = function (p, top) {
  if (top) {
    this.domChildren().prepend(p);
  } else {
    this.domChildren().append(p);
  }
}

// zoomin
Project.prototype.zoomin = function () {
  this.expand();
  Xlist.zoomin($(this).data('id'));
}

// zoomout
Project.prototype.zoomout = function () {
  if (this.getParent()) {
    this.getParent().zoomout();
  } else {
    Xlist.zoomin(this.data('pid'));
  }
}

// ------------------------------------------
// 创建
Project.create = function (config) {
  var df = {id:0, pid: 0, sort: 0, title: '', members: '', is_expand: 0, is_completed: 0, is_deleted: 0, create_time: 0, update_time: 0, children: []};
  config = $.extend(df, config || []);
  if (config.id == 0) {
    config.id = Project.guid();
  }
  return $($('#project').html()).xlist().data(config).bindEvent().render();
}

Project.S4 = function () {
   return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
}

Project.guid  = function () {
   return (Project.S4()+Project.S4()+"-"+Project.S4()+"-"+Project.S4()+"-"+Project.S4()+"-"+Project.S4()+Project.S4()+Project.S4());
}

// ------------------------------------------
$(function () {
  var Xlist = function () {

  }

  // 初始化,设置数据和DOM
  Xlist.prototype.bootstrap = function (data, container) {
    this.zoom_data = this.data = data;
    this.container = container;
    this.render();
  }

  // 渲染
  Xlist.prototype.render = function () {
    render(this.zoom_data, this.container);
  }

  Xlist.prototype.add = function (project) {
    console.log(project);
  }

  // zoomin
  Xlist.prototype.zoomin = function (id) {
    Xlist.breadcrumb = [];
    this.zoom_data = (id==root_id)?this.data:[findById(id, this.data)];
    this.container.html(''); render(this.zoom_data, this.container);
    if (id!=root_id) {
      var project = this.container.find('.project:first').xlist();
      project.domChildren().css('margin-left', 0).css('padding-left', 0).css('border-left', 0);
      project.domPlus().hide(); project.domDot().hide();

      $('#breadcrumb').html(makeBreadcrumb(Xlist.breadcrumb)).show();
    } else {
      $('#breadcrumb').hide();
    }
  }

  // 搜索
  Xlist.prototype.search = function (keyword) {
    var data = keyword==''?this.zoom_data:search(this.zoom_data, keyword);
    this.container.html(''); render(data, this.container);
  }

  function render (arr, container) {
    if (arr.length==0) {
      var p = new Project();
      container.append(p);
      p.sorting();
    } else {
      $.each(arr, function (k, v) {
        var p = new Project(v);
        container.append(p);
        if (!$.isEmptyObject(v.children)) {
          render(v.children, p.domChildren());
          p.render();// 父级重新渲染
        }
      });
    }
    $('.title:first').focus();
  }

  function makeBreadcrumb(x) {
    x.unshift([0, 'Home']);
    var str = '';
    for (var i in x) {
      str += '<a href="javascript:;" onclick="Xlist.zoomin(\''+x[i][0]+'\');">'+x[i][1]+'</a> > ';
    }
    return str;
  }

  Xlist.breadcrumb = [];

  findById = function (id, data, breadcrumb) {
    var $return;

    breadcrumb = breadcrumb||[];

    if (typeof data[id] == 'undefined') {
      for (var i in data) {
        if (!$.isEmptyObject(data[i]['children'])) {
          breadcrumb.push([data[i]['id'],data[i]['title']]);
          if ($return = findById(id, data[i]['children'], breadcrumb)) {
            Xlist.breadcrumb = breadcrumb;
            return $return;
          }
        }
      }
    } else {
      return data[id];
    }

    return false;
  }

  function search(data, keyword) {
      var $return = {};
      $.each(data, function (k, v) {
        if (v.title.search(keyword)!=-1) {
          $return[k] = v;
        }
        $.extend($return, search(v.children, keyword));
      });
      return $return;
  }

  // 记录属性修改
  Xlist.prototype.changes = [];

  Xlist.prototype.setData = function (id, att) {
    Xlist.breadcrumb = [];
    var obj = findById(id, this.data);
    obj = $.extend(obj, att);

    this.changes.push([id, att]);
  }

  Xlist.prototype.cleanup = function() {
      var changes = this.changes;
      this.changes = [];
      var returns = {};
      $.each(changes, function (k,v) {
        returns[v[0]] = (returns[v[0]]==undefined) ? v[1] : $.extend(returns[v[0]], v[1]);
      });
      return returns;
  }

  Xlist.prototype.batchUpdate = function () {

      var update = this.cleanup();

      $.each(update, function (k, v) {
          console.log(k, v);
      });

      if (!$.isEmptyObject(update)) {
          API.post('batch/update', {lists:update}, function (response) {
              console.log(response);
          });
      }
  }

  window.Xlist = new Xlist;
});

// Arr.sort(by('field1', [by(field2)]))
var by = function(name,minor){
  return function(o,p){
      var a,b;
      if(o && p && typeof o === 'object' && typeof p ==='object'){
          a = o[name];
          b = p[name];
          if(a === b){
              return typeof minor === 'function' ? minor(o,p):0;
          }
          if(typeof a === typeof b){
              return a <b ? -1:1;
          }
          return typeof a < typeof b ? -1 : 1;
      }else{
          thro("error");
      }
  }
}
