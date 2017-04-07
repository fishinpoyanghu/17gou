<?php
/**
 * @since 2016-01-20
 */
class TeamOrderTaskMod extends BaseMod{

	public function run(){ 
			 
		//后台任务
		//开启任务 拼团商品支付
	 
				 
		$this->openTask();
		while(1){
			//判断是否需要停止，不能用kill来杀死该进程，这会导致一个任务执行一半被终止
			if($this->isStop()){
				echo 'done\n';
				break;
			}  
			$this->endTeamTask();  
            $msg_mod = Factory::getMod('msg');
            $nc_list = Factory::getMod('nc_list');
			$nc_lock = Factory::getMod('nc_lock');
		 
			$nc_lock->lockFile('team_order_task','team_task');
				 
			//获取一个任务
			$taskInfo = $this->getOneTask();   
			if(empty($taskInfo)){//没有数据，sleep5秒
				$nc_lock->unLockFile('team_task');  //echo 'notask';exit;
				sleep(1);   
				continue;
			} 
			 
			$order_num = $taskInfo['order_num'];

			
			//获取订单信息
			$order = $this->getOrderInfo($order_num);  
			$orderInfo = $order['order_info'];
			$moneyInfo = $order['money_info'];
			//用户锁
			$nc_lock->lockFile('team_order_user'.$order['uid'], 'team_user');
			//用户剩余的金额
			$userMoney = $this->getUserMoney($order['uid']);
            //新版

            $userMoney2 = bcadd($userMoney['money'],$userMoney['yongjin'],2);

			//实际可以使用的金额
			if($userMoney2 > $moneyInfo['remain_use']){
				$totalMoney = $moneyInfo['remain_use'] + $moneyInfo['need_money'] ;
			}else{
				$totalMoney = $userMoney2 + $moneyInfo['need_money'] ;
			}
			$money = $totalMoney;
            $gogggg = '';

	 	   
			//计算夺宝号 目前团购都必须单独购买 若多团购买则修改下单跟这里
			if(!empty($orderInfo) && $totalMoney > 0){ 
				$currentTime = time();
				foreach($orderInfo as $val){
                    $end = false;
                    $ttt = 0;
                     
					$activity_id = $val['activity_id']; 
					//根据总金额过滤一遍
					if($val['num']>$totalMoney){ //金额不足够直接退出  这里不能去掉！！去掉等于小给了金钱,直接跳出循环退款
						$this->update_order_status($order,array('status'=>-3));
						//更新订单状态
						continue;
					}

					$num = ($val['num'] > $totalMoney) ? $totalMoney : $val['num'];

					//把福袋当做1个商品
                    if($order['goods_type']==6){ //福袋 
                    	$this->addluckypacket($val['num'],$order);//开团
                    	$totalMoney -= $val['num'];
                    	$gogggg='福袋';
                    	break;
                    }

					if($order['goods_type']==3){ //表示此订单是普通拼团 。即是开团 2.就是参团
						$nc_team = Factory::getMod('nc_team');
						$goods=$nc_team->verifiyTeam($order['uid'],$order['order_goods_id'],false);  
						if(!$goods){ //开团失败。//更新订单状态
							$this->update_order_status($order,array('status'=>-3));
							break;
						}
						 
						$this->createteam($order,$goods);//开团
						$totalMoney -= $num;//扣除金额
						break;
					}
					if($order['goods_type']==4){ //单独购买
						 
						 
						//处理订单
						//更新商品数量
						$this->dealsingleorder($order); 
						$msg=array('uid'=>$order['uid'], 'activity_id'=>$activityInfo['activity_id'],'goods_id'=>$order['order_goods_id'],'activity_type'=>7); 
						$msg_mod->sendSystNotify(2,$msg); 
						//插入订单到后台
						$totalMoney -= $num;
						break;
					}

					//获取活动信息
					$activityInfo = $this->getActivityInfo($activity_id);     
					if($activityInfo['flag'] > 1){//活动已经结束
						$this->update_order_status($order,array('status'=>-3));  
						continue;
					}
					$nc_lock->lockFile('team'.$activity_id, 'team');
                    //商品信息
                    $goodsmsg = $this->getGoods($activityInfo['goods_id']); 
                    $gogggg .= '【'.$goodsmsg['title'].'】';
					$where = array(
						'teamwar_id' => $activity_id
					);
					$activityData = array();  
					 //var_dump($activityInfo); 
					 //var_dump($goodsmsg );exit;
					if($goodsmsg['activity_type']!=2){ //普通拼团。团长免费拼团
						if($activityInfo['people_num'] <= $activityInfo['join_num'] + 1){
							$realNum = $num;  //realNum，$num, 等于是金额
	                        $end = true;  
	                    }else{
							$realNum = $num;  
							$nc_list->setDbConf('team', 'teamwar');
	                        $activityData['user_num'] = $activityInfo['user_num'] + $realNum;  
	                        $activityData['join_num'] = $activityInfo['join_num']+1;
	                        
	                        //修改活动状态
	                        $res=$nc_list->updateData($where, $activityData);    
						}
						 
					}else{ //幸运拼团岑团
						if($activityInfo['need_num'] <= $activityInfo['user_num'] + $num){
							$realNum = $activityInfo['need_num'] - $activityInfo['user_num'];  
	                        $end = true;
	                    }else{
							$realNum = $num;  
							$nc_list->setDbConf('team', 'teamwar');
	                        $activityData['user_num'] = $activityInfo['user_num'] + $realNum;  
	                        $activityData['join_num'] = $activityInfo['join_num']+1;
	                        
	                        //修改活动状态
	                        $res=$nc_list->updateData($where, $activityData);    
						}
					}
					 

                    if($realNum < 1){
                        continue;
                    }

					$totalMoney -= $realNum;
					//生成夺宝号
					 
					 $this->createActivityNum($realNum, $order, $activityInfo,$ttt,$goodsmsg);
				 
					 
					 
                    if($end){
                        $this->activityEnd($activityInfo,$goodsmsg['activity_type']); 
    					$msg=array('uid'=>$order['uid'], 'activity_id'=>$activityInfo['activity_id'],'goods_id'=>$activityInfo['goods_id'],'activity_type'=>6);  
                    }else{
                    	 $msg=array('uid'=>$order['uid'],'activity_id'=>$activityInfo['activity_id'],'goods_id'=>$activityInfo['goods_id'],'activity_type'=>5); 
                    }
                    $msg_mod->sendSystNotify(2,$msg); 
					$nc_lock->unLockFile('team');
					if($totalMoney <= 0){
						break;
					}

                }

				$realPayMoney = $money - $totalMoney;
				//没有用到的金额，退还回去
				/*if($realPayMoney == 0 && !empty($moneyInfo['packet_use'])){
					//如果都没有支付，且使用了红包，红包退回去
					$this->giveBackPacket($moneyInfo['packet_use'][0]);
				}*/
				
				//$realPayMoney = $realPayMoney - $moneyInfo['packet_use'][1];
				//实际支付的金额大于0，判断是否给用户发红包
                //计算支付信息
                $payInfo = array();

                if($userMoney['money']>=$realPayMoney){
                    $payInfo = array(
                        'money' => $realPayMoney,
                        'yongjin' => 0,
                    );
                }elseif(bcadd($userMoney['money'],$userMoney['yongjin'],2) >= $realPayMoney){
                    $payInfo = array(
                        'money' => $userMoney['money'],
                        'yongjin' => bcsub($realPayMoney,$userMoney['money'],2),
                    );
                }else{
                    $payInfo = array(
                        'money' => $userMoney['money'],
                        'yongjin' => $userMoney['yongjin'],
                    );
                }
                
				if(bcsub($realPayMoney,$payInfo['yongjin'],2) > 0){
					//$this->createPacket($order['uid'], $order['appid']);
                   // $this->createPoint($order['uid'], bcsub($realPayMoney,$payInfo['yongjin'],2));
                     $this->createFenxiao($order['uid'], bcsub($realPayMoney,$payInfo['yongjin'],2));
				}
				if($gogggg){
					$msg_mod->sendNotify(10002, $order['uid'], 10002, 5, 0, 7, '恭喜您购买'.$gogggg.'成功！祝您购物愉快！');
				}
                  
                //扣钱
				if($payInfo){
                    $this->delMoney($payInfo, $order['uid'], 1);
                }

                //是否要退钱
                if($realPayMoney > $userMoney2){
                    $this->addMoney($moneyInfo['need_money'] + $userMoney2 - $realPayMoney,$order['uid']); 
                    if($moneyInfo['need_money'] + $userMoney2 - $realPayMoney){
        			$msg_mod->sendNotify(10002,  $order['uid'], 10002, 5, 0, 7, '商品购买失败。你有一笔金额退回,金额：'.$moneyInfo['need_money'] + $userMoney2 - $realPayMoney.'元');
        			}
                }elseif($realPayMoney <= $userMoney2){
                    $this->addMoney($moneyInfo['need_money'],$order['uid']);
                    if($moneyInfo['need_money']){
                    	$msg_mod->sendNotify(10002,  $order['uid'], 10002, 5, 0, 7, '商品购买失败，你有一笔金额退回!金额：'.$moneyInfo['need_money'].'元');
                    }
                     
                }

			}

            //修改任务状态为完成并解锁
            $this->completeTask($taskInfo['task_id']);
            $nc_lock->unLockFile('team_task');
			$nc_lock->unLockFile('team_user');
			unset($nc_lock);
			unset($nc_list);      
		}
	}
	
