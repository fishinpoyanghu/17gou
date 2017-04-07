<?php
/**
 * @since 2016-01-22
 */
class NcRedLuckyMod extends BaseMod{
	
	public function run(){
		$nc_list = Factory::getMod('nc_list');
        while(1){
            sleep(8);
            $nc_list->setDbConf('shop', 'red_activity');
            $where = array(
                'flag' => 1
            );
            $column = array(
                'activity_id','need_num','publish_time','red_id','price'
            );
            $order = array(
                'publish_time' => 'asc'
            );
            $data = $nc_list->getDataList($where, $column, $order,array(),false);
            //获取时彩号
            //$lottery_num = $this->getLottery();
            //$lotteryInt = intval($lottery_num);

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
                    'time'
                );
                $nc_list->setDbConf('shop', 'red_user');
                $time_sum = $nc_list->getDataList($where, $column, array(), array(), false);
                if(empty($time_sum)) continue;
                $sum = 0;
                foreach($time_sum as $t){
                    $sum = bcadd($t['time'],$sum);
                }
                $changeNum = bcmod($sum,$val['need_num']);
                //幸运号
                $luckyNum = bcadd($changeNum,1000001);

                $where = array(
                    'activity_id' => $activity_id,
                    'lucky_num' => $luckyNum
                );
                $column = array(
                    'uid'
                );
                //中奖玩家
                $userInfo = $nc_list->getDataOne($where, $column, array(), array(), false);
                if(empty($userInfo)) continue;

                $update = array(
                    'result_num' => $luckyNum,
                    'result_uid' => $userInfo['uid'],
                    'flag' => 2,
                    'ut' => time(),
                );
                $where = array(
                    'activity_id' => $activity_id
                );
                $nc_list->setDbConf('shop', 'red_activity');
                $nc_list->updateData($where, $update);
                //通知用户中奖啦
                $msg_mod->sendNotify(10002, $userInfo['uid'], 0, 5, 0, 7, '恭喜您中红包奖啦。');
                //发奖
                $nc_list->setDbConf('shop', 'money');
                $insert = array(
                    'uid' => $userInfo['uid'],
                    'money' => $val['price'],
                    'desc' => '抢红包::',
                    'ut' => time(),
                );
                $nc_list->insertData($insert);
                $nc_list->setDbConf('main', 'user');

                $sql = "update {$nc_list->dbConf['tbl']} set `money`=`money`+{$val['price']} where `uid`={$userInfo['uid']}";
                $nc_list->executeSql($sql);
            }
        }
    }


}