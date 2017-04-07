<?php
/**
 * ninecent 用户收货地址api
 */

class TeamCtrl extends BaseCtrl {
	//baituan_detail 百团大战商品详情页
	 public function baituandetail() {    
	    $goodsid = pint('goods_id'); 
		if (!$goodsid) {
			api_result(5, '商品不合法');
		} 
		$pub_mod = Factory::getMod('pub'); 
		$pub_mod->init('team', 'team_goods', 'goods_id'); 
		$goods = $pub_mod->getRow($goodsid); 
        if (!$goods) {
			api_result(2, '战团活动的商品不存在'); 
		}
		$data = array( 
			'goods_id' => intval($goods['goods_id']),
            'goods_img' => explode(',', $goods['title_img']),
            'goods_category'=>intval($goods['goods_type_id']),
            'img' => $goods['main_img'],
			'goods_title' => ap_strval($goods['title']),
			'goods_subtitle' => ap_strval($goods['sub_title']),
			'activity_type' => intval($goods['activity_type']), 
			'peoplenum' => $goods['people_num'],
			'originalprice'=>$goods['original_price'],
			'singleprice'=>$goods['single_price']==intval($goods['single_price'])?intval($goods['single_price']):$goods['single_price'],
			'detail'=>$goods['detail'],
			'price'=>$goods['price'],
			'endday'=>$goods['end_day'],
			'sale_num'=>$goods['sale_num'],
			'status'=>$goods['status'],
			'is_in_activity'=>$goods['is_in_activity']
			 
		);
		$nc_list = Factory::getMod('nc_list');   
	    $nc_list->setDbConf('team', 'teamwar');   
	    $time=time();
        $sql ="SELECT  $time as nt, u.nick,w.teamwar_id,u.icon,w.uid,w.et,w.flag,w.people_num,w.user_num,w.join_num   FROM   
         ".DATABASE.".t_teamwar    AS w  LEFT JOIN  ".DATABASE.".t_user u ON w.`uid`=u.`uid` where w.goods_id=$goodsid and w.flag=1 and et > $time "; 
        $msg = $nc_list->getDataBySql($sql,false);  
        foreach($msg as $k=>$v){
        	$msg[$k]['icon']=ap_user_icon_url($v['icon'])['icon'];
        }
		$data['team']=$msg; 
		api_result(0, 'succ', $data);
		 
    }

	 public function teamdetail(){
	 	 $team = pint('team');  
	 	 if (!$team) {
			api_result(5, '战团不存在！');
		 }
	 	 $nc_list = Factory::getMod('nc_list'); 
	     $nc_list->setDbConf('team', 'teamwar');   
	     $sql ="SELECT  t.*,u.icon,u.nick FROM  ".DATABASE.".t_team_member  AS t   left join ".DATABASE.".t_user u on u.uid=t.uid where t.teamwar_id=$team"; 
         $teamlist = $nc_list->getDataBySql($sql,false); 
    	 $sql ="SELECT g.sub_title,g.sale_num,g.single_price,g.activity_type,w.publish_time,g.title,g.price,g.original_price,g.title_img, w.goods_id,w.uid teamleader,w.teamwar_id,w.user_num,w.join_num,w.people_num,w.flag,g.title,g.main_img,w.title war_title,FROM_UNIXTIME(w.et) end,w.et endtime FROM  ".DATABASE.".t_teamwar  AS w  left join ".DATABASE.".t_team_goods g on w.goods_id=g.goods_id where w.teamwar_id=$team "	 ;   
        $teamdetial = $nc_list->getDataBySql($sql,false); 
        if($teamdetial){
         	$teamdetial[0]['remain_time'] = $teamdetial[0]['publish_time'] - time();
         	$teamdetial[0]['endtime'] = $teamdetial[0]['endtime'] - time();
        }  
       if($teamdetial[0]['flag']>7){  
    	 	$sql="select u.nick ,u.icon,u.uid,t.lucky_num,t.user_num   from  ".DATABASE.".t_team_lucky_num t inner join ".DATABASE.".t_user u on u.uid=t.uid and t.activity_id=$team";
    	 	$luckymsg = $nc_list->getDataBySql($sql,false); 
    	 	$luckymsg[0]['icon']=ap_user_icon_url($luckymsg[0]['icon'])['icon'];
    	 	$data['lucky']=$luckymsg[0];   
       }
	   foreach($teamlist as $k =>$v){
	     	$teamlist[$k]['rt']=date('Y-m-d H:i:s', $v['rt']);
	     	$teamlist[$k]['icon']=ap_user_icon_url($v['icon'])['icon'];
	        if($data['lucky']['uid']==$v['uid']){
	     	     $data['lucky']['rt']=$teamlist[$k]['rt'];//获奖者参团时间
	     	}
	     }
    	 $data['teamlist']=$teamlist?$teamlist:'';//队员列表
         $data['teamdetial']=$teamdetial[0]; //团商品详情
         api_result(0, 'succ', $data); 

	 }

