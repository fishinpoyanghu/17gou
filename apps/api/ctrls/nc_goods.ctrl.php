<?php
/**
 * ninecent 商品api
 */

class NcGoodsCtrl extends BaseCtrl {


	
	public function detail() {
		
		// 调用测试用例
// 		$this->test_detail();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], false);
		
		$activity_id = pint('activity_id');
		
		if (!$activity_id) {
			api_result(5, 'activity_id不合法');
		}
		
		$pub_mod = Factory::getMod('pub');
		
		// 取得云客活动
		$pub_mod->init('shop', 'activity', 'activity_id');
		$activity = $pub_mod->getRow($activity_id);
		
		if (!$activity) {
			api_result(2, '云客活动不存在');
		}
		
		// 取得活动对应的商品
		$pub_mod->init('shop', 'goods', 'goods_id');
		$goods = $pub_mod->getRow($activity['goods_id']);
        require(dirname(dirname(__FILE__)).'/mods/ip.mod.php');

        if (!$goods) {
			api_result(2, '云客活动的商品不存在');
		}
		
		$data = array(
			'activity_id' => intval($activity['activity_id']),
			'goods_id' => intval($goods['goods_id']),
            'goods_img' => explode(',', $goods['title_img']),
            'goods_category'=>intval($goods['goods_type_id']),
            'img' => $goods['main_img'],
			'goods_title' => ap_strval($goods['title']),
			'goods_subtitle' => ap_strval($goods['sub_title']),
			'activity_type' => intval($goods['activity_type']),
			'status' => intval($activity['flag']),
			'need_num' => intval($activity['need_num']),
			'remain_num' => intval($activity['need_num'] - $activity['user_num']),
		);
		
		if ($data['remain_num'] < 0) $data['remain_num'] = 0;
		
		// 如果status=1 即将揭晓
		if ($data['status'] == 1) {
			$data['remain_time'] = $activity['publish_time'] - time();
			if ($data['remain_time'] < 0) $data['remain_time'] = 0;
		}
		
		// 如果status=2 已经揭晓
		elseif ($data['status'] == 2) {

                // 从t_lucky_num获得开奖的记录
                $pub_mod->init('shop', 'lucky_num', 'lucky_num_id');

                $where = array(
                    'activity_id' => $activity['activity_id'],
                    'stat' => 0,
                );

                $lucky_num = $pub_mod->getRowWhere($where);

                // 这里，可以考虑加个错误处理
                // @todo
            $ip = new LibIp();
                if ($lucky_num) {

                    $concat_keys = array(
                        'nick' => 'unick',
                        'icon' => 'uicon',
                        'ip' => 'uip',
                    );
                    if($lucky_num['uid']==0){
                    	$lucky_num['unick']='(无)本期无获奖者';
                    }
                    $list_mod = Factory::getMod('list');
                    $lucky_num_list = $list_mod->concatTbl($base['appid'], array($lucky_num), 'uid', 'main', 'user', 'uid', $concat_keys);
                    $lucky_num = $lucky_num_list[0];
                    $icon_info = ap_user_icon_url($lucky_num['uicon']);
                    $data['lucky_uid'] = intval($lucky_num['uid']);
                    $data['lucky_unick'] = ap_strval($lucky_num['unick']);
                    $data['lucky_uicon'] = ap_strval($icon_info['icon']);
                    $data['lucky_user_num'] = intval($lucky_num['user_num']);
                    $data['lucky_num'] = intval($lucky_num['lucky_num']);
                    $data['lucky_ip'] = $ip->getlocation(long2ip($lucky_num['uip']));
                    $data['publish_time'] = date('Y-m-d H:i:s', $lucky_num['ut']);
                }
		}
		
		//获取新一期的活动id
		if($data['status'] > 0){
			$pub_mod->init('shop', 'activity', 'activity_id');
				
			$where = array(
				'appid' => $base['appid'],
				'flag' => 0,
				'goods_id' => $data['goods_id']
			);
			$activityInfo = $pub_mod->getRowWhere($where);
			$data['new_activity_id'] = intval($activityInfo['activity_id']);
		}
		
		// 加上当前用户参与的次数
		if ($login_user['uid'] > 0) {
			$where = array(
					'appid' => $base['appid'],
					'uid' => $login_user['uid'],
					'activity_id' => $activity['activity_id'],
			);

			$pub_mod->init('shop', 'activity_num', 'activity_num_id');
			$activity_num_list = $pub_mod->getRowList($where, 0, 12);

            $pub_mod->init('shop', 'activity_user', 'activity_user_id');
            $count = $pub_mod->getRowWhere($where);

            $data['user_num'] = $count['user_num'];
			$data['user_lucky_num'] = array();

			foreach ($activity_num_list as $activity_num) {
				$data['user_lucky_num'][] = $activity_num['activity_num'];
			}
		}
		else {
			$data['user_num'] = 0;
			$data['user_lucky_num'] = array();
		}

        //
        $where = array(
            'a.goods_id' => $activity['goods_id'],
        );
        $column = array(
            'a.activity_id','a.lucky_num','a.user_num','b.nick','b.ip','b.icon','a.uid','a.ut'
        );
        //连表配置
        $join = array(
            'from' => DATABASE.'.t_lucky_num a',
            'join' => array(
                array(
                    'join_type' => 'left join',
                    'join_table' => DATABASE.'.t_user b',
                    'on' => array(
                        'a.uid=b.uid'
                    )
                )
            )
        );

        $nc_list_mod = Factory::getMod('nc_list');
        $nc_list_mod->setDbConf('shop', 'lucky_num');
        $luckyData = $nc_list_mod->getDataJoinTable($join, $where, $column,array('a.lucky_num_id' => 'desc'),array('begin'=>0,'length'=>4),false);
        $ip = new LibIp();
        if($luckyData[0]['uid']==0){
            $icon_info = ap_user_icon_url($luckyData[1]['icon']);
            $result['lucky_num'] = $luckyData[1]['lucky_num'];
            $result['lucky_icon'] = $icon_info['icon'];
            $result['lucky_unick'] = $luckyData[1]['nick'];
            $result['lucky_user_num'] = $luckyData[1]['user_num'];
            $result['lucky_ip'] = $ip->getlocation(long2ip($luckyData[1]['ip']));
            $result['activity_id'] = $luckyData[1]['activity_id'];
            $result['publish_time'] = $luckyData[1]['ut'];
            $data['last'] = $result;
        }else{
            $icon_info = ap_user_icon_url($luckyData[0]['icon']);
            $result['lucky_num'] = $luckyData[0]['lucky_num'];
            $result['lucky_icon'] = $icon_info['icon'];
            $result['lucky_unick'] = $luckyData[0]['nick'];
            $result['lucky_user_num'] = $luckyData[0]['user_num'];
            $result['lucky_ip'] = $ip->getlocation(long2ip($luckyData[0]['ip']));
            $result['activity_id'] = $luckyData[0]['activity_id'];
            $result['publish_time'] = $luckyData[0]['ut'];

            $data['last'] = $result;
        }
        $resultlist=array(); //页面上期揭晓信息需要4条数据需求 若不需要就注释以下方法。
        foreach($luckyData as $k=>$v){
			$resultlist[$k]['lucky_icon']=ap_user_icon_url($luckyData[$k]['icon']);  
			$resultlist[$k]['lucky_ip']=$ip->getlocation(long2ip($luckyData[$k]['ip']));
			$resultlist[$k]['lucky_num'] = $luckyData[$k]['lucky_num'];
			$resultlist[$k]['lucky_unick'] = $luckyData[$k]['nick'];
			$resultlist[$k]['lucky_user_num'] = $luckyData[$k]['user_num'];
			$resultlist[$k]['activity_id'] = $luckyData[$k]['activity_id'];
			$resultlist[$k]['publish_time'] = $luckyData[$k]['ut'];      
			$resultlist[$k]['luck_uid'] = $luckyData[$k]['uid'];   
        }
        $data['lastlist']=$resultlist;
		api_result(0, 'succ', $data);
	}
	
