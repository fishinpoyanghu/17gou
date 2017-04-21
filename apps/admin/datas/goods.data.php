<?php
class GoodsData extends BaseData {

	private $cfg_name='shop';
	private $tbl_alias='goods_type';

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
    }

	public function goods($id){
		$this->tbl_alias = 'goods';
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
	 * @param array $searchArr
	 * @return array
	 */
	public function goodsList($page=1,$num='',$searchArr=''){
		$this->tbl_alias = 'goods';
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$keyword = safe_db_data($searchArr['keyword']);
		$limit = $num ? "limit ".($page-1)*$num.",$num" : '';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);

		$and = " and a.`stat` = 0 and (a.`goods_type_id` = b.`goods_type_id` or a.`goods_type_id`=0 )";
		if($keyword){
			$and .= " and a.`title` like '%{$keyword}%'";
		}
		 
		$and .= $searchArr['cate']?" and b.`goods_type_id` ={$searchArr['cate']}":'';
	 	$and .= $searchArr['activity_type']?" and a.`activity_type`={$searchArr['activity_type']}":''; 
	 	$and .= $searchArr['rate_percent']?" and a.`rate_percent`={$searchArr['rate_percent']}":''; 
	 	 
		$sql = "select a.*,b.`name` from {$db_cfg['tbl']} as a,".DATABASE.".`t_goods_type` as b where 1 {$and} order by a.`rt` desc {$limit}";

		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} as a,".DATABASE.".`t_goods_type` as b where 1 {$and}");

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
		$this->tbl_alias = 'activity';
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
	 * 删除商品分类
	 * @param $ids
	 * @return mixed
	 */
	public function delGoodsCfy($ids){
		$ids = safe_db_data($ids);
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
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "select `goods_type_id` as id,`name`,`img` from {$db_cfg['tbl']} where `goods_type_id` = {$id}";
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
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		if($id){
			$sql = "update {$db_cfg['tbl']} set {$data} where `goods_type_id` = {$id}";
		}else{
			$sql = "insert into {$db_cfg['tbl']} set {$data}";
		}
		return $db_op->execute($sql);
	}


    public function shouin($id){
        $db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
        $id = intval($id);
        $sql = "update {$db_cfg['tbl']} set `shou`=1 where `goods_type_id`={$id} limit 1";
        return $db_op->execute($sql);
    }

    public function shouout($id){
        $db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
        $id = intval($id);
        $sql = "update {$db_cfg['tbl']} set `shou`=0 where `goods_type_id`={$id} limit 1";
        return $db_op->execute($sql);
    }

	/**
	 * banner列表
	 * @param int $page
	 * @param string $num
	 * @return array
	 */
	public function banner($page=1,$num=''){
		$this->tbl_alias = 'banner';
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$start = ($page-1)*$num;
		$limit = $num;

		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "select * from {$db_cfg['tbl']} where `is_del` = 0 order by `sort` desc limit {$start},{$limit}";
		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} where `is_del` = 0");
		if($count['c']==0) return array();
		return array('list' => $db_op->queryList($sql),'total' => $count['c']);
	}

    public function pcbanner($page=1,$num=''){
        $this->tbl_alias = 'pcbanner';
        $page = safe_db_data($page);
        $num = safe_db_data($num);
        $start = ($page-1)*$num;
        $limit = $num;

        $db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
        $sql = "select * from {$db_cfg['tbl']} where `is_del` = 0 order by `sort` desc limit {$start},{$limit}";
        $count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} where `is_del` = 0");
        if($count['c']==0) return array();
        return array('list' => $db_op->queryList($sql),'total' => $count['c']);
    }

	/**
	 * 关闭或发布banner
	 * @param $id
	 * @return mixed
	 */
	public function editBanner($id){
		$this->tbl_alias = 'banner';
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "update {$db_cfg['tbl']} set `state` = abs(`state` - 1) where `id` = {$id}";
		return $db_op->execute($sql);
	}

	/**
	 * 删除banner
	 * @param $id
	 * @return mixed
	 */
	public function delBanner($id){
		$this->tbl_alias = 'banner';
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "update {$db_cfg['tbl']} set `is_del` = 1 where `id` = {$id}";
		return $db_op->execute($sql);
	}

	/**
	 * 添加banner
	 * @param $insert
	 * @return mixed
	 */
	public function addBanner($insert){
		$this->tbl_alias = 'banner';
		$insert = safe_db_data($insert);
		$data = parse_data($insert);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "insert into {$db_cfg['tbl']} set {$data}";
		return $db_op->execute($sql);
	}

	/**
	 * 修改banner排序
	 * @param $id
	 * @param $sort
	 * @return mixed
	 */
	public function sortBanner($id,$sort){
		$this->tbl_alias = 'banner';
		$sort = safe_db_data($sort);
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "update {$db_cfg['tbl']} set `sort` = {$sort} where `id` = {$id}";
		return $db_op->execute($sql);
	}

	/**
	 * 添加banner链接
	 * @param $id
	 * @param $goods_id
	 * @return int
	 */
	public function addLink($id,$goods_id,$url,$type){
		$this->tbl_alias = 'banner';
		$goods_id = safe_db_data($goods_id);
		$type=$type+0;
		$id = safe_db_data($id);
		$url = safe_db_data($url);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "update {$db_cfg['tbl']} set `goods_id` = '{$goods_id}',`url`='{$url}' ,`type`={$type} where `id` = {$id}";
		$res = $db_op->execute($sql);
		return $res === false ? 0 : 1;
	}


    /**
     * 关闭或发布banner
     * @param $id
     * @return mixed
     */
    public function editpcBanner($id){
        $this->tbl_alias = 'pcbanner';
        $id = safe_db_data($id);
        $db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
        $sql = "update {$db_cfg['tbl']} set `state` = abs(`state` - 1) where `id` = {$id}";
        return $db_op->execute($sql);
    }

    /**
     * 删除banner
     * @param $id
     * @return mixed
     */
    public function delpcBanner($id){
        $this->tbl_alias = 'pcbanner';
        $id = safe_db_data($id);
        $db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
        $sql = "update {$db_cfg['tbl']} set `is_del` = 1 where `id` = {$id}";
        return $db_op->execute($sql);
    }

    /**
     * 添加banner
     * @param $insert
     * @return mixed
     */
    public function addpcBanner($insert){
        $this->tbl_alias = 'pcbanner';
        $insert = safe_db_data($insert);
        $data = parse_data($insert);
        $db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
        $sql = "insert into {$db_cfg['tbl']} set {$data}";
        return $db_op->execute($sql);
    }

    /**
     * 修改banner排序
     * @param $id
     * @param $sort
     * @return mixed
     */
    public function sortpcBanner($id,$sort){
        $this->tbl_alias = 'pcbanner';
        $sort = safe_db_data($sort);
        $id = safe_db_data($id);
        $db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
        $sql = "update {$db_cfg['tbl']} set `sort` = {$sort} where `id` = {$id}";
        return $db_op->execute($sql);
    }

    /**
     * 添加banner链接
     * @param $id
     * @param $goods_id
     * @return int
     */
    public function addpcLink($id,$goods_id,$url){
        $this->tbl_alias = 'pcbanner';
        $goods_id = safe_db_data($goods_id);
        $id = safe_db_data($id);
        $url = safe_db_data($url);
        $db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
        $sql = "update {$db_cfg['tbl']} set `goods_id` = {$goods_id},`url`='{$url}' where `id` = {$id}";
        $res = $db_op->execute($sql);
        return $res === false ? 0 : 1;
    }

	/**
	 * 获取积分规则信息
	 * @return mixed
	 */
	public function getPointRule(){
		$this->tbl_alias = 'point_rule';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->queryList("select * from {$db_cfg['tbl']}");
	}

	/**
	 * 保存积分规则
	 * @param $info
	 * @return int
	 */
	public function savePointRule($info){
		$this->tbl_alias = 'point_rule';
		$info = safe_db_data($info);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$type = array(
            'login' => '登录',
            'info' => '完善资料',
            'lottery' => '抽奖',
            'share' => '分享',
            'consume' => '消费',
            'show' => '晒单',
            'red' => '红包',
        );
		foreach((array)$info as $key=>$val){
			$sql = "insert into {$db_cfg['tbl']} (`type`,`point`,`limit`) values('$type[$key]',{$val['point']},{$val['limit']})
					ON DUPLICATE KEY update `point` = {$val['point']},`limit` = {$val['limit']}";
			$res = $db_op->execute($sql);
			if($res === false){
				return 0;
			}
		}
		return 1;
	}

	/**
	 * 奖品列表
	 * @return mixed
	 */
	public function lottery(){
		$this->tbl_alias = 'lottery';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->queryList("select * from {$db_cfg['tbl']} order by `percent` desc");
	}

	/**
	 * 删除奖品
	 * @param $id
	 * @return mixed
	 */
	public function delLottery($id){
		$this->tbl_alias = 'lottery';
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "delete from {$db_cfg['tbl']} where `id` = {$id}";
		return $db_op->execute($sql);
	}

	/**
	 * 添加奖品
	 * @param $data
	 * @return mixed
	 */
	public function addLottery($data){
		$this->tbl_alias = 'lottery';
		$data = safe_db_data($data);
		$data = parse_data($data);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "insert into {$db_cfg['tbl']} set {$data}";
		return $db_op->execute($sql);
	}

	/**
	 * 保存抽奖设置
	 * @param $update
	 * @return number
	 */
	public function saveLottery($update){
		$this->tbl_alias = 'lottery';
		$info = safe_db_data($update);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);

		foreach((array)$info as $val){
			$sql = "update {$db_cfg['tbl']} set `percent` = {$val['percent']} where `id` = {$val['id']}";
			$res = $db_op->execute($sql);
			if($res === false){
				return 0;
			}
		}
		return 1;
	}

	/**
	 * 抽奖记录
	 * @param int $page
	 * @param string $num
	 * @param string $keyword
	 * @return array
	 */
	public function lotteryRecord($page=1,$num='',$keyword=''){
		$this->tbl_alias = 'lottery_record';
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$keyword = safe_db_data($keyword);
		$start = ($page-1)*$num;
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);

		$and = " and a.`uid` = b.`uid`";
		if($keyword){
			$and .= " and b.`nick` = '{$keyword}'";
		}
		$sql = "select a.*,b.`nick` from {$db_cfg['tbl']} as a,".DATABASE.".`t_user` as b where 1 {$and} order by a.`ut` desc limit $start,$num";
		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} as a,".DATABASE.".`t_user` as b where 1 {$and}");
		if($count['c'] == 0) return array();
		return array('list' => $db_op->queryList($sql),'total' => $count['c'],'keyword' => $keyword);
	}


	/**
	 * 添加商品
	 * @param $data
	 * @param $id
	 * @return mixed
	 */
	public function saveGoods($data,$id){// var_dump($data);exit;
		$this->tbl_alias = 'goods';
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
		$this->tbl_alias = 'goods';
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->queryRow("select * from {$db_cfg['tbl']} where `goods_id` = {$id}");
	}

	/**
	 * 商品开始第一期
	 * @param $data
	 * @return mixed
	 */
	public function startFirst($data){
		$this->tbl_alias = 'activity';
		$data = safe_db_data($data);
		$goods_id = $data['goods_id'];
		$data = parse_data($data);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$res = $db_op->queryRow("select * from {$db_cfg['tbl']} where `goods_id` = {$goods_id} and `flag` = 0");
		if($res){
			return true;
		}
		return $db_op->execute("insert into {$db_cfg['tbl']} set {$data}");
	}
	
	public function insertNumData($data){
        $this->tbl_alias = 'num';
        $db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
        $sql = "insert into {$db_cfg['tbl']}";
        $key = array_keys($data[0]);
        $sql .= "(`".implode("`,`", $key)."`)values";
        foreach($data as $val){
            $sql .= "('".implode("','", $val)."'),";
        }
        $sql = rtrim($sql, ',');
        return $db_op->execute($sql);
    }

	/**
	 * 商品是否开始第一期
	 * @param $id
	 * @return mixed
	 */
	public function isActivity($id){
		$this->tbl_alias = 'activity';
		$id = safe_db_data($id);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->queryRow("select * from {$db_cfg['tbl']} where `goods_id` = {$id} order by `rt` desc limit 1");
	}

	/**
	 * 录入物流信息
	 * @param $id
	 * @param $str
	 * @return mixed
	 */
	public function addExpress($id,$str){
		$this->tbl_alias = 'lottery_record';
		$id = safe_db_data($id);
		$str = safe_db_data($str);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "update {$db_cfg['tbl']} set `kuaidi` = '{$str}',`send` = 1 where `id` = {$id}";
		return $db_op->execute($sql);
	}

}