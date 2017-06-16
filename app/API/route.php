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

        function listtree($pid, $uid=0)
        {
            if ($uid) {
                $sql = 'select * from xlist_lists where is_deleted=0 and pid="'.$pid.'" and uid='.$uid.' order by sort asc';
            } else {
                $sql = 'select * from xlist_lists where is_deleted=0 and pid="'.$pid.'" order by sort asc';
            }
            
            $return = [];
            if ($rs = DB::query($sql)) {
                foreach ($rs as $k=>$row) {
                    $return[$row['id']] = $row;
                    $return[$row['id']]['children'] = listtree($row['id'], $row['is_composer']?0:$uid);
                    $return[$row['id']]['children'] = listtree($row['is_composer']&&$row['noumenon_id']?$row['noumenon_id']:$row['id'], $row['is_composer']?0:$uid);
                }
            }
            return $return;
        }
        Config('app.debug', false);
        return listtree( isset($params['pid'])?$params['pid']:0, $uid );
    });

    Route::post('batch/update', function ($params) {
        
        $uid = Cache($params['token']);

        foreach (Input('lists') as $id=>$changes) {

            // 创建
            if (! ($olist = find('lists', 'id, noumenon_id, uid, pid, title, members, is_composer, is_completed, is_deleted', ['id'=>$id])) ) {
                $insert_data = [];
                $insert_data['id'] = $id;
                $insert_data['noumenon_id'] = $changes['noumenon_id']?:0;
                $insert_data['uid'] = $uid;
                $insert_data['create_time'] = time();
                insert('lists', $insert_data);
                $olist = find('lists', 'id, noumenon_id, uid, pid, title, members, is_composer, is_completed, is_deleted', ['id'=>$id]);
            }

            if ($olist) {

                $update_data = [];
                $sync_data = [];
                $noumenon_id = $olist['noumenon_id']?:$olist['id'];

                // 标题
                if (isset($changes['title']) && $changes['title']!=$olist['title']) {
                    $sync_data['title'] = $changes['title'];
                }

                // 成员
                if (isset($changes['members']) && trim($changes['members'])!=$olist['members']) {
                    
                    $id = $olist['id'];
                    
                    // 新增 和 减少
                    $add = $del = [];
                    $old_members = $olist['members']?explode(',',$olist['members']):[];
                    $new_members = $changes['members']?explode(',',$changes['members']):[];
                    $del = array_diff($old_members, $new_members);
                    $add = array_diff($new_members, $old_members);

                    foreach ($del as $username) {
                        if ($username=='协作') {
                            $sync_data['is_composer'] = 0;
                            continue;
                        }
                        if ($uinfo = find('users', 'id', ['username'=>$username])) {
                            update('lists', ['is_deleted'=>1], ['uid'=>$uinfo['id'], 'noumenon_id'=>$noumenon_id]);
                        }
                    }

                    foreach ($add as $username) {
                        if ($username=='协作') {
                            $sync_data['is_composer'] = 1;
                            continue;
                        }
                        if ($uinfo = find('users', 'id, username', ['username'=>$username])) {
                             insert('lists', ['id'=>uuid(), 'noumenon_id'=>$noumenon_id, 'uid'=>$uinfo['id']]);
                        }
                    }

                    $sync_data['members'] = trim($changes['members']);
                }

                // 已完成
                if (isset($changes['is_completed']) && $changes['is_completed']!=$olist['is_completed']) {
                    if ($changes['is_completed']==0 || $changes['is_completed']==1) {
                        $sync_data['is_completed'] = $changes['is_completed'];
                    }
                }
                
                // 标题, 成员, 完成状态, 协作   同步
                if ($sync_data) {
                    update('lists', $sync_data, 'id = "'.$noumenon_id .'" OR noumenon_id = "'.$noumenon_id.'"');
                }

                // 删除
                if (isset($changes['is_deleted']) && $changes['is_deleted']!=$olist['is_deleted']) {
                    if ($changes['is_deleted']==0 || $changes['is_deleted']==1) {
                        $update_data['is_deleted'] = $changes['is_deleted'];
                    }
                }

                // 父级
                if (isset($changes['pid'])) {
                    if ( $changes['pid']==0 || (strlen($changes['pid'])==36 && $pinfo = find('lists', 'id,noumenon_id,is_composer', ['id'=>$changes['pid']]))) {
                        $pid = $changes['pid'];
                        if ($pinfo['is_composer']) {
                            $pid = $pinfo['noumenon_id']?:$pid;
                        }
                        $update_data['pid'] = $pid;
                    }
                }

                // 排序 user_lists sort
                if (isset($changes['sort'])) {
                    $update_data['sort'] = $changes['sort'];
                }

                // 展开 user_lists is_expand
                if (isset($changes['is_expand'])) {
                    $update_data['is_expand'] = $changes['is_expand'];
                }

                if ($update_data) {
                    $update_data['update_time'] = time();
                    update('lists', $update_data, ['id'=>$id]);
                }
            }
        }
    });
});

Route::group('noname', function () {
    DB::switchTo('noname');
    Route::get('hi', function () {
        $q = Input::get('q');
        if ($sentence = find('sentences', ['sentence'=>$q])) {
            if ($rs = find('sentences_map', ['sid'=>$sentence['id']])) {
                return find('sentences', ['id'=>$rs['sid_response']]);
            }
        }
    });
});

function uuid($prefix = '')  
{
    $chars = md5(uniqid(mt_rand(), true));  
    $uuid  = substr($chars,0,8) . '-';  
    $uuid .= substr($chars,8,4) . '-';  
    $uuid .= substr($chars,12,4) . '-';  
    $uuid .= substr($chars,16,4) . '-';  
    $uuid .= substr($chars,20,12);  
    return $prefix . $uuid;  
}   
