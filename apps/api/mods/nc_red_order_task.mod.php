<?php
/**
 * @since 2016-01-20
 */
class NcRedOrderTaskMod extends BaseMod{
	
	public function run(){
		//后台任务
		//开启任务
		while(1){
			//判断是否需要停止，不能用kill来杀死该进程，这会导致一个任务执行一半被终止
			$nc_list = Factory::getMod('nc_list');
            $nc_list->setDbConf('shop', 'red_user');
            //获取一个任务
			$taskInfo = $this->getOneTask();
			if(empty($taskInfo)){//没有数据，sleep5秒
				sleep(1);
				continue;
			}
            $activityInfo = $this->getActivityInfo($taskInfo['activity_id']);
            if($activityInfo['need_num']<=$activityInfo['user_num']){
                $this->updateStat($taskInfo,2);
                continue;
            }

            //查积分
            $kou = $this->koujifen($taskInfo['uid']);
            if(!$kou){
                $this->updateStat($taskInfo,2);
                continue;
            }

            $insert = array(
                'activity_id' => $taskInfo['activity_id'],
                'uid' => $taskInfo['uid'],
                'ut' => time(),
                'lucky_num' => 1000001+$activityInfo['user_num'],
                'time' => round(microtime(true)*1000),
            );
            $re = $nc_list->insertData($insert);
            if(!$re){
                $this->updateStat($taskInfo,3);
                continue;
            }else{
                $this->updateStat($taskInfo,1);
            }
            $update = " `user_num`=`user_num`+1 ";
            if($activityInfo['need_num'] == $activityInfo['user_num']+1){
                //end
                $time = time();
                $pub = $this->getPublishTime($time);
                $update .= " ,flag=1,end_time={$time},publish_time={$pub} ";
                //auto start
                $red = $this->getRedInfo($activityInfo['red_id']);
                if($red['is_in_activity']==2 && $red['stat']==0){
                    $this->startNew($red);
                }
            }
            $nc_list->setDbConf('shop', 'red_activity');
            $sql = "update {$nc_list->dbConf['tbl']} set {$update} where `activity_id`={$taskInfo['activity_id']}";
            $nc_list->executeSql($sql);
		}
	}

    public function koujifen($uid){
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'point_rule');
        $info = $nc_list->getDataOne(array('type'=>'红包'), array('point'), array(), array(),false);
        if($info['point']==0){
            return true;
        }
        $nc_list->setDbConf('shop', 'point');
        $info2 = $nc_list->getDataOne(array('uid'=>$uid), array('point'), array(), array(),false);
        if($info2['point']<$info['point']){
            return false;
        }
        $time = time();
        $sql = "update {$nc_list->dbConf['tbl']} set `point`=`point`-{$info['point']},`use`=`use`+{$info['point']},`ut`={$time} where `uid`='{$uid}' ";
        $nc_list->executeSql($sql);
        $nc_list->setDbConf('shop', 'point_detail');
        $nc_list->insertData(array(
            'uid' => $uid,
            'desc' => '参与红包',
            'point' => 0-$info['point'],
            'ut' => $time,
        ));
        return true;
    }

    public function startNew($red){
        $insert = array(
            'red_id' => $red['red_id'],
            'need_num' => $red['need_num'],
            'user_num' => 0,
            'flag' => 0,
            'rt' => time(),
            'ut' => time(),
            'price' => $red['price'],
        );
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'red_activity');
        $nc_list->insertData($insert);
    }

    public function getRedInfo($red_id){
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'red');
        $where = array(
            'red_id' => $red_id
        );
        $data = $nc_list->getDataOne($where, array(), array(),array(), false);
        return $data;
    }

    /**
     * 揭晓时间 开奖时间
     */
    public function getPublishTime($currentTime){
        /*$h = date('H', $currentTime);
        if($h < 9){//小于9点的，等到9点10分开奖
            return strtotime(date('Y-m-d 09:11:00'));
        }else if($h == 23){//等于23点的，等到第二天9点10分开奖
            return strtotime(date('Y-m-d 09:11:00')) + 86400;
        }else{
            return $currentTime + 600;
        }*/
        return $currentTime+600;
    }

    public function updateStat($taskInfo,$type){
        $update = array(
            'stat' => $type
        );
        $where = array(
            'id' => $taskInfo['id']
        );
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'red_order');
        $nc_list->updateData($where,$update);
    }

    /**
     * 获取一个任务
     */
    public function getOneTask(){
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'red_order');
        $where = array(
            'stat' => 0
        );
        $order = array(
            'id' => 'asc'
        );
        $limit = array(
            'begin' => 0,
            'length' => 1
        );
        $data = $nc_list->getDataOne($where, array(), $order, $limit, false);
        return $data;
    }

    public function getActivityInfo($activity_id){
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'red_activity');
        $where = array(
            'activity_id' => $activity_id
        );

        $data = $nc_list->getDataOne($where, array(), array(), array(), false);
        return $data;
    }
	

}
