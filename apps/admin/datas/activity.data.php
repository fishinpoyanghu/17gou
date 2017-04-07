<?php
class ActivityData extends BaseData {

	private $cfg_name='shop';
	private $tbl_alias='activity';

	/**
	 * 活动记录
	 * @param $type
	 * @param $page
	 * @param $num
	 * @return array
	 */
	public function activity($type,$page,$num){
		$type = safe_db_data($type);
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$limit = $num ? "limit ".($page-1)*$num.",$num" : '';

		$res = $this->getActivitySql($type,$limit);

		$db_op = DbOp::getInstance($res['db_cfg']);
		$count = $db_op->queryRow($res['c_sql']);
		if($count['total']==0) return array();
		return array(
			'list' => $db_op->queryList($res['sql']),
			'total' => $count['total']
		);
	}

	/**
	 * 获取活动列表sql
	 * @param $type
	 * @param $limit
	 * @return array
	 */
	private function getActivitySql($type,$limit){
		$and = " and a.`stat` = 0 ";
		//红包中奖记录 
		if(0){ //状态4已改为二人购
			$this->tbl_alias = 'red_activity';
			$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
			$table = " {$db_cfg['tbl']} as a left join ".DATABASE.".`t_red` as b on a.`red_id` = b.`red_id` ";
			$table .= " left join ".DATABASE.".`t_user` as c on a.`result_uid` = c.`uid` ";
			$sql = "select a.*,a.`result_num` as lucky_num,b.`title`,c.`nick` from {$table} where 1 {$and} order by a.`flag` asc,a.`ut` desc {$limit}";
		}else{
			$and .= " and b.`activity_type` = {$type}";
			$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
			$table = " {$db_cfg['tbl']} as a left join ".DATABASE.".`t_goods` as b on a.`goods_id` = b.`goods_id` ";
			$table .= " left join ".DATABASE.".`t_lucky_num` as c on a.`activity_id` = c.`activity_id` ";
			$table .= " left join ".DATABASE.".`t_user` as d on c.`uid` = d.`uid` ";
			$sql = "select a.*,b.`title`,c.`lucky_num`,d.`nick` from {$table} where 1 {$and} order by a.`flag` asc,a.`ut` desc {$limit}";
		}
		//count_sql
		$c_sql = "select count(*) as total from {$table} where 1 {$and}";
		return array('sql' => $sql,'c_sql' => $c_sql,'db_cfg' => $db_cfg);
	}


	/**
	 * 指定中奖or取消指定
	 * @param $id
	 * @return mixed
	 */
	public function assign($id){
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->execute("update {$db_cfg['tbl']} set `is_false` = abs(`is_false`-1) where `activity_id` = {$id}");
	}

	/**
	 * 批量指定中奖
	 * @param $id
	 * @return int
	 */
	public function multiAssign($id){
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$db_op->execute("update {$db_cfg['tbl']} set `is_false` = 1 where `activity_id` in ({$id})");
		return 1;
	}

	/**
	 * 红包列表
	 * @param $page
	 * @param $num
	 * @return array
	 */
	public function redList($page,$num){
		$this->tbl_alias = 'red';
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$limit = $num ? "limit ".($page-1)*$num.",$num" : '';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);

