<?php
/**
 * @since 2016-01-20
 */
class NcShuaMod extends BaseMod{

    public $time;
    private $hour;
    private $goods;
    private $stop;

	public function run(){
		//后台任务
        $nc_list_mod = Factory::getMod('nc_list');
        while(1){
            $nc_list_mod->setDbConf('shop', 'shua');
            $info = $nc_list_mod->getDataOne(array(),array(),array(),array(),false);
            //未开始
            if(empty($info) || $info['state']==0){
                sleep($this->sleep());
                continue;
            }
            //非刷单时间
            $this->hour = explode(',',$info['hour']);
            if(!in_array(date('H'),$this->hour)){
                sleep($this->sleep());
                continue;
            }
            
            //商品
            $this->goods = explode(',',$info['goods']);
            if(empty($this->goods)){
                sleep($this->sleep());
                continue;
            }

            $this->time = array($info['jiange1'],$info['jiange2']);

            $nc_list_mod->setDbConf('shop', 'activity');
            $sql = "select `activity_id`,`need_num`,`user_num`,`goods_id` from {$nc_list_mod->dbConf['tbl']} where `flag`=0 and `goods_id` in ({$info['goods']})";
            $goods = $nc_list_mod->getDataBySql($sql,false);

            //截止时间
            $this->stop = $info['stop'];
            if(empty($goods)){
                sleep($this->sleep());
                continue;
            }
            $need_goods = array();
            foreach($goods as $good){
                if($good['need_num']-$good['user_num'] >= $this->stop){
                    $need_goods[] = $good;
                }
            }
            if(empty($need_goods)){
                sleep($this->sleep());
                continue;
            }
            shuffle($need_goods);
            shuffle($need_goods);
            //随机商品出来
            $true_goods = array_pop($need_goods);
            //取用户
            $nc_list_mod->setDbConf('shop', 't_false');

            $sql2 = "select floor((select count(*) from {$nc_list_mod->dbConf['tbl']} where stat=0) * rand()) as uid";
            $user = $nc_list_mod->getDataBySql($sql2,false);
            $sql2 = "SELECT `uid` FROM {$nc_list_mod->dbConf['tbl']} WHERE `stat`=0 LIMIT {$user[0]['uid']},1";
            $user = $nc_list_mod->getDataBySql($sql2,false);
            if(empty($user)){
                sleep($this->sleep());
                continue;
            }
            $_uid = $user[0]['uid'];

            //分类型
            $nc_list_mod->setDbConf('shop', 'goods');
            $where = array(
                'goods_id' => $true_goods['goods_id'],
            );
            $_type = $nc_list_mod->getDataOne($where,array('activity_type'),array(),array(),false);
            if($_type['activity_type'] == 3 ){
                $nc_list_mod->setDbConf('shop', 'activity_user');
                $wher = array(
                    'activity_id' => $true_goods['activity_id'],
                    'uid' => $_uid,
                );
                $_user_num = $nc_list_mod->getDataOne($wher,array('user_num'),array(),array(),false);
                if($_user_num['user_num']>=10) {
                    sleep($this->sleep());
                    continue;
                }
                $_buy_num = 10 - (int)$_user_num['user_num'];
            }/*elseif($_type['activity_type'] == 2){  //所有商品改成1元起购
                if($true_goods['need_num']-$true_goods['user_num']<=10){
                    sleep($this->sleep());
                    continue;
                }
                $_buy_num = 10;
                if($true_goods['need_num']-$true_goods['user_num']>=110){
                   $_buy_num= ceil(rand(1,10))*10;                  
                }
                
            }*/else{
                if($info['money1'] && $info['money2']){   
                    $lastnum=$true_goods['need_num']-$true_goods['user_num'];
                    if($lastnum < $info['money1']){
                         $_buy_num = mt_rand(1,$lastnum);
                    }else{
                        $lastnum= $lastnum > $info['money2'] ?$info['money2']:$lastnum;  //求出最大的数
                        $_buy_num = mt_rand($info['money1'],$lastnum);
                    }
                     
                }else{
                    $_buy_num = mt_rand(1,min($true_goods['need_num']-$true_goods['user_num'],10));
                }
                
                
            }
            //加余额
            $nc_list_mod->setDbConf('main', 'user');
            $_sql = "update {$nc_list_mod->dbConf['tbl']} set `money`=`money`+{$_buy_num} where `uid`='{$_uid}'";
            $nc_list_mod->executeSql($_sql);
            //加订单
            list($currentTime, $ms) = $this->getMicrotime();
            $nc_list_mod->setDbConf('shop', 'order');
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
                'status'=>-8,
                'order_aid'=>$true_goods['activity_id']
            );

            $_insert = $nc_list_mod->insertData($_data);
            if(!$_insert){
                sleep($this->sleep());
                continue;
            }
            //任务
            $_data = array(
                'order_num' => $_orderNum,
                'flag' => 0,
                'ut' => $currentTime,
                'rt' => $currentTime
            );
            $nc_list_mod->setDbConf('shop', 'task');
            $nc_list_mod->insertData($_data);
            file_put_contents('/tmp/shua.log',var_export($_data,true),FILE_APPEND);
            sleep($this->sleep());
        }
	}

    public function sleep(){
        $time = (int) mt_rand($this->time[0],$this->time[1]);
        return $time<5 ? 5 : $time;
    }

    public function getMicrotime(){
        $time = (string)microtime(true);
        $timeArr = explode('.', $time);
        $currentTime = $timeArr[0];//当前时间
        $ms = substr($timeArr[1],0,3);
        return array($currentTime, $ms);
    }

    public function makeOrderNum($uid){
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
