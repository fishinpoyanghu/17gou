<?php
/**
 * @since 2016-01-03
 * note 夺宝记录相关
 */
class NcRecordCtrl extends BaseCtrl{
	

	
	/**
	 * TA的夺宝记录
	 */
	public function recordList(){
		/*------测试环境下使用------*/
		//$this->test_post_data(array('uid' => 60, 'from' => 1, 'count' => 10));
		
		//标准参数检查
		$base = api_check_base();
		
		$validate_cfg = array(
			'uid' => array(),
			'from' => array(),
			'count' => array(),
			'status' => array()
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$nc_record_mod = Factory::getMod('nc_record');
		$result = $nc_record_mod->getRecordList($ipt_list, $base);
		
		api_result(0, '获取成功', $result);
	}
	
	/**
	 * TA的中奖记录
	 */
	public function winRecordList(){
		/*------测试环境下使用------*/
		//$this->test_post_data(array('from' => 1,'count' => 10, 'status' => 0));
		
		//标准参数检查
		$base = api_check_base();
		
		//设置参数
		$validate_cfg = array(
			'uid' => array(),
			'logistics_stat' => array(),
			'status' => array(),
			'from' => array(),
			'count' => array()
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$nc_record_mod = Factory::getMod('nc_record');
		$result = $nc_record_mod->getWinRecordList($ipt_list, $base);
		
		api_result(0, '获取成功', $result);
	}
	public function TeamWinRecordList(){
		/*------测试环境下使用------*/
		//$this->test_post_data(array('from' => 1,'count' => 10, 'status' => 0));
		
		//标准参数检查
		$base = api_check_base();
		
		//设置参数
		$validate_cfg = array(
			'uid' => array(),
			'logistics_stat' => array(),
			'status' => array(),
			'from' => array(),
			'count' => array()
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$nc_record_mod = Factory::getMod('nc_record');
		$result = $nc_record_mod->getTeamWinRecordList($ipt_list, $base);
		
		api_result(0, '获取成功', $result);
	}
	/**
	 * 获取某个用户某一期的夺宝号
	 */
	public function activityNum(){
		/*------测试环境下使用------*/
		//$this->test_post_data(array('uid' => 60, 'activity_id' => 1));
		
		//标准参数检查
		$base = api_check_base();
		//设置参数
		$validate_cfg = array(
			'uid' => array(),
			'activity_id' => array(
				'api_v_numeric|1||activity_id不合法',
			),
		    'team'=>array()
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		 
		$nc_record_mod = Factory::getMod('nc_record');
		$result = $nc_record_mod->getActivityNum($ipt_list, $base,$ipt_list['team']);
		
		api_result(0, '获取成功', $result);
	}

	public function logistic(){  
		 $base = api_check_base(); 
		 $validate_cfg = array(
		/*	'logistics_num' => array(	
				'api_v_notnull||快递号码不能为空',)
			,
			'activity_id' => array(
				'api_v_numeric|1||activity_id不合法',
			),*/
			  'logistics_id' => array(
				'api_v_numeric|1||logistics_id 不合法',
			),
		    
		);
	  
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);  
		//$ipt_list['logistics_num']=719608095086;
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		$where = array( 
			//'activity_id' => $ipt_list['activity_id'],
			'logistics_id'=>$ipt_list['logistics_id']
		); 
		 
		$nc_list= Factory::getMod('nc_list');
	    $nc_list->setDbConf('shop', 'logistics'); 
		$ret = $nc_list->getDataOne($where, array(), array(), array(), false); 
		if(empty($ret)){
			api_result(1, '物流单号错误!');
		}
		 
		$where = array(  
			'uid'=>$login_user['uid']
		);  
		$time=time();
		$nc_list->setDbConf('main', 'user_extend'); 
		$limit=10;//限制每天只能查10次
		$usermsg = $nc_list->getDataOne($where, array(), array(), array(), false);   
 	    if(!empty($usermsg)){
 	    	 
 	    	$msg=json_decode($usermsg['user_msg'],true);
 	    	if(date('Y-m-d')==date('Y-m-d',$msg['time']) ){ //判断是不是今天
 	    		 if($msg['logistic_count']>=$limit){
 	    		 	api_result(1,"每天查看物流信息不能超过{$limit}次!");
 	    		 }
 	    		$msg['logistic_count']=$msg['logistic_count']+1;
 	    		$msg['time']=$time;
 	    		$update['user_msg']=json_encode($msg);
 	    		$nc_list->updateData($where,$update); 
 	    	}else{
 	    		$msg['logistic_count']=1;
 	    		$msg['time']=$time;
 	    		$update['user_msg']=json_encode($msg);
 	    		$nc_list->updateData($where,$update);
 	    	}
 	    	 

 	    }else{ 
 	    	$user_msg['time']=$time;
 	    	$user_msg['logistic_count']=1;
 	    	$insertData['uid']=$login_user['uid'];
 	    	$insertData['user_msg']=json_encode($user_msg);  
 	    	$nc_list->insertData($insertData);
 	    	 
 	    }
 	   
	   /* $nc_list->setDbConf('shop', 'goods'); 
        $where = array(
            'uid' => $login_user['uid'],
            'desc' => '完善资料',
        );
        $ret = $nc_list->getDataOne($where, array(), array(), array(), false);*/


		$host = "http://ali-deliver.showapi.com";
	    $path = "/showapi_expInfo";
	    $method = "GET";
	    $appcode = "0276ea16f7fe4644acccc6b851ef1e7a";
	    $headers = array();
	    $typeNu=$ret['logistics_num'];
	    //$typeNu='123';  
	    
	    array_push($headers, "Authorization:APPCODE " . $appcode);
	    $querys = "com={$ret['logistics_type']}&nu=$typeNu";
	    $bodys = "";
	    $url = $host . $path . "?" . $querys;

	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($curl, CURLOPT_FAILONERROR, false);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_HEADER, false);
	    if (1 == strpos("$".$host, "https://"))
	    {
	        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	    }
	    $msg=curl_exec($curl);
	    curl_close($curl); 
 	    api_result(0, '获取成功',json_decode($msg,true));
    	// var_dump(json_decode($msg,true));  
	}


	public function getcom(){  
		 
    $host = "http://ali-deliver.showapi.com";
    $path = "/showapi_expressList";
    $method = "GET";
    $appcode = "0276ea16f7fe4644acccc6b851ef1e7a";
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    $querys = "expName=&maxSize=100000&page=";
    $bodys = "";
    $url = $host . $path . "?" . $querys;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    if (1 == strpos("$".$host, "https://"))
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    $msg=curl_exec($curl);
    
    var_dump( json_decode($msg,true));

	}

}