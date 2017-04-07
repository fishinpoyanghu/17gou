<?php
/**
 * @since 2016-01-06
 * note 商品列表相关
 */
class NcActivityMod extends BaseMod{
	
	/**
	 * 获取分类列表
	 */
	public function getCategoryList($base, $keyVal = false){
		//$data = do_cache('get', 'goods', 'goods_type');
		if(!empty($data)){
			$result = json_decode($data, true);
		}else{
			$nc_list_mod = Factory::getMod('nc_list');
			$where = array(
				'appid' => $base['appid'],
				'father_id' => 0,
				'stat' => 0
			);
			$column = array(
				'goods_type_id','name','img','shou'
			);
			$nc_list_mod->setDbConf('shop', 'goods_type'); // 这里导航默认选11条 配合导航跟界面
			$categoryList = $nc_list_mod->getDataList($where, $column, array(), array('length'=>11), false);
			$result = array();
			if(!empty($categoryList)){
				foreach($categoryList as $val){
					$result[] = array(
						'goods_type_id' => $val['goods_type_id'],
						'name' => $val['name'],
                        'img' => $val['img'],
                        'shou' => $val['shou'],
					);
				}
				//do_cache('set', 'goods', 'goods_type', json_encode($result));
			}
		}
		//需要key-value形式的
		if(!empty($result) && $keyVal){
			$re = array();
			foreach($result as $val){
				$re[$val['goods_type_id']] = $val['name'];
			}
			return $re;
		}
		return $result;
	}
	
