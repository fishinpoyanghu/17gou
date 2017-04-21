<?php
/**
 * @since 2016-01-23
 */
class NcOrderMod extends BaseMod{
	
	/**
	 * 获取订单结果
	 */
	public function getOrderResult($base, $ipt_list){
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		$nc_list = Factory::getMod('nc_list');
		$where = array(
			'order_num' => $ipt_list['order_num'],
			'uid' => $login_user['uid'],
			'flag' => 1
		);
		$column = array(
			'order_info','money_info'
		);
		$nc_list->setDbConf('shop', 'order');
		//获取订单详情
		$order = $nc_list->getDataOne($where, $column);
		$orderInfo = json_decode($order['order_info'], true);
		$moneyInfo = json_decode($order['money_info'], true);

        $nc_list->setDbConf('shop', 'task');
        $task_info = $nc_list->getDataOne(array('order_num' => $ipt_list['order_num'],'flag'=>1), array('task_id'));

		if(empty($task_info) || empty($orderInfo) || empty($moneyInfo)){
			api_result(0, '数据为空');
		}
		
		$payNum = $activity_ids = $result = $newActivity = $realNum = array();
		
		foreach($orderInfo as $val){
			$payNum[$val['activity_id']] = $val['num'];
			$activity_ids[] = $val['activity_id'];
		}
		

        /*$sql = "select count(*) `total`,`activity_id`,max(`activity_num`) `max`,min(`activity_num`) `min` from {$nc_list->dbConf['tbl']} where `order_num`='{$ipt_list['order_num']}' and `uid`='{$login_user['uid']}' group by `activity_id`";
        $list = $nc_list->getDataBySql($sql,false);

		//获取夺宝号详情
		//$activityNum = $nc_list->getDataList($where, $column);
        $countt = array();
		foreach($list as $val){
            $realNum[$val['activity_id']][] = $val['min'];
            $realNum[$val['activity_id']][] = $val['max'];
            $countt[$val['activity_id']] = $val['total'];
		}*/

		//查看任务是否已经完成
		$where = array(
			'order_num' => $ipt_list['order_num'],
			'flag' => 1
		);
		$nc_list->setDbConf('shop', 'task');
		$taskInfo = $nc_list->getDataList($where, array(), array(), array(), false);
		$taskNotDone = false;
		if(empty($taskInfo)){
			$taskNotDone = true;
		}else{
            $nc_list->setDbConf('shop', 'activity_num');
            //完成了才去查嘛，真是
            $sql = "select count(*) `total` from {$nc_list->dbConf['tbl']} where `order_num`='{$ipt_list['order_num']}' and `uid`='{$login_user['uid']}'";
            $res = $nc_list->getDataBySql($sql,false);
            $need_count = 1;
            if($res[0]['total']>500){
                $need_count = ceil($res[0]['total']/500);
            }
            for($i=0;$i<$need_count;$i++){
                $start = $i*500;
                $sql2 = "select `activity_id`,`activity_num` from {$nc_list->dbConf['tbl']} where `order_num`='{$ipt_list['order_num']}' and `uid`='{$login_user['uid']}' limit {$start},500";
                $list = $nc_list->getDataBySql($sql2,false);
                foreach($list as $val){
                    $realNum[$val['activity_id']][] = $val['activity_num'];
                }
            }
            foreach($realNum as $id=>$v){
                $countt[$id] = count($v);
            }

        }

		foreach($orderInfo as $val){
			$activity_id = $val['activity_id'];
			$result[$activity_id]['activity_id'] = $activity_id;
            $result[$activity_id]['total'] = $count = empty($countt[$activity_id]) ? 0 : $countt[$activity_id];
			//$count = count($activity_num);
			if($count == 0 && $taskNotDone){
				$result[$activity_id]['activity_num'] = array();
				$result[$activity_id]['status'] = 4;
			}else if($count == 0){//失败
				$result[$activity_id]['activity_num'] = array();
				$result[$activity_id]['status'] = 3;
				$result[$activity_id]['no_use_money'] = $val['num'];
				$newActivity[] = $val['activity_id'];
			}else if($count < $val['num']){//不足
				$result[$activity_id]['status'] = 2;
				$result[$activity_id]['activity_num'] = $realNum[$activity_id];
				$result[$activity_id]['no_use_money'] = $val['num'] - $count;
			}else{//成功
				$result[$activity_id]['status'] = 1;
				$result[$activity_id]['activity_num'] = $realNum[$activity_id];
			}
			$activity_ids[] = $val['activity_id'];
		}
		
		//获取商品title
		$where = array(
			'activity_id' => array(
				$activity_ids,'in'
			)
		);
		$column = array(
			'activity_id','goods_id'
		);
		$nc_list->setDbConf('shop', 'activity');
		$goods = $nc_list->getDataList($where, $column);
		$goods_ids = $activityGoods = $goodsActivity = array();
		foreach($goods as $val){
			$goods_ids[] = $val['goods_id'];
			$activityGoods[$val['goods_id']] = $val['activity_id'];
			$goodsActivity[$val['activity_id']] = $val['goods_id'];
		}
		$where = array(
			'goods_id' => array(
				$goods_ids,'in'
			)
		);
		$column = array(
			'goods_id','title'
		);
		$nc_list->setDbConf('shop', 'goods');
		$goodsInfo = $nc_list->getDataList($where, $column);
		foreach($goodsInfo as $val){
			$activity_id = $activityGoods[$val['goods_id']];
			$result[$activity_id]['goods_title'] = $val['title'];
		}
		
		//如果有些活动已经购买失败，则获取最新一期的活动id
		if(!empty($newActivity)){
			$goods_ids = array();
			foreach($newActivity as $val){
				$goods_ids[] = $goodsActivity[$val];
			}
			$where = array(
				'goods_id' => array(
					$goods_ids,'in',
				),
				'flag' => 0
			);
			$column = array(
				'activity_id','goods_id'
			);
			$nc_list->setDbConf('shop', 'activity');
			$activityInfo = $nc_list->getDataList($where, $column, array(), array(), false);
			if(!empty($activityInfo)){
				foreach($activityInfo as $val){
					$activity_id = $activityGoods[$val['goods_id']];
					$result[$activity_id]['new_activity_id'] = intval($val['activity_id']);
				}
			}
		}
		return $nc_list->toArray($result);
	}

