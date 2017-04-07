<?php
/**
 * @since 2016-01-06
 */
class NcUserShowMod extends BaseMod{
	
	private $currentTime;
	
	/**
	 * 获取晒单列表
	 */
	public function getShareList($ipt_list, $base){
		$nc_list_mod = Factory::getMod('nc_list');
		$this->currentTime = time();
		//where条件
		$where = array(
			'appid' => $base['appid'],
			'stat' => 2
		);
		if(!empty($ipt_list['goods_id'])){
			$where['goods_id'] = $ipt_list['goods_id'];
		}
		if(!empty($ipt_list['uid'])){
			$where['uid'] = $ipt_list['uid'];
		}
		//获取晒单列表
		$limit = array(
			'begin' => $ipt_list['from'],
			'length' => $ipt_list['count']
		);
		$order = array(
			'show_id' => 'desc'
		);
		$column = array(
			'goods_id','activity_id','uid','show_title','show_desc','ut','img'
		);
		
		$nc_list_mod->setDbConf('shop', 'show');
		$showInfo = $nc_list_mod->getDataList($where, $column, $order, $limit);
		$result = $user_key = $goods_key = array();
		foreach($showInfo as $val){
			$key = $val['uid'].'_'.$val['activity_id'];
			$user_key[$val['uid']][] = $key;
			$goods_key[$val['goods_id']][] = $key;
			$result[$key] = array(
				'uid' => intval($val['uid']),
				'show_title' => $val['show_title'],
				'activity_id' => intval($val['activity_id']),
				'show_desc' => $val['show_desc'],
				'show_imgs' => explode(',', $val['img']),
				'show_time' => date_friendly($val['ut'])
			);
		}
		
		//获取用户信息
		$where = array(
			'appid' => $base['appid'],
			'uid' => array(
				array_keys($user_key),'in'
			),
		);
		$column = array(
			'uid','icon','nick'
		);
		
		$nc_list_mod->setDbConf('main', 'user');
		$userInfo = $nc_list_mod->getDataList($where, $column);
		foreach($userInfo as $val){
			$icon_info = ap_user_icon_url($val['icon']);
			foreach($user_key[$val['uid']] as $key){
				$result[$key]['uicon'] = $icon_info['icon'];
				$result[$key]['unick'] = $val['nick'];
			}
		}
		
		//获取商品信息
		$where = array(
			'appid' => $base['appid'],
			'goods_id' => array(
				array_keys($goods_key),'in'
			),
		);
		$column = array(
			'goods_id','title','sub_title'
		);
		$nc_list_mod->setDbConf('shop', 'goods');
		$goodsInfo = $nc_list_mod->getDataList($where, $column);
		foreach($goodsInfo as $val){
			foreach($goods_key[$val['goods_id']] as $key){
				$result[$key]['goods_title'] = $val['title'];
				$result[$key]['goods_subtitle'] = $val['sub_title'];
			}
		}
		return $nc_list_mod->toArray($result);
	}
	
	/**
	 * 分享晒单
	 */
	public function doShare($ipt_list, $base){
		$nc_list_mod = Factory::getMod('nc_list');
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		//查看这一期用户是否中奖了
		$where = array(
			'appid' => $base['appid'],
			'activity_id' => $ipt_list['activity_id'],
			'uid' => $login_user['uid']
		);
		$column = array(
			'goods_id'
		);
		$nc_list_mod->setDbConf('shop', 'lucky_num');
		$goodsInfo = $nc_list_mod->getDataList($where, $column, array(), array(), false);
		if(empty($goodsInfo)){
			api_result(1, '只有中奖用户才能分享晒单');
		}
		$goods_id = $goodsInfo[0]['goods_id'];
		//查看用户是否已经分享
		$where = array(
			'appid' => $base['appid'],
			'goods_id' => $goods_id,
			'activity_id' => $ipt_list['activity_id'],
		);
		$column = array(
			'show_id'
		);
		$nc_list_mod->setDbConf('shop', 'show');
		$showInfo = $nc_list_mod->getDataList($where, $column, array(), array(), false);
		if(!empty($showInfo)){
			api_result(1, '一期活动只能分享一次');
		}
		//执行分享
		$time = time();
		$data = array(
			'appid' => $base['appid'],
			'goods_id' => $goods_id,
			'activity_id' => $ipt_list['activity_id'],
			'uid' => $login_user['uid'],
			'show_title' => $ipt_list['show_title'],
			'show_desc' => $ipt_list['show_desc'],
			'img' => $ipt_list['img'],
			'stat' => 0,
			'rt' => $time,
			'ut' => $time
		);
		$ret = $nc_list_mod->insertData($data);
		return $ret;
	}

