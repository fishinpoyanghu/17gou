<?php
class ActivityMod extends BaseMod{
    private $data;
    public function __construct(){
        $this->data = Factory::getData('activity');
    }

    /**
     * 活动记录
     * @param $type
     * @param int $page
     * @param string $num
     * @return mixed
     */
    public function activity($type,$page=1,$num,$keyword){
        $info = $this->data->activity($type,$page,$num,$keyword);

        $flag = array('进行中','即将揭晓','已揭晓',4 => '活动结束');
        foreach($info['list'] as &$val){

            $val['ut'] = $val['ut'] ? date('Y-m-d H:i:s',$val['ut']) : '-';
            $val['end_time'] = $val['end_time'] ? date('Y-m-d H:i:s',$val['end_time']) : '-';
            $val['lucky'] = $val['lucky_num']&&$val['nick'] ? $val['lucky_num'].'（'.$val['nick'].'）' : '-';
            $val['assign'] = $val['is_false'] ? '取消指定' : '指定中奖';
            $val['is_gray'] = $val['process'] > 20 || $val['flag'] == 1 ? 1 : 0;//指定操作是否置灰
	        $val['is_end'] = $val['flag'];
            $val['flag'] = $flag[$val['flag']];
        }
        return $info;
    }
    public function sharelist($type,$page=1,$num,$keyword){
         $info = $this->data->sharelist($type,$page,$num,$keyword);
           $flag = array('进行中','即将揭晓','已揭晓',4 => '活动结束');
            foreach($info['list'] as &$val){ 
                $val['ut'] = $val['ut'] ? date('Y-m-d H:i:s',$val['ut']) : '-';
                $val['end_time'] = $val['end_time'] ? date('Y-m-d H:i:s',$val['end_time']) : '-';
                $val['lucky'] = $val['lucky_num']&&$val['nick'] ? $val['lucky_num'].'（'.$val['nick'].'）' : '-';  
                $val['flag'] = $flag[$val['flag']];
            }
         return $info;
    }

    /**
     * 指定中奖or取消指定
     * @param $id
     * @return int
     */
    public function assign($id){
        if(!$id) return 0;
        return $this->data->assign($id);
    }

    /**
     * 批量指定中奖
     * @param $ids
     * @return int
     */
    public function multiAssign($ids){
        if(empty($ids)) return 0;
        foreach($ids as &$v){
            $v = intval($v);
        }
        $id = implode(',',$ids);
        return $this->data->multiAssign($id);
    }

    /**
     * 红包列表
     * @param int $page
     * @param string $num
     * @return mixed
     */
    public function redList($page=1,$num=''){
        $info = $this->data->redList($page,$num);
        foreach($info['list'] as &$val){
            //红包是否开始第一期
            $res = $this->data->isActivity($val['red_id']);
            $val['is_start'] = empty($res) ? 0 : 1;
        }
        return $info;
    }

    /**
     * 批量修改红包
     * @param $ids
     * @param $type
     * @return int
     */
    public function modifyRed($ids,$type){
        if(empty($ids)) return 0;
        $type = in_array($type,array(1,2)) ? $type : 1;
        foreach($ids as &$v){
            $v = intval($v);
        }
        $id = implode(',',$ids);
        return $this->data->modifyRed($id,$type);
    }

    /**
     * 删除红包
     * @param $id
     * @return int
     */
    public function delRed($id){
        if(!$id) return 0;
        return $this->data->delRed($id);
    }

    /**
     * 获取红包信息
     * @param $id
     * @return array
     */
    public function getRed($id){
        if(!$id) return array();
        return $this->data->getRed($id);
    }

    /**
     * 添加红包or编辑红包
     * @param $data
     * @param $id
     * @return array
     */
    public function addRed($data,$id){
        foreach($data as $val){
            if(empty($val)){
                return array('state' => 0,'msg' => '请输入完整信息');
            }
        }
        $data['need_num'] = intval($data['need_num']);
        $data['price'] = round($data['price'],2);
        $data['rt'] = time();
        $row = $this->data->addRed($data,$id);
        if($row){
            return array('state' => $row,'msg' => '操作成功');
        }else{
            return array('state' => 0,'msg' => '操作失败');
        }
    }