	 public function myteam(){ //我的战团
	 	$base = api_check_base();  
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true); 
	 	$nc_list = Factory::getMod('nc_list');   
	    $nc_list->setDbConf('team', 'team_member');  
	  
        $sql ="SELECT  t.team_member_id,u.nick name,w.teamwar_id,w.flag,g.title,g.main_img,w.user_num,w.join_num,w.people_num,w.ut,w.et  FROM   
         ".DATABASE.".t_team_member    AS t  LEFT JOIN  ".DATABASE.".t_teamwar w ON   w.`teamwar_id`=t.`teamwar_id` left join  ".DATABASE.".t_team_goods g on g.goods_id=w.goods_id  LEFT JOIN  ".DATABASE.".t_user u ON   u.`uid`=w.`uid`  where   t.uid={$login_user['uid']}"; 
        $msg = $nc_list->getDataBySql($sql);  

        foreach($msg as $v){
         	if($v['flag']>7){
         		$activity[]=$v['teamwar_id'];
         	}
         } 
        $activity=array_unique($activity);

        if(!empty($activity)){

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
    	$where = array( 
			'a.activity_id' => array(
				$activity,'in'
			)
		); 
		$nc_list_mod = Factory::getMod('nc_list');
		$nc_list_mod->setDbConf('team', 'teamwar');
		$column = array(
			'a.activity_id' ,'a.lucky_num', 'b.nick'
		); 
 
		$activityInfo = $nc_list_mod->getDataJoinTable($join, $where, $column, $order, $limit);
		 foreach($msg as $k=>$v){
		 	foreach($activityInfo as $av){
		 		if($v['teamwar_id']==$av['activity_id']){
		 			$v['lucky_num']=$av['lucky_num']; 
		 			$msg[$k]['lucky_num']=$av['lucky_num'];
		 			$msg[$k]['lucky_nick']=$av['nick'];
		 		}
		 	}
		 }

        }
        api_result(0, 'succ', $msg); 

	 }
	 //百团大战（幸运团） 10人团的话算上团长是11个人
	 //团长免费 （团长免费） 8人的团长免费 就是算上团长8人。开团后再拉7人就成团了 date : 2017-02-05
	 public function createteam(){  
	 	$base = api_check_base();  
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true); 
	    $validate_cfg = array(
			'address_id' => array(
				'api_v_numeric|1||收获地址不合法',
			),
			'goods_id' => array(
				'api_v_numeric|1||商品不合法',
			),
			'title' => array( 
			)
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		 
		 

		$nc_team_mod = Factory::getMod('nc_team');

		$goods = $nc_team_mod->verifiyTeam($login_user['uid'],$ipt_list['goods_id']);

		if($goods[0]['activity_type']==1){ //普通拼团必须是付费才能拼团
        	api_result(1, '当前商品是普通拼团商品,开团失败!');
        }  
        


	/*	$pub_mod->init('team', 'address', 'address_id') ;
		$addressInfo = $pub_mod->getRow($ipt_list['address_id']);
		if(!$addressInfo){
			api_result(1, '获取不到收获地址,请重新申请战团！');
		}

        $address = urlencode($addressInfo['province'].$addressInfo['city'].$addressInfo['area'].$addressInfo['detail']).':'.urlencode($addressInfo['name']).':'.$addressInfo['mobile'];*/
        //申团条件 同一商品且同1用户只能申请1个团。 1个用户可以申请多个团  
        $nc_list = Factory::getMod('nc_list');   
	    $nc_list->setDbConf('team', 'teamwar');   
        $sql ="SELECT   flag   FROM   
         ".DATABASE.".t_teamwar     where  goods_id={$ipt_list['goods_id']}  and  uid={$login_user['uid']} and  stat=0 and  flag=1   "; 
        $msg = $nc_list->getDataBySql($sql,false);       
        if($msg ){ //&& ($msg[0]['flag']==1 || $msg[0]['flag']==2)
         	api_result(1, '当前商品你已经申请参加了战团,不能再次申请');
        }  
      
         /*  $goodsql ="SELECT  price,team_limit,activity_type, people_num,end_day FROM   
         ".DATABASE.".t_team_goods     where  goods_id={$ipt_list['goods_id']} and status=1 and is_in_activity=2"; 
 
        $goods = $nc_list->getDataBySql($goodsql,false); 
        if($goods[0]['activity_type']==1){ //普通拼团必须是付费才能拼团
        	api_result(1, '当前商品是普通拼团商品,开团失败!');
        }  
        if(empty($goods)){
        	api_result(1, '当前商品已经下架!');
        }  
        if($goods[0]['team_limit']){ //开团数量限制
        	$sql="select count(flag) count from " .DATABASE.".t_teamwar where goods_id={$ipt_list['goods_id']} and  ( flag=1 || flag>6) and stat=0";  
        	$teamcount = $nc_list->getDataBySql($sql,false);  
        	if($teamcount[0]['count']>=$goods[0]['team_limit']){
        		api_result(1, '当前商品开团申请已爆满！请期待下一轮！');
        	}
        }*/
  		$pub_mod = Factory::getMod('pub'); 
        $pub_mod->init('team', 'teamwar', 'teamwar_id');
        $time=time();  
        $wardata = array( 
                'goods_id' => $ipt_list['goods_id']+0,
                'teamwar_id'=>get_auto_id(C('AUTOID_M_TEAMWAR')),
                'uid'=>$login_user['uid'], 
                'ut' => $time,
                'rt' => $time,
                'et' => $goods[0]['end_day']>0?$time+$goods[0]['end_day']*3600*24:$time+3600*24*3,
                'people_num'=>$goods['0']['people_num'],
                'need_num'=>$goods[0]['activity_type']==3?$goods['0']['price']*$goods['0']['people_num']:$goods['0']['price'],
                'user_num'=>0  
            );
        $wardata['join_num']=$goods[0]['activity_type']==3?1:0;//
        $ret = $pub_mod->createRow($wardata); 
        if($goods[0]['activity_type']==2){ //幸运团购就是要生成幸运码。随机抽。
        	$this->startteam($wardata['teamwar_id'],$goods[0]['price']);  
   	    }
        $pub_mod->init('team', 'team_member', 'team_id'); 
        $teamdata = array( 
                'teamwar_id' => $wardata['teamwar_id'], 
                'uid'=>$login_user['uid'], 
                'ut' => $time,
                'rt'=>$time,
                'address_id'=>$ipt_list['address_id'], 
                'num'=>0 //团长不用参与
            );
        $pub_mod->createRow($teamdata); 
        $nc_pay_mod= Factory::getMod('nc_pay');    
        $orderNum= $nc_pay_mod->makeOrderNum($login_user['uid']);
        $nc_list_mod= Factory::getMod('nc_list'); 
        $nc_list_mod->setDbConf('shop', 'order');
        $orderInfo=array();
        $money_info=array();
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
				'rt' => $time,
				'ut' => $time, 
				'teamwar_id'=>$wardata['teamwar_id'],
				'order_goods_id'=>$ipt_list['goods_id']+0,
				'goods_type'=>5,//免费开团
				'status'=>2,
				'address_id'=>$ipt_list['address_id'],
				
		);  
        $addret= $nc_list_mod->insertData($data);


        if($ret && $addret){
        	$msg_mod = Factory::getMod('msg');
        	$msg=array('uid'=>$login_user['uid'],'nick'=>$login_user['nick'],'activity_id'=>$wardata['teamwar_id'],'goods_id'=>$ipt_list['goods_id'],'activity_type'=>4); 
            $msg_mod->sendSystNotify(2,$msg);  
			api_result(0, '填写成功', array('team' => $wardata['teamwar_id']));
		}else{
			api_result(1, '填写失败');
		}

     

	 }
