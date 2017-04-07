<?php
/**
 * @since 2016-01-06
 * note 搜索相关
 */
class NcSearchMod extends BaseMod{
	
	/**
	 * 获取关键字
	 */
	public function getWord($base, $ipt_list){
		$nc_list_mod = Factory::getMod('nc_list');
		$tenTime = strtotime('-5 days');
		$where = array(
			'stat' => 0,
			'appid' => $base['appid']
		);
		if(!empty($ipt_list['keywords'])){
			$where['title'] = array(
				'%'.$ipt_list['keywords'].'%','like'
			);
		}
		$where = safe_db_data($where);
		$whereSql = parse_where($where);
		
		$sql = "select distinct title from ".DATABASE.".t_goods {$whereSql} order by weight desc limit 0,5";

		$nc_list_mod->setDbConf('shop', 'goods');
		$titles = $nc_list_mod->getDataBySql($sql);
		$result = array();
		foreach($titles as $val){
			$result[] = $val['title'];
		}
		return $result;
	}

	public function gethotword($base, $ipt_list){
		$nc_list_mod = Factory::getMod('nc_list');  
		if($ipt_list['productType']==1){
			$where = array(
			 'stat' => 0, 
			);
			 $tablename='t_goods';
		  $titles=array('小米手机','手机','电视','小米','零食','跑车','方便面','吹风机','手表','相机','佳能','电脑','首饰','礼物');
		}else{ //拼团
			$where = array(
			'is_in_activity' => 2, 
			'status'=>1
			); 
		  $tablename='t_team_goods';
		  $titles=array('电脑炉','牙膏','睡衣','内衣','洗发露','吃的','学生用品','水果');
		}
 
		// if(!empty($ipt_list['keywords'])){
		// 	$where['title'] = array(
		// 		'%'.$ipt_list['keywords'].'%','like'
		// 	);
		// }
/*		$where = safe_db_data($where);
		$whereSql = parse_where($where);
		
		$sql = "select distinct title from ".DATABASE.".{$tablename} {$whereSql} order by weight desc limit 0,20";
		 
		$nc_list_mod->setDbConf('shop', 'goods');
		$titles = $nc_list_mod->getDataBySql($sql);*/
		$result = array();
		foreach($titles as $val){
			$result[] = $val;
		}
		return $result;
	}

	 
}