    /**
     * 红包开始第一期
     * @param $id
     * @return int
     */
    public function redStart($id){
        if(!$id) return 0;
        $info = $this->data->getRed($id);
        if(!$info) return 0;
        $time = time();
        $data = array(
            'red_id' => $info['red_id'],
            'need_num' => $info['need_num'],
            'rt' => $time,
            'ut' => $time,
            'price' => $info['price'],
        );
        return $this->data->redStart($data);
    }

    /**
     * 晒单列表
     * @param string $state
     * @param int $page
     * @param string $num
     * @return mixed
     */
    public function show($state='',$page=1,$num=''){

        $info = $this->data->show($state,$page,$num);
        $check = array(0=>'未审核',2=>'已通过',3=>'不通过');
        foreach($info['list'] as &$val){
            $val['img'] = explode(',',$val['img']);
            $val['state'] = $check[$val['stat']] ? $check[$val['stat']] : '未知';
        }
        return $info;
    }

    /**
     * 晒单or评论 审核、删除
     * @param $id
     * @param $stat
     * @param $type
     * @return int
     */
    public function modifyShow($id,$stat,$type){
        if(!$id || !in_array($stat,array(1,2,3)) || !in_array($type,array(0,1))) return 0;
        return $this->data->modifyShow($id,$stat,$type);
    }

    /**
     * 晒单or评论 批量删除
     * @param $id
     * @param $type
     * @return int
     */
    public function multiShowDel($id,$type){
        if(empty($id) || !is_array($id) || !in_array($type,array(0,1))) return 0;
        foreach($id as &$v){
            $v = intval($v);
        }
        $ids = implode(',',$id);
        return $this->data->multiShowDel($ids,$type);
    }

    /**
     * 评论审核列表
     * @param string $state
     * @param int $page
     * @param string $num
     * @return mixed
     */
    public function comment($state='',$page=1,$num=''){
        $info = $this->data->show($state,$page,$num,false);
        $check = array(0=>'未审核',2=>'已通过',3=>'不通过');
        foreach($info['list'] as &$val){
            $val['state'] = $check[$val['stat']] ? $check[$val['stat']] : '未知';
        }
        return $info;
    }

    /**
     * 订单列表
     * @param int $page
     * @param string $num
     * @param string $state
     * @return mixed
     */
    public function order($page=1,$num='',$state=''){
        $info = $this->data->order($page,$num,$state);
        $o_stat = array('待发货','已发货','交易完成');
        foreach($info['list'] as &$v){
            $v['state'] = empty($o_stat[$v['logistics_stat']]) ? '待填写收货地址' : $o_stat[$v['logistics_stat']];
            if($v['address']){
                $tmp = explode(':',$v['address']);
                $v['add'] = urldecode($tmp[0]);
                $v['name'] = urldecode($tmp[1]);
                $v['phone'] = $tmp[2];
            }
        }
        return $info;
    }

    /**
     * 指定中奖名单列表
     * @param $page
     * @param $num
     * @return mixed
     */
    public function assignList($page,$num,$award){
        $info = $this->data->assignList($page,$num,$award);
        foreach($info['list'] as &$v){
            $v['lucky_num'] = $this->data->getLuckyNum($v['uid']);
        }
        return $info;
    }

    /**
     * 开启或关闭指定中奖
     * @param $id
     * @return int
     */
    public function modifyAssign($id){
        if(!$id) return 0;
        return $this->data->modifyAssign($id);
    }

