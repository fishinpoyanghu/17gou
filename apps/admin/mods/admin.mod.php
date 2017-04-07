<?php
/**
 * @author wangyihuang
 */

class AdminMod extends BaseMod {
	
	/**
	 * 通过uid获得一条管理用户信息
	 * @param int $admin_id
	 */
	public function getAdmin($admin_id) {
		
		$admin_data = Factory::getData('admin');
		
		$admin = $admin_data->getAdmin($admin_id);
		
		return $admin;
	}
	
	/**
	 * 通过一定条件获取管理员列表
	 *
	 * @param array $where
	 * @param int $start
	 * @param int $pagesize
	 */
	public function getAdminList($where, $start=0, $pagesize=20) {
	
		$admin_data = Factory::getData('admin');
		
		$adminList = $admin_data->getAdminList($where, $start, $pagesize);
		
		return $adminList;
	}
	
	/**
	 * 创建一个管理用户
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function createAdmin($data) {
	
		$admin_data = Factory::getData('admin');
		
		$ret = $admin_data->createAdmin($data);
			
		return $ret;
	}
	
	/**
	 * 更新一个管理用户
	 *
	 * @param int $admin_id
	 * @param array $data
	 */
	public function updateAdmin($admin_id, $data) {
	
		$admin_data = Factory::getData('admin');
		
		$ret = $admin_data->updateAdmin($admin_id, $data);
			
		return $ret;
	}

	/**
	 * 管理员列表
	 * @param int $page
	 * @param string $num
	 * @return mixed
	 */
	public function adminList($page=1,$num=''){
		$admin_data = Factory::getData('admin');
		return $admin_data->adminList($page,$num);
	}

	/**
	 * 添加管理员
	 * @param $data
	 * @return array
	 */
	public function addAdmin($data){
		if(!preg_match('/^1\d{10}$/',$data['name'])){
			return array('state' => 0,'msg' => '账号只能是手机号');
		}
		//密码
		if(preg_match('/[a-zA-Z]/',$data['password'])==0){
			return array('state' => false,'msg'=>'密码必须包含字母');
		}
		if(preg_match('/[0-9]/',$data['password'])==0){
			return array('state' => false,'msg'=>'密码必须包含数字');
		}
		if(strlen($data['password']) < 6){
			return array('state' => false,'msg'=>'密码不能低于6位');
		}
		//权限
		$data['type'] = in_array($data['type'],array(0,1)) ? $data['type'] : 0;
		$data['password'] = md5(md5($data['name'].LOGIN_KEY.$data['password']));
		$data['rt'] = time();
		$admin_data = Factory::getData('admin');
		$res = $admin_data->addAdmin($data);
		if($res){
			return array('state' => $res,'msg' => '添加成功');
		}else{
			return array('state' => 0,'msg' => '添加失败');
		}
	}

	/**
	 * 修改管理员账户信息
	 * @param $data
	 * @return int
	 */
	public function modifyAdmin($data){
		if(!$data['id'] || !in_array($data['type'],array(0,1))) return 0;
		$admin_data = Factory::getData('admin');
		return $admin_data->modifyAdmin($data);
	}
	public function userList($page,$num,$sarchArr){
		 $page = $page < 1 ? 1 : $page;
        $admin = Factory::getData('admin');
       return $admin->userList($page,$num,$sarchArr);
	}
}
