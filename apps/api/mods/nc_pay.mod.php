<?php
/**
 * @since 2016-01-13
 */
class NcPayMod extends BaseMod{
	
	/**
	 * 获取订单 
	 */
	public function getOrderInfo($base, $postData,$ordertype=''){  
		//获取登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$nc_list_mod = Factory::getMod('nc_list');
		
		$orderNum = $this->makeOrderNum($login_user['uid']);
		
		$money = 0;//金额
		$appid = $base['appid'];
		//获取时间和毫秒
		list($currentTime, $ms) = $this->getMicrotime();
		
		$result_data = array();
		
		//分析订单
		$address_id='';
		$orderInfo = array();
		foreach($postData as $activity_id=>$val){ //前端的ordertype 1,2 表示参团 3表示开团  4表示拼团单独购买
			if(isset($val['address_id'])){ 
				$address_id=$val['address_id'];
			}
			if($ordertype){ //表示当前订单是拼团订单
				$goodsid['goods_id']=$postData[0]['goods_id']+0; //拼团商品id
				if($ordertype==2 || $ordertype==1){ //拼团商品   参团  目前团购都必须单独购买 若多团购买则修改这里跟后台处理订单 
	                $nc_list_mod->setDbConf('team', 'teamwar');   
					$sql ="SELECT  g.goods_id,g.price,g.people_num,g.activity_type  FROM   
					".DATABASE.".   t_teamwar  AS w  left JOIN  ".DATABASE.".t_team_goods g ON   g.`goods_id`=w.`goods_id`     where w.teamwar_id={$activity_id} and w.flag=1 and {$currentTime} < w.et and g.status=1 and g.is_in_activity=2"; 
					$msg = $nc_list_mod->getDataBySql($sql,false);  
					 
					if(empty($msg)){
						 api_result(2, '当前商品已下架!');
					} 
					 
					$psql="select uid from ".DATABASE.".t_team_member where teamwar_id={$activity_id} and uid={$login_user['uid']}" ;
					$user=$nc_list_mod->getDataBySql($psql,false);  
					if($user){
						api_result(2, '当前团只能参团一次');
					}
					if($msg[0]['activity_type']!=2){ //非幸运拼团
						$num=intval($msg[0]['price']);
					}else{  //幸运拼团
						$num = intval($msg[0]['price']/$msg[0]['people_num']);
					}
					$goodsid['goods_id']=$msg[0]['goods_id'];
				    $money += $num;
					$result_data[] = array(
						'user_num' => $num,
						'goods_title' => $val['title'],
						'teamwar_id'=>$activity_id,
						'address_id'=>$val['address_id']

					);
					$orderInfo[] = array(
						'activity_id' => $activity_id,
						'teamwar_id'=>$activity_id,
						'num' => $num,  
					);
					break;//跳出，不再执行下面流程
					 
				}
				if($ordertype==3){ //拼团商品  //开团
						if(!$postData[0]['goods_id']){
							api_result(2, '开团数据异常!');
					 	}
					 	 
					 	$team_mod = Factory::getMod('nc_team');
					 	$msg=$team_mod->verifiyTeam($login_user['uid'],$goodsid['goods_id']); 
						$num = intval($msg[0]['price']);
						$money += $num;
						$result_data[] = array(
							'user_num' => $num,
							'goods_title' => $val['title'],  
							'ordertype'=>$ordertype
						);
						$orderInfo[] = array( 
							'num' => $num, 
							'goods_id'=>$val['goods_id'],
							//'address_id'=>$val['address_id']
						);
					    break;//跳出，不再执行下面流程

				}
				if($ordertype==4){ //拼团商品  //单独购买
						if(!$postData[0]['goods_id']){
							api_result(2, '拼团数据异常!');
					 	}
					 	 
					    $nc_list_mod->setDbConf('team', 'teamwar');   
						$sql ="SELECT  g.price,g.people_num,g.single_price  FROM   ".DATABASE.".t_team_goods g    where    g.status=1 and g.is_in_activity=2 and g.goods_id=".$goodsid['goods_id'];   
						$msg = $nc_list_mod->getDataBySql($sql,false);   
						if(empty($msg)){
							 api_result(2, '当前商品已下架!');
						} 

						$singleprice = intval($msg[0]['single_price']);
						$money += $val['goods_num']*$singleprice;
						$result_data[] = array(
							'user_num' => $money,
							'goods_title' => $val['title'],  
							'goods_num'=>$val['goods_num'],
							'single_price'=>$singleprice,
							'ordertype'=>$ordertype
						);
						
						$orderInfo[] = array( 
							'num' => $money,  //num是金钱的意思
							'goods_num'=>$val['goods_num'],//这里才是商品数量
							'goods_id'=>$val['goods_id'],
							//'address_id'=>$val['address_id']
						);
					    break;//跳出，不再执行下面流程

				} 
				 if($ordertype==6){ //福袋购买
				 	 
						if(!$postData[-1]['num']){
							api_result(2, '福袋数据异常!');
					 	}
					    $money += $postData[-1]['num']+0; 
						 
						$result_data[] = array(
							'user_num' => $money,
							'goods_title' => '福袋',  
							'goods_num'=>$money, 
							'ordertype'=>$ordertype
						);
						
						$orderInfo[] = array( 
							'num' => $money,  //num是金钱的意思
							'goods_num'=>$money,//这里才是商品数量
							 
						);
					    break;//跳出，不再执行下面流程

				}

				break; //跳出！！
			}

            //goods_type
            $num = $val['num'];
            $nc_list_mod->setDbConf('shop', 'goods');
            $sql = "select a.`activity_type`,b.`need_num`,b.`user_num` from `".DATABASE."`.`t_goods` a,`".DATABASE."`.`t_activity` b where a.`goods_id`=b.`goods_id` and b.`activity_id`='{$activity_id}'";
            $temp = $nc_list_mod->getDataBySql($sql);
            if($temp[0]['activity_type'] == 3 ){
                if($num > 10){
                    api_result(2, '限购商品('.$val['title'].')最多只能购买10份');
                }
                $nc_list_mod->setDbConf('shop', 'activity_user');
                $wher = array(
                    'activity_id' => $activity_id,
                    'uid' => $login_user['uid'],
                );
                $user_num = $nc_list_mod->getDataOne($wher,array('user_num'),array(),array(),false);
                if($user_num['user_num']>=10){
                    api_result(2, '限购商品('.$val['title'].')最多只能购买10份');
                }
            }
            if($temp[0]['activity_type'] == 2 && $num%10!=0){
                api_result(2, '十元专区商品只能购买10的倍数');
            }
            //新增二人购 
            if($temp[0]['activity_type'] == 4 ){
            	$goodsnumber=($temp[0]['need_num']-$temp[0]['user_num'])/$num;  
            	if(!($goodsnumber==1 || $goodsnumber==2) ){
                	api_result(2, '二人购商品只能是总数量全部或者一半');
            	}
            }
            
            if($temp[0]['activity_type'] == 6 ){
            	api_result(2, '双12活动终止，幸运购商品已停售!');
            	$goodsnumber=($temp[0]['need_num']-$temp[0]['user_num'])/$num;  
            	if(!($goodsnumber==1 || $goodsnumber==2) ){
                	api_result(2, '幸运购商品只能是总数量全部或者一半');
            	}
            }
			 
			$money += $num;
			$result_data[] = array(
				'user_num' => $num,
				'goods_title' => $val['title']
			);
			$orderInfo[] = array(
				'activity_id' => $activity_id,
				'num' => $num,
				//'hot_luckyBuy'=>$temp[0]['activity_type'] == 6?$val['hot_luckyBuy']:''
			);
		}
	 
		$result = array(
			'order_num' => $orderNum,
            'sign' => md5($orderNum.LOGIN_KEY),
			'order_money' => intval($money),
			'result_data' => $result_data
		);


		if($temp[0]['activity_type'] == 6 ){
			$nc_list_mod->setDbConf('shop', 'activity');
            $goodsid= $nc_list_mod->getDataOne(array('activity_id'=>$activity_id), array('goods_id'),array(),array(),false);           


			$where = array(
				'order_aid' => $activity_id,
				'flag'=>1,
				'uid'=>$login_user['uid']		 
					);
			$column = array(
				'order_info' 
			);

            // $val['hot_luckyBuy']
           $nc_list_mod->setDbConf('shop', 'order');
            $ordermsg= $nc_list_mod->getDataList($where, $column,array(),array(),false);
            if(count($ordermsg)==2){
            	api_result(2, '当前期号商品已购完,请重进返现专区刷新商品再购买');
            }else{
            	//json_decode($ordermsg[0]['order_info'],true);
            	$ordmsg=json_decode($ordermsg[0]['order_info'],true);
            	if($ordmsg[0]['hot_luckyBuy']==$val['hot_luckyBuy']){
            		  api_result(2, '请购买其他幸运牌');
            	}

            }
            $where = array(				 
				'flag'=>1,
				'uid'=>$login_user['uid'],
				'order_goods_id'=>$goodsid['goods_id']	 
					);

            
            $goodmsg=$nc_list_mod->getDataOne($where, array('uid'),array(),array(),false);
            if($goodmsg['uid']){
            	api_result(2, '您好,幸运购同一款商品只能买一次。');
            }
            
            	 
        }
        
		//获取金钱的分配
		
		//获取可以使用的红包
		/*$where = array(
			'appid' => $login_user['appid'],
			'uid' => $login_user['uid'],
			'overdue' => array(
				$currentTime,'>='
			),
			'flag' => 1
		);
		$column = array(
			'packet_id','money','type','user_range','need_money'
		);
		//按优先级排序
		$order = array(
			'type' => 'asc',
			'money' => 'desc'
		);
		$nc_list_mod->setDbConf('shop', 'packet');
		$packetData = $nc_list_mod->getDataList($where, $column, array(), array(), false);
		$usePacket = array();
		if(!empty($packetData)){
			$usePacket = $this->getUsePacket($packetData, $postData, $money);
		}*/
		/*if(!empty($usePacket)){
			//使用红包支付的金额
			$result['packet_use'] = $usePacket[1];
			$money -= $usePacket[1];
		}else{
			$result['packet_use'] = 0;
		}*/
		
		//获取用户剩余的夺宝币
		$where = array(
			'appid' => $login_user['appid'],
			'uid' => $login_user['uid'],
		);
        $column = array('money','yongjin');
        $nc_list_mod->setDbConf('main', 'user');
        $temp = $nc_list_mod->getDataList($where, $column);
        $remainMoney = intval($temp[0]['money']+$temp[0]['yongjin']);
		
		$result['remain_money'] = $remainMoney;
		//余额支付
		$result['remain_use'] = intval(min($remainMoney, $money));
		//还需支付
		$result['need_money'] = intval($result['order_money'] - $result['packet_use'] - $result['remain_use']);
		/*if($result['need_money'] > 0){
            api_result(1, '余额不足，请先充值');
        }*/


		try{
			//开启事务
			$nc_list_mod->executeSql('START TRANSACTION');
			
			/*if(!empty($usePacket)){
				//如果使用了红包，修改红包状态
				$nc_list_mod->setDbConf('shop', 'packet');
				$where = array(
					'packet_id' => $usePacket[0]
				);
				$data = array(
					'flag' => 2
				);
				$nc_list_mod->updateData($where, $data);
			}*/
			
			//写订单
			$nc_list_mod->setDbConf('shop', 'order');
			$money_info = array(
				//'packet_use' => $usePacket,//红包存放规则：array($packet_id,$money)
				'remain_use' => $result['remain_use'],
				'need_money' => $result['need_money']
			);

			$data = array(
				'appid' => $login_user['appid'],
				'order_num' => $orderNum,
				'uid' => $login_user['uid'],
				'order_info' => json_encode($orderInfo),
				'money_info' => json_encode($money_info),
				'order_type' => 0,
				'ip' => get_ip(1),
				'flag' => 0,
				'ms' => $ms,
				'rt' => $currentTime,
				'ut' => $currentTime,
				'order_aid'=>$activity_id,
				'order_goods_id'=>$goodsid['goods_id'],
				'address_id'=>$address_id
			);
		 	if($ordertype==1){ //商品类型1 是原1元购 ,2 表示参团 3表示开团 4表示拼团单独购买 5 表示开团（免费开）
		 		$data['goods_type']=2;
		 		$data['teamwar_id']=$orderInfo[0]['teamwar_id'];
		 	}else if($ordertype==2){
		 		$data['goods_type']=2;
		 		$data['teamwar_id']=$orderInfo[0]['teamwar_id'];
		 	}else if($ordertype==3){
		 		$data['goods_type']=3;
		 	}else if($ordertype==4){
		 		$data['goods_type']=4; 
		 	}else if($ordertype==6){
		 		$data['goods_type']=6; 
		 	}
		 	else{
		 		$data['goods_type']=1;//默认1元购状态是1
		 	}
			$nc_list_mod->insertData($data);
			
			$nc_list_mod->executeSql('COMMIT');
		}catch(Exception $e){
			$nc_list_mod->executeSql('ROLLBACK');
			api_result(1, '提交失败');
		}
		if($result['need_money']){

            //微信支付页面地址
            $result['wx_pay'] = A_PATH.'/?c=nc_pay&a=wx_pay&order_num='.$orderNum;
            $result['wx2_pay'] = A_PATH.'/?c=nc_pay&a=wx_pay2&order_num='.$orderNum;
            $result['wx3_pay'] = A_PATH.'/?c=nc_pay&a=wx_pay3&order_num='.$orderNum;
			//支付宝支付页面地址
            $result['al_pay'] = A_PATH.'/?c=nc_pay&a=al_pay&order_num='.$orderNum;
            $result['al_pay2'] = A_PATH.'/?c=nc_pay&a=al_pay2&order_num='.$orderNum;
            $result['al_pay3'] = A_PATH.'/?c=nc_pay&a=al_pay&src=pc2&order_num='.$orderNum;
		}
		return $result;
	}
	