	/**
	 * 活动列表
	 */
	public function getActivityList($base, $ipt_list){		
		if(empty($ipt_list['status'])){
			$ipt_list['status'] = 0;
		}


		if(empty($ipt_list['order_key'])){
			$ipt_list['order_key'] = 'time';
		}
		
		//如果为3，需要获取即将揭晓和已经揭晓
		if($ipt_list['status'] == 3){
			$where = array(
				'a.appid' => $base['appid'],
				'a.flag' => array(
					array(1,2),'in'
				)
			);
			$order = array(
				'a.flag' => 'asc',
				'a.publish_time' => 'desc'
			);
		}else{
			$where = array(
				'a.appid' => $base['appid'],
				'a.flag' => $ipt_list['status'],
			);
			$orderKey = array(
				'weight' => 'b.weight',
				'time' => 'a.activity_id',
                'num' => 'a.need_num',
				'ing' => 'a.process',
			);
			$order = array(
				$orderKey[$ipt_list['order_key']] => $ipt_list['order_type']?$ipt_list['order_type']:'desc'
			);
		}
		if(!empty($ipt_list['goods_type_id'])){
			$where['b.goods_type_id'] = $ipt_list['goods_type_id'];
		}

		if(!empty($ipt_list['activity_type'])){   
			if($ipt_list['activity_type']==-4){
				$where['b.activity_type'] = array(
				4,'<'
			); //默认不显示二人购商品
			}else{
				$where['b.activity_type'] = $ipt_list['activity_type'];
			}
			 
		} 
		 
		if(!empty($ipt_list['key_word'])){ //复合查询
			$whereArr[]=array('b.title','like','%'.$ipt_list['key_word'].'%');
			$whereArr[]= array('b.sub_title','like','%'.$ipt_list['key_word'].'%');
			$where['_complexsql'] =array(
				  'whereArr'=>$whereArr,
				 '_logic'=>'or'
				 );				 
		}
        $limit = array(
            'begin' => (int)$ipt_list['from'],
            'length' => (int)$ipt_list['count']
        );
        $limit['length']=$limit['length']>30?30:$limit['length'];
		$column = array(
			'a.activity_id','a.goods_id','a.need_num','a.user_num','a.end_time','a.flag',
			'a.publish_time','b.main_img','b.title_img','b.title','b.sub_title','b.activity_type'
		);
		
		//连表配置
		$join = array(
			'from' => DATABASE.'.t_activity a',
			'join' => array(
				array(
					'join_type' => 'left join',
					'join_table' => DATABASE.'.t_goods b',
					'on' => array(
						'a.appid=b.appid','a.goods_id=b.goods_id'
					)
				)
			)
		);
		$nc_list_mod = Factory::getMod('nc_list');
		$nc_list_mod->setDbConf('shop', 'activity');
		
		$activityInfo = $nc_list_mod->getDataJoinTable($join, $where, $column, $order, $limit);
		$count = $nc_list_mod->getJoincount($join, $where, $column[0]); 
		$result = $endActivity = array();
		$currentTime = time();
		$ipt_list['status'] = intval($ipt_list['status']);
		foreach($activityInfo as $val){
			$activity_id = $val['activity_id'];
            $_a = explode(',',$val['main_img']);
			$temp = array(
				'activity_id' => intval($val['activity_id']),
                'goods_img' => $val['main_img'],
                'title_img' => $_a[0],
				'goods_title' => $val['title'],
				'goods_subtitle' => $val['sub_title'],
				'need_num' => intval($val['need_num']),
				'remain_num' => intval($val['need_num'] - $val['user_num']),
				'activity_type' => intval($val['activity_type']),
				'status' => intval($val['flag'])
			);
            $temp['publish_time'] = 0;
			if($val['flag'] == 1){//即将揭晓
				$temp['remain_time'] = $val['publish_time'] - $currentTime;
                $temp['remain_time'] = ($temp['remain_time'] < 0) ? 0 : $temp['remain_time'];
			}else if($val['flag'] == 2){//已经结束
                $temp['publish_time'] = date('Y-m-d H:i:s', $val['publish_time']);
                $endActivity[] = $activity_id;
			}
			$result[$activity_id] = $temp;
		}
		//如果有已经结束的，要获取中奖者信息
        foreach($result as &$c){
            $c['lucky_num'] = '';
            $c['lucky_unick'] = '';
            $c['lucky_user_num'] = '';
            $c['lucky_ip'] = '';
            $c['lucky_uicon'] = '';
            $c['lucky_uid'] = '';
            $c['join_number']=1;
            //$c['join_number']=$c['activity_type']==2?10:1;//兼容前台框架首页加减数量
        }
		if(!empty($endActivity)){
			$where = array(
				'a.activity_id' => array(
					$endActivity,'in'
				)
			);
			$column = array(
				'a.activity_id','a.lucky_num','a.user_num','b.nick','b.ip','b.icon','b.uid'
			);
			//连表配置
			$join = array(
				'from' => DATABASE.'.t_lucky_num a',
				'join' => array(
					array(
						'join_type' => 'left join',
						'join_table' => DATABASE.'.t_user b',
						'on' => array(
							'a.appid=b.appid','a.uid=b.uid'
						)
					)
				)
			);
			$luckyData = $nc_list_mod->getDataJoinTable($join, $where, $column);
            require(dirname(dirname(__FILE__)).'/mods/ip.mod.php');
            $ip = new LibIp();
			foreach($luckyData as $val){
                $icon_info = ap_user_icon_url($val['icon']);
				$result[$val['activity_id']]['lucky_num'] = $val['lucky_num'];
				$result[$val['activity_id']]['lucky_unick'] = $val['nick'];
                $result[$val['activity_id']]['lucky_user_num'] = $val['user_num'];
                $result[$val['activity_id']]['lucky_ip'] = $ip->getlocation(long2ip($val['ip']));
                $result[$val['activity_id']]['lucky_uicon'] = $icon_info['icon'];
                $result[$val['activity_id']]['lucky_uid'] = $val['uid'];
			}
		}
		$resultArr=$nc_list_mod->toArray($result);
		$resultArr['count']=$count;
		return $resultArr;
	}
	
	/**
	 * 活动信息
	 */
	public function getActivityInfo($appid, $activity_id){
		$nc_list_mod = Factory::getMod('nc_list');
		$where = array(
			'appid' => $appid,
			'activity_id' => $activity_id
		);
		$column = array(
			'need_num','user_num'
		);
		$nc_list_mod->setDbConf('shop', 'activity');
		$activityInfo = $nc_list_mod->getDataList($where, $column);
		$activityInfo = $activityInfo[0];
		return array(
			'need_num' => intval($activityInfo['need_num']),
			'remain_num' => intval($activityInfo['need_num'] - $activityInfo['user_num'])
		);
	}

