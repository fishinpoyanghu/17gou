<?php
/**
 * @since 2016-01-13
 * note 订单相关
 */
class NcOrderCtrl extends BaseCtrl{
	

	
	/**
	 * 支付订单
	 */
	public function orderInfo(){
	
		// $this->test_post_data(array('data' => array(array('activity_id' => 31, 'goods_title' =>'iWatch', 'num' => 2))));
	
		//标准参数检查
		$base = api_check_base(); 
		$postData = pstr('data');	
	    $orderType = pstr('data');		 
 	
		//检测参数
		if(empty($postData)){   
			 api_result(1, '参数不正确');
		}
		 
		$data = array();
		 
		//处理参数
		foreach($postData as &$val){
			 
			if($orderType[0]['orderType']==4  ){ //单独购买
				 
				if(empty($val['goods_id']) || empty($val['goods_num']) || empty($val['goods_title'])){
					api_result(1, '参数不正确');
				}
			}else if($orderType[0]['orderType']==3){  //表示开团
				if(empty($val['goods_id'])   || empty($val['goods_title'])){
					api_result(1, '参数不正确');
				} 
			}else if($orderType[0]['orderType']==2 || $orderType[0]['orderType']==1){  //表示岑团
				if(empty($val['activity_id']) || empty($val['goods_title'])){
					api_result(1, '参数不正确');
				} 
			}else if($orderType[0]['orderType']==5){ //购买福袋
				if(empty($val['num']) || empty($val['goods_title'])){
					api_result(1, '参数不正确');
				}
			}else{
				if(empty($val['activity_id']) || empty($val['num']) || empty($val['goods_title'])){
					api_result(1, '参数不正确');
				}
			}

			$val['activity_id'] = intval($val['activity_id']);
			$val['num'] = intval($val['num']);
			$data[$val['activity_id']]['title'] = $val['goods_title'];
			$data[$val['activity_id']]['num'] += $val['num'];
			$data[$val['activity_id']]['hot_luckyBuy']=$val['hot_luckyBuy'];
			$data[$val['activity_id']]['goods_id']=$val['goods_id'];
			$data[$val['activity_id']]['goods_num']=$val['goods_num'];
			$data[$val['activity_id']]['address_id']=$val['address_id'];

		}

		$nc_pay_mod = Factory::getMod('nc_pay');
		$result = $nc_pay_mod->getOrderInfo($base, $data,$orderType[0]['orderType']);
		// $postData[0]['parent_invite_code']='fjfbhww'; 
/*		if(isset($postData[0]['parent_invite_code'])){ 
			 $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		     $rebate_uid = api_decode_invite_code($base['appid'], $postData[0]['parent_invite_code']);

			 $nc_list = Factory::getMod('nc_list');   
			 $nc_list->setDbConf('main', 'activity_person');          
	         $where = array(
				'uid' => $login_user['uid']
			 );
			 $column = array(
				'uid'
			 ); 
		    $person = $nc_list->getDataOne($where, $column,array(),array(),false);		    	 
		    if(!$person && $login_user['uid']!=$rebate_uid && $rebate_uid){    		    	 
		    	 $insert = array(
                'uid' => $login_user['uid'],
                'pid' => $rebate_uid,
                'ut' => time()
                  ); 
                
                $nc_list->insertData($insert);  
           
		    }
		}*/

		 
		api_result(0, '成功', $result);
	}

/*	public function teamorderinfo(){
		$base = api_check_base(); 
		$postData = pstr('data');		 
		//检测参数
		if(empty($postData)){   
			 api_result(1, '参数不正确');
		}
		$data = array();
		//处理参数
		foreach($postData as &$val){
			if(empty($val['teamwar_id']) || empty($val['num']) || empty($val['goods_title'])){
				api_result(1, '参数不正确');
			}
			$val['teamwar_id'] = intval($val['teamwar_id']);
			$val['num'] = intval($val['num']);
			$data[$val['teamwar_id']]['goods_title'] = $val['goods_title'];
			$data[$val['teamwar_id']]['num'] += $val['num']; 
		}
		$nc_pay_mod = Factory::getMod('nc_pay');
		$result = $nc_pay_mod->getTeamOrderInfo($base, $data); 
	 	api_result(0, '成功', $result);
		
	}*/
	