	/**
	 * 充值
	 */
	public function doRecharge($base, $ipt_list){
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$nc_list_mod = Factory::getMod('nc_list');
		
		$orderNum = $this->makeOrderNum($login_user['uid']);
		
		//写订单
		$nc_list_mod->setDbConf('shop', 'order');

		$money_info = array(
			'need_money' => (int) $ipt_list['pay_money']
		);
		$currentTime = time();
		$data = array(
			'appid' => $login_user['appid'],
			'order_num' => $orderNum,
			'uid' => $login_user['uid'],
			'order_info' => '',
			'money_info' => json_encode($money_info),
            'order_type' => 1,
            'pay_type' => $ipt_list['pay_type'],
			'ip' => get_ip(1),
			'flag' => 0,
			'ms' => '000',
			'rt' => $currentTime,
			'ut' => $currentTime
		);
		$nc_list_mod->insertData($data);
		$result = array(
			'order_num' => $orderNum,
			'sign' => md5($orderNum.LOGIN_KEY),
            'zuobi' => 1,
		);
		if($ipt_list['pay_type'] == 1){
			//微信支付页面
            $result['pay_url'] = A_PATH.'/?c=nc_pay&a=wx_pay&order_num='.$orderNum;
            $result['pay_url2'] = A_PATH.'/?c=nc_pay&a=wx_pay3&order_num='.$orderNum;
		}elseif($ipt_list['pay_type'] == 2){
			//支付宝支付页面
            $result['pay_url'] = A_PATH.'/?c=nc_pay&a=al_pay&order_num='.$orderNum;
            $result['pay_url2'] = A_PATH.'/?c=nc_pay&a=al_pay&src=pc&order_num='.$orderNum;
		}elseif($ipt_list['pay_type'] == 3){
            //支付宝支付页面
            $result['pay_url'] = A_PATH.'/?c=nc_pay&a=wx_pay2&order_num='.$orderNum;
        }elseif($ipt_list['pay_type'] == 4){
            //支付宝支付页面
            $result['pay_url'] = A_PATH.'/?c=nc_pay&a=al_pay2&order_num='.$orderNum;
        }
		return $result;
	}