    public function zan($show_id,$uid){
        $nc_list_mod = Factory::getMod('nc_list');
        $nc_list_mod->setDbConf('shop', 'show');
        $where = array(
            'show_id' => $show_id,
        );
        $showInfo = $nc_list_mod->getDataList($where, array(), array(), array(), false);
        if(empty($showInfo)){
            api_result(1, '不存在该晒单');
        }
        $where = array(
            'uid' => $uid,
            'show_id' => $show_id,
        );
        $nc_list_mod->setDbConf('shop', 'show_zan');
        $showInfo = $nc_list_mod->getDataList($where, array(), array(), array(), false);


        if($showInfo){
            api_result(1, '你已经赞过了');
        }
        $data = array(
            'uid' => $uid,
            'show_id' => $show_id,
            'ut' => time(),
        );
        $ret = $nc_list_mod->insertData($data);
        $nc_list_mod->setDbConf('shop', 'show');
        $sql = "update {$nc_list_mod->dbConf['tbl']} set `zans`=`zans`+1 where `show_id`={$show_id}";
        $nc_list_mod->executeSql($sql);
        return $ret;
    }

    public function comment($ip_list,$uid){
        $nc_list_mod = Factory::getMod('nc_list');
        $nc_list_mod->setDbConf('shop', 'show');
        $where = array(
            'show_id' => $ip_list['show_id'],
        );
        $showInfo = $nc_list_mod->getDataList($where, array(), array(), array(), false);
        if(empty($showInfo)){
            api_result(1, '不存在该晒单');
        }

        $nc_list_mod->setDbConf('shop', 'show_comment');
        $data = array(
            'uid' => $uid,
            'show_id' => $ip_list['show_id'],
            'text' => $ip_list['text'],
            'comment_uid' => $ip_list['comment_uid'],
            'rt' => time(),
            'stat' => 2,
        );
        $ret = $nc_list_mod->insertData($data);
        $nc_list_mod->setDbConf('shop', 'show');
        $sql = "update {$nc_list_mod->dbConf['tbl']} set `comments`=`comments`+1 where `show_id`={$ip_list['show_id']}";
        $nc_list_mod->executeSql($sql);
        return $ret;

    }

    public function commentList($ip_list){
        $nc_list_mod = Factory::getMod('nc_list');
        $nc_list_mod->setDbConf('shop', 'show_comment');
        $where = array(
            'show_id' => $ip_list['show_id'],
            'stat' => 2,
        );
        $page = $ip_list['page']<1 ? 1 : (int)$ip_list['page'];
        $limit = array(
            'begin' => ($page-1)*20,
            'length' => 20,
        );
        $order = array(
            'rt' => 'asc'
        );
        $showInfo = $nc_list_mod->getDataList($where, array(), $order,$limit, true);
        $data = array();
        $uid = array();
        foreach($showInfo as $v){
            $uid[] = $v['uid'];
            if($v['comment_uid']>0){
                $uid[] = $v['comment_uid'];
            }
        }
        $uid = array_unique($uid);
        $where = array(
            'uid' => array($uid,'in'),
        );
        $nc_list_mod->setDbConf('main', 'user');
        $userInfo = $nc_list_mod->getDataList($where, array('uid','nick','icon'), array(),array(), false);
        foreach($userInfo as $d){
            $data[$d['uid']] = $d;
        }
        $dd = array();
        foreach($showInfo as $v){
            $icon_info = ap_user_icon_url($data[$v['uid']]['icon']);
            $dd[] = array(
                'nick' => $data[$v['uid']]?$data[$v['uid']]['nick']:$v['uid'],
                'icon' => $icon_info['icon'],
                'comment_nick' => $data[$v['comment_uid']]['nick']?$data[$v['comment_uid']]['nick']:'',
                'text' => $v['text'],
                'uid' => $v['uid'],
                'comment_uid' => $v['comment_uid'],
            );
        }
        return $dd;
    }