	/**
	 * 订单状态查询
	 */
	public function orderStat(){
		//测试使用
		//$this->test_post_data(array('order_num' => '2016012012195310000000060'));
		//标准参数检查
		$base = api_check_base();
		
		$validate_cfg = array(
			'order_num' => array(),
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		if(empty($ipt_list['order_num'])){
			api_result(5, '参数不正确');
		}
		$nc_list_mod = Factory::getMod('nc_list');
		$nc_list_mod->setDbConf('shop', 'order');
		$where = array(
			'order_num' => $ipt_list['order_num']
		);
		$column = array(
			'uid','flag','transaction_flag'
		);
		$orderInfo = $nc_list_mod->getDataOne($where, $column);
		//判断用户权限
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		if($orderInfo['uid'] != $login_user['uid']){
			api_result(9, '没有权限');
		}
		$flag = $orderInfo['flag'];
		if($orderInfo['flag'] == 0){
			if($orderInfo['transaction_flag'] == 3){
				$flag = 3;
			}else{
				$flag = 2;
			}
		}else{
			$flag = 1;
		}
		
		$data = array(
			'status' => $flag
		);
		api_result(0, '获取成功', $data);
	}
	
	/**
	 * 订单结果查询
	 */
	public function orderResult(){
		//测试使用
		//$this->test_post_data(array('order_num' => '2016012220005810000000060'));
		//标准参数检查
		$base = api_check_base();
		
		$validate_cfg = array(
			'order_num' => array(),
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		if(empty($ipt_list['order_num'])){
			api_result(5, '参数不正确');
		}
		$nc_order = Factory::getMod('nc_order');
		$result = $nc_order->getOrderResult($base, $ipt_list);
		api_result(0, '获取成功', $result);
	}

    public function payResult(){
        $order_num = gstr('order_num');
        $sign = gstr('sign');
        if($order_num == '') exit;
        if(md5($order_num.LOGIN_KEY)!=$sign) exit;
        $nc_order = Factory::getMod('nc_order');
        $result = $nc_order->getPayResult($order_num);
        Factory::getView("pay/view_pay", $result);
    }
    //福袋订单
    public function packetorder(){
    	$base = api_check_base(); 
    	// $this->test_post_data(array('num'=>10));
    	 
    	//$this->test_post_data(array('num'=>5));
		$validate_cfg = array(
			'num' => array('api_v_numeric|1||福袋数量不合法'),
		);

		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
 		//$nc_list = Factory::getMod('nc_list');   
		/*$nc_list->setDbConf('main', 'activity_person');        
		$sql = "UPDATE `17gou`.`t_sysset` SET `packet_num` = '0' , `open_packet_num` = '0' ";
		$updatesql=$nc_list->executeSql($sql); 
		$sql = " TRUNCATE `17gou`.`t_order`; ";
		$updatesql=$nc_list->executeSql($sql); 
		$nc_order = Factory::getMod('nc_order');

		for($i=1;$i<=10;$i++){
			$nc_order->getPacketOrder($base, $ipt_list);
			 
    	}
        $this->packetorderinfo();exit;*/
        $nc_order = Factory::getMod('nc_order');
		$result = $nc_order->getPacketOrder($base, $ipt_list);
		api_result(0, '获取成功', $result);

    }

/*    public function packetorderinfo(){
    	 $nc_list = Factory::getMod('nc_list');   
		 $nc_list->setDbConf('main', 'activity_person');        
		 $sql="select * from 17gou.t_order where uid=26 ";
		 $order=$nc_list->getdatabysql($sql,false);
		 $one=0;$two=0;$three=0;
		 foreach($order as $k=> $v){
		 	//echo  "第{$k}张订单",'</br>';
			 $orderifo=json_decode($v['order_info'],true);
			 foreach($orderifo as $v){
			 	 $sql="select g.title ,g.rate_percent from 17gou.t_goods g left join 17gou.t_activity a on a.goods_id=g.goods_id where    a.activity_id=".$v['activity_id'];
			 	   
			 	 $goods=$nc_list->getdatabysql($sql,false); 
			 	// echo '产品：'.$goods[0]['title'].'分润比:'.$goods[0]['rate_percent'].'个数:'.$v['num'],'</br>';
			 	 if($goods[0]['rate_percent']>45){
	 				$one+=$v['num'];
	 			//	echo 'ccc',$v['num'];
		 		}else if($goods[0]['rate_percent']>35 &&  $goods[0]['rate_percent'] < 45){
		 			$two+=$v['num'];
		 		}else if($goods[0]['rate_percent']>=25 && $goods[0]['rate_percent'] <= 35){
		 			$three+=$v['num'];
		 		}


			 }
		   
		 }

		 echo '开出分润比50个数'.$one.'分润比40个数'.$two.'分润比30个数'.$three,'</br>';
    }*/
}