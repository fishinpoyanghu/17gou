<?php
class GoodsMod extends BaseMod{

    /**
     * 商品分类列表
     * @param int $page
     * @param string $num
     * @param string $keyword
     * @return mixed
     */
    public function goodsType($page=1,$num='',$keyword=''){
        $page = $page < 1 ? 1 : intval($page);

        $goods_data = Factory::getData('goods');
        $info = $goods_data->goodsType($page,$num,$keyword);
        return $info;
    }

    public function goodsTypeTwo(){

        $goods_data = Factory::getData('goods');
        $info = $goods_data->goodsTypeTwo();
        return $info;
    }

    public function goods($id){
        if(!$id) return array();
        $goods_data = Factory::getData('goods');
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
    public function goodsList($page=1,$num='',$keyword=''){
        $page = $page < 1 ? 1 : $page;
        $goods_data = Factory::getData('goods');
        $info = $goods_data->goodsList($page,$num,$keyword);
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
    
    public function remen($id){
        $goods_data = Factory::getData('goods');
        $info = $goods_data->remen($id);
        return $info;
    }


    /**
     * 参加过活动的商品
     * @return mixed
     */
    public function activityList(){
        $goods_data = Factory::getData('goods');
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
        $goods_data = Factory::getData('goods');
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
    public function delGoods($id){
        if(!$id) array('state' => 0,'msg' => '删除失败');
        $goods_data = Factory::getData('goods');
        $res = $goods_data->isActivity($id);
        if(!(empty($res) || $res['user_num']==0 || $res['flag'] >= 2)){
          return   array('state' => 0,'msg' => '删除失败,已有人购买该商品');
        }
        $state = $goods_data->delGoods($id);
        return array('state' => $state?1:0,'msg' => $state?'删除成功':'删除失败');
    }

    /**
     * 删除商品分类
     * @param $ids
     * @return int
     */
    public function delGoodsCfy($ids){
        if(empty($ids)) return 0;
        $goods_data = Factory::getData('goods');
        return $goods_data->delGoodsCfy($ids);
    }

    /**
     * 获取商品分类信息
     * @param $id
     * @return array
     */
    public function getGoodsCfy($id){
        if(!$id) return array();
        $goods_data = Factory::getData('goods');
        return $goods_data->getGoodsCfy($id);
    }

    /**
     * 添加或编辑商品分类
     * @param $name
     * @param $url
     * @param $id
     * @return array
     */
    public function editGoodsCfy($name,$url,$id,$pid){
        if(empty($name)) return array('state' => 0,'msg' => '请输入分类名称');
        //if(empty($url)) return array('state' => 0,'msg' => '请上传分类图片');
        $goods_data = Factory::getData('goods');
        $data = array('appid' =>APP_ID ,'name' => $name,'img' => $url,'rt' => time(),'father_id'=>$pid);
        $res = $goods_data->editGoodsCfy($data,$id);
        if($res > 0){
            return array('state' => $res,'msg' => '提交成功！');
        }else{
            return array('state' => $res,'msg' => '提交失败！');
        }
    }

    /**
     * banner列表
     * @param int $page
     * @param string $num
     * @return mixed
     */
    public function banner($page=1,$num=''){
        $page = $page < 1 ? 1 : intval($page);
        $goods_data = Factory::getData('goods');
        $info = $goods_data->banner($page,$num);
        return $info;
    }

    public function pcbanner($page=1,$num=''){
        $page = $page < 1 ? 1 : intval($page);
        $goods_data = Factory::getData('goods');
        $info = $goods_data->pcbanner($page,$num);
        return $info;
    }

    /**
     * 关闭或发布banner
     * @param $id
     * @return bool
     */
    public function editBanner($id){
        if(!$id) return false;

        $goods_data = Factory::getData('goods');
        return $goods_data->editBanner($id);
    }

    /**
     * 删除banner
     * @param $id
     * @return bool
     */
    public function delBanner($id){
        if(!$id) return false;

        $goods_data = Factory::getData('goods');
        return $goods_data->delBanner($id);
    }

    /**
     * 添加banner
     * @param $url
     * @param $name
     * @return bool
     */
    public function addBanner($url,$name){
        if(!$url) return false;
        $insert = array(
            'img' => $url,
            'cer' => $name,
            'rt' => time(),
        );
        $goods_data = Factory::getData('goods');
        return $goods_data->addBanner($insert);
    }

    /**
     * 修改banner排序
     * @param $id
     * @param $sort
     * @return array
     */
    public function sortBanner($id,$sort){
        if(!$id) return array('state' => 0,'msg' => '操作失败！');
        if(!$sort) return array('state' => 0,'msg' => '排序不能为零或空');

        $goods_data = Factory::getData('goods');
        $res = $goods_data->sortBanner($id,$sort);
        return array('state' => $res,'msg' => '排序成功！');
    }

    /**
     * 添加banner链接
     * @param $id
     * @param $goods_id
     * @return array
     */
    public function addLink($id,$goods_id,$url,$type){
        if(!$id) return array('state' => 0,'msg' => '添加失败！');
        //if(!$goods_id) return array('state' => 0,'msg' => '请选择商品！');
        $goods_data = Factory::getData('goods');
        $res = $goods_data->addLink($id,$goods_id,$url,$type);
        return array('state' => $res,'msg' => $res?'添加成功':'添加失败');
    }


    /**
     * 关闭或发布banner
     * @param $id
     * @return bool
     */
    public function editpcBanner($id){
        if(!$id) return false;

        $goods_data = Factory::getData('goods');
        return $goods_data->editpcBanner($id);
    }

    /**
     * 删除banner
     * @param $id
     * @return bool
     */
    public function delpcBanner($id){
        if(!$id) return false;

        $goods_data = Factory::getData('goods');
        return $goods_data->delpcBanner($id);
    }

    /**
     * 添加banner
     * @param $url
     * @param $name
     * @return bool
     */
    public function addpcBanner($url,$name){
        if(!$url) return false;
        $insert = array(
            'img' => $url,
            'cer' => $name,
            'rt' => time(),
        );
        $goods_data = Factory::getData('goods');
        return $goods_data->addpcBanner($insert);
    }

    /**
     * 修改banner排序
     * @param $id
     * @param $sort
     * @return array
     */
    public function sortpcBanner($id,$sort){
        if(!$id) return array('state' => 0,'msg' => '操作失败！');
        if(!$sort) return array('state' => 0,'msg' => '排序不能为零或空');

        $goods_data = Factory::getData('goods');
        $res = $goods_data->sortpcBanner($id,$sort);
        return array('state' => $res,'msg' => '排序成功！');
    }

    /**
     * 添加banner链接
     * @param $id
     * @param $goods_id
     * @return array
     */
    public function addpcLink($id,$goods_id,$url){
        if(!$id) return array('state' => 0,'msg' => '添加失败！');
        //if(!$goods_id) return array('state' => 0,'msg' => '请选择商品！');
        $goods_data = Factory::getData('goods');
        $res = $goods_data->addpcLink($id,$goods_id,$url);
        return array('state' => $res,'msg' => $res?'添加成功':'添加失败');
    }

    /**
     * 获取积分规则信息
     * @return mixed
     */
    public function getPointRule(){
        $goods_data = Factory::getData('goods');
        $data = $goods_data->getPointRule();
        $_data = array();
        foreach($data as $g){
            $_data[$g['type']] = $g;
        }
        return $_data;
    }

    /**
     * 保存积分规则
     * @param $data
     * @return array
     */
    public function savePointRule($data){
        foreach($data as &$val){
            if(trim($val['point']) == '' || $val['point']<0){
                return array('state' => 0,'msg' => '请输入完整信息！');
            }
            if(array_key_exists('limit',$val) && (trim($val['limit']) == '' || $val['point']<0)){
                return array('state' => 0,'msg' => '请输入完整信息！');
            }
            $val['point'] = intval($val['point']);
            $val['limit'] = $val['limit'] ? intval($val['limit']) : 1;
        }
        $goods_data = Factory::getData('goods');
        $res = $goods_data->savePointRule($data);
        if($res > 0){
            return array('state' => $res,'msg' => '保存成功！');
        }else{
            return array('state' => $res,'msg' => '保存失败！');
        }
    }

    /**
     * 奖品列表
     * @return mixed
     */
    public function lottery(){
        $goods_data = Factory::getData('goods');
        return $goods_data->lottery();
    }

    /**
     * 删除奖品
     * @param $id
     * @return int
     */
    public function delLottery($id){
        if(!$id) return 0;
        $data = Factory::getData('goods');
        return $data->delLottery($id);
    }

    /**
     * 添加奖品
     * @param $type
     * @param $content
     * @return array
     */
    public function addLottery($type,$content){
        if(!in_array($type,array(0,1,2))) return array('state' => 0,'msg' => '添加失败');
        if(empty($content)) return array('state' => 0,'msg' => '信息不完整');
        $data = array(
            'type' => $type,
            'name' => $type ?  ($type==1?$content:$content.' 元 '): intval($content).'积分',
            'point' => $type==1 ? 0 : intval($content),
            'percent' => 0,
            );
        $goods_data = Factory::getData('goods');
        $row = $goods_data->addLottery($data);
        if($row > 0){
            return array('state' => $row,'msg' => '添加成功');
        }else{
            return array('state' => 0,'msg' => '添加失败');
        }
    }

    /**
     * 保存抽奖设置
     * @param $data
     * @return array
     */
    public function saveLottery($data){
        $update = array();
        foreach($data as $id=>&$val){
            $val = intval($val);
            $update[] = array('id' => intval($id),'percent' => intval($val));
        }
        if(array_sum($data) > 100){
            return array('state' =>0,'msg' => '中奖率之和不能超过100');
        }
        $goods_data = Factory::getData('goods');
        $res = $goods_data->saveLottery($update);
        if($res > 0){
            return array('state' => $res,'msg' => '保存成功！');
        }else{
            return array('state' => $res,'msg' => '保存失败！');
        }
    }

    /**
     * 抽奖记录
     * @param int $page
     * @param string $num
     * @param string $keyword
     * @return mixed
     */
    public function lotteryRecord($page=1,$num='',$keyword=''){
        $page = $page < 1 ? 1 : intval($page);
        $goods_data = Factory::getData('goods');
        $info =  $goods_data->lotteryRecord($page,$num,$keyword);

        return $info;
    }


    /**
     * 添加商品
     * @param $data
     * @param $id
     * @return array
     */
    public function saveGoods($data,$id){
        $data['goods_type_id'] = intval($data['goods_type_id']);
        $data['activity_type'] = intval($data['activity_type']);
        $data['is_in_activity'] = intval($data['is_in_activity']);
        $data['need_num'] = intval($data['need_num']);
        $data['rate_percent'] = intval($data['rate_percent']);
         
       // $data['status'] = intval($data['status']); 
       // $data['original_price'] = intval($data['original_price']);  
         
        if(!$data['activity_type']){
            return array('state' => 0,'msg' => '请选择商品专区');
        }
        if(!$data['goods_type_id']){
            return array('state' => 0,'msg' => '请选择商品类型');
        }
        if(!$data['need_num']){
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
        }
        //限购需检查是否有白名单
        if($data['is_auto_false']){
            $activity = Factory::getData('activity');
            $info = $activity->getAssign();
            if(empty($info['id'])){
                return array('state' => 0,'msg' => '不可自动限购，请联系管理员添加中奖名单');
            }
        }

        $data['appid'] = APP_ID;
        $data['rt'] = time();
        $data['goods_id'] = $id ? $id :get_auto_id(C('AUTOID_SHOP_GOODS'));

        $goods_data = Factory::getData('goods');
        $res = $goods_data->saveGoods($data,$id);
        if($res){
            return array('state' => $res,'msg' => '保存成功！');
        }else{
            return array('state' => $res,'msg' => '保存失败！');
        }
    }

    public function shouin($id){
        if(!$id) return array();
        $goods_data = Factory::getData('goods');
        $res =  $goods_data->shouin($id);
        if($res){
            return array('state' => $res,'msg' => '操作成功！');
        }else{
            return array('state' => $res,'msg' => '操作失败！');
        }
    }

    public function shouout($id){
        if(!$id) return array();
        $goods_data = Factory::getData('goods');
        $res =  $goods_data->shouout($id);
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
        $goods_data = Factory::getData('goods');
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
     * 录入物流信息
     * @param $id
     * @param $code
     * @param $num
     * @return array
     */
    public function addExpress($id,$code,$num){
        if(empty($id) || empty($code) || empty($num)) return 0;
        $goods_data = Factory::getData('goods');
        $str = $code.':'.$num;
        return $goods_data->addExpress($id,$str);
    }


}