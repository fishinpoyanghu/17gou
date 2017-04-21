<?php
class FinanceData extends BaseData {

	private $cfg_name='shop';
	private $tbl_alias='';

    public function getFinance($start,$end){
        $this->tbl_alias = 'money';
        $start = safe_db_data($start);
        $end = safe_db_data($end);
        $db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
        $and = " and `ut` >= {$start} and `ut` <= {$end}";
        $sql = "select from_unixtime(`ut`,'%Y-%m-%d') as t,abs(sum(money)) as total from {$db_cfg['tbl']} where `desc`='消费' {$and} and `uid`   in (select `uid` from 17gou.t_user where type!=-1) group by t";
        return $db_op->queryList($sql);
    }



    public function consume($start,$end,$page=1,$num='',$keyword=''){
		$this->tbl_alias = 'activity_num';
		$start = safe_db_data($start);
		$end = safe_db_data($end);
		$keyword = safe_db_data($keyword);
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$limit = $num ? "limit ".($page-1)*$num.",$num" : '';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$and = " and a.`rt` >= {$start} and a.`rt` <= {$end} and d.type!=-1";
		if($keyword){
			$and .= " and d.`nick` = '{$keyword}'";
		}
		$table_activity = DATABASE.".`t_activity`";
		$table_user = DATABASE.".`t_user`";
		$table_goods = DATABASE.".`t_goods`";
		$table = "{$db_cfg['tbl']} as a left join {$table_activity} as b on a.`activity_id` = b.`activity_id` ";
		$table .= "left join {$table_goods} as c on b.`goods_id` = c.`goods_id` ";
		$table .= "left join {$table_user} as d on a.`uid` = d.`uid`";
        //,group_concat(a.`activity_num`) as num_str 幸运号ajax获取
		$sql = "select a.*,c.`title`,d.`nick`  from {$table} where 1 {$and} group by a.`order_num`,a.`activity_id` order by a.`rt` desc {$limit}";
       // echo "select count(DISTINCT a.`order_num`,a.`activity_id`) as total from {$db_cfg['tbl']} as a  left join {$table_user} as d on a.`uid` = d.`uid` where 1 {$and}";exit;
      //  echo "select count(*) as total from {$db_cfg['tbl']} as a  left join {$table_user} as d on a.`uid` = d.`uid` where 1 {$and}";exit;
		$count = $db_op->queryRow("select count(DISTINCT a.`order_num`,a.`activity_id`) as total from {$db_cfg['tbl']} as a  left join {$table_user} as d on a.`uid` = d.`uid` where 1 {$and}");
          
       // echo $count['total'];exit;
		if(!$count['total']) return array();
	//	$total = $db_op->queryRow("select count(*) as total from {$db_cfg['tbl']} as a  left join {$table_user} as d on a.`uid` = d.`uid` where 1 {$and}");
		return array(
			'list' => $db_op->queryList($sql),//数据显示  18
			 'total' => $count['total'], //用于分页  12
			//'total_money' => $total['total'],//总金额
		);
	}
    public function ajaxtotal($start,$end,$page=1,$num='',$keyword=''){
        $this->tbl_alias = 'activity_num';
        $start = safe_db_data($start);
        $end = safe_db_data($end);
        $keyword = safe_db_data($keyword);
        $page = safe_db_data($page);
        $num = safe_db_data($num);
        $limit = $num ? "limit ".($page-1)*$num.",$num" : '';
        $db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
         $and = " and a.`rt` >= {$start} and a.`rt` <= {$end} and d.type!=-1";
        if($keyword){
            $and .= " and d.`nick` = '{$keyword}'";
        }
        $table_activity = DATABASE.".`t_activity`";
        $table_user = DATABASE.".`t_user`";
        $table_goods = DATABASE.".`t_goods`";
        $table = "{$db_cfg['tbl']} as a left join {$table_activity} as b on a.`activity_id` = b.`activity_id` ";
        $table .= "left join {$table_goods} as c on b.`goods_id` = c.`goods_id` ";
        $table .= "left join {$table_user} as d on a.`uid` = d.`uid`";  
        $total = $db_op->queryRow("select count(*) as total from {$db_cfg['tbl']} as a  left join {$table_user} as d on a.`uid` = d.`uid` where 1 {$and}");
        $total=$total?$total:array('total'=>0);
        
         
         echo json_encode($total);


    }
	/**
	 * 充值记录
	 * @param $start
	 * @param $end
	 * @param int $page
	 * @param string $num
	 * @param string $keyword
	 * @return array
	 */
	public function recharge($start,$end,$page=1,$num='',$keyword=''){
		$this->tbl_alias = 'order';
		$start = safe_db_data($start);
		$end = safe_db_data($end);
		$keyword = safe_db_data($keyword);
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$limit = $num ? "limit ".($page-1)*$num.",$num" : '';

		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$and = " and a.`pay_type` > 0 and a.`flag` = 1 and a.`ut` >= {$start} and a.`ut` <= {$end}";
		if($keyword){
			$and .= " and b.`nick` = '{$keyword}'";
		}
		$table_user = DATABASE.".`t_user`";
		$table = "{$db_cfg['tbl']} as a left join {$table_user} as b on a.`uid` = b.`uid`";
		$sql = "select a.*,b.`nick`,b.`money` from {$table} where 1 {$and} order by a.`ut` desc {$limit}";
		$count = $db_op->queryRow("select count(*) as c from {$table} where 1 {$and}");
		if(!$count['c']) return array();
		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['c'],
		);
	}

    public function yijian($page,$num){
        $this->tbl_alias = 'yijian';
        $page = safe_db_data($page);
        $num = safe_db_data($num);
        $limit = $num ? "limit ".($page-1)*$num.",$num" : '';

        $db_cfg = load_db_cfg('shop', $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);

        $sql = "select a.*,b.nick from {$db_cfg['tbl']} a,17gou.t_user b where a.uid=b.uid order by a.`ut` desc {$limit}";
        $sql2 = "select count(*) as c from {$db_cfg['tbl']}";
        $count = $db_op->queryRow($sql2);
        if(!$count['c']) return array();
        return array(
            'list' => $db_op->queryList($sql),
            'total' => $count['c'],
        );
    }

    public function notice($page,$num){
        $this->tbl_alias = 'notice';
        $page = safe_db_data($page);
        $num = safe_db_data($num);
        $limit = $num ? "limit ".($page-1)*$num.",$num" : '';

        $db_cfg = load_db_cfg('admin', $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);

        $sql = "select * from {$db_cfg['tbl']} where is_del=0 order by `time` desc {$limit}";
        $sql2 = "select count(*) as c from {$db_cfg['tbl']} where is_del=0";
        $count = $db_op->queryRow($sql2);
        if(!$count['c']) return array();
        return array(
            'list' => $db_op->queryList($sql),
            'total' => $count['c'],
        );
    }

    public function notice_info($id){
        $this->tbl_alias = 'notice';
        $db_cfg = load_db_cfg('admin', $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
        $id = intval($id);
        $sql = "select * from {$db_cfg['tbl']} where id={$id}";
        return $db_op->queryRow($sql);
    }
    
    public function notice_save($data,$id){
        $this->tbl_alias = 'notice';
        $data = safe_db_data($data);
        $data = parse_data($data);
        $db_cfg = load_db_cfg('admin', $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
        if($id){
            $sql = "update {$db_cfg['tbl']} set {$data} where `id` = {$id}";
        }else{
            $sql = "insert into {$db_cfg['tbl']} set {$data}";
        }
        return $db_op->execute($sql);
    }

    public function noticeZiding($id){
        $this->tbl_alias = 'notice';
        $db_cfg = load_db_cfg('admin', $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
        $id = intval($id);
        $sql = "update {$db_cfg['tbl']} set `ding`=1 where `id`={$id} limit 1";
        return $db_op->execute($sql);
    }

    public function noticeQuxiaoziding($id){
        $this->tbl_alias = 'notice';
        $db_cfg = load_db_cfg('admin', $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
        $id = intval($id);
        $sql = "update {$db_cfg['tbl']} set `ding`=0 where `id`={$id} limit 1";
        return $db_op->execute($sql);
    }

    public function noticeShanchu($id){
        $this->tbl_alias = 'notice';
        $db_cfg = load_db_cfg('admin', $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
        $id = intval($id);
        $sql = "update {$db_cfg['tbl']} set `is_del`=1 where `id`={$id} limit 1";
        return $db_op->execute($sql);
    }
    /**
     *  退款记录
     * @param int $page
     * @param string $num
     * @param string $keyword
     * @return array
     */
    public function refundlist($page=1,$num='',$keyword=''){
        $this->cfg_name= 'main';
        $this->tbl_alias = 'refund';
        $page = safe_db_data($page);
        $num = safe_db_data($num);
        $keyword = safe_db_data($keyword);
        $limit = $num ? "limit ".($page-1)*$num.",$num" : '';
        $db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
        $db_op = DbOp::getInstance($db_cfg);
         
        if($keyword){
           // $and .= " and b.`nick` = '{$keyword}'";
        }

        $sql = "select a.*,b.teamwar_id,c.nick from {$db_cfg['tbl']} as a left join ".DATABASE.".`t_order` as b
                on a.`order_num` = b.`order_num` left join ".DATABASE.".t_user c on c.uid=a.uid where 1 {$and} order by a.`order_id` desc {$limit}";
        $count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} as a left join ".DATABASE.".`t_order` as b on a.`order_num` = b.`order_num` where 1 {$and}");
        if(!$count['c']) return array();
        return array(
            'list' => $db_op->queryList($sql),
            'total' => $count['c'],
        );
    }

}