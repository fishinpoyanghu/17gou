<?php
/**
 * ninecent 用户收货地址api
 */

class NcUserCtrl extends BaseCtrl {

	public function addressList() {
		
		// 调用测试用例
// 		$this->test_address_list();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$pub_mod = Factory::getMod('pub');
		
		$where = array(
				'uid' => $login_user['uid'],
				'stat' => 0,
		);
		
		$orderby = 'ORDER BY address_id DESC';
		
		$pub_mod->init('main', 'address', 'address_id');
		$address_list = $pub_mod->getRowList($where, 0, 4, $orderby);
		
		if (false === $address_list) {
			api_result(1, '数据库错误，请重试');
		}
		
		$data = array();
		foreach ($address_list as $address) {
			$data[] = $this->format_address($address);
		}
		
		api_result(0, 'succ', $data);
	}

    public function point(){
        // 标准参数检查
        $base = api_check_base();
        // 得到当前登录用户
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $pub_mod = Factory::getMod('pub');

        $where = array(
            'uid' => $login_user['uid'],
        );

        $pub_mod->init('shop', 'point', 'id');
        $point = $pub_mod->getRowList($where, 0, 1);
        $point = $point[0] ? $point[0] : 0;
        api_result(0, 'succ',$point);
    }

    public function pointDetail(){
        // 标准参数检查
        $base = api_check_base();
        // 得到当前登录用户
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $page = pstr('page');
        $page = $page < 1 ? 1 : $page;
        $pub_mod = Factory::getMod('pub');

        $where = array(
            'uid' => $login_user['uid'],
        );
        $pub_mod->init('shop', 'point_detail', 'id');
        $orderby = 'ORDER BY `ut` DESC';

        $point_list = $pub_mod->getRowList($where, ((int)$page -1 ) * 10, 10, $orderby);
        api_result(0, 'succ',$point_list);

    }

    public function moneyDetail(){
        // 标准参数检查
        $base = api_check_base();
        // 得到当前登录用户
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $validate_cfg = array(
            'page' => array(),
        );
        $ipt_list = api_get_posts($base['appid'], $validate_cfg);
        $pub_mod = Factory::getMod('pub');

        $where = array(
            'uid' => $login_user['uid'],
        );
        $pub_mod->init('shop', 'money', 'id');
        $orderby = 'ORDER BY `ut` DESC';
        $limit = array(
            'begin' => ((int)$ipt_list['page'] -1 ) * 10,
        );
        $point_list = $pub_mod->getRowList($where, $limit['begin'], 10, $orderby);
        foreach($point_list as &$p){
            $pd = explode(':',$p['desc']);
            $p['desc'] = $pd[0];
        }
        api_result(0, 'succ',$point_list);
    }

	
	public function addressAdd() {
		
		// 调用测试用例
// 		$this->test_address_add();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$validate_cfg = array(
				'name' => array(
						'api_v_notnull||收件人不能为空',
						'api_v_length|1,10||收件人不能超过10个字',
				),
				'mobile' => array(
						'api_v_mobile||手机号码不合法',
				),
				'province' => array(
						'api_v_notnull||省份不能为空',
						'api_v_length|1,10||省份不合法',						
				),
				'city' => array(
						'api_v_notnull||城市不能为空',
						'api_v_length|1,10||城市不合法',						
				),
				'area' => array(
						'api_v_notnull||区县不能为空',
						'api_v_length|1,10||区县不合法',						
				),
				'detail' => array(
						'api_v_notnull||详细地址不能为空',
						'api_v_length|1,60||地址长度过长',						
				),
				'is_default' => array(
						
				),
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		$ipt_list = api_safe_ipt($ipt_list);
		$ipt_list['is_default'] = $ipt_list['is_default'] ? 1 : 0;
		
		$pub_mod = Factory::getMod('pub');
		
		// 最多只能添加4个地址
		$pub_mod->init('main', 'address', 'address_id');
		$where = array(
				'uid' => $login_user['uid'],
				'stat' => 0,
		);
		
		$address_total = $pub_mod->getRowTotal($where);
		if ($address_total >= 4) {
			api_result(1, '最多只能添加4个地址');
		}
		
		// 如果新增的是默认地址，把其他地址改成非默认
		if ($ipt_list['is_default']) {
			
			$update_data = array(
					'is_default' => 0,
					'ut' => time(),
			);
			$where = array(
					'uid' => $login_user['uid'],
			);
			
			$pub_mod->updateRowWhere($where, $update_data);
		}
		
		$data = array(
				'address_id' => get_auto_id(C('AUTOID_M_ADDRESS')),
				'uid' => $login_user['uid'],
				'name' => $ipt_list['name'],
				'mobile' => $ipt_list['mobile'],
				'province' => $ipt_list['province'],
				'city' => $ipt_list['city'],
				'area' => $ipt_list['area'],
				'detail' => $ipt_list['detail'],
				'is_default' => $ipt_list['is_default'],
				'rt' => time(),
				'ut' => time(),
		);
		
		$pub_mod->init('main', 'address', 'address_id');
		$ret = $pub_mod->createRow($data);
		
		if (!$ret) {
			api_result(1, '数据库错误，请重试');
		}

        //发送积分
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'point_detail');
        $where = array(
            'uid' => $login_user['uid'],
            'desc' => '完善资料',
        );
        $ret = $nc_list->getDataOne($where, array(), array(), array(), false);
        if(empty($ret)){
            $time = time();
            $nc_list->setDbConf('shop', 'point_rule');
            $where = array('type'=>'完善资料');
            $ret2 = $nc_list->getDataOne($where, array(), array(), array(), false);

            $nc_list->setDbConf('shop', 'point_detail');
            $point = $ret2['point'];
            $nc_list->insertData(array(
                'uid' => $login_user['uid'],
                'desc' => '完善资料',
                'point' => $point,
                'ut' => $time,
            ));
            $nc_list->setDbConf('shop', 'point');
            $sql = "insert into {$nc_list->dbConf['tbl']} (`uid`,`point`,`total`,`use`,`ut`) values({$login_user['uid']},$point,$point,0,{$time}) on duplicate key update `point`=`point`+{$point},`total`=`total`+{$point},`ut`={$time}";
            $nc_list->executeSql($sql);
        }