    /**
     * 指定中奖名单
     * @param $name
     * @return array
     */
    public function addAssign($name){
        $name = array_filter(explode("\n",$name));
        $res = 0;
        require_once(COMMON_PATH.'include/PubUtil.php');
        foreach($name as $nick){
            $nick = trim($nick);
            if(!preg_match('/^[\x{4e00}-\x{9fa5}\w\d]+$/u',$nick)){
                return array('state' => 0,'msg' => '用户名只能由中英文、数字、下划线组成');
            }
            $pwd = mt_rand(100000,999999);
            $str = '0123456789abcdfghijklmnopqrstuvwxyz_ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $user = '';
            for($i=0;$i<8;$i++){
                $user .= $str[mt_rand(0,strlen($str)-1)];
            }
            $uid = get_auto_id(C('AUTOID_M_USER'));
            //添加用户
            $user_info = array(
                'uid' => $uid,
                'appid' => APP_ID,
                'name' => $user,
                'nick' => $nick,
                'type'=>-1,
                'password' => md5($pwd),
                'rt' => time(),
                'ip' => ip2long(PubUtil::rand_ip()),
                'icon' => mt_rand(1,500).'.jpg',
            );
            $res = $this->data->addUser($user_info);
            if($res){
                //添加指定中奖名单
                $info = array(
                    'uid' => $uid,
                    'ut' => time(),
                    'user' => $user,
                    'pwd' => $pwd,
                );
                $res = $this->data->addAssign($info);
            }else{
                return array('state' => 0,'msg' => '添加失败');
            }
        }
        return array('state' =>$res);
    }

    /**
     * 录入物流信息
     * @param $id
     * @param $code
     * @param $num
     * @return array
     */
    public function addExpress($id,$code,$num,$ordernum,$address){
        if(empty($id) || empty($code) || empty($num)) return 0;
        return $this->data->addExpress($id,$code,$num,$ordernum,$address);
    }

    /**
     * 活动参与记录
     * @param $id
     * @param $page
     * @param $num
     * @param bool|false $type
     * @return array
     */
    public function record($id,$page,$num,$type=false){
        if(!$id) return array();
        $info = $this->data->record($id,$page,$num,$type);
        foreach($info['list'] as &$val){
            //获取夺宝号
            $res = $this->data->getActivityNum($val['activity_id'],$val['uid']);
            $val['activity_num'] = empty($res) ? "" : $res['activity_num'];
        }
        return $info;
    }

    /**
     * 分佣设置信息
     * @return mixed
     */
    public function commission(){
        $info = $this->data->commission();
        $res = array();
        foreach($info as $val){
            $res[$val['level']]['percent'] = $val['percent'];
        }
        return $res;
    }

    /**
     * 保存分佣设置
     * @param $data
     * @return array
     */
    public function saveCom($data){
        foreach($data as &$val){
            $val['level'] = intval($val['level']);
            if($val['percent']<0 || $val['percent'] > 100){
                return array('state' => 0,'msg' => '请输入0-100之间的数');
            }
        }
        $row = $this->data->saveCom($data);
        return array('state' => $row,'msg' => $row?'保存成功':'保存失败');
    }

    /**
     * 分销用户
     * @param int $page
     * @param $num
     * @return mixed
     */
    public function distribution($page=1,$num){
        $info = $this->data->distribution($page,$num);
        $info['total_money'] = $this->data->totalMoney();//合计佣金
        $info['total_number'] = $this->data->friendCount();//合计分销人数
        foreach($info['list'] as &$val){
            if(empty($val['icon'])){
                $val['icon'] = USER_ICON_URL."user/icon.png";
            }else{
                if(strpos($val['icon'],'http') === false){
                    $val['icon'] = USER_ICON_URL.$val['icon'];
                }
            }
            $val['f_total'] = $this->data->friendCount($val['uid']);
        }
        return $info;
    }

    /**
     * 分润详情
     * @param $uid
     * @param int $page
     * @param string $num
     * @return array
     */
    public function profitDetail($uid,$page=1,$num=''){
        if(!$uid) return array();
        $info = $this->data->profitDetail($uid,$page,$num);
        foreach($info['list'] as &$val){
            $tmp = explode(':',$val['desc']);
            $val['consume'] = $tmp[1];
            $val['nick'] = urldecode($tmp[2]);
        }
        $all = $this->data->profitDetail($uid);
        $info['total_money'] = 0;
        $info['total_consume'] = 0;
        foreach($all['list'] as $v){
            $tmp = explode(':',$v['desc']);
            $info['total_money'] += $v['money'];
            $info['total_consume'] += $tmp[1];
        }

        return $info;
    }