	/**
	 * 付款成功后的处理
	 * 如果是充值：则直接更新用户的金额
	 * 如果是支付：则只是简单的更新订单状态，并将订单放入队列
	 */
	public function paySuccess($orderNum, $payType = 0, $transaction_id = ''){
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('shop', 'order');
		//获取订单，避免重复
		$where = array(
			'order_num' => $orderNum,
			'flag' => 0
		);
		$nc_lock = Factory::getMod('nc_lock');
		$nc_lock->lockFile('pay_order'.$orderNum);
		$result = true;
		$data = $nc_list->getDataList($where, array(), array(), array(), false);
		if(!empty($data)){
			$orderInfo = $data[0];//订单信息
			try{
				//开启事务
				$nc_list->executeSql('START TRANSACTION');
				$currentTime = time();
				//更新订单状态
				$where = array(
					'order_num' => $orderNum
				);
				$data = array(
					'flag' => 1,
					'pay_type' => $payType,
					'transaction_id' => $transaction_id,
					'ut' => $currentTime,
					'status'=>$orderInfo['goods_type']==4?3:2//拼团专用(单独购买)
				);
				$nc_list->updateData($where, $data);
				 //双12活动，过后删除即可 非活动商品返现15%
			/*	$where['flag']=1;
		        $ordermsg=$nc_list->getdataone($where,array(), array(), array(), false);
		        if(!empty($ordermsg) && $orderInfo['order_type']==0){
		            $nc_list->setDbConf('main', 'activity_person');
		            $where=array('uid'=>$ordermsg['uid']);
		            $parentmsg=$nc_list->getdataone($where,array(), array(), array(), false);
		            if(!empty($parentmsg)){
						$ordermoney=json_decode($ordermsg['order_info'],true);  
			   	  		$sql="SELECT g.activity_type FROM ".DATABASE.".t_activity t LEFT JOIN ".DATABASE.".t_goods g ON t.goods_id=g.goods_id WHERE t.activity_id=".($ordermoney[0]['activity_id']+0); 			   	  		 
		    	  		$msg=$nc_list->getDataBySql($sql,false);
		    	  		$people = array(1476,46018,34938,5902,29180,29178);//
			    	    if($msg[0]['activity_type']!=6 && $msg[0]['activity_type']!=7  && !in_array($parentmsg['pid'],$people)){ //非活动商品才行
			    	  	    $money=round($ordermoney[0]['num']*0.15,2);       
							$nc_list->setDbConf('main', 'user');
							$sql = "update {$nc_list->dbConf['tbl']} set `money`=`money`+{$money} where `uid`={$parentmsg['pid']}  ";
							$nc_list->executeSql($sql); 
							$msg_mod = Factory::getMod('msg');
 						    $msg_mod->sendNotify(10002, $parentmsg['pid'], 10002, 3, 0, 5, "返现活动:系统已经成功返回{$money}元到您商城的个人账号，敬请查收。若有疑问请添加官方公众号（yiqigou668）咨询客服。");

			    	    }
     
						 
		              
		            }
		        }*/
				if($orderInfo['order_type'] == 0){//支付
					//将订单放入任务队列中
					$data = array(
						'order_num' => $orderNum,
						'flag' => 0,
						'ut' => $currentTime,
						'rt' => $currentTime
					);
					if($orderInfo['goods_type']>=2){
						 $nc_list->setDbConf('team', 'team_task');
					}else{
						 $nc_list->setDbConf('shop', 'task');
					}
					 
					$nc_list->insertData($data);
				}else{//充值
					//更新用户的余额
					$moneyInfo = json_decode($orderInfo['money_info'],true);

                    //充值赠送
                    $_sql = "select count(*) `total` from ".DATABASE.".t_user where `uid`<{$orderInfo['uid']}";
                    $_r = $nc_list->getDataBySql($_sql);
                    $money = $moneyInfo['need_money'];

                    if($_r[0]['total']<1000 && $money>=100){
                        $_rr = $nc_list->getDataOne(array(
                            'uid' => $orderInfo['uid'],
                            'desc' => '充值',
                        ),array(),array(),array(),false);
                        if(empty($_rr)){
                            $money = bcmul($money,1.1,2);
                        }
                    }

					$where = array(
						'appid' => $orderInfo['appid'],
						'uid' => $orderInfo['uid'],
					);
					$whereSql = parse_where($where);
					$sql = "update ".DATABASE.".t_user set money=money+{$money} {$whereSql}";
					$nc_list->executeSql($sql);
					if($money>=20){
						$nc_game=Factory::getMod('nc_games'); 
				    	$nc_game->updatetask($order['uid'],'task5','task5_time');
					}
                    $insert = array(
                        'uid' => $orderInfo['uid'],
                        'desc' => '充值',
                        'money' => $money,
                        'ut' => time(),
                    );
                    $nc_list->setDbConf('shop', 'money');
                    $nc_list->insertData($insert);
				}
				$nc_list->executeSql('COMMIT');
			}catch(Exception $e){
                file_put_contents('/tmp/yydb2.log',$e->getMessage()."\r\n",FILE_APPEND);
				$nc_list->executeSql('ROLLBACK');
				$result = false;
			}
		}
		$nc_lock->unLockFile();
		return $result;
	}
	
