<?php
/**
 * @since 2016-01-29
 */
class NcGoodsMod extends BaseMod{
	
	/**
	 * 幸运号计算详情
	 */
	public function getLuckyNumDetail($base, $ipt_list){
		$nc_list = Factory::getMod('nc_list');
		$where = array(
			'activity_id' => $ipt_list['activity_id']
		);
		$column = array(
			'lucky_num','time_sum','lottery_num','need_num','user_record','lottery_num'
		);
		$nc_list->setDbConf('shop', 'lucky_num');
		//获取该期结果
		$data = $nc_list->getDataOne($where, $column);
		$result = array();
		$result['value_a'] = $data['time_sum'];
		$result['need_num'] = intval($data['need_num']);
        $result['value_b'] = 0;
		if(empty($data['lottery_num'])){//如果没有时彩号，说明该期还未揭晓
			$result['status'] = 1;
		}else{
			$result['status'] = 2;
            $result['value_b'] = intval($data['lottery_num']);
        }
        if($data['lucky_num']){
            $result['status'] = 2;
            $result['lucky_num'] = $data['lucky_num'];
        }else{
            $result['status'] = 1;

        }

		if(!empty($data['user_record'])){
			//该期结束时，最后的五十个参与记录
			//记录格式：id、uid、time
			$user_record = json_decode($data['user_record'], true);
			$aDetail = array();
			$uid_numid = $uids = array();
			foreach($user_record as $val){
                $time = substr($val['rt'],0,4) .'-'. substr($val['rt'],4,2) .'-'. substr($val['rt'],6,2) .' '. substr($val['rt'],8,2).':'.substr($val['rt'],10,2).':'.substr($val['rt'],12,2).'.'.substr($val['rt'],14);
                $aDetail[$val['id']] = array(
                    'time' => $time,
                    'activity_id' => $val['activity_id'],
                    'num' => $val['num'],
                    'title' => $val['title'],
                );
				$uid_numid[$val['uid']][] = $val['id']; 
			}
			$uids = array_keys($uid_numid);
			$where = array(
				'uid' => array(
					$uids,'in'
				)
			);
			$column = array(
				'nick','uid'
			);
			$nc_list->setDbConf('main', 'user');
			$userInfo = $nc_list->getDataList($where, $column);
			foreach($userInfo as $val){
				foreach($uid_numid[$val['uid']] as $id){
					$aDetail[$id]['unick'] = $val['nick'];
					$aDetail[$id]['uid'] = $val['uid'];
				}
			}
			$result['a_detail'] = $nc_list->toArray($aDetail);
		}
		return $result;
	}
}