<?php
/**
 * @since 2016-01-20
 */
class NcRefundMod extends BaseMod{

	public function run(){  
			 
		//后台任务
		//开启任务 拼团商品支付
		$this->openTask();
		while(1){
			//判断是否需要停止，不能用kill来杀死该进程，这会导致一个任务执行一半被终止
		   if($this->isStop()){
				echo 'done\n';exit;
				break;
			}
			$this->pushdisk();
			//判断是否需要停止，不能用kill来杀死该进程，这会导致一个任务执行一半被终止  
            $msg_mod = Factory::getMod('msg');
            $nc_list = Factory::getMod('nc_list');  
            // $work_task = Factory::getMod('nc_work_task'); //执行定时任务
            // $work_task->dealtask();
            // $this->dealmchpay(); 
             
			//获取一个任务
			$taskInfo = $this->getOneTask();  

			if(empty($taskInfo)){//没有数据，sleep5秒 
				sleep(8);  
				continue;
			}  
            require_once COMMON_PATH.'libs/wxpay/WxPay.Api.php'; 
            $nc_list_mod = Factory::getMod('nc_list');
            $nc_list_mod->setDbConf('shop', 'order'); 
            $refund_no=WxPayConfig::MCHID.date("YmdHis"); 
            $input = new WxPayRefund();
            $input->SetTransaction_id($taskInfo['transaction_id']);
            $input->SetOut_trade_no($taskInfo['order_num']);
            $input->SetTotal_fee($taskInfo['need_money']*100);
            $input->SetRefund_fee($taskInfo['need_money']*100);
            $input->SetOut_refund_no($refund_no); //商户自定义退款号
            $input->SetOp_user_id(WxPayConfig::MCHID);  
            $msg=WxPayApi::refund($input); 
            $nc_list->setDbConf('main', 'refund');
            $where = array(
					'flag' => 0,
					'order_num'=>$taskInfo['order_num']
		    );
            if($msg['result_code']=='SUCCESS'){ 
             
				$activityData['flag']=1;
				$activityData['out_refund_no']=$refund_no;
				$activityData['refund_id']=$msg['refund_id'];
				$activityData['refund_fee']=$msg['refund_fee'];
            	$nc_list->updateData($where, $activityData);
            }else{
            	$activityData['flag']=2;//退款失败！
            	$activityData['msg']=$msg['err_code_des'];
            	$nc_list->updateData($where, $activityData);
            }
 
		}
	}
	
	 public function refundquery($trade_no){ 
		require_once COMMON_PATH.'libs/wxpay/WxPay.Api.php';  
		$input = new WxPayRefundQuery();
		//$trade_no='2016091920554110000005839';
		$input->SetOut_trade_no($trade_no); //字符串
	  //  $trade_no='2017010910542310000030031';
	//	$Transaction_id='4004862001201701095711023902';
		//	$input->SetTransaction_id('4004862001201701095711023902');
		$msg=WxPayApi::refundQuery($input);
		if($msg['result_code']=='SUCCESS'){
			// 退款已经成功
			echo '退款已经成功';

		}else{

		}
		 
    }

	/**
	 * 获取一个任务
	 */
	public function getOneTask(){
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('main', 'refund');
		$where = array(
			'flag' => 0
		);
		$column = array(
			 
		);
		 
		$limit = array(
			'begin' => 0,
			'length' => 1
		);
		$data = $nc_list->getDataOne($where, $column, array(), $limit, false);
		return $data;
	}
	 
	  
	   
	
	/**
	 * 开启任务
	 */
	public function openTask(){
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('shop', 'signal');
		$where = array(
			'signal_type' => 'order_task'
		);
		$data = array(
			'signal_val' => 'start'
		);
		$nc_list->updateData($where, $data);
	}
	
