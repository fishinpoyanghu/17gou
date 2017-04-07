<?php
/**
 * @since 2016-01-29
 */
class NcTeamMod extends BaseMod{
	
	/**
	 * 幸运号计算详情
	 */
	public function getLuckyNumDetail($base, $ipt_list){
		$nc_list = Factory::getMod('nc_list');
		$where = array(
			'activity_id' => $ipt_list['activity_id']
		);
		$column = array(
			'lucky_num','time_sum','lottery_num','need_num','user_record','lottery_num'
		);
		$nc_list->setDbConf('team', 'team_lucky_num');
		//获取该期结果
		$data = $nc_list->getDataOne($where, $column);
		$result = array();
		$result['value_a'] = $data['time_sum'];
		$result['need_num'] = intval($data['need_num']);
        $result['value_b'] = 0;
		if(empty($data['lottery_num'])){//如果没有时彩号，说明该期还未揭晓
			$result['status'] = 1;
		}else{
			$result['status'] = 2;
            $result['value_b'] = intval($data['lottery_num']);
        }
        if($data['lucky_num']){
            $result['status'] = 2;
            $result['lucky_num'] = $data['lucky_num'];
        }else{
            $result['status'] = 1;

        }

		if(!empty($data['user_record'])){
			//该期结束时，最后的五十个参与记录
			//记录格式：id、uid、time
			$user_record = json_decode($data['user_record'], true);
			$aDetail = array();
			$uid_numid = $uids = array();
			foreach($user_record as $val){
                $time = substr($val['rt'],0,4) .'-'. substr($val['rt'],4,2) .'-'. substr($val['rt'],6,2) .' '. substr($val['rt'],8,2).':'.substr($val['rt'],10,2).':'.substr($val['rt'],12,2).'.'.substr($val['rt'],14);
                $aDetail[$val['id']] = array(
                    'time' => $time,
                    'activity_id' => $val['activity_id'],
                    'num' => $val['num'],
                    'title' => $val['title'],
                );
				$uid_numid[$val['uid']][] = $val['id']; 
			}
			$uids = array_keys($uid_numid);
			$where = array(
				'uid' => array(
					$uids,'in'
				)
			);
			$column = array(
				'nick','uid'
			);
			$nc_list->setDbConf('main', 'user');
			$userInfo = $nc_list->getDataList($where, $column);
			foreach($userInfo as $val){
				foreach($uid_numid[$val['uid']] as $id){
					$aDetail[$id]['unick'] = $val['nick'];
					$aDetail[$id]['uid'] = $val['uid'];
				}
			}
			$result['a_detail'] = $nc_list->toArray($aDetail);
		}
		return $result;
	}