    public function shareList($ip_list,$uid){
        $nc_list_mod = Factory::getMod('nc_list');
        $nc_list_mod->setDbConf('shop', 'show');
        $where = array(
            'stat' => 2,
        );
        if($ip_list['goods_id']){
            $where['goods_id'] = $ip_list['goods_id'];
        }
        if($ip_list['my'] && $uid){
            $where['uid'] = $uid;
        }
        if($ip_list['uid']+0>0){
            $where['uid']=$ip_list['uid'];
        }
        $page = $ip_list['page']<1 ? 1 : (int)$ip_list['page'];
        $pageCount=$ip_list['pageCount']?$ip_list['pageCount']:10;
        $limit = array(
            'begin' => ($page-1)*$pageCount,
            'length' => $pageCount,
        );
        if($ip_list['type']=='comment'){
            $order = array(
                'comments' => 'desc',
                'rt' => 'desc',
            );
        }elseif($ip_list['type']=='time'){
            $order = array(
                'rt' => 'desc',
            );
        }else{
            $order = array(
                'zans' => 'desc',
                'rt' => 'desc',
            );
        }
        $limit['begin']=$limit['begin']>1?$limit['begin']+1:$limit['begin'];//兼容-1bug
        $shareList = $nc_list_mod->getDataList($where, array(), $order,$limit, true);
         $join['from']=$nc_list_mod->dbConf['tbl'];

         $count = $nc_list_mod->getJoincount($join, $where, 'activity_id'); 

        $result = $user_key = $goods_key = $id = $show = array();
        foreach($shareList as $val){
            $id[] = $val['show_id'];
            $key = $val['uid'].'_'.$val['activity_id'];
            $user_key[$val['uid']][] = $key;
            $goods_key[$val['goods_id']][] = $key;
            $show[$val['show_id']] = $key;
            $result[$key] = array(
                'uid' => intval($val['uid']),
                'show_title' => $val['show_title'],
                'activity_id' => intval($val['activity_id']),
                'show_desc' => $val['show_desc'],
                'show_imgs' => explode(',', $val['img']),
                'show_time' => date('Y-m-d',($val['rt'])),
                'zans' => $val['zans'],
                'comments' => $val['comments'],
                'show_id' => $val['show_id'],
            );
        }

        //获取用户信息
        $where = array(
            'uid' => array(
                array_keys($user_key),'in'
            ),
        );
        $column = array(
            'uid','icon','nick'
        );

        $nc_list_mod->setDbConf('main', 'user');
        $userInfo = $nc_list_mod->getDataList($where, $column);
        foreach($userInfo as $val){
            $icon_info = ap_user_icon_url($val['icon']);
            foreach($user_key[$val['uid']] as $key){
                $result[$key]['uicon'] = $icon_info['icon'];
                $result[$key]['unick'] = $val['nick'];
            }
        }

        //获取商品信息
        $where = array(
            'goods_id' => array(
                array_keys($goods_key),'in'
            ),
        );
        $column = array(
            'goods_id','title','sub_title'
        );
        $nc_list_mod->setDbConf('shop', 'goods');
        $goodsInfo = $nc_list_mod->getDataList($where, $column);
        foreach($goodsInfo as $val){
            foreach($goods_key[$val['goods_id']] as $key){
                $result[$key]['goods_title'] = $val['title'];
                $result[$key]['goods_subtitle'] = $val['sub_title'];
            }
        }
        //检查是否点赞过
        if($uid){
            $where = array(
                'uid' => $uid,
                'show_id' => array(
                    $id,'in'
                ),
            );
            $nc_list_mod->setDbConf('shop', 'show_zan');
            $zanList = $nc_list_mod->getDataList($where,array(),array(),array(),false);
            foreach($zanList as $_v){
                foreach($show as $show_ID=>$key){
                    if($_v['show_id']==$show_ID){
                        $result[$key]['is_zan'] = 1;
                    }
                }
            }
        }


        $resultArr=$nc_list_mod->toArray($result);
        $resultArr['count']=$count; 
        return $resultArr;
         
    }