	/**
	 * 获取一个任务
	 */
	public function getOneTask(){
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('team', 'team_task');
		$where = array(
			'flag' => 0
		);
		$column = array(
			'task_id','order_num'
		);
		$order = array(
			'rt' => 'asc'
		);
		$limit = array(
			'begin' => 0,
			'length' => 1
		);
		$data = $nc_list->getDataOne($where, $column, $order, $limit, false);
		return $data;
	}
	
	/**
	 * 修改任务状态为完成
	 */
	public function completeTask($task_id){
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('team', 'team_task');
		$where = array(
			'task_id' => $task_id
		);
		$data = array(
			'flag' => 1,
			'ut' => time()
		);
		$nc_list->updateData($where, $data);
	}
	
	/**
	 * 获取订单信息
	 */
	public function getOrderInfo($order_num){
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('shop', 'order');
		$where = array(
			'order_num' => $order_num
		);
		$data = $nc_list->getDataOne($where, array(), array(), array(), false);
		if(!empty($data['money_info'])){
			$data['money_info'] = json_decode($data['money_info'], true);
		}
		if(!empty($data['order_info'])){
			$data['order_info'] = json_decode($data['order_info'], true);
		}
		return $data;
	}
	 

	/**
	 * 获取活动信息
	 */
	public function getActivityInfo($activity_id){
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('team', 'teamwar');
		$where = array(
			'teamwar_id' => $activity_id
		);
		$column = array(
			 'goods_id','teamwar_id','need_num','user_num','flag','join_num','people_num','uid'
		);
		$data = $nc_list->getDataOne($where, $column, array(), array(), false);
	 	$data['activity_id']=$data['teamwar_id'];
		return $data;
	}
	