    /**
     * 保存系统消息
     * @param $data
     * @param $id
     * @return array
     */
    public function saveSysMsg($data,$id){
        if(empty($data['title'])) return array('state' => 0,'msg' => '请输入标题');
        if(empty($data['content'])) return array('state' => 0,'msg' => '请输入内容');
        $data['appid'] = APP_ID;
        $data['ut'] = time();
        $state = $this->data->saveSysMsg($data,$id);
        return array('state' => $state,'msg' => $state?'保存成功':'保存失败');
    }

    /**
     * 系统消息列表
     * @param $page
     * @param $num
     * @param $keyword
     * @return mixed
     */
    public function sysMsg($page,$num,$keyword){
        $info = $this->data->sysMsg($page,$num,$keyword);
        return $info;
    }

    /**
     * 系统消息详情
     * @param $id
     * @return array
     */
    public function sysMsgInfo($id){
        if(!$id) return array();
        return $this->data->sysMsgInfo($id);
    }

    /**
     * 删除系统消息
     * @param $id
     * @return array
     */
    public function sysMsgDel($id){
        foreach($id as &$i){
            $i = intval($i);
        }
        $ids = implode(',',$id);
        $state = $this->data->sysMsgDel($ids);
        return array('state' => $state);
    }

    /**
     * 私信销量列表
     * @param $page
     * @param $num
     * @param $keyword
     * @return mixed
     */
    public function privateMsg($page,$num,$keyword){
        $info = $this->data->privateMsg($page,$num,$keyword);
        return $info;
    }

    /**
     * 私信消息详情
     * @param $id
     * @return array
     */
    public function getMsgInfo($id){
        if(!$id) return array();
        return $this->data->getMsgInfo($id);
    }

    /**
     * 保存私信消息
     * @param $data
     * @param $id
     * @return array
     */
    public function saveMsg($data,$id){
        if(empty($data['uid'])) return array('state' => 0,'msg' => '用户不存在');
        if(empty($data['content'])) return array('state' => 0,'msg' => '请输入内容');
        if(!$id){
            $data['msg_notify_id'] = get_auto_id(C('AUTOID_M_MSG_NOTIFY'));
        }
        $data['appid'] = APP_ID;
        $data['from_uid'] = APP_ID;
        $data['type'] = 6;
        $data['target_type'] = 7;
        $data['rt'] = time();
        $data['ut'] = time();
        $state = $this->data->saveMsg($data,$id);
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('main', 'user');
        $time = time();
        $sql = "update ".DATABASE.".t_user set `notify_lucky_new`=`notify_lucky_new`+1,ut={$time} where uid={$data['uid']}";
        $nc_list->executeSql($sql);

        return array('state' => $state,'msg' => $state?'保存成功':'保存失败');
    }

    /**
     * 删除私信消息
     * @param $id
     * @return array
     */
    public function msgDel($id){
        foreach($id as &$i){
            $i = intval($i);
        }
        $ids = implode(',',$id);
        $state = $this->data->msgDel($ids);
        return array('state' => $state);
    }

    /**
     * 获取所有用户列表
     * @return mixed
     */
    public function getUserList(){
        return $this->data->getUserList();
    }
    
    /**
     * 结束活动
     * @param $a_id
     * @return array
     */
    public function endActivity($a_id){
        if(!$a_id) return array('state' => 0,'msg' => '操作失败');
        //修改活动状态、商品不再自动开始
        $res = $this->data->updateActivity($a_id);
        if(!$res){
            return array('state' => 0,'msg' => '结束失败');
        }
        //获取活动参与记录
        $list = $this->data->activityNum($a_id);
        $data = array();
        foreach($list as $val){
            $data[$val['uid']][] = $val['this_num'];
        }
        foreach($data as $uid=>$money){
            $total = array_sum($money);
            //生成退款明细
            $this->data->refund($uid,$total);
            //更新账户余额
            $this->data->userBalance($uid,$total);
            //发送通知
            $this->data->addMsg($uid,$res['title'],$a_id);
        }
        return array('state' => 1,'msg' => '结束成功');
    }