	/**
	 * 获取分类列表
	 */
	public function getCategoryList($base, $keyVal = false){
		 
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
			$order['sort']='asc';
			$nc_list_mod->setDbConf('team', 'team_goods_type'); // 这里导航默认选11条 配合导航跟界面
			$categoryList = $nc_list_mod->getDataList($where, $column, $order, array('length'=>11), false);
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

	public function getGoodList($base, $ipt_list){
	/*	if(empty($ipt_list['status'])){
			$ipt_list['status'] = 0;
		}


		if(empty($ipt_list['order_key'])){
			$ipt_list['order_key'] = 'time';
		}*/
		
		if($ipt_list['activity_type']){
			$where['activity_type'] =  $ipt_list['activity_type'];
		} 
	 
		 
		$orderKey = array(
			'weight' => 'weight',
			'time' => 'goods_id',
            //'num' => 'need_num',
			//'ing' => 'process',
		);
		if($ipt_list['order_key']){
			$order = array(
				 $orderKey[$ipt_list['order_key']] => $ipt_list['order_type']?$ipt_list['order_type']:'desc'
			);
		}
		if(!empty($ipt_list['goods_type_id'])){
			$where['goods_type_id'] = $ipt_list['goods_type_id'];
		}
		$where['status']=1;
		$where['is_in_activity']=2;
		 
	/*	if(!empty($ipt_list['activity_type'])){   
			if($ipt_list['activity_type']==-4){
				$where['b.activity_type'] = array(
				4,'<'
			); //默认不显示二人购商品
			}else{
				$where['b.activity_type'] = $ipt_list['activity_type'];
			}
			 
		} */
		 //$ipt_list['key_word']='先锋油汀取';
		if(!empty($ipt_list['key_word'])){ //复合查询
			$whereArr[]=array('title','like','%'.$ipt_list['key_word'].'%');
			$whereArr[]= array('sub_title','like','%'.$ipt_list['key_word'].'%');
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
		/*$column = array(
			 'a.goods_id','a.title','a.price','a.goods_type_id','a.activity_type',
			 'a.sale_num','a.original_price','a.end_day', 'a.sub_title','a.people_num'
		);*/
		$column= array('goods_id','title','price','goods_type_id','activity_type','sale_num','original_price','end_day','sub_title','people_num','main_img');
		//连表配置
		$join = array(
			'from' => DATABASE.'.t_team_goods ',
			/*'join' => array(
				array(
					'join_type' => 'left join',
					'join_table' => DATABASE.'.t_teamwar b',
					'on' => array(
						 'a.goods_id=b.goods_id'
					)
				)
			)*/
		);
		$nc_list_mod = Factory::getMod('nc_list');
		$nc_list_mod->setDbConf('team', 'team_goods');
		
		//获取用户参与的期号
		$resultArr = $nc_list_mod->getDataList($where, $column, $order, $limit,false);
	  //  echo $nc_list_mod->getlastsql();exit;
	    $count = $nc_list_mod->getJoincount($join, $where, $column[0]);  
		$resultArr['count']=$count; 
		return $resultArr;  
		 
	}

	public function getCollectList($ipt_list, $base){
		$nc_list_mod = Factory::getMod('nc_list'); 
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		 
		//where条件
		$where = array(
			'a.uid' => $ipt_list['uid'], 
		);
		$limit = array(
			'begin' => $ipt_list['from'],
			'length' => $ipt_list['count']
		);
		/*//过滤状态
		if(!empty($ipt_list['status']) || strlen($ipt_list['status']) > 0){
			if($ipt_list['status'] == 3){
				$where['b.flag'] = array(
					array(0,1),'in'
				);
			}else{
				$where['b.flag'] = $ipt_list['status'];
			}
		}
		*/
		$where['b.status']=1;
		$where['b.is_in_activity']=2; 
		$where['a.uid']=$login_user['uid'];  
		$order = array(
			'a.collect_id' => 'desc'
		);
		
		$column = array(
			'b.goods_id','b.main_img','b.people_num','b.title','a.collect_id',
			'b.price','b.sale_num'
		);
		
		//连表配置
		$join = array(
			'from' => DATABASE.'.t_collect a',
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
		
		$nc_list_mod->setDbConf('team', 'collect');
		
		//获取用户参与的期号
		$resultArr = $nc_list_mod->getDataJoinTable($join, $where, $column, $order, $limit,false);
	  
	    $count = $nc_list_mod->getJoincount($join, $where, $column[0]);  
		$resultArr['count']=$count; 
		return $resultArr;  
		 
	}
	//订单是否限制申请团  仅仅幸运购限制，其他不限制
	//开团限制  此方法3处地方调用 1处是直接开团。1处是下单的时候验证是否给开团 .3.是进程处理团购订单
	//返回商品详情
	public function verifiyTeam($uid,$goodsid,$noEmpty = true){ 
		$nc_list = Factory::getMod('nc_list');   
	    $nc_list->setDbConf('team', 'teamwar');   
       
   
        $goodsql ="SELECT  price,team_limit,activity_type, people_num,end_day FROM   
         ".DATABASE.".t_team_goods     where  goods_id=$goodsid and status=1 and is_in_activity=2"; 
 
        $goods = $nc_list->getDataBySql($goodsql,false); 
         
        if(empty($goods)){ if(!$noEmpty) return false;
        	api_result(1, '当前商品已经下架!');
        }  
        if($goods[0]['team_limit']){ //开团数量限制
        	$sql="select count(flag) count from " .DATABASE.".t_teamwar where goods_id=$goodsid and  ( flag=1 || flag>6) and stat=0";  
        	$teamcount = $nc_list->getDataBySql($sql,false);  
        	if($teamcount[0]['count']>=$goods[0]['team_limit']){ if(!$noEmpty) return false;
        		api_result(1, '当前商品开团申请已爆满！请期待下一轮！');
        	}
        }
      /*  if($goods[0]['activity_type']==2){ //幸运购限制开团
	        $sql ="SELECT  w.flag   FROM   
	         ".DATABASE.".t_team_member    AS t  LEFT JOIN  ".DATABASE.".t_teamwar w ON   w.`teamwar_id`=t.`teamwar_id` where w.goods_id=$goodsid  and t.uid=$uid and w.stat=0 and w.flag!=2 and w.flag!=3"; 
	        $msg = $nc_list->getDataBySql($sql,false);       
	        if($msg){ //&& ($msg[0]['flag']==1 || $msg[0]['flag']==2)
	        	if(!$noEmpty) return false;
	         	api_result(1, '当前商品你已经申请参加了战团,不能再次申请');
	        } 
    	}*/


        return $goods;
	}


	/**
	 * 订单列表
	 * @param $page
	 * @param $num
	 * @param $state
	 * @return array
	 */
	public function order($base, $ipt_list){  
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);   
		$limit = array(
            'from' =>  $ipt_list['from'] <=0 ? 1 : (int)$ipt_list['from'] ,
            'count' => $ipt_list['count']>30?30:(int)$ipt_list['count']
        );

        $limit['count']=$limit['count']<1?10:$limit['count'];
        $status=$ipt_list['status']+0;

	    $nc_list = Factory::getMod('nc_list');
	    $nc_list->setDbConf('shop', 'order');
		$limit =  " limit ".($limit['from']-1).",{$limit['count']}"  ; 
 
		$and = " and a.`goods_type` > 1 and a.`goods_type` !=6";//商品类型1 是原1元购 ,2 表示参团 3表示开团 4表示拼团单独购买 5 表示开团（免费开）
		$and .=" and a.uid=".$login_user['uid']; 
		$and .=$status?" and a.status=".$status:''; 
		$table =  DATABASE." .t_order as a 
				left join ".DATABASE.".`t_team_goods` as c on a.`order_goods_id` = c.`goods_id`
				left join ".DATABASE.".`t_logistics` as b on a.`order_num` = b.`order_num`
				left join ".DATABASE.".`t_address` as r on r.`address_id` = a.`address_id`
				left join ".DATABASE.".`t_user` as d on a.`uid` = d.`uid`";

		$sql = "select  a.teamwar_id,a.goods_type,c.single_price,a.order_num,b.logistics_id,b.logistics_num,a.money_info,b.address,a.order_info,a.status, a.`rt` as t, c.goods_id,c.`title`,c.price,c.main_img, d.`nick`,r.province,r.city,r.area,r.detail,r.name,r.mobile from {$table} where 1 {$and}  order by a.`rt` desc {$limit}"; 
	     
		$resultArr = $nc_list->getDataBySql($sql,false);   
		if($resultArr){
			foreach($resultArr as $k=> $v){
				$money=json_decode($v['money_info'],true);
				$orderinfo=json_decode($v['order_info'],true); 
				$resultArr[$k]['goodsnum']=$orderinfo[0]['goods_num']?$orderinfo[0]['goods_num']:1;
				$resultArr[$k]['paymoney']=$money['need_money']+$money['remain_use']; 
				$resultArr[$k]['moneyinfo']=$money; 
				if($v['address']){
				    $aa = explode(':',$v['address']);
					$resultArr[$k]['address']=urldecode($aa[0]).' '.urldecode($aa[1]).' '.$aa[2];
				}else{
					$resultArr[$k]['address']= $resultArr[$k]['province'].$resultArr[$k]['city'].$resultArr[$k]['area'].$resultArr[$k]['detail'].' '.$resultArr[$k]['name'].' '.$resultArr[$k]['mobile'];
				}
				$resultArr[$k]['wxpay']= A_PATH.'/?c=nc_pay&a=wx_pay&order_num='.$v['order_num'];

				unset($resultArr[$k]['money_info'],$resultArr[$k]['order_info']); 
			}

		}
		  
		$count = $nc_list->getDataBySql("select count(*) as total from {$table} where 1 {$and}"); 
		 
	     
		$resultArr['count']=$count[0]['total']; 
		return $resultArr;   
		 
	}



}