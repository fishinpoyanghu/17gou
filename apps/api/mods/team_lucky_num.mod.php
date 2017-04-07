<?php
/**
 * @since 2016-01-22
 */
class TeamLuckyNumMod extends BaseMod{

    private function getLottery(){
        $url = 'http://f.apiplus.cn/cqssc-1.json';
        $result = file_get_contents($url);
        file_put_contents('/tmp/falseteam2.log','lottery'.$result,FILE_APPEND);
        $res = json_decode($result,true);
        $num = $res['data'][0]['opencode'];
        $num = str_replace(',','',$num);
        return (int) $num;
    }
	
	public function run(){
		$nc_list = Factory::getMod('nc_list'); 
        while(1){
            sleep(8);
            $nc_list->setDbConf('team', 'teamwar');
            $where = array(
                'flag' => 7
            );
            $column = array(
                 'teamwar_id','goods_id','need_num','publish_time','uid'
            );
            $order = array(
                'publish_time' => 'asc'
            );
            $data = $nc_list->getDataList($where, $column, $order,array(),false);
            
            //获取时彩号
            $lottery_num = $this->getLottery();

            $currentTime = time();

            $msg_mod = Factory::getMod('msg');
            if(empty($data)) continue;
            foreach($data as $val){
               if($val['publish_time']>=time()+5) continue;
                $activity_id = $val['teamwar_id'];
                $where = array(
                    'activity_id' => $activity_id
                );
                $column = array(
                    'time_sum'
                );
                $nc_list->setDbConf('team', 'team_lucky_num');
                $time_sum = $nc_list->getDataOne($where, $column, array(), array(), false);
            
                if(empty($time_sum)) continue;
                $sum = bcadd($time_sum['time_sum'],$lottery_num);
                $changeNum = bcmod($sum,$val['need_num']);
                //幸运号
                $luckyNum = bcadd($changeNum,10000001);

                $where = array(
                   
                    'activity_id' => $activity_id,
                    'activity_num' => $luckyNum
                );
                $column = array(
                    'uid'
                );
                $nc_list->setDbConf('team', 'team_activity_num');
                //中奖玩家
                $userInfo = $nc_list->getDataOne($where, $column, array(), array(), false);

                if(empty($userInfo)) continue;
                $where = array(
                   
                    'teamwar_id' => $activity_id,
                    'uid' => $userInfo['uid']
                );
                $column = array(
                    'num'
                );
                $nc_list->setDbConf('team', 'team_member');

                //中奖玩家本次参与次数
                $userNum = $nc_list->getDataOne($where, $column, array(), array(), false);
              
                if(empty($userNum)) continue;
                $luckyNumInfo = array(
                    'lucky_num' => $luckyNum,
                    'lottery_num' => $lottery_num,
                    'uid' => $userInfo['uid'],
                    'user_num' => $userNum['num'],
                    'user_read' => 0,
                    'ut' => $currentTime,
                );

                $where = array(
                    'activity_id' => $activity_id
                );
                $nc_list->setDbConf('team', 'team_lucky_num');
                $nc_list->updateData($where, $luckyNumInfo);
                $grouplader=$luckyNumInfo;
                $grouplader['uid']=$val['uid'];
                $grouplader['activity_id']=$activity_id;
                $grouplader['goods_id']=$val['goods_id']; 
                $grouplader['time_sum']=0;
                $grouplader['need_num']=0;
                $grouplader['user_num']=0; 
                $nc_list->insertData($grouplader);  //团长中奖赠送
                 
                $nc_list->setDbConf('team', 'teamwar');
                $activityData = array(
                    'publish_time' => time(),
                    'flag' => 8,
                    'ut' => $currentTime
                );
                //通知用户中奖啦
                
                //更新活动状态
                $where = array(
                    'teamwar_id' => $activity_id
                ); 
                $nc_list->updateData($where, $activityData);  
                $nc_list->setDbConf('main', 'user'); 
                $usermsg = $nc_list->getDataOne(array('uid'=>$userInfo['uid']), array('phone','nick'), array(), array(), false);
                if(preg_match('/^1[345789][0-9]{9,9}$/',$usermsg['phone'])){
                    require_once COMMON_PATH.'/libs/ChuanglanSmsHelper/ChuanglanSmsApi.php';
                    $clapi  = new ChuanglanSmsApi();   
                    $data ="您好，恭喜您{$usermsg['nick']}参团中奖啦 ,请设置好收获地址以便收取商品！  如有疑请关注亿七购公众平台（yiqigou668）！如非本人操作，请忽略本短信。";             
                    $result = $clapi->sendSMS($usermsg['phone'], $data,'true');
                    $result = $clapi->execResult($result);  
                }
                $msg_mod->sendNotify(10002, $userInfo['uid'], 10002, 5, 0, 7, '恭喜您中奖啦。');
              
            }
             
        }
    }
	

}