		$sql = "select * from {$db_cfg['tbl']} where `stat` = 0 order by `rt` desc {$limit}";
		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} where `stat` = 0");
		if($count['c']==0) return array();
		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['c'],
		);
	}

	/**
	 * 批量需改红包
	 * @param $id
	 * @param $type
	 * @return int
	 */
	public function modifyRed($id,$type){
		$this->tbl_alias = 'red';
		$ids = safe_db_data($id);
		$type = safe_db_data($type);

		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "update {$db_cfg['tbl']} set `is_in_activity` = {$type} where `red_id` in ({$ids})";
		$db_op->execute($sql);
		return 1;
	}


	/**
	 * 删除红包
	 * @param $id
	 * @return mixed
	 */
	public function delRed($id){
		$this->tbl_alias = 'red';
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->execute("update {$db_cfg['tbl']} set `stat` = 1 where `red_id` = {$id}");
	}

	/**
	 * 获取红包信息
	 * @param $id
	 * @return mixed
	 */
	public function getRed($id){
		$this->tbl_alias = 'red';
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->queryRow("select * from {$db_cfg['tbl']} where `red_id` = {$id} and `stat` = 0");
	}

	/**
	 * 添加or编辑红包
	 * @param $data
	 * @param string $id
	 * @return mixed
	 */
	public function addRed($data,$id=''){
		$this->tbl_alias = 'red';
		$id = safe_db_data($id);
		$data = safe_db_data($data);
		$data = parse_data($data);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);

		if($id){
			$sql = "update {$db_cfg['tbl']} set {$data} where `red_id` = {$id}";
		}else{
			$sql = "insert into {$db_cfg['tbl']} set {$data}";
		}
		$db_op->execute($sql);
		return 1;
	}

	/**
	 * 红包开始第一期
	 * @param $data
	 * @return mixed
	 */
	public function redStart($data){
		$this->tbl_alias = 'red_activity';
		$data = safe_db_data($data);
		$red_id = $data['red_id'];
		$data = parse_data($data);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$res = $db_op->queryRow("select * from {$db_cfg['tbl']} where `red_id` = {$red_id} and `flag` = 0");
		if($res){
			return true;
		}
		return $db_op->execute("insert into {$db_cfg['tbl']} set {$data}");
	}

	/**
	 * 红包是否开始第一期
	 * @param $id
	 * @return mixed
	 */
	public function isActivity($id){
		$this->tbl_alias = 'red_activity';
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->queryRow("select `activity_id` from {$db_cfg['tbl']} where `red_id` = {$id}");
	}

	/**
	 * 晒单列表 评论审核列表
	 * @param string $state
	 * @param int $page
	 * @param string $num
	 * @param bool|true $type
	 * @return array
	 */
	public function show($state='',$page=1,$num='',$type=true){
		$this->tbl_alias =  $type ? 'show' : 'show_comment';
		$state = safe_db_data($state);
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$limit = $num ? "limit ".($page-1)*$num.",$num" : '';
		$and = " and a.`stat` != 1 and a.`uid` = b.`uid`";
		if($state==1){
			$and .= " and a.`stat` = 0";
		}
		if($state==2){
			$and .= " and a.`stat` = 3";
		}

		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "select a.*,b.`nick` from {$db_cfg['tbl']} as a,".DATABASE.".`t_user` as b where 1 {$and} order by a.`rt` desc {$limit}";
		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} as a,".DATABASE.".`t_user` as b where 1 {$and}");

		if($count['c']==0) return array();
		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['c'],
		);
	}

	/**
	 * 晒单or评论 审核、删除
	 * @param $id
	 * @param $stat
	 * @param $type
	 * @return mixed
	 */
	public function modifyShow($id,$stat,$type){
		$this->tbl_alias = $type ? 'show' : 'show_comment';
		$and = $type ? "`show_id`" : "`id`";
		$id = safe_db_data($id);
		$stat = safe_db_data($stat);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
        $sql = "select `stat`,`uid`,`show_title` from {$db_cfg['tbl']} where {$and} = {$id}";
        $res = $db_op->queryRow($sql);
        if($type && $stat==2){
            if($res['stat']==0 && !empty($res)){
                //jifen
                $nc_list = Factory::getMod('nc_list');
                $nc_list->setDbConf('shop', 'point_rule');
                $where = array('type'=>'晒单');
                $ret = $nc_list->getDataOne($where, array(), array(), array(), false);
                $time = time();
                $point = $ret['point'];
                if($point>0){
                    $nc_list->setDbConf('shop', 'point_detail');
                    $nc_list->insertData(array(
                        'uid' => $res['uid'],
                        'desc' => '晒单',
                        'point' => $point,
                        'ut' => $time,
                    ));
                    $nc_list->setDbConf('shop', 'point');
                    $sql = "insert into {$nc_list->dbConf['tbl']} (`uid`,`point`,`total`,`use`,`ut`) values({$res['uid']},{$point},{$point},0,{$time}) on duplicate key update `point`=`point`+{$point},`total`=`total`+{$point},`ut`={$time}";
                    $nc_list->executeSql($sql);
                }
            }
        }
        if($type && $stat==3){
            if($res['stat']==0 && !empty($res)){
                //send
                $msg_mod = Factory::getMod('msg');
                $msg_mod->sendNotify(10002, $res['uid'], 10002, 5, 0, 7, '很抱歉，您的晒单【'.$res['show_title'].'】没有通过审核！');
            }
        }
		return $db_op->execute("update {$db_cfg['tbl']} set `stat` = {$stat} where {$and} = {$id}");
	}

	/**
	 * 晒单or评论 批量删除
	 * @param $ids
	 * @param $type
	 * @return mixed
	 */
	public function multiShowDel($ids,$type){
		$this->tbl_alias = $type ? 'show' : 'show_comment';
		$and = $type ? "`show_id`" : "`id`";
		$ids = safe_db_data($ids);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->execute("update {$db_cfg['tbl']} set `stat` = 1 where {$and} in ({$ids})");
	}

	/**
	 * 订单列表
	 * @param $page
	 * @param $num
	 * @param $state
	 * @return array
	 */
	public function order($page,$num,$state){
		$this->tbl_alias = 'lucky_num';
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
				left join ".DATABASE.".`t_logistics` as b on a.`activity_id` = b.`activity_id`
				left join ".DATABASE.".`t_goods` as c on a.`goods_id` = c.`goods_id`
				left join ".DATABASE.".`t_user` as d on a.`uid` = d.`uid`";
		$sql = "select a.`lucky_num_id`,a.`rt` as t,b.*,c.`title`,d.`nick` from {$table} where 1 {$and} order by a.`rt` desc {$limit}";
		 
		$count = $db_op->queryRow("select count(*) as total from {$table} where 1 {$and}");
		if($count['total']==0) return array();
		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['total'],
		);
	}

	/**
	 * 指定中奖名单列表
	 * @param $page
	 * @param $num
	 * @return array
	 */
	public function assignList($page,$num,$award){
		$this->tbl_alias = 't_false';
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$limit = $num ? "limit ".($page-1)*$num.",$num" : '';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
        if($award){
            $sql = "select a.*,b.`nick`,c.`uid` from {$db_cfg['tbl']} as a,".DATABASE.".`t_user` as b , ".DATABASE.".`t_lucky_num` as `c` where a.`uid` = b.`uid` and a.`uid`=c.`uid` order by `ut` desc {$limit}";
            $count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} as a,".DATABASE.".`t_user` as b,".DATABASE.".`t_lucky_num` as `c` where a.`uid` = b.`uid` and a.`uid`=c.`uid` ");
        }else {
		$sql = "select a.*,b.`nick` from {$db_cfg['tbl']} as a,".DATABASE.".`t_user` as b where a.`uid` = b.`uid` order by `ut` desc {$limit}";
		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} as a,".DATABASE.".`t_user` as b where a.`uid` = b.`uid`");
        }
		if(!$count['c']) return array();
		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['c'],
		);
	}

	/**
	 * 是否存在指定名单
	 * @return mixed
	 */
	public function getAssign(){
		$this->tbl_alias = 't_false';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->queryRow("select `id` from {$db_cfg['tbl']} where `stat` = 0 limit 1");
	}

	/**
	 * 中奖记录
	 * @param $uid
	 * @return string
	 */
	public function getLuckyNum($uid){
		$this->tbl_alias = 'lucky_num';
		$uid = safe_db_data($uid);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$res = $db_op->queryList("select `activity_id` from {$db_cfg['tbl']} where `uid` = {$uid}");
		$str = "";
		foreach($res as $v){
			$str .= $v['activity_id'].'；';
		}
		return trim($str,'；');
	}

	/**
	 * 开启或关闭指定中奖
	 * @param $id
	 * @return mixed
	 */
	public function modifyAssign($id){
		$this->tbl_alias = 't_false';
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->execute("update {$db_cfg['tbl']} set `stat` = abs(`stat` - 1) where `id` = {$id}");
	}

	/**
	 * 添加用户
	 * @param $info
	 * @return mixed
	 */
	public function addUser($info){
		$this->cfg_name = 'main';
		$this->tbl_alias = 'user';
		$info = safe_db_data($info);
		$info = parse_data($info);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->execute("insert into {$db_cfg['tbl']} set {$info}");
	}

	/**
	 * 添加指定中奖名单
	 * @param $info
	 * @return mixed
	 */
	public function addAssign($info){
		$this->cfg_name = 'shop';
		$this->tbl_alias = 't_false';
		$info = safe_db_data($info);
		$info = parse_data($info);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->execute("insert into {$db_cfg['tbl']} set {$info}");
	}

	/**
	 * 录入物流信息
	 * @param $id
	 * @param $code
	 * @param $num
	 * @return mixed
	 */
	public function addExpress($id,$code,$num,$ordernum,$addr){
		$this->tbl_alias = 'logistics';
		$id = safe_db_data($id);
		$code = safe_db_data($code);
		$num = safe_db_data($num);
		$ordernum = $ordernum?safe_db_data($ordernum):'';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "update {$db_cfg['tbl']} set `logistics_type` = '{$code}',`logistics_num` = '{$num}', `address`='{$addr}',`logistics_stat` = 1 where `logistics_id` = {$id}"; 
		
		 $ret=$db_op->execute($sql);
		if($ordernum){
			$sql = "update ".DATABASE.".`t_order`  set `status` = 4    where `order_num` = '{$ordernum}'";  
			 $db_op->execute($sql);
		}
		return  $ret;
	}

	/**
	 * 活动参与记录
	 * @param $id
	 * @param $page
	 * @param $num
	 * @param bool|false $type
	 * @return array
	 */
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

		$this->tbl_alias = 'activity_user';
	    $order_by = "order by a.`rt` desc";
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$and = " and a.`activity_id` = {$id} and a.`uid` = b.`uid`";
		$sql = "select a.*,b.`nick` from {$db_cfg['tbl']} as a,".DATABASE.".`t_user` as b $querysql where 1 {$and} {$order_by} {$limit}";
		if($type==6){ //幸运购
		$sql  =" SELECT distinct(o.order_id),a.uid,a.user_num,a.activity_id,b.`nick`,o.order_info,o.rt FROM 17gou.`t_user` AS b ,".DATABASE."  .t_activity_user AS a  LEFT JOIN ".DATABASE.".t_order o ON o.`order_aid`=a.`activity_id` AND o.flag=1 and o.uid=a.uid WHERE  1  {$and} group by(o.order_id) {$order_by} {$limit}";
		 //echo $sql;exit;
		}
	 
		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} as a,".DATABASE.".`t_user` as b where 1 {$and}");

		if(!$count['c']) return array();  
		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['c'],
		);
	}

	/**
	 * 获取活动参与记录夺宝号
	 * @param $activity_id
	 * @param $uid
	 * @return mixed
	 */
	public function getActivityNum($activity_id,$uid=''){
		$this->tbl_alias = 'activity_num';
		$activity_id = safe_db_data($activity_id);
		$uid = safe_db_data($uid);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$and = " and `activity_id` = {$activity_id} and `uid` = {$uid}";
		$sql = "select group_concat(activity_num) as activity_num from {$db_cfg['tbl']} where 1 {$and}";
		$res = $db_op->queryRow($sql);
		return $res;
	}

	/**
	 * 分佣设置信息
	 * @return mixed
	 */
	public function commission(){
		$this->tbl_alias = 'fen_xiao';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->queryList("select * from {$db_cfg['tbl']}");
	}

	/**
	 * 保存分佣设置
	 * @param $data
	 * @return int
	 */
	public function saveCom($data){
		$this->tbl_alias = 'fen_xiao';
		$data = safe_db_data($data);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		foreach((array)$data as $val){
			if($val['percent'] == '') continue;
			$sql = "insert into {$db_cfg['tbl']} (`level`,`percent`) values({$val['level']},{$val['percent']})
					ON DUPLICATE KEY update `percent` = {$val['percent']},`level` = {$val['level']}";
//			$res = $db_op->execute("update {$db_cfg['tbl']} set `percent` = {$val['percent']} where `level` = {$val['level']}");
			$res = $db_op->execute($sql);
			if($res === false) return false;
		}
		return true;
	}

	/**
	 * 分销用户列表
	 * @param $page
	 * @param $num
	 * @return array
	 */
	public function distribution($page,$num){
		$this->tbl_alias = 'money';
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$limit = $num ? "limit ".($page-1)*$num.",{$num}" : '';

		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "select a.uid,sum(a.money) as money,b.`nick`,b.`name`,b.`icon` from {$db_cfg['tbl']} as a
 				left join ".DATABASE.".`t_user` as b on a.`uid` = b.`uid`
 				where left(a.`desc`,2) = '佣金' and a.`money` > 0 group by a.`uid` order by a.`ut` desc {$limit}";
		$count = $db_op->queryRow("select count(DISTINCT `uid`) as c from {$db_cfg['tbl']} where left(`desc`,2) = '佣金' and `money` >0");
		if(!$count['c']) return array();
		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['c'],
		);
	}

	/**
	 * 合计佣金
	 * @return mixed
	 */
	public function totalMoney(){
		$this->tbl_alias = 'money';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "select sum(money) as money from {$db_cfg['tbl']} where left(`desc`,2) = '佣金'";
		$money = $db_op->queryRow($sql);
		return empty($money) ? 0 : $money['money'];
	}

	/**
	 * 分销好友数
	 * @param $uid
	 * @return mixed
	 */
	public function friendCount($uid=''){
		$this->tbl_alias = 'user';
		$uid = safe_db_data($uid);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$and = $uid ? " and `rebate_uid` = {$uid}" : " and `rebate_uid` != 0";
		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} where 1 {$and}");
		return $count['c'];
	}

	/**
	 * 分润详情
	 * @param $uid
	 * @param $page
	 * @param $num
	 * @return array
	 */
	public function profitDetail($uid,$page=1,$num=''){
		$this->tbl_alias = 'money';
		$uid = safe_db_data($uid);
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$limit = $num ? "limit ".($page-1)*$num.",{$num}" : '';

		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$and = " and `uid` = {$uid} and left(`desc`,2) = '佣金' and `money` > 0";
		$sql = "select * from {$db_cfg['tbl']} where 1 {$and} order by `ut` desc {$limit}";
		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} where 1 {$and}");
		if(!$count['c']) return array();
		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['c'],
		);
	}

	/**
	 * 保存系统消息
	 * @param $data
	 * @param $id
	 * @return int
	 */
	public function saveSysMsg($data,$id){
		$this->tbl_alias = 'msg_sys';
		$data = safe_db_data($data);
		$data = parse_data($data);
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		if($id){
			$res = $db_op->execute("update {$db_cfg['tbl']} set {$data} where `msg_sys_id` = {$id}");
			return $res==false ? 0 : 1;
		}else{
			return $db_op->execute("insert into {$db_cfg['tbl']} set {$data}");
		}
	}

	/**
	 * 系统消息列表
	 * @param $page
	 * @param $num
	 * @param $keyword
	 * @return array
	 */
	public function sysMsg($page,$num,$keyword){
		$this->tbl_alias = 'msg_sys';
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$keyword = safe_db_data($keyword);
		$limit = $num ? "limit ".($page-1)*$num.",$num" : '';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$and = " and `stat` = 0";
		if($keyword){
			$and .= " and `title` like '%{$keyword}%'";
		}
		$sql = "select * from {$db_cfg['tbl']} where 1 {$and} order by `ut` desc {$limit}";
		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} where 1 {$and}");
		if(!$count['c']) return array();
		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['c'],
		);
	}

	/**
	 * 系统消息详情
	 * @param $id
	 * @return mixed
	 */
	public function sysMsgInfo($id){
		$this->tbl_alias = 'msg_sys';
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->queryRow("select * from {$db_cfg['tbl']} where `msg_sys_id` = {$id} and `stat` = 0");
	}

	/**
	 * 删除系统消息
	 * @param $ids
	 * @return mixed
	 */
	public function sysMsgDel($ids){
		$this->tbl_alias = 'msg_sys';
		$ids = safe_db_data($ids);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->execute("update {$db_cfg['tbl']} set `stat` = 1 where `msg_sys_id` in ({$ids})");
	}

	/**
	 * 私信消息列表
	 * @param $page
	 * @param $num
	 * @param $keyword
	 * @return array
	 */
	public function privateMsg($page,$num,$keyword){
		$this->tbl_alias = 'msg_notify';
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$keyword = safe_db_data($keyword);
		$limit = $num ? "limit ".($page-1)*$num.",$num" : '';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$and = " and a.`stat` = 0 and a.`type` = 6 and a.`uid` = b.`uid`";
		if($keyword){
			$and .= " and b.`nick` = '{$keyword}'";
		}
		$sql = "select a.*,b.`nick` from {$db_cfg['tbl']} as a,".DATABASE.".`t_user` as b where 1 {$and} order by a.`rt` desc {$limit}";
		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} as a,".DATABASE.".`t_user` as b where 1 {$and}");
		if(!$count['c']) return array();
		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['c'],
		);
	}

	/**
	 * 私信消息详情
	 * @param $id
	 * @return mixed
	 */
	public function getMsgInfo($id){
		$this->tbl_alias = 'msg_notify';
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->queryRow("select * from {$db_cfg['tbl']} where `msg_notify_id` = {$id} and `stat` = 0");
	}

	/**
	 * 保存私信消息
	 * @param $data
	 * @param $id
	 * @return int
	 */
	public function saveMsg($data,$id){
		$this->tbl_alias = 'msg_notify';
		$data = safe_db_data($data);
		$data = parse_data($data);
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		if($id){
			$res = $db_op->execute("update {$db_cfg['tbl']} set {$data} where `msg_notify_id` = {$id}");
			return $res==false ? 0 : 1;
		}else{
			return $db_op->execute("insert into {$db_cfg['tbl']} set {$data}");
		}
	}

	/**
	 * 删除私信消息
	 * @param $ids
	 * @return mixed
	 */
	public function msgDel($ids){
		$this->tbl_alias = 'msg_notify';
		$ids = safe_db_data($ids);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->execute("update {$db_cfg['tbl']} set `stat` = 1 where `msg_notify_id` in ({$ids})");
	}

	/**
	 * 获取所有用户列表
	 * @return mixed
	 */
	public function getUserList(){
		$this->tbl_alias = 'user';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->queryList("select `uid`,`nick` from {$db_cfg['tbl']} where `stat` = 0");
	}

	/**
	 * 提现申请记录
	 * @param int $page
	 * @param string $num
	 * @param string $keyword
	 * @return array
	 */
	public function cash($page=1,$num='',$keyword=''){
		$this->tbl_alias = 'money';
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$keyword = safe_db_data($keyword);
		$limit = $num ? "limit ".($page-1)*$num.",$num" : '';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$and = " and a.`money` < 0 and left(a.`desc`,3) = '佣金:'";
		if($keyword){
			$and .= " and b.`nick` = '{$keyword}'";
		}

		$sql = "select a.*,b.`nick` from {$db_cfg['tbl']} as a left join ".DATABASE.".`t_user` as b
 				on a.`uid` = b.`uid` where 1 {$and} order by a.`ut` desc {$limit}";
		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} as a left join ".DATABASE.".`t_user` as b on a.`uid` = b.`uid` where 1 {$and}");
		if(!$count['c']) return array();
		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['c'],
		);
	}

	/**
	 * 获取提现信息
	 * @param $id
	 * @return mixed
	 */
	public function CashInfo($id){
		$this->tbl_alias = 'money';
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->queryRow("select * from {$db_cfg['tbl']} where `id` = {$id}");
	}

	/**
	 * 修改提现状态
	 * @param $id
	 * @param $desc
	 * @return mixed
	 */
	public function modifyCash($id,$desc){
		$this->tbl_alias = 'money';
		$id = safe_db_data($id);
		$desc = safe_db_data($desc);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);

		$state = $db_op->execute("update {$db_cfg['tbl']} set `desc` = '{$desc}' where `id` = {$id}");
		return $state===false ? 0 : 1;
	}
	
	 /**
	 * 提现记录
	 * @param int $page
	 * @param string $num
	 * @param string $keyword
	 * @return array
	 */
	public function cashlist($page=1,$num='',$keyword=''){
		$this->tbl_alias = 'cash';
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$keyword = safe_db_data($keyword);
		$limit = $num ? "limit ".($page-1)*$num.",$num" : '';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		 
		if($keyword){
			$and .= " and b.`nick` = '{$keyword}'";
		}

		$sql = "select a.*,b.`nick`,b.yongjin from {$db_cfg['tbl']} as a left join ".DATABASE.".`t_user` as b
 				on a.`uid` = b.`uid` where 1 {$and} order by a.`ut` desc {$limit}";
		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} as a left join ".DATABASE.".`t_user` as b on a.`uid` = b.`uid` where 1 {$and}");
		if(!$count['c']) return array();
		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['c'],
		);
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
		$sql = "select a.*,b.`title` from {$db_cfg['tbl']} as a left join ".DATABASE.".`t_goods` as b on a.`goods_id` = b.`goods_id` where `activity_id` = '{$a_id}'";
		$activity = $db_op->queryRow($sql);
		if(empty($activity)) return false;
		//修改活动状态
		$res = $db_op->execute("update {$db_cfg['tbl']} set `flag` = 4 where `activity_id` = '{$a_id}'");
		if($res){//商品不再自动开始
			$table = DATABASE.".t_goods";
			$res = $db_op->execute("update {$table} set `is_in_activity` = 1 where `goods_id` = '{$activity['goods_id']}'");
		}
		return $res===false ? 0 : $activity;
	}

	/**
	 * 获取活动参与记录
	 * @param $a_id
	 * @return mixed
	 */
	public function activityNum($a_id){
		$this->tbl_alias = 'activity_num';
		$a_id = safe_db_data($a_id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "select * from {$db_cfg['tbl']} where `activity_id` = {$a_id} group by `order_num`";
		return $db_op->queryList($sql);
	}

	/**
	 * 生成退款明细
	 * @param $uid
	 * @param $total
	 * @return mixed
	 */
	public function refund($uid,$total){
		$this->tbl_alias = 'money';
		$insert = array(
			'uid' => $uid,
			'money' => $total,
			'desc' => '活动结束->退款',
			'ut' => time(),
			'appid' => APP_ID,
		);
		$insert = safe_db_data($insert);
		$data = parse_data($insert);

		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->execute("insert into {$db_cfg['tbl']} set {$data}");
	}

	/**
	 * 更新账户余额
	 * @param $uid
	 * @param $total
	 * @return mixed
	 */
	public function userBalance($uid,$total){
		$this->tbl_alias = 'user';
		$uid = safe_db_data($uid);
		$total = safe_db_data($total);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "update {$db_cfg['tbl']} set `money` = (`money` + {$total}) where `uid` = '{$uid}'";
		return $db_op->execute($sql);
	}

	public function addMsg($uid,$goods_title,$a_id){
		$this->tbl_alias = 'msg_notify';
		$time = time();
		$content = "您参与的商品\"{$goods_title}\",第{$a_id}期活动由于超过活动有效期，被系统自动终止，您的购买记录将退回余额，请注意查收";
		$insert = array(
			'msg_notify_id' => get_auto_id(C('AUTOID_M_MSG_NOTIFY')),
			'appid' => APP_ID,
			'uid' => $uid,
			'from_uid' => APP_ID,
			'type' => 5,
			'target_id' => 0,
			'target_type' => 7,
			'content' => $content,
			'rt' => $time,
			'ut' => $time,
		);
		$insert = safe_db_data($insert);
		$data = parse_data($insert);

		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->execute("insert into {$db_cfg['tbl']} set {$data}");
	}


}