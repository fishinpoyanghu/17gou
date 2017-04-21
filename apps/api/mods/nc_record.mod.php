<?php
/**
 * @since 2016-01-05
 */
class NcRecordMod extends BaseMod{
	
	/**
	 * 获取TA的参与记录
	 */
	public function getRecordList($ipt_list, $base){
		$order = array(
			'a.ut' => 'desc'
		);//此排序太慢，先去掉。后续如需要此排序请为此字段添加索引
		$nc_list_mod = Factory::getMod('nc_list');
		if(empty($ipt_list['uid'])){
			$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
			$ipt_list['uid'] = $login_user['uid'];
		}else{
			$order = array(
			'b.process' => 'desc'
			);//此排序太慢，先去掉。后续如需要此排序请为此字段添加索引
		}
		//where条件
		$where = array(
			'a.uid' => $ipt_list['uid'],
			'a.appid' => $base['appid']
		);
		$limit = array(
			'begin' => $ipt_list['from'],
			'length' => $ipt_list['count']
		);
		//过滤状态
		if(!empty($ipt_list['status']) || strlen($ipt_list['status']) > 0){
			if($ipt_list['status'] == 3){
				$where['b.flag'] = array(
					array(0,1),'in'
				);
			}else{
				$where['b.flag'] = $ipt_list['status'];
			}
		}
		
		 
		
		$column = array(
			'a.activity_user_id','a.activity_id','a.user_num','b.goods_id',
			'b.flag','b.end_time','b.publish_time','b.need_num','b.user_num as remain_num'
		);
		
		//连表配置
		$join = array(
			'from' => DATABASE.'.t_activity_user a',
			'join' => array(
				array(
					'join_type' => 'left join',
					'join_table' => DATABASE.'.t_activity b',
					'on' => array(
						'a.appid=b.appid','a.activity_id=b.activity_id'
					)
				)
			)
		);
		
		$nc_list_mod->setDbConf('shop', 'activity_user');
		
		//获取用户参与的期号
		$activityInfo = $nc_list_mod->getDataJoinTable($join, $where, $column, $order, $limit);
		
		$result = $activity_ids = $goods_ids = $goods_activity = $endActivity = array();
		$currentTime = time();
		foreach($activityInfo as $val){
			$activity_id = $val['activity_id'];
			$goods_ids[] = $val['goods_id'];
			$goods_activity[$val['goods_id']][] = $activity_id;
			$result[$activity_id] = array(
				'record_id' => intval($val['activity_user_id']),
				'activity_id' => intval($val['activity_id']),
				'user_num' => intval($val['user_num']),
				'need_num' => intval($val['need_num']),
				'status' => intval($val['flag'])
			);
			if($val['flag'] == 0){
				//还未结束
				$result[$activity_id]['remain_num'] = $val['need_num'] - $val['remain_num'];
			}else if($val['flag'] == 1){
				//即将揭晓
				$result[$activity_id]['remain_time'] = $val['publish_time'] - $currentTime;
				$result[$activity_id]['remain_time'] = ($result[$activity_id]['remain_time'] < 0) ? 0 : $result[$activity_id]['remain_time'];
			}else{
				//已经揭晓
				$result[$activity_id]['publish_time'] = date('Y-m-d H:i:s', $val['publish_time']);
				$endActivity[] = $activity_id;
			}
		}
		
		//如果有结束的活动，获取获奖者
		if($endActivity){
			$where = array(
				'appid' => $base['appid'],
				'activity_id' => array(
					$endActivity,'in'
				),
			);
			$column = array(
				'activity_id','lucky_num','uid','user_num'
			);
			$nc_list_mod->setDbConf('shop', 'lucky_num');
			$luckyUser = $nc_list_mod->getDataList($where, $column,array(),array(),false);
			$uids = $uid_activity = array();
			foreach($luckyUser as $val){
				$activity_id = $val['activity_id'];
				$result[$activity_id]['lucky_uid'] = $val['uid'];
				$result[$activity_id]['lucky_user_num'] = $val['user_num'];
				$result[$activity_id]['lucky_num'] = $val['lucky_num'];
				$uids[] = $val['uid'];
				$uid_activity[$val['uid']][] = $activity_id;
			}
			//获取用户昵称
			$where = array(
				'appid' => $base['appid'],
				'uid' => array(
					$uids,'in'
				),
			);
            include_once COMMON_PATH.'libs/LibIp.php';
            $ip = new LibIp();
            $column = array(
                'uid','nick','icon','ip'
            );
            $nc_list_mod->setDbConf('main', 'user');
            $userInfo = $nc_list_mod->getDataList($where, $column,array(),array(),false);
            foreach($userInfo as $val){
                $uid = $val['uid'];
                foreach($uid_activity[$uid] as $activity_id){
                    $icon_info = ap_user_icon_url($val['icon']);
                    $result[$activity_id]['lucky_unick'] = $val['nick'];
                    $result[$activity_id]['lucky_uicon'] = ap_strval($icon_info['icon']);
                    $result[$activity_id]['lucky_uip'] = $ip->getlocation(long2ip($val['ip']));
                }
            }
		}
		//获取商品信息
		$where = array(
			'appid' => $base['appid'],
			'goods_id' => array(
				$goods_ids,'in'
			)
		);
		$column = array(
			'goods_id','title','sub_title','activity_type','main_img'
		);
		$nc_list_mod->setDbConf('shop', 'goods');
		
		$goodsInfo = $nc_list_mod->getDataList($where, $column,array(),array(),false);
		foreach($goodsInfo as $val){
			foreach($goods_activity[$val['goods_id']] as $activity_id){
				$result[$activity_id]['goods_img'] = $val['main_img'];
				$result[$activity_id]['goods_title'] = $val['title'];
				$result[$activity_id]['goods_subtitle'] = $val['sub_title'];
				$result[$activity_id]['activity_type'] = intval($val['activity_type']);
				if($val['activity_type']==6){
					$nc_list_mod->setDbConf('shop', 'order');
					$where = array(
						'order_aid' => $activity_id,
						'flag'=>1,
						'uid'=>$ipt_list['uid']		 
					);
					$column = array(
						'order_info' 
					);
				  $xingyungou=$nc_list_mod->getDataList($where, $column,array(),array(),false);
				  if(count($xingyungou)==2){
				  		$buytype=3; //表示全买
				  }else{
				  	   $moneyInfo = json_decode($xingyungou[0]['order_info'], true);
				       $buytype=$moneyInfo[0]['hot_luckyBuy']; 
				  }		    
				  
				 $result[$activity_id]['orderinfo'] = $buytype;
				// var_dump($where,$result[$activity_id]['orderinfo']);exit;
					 
				}
			}
		}
		return $nc_list_mod->toArray($result);
	}
	 
	
	/**
	 * 获取TA的中奖记录
	 */
	public function getWinRecordList($ipt_list, $base){ 
		$nc_list_mod = Factory::getMod('nc_list');
		$selfFlag = false;
		if(empty($ipt_list['uid'])){
			$selfFlag = true;
			$login_user = app_get_login_user($base['sessid'], $base['appid'], false);
			$ipt_list['uid'] = $login_user['uid'];
		}
		//获取活动信息
		$where = array(
			'uid' => $ipt_list['uid'],
			'appid' => $base['appid']
		);
		if((!empty($ipt_list['status']) || strlen($ipt_list['status']) > 0) && $selfFlag){
			$where['user_read'] = intval($ipt_list['status']);
		}
		$order = array(
			'activity_id' => 'desc'
		);
		$limit = array(
			'begin' => $ipt_list['from'],
			'length' => $ipt_list['count']
		);
		$column = array(
			'lucky_num_id','goods_id','activity_id','lucky_num','need_num','user_num','user_read'
		); 
		 
	   $nc_list_mod->setDbConf('shop', 'lucky_num');  
		 
		 
		$activityInfo = $nc_list_mod->getDataList($where, $column, $order, $limit);
		
		$result = $activity_ids = $goods_ids = $goods_activity = $neverRead = array();
		
		foreach($activityInfo as $val){
			$activity_id = intval($val['activity_id']);
			$activity_ids[] = $activity_id;
			$goods_ids[] = $val['goods_id'];
			$goods_activity[$val['goods_id']][] = $val['activity_id'];
			$result[$activity_id] = array(
				'id' => intval($val['lucky_num_id']),
				'activity_id' => $activity_id,
				'need_num' => intval($val['need_num']),
				'lucky_num' => intval($val['lucky_num']),
				'user_num' => intval($val['user_num']),
				'status' => intval($val['user_read']),
				'logistics_stat' => 0,
				'address' => '',
				'logistics_order' => ''
			);
			if($val['user_read'] == 0){
				$neverRead[] = $val['lucky_num_id'];
			}
		}
		//用户自己的中奖记录，需要获取物流信息
		if($selfFlag){
			//修改读取状态
			$where = array(
				'uid' => $login_user['uid'],
				'lucky_num_id' => array(
					$neverRead,'in'
				)
			);
			$data = array(
				'user_read' => 1
			);
			$nc_list_mod->updateData($where, $data);
			$where = array(
				'appid' => $base['appid'],
				'activity_id' => array(
					$activity_ids,'in'
				)
			);
			$column = array(
				'activity_id','logistics_type','logistics_stat','address','logistics_num','logistics_id'
			);
			$nc_list_mod->setDbConf('shop', 'logistics');
			
			$logisticsInfo = $nc_list_mod->getDataList($where, $column, array(), array(), false);


            $express = include get_app_root().'/conf/express.conf.php';
            $_express = array();
            foreach($express as $cv){
                $_express[$cv['code']] = $cv['name'];
            }
            
			if($logisticsInfo){
				foreach($logisticsInfo as $val){
                    $aa = explode(':',$val['address']);
					$activity_id = intval($val['activity_id']);
					$result[$activity_id]['logistics_id'] = intval($val['logistics_id']);
					$result[$activity_id]['logistics_stat'] = intval($val['logistics_stat']);
					$result[$activity_id]['address'] = urldecode($aa[0]).' '.urldecode($aa[1]).' '.$aa[2];
					$result[$activity_id]['logistics_order'] = $_express[$val['logistics_type']].' '.$val['logistics_num'];
					$result[$activity_id]['logistics_num']=$val['logistics_num'];
				}
			}
		}
		
		$where = array(
			'appid' => $base['appid'],
			'goods_id' => array(
				$goods_ids,'in'
			)
		);
		$column = array(
			'title','sub_title','main_img','activity_type','goods_id'
		);
		 
		$nc_list_mod->setDbConf('shop', 'goods');  
		 
	 
		$goodsInfo = $nc_list_mod->getDataList($where, $column);
		foreach($goodsInfo as $val){
			foreach($goods_activity[$val['goods_id']] as $activity_id){
				$result[$activity_id]['goods_img'] = $val['main_img'];
				$result[$activity_id]['goods_title'] = $val['title'];
				$result[$activity_id]['goods_subtitle'] = $val['sub_title'];
				$result[$activity_id]['activity_type'] = intval($val['activity_type']);
			}
		}
		return $nc_list_mod->toArray($result);
	}
	/**
	 * 获取团的中奖记录
	 */
	public function getTeamWinRecordList($ipt_list, $base){ 
		$nc_list_mod = Factory::getMod('nc_list');
		$selfFlag = false;
		if(empty($ipt_list['uid'])){
			$selfFlag = true;
			$login_user = app_get_login_user($base['sessid'], $base['appid'], false);
			$ipt_list['uid'] = $login_user['uid'];
		}
		//获取活动信息
		$where = array(
			'uid' => $ipt_list['uid'],
			'appid' => $base['appid']
		);
		if((!empty($ipt_list['status']) || strlen($ipt_list['status']) > 0) && $selfFlag){
			$where['user_read'] = intval($ipt_list['status']);
		}
		$order = array(
			'activity_id' => 'desc'
		);
		$limit = array(
			'begin' => $ipt_list['from'],
			'length' => $ipt_list['count']
		);
		$column = array(
			'lucky_num_id','goods_id','activity_id','lucky_num','need_num','user_num','user_read'
		); 
	 
	    $nc_list_mod->setDbConf('team', 'team_lucky_num');  
		 
		 
		$activityInfo = $nc_list_mod->getDataList($where, $column, $order, $limit);
		
		$result = $activity_ids = $goods_ids = $goods_activity = $neverRead = array();
		
		foreach($activityInfo as $val){
			$activity_id = intval($val['activity_id']);
			$activity_ids[] = $activity_id;
			$goods_ids[] = $val['goods_id'];
			$goods_activity[$val['goods_id']][] = $val['activity_id'];
			$result[$activity_id] = array(
				'id' => intval($val['lucky_num_id']),
				'activity_id' => $activity_id,
				'need_num' => intval($val['need_num']),
				'lucky_num' => intval($val['lucky_num']),
				'user_num' => intval($val['user_num']),
				'status' => intval($val['user_read']),
				'logistics_stat' => 0,
				'address' => '',
				'logistics_order' => ''
			);
			if($val['user_read'] == 0){
				$neverRead[] = $val['lucky_num_id'];
			}
		}
		//用户自己的中奖记录，需要获取物流信息
		if($selfFlag){
			//修改读取状态
			$where = array(
				'uid' => $login_user['uid'],
				'lucky_num_id' => array(
					$neverRead,'in'
				)
			);
			$data = array(
				'user_read' => 1
			);
			$nc_list_mod->updateData($where, $data);
			$where = array(
				'appid' => $base['appid'],
				'uid'=>$login_user['uid'],
				'teamwar_id' => array(
					$activity_ids,'in'
				)
			);
			$column = array(
				'teamwar_id','logistics_type','logistics_stat','address','logistics_num','logistics_id'
			);
			$nc_list_mod->setDbConf('shop', 'logistics');
			
			$logisticsInfo = $nc_list_mod->getDataList($where, $column, array(), array(), false);


            $express = include get_app_root().'/conf/express.conf.php';
            $_express = array();
            foreach($express as $cv){
                $_express[$cv['code']] = $cv['name'];
            }
            
			if($logisticsInfo){
				foreach($logisticsInfo as $val){
                    $aa = explode(':',$val['address']);
					$activity_id = intval($val['teamwar_id']);
					$result[$activity_id]['logistics_stat'] = intval($val['logistics_stat']);
					$result[$activity_id]['logistics_id'] = intval($val['logistics_id']);
					$result[$activity_id]['address'] = urldecode($aa[0]).' '.urldecode($aa[1]).' '.$aa[2];
					$result[$activity_id]['logistics_order'] = $_express[$val['logistics_type']].' '.$val['logistics_num'];
					$result[$activity_id]['logistics_num']=$val['logistics_num'];
				}
			}
		}
		
		$where = array(
			'appid' => $base['appid'],
			'goods_id' => array(
				$goods_ids,'in'
			)
		);
		$column = array(
			'title','sub_title','main_img','activity_type','goods_id'
		);
		 
	   $nc_list_mod->setDbConf('team', 'team_goods');  
		 
	 
		$goodsInfo = $nc_list_mod->getDataList($where, $column);
		foreach($goodsInfo as $val){
			foreach($goods_activity[$val['goods_id']] as $activity_id){
				$result[$activity_id]['goods_img'] = $val['main_img'];
				$result[$activity_id]['goods_title'] = $val['title'];
				$result[$activity_id]['goods_subtitle'] = $val['sub_title'];
				$result[$activity_id]['activity_type'] = intval($val['activity_type']);
			}
		}
		return $nc_list_mod->toArray($result);
	}
	/**
	 * 获取夺宝号
	 */
	public function getActivityNum($ipt_list, $base,$team=''){
		$nc_list_mod = Factory::getMod('nc_list');
		if(empty($ipt_list['uid'])){
			$login_user = app_get_login_user($base['sessid'], $base['appid'], false);
			$ipt_list['uid'] = $login_user['uid'];
		}
		//where条件
		$where = array(
			'uid' => $ipt_list['uid'],
			'appid' => $base['appid'],
			'activity_id' => $ipt_list['activity_id']
		); 
		$column = array(
			'activity_num'
		);
		if($team){
			$nc_list_mod->setDbConf('team', 'team_activity_num');
		}else{
			$nc_list_mod->setDbConf('shop', 'activity_num');
		}
		 
		$activityNum = $nc_list_mod->getDataList($where, $column);
		$result = array();
		$i = $j = 0;
		foreach($activityNum as $val){
			$result[$i][] = intval($val['activity_num']);
			$j++;
			if($j > 2){
				$i++;
				$j = 0;
			}
		}
		return $result;
	}

    public function userrecord($user,$msg){
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('main', 'user_extend');
		$time=time();
		$where=array('uid'=>$user['uid']);
		$usermsg = $nc_list->getDataOne($where, array(), array(), array(), false); 
		$cmsg=array();
		 

 	    if(!empty($usermsg)){ 

 	    		$updatemsg=json_decode($usermsg['user_msg'],true); 
 	    		foreach ($msg as $k=>$v){
 	    			if(is_array($v) && $v['count']){
						$updatemsg[$k]=$updatemsg[$k]+$v['count'];
					}else{
						$updatemsg[$k]=$v;
					} 
 	    			 
 	    		} 
 	    		$update['user_msg']=json_encode($updatemsg);
 	    		$nc_list->updateData($where,$update);
 	    	 
 	    	 

 	    }else{ 
	 	   foreach($msg as  $k=>$v){
				if(is_array($v) && $v['count']==1){
					$cmsg[$k]=1;
				}else{
					$cmsg[$k]=$v;
				}
			} 
 	    	 
 	    	$insertData['uid']=$user['uid'];
 	    	$insertData['user_msg']=json_encode($cmsg);  
 	    	$nc_list->insertData($insertData);
 	    	 
 	    }


	}

}