	/**
	 * 生成订单号
	 */
	public function makeOrderNum($uid){
		//订单号
		$uid = 10000000000 + $uid;
		$orderNum = date('YmdHis').$uid;
		return $orderNum;
	}
	
	/**
	 * 获取时间和毫秒
	 */
	public function getMicrotime(){
		$time = (string)microtime(true);
		$timeArr = explode('.', $time);
		$currentTime = $timeArr[0];//当前时间
		$ms = substr($timeArr[1],0,3);
		return array($currentTime, $ms);
	}
	
	/**
	 * 获取可以使用的红包
	 */
	/*public function getUsePacket($packetData, $postData, $money){
		//获取每类商品在本订单的消费，用于下面的红包判断
		$typeMoney = $this->getTypeMoney($postData);
		foreach($packetData as $val){
			$type = $val['type'];
			if($type == 1){//品类满减
				if($val['need_money'] <= $typeMoney[$val['user_range']]){
					return array($val['packet_id'], $val['money']);
				}
			}else if($type == 2){//全场满减
				if($val['need_money'] <= $money){
					return array($val['packet_id'], $val['money']);
				}
			}else if($type == 3){//品类直减
				if($typeMoney[$val['user_range']] >= $val['money']){
					return array($val['packet_id'], $val['money']);
				}
			}else{//全场直减
				if($money >= $val['money']){
					return array($val['packet_id'], $val['money']);
				}
			}
		}
		return array();
	}*/
	
	/**
	 * 获取每种类别的商品在本次订单的消耗
	 */
	public function getTypeMoney($postData){
		$activityIds = array_keys($postData);
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('shop', 'activity');
		$where = array(
			'activity_id' => array(
				$activityIds,'in'
			)
		);
		$column = array(
			'goods_id','activity_id' 
		);
		$data = $nc_list->getDataList($where, $column);
		$goods_ids = $activity_goods = array();
		foreach($data as $val){
			$goods_ids[] = $val['goods_id'];
			$activity_goods[$val['goods_id']] = $val['activity_id'];
		}
		$nc_list->setDbConf('shop', 'goods');
		$where = array(
			'goods_id' => array(
				$goods_ids,'in'
			)
		);
		$column = array(
			'goods_id','type'
		);
		$data = $nc_list->getDataList($where, $column);
		$result = array();
		foreach($data as $val){
			$activity_id = $activity_goods[$val['goods_id']];
			$result[$val['type']] += $postData[$activity_id]['num'];
		}
		return $result;
	}
}