    public function getPayResult($order_num){
        $nc_list = Factory::getMod('nc_list');
        $where = array(
            'order_num' => $order_num,
        );
        $column = array(
            'flag'
        );
        $nc_list->setDbConf('shop', 'order');
        //获取订单详情
        $order = $nc_list->getDataOne($where, $column,array(),array(),false);
        return $order;
    }
    /**
    **福袋开5个出10个 这样开出起码就5个商品   然后每个商品对应1到3个
    * 福袋比率    每10个福袋开出炸  30 利润比商品2个  40利润比 4个 50利润比5个
    */
    public function getPacketOrder($base, $ipt_list){   
    	$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
    	 
    	if($login_user['lucky_packet']<$ipt_list['num']+0){
    		api_result(5, '福袋数量错误,现在福袋数量是:'.$login_user['lucky_packet']);
    	}
    	if($ipt_list['num']+0>10){
    		api_result(5, '目前只能开10个福袋!');
    	}
        $nc_list_mod = Factory::getMod('nc_list');
        $nc_list_mod->setDbConf('main', 'user');
        $percent=rand(0,200)*0.01;  //10个最小开到13个 10个平均开15个  福袋送1元购 概率1.22   目前概率太大看看需不需要放大10倍 1,200 *0.01
        //如果开1个福袋。开了3个的话就通知所有人
     	        
        if($percent<0.3){
        	$percent=0.3;
        } 
      /*  if(rand(1,10)>2 && $percent>1.7 ){
        	 $percent=$percent-0.3;
        } 
	
        if(rand(1,10)>2 && $percent>0.7 ){
        	 $percent=$percent-0.3;
        } */

        $sql=" select packet_num,open_packet_num from ".DATABASE.".t_sysset";
        $packet = $nc_list_mod->getDataBySql($sql,false); 
        $packetpercent=round($packet[0]['open_packet_num']/$packet[0]['packet_num'],1)-1;//10个平均开15个
        
        if($packetpercent >0.5  && rand(1,10)>2){
        	$percent=0.5;
        } 
        //$percent=0.5;
       // echo $ipt_list['num'],'kk',$percent,'cc'; 
        $num= intval($ipt_list['num']*(1+$percent));   
       // echo $num;exit;
        $where="and rate_percent between 30 and 50";
        $sql = "select  g.rate_percent,g.main_img,g.goods_id,g.title goods_name,a.activity_id,a.need_num-a.user_num-$num lastnum from ".DATABASE.".t_activity a left join ".DATABASE.".t_goods g on a.goods_id=g.goods_id   where     a.`flag`=0 and g.activity_type=1  $where  having lastnum >0  order by `process` asc,  lastnum desc  , rate_percent desc  limit 30";
        
        $goods = $nc_list_mod->getDataBySql($sql,false); 
        if(!$goods){ 
        	$where=" ";
        	$sql = "select  g.rate_percent,g.main_img,g.goods_id,g.title goods_name,a.activity_id,a.need_num-a.user_num-$num lastnum from ".DATABASE.".t_activity a left join ".DATABASE.".t_goods g on a.goods_id=g.goods_id   where     a.`flag`=0 and g.activity_type=1  $where  having lastnum >0  order by `process` asc,  lastnum desc  , rate_percent desc  limit 30";
        	if(!$goods){
       			api_result(5, '开启福袋数量较多,目前找不到适当可开奖商品。' );
       		}
        }
        shuffle($goods);
        $sql="update ".DATABASE.".t_sysset set packet_num=packet_num+{$ipt_list['num']}, open_packet_num=open_packet_num+{$num}";
        $nc_list_mod->executeSql($sql);
        $count=$ipt_list['num'];
        //$count=count($goods);
       // $count= rand(1,count($goods));  
       /* $num=12;
        $count=5;*/
        $a=true;
        while($a){
        	$luckyarray=$this->sendpacket($num,$count);  
        	if($num==array_sum($luckyarray)){
        		$a=false;
        	}
        	 
        }
       
 		 
        $luckypercent[0]=round($num*0.6);
        $luckypercent[1]=round($num*0.266); 
        $luckypercent[2]=round($num*0.134);
        
		$lucky=$this->dealpacketarr($luckyarray,$luckypercent);// var_dump($lucky);exit;
		$rabate_goods=array();
		foreach($goods as $v){
	 		if($v['rate_percent']>45){
	 			$rabate_goods[0][]=$v;
	 		}else if($v['rate_percent']>35 && $v['rate_percent'] < 45){
	 			$rabate_goods[1][]=$v;
	 		}else if($v['rate_percent']>=25 && $v['rate_percent'] <= 35){
	 			$rabate_goods[2][]=$v;
	 		}
	 		 
	 	}
	   

	 	foreach($lucky as $k=>$v){
	 		 foreach($v as $c){
	 		 	 /* if($k==0){
	 		 	     $tgoods=$rabate_goods[$k][0]; 
	 				 shuffle($rabate_goods[$k]);
	 		 	  }
	 		 	  if($k==1){
	 		 	    $tgoods=$rabate_goods[$k][0]; 
	 				shuffle($rabate_goods[0]);
	 		 	  }
	 		 	  if($k==2){
		 		 	  $tgoods=$rabate_goods[2][0]; 
		 			  shuffle($rabate_goods[0]); 
	 		 	  }*/
	 		 if(!$rabate_goods[$k]){
  				$rabate_goods[$k][0]=$goods[0];
	 		 }
	 	     $orderInfo[] = array(
				'activity_id' => $rabate_goods[$k][0]['activity_id'], 
				'num' => $c,  
			 ); 
        	$datainfo['num']=$c;     //当前商品总共买了几次
	        $datainfo['goods_name']=$rabate_goods[$k][0]['goods_name'];
	        $datainfo['activity_id']=$rabate_goods[$k][0]['activity_id'];
	        $datainfo['goods_img']=$rabate_goods[$k][0]['main_img']; 
        	$resultlist[]= $datainfo;
        	shuffle($rabate_goods[$k]);

	 		 }
	 	}

	 	/*var_dump($goods);
	 	var_dump($resultlist);*/
	 	$allnum=0;
       foreach($orderInfo as $v){
       		$allnum+=$v['num'];
       }
       if($allnum > $num){
       		 api_result(5, '开启福袋失败,请重新开启!' );
       }
        
        //  echo $one,'ccc',$two,'www',$three;exit;
        //  echo $num; var_dump($luckyarray);exit;
        
     /*   $resultlist=array(); 
         
        foreach($luckyarray as $k=>$v){ 
        	$orderInfo[] = array(
				'activity_id' => $goods[$k]['activity_id'], 
				'num' => $v,  
			 );
        	$datainfo['num']=$v;     //当前商品总共买了几次
	        $datainfo['goods_name']=$goods[$k]['goods_name'];
	        $datainfo['activity_id']=$goods[$k]['activity_id'];
	        $datainfo['goods_img']=$goods[$k]['main_img']; 
        	$resultlist[]= $datainfo;

        }*/
         if($percent>=1.3){
        	$resultinfo['show']=2;
        }else if($percent >=0.7){
        	$resultinfo['show']=1;
        }else{
        	$resultinfo['show']=0;
        }
     /*   $resultinfo['show']=$percent==2?2:1;
        $resultinfo['show']=($num==$ipt_list['num'])?0:$resultinfo['show'];*/
        $resultinfo['percent']=$percent;
        $resultinfo['list']=$resultlist; 
        $resultinfo['luckynum']=$num;  //开福袋中奖数量sss
	    $resultinfo['num']=$ipt_list['num'];  //开了几个福袋 
       if($resultinfo['num']==1 && $resultinfo['luckynum']==3){ // 开1个中3个提示到所有用户
       	 	$msg_mod = Factory::getMod('msg');
       		$msg=array('uid'=>$login_user['uid'],'nick'=>$login_user['nick'] ,'activity_type'=>8,'num'=>$resultinfo['num'],'lucky_num'=> $resultinfo['luckynum']);  
       		$msg_mod->sendSystNotify(3,$msg); 
       }
      	/*$activity_id=$goods[0]['activity_id'];
      	$goods_id=$goods[0]['goods_id'];*/ 
      	 
		try{
			  
			//开启事务
			$nc_list_mod->executeSql('START TRANSACTION'); 
			//写订单
			$sql = "update ".DATABASE.".t_user set `lucky_packet`=`lucky_packet`-{$ipt_list['num']} where `uid`={$login_user['uid']} and `lucky_packet`>={$ipt_list['num']}";
        	$updatesql=$nc_list_mod->executeSql($sql);
	        if(!$updatesql){
	         	api_result(5, '当前福袋数量不足以开奖，请刷新页面重新再开福袋。' );
	        }
	        //添加到福袋动态
	        $msg_mod = Factory::getMod('msg'); 
            $content=json_encode($resultinfo);
            $msg_mod->sendPacketNotify($login_user['uid'],5,$ipt_list['num'],$content,$goods_id,1,'');
          
			$nc_list_mod->setDbConf('shop', 'order');
			 
			$money_info = array(
				'luckypacket_use' => $ipt_list['num'],//红包存放规则：array($packet_id,$money)
				'remain_use' => $num,
				'need_money' => 0,
				'luckypacket_num'=>$num,
			);
			$nc_pay = Factory::getMod('nc_pay');
			$orderNum=$nc_pay->makeOrderNum($login_user['uid']);

			$data = array(
				'appid' => $login_user['appid'],
				'order_num' => $orderNum,
				'uid' => $login_user['uid'],
				'order_info' => json_encode($orderInfo),
				'money_info' => json_encode($money_info),
				'order_type' => 0,
				'ip' => get_ip(1),
				'flag' => 1,
				'ms' => $ms,
				'rt' => $currentTime,
				'ut' => $currentTime,
				'order_aid'=>0,
				'order_goods_id'=>0,
				'address_id'=>0,
				'goods_type'=>7
			);
		 	 
			$nc_list_mod->insertData($data); 
			$data = array(
						'order_num' => $orderNum,
						'flag' => 0,
						'ut' => $currentTime,
						'rt' => $currentTime
					);
		    $nc_list_mod->setDbConf('shop', 'task'); 
			$nc_list_mod->insertData($data);  
			$nc_list_mod->executeSql('COMMIT');
			$order_mod = Factory::getMod('nc_games');
        	 $order_mod->updatetask($login_user['uid'],'task1','task1_time');
		}catch(Exception $e){
			$nc_list_mod->executeSql('ROLLBACK');
			api_result(1, '提交失败');
		}
		 //return '';
		api_result(0, '获取成功！',$resultinfo);

         
    }
    public function dealpacketarr($luckyarray,$percent){
    	// var_dump($luckyarray,$percent);
    	$num=0;$crr=array();
    	foreach($percent as $q=>$c){  

    		 foreach($luckyarray as $k=>$v){  
    		 	$num+=$v;
    		 	$crr[$q][]=$v;
    		 	unset($luckyarray[$k]);
	    		if($num>=$c){      
	    			$num=0;
	    			break;;
	    			 
	    		}
    		 
    		}
  

    	}
    	 
     	//var_dump($crr);exit;
    	return $crr;

    }

