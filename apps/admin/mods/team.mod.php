<?php
class TeamMod extends BaseMod{
    
    /**
     * 商品分类列表
     * @param int $page
     * @param string $num
     * @param string $keyword
     * @return mixed
     */
    public function goodsType($page=1,$num='',$keyword=''){
        $page = $page < 1 ? 1 : intval($page);

        $goods_data = Factory::getData('team');
        $info = $goods_data->goodsType($page,$num,$keyword);
        return $info;
    }

    // public function goodsTypeTwo(){

    //     $goods_data = Factory::getData('team');
    //     $info = $goods_data->goodsTypeTwo();
    //     return $info;
    // }

    public function teamgoods($id){
        if(!$id) return array();
        $goods_data = Factory::getData('team');
        $info = $goods_data->goods($id);
        return $info;
    }

    /**
     * 商品列表
     * @param int $page
     * @param string $num
     * @param string $keyword
     * @return mixed
     */
    public function teamGoodsList($page=1,$num='',$keyword=''){
        $page = $page < 1 ? 1 : $page;
        $goods_data = Factory::getData('team');
        $info = $goods_data->teamgoodsList($page,$num,$keyword);
        foreach($info['list'] as &$val){
            //商品最新一期的活动
             $res = $goods_data->isActivity($val['goods_id']);
            if(empty($res)){
                $val['is_start'] = 1;
            }else{
                if($res['flag'] == 0){
                    $val['is_start'] = 0;
                }else{
                    $val['is_start'] = 1;
                }
            }
            $val['is_del'] = empty($res) || $res['user_num']==0 || $res['flag'] >= 2 ? 1 : 0;//是否可删除
             
        }
        return $info;
    }
    public function activity($page=1,$num='',$keyword=''){
        $page = $page < 1 ? 1 : $page;
        $goods_data = Factory::getData('team');
        $info = $goods_data->activity($page,$num,$keyword); 
        return $info;
    }
    //下架
    public function stop($goodsid){
        $team_data = Factory::getData('team');
       return  $team_data->stop($goodsid);

    }

    public function remen($id){
        $goods_data = Factory::getData('team');
        $info = $goods_data->remen($id);
        return $info;
    }

   /**
     * 结束活动  新添加退款到微信
     * @param $a_id
     * @return array
     */ 
    public function endActivity($a_id){
        if(!$a_id) return array('state' => 0,'msg' => '操作失败');
        //修改活动状态、商品不再自动开始
        $teamdata=Factory::getData('team');
     
        $res = $teamdata->updateActivity($a_id);
        if(!$res){
            return array('state' => 0,'msg' => '结束失败');
        }
        //获取活动参与记录
        $list = $teamdata->activityNum($a_id);
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'order');
        
       $data = array();$refund=array();//微信等第三方支付付款
        foreach($list as $val){
             $sql = "select uid,money_info,order_num,transaction_id  from {$nc_list->dbConf['tbl']}    where flag=1 and pay_type=1  and order_num={$val['order_num']} and transaction_flag=1";  
             $row= $nc_list->getdatabysql($sql,false);  

             if($row){
                $needmoney=json_decode($row[0]['money_info'],true);
                if($needmoney['need_money']>0 && $needmoney['need_money']<=$val['this_num']){
                    $moneyinfo['need_money']=$needmoney['need_money']; 
                    $moneyinfo['transaction_id']=$row[0]['transaction_id'];
                    $moneyinfo['order_num']=$row[0]['order_num'];
                    $moneyinfo['uid']=$row[0]['uid'];
                    $moneyinfo['flag']=0;
                    $refund[]=$moneyinfo;
                    $val['this_num']=$val['this_num']-$needmoney['need_money'];
                }
             }
             $data[$val['uid']][] = $val['this_num'];
        } 
        if(!empty($refund)){
             $time=time();
             $nc_list->setDbConf('main', 'refund');
            foreach($refund as $v){
                $v['rt']=$time;
                $v['ut']=$time;
                $v['pay_type']=1;
                $nc_list->insertData($v);
            }
        }
        
         
     