	/**
	 * 判断是否结束
	 */
	public function isStop(){
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('shop', 'signal');
		$where = array(
			'signal_type' => 'order_task'
		);
		$column = array('signal_val');
		$data = $nc_list->getDataOne($where, $column, array(), array(), false);
		if(!empty($data) && $data['signal_val'] == 'stop'){
			return true;
		}
		return false;
	} 
	private function dealmchpay(){ 
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('main', 'cash');
        $sql ="SELECT  a.money,a.order_num,a.id,b.yongjin,a.uid,b.nick,w.wx_openid from {$nc_list->dbConf['tbl']} a  LEFT JOIN  ".DATABASE.".t_user b ON   a.`uid`=b.`uid` left join ".DATABASE.".t_wxuser w on w.uid=b.uid  where a.status=2 and a.type=1 limit 1";    
        $data = $nc_list->getDataBySql($sql,false);
        if(!$data) return ; 
        $info=$data[0];
		$info['yongjin']=$info['yongjin']-1;//先扣除1元手续费
        $realmoney=$info['yongjin']<$info['money']?$info['yongjin']:$info['money']; 
        if($realmoney < 1){
    	    $where['id']= $info['id'];
    		$data = array( 
				'status' => 5,
				'msg' => '提现失败！佣金余额不足。', 
			);
			$nc_list->updateData($where, $data);
			return ;
        }
        try{ 
			//开启事务
			$nc_list->executeSql('START TRANSACTION');
			//先扣除佣金  
		    $yongjin=$realmoney+1;//增加1元手续费 //失败通知用户
		    $sql = "update ".DATABASE.".t_user set `yongjin`=`yongjin`-$yongjin where `uid`={$info['uid']}";
	        $nc_list->executeSql($sql); 
			$msg=$this->mchpay($info['order_num'],$realmoney,$info['uid'],$info['wx_openid']);  
			$where['id']=$info['id'];
			if($msg['return_msg']=='SUCCESS'){ 
				$data = array(
        		    'realmoney'=>$realmoney,
					'status' =>4,
					'msg' => '提现成功!',
					'payment_no'=>$msg['payment_no'],
					'ut'=>time(),		 
				); 
			}else{
				 $nc_list->executeSql('ROLLBACK');   //先回滚金额再更新状态
				 $data = array( 
					'status' =>5,
					'msg' => $msg['return_msg'], 
				);
			    $nc_list->updateData($where, $data);
			    return ;

			}
			$nc_list->updateData($where, $data);
			$nc_list->executeSql('COMMIT');
	    }catch(Exception $e){
	    	$data = array(
        			'id'=>$info['id'],
					'status' => 5,
					'msg' => '系统繁忙！提现失败！',  
					 
				);
		    $nc_list->updateData($where, $data);
			$nc_list->executeSql('ROLLBACK');
			 
		}
	}

   private  function mchpay($order,$money,$re_user_name,$openid){   
	    require_once COMMON_PATH.'libs/wxpay/WxPay.Api.php';  
	    $spbill_create_ip=$_SERVER["REMOTE_ADDR"];
		$mchPay = new  WxMchPay(); 
		$mchPay->setParameter('openid', $openid);
         // 商户订单号
        $mchPay->setParameter('partner_trade_no', $order); //商户订单号
        // 校验用户姓名选项
        $mchPay->setParameter('check_name', 'NO_CHECK');
        // 企业付款金额  单位为分
        $mchPay->setParameter('amount', $money); //单位分
        // 企业付款描述信息
        $mchPay->setParameter('desc', '提现'); 
        $mchPay->setParameter('re_user_name', $re_user_name);  
        // 调用接口的机器IP地址  自定义
       return WxPayApi::wxmchpay($mchPay);  
        

	}

	//把公盘的金额推送到个人账户里面
    public function pushdisk(){   
    	$time=time();
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('main', 'disk'); 
		$where['status']=0;
		$order = array(
            'id' => 'asc'
        );
		$ret = $nc_list->getDataList($where,array('id'),$order,array('length'=>10),false);  
		if(count($ret)<10){
			return ;
		}
		foreach($ret as $v){
			$ids[]=$v['id'];
		}
		$ids=implode($ids,',');
	    $sql = "update {$nc_list->dbConf['tbl']} set `status`=1 where `id` in ($ids) limit 10";
        $nc_list->executeSql($sql);

        $push = $nc_list->getDataOne(array(),array(),$order,array(),false);
        if($push){
        	$sql = "delete from  {$nc_list->dbConf['tbl']}  where `id` = {$push['id']}";
        	$nc_list->executeSql($sql);
        }  
        $nc_list->setDbConf('main', 'outdisk');  
        $nc_list->insertData(array(
        			'id'=>$push['id'],
	                'uid' => $push['uid'],
	                'order_num' => $push['order_num'],
	                'money' => 1, 
	                'rt' => $time,//date('Y-m-d H:i:s'),
        )); 
 		$sql = "update ".DATABASE.".t_user set money=money+1 where uid={$push['uid']}";   
        $nc_list->executeSql($sql);  
        $nc_list->setDbConf('shop', 'money');
        $detail = array(
            'uid' => $push['uid'],
            'desc' => '公盘返现',
            'money' => 1,
            'ut' => time(),
        );
        $nc_list->insertData($detail); 
       	$this->pushdisk(); //递归去推送


     }
 
 


}