    /**
     * 提现申请列表
     * @param int $page
     * @param string $num
     * @param string $keyword
     * @return mixed
     */
    public function cash($page=1,$num='',$keyword=''){
        $info = $this->data->cash($page,$num,$keyword);
        $type = array(1=>'待审核',2=>'批准，等待打款',4=>'已打款，待查收');
        foreach($info['list'] as &$val){
            $tmp = explode(':',$val['desc']);
            $val['type'] = $type[$tmp[3]];
            $val['money'] = abs($val['money']);
            $val['weixin_id'] = urldecode($tmp[2]);
            $val['state'] = $tmp[3];
        }
        return $info;
    }


    /**
     * 提现列表
     * @param int $page
     * @param string $num
     * @param string $keyword
     * @return mixed
     */
    public function cashlist($page=1,$num='',$keyword=''){
        $info = $this->data->cashlist($page,$num,$keyword); 
        return $info;
    }

    /**
     * 修改提现状态
     * @param $id
     * @param $state
     * @return array
     */
    public function modifyCash($id,$state){
        if(!$id || !in_array($state,array(2,4))){
            return array('state' => 0);
        }
        $cash = $this->data->CashInfo($id);
        $tmp = explode(':',$cash['desc']);
        $tmp[3] = $state;
        $desc = implode(':',$tmp);
        $state = $this->data->modifyCash($id,$desc);
        return array('state' => $state?1:0);
    }

    public function savedoshare($data, $id){
        $nc_list_mod = Factory::getMod('nc_list'); 
        $where = array( 
            'activity_id' => $data['activity_id'],
             
        );

        if(empty($data['show_title']) || empty($data['show_desc']) || empty($data['img']) || empty($data['activity_id']) || empty($data['rt']) ){
             return array('state' => 0,'msg' => '数据不完整,请填写好所有数据！');
        }
        if($data['rt']>date('Y-m-d')){
             return array('state' => 0,'msg' => '时间不能大于今天！');
        }

        $column = array(
             'goods_id','uid','ut'
        );
        $nc_list_mod->setDbConf('shop', 'lucky_num');
        $goodsInfo = $nc_list_mod->getDataList($where, $column, array(), array(), false);
        if(strtotime($data['rt'])<$goodsInfo[0]['ut']+3*24*3600){
             return array('state' => 0,'msg' => '晒单时间必须是中奖的3天后');
        }
        
       // echo $nc_list_mod->getlastsql();exit;
        if(empty($goodsInfo) || $goodsInfo[0]['uid']==0){
           return array('state' => 0,'msg' => '当前订单没开奖');
        }

        $nc_list_mod->setDbConf('main', 'user');
        $ret = $nc_list_mod->getDataOne(array('uid'=>$goodsInfo[0]['uid']), array('type'), array(), array(), false);
       
        if($ret['type']!='-1'){
            return array('state' => 0,'msg' => '当前订单不是正常用户中奖！');
        } 

        $goods_id = $goodsInfo[0]['goods_id'];
        //查看用户是否已经分享
        $where = array( 
            'activity_id' => $data['activity_id'],
        );
        $column = array(
            'show_id'
        );  
        $nc_list_mod->setDbConf('shop', 'show');
        $showInfo = $nc_list_mod->getDataList($where, $column, array(), array(), false);
        if(!empty($showInfo)){ 
             $data['ut']=$data['rt']=strtotime($data['rt']); 
             $nc_list_mod->updateData($where, $data); //var_dump($data); 
             return array('state' => 1,'msg' => '保存成功');
            //保存api_result(1, '一期活动只能分享一次');
        }

        //执行分享
        $time = time();
        $data = array(
            'appid' =>10002,
            'goods_id' => $goods_id,
            'activity_id' => $data['activity_id'],
            'uid' => $goodsInfo[0]['uid'],
            'show_title' => $data['show_title'],
            'show_desc' => $data['show_desc'],
            'img' => $data['img'],
            'stat' => 2,
            'rt' => strtotime($data['rt']),
            'ut' =>strtotime($data['rt'])
        );
        $ret = $nc_list_mod->insertData($data);
        return array('state' => $ret,'msg' => '添加成功');
        
    }

}