   /* L = 100 # 这两个值任意更改，也可以用sys.argv来设置
    n = 5 
    
    lst = []
    j = L 
    k = L 
    for i in xrange(n-1): # 随机生成前面n-1个数
        while j > ( k - (n - i) ): # 防止随机数太大，让后面的数不够分
            j = randint(1, k)
        lst.append( j ) 
        k -= j
        j = k

    lst.append( L - sum(lst) ) # 最后一个数字，用减法*/
    //或者随机分配1堆数组然后再取出来
 	public function sendpacket($total,$count){
 		/*$total=20;
 		$count=3; */
 	    $avg   = intval($total/$count);   
        $i       = 0;
        $items=array();
        while($i<$count){ 
        	if($i<$count-1){  
        		  $a=mt_rand(1,3);
        		 
        		 $all= array_sum($items); 
        		 if(($total-$a-$all)/($count-count($items))<1){
        		 	$a=1;
        		 } 
        		 $items[]  = $a;
        		 
        	}else{  
        		 $last=$total-array_sum($items); 
        		/* var_dump($items);
        		 echo $total,'cc',array_sum($items);
        		 if($last==0){
        		  return	$this->sendpacket($total,$count);
        		 }*/

        		 if($last>3){     
        		    $items[]=3;
        		 	$lastnum=$last-3; 
        		  
        		 	for($c=0;$c<$count;$c++){
        		 		if($lastnum==0){
	        		 	   	 break;
	        		 	   }
        		 		if($items[$c]<3){ 
	        		 	   $items[$c]+=1;
	        		 	   $lastnum--;
	        		 	    
        		 		} 
        		 	}
        		 	  $items[$c]=$lastnum;
        		 }else{
        			 $items[]=$total-array_sum($items); 
        		}
        		 
        	}	
        	 
          $i++;
        } 
        return $items;
        
      
 	}
    //红包算法
    public function sendRandBonus($total=0, $count=3, $type=2){
    	$total=10;$count=5;
      if($type==1){
      	$input     = range(1, $total,1);
        if($count>1){
          $rand_keys = (array) array_rand($input, $count-1);
          $last    = 0;
        // $input     = range(0.01, $total, 0.01);小数红包
          foreach($rand_keys as $i=>$key){
            $current  = $input[$key]-$last;
            $items[]  = $current;
            $last    = $input[$key];
          }
        }
        if($total-array_sum($items) > 0){
        	$items[]= $total-array_sum($items);
        }
         
      }else{
	  /*    	$avg      = number_format($total/$count, 2);
	    $i       = 0;
	    while($i<$count){
	      $items[]  = $i<$count-1?$avg:($total-array_sum($items));
	      $i++;
	    }
	  }
	  return $items;*/
        //$avg      = number_format($total/$count, 2); 
        $avg   = intval($total/$count);  //echo $total.'ccc'.$total.'kk'.$avg; 
        $i       = 0;
        while($i<$count){
        	if($i<$count-1){
        		 $items[]=$total-array_sum($items);
        	}else{

        		  $items[] =$avg;
        		//$items[] =mt_rand(1,3)
        	}	
         // $items[]  = $i<$count-1?$avg:($total-array_sum($items));
          $i++;
        }
      }
      return $items;
    }
}