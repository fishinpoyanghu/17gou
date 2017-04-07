<?php
/**
 * @since 2016-01-06
 * note 晒单相关
 */
class NcUserShowCtrl extends BaseCtrl{
	

	
	/**
	 * 晒单分享
	 */
//	public function shareList(){
//		/*------测试环境下使用------*/
//		//$this->test_post_data(array('goods_id' => 3, 'from' => 1, 'count' => 10));
//
//		//标准参数检查
//		$base = api_check_base();
//		$validate_cfg = array(
//			'goods_id' => array(),
//			'uid' => array(),
//			'from' => array(),
//			'count' => array(),
//		);
//
//		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
//
//		$nc_user_show_mod = Factory::getMod('nc_user_show');
//		$result = $nc_user_show_mod->getShareList($ipt_list, $base);
//
//		api_result(0, '获取成功', $result);
//	}
	
	/**
	 * 分享晒单
	 */
	public function doShare(){
		/*------测试环境下使用------*/
		/* $this->test_post_data(array(
				'activity_id' => 3, 
				'show_title' => '啦啦啦',
				'show_desc' => '哈哈哈哈',
		)); */
		//标准参数检查
		$base = api_check_base();
		$validate_cfg = array(
			'activity_id' => array(
				'api_v_numeric|1||activity_id不合法'
			),
			'show_title' => array(
				'api_v_notnull||标题不能为空'
			),
			'show_desc' => array(
				'api_v_notnull||内容不能为空'
			),
			'img' => array(
				'api_v_notnull||图片地址不能为空'
			),
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		$ipt_list['show_title'] = api_safe_ipt($ipt_list['show_title']);
		$ipt_list['show_desc'] = api_safe_ipt($ipt_list['show_desc']);
		$ipt_list['img'] = api_safe_ipt($ipt_list['img']);
		//参数处理
		$img = explode(',', $ipt_list['img']);
		if(count($img) > 6){
			api_result(1, '图片数不能大于6');
		}
		if(mb_strlen($ipt_list['show_title'],'utf8') > 20){
			api_result(1, '标题不大于20个字');
		}
		if(mb_strlen($ipt_list['show_desc'],'utf8') > 140){
			api_result(1, '正文不大于140个字');
		}
		
		$nc_record_mod = Factory::getMod('nc_user_show');
		$ret = $nc_record_mod->doShare($ipt_list, $base);
		
		if($ret){
            
            api_result(0, '分享成功');
		}else{
			api_result(1, '分享失败');
		}
	}

    public function zan(){
        $base = api_check_base();
        $show_id = pint('show_id');
        if(!$show_id) api_result(1, 'show_id不能为空');
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $nc_record_mod = Factory::getMod('nc_user_show');
        $ret = $nc_record_mod->zan($show_id,$login_user['uid']);
        if($ret){
            api_result(0, '成功');
        }else{
            api_result(1, '失败');
        }
    }

    public function comment(){
        $base = api_check_base();
        $validate_cfg = array(
            'show_id' => array(
                'api_v_notnull||show_id不能为空'
            ),
            'text' => array(
                'api_v_notnull||内容不能为空'
            ),
            'comment_uid' => array(

            ),
        );
        $ipt_list = api_get_posts($base['appid'], $validate_cfg);
        $ipt_list['show_id'] = api_safe_ipt($ipt_list['show_id']);
        $ipt_list['text'] = api_safe_ipt($ipt_list['text']);
        $ipt_list['comment_uid'] = api_safe_ipt($ipt_list['comment_uid']);
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $nc_record_mod = Factory::getMod('nc_user_show');
        $ret = $nc_record_mod->comment($ipt_list,$login_user['uid']);
        if($ret){
            api_result(0, '成功');
        }else{
            api_result(1, '失败');
        }
    }

    public function commentList(){
        $base = api_check_base();
        $validate_cfg = array(
            'show_id' => array(
                'api_v_notnull||show_id不能为空'
            ),
            'page' => array(
            ),
        );
        $ipt_list = api_get_posts($base['appid'], $validate_cfg);
        $nc_record_mod = Factory::getMod('nc_user_show');
        $ret = $nc_record_mod->commentList($ipt_list);
        api_result(0, '成功',$ret);

    }

    public function shareList(){
        $base = api_check_base();
        $validate_cfg = array(
            'type' => array(
            ),
            'page' => array(
            ),
            'my' => array(),
            'goods_id'=>array(
            ),
            'pageCount'=>array(),
            'uid'=>array(),
        );
        $ipt_list = api_get_posts($base['appid'], $validate_cfg);
        $login_user = app_get_login_user($base['sessid'], $base['appid'], false);

        $nc_record_mod = Factory::getMod('nc_user_show');
        $ret = $nc_record_mod->shareList($ipt_list,$login_user['uid']);
        api_result(0, '成功',$ret);
    }

    public function shareInfo(){
        $base = api_check_base();
        $validate_cfg = array(
            'show_id' => array(
            ),
        );
        $ipt_list = api_get_posts($base['appid'], $validate_cfg);

        $nc_record_mod = Factory::getMod('nc_user_show');
        $ret = $nc_record_mod->shareInfo($ipt_list['show_id']);
        api_result(0, '成功',$ret);
    }

    public function disk(){
        $base = api_check_base();
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        $yesterday2= strtotime(date("Y-m-d"))-1; //晚上
        $yesterday1=$yesterday2-3600*24+1; //早上  
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('main', 'disk');  
        $sql = "select count(`id`) total from {$nc_list->dbConf['tbl']} where `uid`={$login_user['uid']}  ";
        $diskmoney = $nc_list->getDataBySql($sql,false); //等待金盘资金
       
        if(!$diskmoney[0]['total']){  //还没产生云购订单
            $ret['orderstatus']=0;
             api_result(0, '成功',$ret);
        }

        $ret['orderstatus']=1;
        $ret['diskmoney']=$diskmoney['0']['total']; //等待进盘金额
        //昨天推出列数量

        $sql = "select count(id) count from {$nc_list->dbConf['tbl']} where  status=1 and rt between $yesterday1 and $yesterday2 ";
        $count = $nc_list->getDataBySql($sql,false); 
        $sql= "select count('id') count from   {$nc_list->dbConf['tbl']}  where id <=  ( select id  from {$nc_list->dbConf['tbl']} where `uid`={$login_user['uid']} order by id desc  limit 1 )  ";
        //待推列数量 
        $holdqueen = $nc_list->getDataBySql($sql,false);     
        $days=intval($holdqueen[0]['count']*10/$count[0]['count']);  //待推数量除以昨天推出数量=预计天数
        $days=$count[0]['count']==0? $holdqueen[0]['count']*10:$days; 
       
        $ret['pushday']=$days==0?7:$days; //$count[0]['count'] 为0就默认7天 预计全额返回天数
       /* $sql = "select  id from  ".DATABASE.".t_outdisk where `uid`={$login_user['uid']} limit 1 ";
        $outmoney = $nc_list->getDataBySql($sql,false); //查询是否成为受益者*/
     
        $sql = "select count('id') count,status from   {$nc_list->dbConf['tbl']}  where id <=  ( select id  from {$nc_list->dbConf['tbl']} where `uid`={$login_user['uid']} limit 1 )";    
        $queuenum = $nc_list->getDataBySql($sql,false); //还未成为受益者
        $ret['peoplenum']=0;
        $ret['percent']=0;

        if($queuenum[0]['count']){
              $ret['queuecount']=$queuenum[0]['count'];
             // $ret['queuenum']=$queuenum[0]['count']*10; 
               if($queuenum[0]['count']==1){ //公盘队列第一。到了我是受益者
                    $sql = "select count('id') count from   {$nc_list->dbConf['tbl']}  where status=0 limit 1  ";  
                    $holdqueue=$nc_list->getDataBySql($sql,false); //未推的人 
                    $st=$queuenum[0]['status']?0:1;
                    $ret['percent']=($holdqueue[0]['count']-$st)%10;   //进度条 只有公盘第一个才有进度条显示。。就是公盘队列的第一位
                    $ret['queuenum']=10-($holdqueue[0]['count']-$st);
               }else{
                    $ret['peoplenum']=$queuenum[0]['count']-1; 
               }
        }  
        api_result(0, '成功',$ret); 
    }
     
   
    //最新订单获取 公盘
    public function recentOrder(){
        $base = api_check_base();
        $validate_cfg = array(
            'count' => array(
                 
            ),
             'from'=>array()
            
        );
       // $count=$ipt_list['count']<20 && $ipt_list['count']?$ipt_list['count']:2;
        $count=2; //需要机器人所以2条。
        $begin = empty($ipt_list['from']) ? 0 : $ipt_list['from']-1;

        $ipt_list = api_get_posts($base['appid'], $validate_cfg);
        
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'order');   
        $sql = "select order_id, u.uid,order_aid,money_info,o.rt,u.nick,u.icon  from   {$nc_list->dbConf['tbl']} o  left join ".DATABASE.".t_user u on u.uid=o.uid  where (goods_type=1 and flag >0 and order_aid > 0) || status=-8  order by order_id desc     limit $begin,$count ";   
          
        $ret = $nc_list->getDataBySql($sql,false); //还未成为受益者ap_user_icon_url
       
        foreach($ret as $v){
            $activityids[]=$v['order_aid'];
            $money=json_decode($v['money_info'],true);
            $userinfo[$v['order_aid']]['money']=$money['need_money']+$money['remain_use'];
            $userinfo[$v['order_aid']]['time']=date('Y-m-d H:i:s',$v['rt']);
            $userinfo[$v['order_aid']]['icon']=ap_user_icon_url($v['icon'])['icon'];
            $userinfo[$v['order_aid']]['nick']= $v['nick'] ;
            $userinfo[$v['order_aid']]['uid']= $v['uid'] ;
            $userinfo[$v['order_aid']]['order_id']= $v['order_id'] ;
        } 
    
        $ids=implode($activityids,','); 
        $sql = "select  a.activity_id,g.title, g.goods_id,g.main_img   from    ".DATABASE.".t_activity a left join   ".DATABASE.".t_goods g on g.goods_id=a.goods_id    where a.activity_id in ($ids)     ";   
        $msg = $nc_list->getDataBySql($sql,false); //还未成为受益者

        foreach($msg as $k=>$c){
            foreach($userinfo as $wk=>$wv){
                if($c['activity_id']==$wk){
                    $msg[$k]['money']=$wv['money'];
                    $msg[$k]['time']=$wv['time'];
                    $msg[$k]['icon']=$wv['icon'];
                    $msg[$k]['nick']=$wv['nick'];
                    $msg[$k]['uid']=$wv['uid'];
                    $msg[$k]['order_id']=$wv['order_id'];
                 }
                
            }
            /*$v['money']=$paymoney[$v['activity_id']];
            $v['time']=$rt[$v['activity_id']];*/
            
        }
         
        api_result(0, '成功',$msg); 
         
    }
    //福袋
    public function luckypacket(){
        //标准参数检查
        $base = api_check_base(); 
        $validate_cfg = array(   
            'from' => array(),
            'count' => array(),

        ); 
        $ipt_list = api_get_posts($base['appid'], $validate_cfg);  
        $nc_user_show = Factory::getMod('nc_user_show');
        $result = $nc_user_show->luckyPacketList($base,$ipt_list); 
        api_result(0, '获取成功', $result);

        
    /*     $team = Factory::getMod('team_order_task');
         $activityInfo=$team->getActivityInfo(12);
         var_dump($activityInfo); 
         $team =$team->addpacket($activityInfo);*/


        /*$msg_mod = Factory::getMod('msg');
        $content=json_encode(array('invite_nick'=> 'c','goods_name'=>'')); 
        $msg_mod->sendPacketNotify(23,1,1,$content,'','','',$login_user['uid']);*/


    }
    //个人注册曲线图
    public function getlineChart(){
        $base = api_check_base(); 
        $validate_cfg = array(   
            'type' => array(),
            'year'=>array(),
            'month'=>array(),            

        ); 
        $ipt_list = api_get_posts($base['appid'], $validate_cfg);  
        $nc_user_show = Factory::getMod('nc_user_show');
        $nc_user_show->getlineChart($base,$ipt_list); 
        



    }
    /**
    *我更喜欢
    **/
    public function getuserfollow(){
        //$this->test_post_data(array('goods_id' => array(3,5,6,7,8,2,3,4,5,8,9,11,12,154,1,16,171,345,346,456,457,66,77)));
        $base = api_check_base(); 
        $validate_cfg = array(   
            'goods_id' => array(),
            'type'=>array(),
               

        ); 
        $ipt_list = api_get_posts($base['appid'], $validate_cfg);  
        $nc_user_show = Factory::getMod('nc_user_show');
        $result =$nc_user_show->getuserfollow($base,$ipt_list);  
        api_result(0, '获取成功', $result);
    }

}