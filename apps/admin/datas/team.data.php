<?php
class TeamData extends BaseData {

	private $cfg_name='team';
	private $tbl_alias='teamwar';

	/**
	 * 分类列表
	 * @param int $page
	 * @param string $num
	 * @param string $keyword
	 * @return mixed
	 */
	 public function goodsType($page=1,$num='',$keyword=''){
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$keyword = safe_db_data($keyword);
		$limit = $num ? "limit ".($page-1)*$num.",$num" : '';
		$this->tbl_alias='team_goods_type';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$and = " and `stat` = 0";
		if($keyword){
			$and .= " and `name` = '{$keyword}'";
		}
		$sql = "select * from {$db_cfg['tbl']} where 1 {$and} order by `rt` desc {$limit}";
		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} where 1 {$and}");

		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['c'],
			'keyword' => $keyword,
		);
	}
		/**
	 * 删除商品分类
	 * @param $ids
	 * @return mixed
	 */
	public function delGoodsCfy($ids){
		$ids = safe_db_data($ids);
		$this->tbl_alias='team_goods_type';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "update {$db_cfg['tbl']} set `stat` = 1 where `goods_type_id` in ({$ids})";
		return $db_op->execute($sql);
	}

	/**
	 * 获取商品分类信息
	 * @param $id
	 * @return mixed
	 */
	public function getGoodsCfy($id){
		$id = safe_db_data($id);
        $this->tbl_alias='team_goods_type'; 
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "select `goods_type_id` as id,`name`,`img`,`sort` from {$db_cfg['tbl']} where `goods_type_id` = {$id}";
		return $db_op->queryRow($sql);
	}

	/**
	 * 添加或编辑商品分类
	 * @param $data
	 * @param string $id
	 * @return mixed
	 */
	public function editGoodsCfy($data,$id=''){
		$data = safe_db_data($data);
		$data = parse_data($data);
		$id = safe_db_data($id);
    	$this->tbl_alias='team_goods_type'; 
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		if($id){
			$sql = "update {$db_cfg['tbl']} set {$data} where `goods_type_id` = {$id}";
		}else{
			$sql = "insert into {$db_cfg['tbl']} set {$data}";
		}
		return $db_op->execute($sql);
	}
	//上主页
   public function showindex($id,$index){
   		$this->tbl_alias='team_goods_type';
        $db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
        $id = intval($id);
        $sql = "update {$db_cfg['tbl']} set `shou`=$index where `goods_type_id`={$id} limit 1";
        return $db_op->execute($sql);
    }
	/*
    public function goodsTypeTwo(){
        $db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
        $and = " and `stat` = 0";

        $sql = "select * from {$db_cfg['tbl']} where 1 {$and} order by `rt` desc ";
        $res = $db_op->queryList($sql);
        $result = array();
        $father = array();
        foreach($res as $v){
            if($v['father_id']==0){
                $father[$v['goods_type_id']] = $v['name'];
            }
        }
        foreach($res as $_v){
            if($_v['father_id']>0){
                $result[] = array(
                    'name' => $father[$_v['father_id']] .' -- '.$_v['name'],
                    'goods_type_id' => $_v['goods_type_id'],
                );
            }
        }
        return array(
            'list' => $result,
        );
    }*/

	public function goods($id){
		$this->tbl_alias = 'team_goods';
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);

		$sql = "select * from {$db_cfg['tbl']} where `goods_id` = {$id}";
		return $db_op->queryRow($sql);
	}

	/**
	 * 商品列表
	 * @param int $page
	 * @param string $num
	 * @param string $keyword
	 * @return array
	 */
	public function teamgoodsList($page=1,$num='',$keyword=''){
		$this->tbl_alias = 'team_goods';
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$keyword = safe_db_data($keyword);
		$limit = $num ? "limit ".($page-1)*$num.",$num" : '';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);

		//$and = " and a.`stat` = 0 and (a.`goods_type_id` = b.`goods_type_id` or a.`goods_type_id`=0 )";
		if($keyword){
			$and .= " and a.`title` like '%{$keyword}%'";
		}
		/*$and .= $searchArr['cate']?" and b.`goods_type_id` ={$searchArr['cate']}":'';
	 	$and .= $searchArr['activity_type']?" and a.`activity_type`={$searchArr['activity_type']}":''; */
	 	$sql = "select a.*,b.`name` from {$db_cfg['tbl']} as a left join ".DATABASE.".`t_team_goods_type` as b  ON a.`goods_type_id`=b.`goods_type_id` where 1 {$and} order by a.`rt` desc {$limit}";
 
		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} as a left join  ".DATABASE.".`t_team_goods_type` as b  ON a.`goods_type_id`=b.`goods_type_id`  where 1 {$and}");

		/*$sql = "select a.*  from {$db_cfg['tbl']} as a  where 1 {$and} order by a.`rt` desc {$limit}";

		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} as a where 1 {$and}") ;*/

		if($count['c']==0) return array();
		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['c'],
			'keyword' => $keyword,
		);
	}
	//获取商品参团状态
	public function stop($goodsid){
		$this->tbl_alias = 'teamwar';
        $id = safe_db_data($goodsid);
        $db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
        $sql = "select a.teamwar_id from {$db_cfg['tbl']} as a   left join ".DATABASE.".`t_team_goods` as b on a.`goods_id` = b.`goods_id` where b.`goods_id`='{$id}' and a.flag=1";
        $res = $db_op->queryRow($sql); 
        if($res['teamwar_id']){
            return false;
        } 
        $sql = "update ".DATABASE.".`t_team_goods`  set `status`=2 ,`is_in_activity`=1 where `goods_id`='{$id}' limit 1";
        $ret=$db_op->execute($sql);
        $sql = "update ".DATABASE.".`t_teamwar`  set `stat`=1   where `goods_id`='{$id}'  "; //修改已下架商品为1 ，用于前端开团解决团限制导致不能开团。
        $db_op->execute($sql);
        return $ret?true:false;
	}
		/**
	 * 百团大战列表
	 * @param int $page
	 * @param string $num
	 * @param string $keyword
	 * @return array
	 */
	public function activity($page=1,$num='',$keyword=''){
		$this->tbl_alias = 'teamwar';
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$keyword = safe_db_data($keyword);
		$limit = $num ? "limit ".($page-1)*$num.",$num" : '';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);

		  
		if($keyword){
			$and .= " and b.`title` like '%{$keyword}%'";
		}

		$sql = "select a.* ,b.title,c.lucky_num,d.nick from {$db_cfg['tbl']} as a   left join ".DATABASE.".`t_team_goods` as b on a.`goods_id` = b.`goods_id` left join ".DATABASE.".`t_team_lucky_num` as c on a.`teamwar_id` = c.`activity_id`  left join ".DATABASE.".`t_user` as d on c.`uid` = d.`uid` 		where 1 {$and}  group by a.teamwar_id order by a.`ut` desc {$limit}";

		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} as a where 1 {$and}") ;
		 
		if($count['c']==0) return array();
		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['c'],
			'keyword' => $keyword,
		);
	}
    
    public function remen($id){
        $this->tbl_alias = 'goods';
        $id = safe_db_data($id);
        $db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
        $sql = "select weight from {$db_cfg['tbl']} where `goods_id`='{$id}'";
        $res = $db_op->queryRow($sql);
        if($res['weight']>0){
            $weight = 0;
        }else{
            $weight = time();
        }
        $sql = "update {$db_cfg['tbl']} set `weight`={$weight} where `goods_id`='{$id}' limit 1";
        $db_op->execute($sql);
        return true;
    }


	/**
	 * 参加过活动的商品
	 * @return mixed
	 */
	public function activityList(){
		$this->tbl_alias = 'team_activity';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "select a.`goods_id`,b.`title` from {$db_cfg['tbl']} as a left join ".DATABASE.".`t_goods` as b on a.`goods_id` = b.`goods_id` group by a.`goods_id`";
		return $db_op->queryList($sql);
	}

	/**
	 * 商品列表-批量删除
	 * @param $ids
	 * @param $type
	 * @return mixed
	 */
	public function modifyGoods($ids,$type){
		$this->tbl_alias = 'goods';
		$ids = safe_db_data($ids);
		$type = safe_db_data($type);

		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "update {$db_cfg['tbl']} set `is_in_activity` = {$type} where `goods_id` in ({$ids})";
		$db_op->execute($sql);
		return 1;
	}

	/**
	 * 商品列表-删除
	 * @param $id
	 * @return mixed
	 */
	public function delGoods($id){
		$this->tbl_alias = 'goods';
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$res = $db_op->execute("update {$db_cfg['tbl']} set `stat` = 1 where `goods_id` = {$id}");
		if($res){
			$table = DATABASE.".t_activity";
			$db_op->execute("update {$table} set `stat` = 1,`flag` = 3  where `goods_id` = {$id} order by `activity_id` desc limit 1");
		}
		return $res;
	}

	  

	 
  
	/**
	 * 添加商品
	 * @param $data
	 * @param $id
	 * @return mixed
	 */
	public function saveGoods($data,$id){

		$this->tbl_alias = 'team_goods';
		$data = safe_db_data($data);
		$data = parse_data($data);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		if($id){
			$sql = "update {$db_cfg['tbl']} set {$data} where `goods_id` = {$id}";
		}else{
			$sql = "insert into {$db_cfg['tbl']} set {$data}";
		}
	 
		return $db_op->execute($sql);
	}


	/**
	 * 获取商品信息
	 * @param $id
	 * @return mixed
	 */
	public function getGoods($id){
		$this->tbl_alias = 'team_goods';
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->queryRow("select * from {$db_cfg['tbl']} where `goods_id` = {$id}");
	}
 
 

	/**
	 * 商品是否开始第一期
	 * @param $id
	 * @return mixed
	 */
	public function isActivity($id){
		$this->tbl_alias = 'team_activity';
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->queryRow("select * from {$db_cfg['tbl']} where `goods_id` = {$id} order by `rt` desc limit 1");
	}

 

	public function record($id,$page,$num,$type=false){  
		$id = safe_db_data($id);
		$num = safe_db_data($num);
		$limit = $num ? "limit ".($page-1)*$num.",$num" : '';
	/*	if($type){
			$this->tbl_alias = 'red_user';
			$order_by = "order by a.`ut` desc";
		}else{
			$this->tbl_alias = 'activity_user';
			$order_by = "order by a.`rt` desc";
		}*/

		$this->tbl_alias = 'team_member';
	    $order_by = "order by a.`rt` desc";
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$and = " and a.`teamwar_id` = {$id} and a.`uid` = b.`uid`";
		$sql = "select a.*,b.`nick` from {$db_cfg['tbl']} as a,".DATABASE.".`t_user` as b $querysql where 1 {$and} {$order_by} {$limit}";
		  
	 
		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} as a,".DATABASE.".`t_user` as b where 1 {$and}");

		if(!$count['c']) return array();  
		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['c'],
		);
	}

	/**
	 * 获取活动参与记录夺宝号
	 * @param $teamwar_id
	 * @param $uid
	 * @return mixed
	 */
	public function getActivityNum($teamwar_id,$uid=''){
		$this->tbl_alias = 'team_activity_num';
		$teamwar_id = safe_db_data($teamwar_id);
		$uid = safe_db_data($uid);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$and = " and `teamwar_id` = {$teamwar_id} and `uid` = {$uid}";
		$sql = "select group_concat(activity_num) as activity_num from {$db_cfg['tbl']} where 1 {$and}";
		$res = $db_op->queryRow($sql);
		return $res;
	}
		/**
	 * 修改活动状态
	 * @param $a_id
	 * @return mixed
	 */
	public function updateActivity($a_id){
		$a_id = safe_db_data($a_id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "select a.*,b.`title` from {$db_cfg['tbl']} as a left join ".DATABASE.".`t_team_goods` as b on a.`goods_id` = b.`goods_id` where `teamwar_id` = '{$a_id}'"; 
		$activity = $db_op->queryRow($sql);
		if(empty($activity)) return false;
		//修改活动状态
		$res = $db_op->execute("update {$db_cfg['tbl']} set `flag` = 3 where `teamwar_id` = '{$a_id}'");
		$db_op->execute("update ".DATABASE.".`t_order` set `status` =-3 where `teamwar_id` = '{$a_id}'");
		/*if($res){//商品不再自动开始
			$table = DATABASE.".t_goods";
			$res = $db_op->execute("update {$table} set `is_in_activity` = 1 where `goods_id` = '{$activity['goods_id']}'");
		}*/
		return $res===false ? 0 : $activity;
	}

	/**
	 * 获取活动参与记录
	 * @param $a_id
	 * @return mixed
	 */
	public function activityNum($a_id){
		$this->tbl_alias = 'team_activity_num';
		$a_id = safe_db_data($a_id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "select * from {$db_cfg['tbl']} where `activity_id` = {$a_id} group by `order_num`";
		return $db_op->queryList($sql);
	}
	/**
	 * 订单列表
	 * @param $page
	 * @param $num
	 * @param $state
	 * @return array
	 */
	public function order($page,$num,$state){ 
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$limit = $num ? "limit ".($page-1)*$num.",$num" : '';
		$db_cfg = load_db_cfg('shop','order', '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$and = " and a.`goods_type` > 1";//商品类型1 是原1元购 ,2 表示参团 3表示开团 4表示拼团单独购买 5 表示开团（免费开）
		$and=" and a.flag=1";
		if($state==1){
			$and .= " and b.`logistics_stat` = 0";
		}
		if($state==2){
			$and .= " and b.`logistics_stat` = 1";
		}
		$table = "{$db_cfg['tbl']} as a
				inner join ".DATABASE.".`t_logistics` as b on a.`order_num` = b.`order_num`
				left join ".DATABASE.".`t_team_goods` as c on a.`order_goods_id` = c.`goods_id`
				left join ".DATABASE.".`t_user` as d on a.`uid` = d.`uid`";
		$sql = "select  a.`rt` as t,b.*,c.`title`,d.`nick` from {$table} where 1 {$and} group by b.logistics_id order by a.`rt` desc {$limit}";
		 
		$count = $db_op->queryRow("select count(*) as total from {$table} where 1 {$and}");
		 
		if($count['total']==0) return array();
		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['total'],
		);
	}
	//旧百团订单
	public function baituan_order($page,$num,$state){
		$this->tbl_alias = 'team_lucky_num';
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$limit = $num ? "limit ".($page-1)*$num.",$num" : '';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$and = " and a.`stat` = 0";
		if($state==1){
			$and .= " and b.`logistics_stat` = 0";
		}
		if($state==2){
			$and .= " and b.`logistics_stat` = 1";
		}
		$table = "{$db_cfg['tbl']} as a
				left join ".DATABASE.".`t_logistics` as b on a.`activity_id` = b.`teamwar_id`
				left join ".DATABASE.".`t_team_goods` as c on a.`goods_id` = c.`goods_id`
				left join ".DATABASE.".`t_user` as d on a.`uid` = d.`uid`";
		$sql = "select a.`lucky_num_id`,a.`rt` as t,b.*,c.`title`,d.`nick` from {$table} where 1 {$and} group by b.logistics_id order by a.`rt` desc {$limit}";
		 
		$count = $db_op->queryRow("select count(*) as total from {$table} where 1 {$and}");
		 
		if($count['total']==0) return array();
		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['total'],
		);
	}


}