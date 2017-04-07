<?php
/**
 * @since 2016-01-06
 * note 商品列表相关
 */
class NcRedActivityMod extends BaseMod{
	
	/**
	 * 活动列表
	 */
	public function getActivityList($base,$page){

        $limit = array(
            'begin' => ($page-1)*10,
            'length' => 10
        );

		$column = array(
			'a.activity_id','a.red_id','a.need_num','a.user_num','a.end_time','a.flag','a.result_uid',
			'a.publish_time','b.price','b.title','b.sub_title'
		);

		//连表配置
		$join = array(
			'from' => DATABASE.'.t_red_activity a',
			'join' => array(
				array(
					'join_type' => 'left join',
					'join_table' => DATABASE.'.t_red b',
					'on' => array(
						'a.red_id=b.red_id'
					)
				)
			)
		);
        $order = array(
            'a.flag' => 'asc',
            'a.rt' => 'asc',
        );
		$nc_list_mod = Factory::getMod('nc_list');
		$nc_list_mod->setDbConf('shop', 'red');
		
		$activityInfo = $nc_list_mod->getDataJoinTable($join, array(), $column, $order, $limit);

        $nc_list_mod->setDbConf('shop', 'point_rule');
        $p = $nc_list_mod->getDataOne(array('type'=>'红包'), array('point'), array(), array(),false);


        foreach($activityInfo as &$c){
            if($c['flag']==1){
                $c['remain_time'] = $c['publish_time'] - time();
            }

            $c['point'] = $p['point'];
        }

		return $activityInfo;
	}

    public function detail($base,$activity_id,$uid){
        $where = array(
            'a.activity_id' => $activity_id,
        );
        $column = array(
            'a.activity_id','a.red_id','a.need_num','a.user_num','a.end_time','a.flag','a.result_uid','a.result_num','a.publish_time','b.price','b.title','b.sub_title'
        );

        //连表配置
        $join = array(
            'from' => DATABASE.'.t_red_activity a',
            'join' => array(
                array(
                    'join_type' => 'left join',
                    'join_table' => DATABASE.'.t_red b',
                    'on' => array(
                        'a.red_id=b.red_id'
                    )
                )
            )
        );

        $nc_list_mod = Factory::getMod('nc_list');
        $nc_list_mod->setDbConf('shop', 'red');

        $activityInfo = $nc_list_mod->getDataJoinTable($join, $where, $column, array(), array());
        $nc_list_mod->setDbConf('shop', 'red_user');
        $activityInfo[0]['already'] = 0;

        if($uid){
            $where = array(
                'activity_id' => $activity_id,
                'uid' => $uid,
            );
            $Info = $nc_list_mod->getDataOne($where, array(), array(), array(),false);
            if(!empty($Info)){
                $activityInfo[0]['already'] = 1;
                $activityInfo[0]['lucky_num'] = $Info['lucky_num'];
            }
        }

        if($activityInfo[0]['result_uid']){
            include_once COMMON_PATH.'libs/LibIp.php';
            $ip = new LibIp();

            $nc_list_mod->setDbConf('main', 'user');
            $user = $nc_list_mod->getDataOne(array('uid'=>$activityInfo[0]['result_uid']),array(),array(),array(),false);
            $icon_info = ap_user_icon_url($user['icon']);
            $activityInfo[0]['lucky_unick'] = $user['nick'];
            $activityInfo[0]['lucky_uicon'] = ap_strval($icon_info['icon']);
            $activityInfo[0]['lucky_uip'] = $ip->getlocation(long2ip($user['ip']));
        }

        $nc_list_mod->setDbConf('shop', 'point_rule');
        $activityInfo[0]['point'] = $nc_list_mod->getDataOne(array('type'=>'红包'), array('point'), array(), array(),false);

        $activityInfo[0]['remain_time'] =  $activityInfo[0]['publish_time'] - time();


        return $activityInfo[0];
    }

    public function joinRed($base,$activity_id,$uid){
        $nc_list_mod = Factory::getMod('nc_list');
        //check user
        $nc_list_mod->setDbConf('shop', 'red_order');
        $where = array(
            'activity_id' => $activity_id,
            'uid' => $uid,
            'stat' => 1,
        );
        $Info2 = $nc_list_mod->getDataOne($where, array(), array(), array(),false);
        if(!empty($Info2)){
            api_result(2, '已参加活动');
        }
        //check act
        $nc_list_mod->setDbConf('shop', 'red_activity');
        $where = array(
            'activity_id' => $activity_id,
            'flag' => 0
        );
        $Info = $nc_list_mod->getDataOne($where, array('need_num','user_num'), array(), array(),false);
        if(empty($Info)){
            api_result(1, '活动已结束或不存在');
        }
        if($Info['user_num'] >= $Info['need_num']){
            api_result(1, '活动已结束');
        }

        $nc_list_mod->setDbConf('shop', 'point_rule');
        $info = $nc_list_mod->getDataOne(array('type'=>'红包'), array('point'), array(), array(),false);
        if($info['point']>0){
            $nc_list_mod->setDbConf('shop', 'point');
            $info2 = $nc_list_mod->getDataOne(array('uid'=>$uid), array('point'), array(), array(),false);
            if($info2['point']<$info['point']){
                api_result(1, '积分不足，无法参与');
            }
        }

        $nc_list_mod->setDbConf('shop', 'red_order');
        $insert = array(
            'activity_id' => $activity_id,
            'uid' => $uid,
            'stat' => 0,
        );
        $nc_list_mod->insertData($insert);
        $data = $nc_list_mod->getDataOne($insert,array('id'),array(),array(),false);
        return $data['id'];
    }

