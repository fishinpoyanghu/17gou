<?php
/**
 * @since 2016-01-20
 */
class NcOrderTaskMod extends BaseMod{

	public function run(){
		//后台任务
		//开启任务
		  
		$this->openTask();
		while(1){

			//判断是否需要停止，不能用kill来杀死该进程，这会导致一个任务执行一半被终止
			if($this->isStop()){
				echo 'done\n';
				break;
			} 
            $msg_mod = Factory::getMod('msg');
            $nc_list = Factory::getMod('nc_list');
			$nc_lock = Factory::getMod('nc_lock');
			
			$nc_lock->lockFile('order_task','task');
			//获取一个任务
			$taskInfo = $this->getOneTask();
			if(empty($taskInfo)){//没有数据，sleep5秒
				$nc_lock->unLockFile('task');
				sleep(1);   
				continue;
			}
			 
			$order_num = $taskInfo['order_num'];

			
			//获取订单信息
			$order = $this->getOrderInfo($order_num);

			$orderInfo = $order['order_info'];
			$moneyInfo = $order['money_info'];
			//用户锁
			$nc_lock->lockFile('order_user'.$order['uid'], 'user');
			//用户剩余的金额
			$userMoney = $this->getUserMoney($order['uid']);
            //新版
			$userMoney['money']=isset($moneyInfo['luckypacket_num'])?$userMoney['money']+$moneyInfo['luckypacket_num']:$userMoney['money'];//如果有福袋把福袋加到金额里面去
            $userMoney2 = bcadd($userMoney['money'],$userMoney['yongjin'],2);

			//实际可以使用的金额
			if($userMoney2 > $moneyInfo['remain_use']){
				$totalMoney = $moneyInfo['remain_use'] + $moneyInfo['need_money'] ;
			}else{
				$totalMoney = $userMoney2 + $moneyInfo['need_money'] ;
			}
			$money = $totalMoney;
            $gogggg = '';   
			//计算夺宝号
			if(!empty($orderInfo) && $totalMoney > 0){
				$currentTime = time();
				foreach($orderInfo as $val){
                    $end = false;
                    $ttt = 0;
					$activity_id = $val['activity_id'];
					//根据总金额过滤一遍
					$num = ($val['num'] > $totalMoney) ? $totalMoney : $val['num'];
					$nc_lock->lockFile('activity'.$activity_id, 'activity');
					
					//获取活动信息
					$activityInfo = $this->getActivityInfo($activity_id);  
					if($activityInfo['flag'] > 0){//活动已经结束
						continue;
					}
                    //商品信息
                    $goos = $this->getGoods($activityInfo['goods_id']);
                    $gogggg .= '【'.$goos['title'].'】';
					$where = array(
						'activity_id' => $activity_id
					);
					$activityData = array();

                    //
                    if($goos['activity_type'] == 3){
                        //限购
                        $nc_list->setDbConf('shop', 'activity_user');
                        $wher = array(
                            'activity_id' => $activity_id,
                            'uid' => $order['uid'],
                        );
                        $user_num = $nc_list->getDataOne($wher,array('user_num'),array(),array(),false);
                        if(($user_num['user_num'] + $num) > 10){
                            $num = 10-$user_num['user_num'];
                        }
                    }
                   if($goos['activity_type'] == 4){ 
                    	$goodsnumber=($activityInfo['need_num']-$activityInfo['user_num'])/$num;  
		            	if(!($goodsnumber==1 || $goodsnumber==2) ){
		                	//二人购商品只能是总数量全部或者一半 直接返回钱 
		                	/*$process = round(($activityInfo['user_num']/$activityInfo['need_num'])*100);
		                 	$twobuy=array('activity_id'=>$activity_id,'num'=>$num,'uid'=>$order['uid'],'process'=>$process);
		                 	$num=0; */
		                 	continue; //直接跳出循环
		            	} 
                    	 
                    }


					if($activityInfo['need_num'] <= $activityInfo['user_num'] + $num){  
						$realNum = $activityInfo['need_num'] - $activityInfo['user_num'];
						//本期活动已经结束
                        if($activityInfo['is_false']==1){
                            //计算false数据
                            $_total = $this->getTimeSum(49);
                            $ttt = $this->getFalse($activity_id,$activityInfo['need_num'],$_total['sum']);
                        }
                        $end = true;
                    }else{  
						$realNum = $num; 
						$nc_list->setDbConf('shop', 'activity');
                        $activityData['user_num'] = $activityInfo['user_num'] + $realNum;
                        $activityData['process'] = round((($activityInfo['user_num'] + $realNum)/$activityInfo['need_num'])*100);

                        if($activityInfo['is_false']==1){
                            //false

                            if($activityData['process']>=20){
                                $max = $activityInfo['need_num']-$activityInfo['user_num']-$num;
                                if($max>1){
                                    //1元和十元
                                    $limit = false;
                                    if($goos['activity_type']==2){
                                        if($max<=10){
                                            $false_num = 0;
                                        }else{
                                            $false_num = 10;
                                        }
                                    }elseif($goos['activity_type']==1){
                                        $false_num = (int) mt_rand(1,min($max-1,10));
                                    }elseif($goos['activity_type']==3){
                                        $false_num = (int) mt_rand(1,min($max-1,10));
                                        $limit = true;
                                    }else{
                                        $false_num = 0;
                                    }
                                    if($false_num>0){
                                        $f_u = $this->getFalseUser($activity_id,$limit);
                                        if($f_u['uid']){
                                            $__a = mt_rand(1,10);
                                            $false_order = array(
                                                'appid' => $order['appid'],
                                                'order_num' => date('YmdHis').(10000000000 + $f_u['uid']),
                                                'uid' => $f_u['uid'],
                                                'ip' => $this->listIp($f_u['uid']),
                                                'ut' => time()-$__a,
                                                'ms' => mt_rand(100,999),
                                                'rt' => time()-$__a,
                                            );

                                            $this->createActivityNum($false_num,$false_order,$activityInfo,(time()-mt_rand(0,5)).mt_rand(100,999));
                                            $activityInfo['user_num'] += $false_num;
                                            $activityData['user_num'] += $false_num;
                                            $activityData['process'] = round((($activityInfo['user_num'] + $realNum + $false_num)/$activityInfo['need_num'])*100);
                                        }
                                    }
                                }
                            }
                        }



                        //修改活动状态
                        $nc_list->updateData($where, $activityData);
					}

                    if($realNum < 1){
                        continue;
                    }

					$totalMoney -= $realNum;
					//生成夺宝号
					$this->createActivityNum($realNum, $order, $activityInfo,$ttt);
					 if($goos['activity_type']==6){
						$end=true;//幸运购1个人买一次
					}
                    if($end){
                       $this->activityEnd($activityInfo,$goos['activity_type']); 
                       $msg=array('uid'=>$order['uid'],'activity_id'=>$activityInfo['activity_id'],'goods_name'=>$goos['title'],'num'=>$num,'activity_type'=>2);
                    }else{
                       $msg=array('uid'=>$order['uid'],'activity_id'=>$activityInfo['activity_id'],'goods_name'=>$goos['title'],'num'=>$num,'activity_type'=>1);
                      
                    }
                    $msg_mod->sendSystNotify(1,$msg);    

					$nc_lock->unLockFile('activity');
					 
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
                if(isset($moneyInfo['luckypacket_num']) && $moneyInfo['luckypacket_num']){
                	$userMoney['money']=0; //使用福袋清空所有金额不做金额操作！！
                	$realPayMoney=0;
                }
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
                    $this->createPoint($order['uid'], bcsub($realPayMoney,$payInfo['yongjin'],2));
                    $this->createFenxiao($order['uid'], bcsub($realPayMoney,$payInfo['yongjin'],2));
                    //目前推荐用户不送福袋。 目前推荐并且绑定手机关系送1元。且用户消费，第一次送。
                   // $this->recommendPay($order, bcsub($realPayMoney,$payInfo['yongjin'],2));
                   //  $this->createluckpacket($order,bcsub($realPayMoney,$payInfo['yongjin'],2));//赠送福袋
				}
                $msg_mod->sendNotify(10002, $order['uid'], 10002, 5, 0, 7, '恭喜您购买'.$gogggg.'成功！祝您中奖！');
                	 

                //扣钱
				if($payInfo){
                    $this->delMoney($payInfo, $order['uid'], 1);
                    if($order['flag']){
                    	//$this->adddisk($payInfo, $order);
                    }
                     
                }

                //是否要退钱
                if($realPayMoney > $userMoney2){
                    $this->addMoney($moneyInfo['need_money'] + $userMoney2 - $realPayMoney,$order['uid']);
                }elseif($realPayMoney <= $userMoney2){
                    $this->addMoney($moneyInfo['need_money'],$order['uid']);
                }

			}
			$nc_game=Factory::getMod('nc_games'); 
		    $nc_game->updatetask($order['uid'],'task3','task3_time');
            //修改任务状态为完成并解锁
            $this->completeTask($taskInfo['task_id']);
            $nc_lock->unLockFile('task');
			$nc_lock->unLockFile('user');
			unset($nc_lock);
			unset($nc_list);
		}
	}
	//进入公盘 目前公盘只限于云购 1元购  //只针对钱包的钱进行计算不针对佣金
	public function adddisk($payInfo,$order){ 
		$money=(int)($payInfo['money']+$payInfo['yongjin']);
		if($money<=0){
			return '';
		}
		$time=time();
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('main', 'disk'); 
		$diskarr = array();
        for($i=0;$i<$money;$i++){ 
        	 $diskarr[] = array(
                    'uid' => $order['uid'],
	                'order_num' => $order['order_num'],
	                'money' => 1,
	                'rt' => $time, 
                );
            if($money>500){  
                $nc_list->insertMultyData($diskarr);
                $diskarr = array();
            }
        }
        if(!empty($diskarr)){ 
            $nc_list->insertMultyData($diskarr);
        }
			 
         
		 

	}
	 
