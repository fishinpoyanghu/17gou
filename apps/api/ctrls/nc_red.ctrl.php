<?php
/**
 * @since 2016-01-06
 * note 晒单相关
 */
class NcRedCtrl extends BaseCtrl{
	
    public function redList(){
        $base = api_check_base();
        $page = pint('page');
        $page = $page<1 ? 1 : (int)$page;
        $nc_activity_mod = Factory::getMod('nc_red_activity');
        $result = $nc_activity_mod->getActivityList($base,$page);
        api_result(0, '获取成功', $result);
    }

    public function detail(){
        $base = api_check_base();
        $activity_id = pint('activity_id');
        if (!$activity_id) {
            api_result(5, 'activity_id不合法');
        }

        $login_user = app_get_login_user($base['sessid'], $base['appid'], false);
        $nc_activity_mod = Factory::getMod('nc_red_activity');
        $result = $nc_activity_mod->detail($base,$activity_id,$login_user['uid']);
        api_result(0, '获取成功', $result);
    }

    public function joinRed(){
        $base = api_check_base();
        $activity_id = pint('activity_id');
        if (!$activity_id) {
            api_result(5, 'activity_id不合法');
        }
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $nc_activity_mod = Factory::getMod('nc_red_activity');
        $result = $nc_activity_mod->joinRed($base,$activity_id,$login_user['uid']);
        if($result){
            api_result(0, '处理中',array('order_id'=>$result));
        }else{
            api_result(0, '参与失败');
        }
    }

    public function joinWait(){
        $base = api_check_base();
        $activity_id = pint('order_id');
        if (!$activity_id) {
            api_result(5, 'order_id不合法');
        }
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $nc_activity_mod = Factory::getMod('nc_red_activity');
        $result = $nc_activity_mod->joinWait($base,$activity_id,$login_user['uid']);
        api_result(0, 'succ',$result);
    }

    public function joinList(){
        $base = api_check_base();
        $activity_id = pint('activity_id');
        if (!$activity_id) {
            api_result(5, 'activity_id不合法');
        }
        $nc_activity_mod = Factory::getMod('nc_red_activity');
        $result = $nc_activity_mod->joinList($base,$activity_id);
        api_result(0, 'succ',$result);
    }

    public function joinHistory(){
        $base = api_check_base();
        $red_id = pint('red_id');
        $page = pint('page');
        if (!$red_id) {
            api_result(5, 'red_id不合法');
        }
        $page = $page < 1 ? 1 : (int)$page;
        $nc_activity_mod = Factory::getMod('nc_red_activity');
        $result = $nc_activity_mod->joinHistory($base,$red_id,$page);
        api_result(0, 'succ',$result);
    }

    public function joinResult(){
        $base = api_check_base();
        $activity_id = pint('activity_id');
        if (!$activity_id) {
            api_result(5, 'activity_id不合法');
        }
        $nc_activity_mod = Factory::getMod('nc_red_activity');
        $result = $nc_activity_mod->joinResult($base,$activity_id);
        api_result(0, 'succ',$result);
    }

}