        $activityData=Factory::getData('activity');
        foreach($data as $uid=>$money){
          $usql = "update  ".DATABASE.".t_team_member set `status`=1 where `teamwar_id`={$a_id} and uid={$uid}"; 
          $nc_list->executeSql($usql);  
            $total = array_sum($money);
            //生成退款明细
            $activityData->refund($uid,$total);
            //更新账户余额
            $activityData->userBalance($uid,$total);
            //发送通知
            $activityData->addMsg($uid,'拼团商品:'.$res['title'],$a_id);
        }
        return array('state' => 1,'msg' => '结束成功');
    }



    /**
     * 参加过活动的商品
     * @return mixed
     */
    public function activityList(){
        $goods_data = Factory::getData('team');
        $info = $goods_data->activityList();
        return $info;
    }

    /**
     * 商品列表-批量修改
     * @param $ids
     * @param $type
     * @return array
     */
    public function modifyGoods($ids,$type){
        if(empty($ids)) return array('state' => 0,'msg' => '请至少选择一项');
        $type = in_array($type,array(1,2)) ? $type : 1;
        foreach($ids as &$id){
            $id = intval($id);
        }
        $ids = implode(',',$ids);
        $goods_data = Factory::getData('team');
        $res = $goods_data->modifyGoods($ids,$type);
        if($res){
            return array('state' => $res,'msg' => '修改成功！');
        }else{
            return array('state' =>0,'msg' => '修改失败！');
        }
    }

    /**
     * 商品列表-删除
     * @param $id
     * @return int
     */
   /* public function delGoods($id){
        if(!$id) array('state' => 0,'msg' => '删除失败');
        $goods_data = Factory::getData('team');
        $res = $goods_data->isActivity($id);
        if(!(empty($res) || $res['user_num']==0 || $res['flag'] >= 2)){
            array('state' => 0,'msg' => '删除失败,已有人购买该商品');
        }
        $state = $goods_data->delGoods($id);
        return array('state' => $state?1:0,'msg' => $state?'删除成功':'删除失败');
    }*/

    

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
        $record_data = Factory::getData('team'); 
        $info = $record_data->record($id,$page,$num,$type);
        foreach($info['list'] as &$val){
            //获取夺宝号
            $res = $record_data->getActivityNum($val['activity_id'],$val['uid']);
            $val['activity_num'] = empty($res) ? "" : $res['activity_num'];
        }
        return $info;
    }


    /**
     * 添加商品
     * @param $data
     * @param $id
     * @return array
     */
    public function saveTeamGoods($data,$id){
        $data['goods_type_id'] = intval($data['goods_type_id']);
        $data['activity_type'] = intval($data['activity_type']); 
        $data['people_num'] = intval($data['people_num']);
        $data['price'] = intval($data['price']); 
        $data['sale_num'] = intval($data['sale_num']); 
        $data['team_limit'] = intval($data['team_limit']);
        $data['single_price'] = intval($data['single_price']); 
        if($data['activity_type']==2 && $data['price']%$data['people_num']!=0){
            return array('state' => 0,'msg' => '拼团人数必须能被商品价格整除!');
        }
        /*if(!$data['activity_type']){
            return array('state' => 0,'msg' => '请选择商品专区');
        }
        if(!$data['goods_type_id']){
            return array('state' => 0,'msg' => '请选择商品类型');
        }*/


       /* if(!$data['need_num']){
            return array('state' => 0,'msg' => '参与人数不能为零或空');
        }
        if(!$data['value']){
            return array('state' => 0,'msg' => '商品价值不能为空');
        }
        if(empty($data['title'])){
            return array('state' => 0,'msg' => '请输入商品标题');
        }
        if(empty($data['sub_title'])){
            return array('state' => 0,'msg' => '请输入商品子标题');
        }
        if(mb_strlen($data['sub_title'],'utf8')>50){
            return array('state' => 0,'msg' => '子标题字数不能超过50，当前字数'.mb_strlen($data['sub_title'],'utf8'));
        }
        if(empty($data['main_img'])){
            return array('state' => 0,'msg' => '请上传展示图片');
        }
        if(empty($data['title_img'])){
            return array('state' => 0,'msg' => '请上传标题图片');
        }*/

 
        $data['appid'] = APP_ID;
        $data['rt'] = time();
        $data['goods_id'] = $id ? $id :get_auto_id(C('AUTOID_SHOP_TEAMGOODS'));
         
        $goods_data = Factory::getData('team');
        $res = $goods_data->saveGoods($data,$id);
        if($res){
            return array('state' => $res,'msg' => '保存成功！');
        }else{
            return array('state' => $res,'msg' => '保存失败！');
        }
    }

    public function showindex($id,$index){
        if(!$id) return array();
        $goods_data = Factory::getData('team');
        $res =  $goods_data->showindex($id,$index);
        if($res){
            return array('state' => $res,'msg' => '操作成功！');
        }else{
            return array('state' => $res,'msg' => '操作失败！');
        }
    }

    


    /**
     * 商品开始第一期
     * @param $id
     * @return int
     */
    public function startFirst($id){
        if(!$id) return 0;
        $goods_data = Factory::getData('team');
        $info = $goods_data->getGoods($id);
        if(empty($info)) return 0;
        $time = time();
        $activity_id =  get_auto_id(C('AUTOID_SHOP_ACTIVITY'));
        $data = array(
            'activity_id' => $activity_id,
            'appid' => APP_ID,
            'goods_id' => $info['goods_id'],
            'need_num' => $info['need_num'],
            'is_false' => $info['is_auto_false'],
            'rt' => $time,
            'ut' => $time,
            'is_luan' => 1,
        );
        $re = $goods_data->startFirst($data);
        if(!$re) return false;
        //循环生成号码数据
        $num = array();
        for($i=0;$i<$data['need_num'];$i++){
            $num[] = array(
                'num'=>bcadd(10000001,$i),
                'activity_id' => $activity_id,
            );
            if(count($num)>500){
                shuffle($num);
                $goods_data->insertNumData($num);
                $num = array();
            }
        }
        if(!empty($num)){
            shuffle($num);
            $goods_data->insertNumData($num);
        }

        return $re;
    }

  


       /**
     * 订单列表
     * @param int $page
     * @param string $num
     * @param string $state
     * @return mixed
     */
    public function order($page=1,$num='',$state=''){
        $teamData=Factory::getData('team');
        $info = $teamData->order($page,$num,$state);
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
     * 旧百团订单列表
     * @param int $page
     * @param string $num
     * @param string $state
     * @return mixed
     */
    public function baituan_order($page=1,$num='',$state=''){
        $teamData=Factory::getData('team');
        $info = $teamData->baituan_order($page,$num,$state);
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
     * 删除商品分类
     * @param $ids
     * @return int
     */
    public function delGoodsCfy($ids){
        if(empty($ids)) return 0;
        $goods_data = Factory::getData('team');
        return $goods_data->delGoodsCfy($ids);
    }

    /**
     * 获取商品分类信息
     * @param $id
     * @return array
     */
    public function getGoodsCfy($id){
        if(!$id) return array();
        $goods_data = Factory::getData('team');
        return $goods_data->getGoodsCfy($id);
    }

    /**
     * 添加或编辑商品分类
     * @param $name
     * @param $url
     * @param $id
     * @return array
     */
    public function editGoodsCfy($name,$url,$id,$pid,$sort){
        if(empty($name)) return array('state' => 0,'msg' => '请输入分类名称');
        //if(empty($url)) return array('state' => 0,'msg' => '请上传分类图片');
        $goods_data = Factory::getData('team');
        $data = array('appid' =>APP_ID ,'name' => $name,'img' => $url,'rt' => time(),'father_id'=>$pid,'sort'=>$sort);
 
        $res = $goods_data->editGoodsCfy($data,$id);
        if($res > 0){
            return array('state' => $res,'msg' => '提交成功！');
        }else{
            return array('state' => $res,'msg' => '提交失败！');
        }
    }


}