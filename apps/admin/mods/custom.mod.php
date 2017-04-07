<?php
class CustomMod extends BaseMod{
    private $data;
    public function __construct(){
        $this->data = Factory::getData('custom');
    }

    /**
     * 获取所有商品列表
     * @return mixed
     */
    public function allGoodsList(){
        return $this->data->allGoodsList();
    }

    public function getInfo(){
        $info = $this->data->getInfo();
        $info['goods'] = explode(',',$info['goods']);
        $info['hour'] = explode(',',$info['hour']);
        return $info;
    }

    /**
     * 保存刷单设置
     * @param $data
     * @return array
     */
    public function save($data){
        if($data['state'] == 1){
            if($data['jiange1'] === '' || $data['jiange2'] === ''){
                return array('state' => 0,'msg' => '请输入间隔时间');
            }
            if(empty($data['hour'])){
                return array('state' => 0,'msg' => '请至少选择一个刷单时间');
            }
            if($data['stop'] === ''){
                return array('state' => 0,'msg' => '请输入停止刷单是份数');
            }
            if(empty($data['goods'])){
                return array('state' => 0,'msg' => '请至少选择一个刷单的商品');
            }
            if(count($data['goods'])>10){
                return array('state' => 0,'msg' => '最多选择10个商品');
            }
        }
        $goods_id = explode(',',$data['goods']);
        foreach($goods_id as &$id){
            $id = intval($id);
        }
        $time = explode(',',$data['hour']);
        foreach($time as &$t){
            $t = intval($t);
        }
        $data['hour'] = implode(',',$time);
        $data['goods'] = implode(',',$goods_id);
        $state = $this->data->save($data);
        return array('state' => $state,'msg' => $state?"保存成功":"保存失败");
    }

}