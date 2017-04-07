<?php
/**
 * @since 2016-01-06
 * note 商品列表相关
 */
class NcActivityCtrl extends BaseCtrl{


	/**
	 * 商品类别
	 */
	public function categoryList(){
		/*------测试环境下使用------*/
		//$this->test_post_data(array('goods_id' => 1, 'from' => 1, 'count' => 10));

		//标准参数检查
		$base = api_check_base();

		$nc_activity_mod = Factory::getMod('nc_activity');
		$result = $nc_activity_mod->getCategoryList($base);

		if(empty($result)){
			api_result(0, '数据为空');
		}
		api_result(0, '获取成功', $result);
	}

	/**
	 * 商品列表
	 */
	public function activityList(){
		/*------测试环境下使用------*/
		//$this->test_post_data(array('status' => 3, 'from' => 1, 'count' => 10));

		//标准参数检查
		$base = api_check_base();

		$validate_cfg = array(
			'goods_type_id' => array(),
			'key_word' => array(),
			'order_key' => array(),
			'order_type' => array(),
			'activity_type' => array(),
			'status' => array(),
            'from' => array(),
            'count' => array(),
		);

		$ipt_list = api_get_posts($base['appid'], $validate_cfg);

		$nc_activity_mod = Factory::getMod('nc_activity');
		$result = $nc_activity_mod->getActivityList($base, $ipt_list);
		api_result(0, '获取成功', $result);
	}

	/**
	 * 活动信息
	 */
	public function activityInfo(){
		/*------测试环境下使用------*/
		//$this->test_post_data(array('activity_id' => 1));

		//标准参数检查
		$base = api_check_base();
		//设置参数
		$validate_cfg = array(
			'activity_id' => array(
				'api_v_numeric|1||activity_id不合法',
			)
		);

		$ipt_list = api_get_posts($base['appid'], $validate_cfg);

		$nc_activity_mod = Factory::getMod('nc_activity');
		$result = $nc_activity_mod->getActivityInfo($base['appid'], $ipt_list['activity_id']);

		api_result(0, '获取成功', $result);
	}

    public function luckyInfo(){
        //标准参数检查
        $base = api_check_base();
        $nc_activity_mod = Factory::getMod('nc_activity');
        $result = $nc_activity_mod->getluckyInfo($base['appid']);
        //var_dump($result);exit;
        api_result(0, '获取成功', $result);
    }

    public function remen(){
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'goods');
        $sql = "select 1 as join_number,a.title as goods_title,a.sub_title as goods_subtitle,a.main_img as goods_img,b.goods_id,b.need_num,a.activity_type,b.activity_id,b.process,(b.need_num - b.user_num) as remain_num ,b.flag as status from {$nc_list->dbConf['tbl']} a,".DATABASE.".`t_activity` b where a.`weight`>0 and b.flag=0 and b.goods_id=a.goods_id order by a.`weight` desc limit 5";
        $res = $nc_list->getDataBySql($sql);
        foreach($res as &$a){
            $b = explode(',',$a['goods_img']);
            $a['goods_img'] = $b[0];
        }
        api_result(0,'succ',$res);
    }
}