	public function imgDetail() {
		
		// 调用测试用例
// 		$this->test_img_detail();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], false);
		
		$goods_id = pint('goods_id');
		
		if (!$goods_id) {
			api_result(5, 'goods_id不合法');
		}
		
		$pub_mod = Factory::getMod('pub');
		
		// 取得云客活动
		$pub_mod->init('shop', 'goods', 'goods_id');
		$goods = $pub_mod->getRow($goods_id);
		
		if (!$goods_id) {
			api_result(2, '商品不存在');
		}
		
		$data = array(
				'html' => ap_strval($goods['detail']),
		);
		
		api_result(0, 'succ', $data);
	}
	
	public function joinList() {
		
		// 调用测试用例
// 		$this->test_join_list();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], false);
		
		$validate_cfg = array(
				'activity_id' => array(
						'api_v_numeric|1||activity_id不合法',
				),
		);		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		$list_mod = Factory::getMod('list');
		$plist = $list_mod->getPageCond();

		$pub_mod = Factory::getMod('pub');
		
		// 判断activity_id存不存在
		$pub_mod->init('shop', 'activity', 'activity_id');
		$activity = $pub_mod->getRow($ipt_list['activity_id']);
		
		if (!$activity) {
			api_result(2, '云客活动不存在');
		}
		
		// 获得参与记录
		$where = array(
				'appid' => $base['appid'],
				'activity_id' => $ipt_list['activity_id'],
		);
		$orderby = ' ORDER BY activity_num_id DESC';
		
		$pub_mod->init('shop', 'activity_num', 'activity_num_id');
		$activity_user_list = $pub_mod->getRowList($where, $plist['from']-1, $plist['count'], $orderby,'distinct(order_num),uid,ip,ut,rt,ms,this_num');
		
		if (false === $activity_user_list) {
			api_result(1, '数据库错误，请稍后重试');
		}
		
		$concat_keys = array(
				'nick' => 'unick',
				'icon' => 'uicon',
            'ip' => 'uip',
		);
		$activity_user_list = $list_mod->concatTbl($base['appid'], $activity_user_list, 'uid', 'main', 'user', 'uid', $concat_keys);
		$pub_mod->init('shop', 'goods', 'goods_id');
		$goodtype=$pub_mod->getRowWhere(array('goods_id'=>$activity['goods_id']));
		   
		$data = array();
        require(dirname(dirname(__FILE__)).'/mods/ip.mod.php');
        $ip = new LibIp();
       
		foreach ($activity_user_list as $k=>$activity_user) {
			
			$icon_info = ap_user_icon_url($activity_user['uicon']);
			
			$data[$k] = array(
					'uid' => intval($activity_user['uid']),
					'unick' => ap_strval($activity_user['unick']),
					'uicon' => ap_strval($icon_info['icon']),
					'user_num' => intval($activity_user['this_num']),
					'join_time' => date('Y-m-d H:i:s', $activity_user['rt']).'.'.$activity_user['ms'],
                    'ip' => $ip->getlocation(long2ip($activity_user['uip'])),
			);
		/*	if($goodtype['activity_type']==6){
				$pub_mod->init('shop', 'order','order_id');
				$msg=$pub_mod->getRowList(array('order_num'=>$activity_user['order_num'],'flag'=>1),0,2,'','order_info');	//	var_dump($msg);exit;		  
		  	    $moneyInfo = json_decode($msg[0]['order_info'], true);
		        $buytype=$moneyInfo[0]['hot_luckyBuy']; 		
		        $data[$k]['orderinfo']=$buytype;		  
			}*/
			 
		}
		
		api_result(0, 'succ', $data);
	}
	