/*	 public function jointeam(){
	 	$base = api_check_base();  
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		$validate_cfg = array(
			'address_id' => array(
				'api_v_numeric|1||地址不存在',
			),
			'team_id' => array(
				'api_v_numeric|1||战团不存在',
			)
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);  
	 
	    $nc_list = Factory::getMod('nc_list');   
	    $nc_list->setDbConf('team', 'team_member');
	    $where=array('teamwar_id'=>$ipt_list['team_id'],'uid'=>$login_user['uid']);
	    $user=$nc_list->getDataOne($where,array('uid'),array(),array(),false); 
	    if($user){
	    	api_result(1, '参团失败,你已参加过当前团！');
	    }

	    $nc_list->setDbConf('team', 'teamwar');
	    $where=array('teamwar_id'=>$ipt_list['team_id']);
	    $column=array('user_num','people_num','flag') ;
	    $teammsg=$nc_list->getDataOne($where,$column,array(),array(),false);  
		if($teammsg['flag']!=1){
			api_result(1, '参团失败,当前团人数已满');
		}
		$data['user_num']=$teammsg['user_num']+1;
		if($teammsg['people_num']-$data['user_num']<0){
			api_result(1, '参团失败,当前团人数已满');
		} 
		if($teammsg['people_num']-$data['user_num']==0){
			$data['flag']=2;//成团
		}
	    $nc_list->updateData($where,$data); 
	    $nc_list->setDbConf('team', 'team_member');
	    $data=array('teamwar_id'=>$ipt_list['team_id'],'uid'=> $login_user['uid'],'ut'=>time(),'address_id'=>$ipt_list['address_id']);
	    $insert=$nc_list->insertData($where,$data);
	    if($insert){
	    	api_result(0, '参团成功!');
	    }else{
	    	api_result(1, '参团失败,当前团人数已满!');
		  
	    }



	 }*/
	 // 旧幸运首页获取参团以及开团列表
	 public function teamgoodlist(){ 
	 	  $nc_list = Factory::getMod('nc_list');   
	      $nc_list->setDbConf('team', 'team_member');
	      $time=time();//flag 0 未开团商品 1已开团商品
	      $sql ="select *  from (SELECT w.et,g.title,g.people_num, g.price,g.original_price,IFNULL(w.flag,0) AS flag, 1482717505 AS nt, w.teamwar_id, w.uid,  g.goods_id,w.user_num,w.join_num,g.main_img   FROM   
         ".DATABASE.".t_team_goods    AS g  LEFT JOIN  ".DATABASE.".t_teamwar w   ON w.`goods_id`=g.`goods_id` and w.et > $time and w.flag=1 where  g.status=1  and  activity_type=2   and g.is_in_activity=2   order by w.join_num desc  ) tmp group by goods_id";   //团结束要后台写代码实时更新团结束代码 
         
          $msg = $nc_list->getDataBySql($sql,false);  
      	  api_result(0, 'succ',$msg);

	}
	//新拼团，非幸运拼团， 即普通拼团跟团长免费
	public function goodslist(){ 
	    $base = api_check_base(); 
		$validate_cfg = array(
			'goods_type_id' => array(),
			'key_word' => array(),
			'order_key' => array(),
			'order_type' => array(),
			'activity_type' => array(),
			'status' => array(),
            'from' => array(),
            'count' => array(),

		); 

 		$ipt_list = api_get_posts($base['appid'], $validate_cfg); 
		$nc_team = Factory::getMod('nc_team');
		$result = $nc_team->getGoodList($base, $ipt_list);

		api_result(0, '获取成功', $result);
	}

	 /**
	 * 商品类别
	 */
	public function categoryList(){
		/*------测试环境下使用------*/
		//$this->test_post_data(array('goods_id' => 1, 'from' => 1, 'count' => 10));

		//标准参数检查
		$base = api_check_base();

		$nc_activity_mod = Factory::getMod('nc_team');
		$result = $nc_activity_mod->getCategoryList($base);

		if(empty($result)){
			api_result(0, '数据为空');
		}
		api_result(0, '获取成功', $result);
	}


	 //商品详情
     public function imgDetail() {  
		$base = api_check_base();  
		$goods_id = pint('goods_id');
		
		if (!$goods_id) {
			api_result(5, 'goods_id不合法');
		}
		
		$pub_mod = Factory::getMod('pub');
		
		// 取得云客活动
		$pub_mod->init('team', 'team_goods', 'goods_id');
		$goods = $pub_mod->getRow($goods_id);
		
		if (!$goods_id) {
			api_result(2, '商品不存在');
		}
		
		$data = array(
				'html' => ap_strval($goods['detail']),
		);
		
		api_result(0, 'succ', $data);
	}
    private function startteam($teamwar_id,$number){

        if(!$teamwar_id) return 0;
        if(!$number)   return 0;
         $nc_list = Factory::getMod('nc_list'); 
         $nc_list->setDbConf('team', 'team_num');
         $time = time();  
         
        //循环生成号码数据
        $num = array();
        for($i=0;$i<$number;$i++){
            $num[] = array(
                'num'=>bcadd(10000001,$i),
                'activity_id' => $teamwar_id,
            );
            if(count($num)>200){
                shuffle($num);
                $nc_list->insertMultyData($num);
                $num = array();
            }
        } 

        if(!empty($num)){
            shuffle($num);
            $nc_list->insertMultyData($num);
        }

        return $re;
    }
    //团活动揭晓
    public function activityList(){
		/*------测试环境下使用------*/
		//$this->test_post_data(array('status' => 3, 'from' => 1, 'count' => 10));

		//标准参数检查
		$base = api_check_base();

		$validate_cfg = array(
			//'goods_type_id' => array(),
		//	'key_word' => array(),
		//	'order_key' => array(),
			//'order_type' => array(),
		//	'activity_type' => array(),
		//	'status' => array(),
            'from' => array(),
            'count' => array(),
		);

		$ipt_list = api_get_posts($base['appid'], $validate_cfg);

		$nc_activity_mod = Factory::getMod('nc_activity');
		$result = $nc_activity_mod->getTeamActivityList($base, $ipt_list);
		api_result(0, '获取成功', $result);
	}

	/**
	 * 幸运号计算详情
	 */
	public function luckyNumDetail(){
		//测试使用
		//$this->test_lucky_num_detail();
		//标准参数检查
		$base = api_check_base();
		
		$validate_cfg = array(
			'activity_id' => array(
				'api_v_numeric|1||activity_id不合法',
			),
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$goods_mod = Factory::getMod('nc_team');
		$result = $goods_mod->getLuckyNumDetail($base, $ipt_list); 
		api_result(0, '获取成功', $result);
	}
	 //收藏列表
	public function collectlist(){ 
		$base = api_check_base(); 
		$validate_cfg = array( 
			'from' => array(),
			'count' => array(), 
		); 
		$ipt_list = api_get_posts($base['appid'], $validate_cfg); 
		$nc_team = Factory::getMod('nc_team');
		$result = $nc_team->getCollectList($ipt_list, $base); 
		api_result(0, '获取成功', $result);

	}
 	// 添加收藏
	public function addcollect(){ 
        $base = api_check_base();  
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true); 
		$validate_cfg = array( 
			'goods_id' => array(
				'api_v_numeric|1||商品不合法',
			),
			 
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		$time=time(); 
		$nc_list = Factory::getMod('nc_list');   
		$nc_list->setDbConf('team', 'collect');

		$where=array('goods_id'=>$ipt_list['goods_id'],'uid'=>$login_user['uid']);
	    $collect=$nc_list->getDataOne($where,array('uid'),array(),array(),false); 
	    if($collect){
	    	api_result(1, '你已经收藏过当前商品!');
	    }

        $ret=$nc_list->insertData(array(
            'uid' => $login_user['uid'], 
            'goods_id' => $ipt_list['goods_id'],
            'ut' => $time,
        ));
        if($ret){
        api_result(0, '收藏成功', $ret);
    	}
    	api_result(1, '收藏失败!');

	}
	public function canclecollect(){ 
        $base = api_check_base();  
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true); 
		$validate_cfg = array( 
			'goods_id' => array(
				'api_v_numeric|1||商品不合法',
			),
			 
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg); 
		$nc_list = Factory::getMod('nc_list');   
		$nc_list->setDbConf('team', 'collect');  
        $sql = "delete from {$nc_list->dbConf['tbl']} where uid={$login_user['uid']}  and `goods_id`={$ipt_list['goods_id']}";
        $nc_list->executeSql($sql);  
       
        api_result(0, '取消收藏成功', $result);

	}
	 //拼团订单
	public function order(){ 
		//标准参数检查
		$base = api_check_base(); 
		$validate_cfg = array(  
			'status' => array(),
            'from' => array(),
            'count' => array(),

		); 
		$ipt_list = api_get_posts($base['appid'], $validate_cfg); 
		$order_mod = Factory::getMod('nc_team');
		$result = $order_mod->order($base, $ipt_list); 
		api_result(0, '获取成功', $result);


	}
 	public function orderstatus(){ 
 		$base = api_check_base(); 
 		$login_user = app_get_login_user($base['sessid'], $base['appid'], true); 

		$validate_cfg = array(  
			'order_num' => array('api_v_notnull||订单号不能为空'), 
            'msg' => array(), 
            'status'=>array('api_v_notnull||状态id不合法'),
		);  
		$ipt_list = api_get_posts($base['appid'], $validate_cfg); 
		$nc_list = Factory::getMod('nc_list');  
		$nc_list->setDbConf('shop', 'order'); 
		if($ipt_list['status']==-2){
			 $sql = "update   {$nc_list->dbConf['tbl']} set status=-2 where order_num={$ipt_list['order_num']} and flag=0 and uid={$login_user['uid']};";
		}else if($ipt_list['status']==5){
			 $sql = "update  {$nc_list->dbConf['tbl']} set status=5 where order_num={$ipt_list['order_num']} and status=4 and flag=1 and uid={$login_user['uid']};";
		}else{
			api_result(1, '非法操作');
		}
		  
        
         
        $ret=$nc_list->executeSql($sql); 
        if($ret){
            api_result(0, '操作成功');
        }else{
            api_result(1, '操作失败');
        } 

 	}
    /**
     * 修改收货地址
     */
    public function editaddress(){
        // 调用测试用例 
        $base = api_check_base(); 
        // 得到当前登录用户
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        
        $validate_cfg = array(
            'address_id' => array(
                'api_v_numeric|1||address_id不合法',
            ), 
           'order_num' => array('api_v_notnull||订单号不能为空'), 

        );
        $ipt_list = api_get_posts($base['appid'], $validate_cfg);
        
        /*$pub_mod = Factory::getMod('pub'); 
        
        $pub_mod->init('shop', 'logistics', 'logistics_id');
        //查看用户是否中了这一期
        $where = array(
            
            'uid' => $login_user['uid'],
            'logistics_id' => $ipt_list['logistics_id']
        );
        $ret = $pub_mod->getRowWhere($where);
        if(empty($ret)){
            api_result(9, '没有权限');
        }*/

        $nc_list = Factory::getMod('nc_list');   
	    $nc_list->setDbConf('shop', 'order');   
	    $where=array('order_num'=>$ipt_list['order_num'],'uid'=>$login_user['uid']);
	    $order=$nc_list->getDataOne($where,array('uid'),array(),array(),false); 
	    if($order){
	    	$data['address_id']=$ipt_list['address_id'];
	    	$ret=$nc_list->updateData($where,$data); 
	    }else{
	    	api_result(9, '没有权限');
	    }

	     
        //获取地址
        $pub_mod = Factory::getMod('pub'); 
        $pub_mod->init('main', 'address', 'address_id');
        $addressInfo = $pub_mod->getRow($ipt_list['address_id']);
        $address = urlencode($addressInfo['province'].$addressInfo['city'].$addressInfo['area'].$addressInfo['detail']).':'.urlencode($addressInfo['name']).':'.$addressInfo['mobile'];

        $nc_list->setDbConf('shop', 'logistics'); 
        $data['address_id']=$ipt_list['address_id'];
	    $logisticsInfo=$nc_list->getDataOne($where,array('logistics_stat'),array(),array(),false); 
	    if($logisticsInfo){
	    	if($logisticsInfo['logistics_stat']>0){
	    		api_result(1, '当前状态不支持更改地址');
	    	}
	    	$ret=$nc_list->updateData($where,array('address' => $address)); 
	    }
	    api_result(0, '填写成功', array('address' => $address));//默认填写成功！
	/*    if($ret){
            api_result(0, '填写成功', array('address' => $address));
        }else{
            api_result(1, '填写失败');
        }*/
        //查看该期是否已经被填写
       /* $pub_mod->init('shop', 'logistics', 'logistics_id');
        $where = array(
           
            'logistics_id' => $ipt_list['logistics_id']
        );
        $logisticsInfo = $pub_mod->getRowWhere($where);
        if(!empty($logisticsInfo)){
            $flag = $logisticsInfo['logistics_id'];
        }
        if($logisticsInfo['logistics_stat']>0){
            api_result(1, '当前状态不支持更改地址');
        }
        //填写收货地址
        $time = time();
        if($flag){
            $data = array(
                'address' => $address,
                'ut' => $time,
            );
            $ret = $pub_mod->updateRow($flag,$data);
        } 
        if($ret){
            api_result(0, '填写成功', array('address' => $address));
        }else{
            api_result(1, '填写失败');
        }*/
    }
    //获取订单信息,用于判断用户需要微信支付
    // 拼团订单付款时候可能需要更改金额
    public function getpayorder(){
    	 
    	$base = api_check_base(); 
        // 得到当前登录用户
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true); 
        $validate_cfg = array( 
           'order_num' => array('api_v_notnull||获取订单信息失败'),  
        );
        $ipt_list = api_get_posts($base['appid'], $validate_cfg);
        $nc_list = Factory::getMod('nc_list');   
	    $nc_list->setDbConf('shop', 'order');
	    $where=array('order_num'=>$ipt_list['order_num'],'uid'=>$login_user['uid']);
	    $ret = $nc_list->getDataOne($where, array(), array(), array(), false);  
	    if(!$ret){
	    	api_result(1, '付款失败,查找不到订单信息！');
	    } 
	    $money_info=json_decode($ret['money_info'],true); 
	    $userMoney = bcadd($login_user['money'],$login_user['yongjin'],2);
	   if($money_info['remain_use']>$userMoney){ //现有余额不足以支付 金额变成全部由第三方微信支付
	  		$data['money_info']=json_encode(array('remain_use'=>0,'need_money'=>bcadd($money_info['remain_use'],$money_info['need_money'],2)));
		    $nc_list->updateData($where, $data);  //need_money=0;
		    $paytype['paytype']=2;//第三方微信支付
	   }else if($money_info['need_money']>0){
	   	    $paytype['paytype']=2;//第三方微信支付
	   }else{
	   		$paytype['paytype']=1;//余额支付
	   } 
	   api_result(0, '获取成功',$paytype);


    }
  			 
}