    public function getluckyInfo($appid){
        $nc_list_mod = Factory::getMod('nc_list');
        /*$where = array(
            'appid' => $appid,
        );
        $column = array(
            'uid','goods_id','ut'
        );
        $order = array(
            'ut' => 'desc',
        );*/
        $nc_list_mod->setDbConf('shop', 'lucky_num');
        $sql = "select `uid`,`goods_id`,`ut` from {$nc_list_mod->dbConf['tbl']} where `uid`!=0 order by `ut` desc limit 10";
        $luckyInfo = $nc_list_mod->getDataBySql($sql);
        if(empty($luckyInfo)) return array();
        $uid = array();
        $goods = array();
        foreach($luckyInfo as $v){
            $uid[] = $v['uid'];
            $goods[] = $v['goods_id'];
        }
        $goods = join(',',$goods);
        $uid = join(',',$uid);
        $dbConf = load_db_cfg('shop', 'goods', '', 'rw');
        $db_op = DbOp::getInstance($dbConf);
        $ret = $db_op->queryList("select `goods_id`,`title` from {$dbConf['tbl']} where `goods_id` in ({$goods})");
        $_goods = array();
        foreach($ret as $v){
            $_goods[$v['goods_id']] = $v['title'];
        }

        $dbConf = load_db_cfg('main', 'user', '', 'rw');
        $db_op = DbOp::getInstance($dbConf);
        $ret = $db_op->queryList("select `uid`,`nick` from {$dbConf['tbl']} where `uid` in ({$uid})");
        $_uid = array();
        foreach($ret as $v){
            $_uid[$v['uid']] = $v['nick'];
        }
        foreach($luckyInfo as &$v){
            $v['name'] = $_uid[$v['uid']];
            $v['goods_name'] = $_goods[$v['goods_id']];
        }
        return $luckyInfo;
    }


