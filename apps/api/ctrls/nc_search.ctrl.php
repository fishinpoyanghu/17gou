<?php
/**
 * @since 2016-01-06
 * note 搜索相关
 */
class NcSearchCtrl extends BaseCtrl{
	

	
	/**
	 * 获取关键字提醒
	 */
	public function word(){
		/*------测试环境下使用------*/
		//$this->test_post_data(array('keywords' => 'iphone'));
		
		//标准参数检查
		$base = api_check_base();
		$validate_cfg = array(
			'keywords' => array()
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$nc_search_mod = Factory::getMod('nc_search');
		$result = $nc_search_mod->getWord($base, $ipt_list);
		
		api_result(0, '获取成功', $result);
	}
	public function hotword(){
		$base = api_check_base();
		$validate_cfg = array( 
			 'productType' => array('api_v_numeric|1||id不合法',), 
		); 
		$ipt_list = api_get_posts($base['appid'], $validate_cfg); 
		$nc_search_mod = Factory::getMod('nc_search');
		$result = $nc_search_mod->gethotword($base, $ipt_list);
		
		api_result(0, '获取成功', $result);
	}
}