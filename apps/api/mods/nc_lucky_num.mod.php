<?php
/**
 * @since 2016-01-22
 */
class NcLuckyNumMod extends BaseMod{
    public function getlottery() { 
        $tokenFile = "/tmp/lottery.txt";//缓存文件名
        $data = json_decode(file_get_contents($tokenFile),true);
        date_default_timezone_set('Asia/Shanghai');
        //10:00-22:00  10分钟一期，22:00-02:00 5分钟一期
        if($data['num'] && $data['time'] && date('H:i',$data['time']) >'02:00' && date('H:i',$data['time']) <'10:00'){
            return $data['num']; //晚上2点到白天10点停止开彩
        } 
        $now=date('H:i',time());  
        $difference=0;
        if($now>'10:00' && $now <'22:00'){
            $difference=10;
        }else if($now>'22:00' || $now <'02:00'){
            $difference=5;
        }
          
        //echo date('H:i',$data['time']);exit;
        $timedifference=$difference*60;
        if ($data['time']+$timedifference < time() or !$data['num']) { 
            $res=$this->getapilottery(); //var_dump($res);exit;
            $num = $res['num'];  
            if($num) { 
                $fp = fopen($tokenFile, "w");
                fwrite($fp, json_encode($res));
                fclose($fp);
            }
        } else {
            $num = $data['num'];
        }
        
        return $num;
         
  }

  private function getapilottery(){   
    $host = "http://ali-lottery.showapi.com";
    $path = "/newest";
     //$path = "/multi";
    $method = "GET";
    $appcode = "0276ea16f7fe4644acccc6b851ef1e7a";
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    $querys = "code=cqssc";
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
    $code=json_decode($msg,true);
    $num = str_replace(',','',$code['showapi_res_body']['result']['0']['openCode']); 
    $data['num']=(int)$num;
    $data['time']=$code['showapi_res_body']['result']['0']['timestamp'];
    
    file_put_contents('/tmp/apilottery.log','lottery:'.var_export($data,true).date('Y-m-d H:i:s'),FILE_APPEND);
    return $data; 
        

    } 
	