    /**
	 * 活动列表
	 */
	public function getTeamActivityList($base, $ipt_list){	 
	 
		$order = array(
			 'publish_time'  => 'desc'
		); 
	 
        $limit = array(
            'begin' => (int)$ipt_list['from'],
            'length' => (int)$ipt_list['count']
        );
        $limit['length']=$limit['length']>30?30:$limit['length'];
		$column = array(
			'a.teamwar_id','a.goods_id','a.need_num','a.user_num','a.et','a.flag',
			'a.publish_time','b.main_img','b.title_img','b.title','b.sub_title','b.activity_type'
		);
	    $where['a.flag'] = array(
				6,'>'
		 );
		//连表配置
		$join = array(
			'from' => DATABASE.'.t_teamwar a',
			'join' => array(
				array(
					'join_type' => 'left join',
					'join_table' => DATABASE.'.t_team_goods b',
					'on' => array(
					 'a.goods_id=b.goods_id'
					)
				)
			)
		);

		$nc_list_mod = Factory::getMod('nc_list');
		$nc_list_mod->setDbConf('team', 'teamwar');
		  
		$activityInfo = $nc_list_mod->getDataJoinTable($join, $where, $column, $order, $limit); 
		$count = $nc_list_mod->getJoincount($join, $where, $column[0]);  
		$result = $endActivity = array();
		$currentTime = time(); 
		foreach($activityInfo as $val){
			$activity_id = $val['teamwar_id'];
            $_a = explode(',',$val['main_img']);
			$temp = array(
				'activity_id' => intval($val['teamwar_id']),
                'goods_img' => $val['main_img'],
                'title_img' => $_a[0],
				'goods_title' => $val['title'],
				'goods_subtitle' => $val['sub_title'],
				'need_num' => intval($val['need_num']),
				'remain_num' => intval($val['need_num'] - $val['user_num']),
				//'activity_type' => intval($val['activity_type']),
				'flag' => intval($val['flag'])
			);
            $temp['publish_time'] = 0;
			if($val['flag'] == 7){//即将揭晓
				$temp['remain_time'] = $val['publish_time'] - $currentTime;
                $temp['remain_time'] = ($temp['remain_time'] < 0) ? 0 : $temp['remain_time'];
			}else if($val['flag'] == 8){//已经结束
                $temp['publish_time'] = date('Y-m-d H:i:s', $val['publish_time']);
                $endActivity[] = $activity_id;
			}
			$result[$activity_id] = $temp;
		} 
	 	 
        foreach($result as &$c){
            $c['lucky_num'] = '';
            $c['lucky_unick'] = '';
            $c['lucky_user_num'] = ''; 
            $c['lucky_uicon'] = '';
            $c['lucky_uid'] = '';
            $c['join_number']=1;
           
        }
		if(!empty($endActivity)){
			$where = array(
				'a.activity_id' => array(
					$endActivity,'in'
				)
			);
			$column = array(
				'a.activity_id','a.lucky_num','a.user_num','b.nick','b.ip','b.icon','b.uid'
			);
			//连表配置
			$join = array(
				'from' => DATABASE.'.t_team_lucky_num a',
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
			$corder = array(
				'a.lucky_num_id' => 'desc', 
			);
	 
			$luckyData = $nc_list_mod->getDataJoinTable($join, $where, $column,$corder);
  		 
			foreach($luckyData as $val){
                $icon_info = ap_user_icon_url($val['icon']);
				$result[$val['activity_id']]['lucky_num'] = $val['lucky_num'];
				$result[$val['activity_id']]['lucky_unick'] = $val['nick'];
                $result[$val['activity_id']]['lucky_user_num'] = $val['user_num'];
                
                $result[$val['activity_id']]['lucky_uicon'] = $icon_info['icon'];
                $result[$val['activity_id']]['lucky_uid'] = $val['uid'];
			}
		}
		$resultArr=$nc_list_mod->toArray($result); 
		$resultArr['count']=$count;
		return $resultArr;
	}
	//登录送钱
	public function addloginmoney($user){   
		$nc_list= Factory::getMod('nc_list');
		$nc_list->setDbConf('main', 'user_extend'); 
		$where = array(  
			'uid'=>$user['uid']
		);  
	 	$time=time();
		$usermsg = $nc_list->getDataOne($where, array(), array(), array(), false);  

	    if(!empty($usermsg)){ 
 	    	$msg=json_decode($usermsg['user_msg'],true);
 	    	if($msg['login_time']){ //判断是不是今天 

 	    		if(date('Y-m-d',time())==date('Y-m-d',$msg['login_time'])){ //一天只能领取一次  //不在间隔24小时，按天数去算
 	    			return '';
 	    		} 

 	    		$msg['login_time']=$time;
 	    		$update['user_msg']=json_encode($msg);
 	    		$nc_list->updateData($where,$update); 
 	    	}else{
 	    		$msg['login_time']=$time; 
 	    		$update['user_msg']=json_encode($msg);
 	    		$nc_list->updateData($where,$update);
 	    	} 
 	    }else{ 
 	    	$user_msg['login_time']=$time; 
 	    	$insertData['uid']=$user['uid'];
 	    	$insertData['user_msg']=json_encode($user_msg);  
 	    	$nc_list->insertData($insertData);
 	    	 
 	    }
	    $sql = "update ".DATABASE.".t_user set `money`=`money`+1   where `uid`={$user['uid']} and back_time > {$time}"; 
	    $result=$nc_list->executeSql($sql); 
	    $msg_mod = Factory::getMod('msg'); 
	    $msg_mod->sendNotify(10002, $user['uid'], 10002, 8, 0, 8, '微信登录赠送1元,祝你购物愉快!');
                
	}
	//天商微购
	public function rebatemoney($unionid,$uid){
		   $nc_list = Factory::getMod('nc_list');
           $nc_list->setDbConf('other', 'user'); 
           $unionmsg = $nc_list->getDataOne(array('unionid'=>$unionid), array(), array(), array(), false); 
           $backtime=0;
           if(isset($unionmsg['extend_msg'])){
                $extend_msg=json_decode($unionmsg['extend_msg'],true);
              if($extend_msg['back_time']>0  && $extend_msg['back_money']>0){
                $backtimestr='';
                $backtime=$extend_msg['back_time'];
                $backmoney=$extend_msg['back_money']>0?$backmoney+$extend_msg['back_money']:0;
                if($extend_msg['back_time']>$user['back_time']){
                    $backtimestr="  ,`back_time`= {$extend_msg['back_time']}";
                    $user['back_time']=$extend_msg['back_time'];  
                } 
                $extend_msg['back_time']=0;
                $extend_msg['back_money']=0;
                $encode_msg=json_encode($extend_msg);
                $sql = "update {$nc_list->dbConf['tbl']} set `extend_msg`= '{$encode_msg}' where `unionid`='{$unionid}'";
             
                $nc_list->executeSql($sql); 
                $nc_list->setDbConf('main', 'user');
                $sql = "update {$nc_list->dbConf['tbl']} set `money`=`money`+{$backmoney}  $backtimestr where `uid`={$uid}";
              
                $nc_list->executeSql($sql);
                $msg_mod = Factory::getMod('msg');
                $day=date('Y-m-d',$backtime);
                $msg_mod->sendNotify(10002, $uid, 10002, 5, 0, 7, "天商微购商城购买商品返现￥$backmoney 元！在 $day 前,每天微信登陆亿七购将获得 ￥1元");
                }
                
           }
         return $backtime;
	}


}