		api_result(0, '添加地址成功', array('address_id'=>intval($data['address_id'])));
	}
	
	public function addressUpdate() {
		
		// 调用测试用例
// 		$this->test_address_update();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$validate_cfg = array(
				'address_id' => array(
						'api_v_numeric|1||address_id不合法',
				),
				'name' => array(
						'api_v_notnull||收件人不能为空',
						'api_v_length|1,10||收件人不能超过10个字',
				),
				'mobile' => array(
						'api_v_mobile||手机号码不合法',
				),
				'province' => array(
						'api_v_notnull||省份不能为空',
						'api_v_length|1,10||省份不合法',
				),
				'city' => array(
						'api_v_notnull||城市不能为空',
						'api_v_length|1,10||城市不合法',
				),
				'area' => array(
						'api_v_notnull||区县不能为空',
						'api_v_length|1,10||区县不合法',
				),
				'detail' => array(
						'api_v_notnull||详细地址不能为空',
						'api_v_length|1,60||地址长度过长',
				),
				'is_default' => array(
						
				),
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		$ipt_list = api_safe_ipt($ipt_list);
		$ipt_list['is_default'] = $ipt_list['is_default'] ? 1 : 0;
		
		$pub_mod = Factory::getMod('pub');
		
		$pub_mod->init('main', 'address', 'address_id');
		$address = $pub_mod->getRow($ipt_list['address_id']);
		
		if (!$address) {
			api_result(2, '要更新的地址不存在');
		}
		
		if ($address['uid'] != $login_user['uid']) {
			api_result(9, '没有权限');
		}
		
		// 如果新增的是默认地址，把其他地址改成非默认
		if ($ipt_list['is_default']) {
				
			$update_data = array(
					'is_default' => 0,
					'ut' => time(),
			);
			$where = array(
					'uid' => $login_user['uid'],
			);
				
			$pub_mod->updateRowWhere($where, $update_data);
		}
		
		$update_data = array(
				'name' => $ipt_list['name'],
				'mobile' => $ipt_list['mobile'],
				'province' => $ipt_list['province'],
				'city' => $ipt_list['city'],
				'area' => $ipt_list['area'],
				'detail' => $ipt_list['detail'],
				'is_default' => $ipt_list['is_default'],
				'ut' => time(),
		);
		
		$pub_mod->init('main', 'address', 'address_id');
		$ret = $pub_mod->updateRow($ipt_list['address_id'], $update_data);
		
		if (!$ret) {
			api_result(1, '数据库错误，请重试');
		}
		
		api_result(0, 'succ');
	}
	
	public function addressDelete() {
		
		// 调用测试用例
// 		$this->test_address_delete();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$validate_cfg = array(
				'address_id' => array(
						'api_v_numeric|1||address_id不合法',
				),
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		$ipt_list = api_safe_ipt($ipt_list);
			
		$pub_mod = Factory::getMod('pub');
		
		$pub_mod->init('main', 'address', 'address_id');
		$address = $pub_mod->getRow($ipt_list['address_id']);
		
		if (!$address) {
			api_result(2, '要删除的地址不存在');
		}
		
		if ($address['uid'] != $login_user['uid']) {
			api_result(9, '没有权限');
		}
		
		$update_data = array(
				'stat' => 1,
				'ut' => time(),
		);
		
		$pub_mod->init('main', 'address', 'address_id');
		$ret = $pub_mod->updateRow($ipt_list['address_id'], $update_data);
		
		if (!$ret) {
			api_result(1, '数据库错误，请重试');
		}
		
		api_result(0, 'succ');
	}
	
	private function format_address($address) {
		
		$ret = array(
				'address_id' => intval($address['address_id']),
				'name' => ap_strval($address['name']),
				'mobile' => ap_strval($address['mobile']),
				'province' => ap_strval($address['province']),
				'city' => ap_strval($address['city']),
				'area' => ap_strval($address['area']),
				'detail' => ap_strval($address['detail']),
				'is_default' => intval($address['is_default']),
		);
		
		return $ret;
	}
	
	/**
	 * 填写收货地址
	 */
	public function fillInAddress(){
		// 调用测试用例
		//$this->test_fill_in_address();
		
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$validate_cfg = array(
			'address_id' => array(
				'api_v_numeric|1||address_id不合法',
			),
			'activity_id' => array(
				'api_v_numeric|1||activity_id不合法',
			)
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$pub_mod = Factory::getMod('pub');
		
		$pub_mod->init('shop', 'lucky_num', 'lucky_num_id');
		//查看用户是否中了这一期
		$where = array(
			'appid' => $base['appid'],
			'uid' => $login_user['uid'],
			'activity_id' => $ipt_list['activity_id']
		);
		$ret = $pub_mod->getRowWhere($where);
		if(empty($ret)){
			api_result(9, '没有权限');
		}
		//获取地址
		$pub_mod->init('main', 'address', 'address_id');
		$addressInfo = $pub_mod->getRow($ipt_list['address_id']);
        $address = urlencode($addressInfo['province'].$addressInfo['city'].$addressInfo['area'].$addressInfo['detail']).':'.urlencode($addressInfo['name']).':'.$addressInfo['mobile'];
		//查看该期是否已经被填写
		$pub_mod->init('shop', 'logistics', 'logistics_id');
		$where = array(
			'appid' => $base['appid'],
			'activity_id' => $ipt_list['activity_id']
		);
		$logisticsInfo = $pub_mod->getRowWhere($where);
        if(!empty($logisticsInfo)){
            $flag = $logisticsInfo['logistics_id'];
        }
		if($logisticsInfo['logistics_stat']>0){
			api_result(1, '当前状态不支持更改地址');
		}
		//填写收货地址
		$time = time();
        if($flag){
            $data = array(
                'address' => $address,
                'ut' => $time,
            );
            $ret = $pub_mod->updateRow($flag,$data);
        }else{
            $data = array(
                'appid' => $base['appid'],
                'activity_id' => $ipt_list['activity_id'],
                'address' => $address,
                'logistics_stat' => 0,
                'ut' => $time,
                'rt' => $time
            );
            $ret = $pub_mod->createRow($data);
        }
		if($ret){
			api_result(0, '填写成功', array('address' => $address));
		}else{
			api_result(1, '填写失败');
		}
	}
	


	/**
	 * 团队填写收货地址
	 */
	public function teamfillInAddress(){
		// 调用测试用例
		//$this->test_fill_in_address();
		
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$validate_cfg = array(
			'address_id' => array(
				'api_v_numeric|1||address_id不合法',
			),
			'activity_id' => array(
				'api_v_numeric|1||activity_id不合法',
			)
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$pub_mod = Factory::getMod('pub');
		
		$pub_mod->init('team', 'team_lucky_num', 'lucky_num_id');
		//查看用户是否中了这一期
		$where = array(
			'appid' => $base['appid'],
			'uid' => $login_user['uid'],
			'activity_id' => $ipt_list['activity_id']
		);
		$ret = $pub_mod->getRowWhere($where);
		if(empty($ret)){
			api_result(9, '没有权限');
		}
		//获取地址
		$pub_mod->init('main', 'address', 'address_id');
		$addressInfo = $pub_mod->getRow($ipt_list['address_id']);
        $address = urlencode($addressInfo['province'].$addressInfo['city'].$addressInfo['area'].$addressInfo['detail']).':'.urlencode($addressInfo['name']).':'.$addressInfo['mobile'];
		//查看该期是否已经被填写
		$pub_mod->init('shop', 'logistics', 'logistics_id');
		$where = array(
			'appid' => $base['appid'],
			'teamwar_id' => $ipt_list['activity_id'],
			'uid'=>$login_user['uid']
		);
		$logisticsInfo = $pub_mod->getRowWhere($where);
        if(!empty($logisticsInfo)){
            $flag = $logisticsInfo['logistics_id'];
        }
		if($logisticsInfo['logistics_stat']>0){
			api_result(1, '当前状态不支持更改地址');
		}
		//填写收货地址
		$time = time();
        if($flag){
            $data = array(
                'address' => $address,
                'ut' => $time,
            );
            $ret = $pub_mod->updateRow($flag,$data);
        }else{
            $data = array(
                'appid' => $base['appid'],
                'activity_id' => 0,
                'teamwar_id' =>  $ipt_list['activity_id'], 
                'address' => $address,
                'logistics_stat' => 0,
                'ut' => $time,
                'rt' => $time,
                'uid'=>$login_user['uid'],
            );
            $ret = $pub_mod->createRow($data);
        }
		if($ret){
			api_result(0, '填写成功', array('address' => $address));
		}else{
			api_result(1, '填写失败');
		}
	}


	/**
	 * 签收
	 */
	public function checkReceive(){
		// 调用测试用例
		//$this->test_check_receive();
		
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$validate_cfg = array(
			'activity_id' => array(
				'api_v_numeric|1||activity_id不合法',
			)
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$nc_list = Factory::getMod('nc_list');
		$where = array(
			'appid' => $base['appid'],
			'uid' => $login_user['uid'],
			'activity_id' => $ipt_list['activity_id']
		);
		$nc_list->setDbConf('shop', 'lucky_num');
		$ret = $nc_list->getDataOne($where, array(), array(), array(), false);
		if(empty($ret)){
			api_result(9, '没有权限');
		}
		
		$where = array(
			'appid' => $base['appid'],
			'activity_id' => $ipt_list['activity_id']
		);
		$data = array(
			'logistics_stat' => 2,
			'rt' => time()
		);
		$nc_list->setDbConf('shop', 'logistics');
		$ret = $nc_list->updateData($where, $data);
		if($ret){
			api_result(0, '修改成功');
		}
		api_result(1, '修改失败');
	}
	/**
	 * 签收
	 */
	public function checkTeamReceive(){ 
		$base = api_check_base(); 
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$validate_cfg = array(
			'activity_id' => array(
				'api_v_numeric|1||activity_id不合法',
			)
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$nc_list = Factory::getMod('nc_list');
		$where = array(
			'appid' => $base['appid'],
			'uid' => $login_user['uid'],
			'activity_id' => $ipt_list['activity_id']
		);
		$nc_list->setDbConf('team', 'team_lucky_num');
		$ret = $nc_list->getDataOne($where, array(), array(), array(), false);
		if(empty($ret)){
			api_result(9, '没有权限');
		}
		
		$where = array(
			'appid' => $base['appid'],
			'teamwar_id' => $ipt_list['activity_id'],
			'uid' => $login_user['uid'],
		);
		$data = array(
			'logistics_stat' => 2,
			'rt' => time()
		);
		$nc_list->setDbConf('shop', 'logistics');
		$ret = $nc_list->updateData($where, $data);
		if($ret){
			api_result(0, '修改成功');
		}
		api_result(1, '修改失败');
	}
    public function sign()
    {
        $base = api_check_base();
        $time = time();
        // 得到当前登录用户
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'sign');
        $where = array(
            'appid' => $base['appid'],
            'uid' => $login_user['uid'],
            'date' => date('Ymd', $time),
        );
        $ret = $nc_list->getDataOne($where, array(), array(), array(), false);
        if (!empty($ret)) {
            api_result(9, '今天已签到');
        }
        $where = array(
            'appid' => $base['appid'],
            'uid' => $login_user['uid'],
            'date' => date('Ymd', $time - 86400),
        );
        $ret = $nc_list->getDataOne($where, array(), array(), array(), false);
        if (empty($ret)) {
            //第一天
            $point = 5;
            $ret['limit'] = 0;
        } else {
            switch ($ret['limit']) {
                case 1:
                    $point = 6;
                    break;
                case 2:
                    $point = 7;
                    break;
                case 3:
                    $point = 8;
                    break;
                case 4:
                    $point = 9;
                    break;
                default:
                    $point = 10;
                    break;
            }
        }
        $insert = array(
            'appid' => $base['appid'],
            'uid' => $login_user['uid'],
            'date' => date('Ymd', $time),
            'limit' => $ret['limit'] + 1,
            'ut' => $time,
        );
        $nc_list->insertData($insert);
        $nc_list->setDbConf('shop', 'point_detail');
        $nc_list->insertData(array(
            'uid' => $login_user['uid'],
            'desc' => '每日签到',
            'point' => $point,
            'ut' => $time,
        ));
        $nc_list->setDbConf('shop', 'point');
        $sql = "insert into {$nc_list->dbConf['tbl']} (`uid`,`point`,`total`,`use`,`ut`) values({$login_user['uid']},$point,$point,0,{$time}) on duplicate key update `point`=`point`+{$point},`total`=`total`+{$point},`ut`={$time}";
        $nc_list->executeSql($sql);
        api_result(0, '签到成功,积分+'.$point);
    }

    public function share(){
        $base = api_check_base();
        $time = time();
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'point_rule');
        $where = array('type'=>'分享');
        $ret = $nc_list->getDataOne($where, array(), array(), array(), false);
        $nc_list->setDbConf('shop', 'point_detail');
        $date = date('Ymd',$time);
        $sql = "select sum(`point`) total from {$nc_list->dbConf['tbl']} where `uid`={$login_user['uid']} and from_unixtime(`ut`,'%Y%m%d')='{$date}' and `desc`='分享'";
        $re = $nc_list->getDataBySql($sql,false);
        if($re[0]['total']>=$ret['limit']){
            api_result(1, '已超最大次数');
        }else{
            $nc_list->setDbConf('shop', 'point_detail');
            $nc_list->insertData(array(
                'uid' => $login_user['uid'],
                'desc' => '分享',
                'point' => $ret['point'],
                'ut' => $time,
            ));
            $nc_list->setDbConf('shop', 'point');
            $sql = "insert into {$nc_list->dbConf['tbl']} (`uid`,`point`,`total`,`use`,`ut`) values({$login_user['uid']},{$ret['point']},{$ret['point']},0,{$time}) on duplicate key update `point`=`point`+{$ret['point']},`total`=`total`+{$ret['point']},`ut`={$time}";
            $nc_list->executeSql($sql);
            api_result(0, '分享成功,积分+'.$ret['point']);
        }

    }

    public function signAlready(){
        $base = api_check_base();
        $time = time();
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'sign');
        $month = date('Ym',$time);
        $sql = "select from_unixtime(`ut`,'%Y-%m-%d') `day` from {$nc_list->dbConf['tbl']} where left(`date`,6)='{$month}' and `uid`={$login_user['uid']}";
        $re = $nc_list->getDataBySql($sql,false);
        $re2 = array();
        foreach($re as $v){
            $re2[] = $v['day'];
        }
        api_result(0, 'succ',array('today'=>date('Y-m-d',$time),'list'=>$re2));
    }

    public function inviteInfo(){
        $base = api_check_base();
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('main', 'user');
        $sql = "select count(*) `total` from {$nc_list->dbConf['tbl']} where `rebate_uid`={$login_user['uid']}";
        $total = $nc_list->getDataBySql($sql);
        /*$nc_list->setDbConf('shop', 'money');
        $sql2 = "select sum(`money`) `total` from {$nc_list->dbConf['tbl']} where `uid`={$login_user['uid']} and `desc` like '佣金%'";*/
        $nc_list->setDbConf('main', 'user');
        $sql2 = "select `yongjin` from {$nc_list->dbConf['tbl']} where `uid`='{$login_user['uid']}'";
        $total2 = $nc_list->getDataBySql($sql2);

        api_result(0, 'succ',array('users'=>$total[0]['total']?$total[0]['total']:0,'money'=>$total2[0]['yongjin']?$total2[0]['yongjin']:0));

    }

    public function inviteMoney(){
        $base = api_check_base();
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'money');
        $page = pstr('page');
        $page = $page?$page:1;
        $start = ((int)$page-1)*10;
        $sql2 = "select `desc`,`money`,`ut` from {$nc_list->dbConf['tbl']} where `uid`={$login_user['uid']} and `desc` like '佣金%' and `desc` != '公盘返现' order by `ut` desc limit $start,10";
        $total2 = $nc_list->getDataBySql($sql2);
        $data = array();
        foreach($total2 as $v){
            $_t = explode(':',$v['desc']);
            $data[] = array(
                'date' => date('Y-m-d',$v['ut']),
                'user' => $v['money'] < 0 ? ($_t[2]==''?'佣金消费':'佣金提现') : urldecode($_t[2]),
                'money' => $_t[1],
                'invite_money' => $v['money'],
            );
        }
        api_result(0, 'succ',$data);

    }

    public function inviteUser(){
        $base = api_check_base();
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('main', 'user');
        $where = array(
            'rebate_uid' => $login_user['uid'],
        );
        $ret = $nc_list->getDataList($where,array('uid','nick','rt'));
        $data = array();
        $one = array();
        foreach($ret as $v){
            $one[] = $v['uid'];
            $v['level'] = '1级';
            $data[] = $v;
        }
        //level 2
        $where = array(
            'rebate_uid' => array($one,'in'),
        );
        $ret2 = $nc_list->getDataList($where,array('uid','nick','rt'),array(),array(),false);
        if(empty($ret2)){
            api_result(0, 'succ',$data);
        }
        //level 3
        $two = array();
        foreach($ret2 as $v2){
            $two[] = $v2['uid'];
            $v2['level'] = '2级';
            $data[] = $v2;
        }
        $where = array(
            'rebate_uid' => array($two,'in'),
        );
        $ret3 = $nc_list->getDataList($where,array('uid','nick','rt'),array(),array(),false);
        if(empty($ret3)){
            api_result(0, 'succ',$data);
        }
        foreach($ret3 as $v3){
            $v3['level'] = '3级';
            $data[] = $v3;
        }
        api_result(0, 'succ',$data);
    }

    public function money(){
        $base = api_check_base();
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('main', 'user');
        $where = array(
            'uid' => $login_user['uid'],
        );
        $ret = $nc_list->getDataOne($where,array('money','yongjin'));
        $ret['money'] = bcadd($ret['money'],$ret['yongjin'],2);
        api_result(0, 'succ',$ret);
    }

    public function lottery(){
        $base = api_check_base();
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'lottery');
        $column = array('id','name');
        $ret3 = $nc_list->getDataList(array(),$column,array(),array(),false);

        $nc_list->setDbConf('shop', 'point_rule');
        $where = array(
            'type' => '抽奖',
        );
        $ret = $nc_list->getDataOne($where,array('point'));


        api_result(0, 'succ',array('data'=>$ret3,'point'=>$ret['point']));
    }

    public function lotteryRun(){
    	api_result(1, '活动已终止');
        $base = api_check_base();
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'point_rule');
        $where = array(
            'type' => '抽奖',
        );
        $ret = $nc_list->getDataOne($where,array('point'));

        //听说要每天免费一次哦，加就加吧，哎，这年头奇葩好多
        $nc_list->setDbConf('shop', 'lottery_record');
        $date = date('Ymd');
        $sql = "select count(*) `total` from {$nc_list->dbConf['tbl']} where `uid`={$login_user['uid']} and from_unixtime(`ut`,'%Y%m%d')='{$date}'";
        $r = $nc_list->getDataBySql($sql,false);

        if(1){ //$r[0]['total']>0 原来是每一天免费1次现在不免费
            $nc_list->setDbConf('shop', 'point');

            $sql = "update {$nc_list->dbConf['tbl']} set `point`=`point`-{$ret['point']},`use`=`use`+{$ret['point']} where `uid`={$login_user['uid']} and `point`>={$ret['point']}";
            $nc_list->executeSql($sql);
            $total = $nc_list->getDataBySql("select row_count() as total",false);
            if($total[0]['total']<1){
                api_result(1, '积分不足');
            }
            $nc_list->setDbConf('shop', 'point_detail');
            $insert = array(
                'uid' => $login_user['uid'],
                'desc' => '抽奖消耗',
                'ut' => time(),
                'point' => 0-$ret['point'],
            );
            $nc_list->insertData($insert);
        }

        //lottery
        $nc_list->setDbConf('shop', 'lottery');
        $ret3 = $nc_list->getDataList(array(),array(),array(),array(),false);
        $arr = array();
		$ret4 = array();
		foreach ($ret3 as $val) {
			$arr[$val['id']] = $val['percent'];
			$ret4[$val['id']] = $val;
		}

		$rid = $this->get_rand($arr); //根据概率获取奖项id
		$res = $ret4[$rid]['id']; //中奖项

		if($ret4[$rid]['type']==0){
			//积分
			$nc_list->setDbConf('shop', 'point_detail');
            $time = time();
			$insert = array(
				'uid' => $login_user['uid'],
				'desc' => '抽奖奖品',
				'ut' => time(),
				'point' => $ret4[$rid]['point'],
			);
			$nc_list->insertData($insert);
			$nc_list->setDbConf('shop', 'point');
			$sql = "insert into {$nc_list->dbConf['tbl']} values(null,{$login_user['uid']},{$ret4[$rid]['point']},{$ret4[$rid]['point']},0,{$time}) on duplicate key update `point`=`point`+{$ret4[$rid]['point']},`total`=`total`+{$ret4[$rid]['point']}";
			$nc_list->executeSql($sql);
		}
		//抽奖记录
		$insert = array(
			'uid' => $login_user['uid'],
			'point' => $ret4[$rid]['point'],
			'name' => $ret4[$rid]['name'],
			'ut' => time(),
			'send' => $ret4[$rid]['type']==0 ? 1 : 0,
		);
        $nc_list->setDbConf('shop', 'lottery_record');
        $nc_list->insertData($insert);

        api_result(0, 'succ',array('id' => $res));

    }
    private function get_rand($proArr) {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);

        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);

        return $result;
    }

    public function lotteryList(){
        $base = api_check_base();
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'lottery_record');
        $where = array(
            'uid' => $login_user['uid'],
        );
        $page = pstr('page');
        $page = $page<1 ? 1 : (int)$page;
        $order = array(
            'ut' => 'desc'
        );
        $limit = array(
            'begin' => ($page-1)*10
        );
        $ret3 = $nc_list->getDataList($where,array(),$order,$limit,false);
        api_result(0, 'succ',array('id' => $ret3));

    }

    public function lotteryList2(){
        $base = api_check_base();
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'lottery_record');
        $where = array(
            'uid' => $login_user['uid'],
            'point' => 0,
        );
        $order = array(
            'ut' => 'desc'
        );
        $validate_cfg = array(
            'from' => array(
            ),
            'count' => array(
            ),
        );
        $ipt_list = api_get_posts($base['appid'], $validate_cfg);
        $limit = array(
            'begin' => (int)$ipt_list['form'],
            'limit' => (int)$ipt_list['count'],
        );
        $ret3 = $nc_list->getDataList($where,array(),$order,$limit,false);
        if(!empty($ret3)){
            foreach($ret3 as &$v){
                $d = explode(':',$v['kuaidi']);
                $v['kuaidi'] = $d[0].' '.$d[1];
            }
        }
        api_result(0, 'succ',$ret3);
    }

    public function checkReceive2(){
        // 调用测试用例
        //$this->test_check_receive();

        $base = api_check_base();

        // 得到当前登录用户
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);

        $validate_cfg = array(
            'id' => array(
                'api_v_numeric|1||id不合法',
            )
        );
        $ipt_list = api_get_posts($base['appid'], $validate_cfg);

        $nc_list = Factory::getMod('nc_list');
        $where = array(
            'uid' => $login_user['uid'],
            'id' => $ipt_list['id']
        );
        $nc_list->setDbConf('shop', 'lottery_record');
        $ret = $nc_list->getDataOne($where, array(), array(), array(), false);
        if(empty($ret)){
            api_result(9, '没有权限');
        }

        $where = array(
            'id' => $ipt_list['id']
        );
        $data = array(
            'send' => 2,
            'ut' => time()
        );
        $ret = $nc_list->updateData($where, $data);
        if($ret){
            api_result(0, '修改成功');
        }
        api_result(1, '修改失败');
    }

    public function lotteryAddress(){
        $base = api_check_base();
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);

        $validate_cfg = array(
            'id' => array(
                'api_v_notnull||id不能为空',
            ),
            'address' => array(
                'api_v_notnull||地址不能为空',
            ),
            'receive' => array(
                'api_v_notnull||收件人不能为空',
            ),
            'phone' => array(
                'api_v_notnull||电话不能为空',
            ),
        );

        $ipt_list = api_get_posts($base['appid'], $validate_cfg);

        $ipt_list = api_safe_ipt($ipt_list);

        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'lottery_record');
        $where = array(
            'uid' => $login_user['uid'],
            'id' => $ipt_list['id'],
        );
        $ret3 = $nc_list->getDataList($where);
        $update = array(
            'address' => $ipt_list['address'],
            'receive' => $ipt_list['receive'],
            'phone' => $ipt_list['phone'],
        );
        $nc_list->updateData($where,$update);
        api_result(0, '修改成功');

    }

    public function banner(){
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'banner');

        $base = api_check_base(); 
		$validate_cfg = array(
			'type' => array(), 
		); 
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);

        $where = array(
            'is_del' => 0,
            'state' => 1,
        ); 
        $where['type']=gint('type')==2?2:1;
        
        $order = array(
            'sort' => 'desc',
        );
        $ret3 = $nc_list->getDataList($where,array('img','goods_id','url'),$order);
        $nc_list->setDbConf('shop', 'activity');
        foreach($ret3 as &$v){
            if($v['url']){
                $v['href'] = $v['url'];
                continue;
            }
            
            if($v['goods_id']){
                $ret4 = $nc_list->getDataOne(array('goods_id'=>$v['goods_id']),array('activity_id'),array('activity_id'=>'desc'),array(),false);
                if($ret4['activity_id']){
                    $v['href'] = '#/activity/'.$ret4['activity_id'];
                }
            }
        }
        api_result(0, 'succ',$ret3);
    }

    public function pcbanner(){
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'pcbanner');
        $where = array(
            'is_del' => 0,
            'state' => 1,
        );
        $order = array(
            'sort' => 'desc',
        );
        $ret3 = $nc_list->getDataList($where,array('img','goods_id','url'),$order);
        $nc_list->setDbConf('shop', 'activity');
        foreach($ret3 as &$v){
            if($v['url']){
                $v['href'] = $v['url'];
                continue;
            }

            if($v['goods_id']){
                $ret4 = $nc_list->getDataOne(array('goods_id'=>$v['goods_id']),array('activity_id'),array('activity_id'=>'desc'),array(),false);
                if($ret4['activity_id']){
                    $v['href'] = '#!/goods/'.$ret4['activity_id'];
                }
            }
        }
        api_result(0, 'succ',$ret3);
    }

    public function cashRecord(){
        $base = api_check_base();
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $page = gint('page');
        $page = $page < 1 ? 1 : $page;

        $limit = array(
            'begin' => ($page-1)*10,
            'length' => 10,
        );

        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'money');

        $sql2 = "select `ut`,`money`,`desc` from {$nc_list->dbConf['tbl']} where `uid`={$login_user['uid']} and `desc` like '佣金%' and `money`<0 order by `ut` desc limit {$limit['begin']},{$limit['length']}";
        $ret3 = $nc_list->getDataBySql($sql2);
        $re = array();
        if($ret3){
            foreach($ret3 as $rt){
                $desc = explode(':',$rt['desc']);
                if($desc[2]==''){
                    continue;
                }
                $re[] = array(
                    'state' => $desc[3],
                    'weixin_id' => urldecode($desc[2]),
                    'money' => abs($rt['money']),
                    'ut' => $rt['ut'],
                );
            }
        }
        api_result(0, 'succ',$re);
    }

    public function cash(){
        $base = api_check_base();
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true); 
        $validate_cfg = array(
          /*  'weixin_id' => array(
                'api_v_notnull||微信号不能为空',
            ),*/
            'money' => array(
                'api_v_notnull||提现金额不能为空',
            ),
        );
        if($login_user['type']!=1){ 
        	$validate_cfg['weixin_id']=array(
                'api_v_notnull||微信号不能为空',
            );
        } 
        $ipt_list = api_get_posts($base['appid'], $validate_cfg);
        $nc_list = Factory::getMod('nc_list');
   
        /*$nc_list->setDbConf('shop', 'money');
        $sql2 = "select sum(`money`) `total` from {$nc_list->dbConf['tbl']} where `uid`={$login_user['uid']} and `desc` like '佣金%'";
        $total2 = $nc_list->getDataBySql($sql2);*/
        
        if(floatval($ipt_list['money'])<1){
            api_result(1, '提现金额不能小于1');
        }

        if(!is_int($ipt_list['money']+0)){
            api_result(1, '提现金额必须是整数');
        }
        if(floatval($ipt_list['money']) > floatval($login_user['yongjin'])){
            api_result(1, '提现金额不能超过可用余额');
        } 
        $ipt_list['money'] = intval($ipt_list['money']); 

        $insert = array(
            'uid' => $login_user['uid'],
            'money' => $ipt_list['money'],
           	'type'=>1, //目前只支持微信提现
            'ut' => time(),
            'rt'=>time(),
            'status'=>1,
            'order_num'=>date('YmdHis').(10000000000 + $login_user['uid'])
            
        );
        $nc_list->setDbConf('main', 'cash');
        $ret=$nc_list->insertData($insert);  
        if($ret){
        	api_result(0, 'succ');
        }else{
        	api_result(1, '服务器繁忙,申请提现失败');

        }
         
    }

    public function luckyLottery(){
        $base = api_check_base();
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'lottery_record');

        $page = pint('page');
        $each = pint('each');
        $page = $page < 1 ? 1 : $page;
        $each = $each < 1 ? 10 : $each;
        $each = $each > 50 ? 50 : $each;
        $limit = array(
            'begin' => ($page-1)*$each,
            'length' => $each,
        );

        $sql = "select a.uid,a.name as goods_name,a.ut,b.nick from {$nc_list->dbConf['tbl']} a,".DATABASE.".t_user b where a.uid=b.uid order by a.ut desc limit {$limit['begin']},{$limit['length']}";
        $res = $nc_list->getDataBySql($sql);
        api_result(0,'succ',$res);
    }
    
    public function yijian(){
        $base = api_check_base();
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $yijian = pstr('yijian');
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'yijian');
        $data = array(
            'uid' => $login_user['uid'],
            'yijian' => $yijian,
            'ut' => time(),
        );
        $nc_list->insertData($data);
        api_result(0,'成功');
    }
    
    
    public function changePoint(){
        $base = api_check_base();
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $validate_cfg = array(
            'point' => array(
                'api_v_notnull||point不能为空',
            ),
        );
        $ipt_list = api_get_posts($base['appid'], $validate_cfg);
        $ipt_list['point'] = intval($ipt_list['point']);
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'point');
        $where = array(
            'uid' => $login_user['uid'],
        );
        $res = $nc_list->getDataOne($where,array('point'),array(),array(),false);
        if($res['point']<$ipt_list['point']){
            api_result(1, '积分不足');
        }
        if($ipt_list['point']%100!=0){
            api_result(1, '积分只能为100的整数倍');
        }
        if($ipt_list['point']<1){
            api_result(1, '错误输入');
        }
        $time = time();
        $p = 0-$ipt_list['point'];
        $nc_list->setDbConf('shop', 'point_detail');
        $nc_list->insertData(array(
            'uid' => $login_user['uid'],
            'desc' => '兑换',
            'point' => $p,
            'ut' => $time,
        ));
        $nc_list->setDbConf('shop', 'point');
        $sql = "update {$nc_list->dbConf['tbl']} set `point`=`point`-{$ipt_list['point']},`use`=`use`+{$ipt_list['point']},`ut`={$time} where `uid`='{$login_user['uid']}'";
        $nc_list->executeSql($sql);
        //zengjiayuer

        $money = bcdiv($ipt_list['point'],100,2);

        $insert = array(
            'uid' => $login_user['uid'],
            'money' => $money,
            'desc' => '积分兑换:'.$money,
            'ut' => time(),
            'appid' => '10002',
        );
        $nc_list->setDbConf('shop', 'money');
        $nc_list->insertData($insert);

        $nc_list->setDbConf('main', 'user');
        $sql = "update {$nc_list->dbConf['tbl']} set `money`=`money`+{$money} where `uid`={$login_user['uid']}";
        $nc_list->executeSql($sql);
        api_result(0, '兑换成功');

    }
     //利用session去做
     public function getredpacket(){ 
       /* session_start();  
        if(!isset($_SESSION['wxregister_first'])){
        	api_result(1, '你已经获取红包或活动已结束');
        }*/
        $base = api_check_base(); 
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
 	  	$nc_list = Factory::getMod('nc_list'); 
        $nc_list->setDbConf('main', 'user');
        $sql = "update {$nc_list->dbConf['tbl']} set `money`=`money`+1,`packet`=1 where `uid`={$login_user['uid']} and packet=0"; 
       // echo $sql;
        $result=$nc_list->executeSql($sql); 
        //$result=1;//注册默认送1快 这里就不送钱了
        if (!$result) {
			api_result(1, '你已经获取红包或活动已结束');
		}
		//unset($_SESSION['wxregister_first']);
        api_result(0,'成功');
    }
    public function bindphone(){
    	$base = api_check_base();
        $validate_cfg = array(
				'phone' => array(
						'api_v_mobile||用户名必须是手机号码',
						//'api_unique|main,user,uid||手机号已存在',暂时允许有相同手机号码
				) 
		); 
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		// 判断手机验证码
		$mcode = pstr('mcode'); 
        $cache_ary = do_cache('get', 'mcode', $ipt_list['phone']);
        
        if(time()-$cache_ary['last']>10*60){
            api_result(8, '验证码已失效');
        }  
		if ($mcode != $cache_ary['code']) {
			api_result(8, '验证码错误');
		} 
		// 删除手机验证码缓存
		do_cache('delete', 'mcode', $ipt_list['phone']);  
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		$nc_list = Factory::getMod('nc_list'); 
        $nc_list->setDbConf('main', 'user');
        $sql = "update {$nc_list->dbConf['tbl']} set `phone`={$ipt_list['phone']} where `uid`={$login_user['uid']}"; 
        $result=$nc_list->executeSql($sql); 
        if($login_user['rebate_uid'] && $login_user['type']==1){ //微信用户才有
        	$sql = "update {$nc_list->dbConf['tbl']} set `lucky_packet`=`lucky_packet`+1 where `uid`={$login_user['rebate_uid']}"; 
        	$result=$nc_list->executeSql($sql);  
        	$msg_mod = Factory::getMod('msg'); 
	        $content=json_encode(array('invite_nick'=> $login_user['nick'],'goods_name'=>''));
	        $msg_mod->sendPacketNotify($login_user['rebate_uid'],1,1,$content,'','','',$login_user['uid']);
        }
        //判断是否送福袋

        if (!$result) {
			api_result(1, '数据库异常,请重试');
		}
		api_result(0,'成功');
    }
    public function lotteryRun12(){  	 //双十二活动代码
    	api_result(1, '抽奖次数用完'); 
    	exit;
    	$base = api_check_base();
    	$validate_cfg = array(
			'share' => array()			 
		);
        api_result(1, ' 抽奖活动已结束~!'); 
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		 
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);   
      	$nc_list = Factory::getMod('nc_list'); 
        $nc_list->setDbConf('main', 'wxuser');
        $where = array(
            'uid' => $login_user['uid']          
        );
        $nc_list->setDbConf('main', 'user');
        $ret = $nc_list->getDataOne($where, array('packet'), array(), array(), false);
        if($ret['packet']==2){
        	 api_result(1, '抽奖次数用完'); 
        }
        if($ret['packet']==1){       
        	 if($ipt_list['share']!=1){
        	 	api_result(1, '分享抽奖信息就可以再次抽奖噢!'); 
        	 }
        	  
        }

        if($ret['packet']==0){  //表示未抽奖过
        	$nc_list->setDbConf('main', 'wxuser'); 
        	$ret = $nc_list->getDataOne($where, array('wx_openid'), array(), array(), false);

	        if(!empty($ret)){
	        	require COMMON_PATH.'libs/wxpay/Wx.Api.php';	
			    $wxApi = new WxApi();     
			    $wxmsg = $wxApi->getsubscribe($ret['wx_openid']);
			    $status=json_decode($wxmsg,true);

			    if(isset($status['errcode'])){
			    	api_result(1,'当前用户不是微信用户');
			    } 
			    if($status['subscribe']==0){
			    	api_result(1,'当前用户未关注公众号');
			    }
			    /*if($status['subscribe']==1){
			    	 // 关注公众号
			    }*/		    	 
			     
	        }else{
	        	 api_result(1,'当前用户不是微信用户');
	        }
        }
         
        $nc_list->setDbConf('main', 'user');
        $sql = "update {$nc_list->dbConf['tbl']} set  `packet`=`packet`+1 where `uid`={$login_user['uid']} ";        
        $result=$nc_list->executeSql($sql); 

       

        //lottery
        $nc_list->setDbConf('shop', 'lottery');
        $ret3 = $nc_list->getDataList(array(),array(),array(),array(),false);
        $arr = array();
		$ret4 = array();
		foreach ($ret3 as $val) {
			$arr[$val['id']] = $val['percent'];
			$ret4[$val['id']] = $val;
		}

		$rid = $this->get_rand($arr); //根据概率获取奖项id
		$res = $ret4[$rid]['id']; //中奖项

		if($ret4[$rid]['type']==0){
			//积分
			$nc_list->setDbConf('shop', 'point_detail');
            $time = time();
			$insert = array(
				'uid' => $login_user['uid'],
				'desc' => '抽奖奖品',
				'ut' => time(),
				'point' => $ret4[$rid]['point'],
			);
			$nc_list->insertData($insert);
			$nc_list->setDbConf('shop', 'point');
			$sql = "insert into {$nc_list->dbConf['tbl']} values(null,{$login_user['uid']},{$ret4[$rid]['point']},{$ret4[$rid]['point']},0,{$time}) on duplicate key update `point`=`point`+{$ret4[$rid]['point']},`total`=`total`+{$ret4[$rid]['point']}";
			$nc_list->executeSql($sql);
		}

		if($ret4[$rid]['type']==2){
			//金钱
			$nc_list->setDbConf('shop', 'point_detail');
            $time = time();
			$insert = array(
				'uid' => $login_user['uid'],
				'desc' => '双12活动抽奖金额',
				'ut' => time(),
				'point' => $ret4[$rid]['point'],
			);
			$nc_list->insertData($insert);	 
			$nc_list->setDbConf('main', 'user');
            $sql = "update {$nc_list->dbConf['tbl']} set `money`=`money`+{$ret4[$rid]['point']} where `uid`={$login_user['uid']}";
			$nc_list->executeSql($sql);
		}
		//抽奖记录
		$insert = array(
			'uid' => $login_user['uid'],
			'point' => $ret4[$rid]['point'],
			'name' => $ret4[$rid]['name'],
			'ut' => time(),
			'send' => $ret4[$rid]['type']==0 || $ret4[$rid]['type']==2 ? 1 : 0,
		);
        $nc_list->setDbConf('shop', 'lottery_record');
        $nc_list->insertData($insert);

        api_result(0, 'succ',array('id' => $res,'type'=>$ret4[$rid]['type']));




    }


    public function christmaswish(){

    	$base = api_check_base(); 
    	$validate_cfg = array(
            'present' => array(),
            'place' => array(),
            'talk' => array(),
        );
        $ipt_list = api_get_posts($base['appid'], $validate_cfg);
       
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
	 
        $time=time();
      	$nc_list = Factory::getMod('nc_list');   
        $nc_list->setDbConf('main', 'christmas'); 
        $sql = "insert into {$nc_list->dbConf['tbl']} (`uid`,`present`,`place`,`talk`,`ut`) values({$login_user['uid']},'{$ipt_list['present']}','{$ipt_list['place']}','{$ipt_list['talk']}',{$time}) on duplicate key update `present`='{$ipt_list['present']}',`place`='{$ipt_list['place']}',`ut`={$time},`talk`='{$ipt_list['talk']}'";
        $nc_list->executeSql($sql); 
        
    	api_result(0, 'succ');

    }
    public function getchristmasWish(){
    	$user=pstr('userid')+0;
    	$nc_list = Factory::getMod('nc_list');   
        $nc_list->setDbConf('main', 'christmas'); 
        $sql = " select  present,place,talk from   {$nc_list->dbConf['tbl']} where uid=$user"; 
        $re = $nc_list->getDataBySql($sql,false);
        if(!empty($re)){
        	$re[0]['invite_code']=api_get_user_invite_code($user);
        } 
    	api_result(0, 'succ',$re);

    }
    public function bindshare(){  
    	$base = api_check_base(); 
    	$validate_cfg = array(
            'goodsid' => array('api_v_numeric|1||商品id不合法'),
            'type' => array(), 
            'invite_code'=> array('api_v_notnull||邀请码不能为空'), 
             
         );  
 		 $ipt_list = api_get_posts($base['appid'], $validate_cfg);
		 $login_user = app_get_login_user($base['sessid'], $base['appid']);  
		 if(!$login_user){ api_result(0, 'long_err');} 
	     $rebate_uid = api_decode_invite_code($base['appid'], $ipt_list['invite_code']); 
	 	 if(!$rebate_uid){  api_result(0, 'invite_error');}
		 $nc_list = Factory::getMod('nc_list');   
		 $nc_list->setDbConf('main', 'activity_person');   
		 $$ipt_list['type']=$ipt_list['type']+0;
		 $where = array(
			'uid' =>  $rebate_uid,
			'type'=>$ipt_list['type'],
			'pid'=>  $login_user['uid'],
			'goods_id'=>$ipt_list['goodsid']
		 ); 
		 $person = $nc_list->getDataOne($where, array('uid'),array(),array(),false);	 //不能互相推荐	
		 if($person){
			  api_result(0, 'invite_error');
	     }

         $where = array(
			'uid' => $login_user['uid'],
			'type'=>$ipt_list['type'],
			'pid'=>$rebate_uid,
			'goods_id'=>$ipt_list['goodsid']
		 );
		 $column = array(
			'uid'
		 );  
	    $person = $nc_list->getDataOne($where, $column,array(),array(),false);	 
	    if(!$person && $login_user['uid']!=$rebate_uid && $rebate_uid){    		    	 
	    	 $insert = array(
            'uid' => $login_user['uid'],
            'pid' => $rebate_uid,
            'goods_id'=>$ipt_list['goodsid'],
            'ut' => time(),
            'type'=>$ipt_list['type'],
              );  
            $nc_list->insertData($insert);  
       
	    }
		api_result(0, 'succ');
    }

}