    public function shareInfo($id){
        $nc_list_mod = Factory::getMod('nc_list');
        $nc_list_mod->setDbConf('shop', 'show');
        $where = array(
            'stat' => 2,
            'show_id' => $id,
        );

        $shareList = $nc_list_mod->getDataList($where, array(), array(),array(), true);
        $result = $user_key = $goods_key = $id = $show = array();
        foreach($shareList as $val){
            $id[] = $val['show_id'];
            $key = $val['uid'].'_'.$val['activity_id'];
            $user_key[$val['uid']][] = $key;
            $goods_key[$val['goods_id']][] = $key;
            $show[$val['show_id']] = $key;
            $result[$key] = array(
                'uid' => intval($val['uid']),
                'show_title' => $val['show_title'],
                'activity_id' => intval($val['activity_id']),
                'show_desc' => $val['show_desc'],
                'show_imgs' => explode(',', $val['img']),
                'show_time' => date_friendly($val['rt']),
                'zans' => $val['zans'],
                'comments' => $val['comments'],
                'show_id' => $val['show_id'],
            );
        }

        //获取用户信息
        $where = array(
            'uid' => array(
                array_keys($user_key),'in'
            ),
        );
        $column = array(
            'uid','icon','nick'
        );

        $nc_list_mod->setDbConf('main', 'user');
        $userInfo = $nc_list_mod->getDataList($where, $column);
        foreach($userInfo as $val){
            $icon_info = ap_user_icon_url($val['icon']);
            foreach($user_key[$val['uid']] as $key){
                $result[$key]['uicon'] = $icon_info['icon'];
                $result[$key]['unick'] = $val['nick'];
            }
        }

        //获取商品信息
        $where = array(
            'goods_id' => array(
                array_keys($goods_key),'in'
            ),
        );
        $column = array(
            'goods_id','title','sub_title'
        );
        $nc_list_mod->setDbConf('shop', 'goods');
        $goodsInfo = $nc_list_mod->getDataList($where, $column);
        foreach($goodsInfo as $val){
            foreach($goods_key[$val['goods_id']] as $key){
                $result[$key]['goods_title'] = $val['title'];
                $result[$key]['goods_subtitle'] = $val['sub_title'];
            }
        }

        return $nc_list_mod->toArray($result);
    }
    public function luckyPacketList($base, $ipt_list){
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);   
        $limit = array(
            'from' =>  $ipt_list['from'] <=0 ? 1 : (int)$ipt_list['from'] ,
            'count' => $ipt_list['count']>30?30:(int)$ipt_list['count']
        ); 
        $limit['count']=$limit['count']<1?10:$limit['count']; 
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('main', 'packet_msg');
        $limit =  " limit ".($limit['from']-1).",{$limit['count']}"  ; 
 
       
        $and .=" and uid=".$login_user['uid'];  
        $table =  DATABASE." .t_packet_msg  ";

        $sql = "select  * from {$table} where 1 {$and}  order by `id` desc {$limit}"; 
     
        $resultArr = $nc_list->getDataBySql($sql);   
        if($resultArr){
            foreach($resultArr as $k=> $v){  

                $resultArr[$k]['content']= json_decode($v['content'],true); 
                $resultArr[$k]['rt']= date('Y-m-d H:i:s');  
            }

        }
         
        $count = $nc_list->getDataBySql("select count(*) as total from {$table} where 1 {$and}");  
         