	public function run(){  
		$nc_list = Factory::getMod('nc_list');
        while(1){ 
            sleep(8);
            $nc_list->setDbConf('shop', 'activity');
            $where = array(
                'flag' => 1
            );
            $column = array(
                'appid','activity_id','goods_id','need_num','publish_time'
            );
            $order = array(
                'publish_time' => 'asc'
            );
            $data = $nc_list->getDataList($where, $column, $order,array(),false);
           // var_dump($data);exit;
            //获取时彩号
         
             
 
            $currentTime = time();

            $msg_mod = Factory::getMod('msg');  
            if(empty($data))  continue; 
            foreach($data as $val){   
                //  echo date('Y-m-d H:i:s',$val['publish_time']); 
                if($val['publish_time']>=time()+5) continue;
                $activity_id = $val['activity_id'];
                $where = array(
                    'activity_id' => $activity_id
                );
                $column = array(
                //    'time_sum'
                );
                $nc_list->setDbConf('shop', 'lucky_num');//echo 123;
                $activity_msg = $nc_list->getDataOne($where, $column, array(), array(), false);  
                $allrecord=json_decode($activity_msg['user_record'],true); 
                /*$activity_msg['time_sum']=0;
                foreach($allrecord as $v){ 
                    $activity_msg['time_sum']=bcadd($activity_msg['time_sum'],$v['rt']);
                } */
              //  var_dump(json_decode($activity_msg['user_record'],true));exit;
                if(empty($activity_msg)) continue; 
                //  $activity_msg['time_sum']='1008187130992976325';//10002882
                // $activity_msg['time_sum']='1008187130992974699';//1256
               //  $activity_msg['time_sum']='1008187130992977699';
               // echo 'timesum:'.$activity_msg['time_sum'],'</br>'; exit;
                $lottery_num = $this->getLottery();
                $sum = bcadd($activity_msg['time_sum'],$lottery_num);  
                $changeNum = bcmod($sum,$val['need_num']); 
                //幸运号
                $luckyNum = bcadd($changeNum,10000001); 
                // var_dump( $activity_msg['time_sum']);exit;  
                //echo $luckyNum,'</br>'; exit;
                // $getluckyNum = '10001256';
                $getluckyNum=$this->setluck($data,$luckyNum); //获取幸运码
                 //echo $getluckyNum,'</br>'; exit;
                 if($getluckyNum && $getluckyNum!=$luckyNum){  
                    $changeluckyNum=bcsub($getluckyNum,10000001);  // echo $getluckyNum,'</br>'; 
                    $sumnum=bcsub($changeluckyNum,$changeNum);     // echo '差',$sumnum,'</br>'; 
                    $backnum= $this->dealuckylsum($sumnum,$activity_msg,$getluckyNum,$lottery_num,$val['need_num']); 
                   
                    if($backnum){   //echo 'true:123';
                        $luckyNum=$backnum;
                    }
                    
                 }
                 // var_dump($data);
               //  echo $luckyNum,'</br>';exit;
               
                
             
                $where = array(
                    'appid' => $val['appid'],
                    'activity_id' => $activity_id,
                    'activity_num' => $luckyNum
                );
                $column = array(
                    'uid'
                );
                $nc_list->setDbConf('shop', 'activity_num');
                //中奖玩家
                $userInfo = $nc_list->getDataOne($where, $column, array(), array(), false);   
                if(empty($userInfo)) continue;
                $where = array(
                    'appid' => $val['appid'],
                    'activity_id' => $activity_id,
                    'uid' => $userInfo['uid']
                );
                $column = array(
                    'user_num'
                );
                $nc_list->setDbConf('shop', 'activity_user');
                //中奖玩家本次参与次数
                $userNum = $nc_list->getDataOne($where, $column, array(), array(), false);//var_dump($userNum);exit;
                if(empty($userNum)) continue;
                $luckyNumInfo = array(
                    'lucky_num' => $luckyNum,
                    'lottery_num' => $lottery_num,
                    'uid' => $userInfo['uid'],
                    'user_num' => $userNum['user_num'],
                    'user_read' => 0,
                    'ut' => $currentTime,
                );
                $where = array(
                    'activity_id' => $activity_id
                );
                $nc_list->setDbConf('shop', 'lucky_num');
                $nc_list->updateData($where, $luckyNumInfo);
                $nc_list->setDbConf('shop', 'activity');
                $activityData = array(
                    'publish_time' => time(),
                    'flag' => 2,
                    'ut' => $currentTime
                );
                $nc_list->updateData($where, $activityData);
            
                 
                //通知用户中奖啦
                $msg_mod->sendNotify($val['appid'], $userInfo['uid'], 10002, 5, 0, 7, '恭喜您中奖啦。');
                $msg=array('activity_id'=>$activity_id,'goods_id'=>$val['goods_id'],'activity_type'=>3);
                $msg_mod->sendSystNotify(1,$msg);
                //更新活动状态
               //  echo 'end'; exit;
                 
            }
        }
    }