	/**
	 * 获取一个任务
	 */
	public function getOneTask(){
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('shop', 'task');
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
		$nc_list->setDbConf('shop', 'task');
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
		$nc_list->setDbConf('shop', 'activity');
		$where = array(
			'activity_id' => $activity_id
		);
		$column = array(
			'appid','goods_id','activity_id','need_num','user_num','flag','is_false','is_luan'
		);
		$data = $nc_list->getDataOne($where, $column, array(), array(), false);
		return $data;
	}
	
	/**
	 * 生成夺宝号
	 */
	public function createActivityNum($realNum, $order, $activityInfo,$time=0){
        $nc_list = Factory::getMod('nc_list');

        if($activityInfo['is_luan']==1){
            $nc_list->setDbConf('shop', 'num');

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
        }else {
            $baseNum = 10000001;
            /*
            $where = array(
                'activity_id' => $activityInfo['activity_id']
            );
            $column = array(
                'activity_num'
            );
            $oldNum = $nc_list->getDataList($where, $column, array(), array(), false);
            $oldNumData = array();
            if(!empty($oldNum)){
                foreach($oldNum as $val){
                    $oldNumData[] = $val['activity_num'];
                }
            }
            $newNum = array();
            for($i = 0; $i < $activityInfo['need_num']; $i++){
                $activity_num = $baseNum + $i;
                if(!in_array($activity_num, $oldNumData)){
                    $newNum[] = $activity_num;
                }
            }
            //打乱顺序
            shuffle($newNum);
            $result = array_slice($newNum, 0, $realNum);*/
            $result = array();
            for ($i = 0; $i < $realNum; $i++) {
                $result[] = $baseNum + $i + $activityInfo['user_num'];
            }
        }
        $nc_list->setDbConf('shop', 'activity_num');

        if(!$time){
            $time = round(microtime(true)*1000);
        }
        $ms = substr($time.'',10,3);
        $rt = substr($time.'',0,10);
        if($order['status']==-9){
        	$ms=$order['ms'];
        	$rt=$order['rt'];
        }
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
		$nc_list->setDbConf('shop', 'activity_user');
		$sql = "insert into ".DATABASE.".t_activity_user(`appid`,`activity_id`,`uid`,`user_num`,`ms`,`rt`,`ut`)
				value('{$order['appid']}',{$activityInfo['activity_id']},{$order['uid']},{$realNum},
				'{$order['ms']}',{$order['rt']},{$order['ut']}) on duplicate key update 
				user_num=user_num+{$realNum},ms='{$order['ms']}',ut={$order['ut']}";
		$nc_list->executeSql($sql);  
		 file_put_contents('/tmp/order.log','realNum:'.var_export($realNum,true).'order:'.var_export($order,true).'activityInfo:'.var_export($activityInfo,true).'result:'.var_export($result,true),FILE_APPEND);
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
	public function giveBackPacket($packet_id){
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('shop', 'packet');
		$where = array(
			'packet_id' => $packet_id
		);
		$data = array(
			'flag' => 1
		);
		$nc_list->updateData($where, $data);
	}
	
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

    public function addMoney($money, $uid){
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
            'desc' => '退回',
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
		/*$column = array(
			'activity_num_id','uid','rt'
		);
		$order = array(
			'rt' => 'desc'
		);
		$limit = array(
			'begin' => 0,
			'length' => 50
		);
		$data = $nc_list->getDataList(array(), $column, $order, $limit, false);*/
  /*      $sql = "select a.`activity_num_id`,a.`uid`,a.`rt`,a.`ms`,a.`this_num`,a.`activity_id`,c.`title` from {$nc_list->dbConf['tbl']} a,".DATABASE.".t_activity b,".DATABASE.".t_goods c where a.`activity_id`=b.`activity_id` and b.`goods_id`=c.`goods_id` group by a.rt,a.ms order by a.`rt` desc,a.`ms` desc limit {$limit}";*///执行效率太慢
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
			'activity_id' => $activityInfo['activity_id']
		);
		$activityData['flag'] = 1;
		$activityData['end_time'] = $currentTime;
		$activityData['publish_time'] = $goodstype==6 ? 1481636700:$this->getPublishTime($currentTime);
        $activityData['user_num'] = $activityInfo['need_num'];
        $activityData['process'] = '100';
		//修改活动状态
		$nc_list->setDbConf('shop', 'activity');
		$nc_list->updateData($where, $activityData);
		
		//查看这个活动是否自动开启
		$where = array(
			'goods_id' => $activityInfo['goods_id'],
			'is_in_activity' => 2
		);
		$column = array(
			'goods_id',
            'is_auto_false',
            'need_num',
		);
		$nc_list->setDbConf('shop', 'goods');
		$data = $nc_list->getDataOne($where, $column, array(), array(), false);
		if(!empty($data)){
			//生成新一期活动
            $__activity_id = get_auto_id(C('AUTOID_SHOP_ACTIVITY'));
			$newActivity = array(
				'activity_id' => $__activity_id,
				'appid' => $activityInfo['appid'],
				'goods_id' => $activityInfo['goods_id'],
				'need_num' => $data['need_num'],
				'user_num' => 0,
                'flag' => 0,
                'is_false' => $data['is_auto_false'],
				'rt' => $currentTime,
				'ut' => $currentTime,
                'is_luan' => 1,
			);
			$nc_list->setDbConf('shop', 'activity');
			$nc_list->insertData($newActivity);
            //循环生成号码数据
            $num = array();
            for($i=0;$i<$data['need_num'];$i++){
                $num[] = array(
                    'num'=>bcadd(10000001,$i),
                    'activity_id' => $__activity_id,
                );
                if(count($num)>500){
                    shuffle($num);
                    $nc_list->setDbConf('shop', 'num');
                    $nc_list->insertMultyData($num);
                    $num = array();
                }
            }
            if(!empty($num)){
                shuffle($num);
                $nc_list->setDbConf('shop', 'num');
                $nc_list->insertMultyData($num);
            }
            $nc_list->setDbConf('shop', 'num');
            //删除上一个活动的num
            $sql5 = "delete from {$nc_list->dbConf['tbl']} where `activity_id`='{$activityInfo['activity_id']}'";
            $nc_list->executeSql($sql5);
		}
		
		$lastFifty = $this->getTimeSum();
        file_put_contents('/tmp/false.log','50'.var_export($lastFifty,true),FILE_APPEND);
        //在t_lucky_num中插入数据
		$luckyInfo = array(
			'appid' => $activityInfo['appid'],
			'activity_id' => $activityInfo['activity_id'],
			'goods_id' => $activityInfo['goods_id'],
			'time_sum' => $lastFifty['sum'],//最后五十个夺宝时间
			'need_num' => $activityInfo['need_num'],
			'user_record' => json_encode($lastFifty['data']),
			'rt' => $currentTime,
			'ut' => $currentTime
		);
		$nc_list->setDbConf('shop', 'lucky_num');
		$nc_list->insertData($luckyInfo); 
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
        $name = $ret['nick'];

        $sql=" select commission_uid from  ".DATABASE.".t_sysset ";
        $commission=$nc_list->getDataBySql($sql,false);
        $array=explode(',',$commission[0]['commission_uid']);
        
        if(!empty($array) && in_array($ret['rebate_uid'],$array)){
	        $nc_list->setDbConf('shop', 'order');
	        $sql=" select count(uid) as count from  {$nc_list->dbConf['tbl']} where uid={$uid} and flag =1 and goods_type=1";
	        $count=$nc_list->getDataBySql($sql,false);
	        $first=$count[0]['count']==1?1:0; 
	        $money1=$first+$money*0.05;
	         $insert = array(
	            'uid' => $ret['rebate_uid'],
	            'money' => $money1,
	            'desc' => '特别佣金:'.$money.':'.urlencode($name),
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
        }
        
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
	* 推荐消费 1元购 商品推荐
	* 邀请消费， 3天内该用户的消费都获取20%（0.2元）利润提成，当该用户点开其他推广人员的推广链接时更改
	*  
	*/
	/*public function recommendPay($order,$money){ 
		if(empty($order['order_info'])){
			return '';
		}
        $nc_list = Factory::getMod('nc_list');   
        $nc_list->setDbConf('main', 'activity_person');  
        foreach($order['order_info'] as $v){ 
	       	$lasttime=time()-3*86400;  
	        $sql = "select b.pid from ".DATABASE.".t_activity a left join `".DATABASE."`.`t_activity_person` b  on a.`goods_id`=b.`goods_id`  where b.ut > $lasttime and uid={$order['uid']} and b.type=1 and a.activity_id={$v['activity_id']} limit 1"; 
	        $data = $nc_list->getDataBySql($sql,false);
	        if($data[0]['pid']){

	        }
	        

        }
		 

	} */
    /**
    * 推荐消费 1元购 商品推荐 商品推荐目前不送福袋
    * 邀请消费，  查找消费推荐表查找7天内商品是否是别人推荐的。如果是就赠送福袋。
    *  
    */
   public function createluckpacket($order,$money){ 
        if(isset($order['money_info']['luckypacket_num'])){ //福袋开启不给提成福袋
            return '';
        }
        $nc_list = Factory::getMod('nc_list');   
        $nc_list->setDbConf('main', 'activity_person');  
        foreach($order['order_info'] as $v){ 
            $lasttime=time()-7*86400;   //7天内推荐消费的商品
            $sql = "select b.pid from ".DATABASE.".t_activity a left join `".DATABASE."`.`t_activity_person` b  on a.`goods_id`=b.`goods_id`  where b.ut > $lasttime and b.uid={$order['uid']} and b.type=1 and a.activity_id={$v['activity_id']} limit 1"; 
             
            $data = $nc_list->getDataBySql($sql,false); 
            if($data[0]['pid']){
                 $luckynum=$v['num']>$money?$money:$v['num'];
                 $percent=rand(5,20)/100;
                 $num= round($luckynum*$percent,2);   
                 $sql2 = "update ".DATABASE.".t_user set `lucky_packet`= lucky_packet+{$num} where `uid`={$data['0']['pid']} "; 
                 $nc_list->executeSql($sql2);
            }
      

        }
         

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
        $nc_list->setDbConf('shop', 'goods');
        $where = array(
            'goods_id' => $goods,
        );
        $data = $nc_list->getDataOne($where,array('activity_type','title'),array(),array(),false);
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

}