        $resultArr['count']=$count[0]['total']; 
        return $resultArr;   


    }

    public function getlineChart($base,$ipt_list){
       // $ipt_list['year']=2016;
       // $ipt_list['month']=9;
        $ipt_list['year']=$ipt_list['year']?$ipt_list['year']:date('Y');
        $ipt_list['month']=$ipt_list['month']?$ipt_list['month']:date('m');   
        $first=strtotime(date($ipt_list['year']."-".$ipt_list['month']."-01"));
        $firsttime=date('Y-m-d', $first); 
        $last=  strtotime("$firsttime +1 month  ")-1;  
 
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);   
        $nc_list=Factory::getMod('nc_list');
        $nc_list->setDbConf('shop','money');
        $msginfo=array();
         for($i=1;$i<=31;$i++){ //初始化数组
            if($i % 2!=0){
                $msginfo[1][$i]=0;
                $msginfo[2][$i]=0;
                $msginfo[3][$i]=0;
            }
        }
        
        if($ipt_list['type']==2){ //获取邀请注册人数 3次去查询或者用递归解决
            //$login_user['uid']=23;
            $sql="select rt ut,nick,uid,type from ".DATABASE.".t_user where rebate_uid={$login_user['uid']}  "; 
            $inver_user[1]  = $nc_list->getDataBySql($sql); //找3级 
            foreach($inver_user[1] as $v){
                $lev2[]=$v['uid'];
            } 
            $sql="select rt ut,nick,uid,type from ".DATABASE.".t_user where rebate_uid in (".implode($lev2,',').")  ";  
            $inver_user[2] = $nc_list->getDataBySql($sql,false);
            foreach( $inver_user[2] as $v){
                $lev3[]=$v['uid'];
            } 
            $sql="select rt ut,nick,uid,type from ".DATABASE.".t_user where rebate_uid in (".implode($lev3,',').")  ";   
            $inver_user[3] = $nc_list->getDataBySql($sql,false);
          /*  echo $first,'</br>', $last;
           echo date('Y-m-d', $first); 
           echo date('Y-m-d', $last),'</br>'; 
            */
            
            foreach( $inver_user as $k=>$v){
                foreach($v as $c=>$b){ 
                     if($b['ut']>$first &&  $b['ut'] <$last){  
                      //  echo $k,'</br>';
                         $day=date('d',$b['ut'])+0;   
                         if($day % 2==0){  
                            $msginfo[$k][$day-1]+=1;
                        }else{
                            $msginfo[$k][$day]+=1;
                        }  
                        $msg[]=$b;
                     }
                }
            } 
           
        }else{ 
            $where=" and type=1 and uid={$login_user['uid']} and ut > $first and ut <$last";
            $sql="select ut,lev,money  from {$nc_list->dbConf['tbl']} where 1   $where and lev > 0 and money!=0 limit 300";
            $msg = $nc_list->getDataBySql($sql,false);   
            foreach ($msg as $k=>$v){
                $lev=$v['lev']; 
                $day=date('d',$v['ut'])+0; 
                if($day % 2==0){
                    $msginfo[$lev][$day-1]+=$v['money'];
                }else{
                    $msginfo[$lev][$day]+=$v['money'];
                }   
            } 
        
        }
        foreach($msginfo as  $k=> $v){  
            foreach($v as $c=> $b){
                $msginfo[4][$c]+=$b; 
            }
        }
       
        $restinfo['one']=array_values($msginfo[1]);
        $restinfo['two']=array_values($msginfo[2]);
        $restinfo['three']=array_values($msginfo[3]);
        $restinfo['all']=array_values($msginfo[4]);
        $restinfo['datelist']= $msg;
        api_result(0, '获取成功', $restinfo);
       
        
         

    }
    public function getuserfollow($base,$ipt_list){
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);  
        $nc_list = Factory::getMod('nc_list');
        $time=time();
        
        if($ipt_list['goods_id']){
            foreach($ipt_list['goods_id'] as $k=> $v){  
                $nc_list->setDbConf('main', 'follow_goods');
                $sql = "insert into ".DATABASE.".t_follow_goods( `uid`,`goods_id`,  `ut`)
                    value(  {$login_user['uid']},{$v},
                     {$time}) on duplicate key update 
                    ut={$time}"; 
                
                $nc_list->executeSql($sql); 
                if($k>=15){
                    break;//每次只可以插15条;
                }
            }
        } 
       $sql = "select  g.goods_id,g.title,g.main_img,g.price from   ".DATABASE.".t_follow_goods f left join ".DATABASE.".t_team_goods g on f.goods_id=g.goods_id where uid={$login_user['uid']}  order by f.`ut` desc  ";   
       $resultArr = $nc_list->getDataBySql($sql); 
       $limit=count($resultArr)-15;
       if($limit>0){
            $sql="delete from ".DATABASE.".t_follow_goods where uid={$login_user['uid']} order by ut desc limit $limit";
            $nc_list->executeSql($sql); 
       }
      return  $resultArr;
         



    }

  

   
}