	/**
	 * 生成夺宝号  
	 * //当前就幸运购需要生成 普通拼团则不需要
	 */
	public function createActivityNum($realNum, $order, $activityInfo,$time=0,$goodsmsg){  
		$nc_list = Factory::getMod('nc_list'); 
	    if($goodsmsg['activity_type']==2){ //幸运拼团 
            $nc_list->setDbConf('team', 'team_num');

            $ttt = time().mt_rand(10000,99999).mt_rand(10000,99999);
            $sql2 = "update {$nc_list->dbConf['tbl']} set `uid`='{$order['uid']}',`ut`='{$ttt}' where `activity_id`='{$activityInfo['activity_id']}' and `uid`='' limit {$realNum}"; 
            $nc_list->executeSql($sql2);
            $where = array(
                'activity_id' => $activityInfo['activity_id'],
                'uid' => $order['uid'],
                'ut' => $ttt,
            );
            $res = $nc_list->getDataList($where,array('num'),array(),array(),false);
            $result = array();
            foreach($res as $r){
                $result[] = $r['num'];
            }
        }else{   //普通拼团岑团
        	$result[]=$order['uid'];
        }
        $nc_list->setDbConf('team', 'team_activity_num');

        if(!$time){
            $time = round(microtime(true)*1000);
        }
        $ms = substr($time.'',10,3);
        $rt = substr($time.'',0,10);
		$count = 1;
		$data = array();
		foreach($result as $val){
			$data[] = array(
				'appid' => $order['appid'],
				'activity_id' => $activityInfo['activity_id'],
				'order_num' => $order['order_num'],
				'uid' => $order['uid'],
				'activity_num' => $val,
				'ip' => $order['ip'],
				'ms' => $ms,
				'rt' => $rt,
				'ut' => $order['ut'],
                'this_num' => $realNum,
			);
			//两百条插入一次
			if($count > 200){
				$count = 1;
				$nc_list->insertMultyData($data);
				$data = array();
			}else{
				$count++;
			}
		}
		if(!empty($data)){
			$nc_list->insertMultyData($data);
		}
		//修改t_activity_user表
		$nc_list->setDbConf('team', 'team_member');
		$sql = "insert into ".DATABASE.".t_team_member( `teamwar_id`,`uid`,`num`, `rt`,`ut`)
				value( {$activityInfo['activity_id']},{$order['uid']},{$realNum},
				 {$order['rt']},{$order['ut']}) on duplicate key update 
				num=num+{$realNum},ut={$order['ut']}"; 
			//	echo $sql;
		$nc_list->executeSql($sql);  
		 file_put_contents('/tmp/team_order.log','realNum:'.var_export($realNum,true).'order:'.var_export($order,true).'activityInfo:'.var_export($activityInfo,true).'result:'.var_export($result,true),FILE_APPEND);
	}
	
	/**
	 * 获取用户剩余的金额
	 */
	public function getUserMoney($uid){
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('main', 'user');
		$where = array(
			'uid' => $uid
		);
		$column = array(
			'money',
            'yongjin'
		);
		$data = $nc_list->getDataOne($where, $column, array(), array(), false);
		return $data;
	}
	
	/**
	 * 退回红包
	 */
/*	public function giveBackPacket($packet_id){
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('shop', 'packet');
		$where = array(
			'packet_id' => $packet_id
		);
		$data = array(
			'flag' => 1
		);
		$nc_list->updateData($where, $data);
	}*/
	
	/**
	 * 用户剩余金额，多退少扣
	 * @param type int 1：扣钱，2：加钱
	 */
	public function delMoney($money, $uid, $type){
        if(empty($money)) return;
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('main', 'user');
		if($type == 1){
			$sql = "update {$nc_list->dbConf['tbl']} set ";
            $conf = array();
            if($money['money']>0){
                $conf[] = " money=money-{$money['money']} ";
                $detail1 = array(
                    'uid' => $uid,
                    'desc' => '消费',
                    'money' => 0-$money['money'],
                    'ut' => time(),
                    'type'=>2,
                );
            }
           
            if($money['yongjin']>0){
                $conf[] = " yongjin=yongjin-{$money['yongjin']} ";
                $detail3 = array(
                    'uid' => $uid,
                    'money' => 0-$money['yongjin'],
                    'desc' => '佣金消费:'.intval(0-$money['yongjin']).':',
                    'ut' => time(),
                    'appid' => '10002',
                    'type'=>3,
                );
            }
            if(empty($conf)) return;
            $sql .= join(',',$conf);
            $sql .= " where uid={$uid} ";
            $nc_list->executeSql($sql);
            $nc_list->setDbConf('shop', 'money');

            if($detail1){
                $nc_list->insertData($detail1);
            }
            if($detail3){
                $nc_list->insertData($detail3);
            }


        }/*else{
			$sql = "update tianhong.t_user set money=money+{$money} where uid={$uid}";
            $insert = array(
                'uid' => $uid,
                'desc' => '退款',
                'money' => $money,
                'ut' => time(),
            );
		}*/

    }

    public function addMoney($money, $uid,$title=''){
        if(empty($money)) return;
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('main', 'user');
        $sql = "update {$nc_list->dbConf['tbl']} set ";
        $conf = array();
        $conf[] = " money=money+{$money} ";
        $sql .= join(',',$conf);
        $sql .= " where uid={$uid} ";
        $nc_list->executeSql($sql);

        $nc_list->setDbConf('shop', 'money');
        $detail = array(
            'uid' => $uid,
            'desc' => $title?$title:'退回',
            'money' => $money,
            'ut' => time(),
        );
        $nc_list->insertData($detail);

    }
	
