<?php
/**
 * @since 2016-01-22
 */
class NcLuckyNumMod extends BaseMod{

    private function getLottery(){
        $url = 'http://f.apiplus.cn/cqssc-1.json';
        $result = file_get_contents($url);
        file_put_contents('/tmp/false2.log','lottery'.$result,FILE_APPEND);
        $res = json_decode($result,true);
        $num = $res['data'][0]['opencode'];
        $num = str_replace(',','',$num);
        return (int) $num;
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
            //获取时彩号
            $lottery_num = $this->getLottery();

            $currentTime = time();

            $msg_mod = Factory::getMod('msg');
            if(empty($data)) continue;
            foreach($data as $val){
                if($val['publish_time']>=time()+5) continue;
                $activity_id = $val['activity_id'];
                $where = array(
                    'activity_id' => $activity_id
                );
                $column = array(
                    'time_sum'
                );
                $nc_list->setDbConf('shop', 'lucky_num');
                $time_sum = $nc_list->getDataOne($where, $column, array(), array(), false);
                if(empty($time_sum)) continue;
                $sum = bcadd($time_sum['time_sum'],$lottery_num);
                $changeNum = bcmod($sum,$val['need_num']);
                //幸运号
                $luckyNum = bcadd($changeNum,10000001);

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
                $userNum = $nc_list->getDataOne($where, $column, array(), array(), false);
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
                 
            }
        }
    }
	

}