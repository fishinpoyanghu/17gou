<?php
/**
 * ninecent 用户收货地址api
 */

class AgencyCtrl extends BaseCtrl {
	 
    public function getagencymsg(){
    	$base = api_check_base();
    	$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
    	$nc_list = Factory::getMod('nc_list');  
        $nc_list->setDbConf('main', 'agency');

        $sql = "select * from {$nc_list->dbConf['tbl']}     where `uid`={$login_user['uid']}";         
        $msg = $nc_list->getDataBySql($sql);  
        if (!$msg) {
			api_result(1, '你好,你还未成为代理。');
		}
        api_result(0,'succ',$msg);
        
    }
}