	/**
	 * 获取最后五十个夺宝号生成的时间
	 */
	public function getTimeSum($limit=50){
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('shop', 'activity_num');
		 
     $sql ="SELECT  a.`activity_num_id`,a.`uid`,a.`rt`,a.`ms`,a.`this_num`,a.`activity_id`,c.`title`   FROM (SELECT a.`activity_num_id`,a.`uid`,a.`rt`,a.`ms`,a.`this_num`,a.`activity_id` FROM  
     ".DATABASE.".t_activity_num a   GROUP BY a.ut  ORDER BY a.`ut` DESC  LIMIT {$limit})   AS a  LEFT JOIN  ".DATABASE.".t_activity b ON   a.`activity_id`=b.`activity_id` LEFT JOIN  ".DATABASE.".t_goods c  ON b.`goods_id`=c.`goods_id`";  
        $data = $nc_list->getDataBySql($sql,false);
		if(empty($data)) return 0;
		$sum = '0';
		$result = array();
		foreach($data as $val){
            $sum = bcadd($sum,date('YmdHis',$val['rt']).$val['ms']);
			$result[] = array(
				'id' => $val['activity_num_id'],
				'uid' => $val['uid'],
                'rt' => date('YmdHis',$val['rt']).$val['ms'],
                'num' => $val['this_num'],
                'activity_id' => $val['activity_id'],
                'title' => $val['title'],
			);
		}
		return array(
			'sum' => $sum,
			'data' => $result
		);
	}
	
	/**
	 * 本期活动已经结束
	 */
	public function activityEnd($activityInfo,$goodstype=0){
		$nc_list = Factory::getMod('nc_list');
		$currentTime = time();
		$where = array(
			'teamwar_id' => $activityInfo['activity_id']
		);
		$nc_list->setDbConf('team', 'teamwar');
		if($goodstype!=2){ //非幸运拼团 不用生成幸运码
			$activityData['flag'] = 8; 
		    $activityData['join_num'] = $activityInfo['people_num'];  
			$activityData['publish_time'] =  $this->getPublishTime($currentTime);  
			$nc_list->updateData($where, $activityData);
			$where['flag']=1;
			$nc_list->setDbConf('shop', 'order');  
			$nc_list->updateData($where,array('status'=>3)); //根据团id更新所有团订单状态。发货！！ 
			if($activityInfo['people_num']==8 || $activityInfo['people_num']==2){
				$this->addlogistics($order,$activityInfo);
			}
			 
			//生成任务7天后处理赠送福袋
			$work_task = Factory::getMod('nc_work_task');
			$content=array('teamwar_id'=> $activityInfo['activity_id'],'day'=>7);
		    $work_task->addworktask($content);

		}else{ //幸运拼团
			$activityData['flag'] = 7; 
			$activityData['publish_time'] =  $this->getPublishTime($currentTime);
	        $activityData['user_num'] = $activityInfo['need_num']; 
	        $activityData['join_num'] = $activityInfo['people_num']; 
			//修改活动状态 
			$nc_list->updateData($where, $activityData);
			
			 $nc_list->setDbConf('team', 'team_num');
	            //删除上一个活动的num
	        $sql5 = "delete from {$nc_list->dbConf['tbl']} where `activity_id`='{$activityInfo['activity_id']}'";
	        $nc_list->executeSql($sql5);  
			$lastFifty = $this->getTimeSum();
	        file_put_contents('/tmp/team_false.log','50'.var_export($lastFifty,true),FILE_APPEND);
	        //在t_lucky_num中插入数据
			$luckyInfo = array(
				'appid' => 10002,
				'activity_id' => $activityInfo['activity_id'],
				'goods_id' => $activityInfo['goods_id'],
				'time_sum' => $lastFifty['sum'],//最后五十个夺宝时间
				'need_num' => $activityInfo['need_num'],
				'user_record' => json_encode($lastFifty['data']),
				'rt' => $currentTime,
				'ut' => $currentTime
			);
			$nc_list->setDbConf('team', 'team_lucky_num');
			$nc_list->insertData($luckyInfo);  //生成幸运码

		}
		 $this->goodsale($activityInfo['goods_id'],$activityInfo['people_num']);
	    $goodsql ="SELECT  count(t.flag) count ,g.team_limit FROM  ".DATABASE.".t_teamwar  t  LEFT JOIN  ".DATABASE.".t_team_goods g ON g.goods_id=t.goods_id   where  g.goods_id={$activityInfo['goods_id']} and t.flag >=7  and g.status=1 and g.is_in_activity=2 and t.stat=0"; 
	    // echo $goodsql;
		 $msg =  $nc_list->getDataBySql($goodsql,false);// var_dump($msg);
		 if($msg[0]['team_limit'] && $msg[0]['count']>=$msg[0]['team_limit']){ //结束活动
		 	  $sql = "update  ".DATABASE.".t_team_goods set `is_in_activity`=1 where `goods_id`={$activityInfo['goods_id']}";
        	  $nc_list->executeSql($sql); 
        	  // echo $sql,'124124124';
		}

	   //  $this->addpacket($activityInfo);

	}
	//福袋个数
	public function addpacket($activityInfo){
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('main', 'user');
		$num=1;//福袋个数
		$sql = "update {$nc_list->dbConf['tbl']} set `lucky_packet`=`lucky_packet`+$num where `uid`={$activityInfo['uid']}"; 
        $result=$nc_list->executeSql($sql);   
		$goods=$this->getgoods($activityInfo['goods_id']);
		$msg_mod = Factory::getMod('msg');  
        $content=json_encode(array('goods_name'=>$goods['title']));  
        $msg_mod->sendPacketNotify($activityInfo['uid'],3,$num,$content,$activityInfo['goods_id'],2,$activityInfo['teamwar_id']);
        
	}
	/**
	 * 揭晓时间 开奖时间
	 */
	public function getPublishTime($currentTime){
        return $currentTime + 600;
		/*$h = date('H', $currentTime);
		if($h < 9){//小于9点的，等到9点10分开奖
			return strtotime(date('Y-m-d 09:11:00'));
		}else if($h == 23){//等于23点的，等到第二天9点10分开奖
			return strtotime(date('Y-m-d 09:11:00')) + 86400;
		}else{
			return $currentTime + 600;
		}*/
	}
	
	/**
	 * 判断是否给用户发红包
	 */
