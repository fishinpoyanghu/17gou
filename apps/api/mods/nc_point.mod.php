<?php
/**
 * @since 2016-01-05
 */
class NcPointMod extends BaseMod{
	
	/**
	 * 生成注册
	 */
	public function createRegPoint($userData){
		//这里是产品定的规则
		$currentTime = time();
        $nc_list_mod = Factory::getMod('nc_list');
        $nc_list_mod->setDbConf('shop', 'point_rule');
        $where = array('type'=>'登录');
        $ret = $nc_list_mod->getDataOne($where, array(), array(), array(), false);
		$insertData = array(
			'uid' => $userData['uid'],
			'point' => $ret['point'],
			'desc' => '首次登录',
			'ut' => $currentTime,
		);
        $insertData2 = array(
            'uid' => $userData['uid'],
            'point' => $ret['point'],
            'total' => $ret['point'],
            'use' => 0,
            'ut' => $currentTime,
        );
		$nc_list_mod->setDbConf('shop', 'point_detail');
		$nc_list_mod->insertData($insertData);
        $nc_list_mod->setDbConf('shop', 'point');
        $nc_list_mod->insertData($insertData2);
    }

}