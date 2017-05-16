<?php




// 登陆 && 退出
Route::post('login', 'mustnotlogin', 'v1\IndexAPI::login');
Route::get('logout', 'auth', 'v1\IndexAPI::logout');
Route::get('heartbeat', 'auth', function () {
    return true;
});

Route::group('/', 'auth', function () {
    Route::get('xlist', function ($params) {
        
        $uid = Cache($params['token']);

        function listtree($pid, $uid=false)
        {
            $sql = 'select * from xlist_lists as l left join xlist_user_lists as ul on l.id=ul.lid where l.is_deleted=0';
            $sql .= ' and ul.pid="'.$pid.'"'.($uid ? ' and ul.uid='.$uid : '').' order by ul.sort asc';
            $return = [];
            if ($rs = DB::query($sql)) {
                foreach ($rs as $k=>$row) {
                    $return[$row['id']] = $row;
                    $return[$row['id']]['children'] = listtree($row['id'], $uid);
                }
            }
            return $return;
        }
        Config('app.debug', false);
        return listtree( isset($params['pid'])?$params['pid']:0, $uid );
    });

    Route::post('batch/update', function ($params) {
        
        $uid = Cache($params['token']);

        foreach (Input('lists') as $lid=>$updates) {

            // 创建
            if (! ($list = find('lists', 'id, uid, pid, title, members, is_completed, is_deleted', ['id'=>$lid])) ) {
                $lists = [];
                $user_list = [];

                $lists['id'] = $lid;
                $lists['uid'] = $uid;
                $lists['create_time'] = time();
                insert('lists', $lists);
                $user_list = ['uid'=>$uid, 'lid'=>$lid];
                insert('user_lists', $user_list);

                $list = find('lists', 'id, uid, pid, title, members, is_completed, is_deleted', ['id'=>$lid]);
            }

            if ($list) {

                $lists = [];
                $user_list = [];

                // 标题
                if (isset($updates['title']) && $updates['title']!=$list['title']) {
                    $lists['title'] = $updates['title'];
                }

                // 成员
                if (isset($updates['members']) && trim($updates['members'])!=$list['members']) {
                    
                    $lid = $list['id'];
                    $creator_uid = $list['uid'];
                    
                    // 新增 和 减少
                    $add = $del = [];
                    $old_members = $list['members']?explode(',',$list['members']):[];
                    $new_members = $updates['members']?explode(',',$updates['members']):[];
                    $del = array_diff($old_members, $new_members);
                    $add = array_diff($new_members, $old_members);

                    foreach ($del as $username) {
                        if ($uinfo = find('users', 'id', ['username'=>$username])) {
                            // 创建者删不掉,保证条目有人在管理
                            if ($uinfo['id']!=$creator_uid) {
                                delete('user_lists', ['uid'=>$uinfo['id'], 'lid'=>$lid]);
                            }
                        }
                    }
                    foreach ($add as $username) {
                        if ($uinfo = find('users', 'id, username', ['username'=>$username])) {
                            if ($uinfo['id']!=$creator_uid) {
                                insert('user_lists', ['uid'=>$uinfo['id'], 'lid'=>$lid]);
                            }
                        }
                    }

                    $lists['members'] = trim($updates['members']);

                }

                // 已完成
                if (isset($updates['is_completed']) && $updates['is_completed']!=$list['is_completed']) {
                    if ($updates['is_completed']==0 || $updates['is_completed']==1) {
                        $lists['is_completed'] = $updates['is_completed'];
                    }
                }
                
                // 删除
                if (isset($updates['is_deleted']) && $updates['is_deleted']!=$list['is_deleted']) {
					if ($list['uid']==$uid) {
						if ($updates['is_deleted']==0 || $updates['is_deleted']==1) {
							$lists['is_deleted'] = $updates['is_deleted'];
						}
					} else {
						// 非创建者删除只是让自己不可见
						if ($updates['is_deleted']==1) {
							delete('user_lists', ['uid'=>$uid, 'lid'=>$lid]);
						}
					}
                }

                if ($lists) {
                    $lists['update_time'] = time();
                    update('lists', $lists, ['id'=>$lid]);
                }

                // 父级
                if (isset($updates['pid'])) {
                    if ( $updates['pid']==0 || (strlen($updates['pid'])==36 && find('lists', 'id', ['id'=>$updates['pid']])) ) {
                        $user_list['pid'] = $updates['pid'];
                    }
                }

                // 排序 user_lists sort
                if (isset($updates['sort'])) {
                    $user_list['sort'] = $updates['sort'];
                }

                // 展开 user_lists is_expand
                if (isset($updates['is_expand'])) {
                    $user_list['is_expand'] = $updates['is_expand'];
                }

                if ($user_list) {
                    update('user_lists', $user_list, ['uid'=>$uid, 'lid'=>$lid]);
                }
            }
        }
    });
});