    public function joinWait($base,$order_id,$uid){
        $nc_list_mod = Factory::getMod('nc_list');
        $nc_list_mod->setDbConf('shop', 'red_order');
        $where = array(
            'id' => $order_id,
            'uid' => $uid,
        );
        $Info2 = $nc_list_mod->getDataOne($where, array('stat','activity_id'), array(), array(),true);
        if($Info2['stat']==3){
            api_result(1, '系统错误，重新抢购吧');
        }
        if($Info2['stat']==2){
            api_result(1, '手太慢了，没抢到');
        }
        if($Info2['stat']==0){
            api_result(2, '请稍后');
        }
        if($Info2['stat']==1){
            $nc_list_mod->setDbConf('shop', 'red_user');
            $where = array(
                'activity_id' => $Info2['activity_id'],
                'uid' => $uid,
            );
            $Info = $nc_list_mod->getDataOne($where, array('lucky_num','ut'), array(), array(),true);
            return $Info;
        }
    }

    public function joinList($base,$activity_id){
        $nc_list_mod = Factory::getMod('nc_list');
        $nc_list_mod->setDbConf('shop', 'red_user');
        $where = array(
            'activity_id' => $activity_id,
        );
        $Info = $nc_list_mod->getDataList($where, array(), array(), array(),true);
        $user = array();
        foreach($Info as $i){
            $user[] = $i['uid'];
        }
        $nc_list_mod->setDbConf('main', 'user');
        $where = array(
            'uid' => array($user,'in'),
        );
        $userInfo = $nc_list_mod->getDataList($where, array('nick','icon','uid','ip'), array(), array(),true);
        $users = array();
        foreach($userInfo as $u){
            $users[$u['uid']] = $u;
        }
        require(dirname(dirname(__FILE__)).'/mods/ip.mod.php');
        $ip = new LibIp();
        foreach($Info as &$ii){
            $icon_info = ap_user_icon_url($users[$ii['uid']]['icon']);
            $ii['nick'] = $users[$ii['uid']]['nick'];
            $ii['icon'] = $icon_info['icon'];
            $ii['ip'] = $ip->getlocation(long2ip($users[$ii['uid']]['ip']));

        }
        return $Info;
    }

    public function joinHistory($base,$red_id,$page){
        $nc_list_mod = Factory::getMod('nc_list');
        $nc_list_mod->setDbConf('shop', 'red_activity');
        $where = array(
            'red_id' => $red_id,
            'flag' => 2,
        );
        $order = array(
            'activity_id' => 'desc',
        );
        $limit = array(
            'begin' => ($page-1)*20,
            'limit' => 20,
        );
        $Info = $nc_list_mod->getDataList($where, array(), $order, $limit,true);
        $user = array();
        foreach($Info as $i){
            $user[] = $i['result_uid'];
        }
        $nc_list_mod->setDbConf('main', 'user');
        $where = array(
            'uid' => array($user,'in'),
        );
        $userInfo = $nc_list_mod->getDataList($where, array('nick','icon','uid','ip'), array(), array(),false);
        $users = array();
        foreach($userInfo as $u){
            $users[$u['uid']] = $u;
        }
        require(dirname(dirname(__FILE__)).'/mods/ip.mod.php');
        $ip = new LibIp();

        foreach($Info as &$ii){
            $icon_info = ap_user_icon_url($users[$ii['result_uid']]['icon']);
            $ii['nick'] = $users[$ii['result_uid']]['nick'];
            $ii['icon'] = $icon_info['icon'];
            $ii['ip'] = $ip->getlocation(long2ip($users[$ii['result_uid']]['ip']));
        }
        return $Info;

    }

    public function joinResult($base,$activity_id){
        $nc_list_mod = Factory::getMod('nc_list');
        $nc_list_mod->setDbConf('shop', 'red_activity');
        $where = array(
            'activity_id' => $activity_id,
            'flag' => 2,
        );
        $column = array(
            'result_num',
            'result_uid',
        );
        $Info = $nc_list_mod->getDataOne($where,$column, array(), array(),false);
        if(empty($Info)){
            api_result(2, '稍后');
        }
        $nc_list_mod->setDbConf('main', 'user');
        $where = array(
            'uid' => $Info['result_uid'],
        );
        require(dirname(dirname(__FILE__)).'/mods/ip.mod.php');
        $ip = new LibIp();
        $userInfo = $nc_list_mod->getDataOne($where, array('nick','icon','ip'), array(), array(),true);
        $icon_info = ap_user_icon_url($userInfo['icon']);
        $Info['nick'] = $userInfo['nick'];
        $Info['icon'] = $icon_info['icon'];
        $Info['ip'] = $ip->getlocation(long2ip($userInfo['ip']));

        return $Info;
    }

	
}