    //指定中奖。
    private function setluck($data,$luckyNum){ //获取幸运号码
        //return 10000318;
        //return '';  
        $nc_list = Factory::getMod('nc_list');
       

          
         $nc_list->setDbConf('shop', 'activity_user');  
          $sql  = "select  uid from   ".DATABASE.".t_activity_chosen   where activity_id={$data[0]['activity_id']}   limit 1  ";
         $user = $nc_list->getDataBySql($sql,false); 
          
        
         if($user){
            $sql  = "select  activity_num from   ".DATABASE.".t_activity_num   where activity_id={$data[0]['activity_id']}   and uid={$user[0]['uid']} limit 1  ";
             $num = $nc_list->getDataBySql($sql,false); 
             if($num){
                 return $num[0]['activity_num'];
             }

         }

        $where = array(
            'appid' =>10002,
            'activity_id' => $data[0]['activity_id'],
            'activity_num' => $luckyNum
        );
        $column = array(
            'uid'
        );
        $nc_list->setDbConf('shop', 'activity_num');
        //中奖玩家
        $userInfo = $nc_list->getDataOne($where, $column, array(), array(), false);  
        if(empty($userInfo)) {
            return false;
        };
        $sql  = "select  f.uid from ".DATABASE.".`t_false` f     WHERE  uid={$userInfo['uid']}  "; 
        $machine = $nc_list->getDataBySql($sql,false); 
        if($machine){ //机器人就直接返回。
            return false;
        }

          $sql  = "select  machine_switch from   ".DATABASE.".t_sysset     limit 1  ";
          $machine_switch = $nc_list->getDataBySql($sql,false); 
          if($machine_switch[0]['machine_switch']){ //机器人中奖
         
             $sql  = "select  t.uid from   ".DATABASE.".`t_activity_user` t inner join ".DATABASE.".`t_false` f  on t.uid=f.uid and f.stat=0 WHERE  activity_id={$data[0]['activity_id']}  "; 
             $machine = $nc_list->getDataBySql($sql,false); 
             if($machine['0']['uid']){

                $sql  = "select  activity_num from   ".DATABASE.".t_activity_num   where activity_id={$data[0]['activity_id']}   and uid={$machine['0']['uid']} limit 1  ";
                $num = $nc_list->getDataBySql($sql,false);  
                 return $num[0]['activity_num'];
             }
          } 
           
         return false; 
       
    }
   
    private function dealuckylsum($sumnum,$activity_msg,$getluckyNum,$lottery_num,$need_num){ //更改sum 的值 
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'lucky_num'); 
        $user_record=json_decode($activity_msg['user_record'],true);
        $all=count($user_record);  
        if($sumnum > 0){
             $sum=bcadd($user_record[0]['rt'],$sumnum);
        }else{  
             $sum=bcadd($user_record[$all-1]['rt'],$sumnum); 
        } 
        
      /*  $num=0;
        foreach($user_record as $v){
            $num=bcadd($num,$v['rt']);
        } 
        echo $num;exit;*/
        $ms=substr($sum,14);
        $currentTime=strtotime(substr($sum,0,14));  
        $nc_list->setDbConf('shop', 't_false'); 
        //$rand=mt_rand(1,25000);
        $rand=mt_rand(1,707);
        $sql  = "select f.uid from {$nc_list->dbConf['tbl']} f INNER JOIN ".DATABASE.".t_user u on u.uid=f.uid where f.stat=0 and f.id>{$rand} limit 1  ";
        $user = $nc_list->getDataBySql($sql,false); 
        if(!$user){
            return false;
        }
        $sql  = "select * from   ".DATABASE.".t_shua    limit 1  ";
        $goods = $nc_list->getDataBySql($sql,false);  
        
        $where=$goods[0]['goods']?' and g.goods_id in ('.$goods[0]['goods'].')':'';
        $sql = "select   g.goods_id,g.title goods_name,a.activity_id,a.need_num-a.user_num  lastnum from ".DATABASE.".t_activity a left join ".DATABASE.".t_goods g on a.goods_id=g.goods_id   where     a.`flag`=0 and g.activity_type=1   $where  having lastnum >0  order by `lastnum` desc,  process desc     limit 15";
         
        $goods = $nc_list->getDataBySql($sql,false); 
        if(!$goods){
           return false;
        }
        shuffle($goods);
        shuffle($goods);
        $true_goods = array_pop($goods);    
        $_uid=$user[0]['uid']; 
        $_buy_num=1;
        $nc_list->setDbConf('main', 'user');
        $_sql = "update {$nc_list->dbConf['tbl']} set `money`=`money`+$_buy_num where `uid`='{$_uid}'";
        $nc_list->executeSql($_sql);
       