/*	public function createPacket($uid, $appid){
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('main', 'user');
		$where = array(
			'uid' => $uid
		);
		$column = array(
			'nick','rebate_uid'
		);
		$userInfo = $nc_list->getDataOne($where, $column, array(), array(), false);
		
		$where = array(
			'uid' => $userInfo['rebate_uid'],
			'invite_uid' => $uid,
		);
		$nc_list->setDbConf('shop', 'invite_count');
		$inviteCount = $nc_list->getDataList($where, array(), array(), array(), false);
		//如果已经有记录，说明不是第一次，则返回。
		if(!empty($inviteCount)) return;
		
		$currentTime = time();
		$data = array(
			'uid' => $userInfo['rebate_uid'],
			'invite_uid' => $uid,
			'rt' => $currentTime
		);
		//记录邀请
		$nc_list->insertData($data);
		//第一次消费，发送红包
		$packet_mod = Factory::getMod('nc_packet');
		$packet_mod->createConsumePacket($appid, $uid);
		
		if($userInfo['rebate_uid'] > 0){//有邀请码的
			$where = array(
				'uid' => $userInfo['rebate_uid']
			);
			$nc_list->setDbConf('shop', 'invite_count');
			$inviteInfo = $nc_list->getDataList($where, array(), array(), array(), false);
			$count = count($inviteInfo);
			if($count < 3){
				$packet_mod->createInvitePacket($appid, $userInfo['rebate_uid'], $userInfo['nick'], $count);
			}
		}
	}*/

    public function createPoint($uid,$money){
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'point_rule');
        $where = array('type'=>'消费');
        $ret = $nc_list->getDataOne($where, array(), array(), array(), false);
        if($money<$ret['limit']){
            return false;
        }
        $num = floor($money/$ret['limit']);
        $time = time();
        $point = intval($ret['point']*$num);
        if($num>0){
            $nc_list->setDbConf('shop', 'point_detail');
            $nc_list->insertData(array(
                'uid' => $uid,
                'desc' => '消费',
                'point' => $point,
                'ut' => $time,
            ));
            $nc_list->setDbConf('shop', 'point');
            $sql = "insert into {$nc_list->dbConf['tbl']} (`uid`,`point`,`total`,`use`,`ut`) values({$uid},{$point},{$point},0,{$time}) on duplicate key update `point`=`point`+{$point},`total`=`total`+{$point},`ut`={$time}";
            $nc_list->executeSql($sql);
        }

    }

    public function createFenxiao($uid,$money){
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'fenxiao');
        $ret = $nc_list->getDataList(array(), array(), array(), array(), false);
        if(empty($ret)) return;
        $percent = array();
        foreach($ret as $v){
            $percent[$v['level']] = $v['percent'];
        }
        $nc_list->setDbConf('main', 'user');
        //level 1
        $where = array(
            'uid' => $uid,
        );
        $ret = $nc_list->getDataOne($where, array(), array(), array(), false);
        if(empty($ret) || $ret['rebate_uid']==0) return;

        $sql ="SELECT count(uid) count from ".DATABASE.".t_order where uid={$uid} and flag=1 AND order_type=0";  
	    $count = $nc_list->getDataBySql($sql,false);
	    if($count[0]['count']==1){
       		 $insert = array(
	            'uid' => $ret['rebate_uid'],
	            'money' => 1,
	            'desc' => '首单佣金:'.$money.':'.urlencode($name),
	            'ut' => time(),
	            'appid' => '10002',
	            'pay_uid'=>$uid,
	            'lev'=>1,
	            'type'=>1,
	        );
	        $nc_list->setDbConf('shop', 'money');
	        $nc_list->insertData($insert); 	
	        $nc_list->setDbConf('main', 'user');
            $sql = "update {$nc_list->dbConf['tbl']} set `yongjin`=`yongjin`+1 where `uid`={$ret['rebate_uid']}";
            $nc_list->executeSql($sql);
	     }

	    return ''; //拼团不做分佣。只是送1元 .
        $name = $ret['nick'];
        $money1 = round($percent[1]*0.01*$money,2);
        $insert = array(
            'uid' => $ret['rebate_uid'],
            'money' => $money1,
            'desc' => '佣金:'.$money.':'.urlencode($name),
            'ut' => time(),
            'appid' => '10002',
            'pay_uid'=>$uid,
            'lev'=>1,
            'type'=>1,
        );
        $nc_list->setDbConf('shop', 'money');
        $nc_list->insertData($insert);
        $nc_list->setDbConf('main', 'user');
        $sql = "update {$nc_list->dbConf['tbl']} set `yongjin`=`yongjin`+{$money1} where `uid`={$ret['rebate_uid']}";
        $nc_list->executeSql($sql);
        //level 2
        $where = array(
            'uid' => $ret['rebate_uid'],
        );
        $ret = $nc_list->getDataOne($where, array(), array(), array(), false);
        if(empty($ret) || $ret['rebate_uid']==0) return;
        $money2 = round($percent[2]*0.01*$money,2);
        $insert = array(
            'uid' => $ret['rebate_uid'],
            'money' => $money2,
            'desc' => '佣金:'.$money.':'.urlencode($name),
            'ut' => time(),
            'appid' => '10002',
            'pay_uid'=>$uid,
            'lev'=>2,
            'type'=>1,
        );
        $nc_list->setDbConf('shop', 'money');
        $nc_list->insertData($insert);
        $nc_list->setDbConf('main', 'user');
        $sql = "update {$nc_list->dbConf['tbl']} set `yongjin`=`yongjin`+{$money2} where `uid`={$ret['rebate_uid']}";
        $nc_list->executeSql($sql);
        //level 3
        $where = array(
            'uid' => $ret['rebate_uid'],
        );
        $ret = $nc_list->getDataOne($where, array(), array(), array(), false);
        if(empty($ret) || $ret['rebate_uid']==0) return ;
        $money3 = round($percent[3]*0.01*$money,2);
        $insert = array(
            'uid' => $ret['rebate_uid'],
            'money' => $money3,
            'desc' => '佣金:'.$money.':'.urlencode($name),
            'ut' => time(),
            'appid' => '10002',
            'pay_uid'=>$uid,
            'lev'=>3,
            'type'=>1,
        );
        $nc_list->setDbConf('shop', 'money');
        $nc_list->insertData($insert);
        $nc_list->setDbConf('main', 'user');
        $sql = "update {$nc_list->dbConf['tbl']} set `yongjin`=`yongjin`+{$money3} where `uid`={$ret['rebate_uid']}";
        $nc_list->executeSql($sql);

    }
	
	/**
	 * 开启任务
	 */
	public function openTask(){
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('shop', 'signal');
		$where = array(
			'signal_type' => 'order_task'
		);
		$data = array(
			'signal_val' => 'start'
		);
		$nc_list->updateData($where, $data);
	}
	
	/**
	 * 判断是否结束
	 */
	public function isStop(){
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('shop', 'signal');
		$where = array(
			'signal_type' => 'order_task'
		);
		$column = array('signal_val');
		$data = $nc_list->getDataOne($where, $column, array(), array(), false);
		if(!empty($data) && $data['signal_val'] == 'stop'){
			return true;
		}
		return false;
	}

    public function strAdd($str1,$str2){
        $res = array();
        if(strlen($str1) > strlen($str2)){
            $str2 = str_pad($str2,strlen($str1),'0',STR_PAD_LEFT);
        }
        else{
            $str1 = str_pad($str1,strlen($str2),'0',STR_PAD_LEFT);
        }
        for($i = strlen($str1)-1; $i>=0; $i--){
            $tmp = $str1[$i] + $str2[$i];
            if(isset($res[$i])){
                $res[$i] += $tmp;
            }else{
                $res[$i] = $tmp;
            }
            if($res[$i] >= 10) {
                $res[$i] -= 10;
                $res[$i-1] += 1;
            }
        }
        ksort($res);
        $res = implode('',$res);
        return $res;
    }

    public function getFalse($active_id,$need,$t){
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'activity_num');
        $sql = "select a.activity_num from {$nc_list->dbConf['tbl']} a,`".DATABASE."`.`t_false` b where a.`activity_id`={$active_id} and a.uid=b.uid limit 200";
        $data = $nc_list->getDataBySql($sql,false);
        file_put_contents('/tmp/false.log','data'.var_export($data,true),FILE_APPEND);

        if(empty($data)) return 0;
        $lucky = array();
        foreach($data as $d){
            $lucky[] = $d['activity_num'];
        }
        sort($lucky);
        $time = round(microtime(true)*1000);
        $total = bcadd($t,date('YmdHis',substr($time,0,10)).substr($time,10,3));
        file_put_contents('/tmp/false.log','total'.$total,FILE_APPEND);

        $changeNum = bcadd(bcmod($total,$need),10000001);

        foreach($lucky as $l){
            if($l >= $changeNum){
                $relative = $l - $changeNum;
                break;
            }else{
                $relative = $l - $changeNum;
            }
        }
        file_put_contents('/tmp/false.log','relative'.$relative,FILE_APPEND);

        $time = bcadd($time,$relative);
        return $time;
    }

    public function getFalseUser($active_id,$limit=false){
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 't_false');
        $where = array(
            'stat' => 0,
        );
        $list = $nc_list->getDataList($where,array('uid'),array(),array(),false);
        if(empty($list)) return array();
        $data = array();
        foreach($list as $l){
            $data[] = $l['uid'];
        }
        $datas = join(',',$data);
        $sql = "select count(*) `total` from `t_activity_user` where `uid` in ({$datas}) and `activity_id`='{$active_id}'";
        $res = $nc_list->getDataBySql($sql,false);
        if($res['total']<5 && (mt_rand(0,10)%2)==1){
            for($i=0;$i<5;$i++){
                shuffle($data);
                $d = array_pop($data);
                if($limit){
                    //限购，每人只能买一次
                    $nc_list->setDbConf('shop', 'activity_user');
                    $wher = array(
                        'activity_id' => $active_id,
                        'uid' => $d['uid'],
                    );
                    $user_num = $nc_list->getDataOne($wher,array('user_num'),array(),array(),false);
                    if(empty($user_num)){
                        break;
                    }
                }else{
                    break;
                }
            }
            file_put_contents('/tmp/false.log','getFalseUser'.$d,FILE_APPEND);
            return array('uid'=>$d);
        }
        return array();
    }

    public function getGoods($goods){
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('team', 'team_goods');
        $where = array(
            'goods_id' => $goods,
        );
        $data = $nc_list->getDataOne($where,array(),array(),array(),false);
        return $data;
    }

    public function listIp($uid){
        $a = array(
            '210.012.009.214',
            '210.012.010.214',
            '210.012.012.214',
            '210.012.013.214',
            '210.012.014.214',
            '210.012.015.214',
            '210.012.016.014',
            '210.012.017.214',
            '210.012.018.014',
            '210.012.019.014',
            '210.012.020.214',
            '210.012.023.214',
            '210.012.024.214',
            '210.012.026.114',
            '210.012.027.214',
            '210.012.028.214',
            '210.012.029.214',
            '210.012.030.214',
            '210.012.031.214',
            '210.012.033.214',
            '210.012.034.214',
            '210.012.035.114',
            '210.012.036.214',
            '210.012.037.214',
            '210.012.038.014',
            '210.012.039.214',
            '210.012.040.014',
            '210.012.041.214',
            '210.012.042.214',
            '210.012.043.214',
            '210.012.045.214',
            '210.012.058.214',
            '210.012.059.214',
            '210.012.060.214',
        );
        $index = $uid%count($a);
        return ip2long($a[$index]);
    }
    //此处注意幸运购，及团长免费类型的订单 开团的人都不要回款！
    public function endTeamTask(){  
    	  $nc_list = Factory::getMod('nc_list');
    	  $nc_list->setDbConf('team', 'teamwar');
    	  $time=time();   
		  $sql="select  w.teamwar_id, g.title  from   ".DATABASE.".t_teamwar w  left join  ".DATABASE.".t_team_goods g on w.goods_id=g.goods_id  where w.flag=1 and w.et < $time";  
        $list = $nc_list->getDataBySql($sql,false);   	 
	 	if(empty($list)){
	 		return ;
	 	} 
        foreach($list as $val){  
			 $sql="select * from  ".DATABASE.".t_team_activity_num where `activity_id` = {$val['teamwar_id']} group by `order_num`";
			 $teamNum=$nc_list->getDataBySql($sql,false);    
			 $sql = "update  ".DATABASE.".t_teamwar set `flag`=2 where `teamwar_id`={$val['teamwar_id']}";
			 $ret=$nc_list->executeSql($sql); //先执行更新停止 
			  $sql = "update  ".DATABASE.".t_order set `status`=-1 where `teamwar_id`={$val['teamwar_id']} and flag=1";
			 $ret=$nc_list->executeSql($sql); //先执行更新停止 

			 if($teamNum){
			 	$this->refund($teamNum,$val);
			 } 
        	 
        } 

    }
    //退款
    public function refund($teamNum,$teammsg){   
    	    $data = array();$refund=array();//微信等第三方支付付款
    	    $nc_list = Factory::getMod('nc_list');
    	    $nc_list->setDbConf('main', 'refund');
        foreach($teamNum as $val){ 
             $sql = "select uid,money_info,order_num,transaction_id  from ".DATABASE.".t_order    where flag=1 and pay_type=1  and order_num={$val['order_num']} and transaction_flag=1";  
             $row= $nc_list->getdatabysql($sql,false);   
             if($row){
                $needmoney=json_decode($row[0]['money_info'],true);
                if($needmoney['need_money']>0 && $needmoney['need_money']<=$val['this_num']){
                    $moneyinfo['need_money']=$needmoney['need_money']; 
                    $moneyinfo['transaction_id']=$row[0]['transaction_id'];
                    $moneyinfo['order_num']=$row[0]['order_num'];
                    $moneyinfo['uid']=$row[0]['uid'];
                    $moneyinfo['flag']=0;
                    $refund[]=$moneyinfo;
                    $val['this_num']=$val['this_num']-$needmoney['need_money'];
                }
             }
             $data[$val['uid']][] = $val['this_num'];
        } 

        if(!empty($refund)){  
             $time=time();
             $nc_list->setDbConf('main', 'refund');
            foreach($refund as $v){
                $v['rt']=$time;
                $v['ut']=$time;
                $v['pay_type']=1;
                $ret=$nc_list->insertData($v);
            }
        } 
       // var_dump($refund);var_dump($ret);
        foreach($data as $uid=>$money){
            $total = array_sum($money); 
            //生成退款明细,更新账户余额 
            $this->addmoney($total,$uid,'活动结束->退款');
			$usql = "update  ".DATABASE.".t_team_member set `status`=1 where `teamwar_id`={$teammsg['teamwar_id']} and uid=$uid"; 
			$uset=$nc_list->executeSql($usql); 
			$msg_mod = Factory::getMod('msg'); //发送通知 
			$msg_mod->sendNotify(10002,  $uid, 10002, 5, 0, 7,"您参与的商品【{$teammsg['title']}】,第{$teammsg['teamwar_id']}期参团活动由于超过活动有效期，被系统自动终止，您的购买记录将退回余额，请注意查收"); 
        }
       


    }
    //开团
    public function createteam($order,$goods){ 
    	$nc_list = Factory::getMod('nc_list'); 
    	$nc_list->setDbConf('team', 'teamwar');   
        $time=time();  
        $wardata = array( 
                'goods_id' => $order['order_goods_id']+0,
                'teamwar_id'=>get_auto_id(C('AUTOID_M_TEAMWAR')),
                'uid'=>$order['uid'], 
                'ut' => $time,
                'rt' => $time,
                'et' => $goods[0]['end_day']>0?$time+$goods[0]['end_day']*3600*24:$time+3600*24*3,
                'people_num'=>$goods['0']['people_num'],//默认10个人
                'need_num'=>$goods['0']['price']*$goods['0']['people_num'],
                'join_num'=>1,
                'user_num'=>$order['order_info'][0]['num'] //团长默认算1人 也就是说只需要拉10人就开团成功 
            );
        $ret=$nc_list->insertData($wardata);
        $this->update_order_status($order,array('status'=>2,'teamwar_id' => $wardata['teamwar_id'])); //待成团
         
        $nc_list->setDbConf('team', 'team_member'); 
        $teamdata = array( 
                'teamwar_id' => $wardata['teamwar_id'], 
                'uid'=>$order['uid'], 
                'ut' => $time,
                'rt'=>$time,
                'address_id'=>$order['address_id'],
                'num'=>$order['order_info'][0]['num'] //团长不用参与
            );
       $mret=$nc_list->insertData($teamdata); 
         
       $nc_list->setDbConf('team', 'team_activity_num'); //为了回款。。 
       $activitydata = array( 
                'appid' => 10002,
				'activity_id' => $wardata['teamwar_id'],
				'order_num' => $order['order_num'],
				'uid' => $order['uid'], 
				'activity_num' => $time.rand(0,10000),//当前 activity_id 跟activity_num 组成唯一。这里必须随机
				'ip' => $order['ip'],
				'ms' => rand(0,100000),
				'rt' => $time,
				'ut' => $time,
                'this_num' => $order['order_info'][0]['num'],

            );
       $aret=$nc_list->insertData($activitydata); 
       $msg_mod = Factory::getMod('msg');
       $msg=array('uid'=>$order['uid'], 'activity_id'=>$wardata['teamwar_id'],'goods_id'=>$order['order_goods_id'],'activity_type'=>4); 
       $msg_mod->sendSystNotify(2,$msg);  
         
    }
    public function addluckypacket($num,$order){
    	$nc_list = Factory::getMod('nc_list'); 
    	$nc_list->setDbConf('main', 'user');   
    	$sql = "update {$nc_list->dbConf['tbl']} set `lucky_packet`=`lucky_packet`+$num where `uid`={$order['uid']}"; 
        $result=$nc_list->executeSql($sql);   
        $msg_mod = Factory::getMod('msg');  
        $content=json_encode(array('num'=>$num,'give_count'=>0));  
        $msg_mod->sendPacketNotify($order['uid'],4,$num,$content,0,0,0);

    }
    public function dealsingleorder($order){
    	$time=time();
    	$where['uid']=$order['uid'];
    	$where['address_id']=$order['address_id']; 
    	$nc_list = Factory::getMod('nc_list'); 
    	$nc_list->setDbConf('main', 'address'); 
    	 
    	$addressInfo = $nc_list->getDataOne($where, array(), array(), array(), false); 

        $address = $addressInfo?urlencode($addressInfo['province'].$addressInfo['city'].$addressInfo['area'].$addressInfo['detail']).':'.urlencode($addressInfo['name']).':'.$addressInfo['mobile']:''; 
    	$insert['appid']=10002;
    	$insert['activity_id']=0;
    	$insert['logistics_type']='';
    	$insert['address']=$address; 
    	$insert['logistics_num']='';
    	$insert['logistics_stat']=0;
    	$insert['ut']=$time;
    	$insert['rt']=$time;
    	$insert['teamwar_id']=-1;
    	$insert['uid']=$order['uid'];
    	$insert['order_num']=$order['order_num'];
        $nc_list->setDbConf('shop', 'logistics'); 
    	$nc_list->insertData($insert);//单独购买添加快递地址
    	 
           
        $this->update_order_status($order,array('status'=>3));  

    	$this->goodsale($order['order_goods_id'],$order['order_info'][0]['goods_num']);


    }
    //拼团成功后帮每一个订单添加快递地址（幸运拼团除外）
    public function addlogistics($order,$activityInfo){
    	if($order['activity_type']==2){   return ''; 	}
    	$time=time();
        $nc_list = Factory::getMod('nc_list');  
        $nc_list->setDbConf('shop', 'order');   
    	$where['teamwar_id']=$activityInfo['teamwar_id'];
    	$where['flag']=1;
    	$where['status']=3;   //根据团号获取相同团的订单。注意。当前退款订单不在此列
    	$team_order_list = $nc_list->getDataList($where, array(), array(), array(), false); 
 		 
 		// var_dump($team_order_list);echo 123;
    	foreach($team_order_list as $v){
	        $condition['uid']=$v['uid'];
	    	$condition['address_id']=$v['address_id'];  
	    	$nc_list->setDbConf('main', 'address'); 
	    	$addressInfo = $nc_list->getDataOne($condition, array(), array(), array(), false); 
        	$address = $addressInfo?urlencode($addressInfo['province'].$addressInfo['city'].$addressInfo['area'].$addressInfo['detail']).':'.urlencode($addressInfo['name']).':'.$addressInfo['mobile']:''; 
        	echo $nc_list->getlastsql();
	        $insert['appid']=10002;
	    	$insert['activity_id']=0;
	    	$insert['logistics_type']='';
	    	$insert['address']=$address; 
	    	$insert['logistics_num']='';
	    	$insert['logistics_stat']=0;
	    	$insert['ut']=$time;
	    	$insert['rt']=$time;
	    	$insert['teamwar_id']=$v['teamwar_id'];
	    	$insert['uid']=$v['uid'];
	    	$insert['order_num']=$v['order_num'];
	        $nc_list->setDbConf('shop', 'logistics'); 
	    	$nc_list->insertData($insert);//单独购买添加快递地址
	    	//echo 1111;var_dump($insert);


    	}
    	 
    	 


    }
    //增加商品售出数量
 	public function goodsale($goodsid,$num){
 		if( (!$goodsid) || (!$num)){ return ''; }
 		$nc_list = Factory::getMod('nc_list');
 		$nc_list->setDbConf('team', 'team_goods'); 
 	    $sql = "update {$nc_list->dbConf['tbl']} set `sale_num`=`sale_num`+{$num} where `goods_id`=$goodsid "; 
 		$nc_list->executeSql($sql);
 	}
 	//更订单状态
 	public function update_order_status($order,$update){
 	    $nc_list = Factory::getMod('nc_list');
 		$nc_list->setDbConf('shop', 'order'); 
 	    $where = array(
            'order_id' =>$order['order_id']
        ); 
        $nc_list->updateData($where,$update);

 	//	$nc_list->executeSql($sql);

 	}


}