	public function historyList() {
		
		// 调用测试用例
// 		$this->test_history_list();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], false);
		
		$validate_cfg = array(
				'goods_id' => array(
						'api_v_numeric|1||goods_id不合法',
				),
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$list_mod = Factory::getMod('list');
		$plist = $list_mod->getPageCond();
		
		$pub_mod = Factory::getMod('pub');
		
		// 判断goods_id存不存在
		$pub_mod->init('shop', 'goods', 'goods_id');
		$goods = $pub_mod->getRow($ipt_list['goods_id']);
		
		if (!$goods) {
			api_result(2, '商品不存在');
		}
		
		// 获得往期揭晓
		$where = array(
				'appid' => $base['appid'],
				'goods_id' => $ipt_list['goods_id'],
				'stat' => 0,
		);
		$orderby = 'ORDER BY lucky_num_id DESC';
		
		$pub_mod->init('shop', 'lucky_num', 'lucky_num_id');
		$lucky_num_list2 = $pub_mod->getRowList($where, $plist['from']-1, $plist['count'], $orderby);
		
		if (false === $lucky_num_list2) {
			api_result(1, '数据库错误，请稍后重试');
		}
        $lucky_num_list = array();
        foreach($lucky_num_list2 as $lucky){
            if($lucky['lucky_num']>0){
                $lucky_num_list[] = $lucky;
            }
        }
        if(empty($lucky)){
            api_result(0, 'succ',array());

        }
		
		$concat_keys = array(
				'nick' => 'unick',
				'icon' => 'uicon',
            'ip' => 'uip',
		);
		$lucky_num_list = $list_mod->concatTbl($base['appid'], $lucky_num_list, 'uid', 'main', 'user', 'uid', $concat_keys);
        require(dirname(dirname(__FILE__)).'/mods/ip.mod.php');
        $ip = new LibIp();
		$data = array();
		foreach ($lucky_num_list as $lucky_num) {
				
			$icon_info = ap_user_icon_url($lucky_num['uicon']);
				
			$data[] = array(
					'activity_id' => intval($lucky_num['activity_id']),
					'publish_time' => date('Y-m-d H:i:s', $lucky_num['rt']),
					'lucky_uid' => intval($lucky_num['uid']),
					'lucky_unick' => ap_strval($lucky_num['unick']),
					'lucky_uicon' => ap_strval($icon_info['icon']),
					'lucky_num' => intval($lucky_num['lucky_num']),
					'lucky_user_num' => intval($lucky_num['user_num']),
                    'ip' => $ip->getlocation(long2ip($lucky_num['uip'])),
			);
		}
		
		api_result(0, 'succ', $data);
	}
	
	/**
	 * 幸运号计算详情
	 */
	public function luckyNumDetail(){
		//测试使用
		//$this->test_lucky_num_detail();
		//标准参数检查
		$base = api_check_base();
		
		$validate_cfg = array(
			'activity_id' => array(
				'api_v_numeric|1||activity_id不合法',
			),
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$goods_mod = Factory::getMod('nc_goods');
		$result = $goods_mod->getLuckyNumDetail($base, $ipt_list);
		
		api_result(0, '获取成功', $result);
	}


	/**
	 * 获取参与记录,往期揭晓,晒单列表的数目
	 * 接口地址：?c=nc_goods&a=getListTotal&11个标准参数
	 * POST参数: activity_id
	 * @return {"join_total":0,"history_total":0,"share_total":0}
	 */
	public function getListTotal() {
//		$this->test_list_total();

		//参数检查
		$base = api_check_base();
		$login_user = app_get_login_user($base['sessid'], $base['appid'], false);
		$validate_cfg = array(
			'activity_id' => array(
				'api_v_numeric|1||activity_id不合法',
			),
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);

		//判断商品和活动是否存在
		$activity_id = $ipt_list['activity_id'];
		$activity = $this->validActivity($activity_id);
		$goods_id = $activity['goods_id'];
		$goods = $this->validGoods($goods_id);

		//参与记录
		$join_total = $this->getJoinTotal($base, $activity_id);
		//往期揭晓
		$history_total = $this->getHistoryTotal($base, $goods_id);
		//晒单分享
		$share_total =  $this->getShareTotal($base, $goods_id);

		$total = array(
			'join_total' => intval($join_total),
			'history_total' => intval($history_total),
			'share_total' => intval($share_total)
		);
		return api_result(0, '获取成功', $total);
	}


	//判断活动是否存在
	private function validActivity($activity_id) {
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('shop', 'activity', 'activity_id');
		$activity = $pub_mod->getRow($activity_id);
		if (!$activity) {
			api_result(2, '云客活动不存在');
		}else {
			return $activity;
		}
	}

	//判断商品是否存在
	private function validGoods($goods_id) {
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('shop', 'goods', 'goods_id');
		$goods = $pub_mod->getRow($goods_id);
		if (!$goods) {
			api_result(2, '商品不存在');
		}else {
			return $goods;
		}
	}

	//获取参与记录的总数
	private function getJoinTotal($base, $activity_id) {
		$pub_mod = Factory::getMod('pub');
		$where = array(
			'appid' => $base['appid'],
			'activity_id' => $activity_id,
		);
		$pub_mod->init('shop', 'activity_user', 'activity_user_id');
		$join_total = $pub_mod->getRowTotal($where);
		return $join_total;
	}

	//获取往期揭晓的总数
	private function getHistoryTotal($base, $goods_id) {
		$pub_mod = Factory::getMod('pub');
		$where = array(
			'appid' => $base['appid'],
			'goods_id' => $goods_id,
		);
		$pub_mod->init('shop', 'lucky_num', 'lucky_num_id');
		$history_total = $pub_mod->getRowTotal($where);
		return $history_total;
	}

	//获取晒单分享的总数
	private function getShareTotal($base, $goods_id) {
		$pub_mod = Factory::getMod('pub');
		$where = array(
			'appid' => $base['appid'],
			'goods_id' => $goods_id,
		);
		$pub_mod->init('shop', 'show', 'show_id');
		$share_total =  $pub_mod->getRowTotal($where);
		return $share_total;
	}

}