        $nc_list->setDbConf('shop', 'order');
        $money_info = array(
            //'packet_use' => $usePacket,//红包存放规则：array($packet_id,$money)
            'remain_use' => $_buy_num,
            'need_money' => 0
        );
        $orderInfo = array();
        $orderInfo[] = array(
            'activity_id' => $true_goods['activity_id'],
            'num' => $_buy_num,
        );
        $_orderNum = $this->makeOrderNum($_uid);
        $_data = array(
            'appid' => 10002,
            'order_num' => $_orderNum,
            'uid' => $_uid,
            'order_info' => json_encode($orderInfo),
            'money_info' => json_encode($money_info),
            'order_type' => 0,
            'ip' => $this->listIp($_uid),
            'flag' => 0, //这里不能改1 因为会进公盘。。
            'ms' => $ms,
            'rt' => $currentTime,
            'ut' => $currentTime,
            'status'=>-9,
            'order_aid'=>$true_goods['activity_id']
        );

        $_insert = $nc_list->insertData($_data);
        if(!$_insert){
            return false;
        }
        //任务
        $_data = array(
            'order_num' => $_orderNum,
            'flag' => 0,
            'ut' => $currentTime,
            'rt' => $currentTime
        );

        $nc_list->setDbConf('shop', 'task');
        $nc_list->insertData($_data);
        file_put_contents('/tmp/shua1.log',var_export($_data,true),FILE_APPEND);
        $rrrid='-'.date('YmdHis').$_uid;
        $updatearray=array('id'=>$rrrid,'uid'=>$_uid,'rt'=>$sum,'num'=>$_buy_num,'activity_id'=>$true_goods['activity_id'],'title'=>$true_goods['goods_name']);
        if($sumnum > 0){
             $user_record[0]=$updatearray;
        }else{
             $user_record[$all-1]=$updatearray;
        }
       //var_dump($updatearray);exit;
        $num=0;
        foreach($user_record as $k=>$v){
            $num=bcadd($num,$v['rt']);
           // $user_record[$k]['user_record']=json_decode($v['user_record'],true);
            //var_dump($v);exit;
        }
         
        //echo $num;exit;
        
        $userrecord=json_encode($user_record);  
         //var_dump($userrecord);exit;
        $sum = bcadd($num,$lottery_num);  
        $changeNum = bcmod($sum,$need_num);  
        $luckyNum = bcadd($changeNum,10000001);   
     //   echo 'lucky22222222222222:',$luckyNum,'</br>';  
       // echo 'getluckyNum1111:',$getluckyNum,'</br>';  
        $userrecord = safe_db_data($userrecord); //字符串 转移，不然json 字符会被当做特殊字符转义。
       // $userrecord = parse_data($userrecord);
        // var_dump($userrecord);exit;
       if($luckyNum==$getluckyNum){
             $sql = "update ".DATABASE.".t_lucky_num set `time_sum`=$num,`user_record`='$userrecord' where `lucky_num_id`={$activity_msg['lucky_num_id']} "; 
           //  echo $sql,'</br>'; //exit;
             $updatesql=$nc_list->executeSql($sql);
             return $luckyNum;
       }
        
       return false;
         
           

    }

    private function makeOrderNum($uid){
        //订单号
        $uid = 10000000000 + $uid;
        $orderNum = date('YmdHis').$uid;
        return $orderNum;
    }
    public function listIp($uid){
        $a = array(
            '210.12.9.214',
            '210.12.10.214',
            '210.12.12.214',
            '210.12.13.214',
            '210.12.14.214',
            '210.12.15.214',
            '210.12.16.014',
            '210.12.17.214',
            '210.12.18.014',
            '210.12.19.014',
            '210.12.20.214',
            '210.12.23.214',
            '210.12.24.214',
            '210.12.26.114',
            '210.12.27.214',
            '210.12.28.214',
            '210.12.29.214',
            '210.12.30.214',
            '210.12.31.214',
            '210.12.33.214',
            '210.12.34.214',
            '210.12.35.114',
            '210.12.36.214',
            '210.12.37.214',
            '210.12.38.014',
            '210.12.39.214',
            '210.12.40.014',
            '210.12.41.214',
            '210.12.42.214',
            '210.12.43.214',
            '210.12.45.214',
            '210.12.58.214',
            '210.12.59.214',
            '210.12.60.214',
        );
        $index = $uid%count($a);
        return ip2long($a[$index]);
    }
	

}