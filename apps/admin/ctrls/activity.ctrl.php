<?php
/**
 * Created by PhpStorm.
 * User: hzm
 * Date: 2016/4/6
 * Time: 17:41
 */
class ActivityCtrl extends BaseCtrl{
    private $mod;
    public function __construct(){
        $this->mod = Factory::getMod('activity');
    }

    /**
     * 活动列表
     */
    public function index(){
        $login_user = app_get_login_user(1, 1);
        $page = gint('page');
        $type = gint('type');
        $type = in_array($type,array(1,2,3,4,6,7)) ? $type : 1;
        $keyword = gstr('keyword');
        clean_xss($keyword);
        $search = "";
        if($keyword){
            $search = "&keyword={$keyword}";
        }
        $page = $page < 1 ? 1 : $page;
        $num = 15;
        $info = $this->mod->activity($type,$page,$num); 
        $page_content = page(ceil($info['total']/$num), $page, "?c=activity&type={$type}&{$search}page"); 
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'type' => $type,
            'menu' => 'activity',
        );
        Factory::getView("activity/activity", $data);
    }

    /**
     * 指定中奖or取消指定
     */
    public function assign(){
        $login_user = app_get_login_user(1, 1);
        if($login_user['is_super']){
            $id = gint('id');
            $row = $this->mod->assign($id);
            echo_result($row);
        }else{
            exit;
        }

    }

    /**
     * 批量指定中奖
     */
    public function multiAssign(){
        $login_user = app_get_login_user(1, 1);
        if($login_user['is_super']){
            $ids = pstr('id');
            $row = $this->mod->multiAssign($ids);
            echo_result($row);
        }else{
            exit;
        }

    }

    /**
     * 红包列表
     */
    public function redList(){
        $login_user = app_get_login_user(1, 1);
        $page = gint('page');
        $page = $page < 1 ? 1 : $page;
        $num = 15;
        $info = $this->mod->redList($page,$num);
        $page_content = page(ceil($info['total']/$num), $page, "?c=activity&a=redList&page");
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'menu' => 'redList',
        );
        Factory::getView("activity/red_list", $data);
    }

    /**
     * 批量修改红包
     */
    public function modifyRed(){
        $ids = pstr('ids');
        $type = pint('type');
        $row = $this->mod->modifyRed($ids,$type);
        echo_result($row);
    }

    /**
     * 删除红包
     */
    public function delRed(){
        $id = gint('id');
        $row = $this->mod->delRed($id);
        echo_result($row);
    }

    /**
     * 添加红包页面
     */
    public function editRed(){
        $id = gint('id');
        $info = $this->mod->getRed($id);
        $data = array(
            'info' => $info,
            'menu' => 'redList',
        );
        Factory::getView("activity/edit_red", $data);
    }

    /**
     * 添加红包
     */
    public function addRed(){
        $data = pstr('data');
        $id = pint('id');
        $res = $this->mod->addRed($data,$id);
        echo_result($res['state'],$res['msg']);
    }

    /*
     * 红包开始第一期
     */
    public function redStart(){
        $id = gint('id');
        $row = $this->mod->redStart($id);
        echo_result($row);
    }

    public function order(){
        $login_user = app_get_login_user(1, 1);
        $page = gint('page');
        $page = $page < 1 ? 1 : $page;
        $state = gint('state');
        $state = in_array($state,array(0,1,2)) ? intval($state) : 0;
        $num = 15;
        $info = $this->mod->order($page,$num,$state);
        $page_content = page(ceil($info['total']/$num), $page, "?c=activity&a=order&state=".$state."&page");
        $express = include get_app_root().'/conf/express.conf.php';
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'state' => $state,
            'express' => $express,
            'menu' => 'order',
        );
        Factory::getView("activity/order_list", $data);
    }

    /**
     * 晒单列表
     */
    public function show(){
        $login_user = app_get_login_user(1, 1);
        $page = gint('page');
        $state = gint('state');
        $page = $page < 1 ? 1 : $page;
        $num = 10;
        $info = $this->mod->show($state,$page,$num);
        $page_content = page(ceil($info['total']/$num), $page, "?c=activity&a=show&page");
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'state' => $state,
            'menu' => 'show',
        );
        Factory::getView("activity/show", $data);
    }

    /**
     * 晒单or评论 审核、删除
     */
    public function modifyShow(){
        $id = gint('id');
        $stat = gint('stat');
        $type = gint('type');
        $row = $this->mod->modifyShow($id,$stat,$type);
        echo_result($row);
    }

    /**
     * 晒单or评论 批量删除
     */
    public function multiShowDel(){
        $id = gstr('id');
        $type = gint('type');
        $row = $this->mod->multiShowDel($id,$type);
        echo_result($row);
    }

    /**
     * 评论审核列表
     */
    public function comment(){
        $login_user = app_get_login_user(1, 1);
        $page = gint('page');
        $state = gint('state');
        $page = $page < 1 ? 1 : $page;
        $num = 15;
        $info = $this->mod->comment($state,$page,$num);
        $page_content = page(ceil($info['total']/$num), $page, "?c=activity&a=comment&page");
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'state' => $state,
            'menu' => 'comment',
        );
        Factory::getView("activity/comment", $data);
    }

    /**
     * 指定中奖名单列表
     */
    public function assignList(){
        $login_user = app_get_login_user(1, 1);
        if(!$login_user['is_super']) exit;
        $page = gint('page');
        $award = gint('award');
        $page = $page < 1 ? 1 : $page;
        $num = 15;
        $info = $this->mod->assignList($page,$num,$award);
        $page_content = page(ceil($info['total']/$num), $page, "?c=activity&a=assignList&award={$award}&page");
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'menu' => 'assignList',
        );

        Factory::getView("activity/assign_list", $data);
    }


    /**
     * 开启或关闭指定中奖
     */
    public function modifyAssign(){
        $login_user = app_get_login_user(1, 1);
        if($login_user['is_super']){
            $id = gint('id');
            $row = $this->mod->modifyAssign($id);
            echo_result($row);
        }else{
            echo_result(0);
        }

    }

    /**
     * 指定中奖名单
     */
    public function addAssign(){
        $name = pstr('name');
        $res = $this->mod->addAssign($name);
        echo_result($res['state'],$res['msg']);
    }

    /**
     * 常见问题
     */
    public function question(){
        $login_user = app_get_login_user(1, 1);
        $res = file_exists(dirname(CORE_ROOT).'/uploads/other/question.html');
        $content = $res ? file_get_contents(dirname(CORE_ROOT).'/uploads/other/question.html') : '';
        $data = array(
            'login_user' => $login_user,
            'content' => $content,
            'menu' => 'question',
            'd' => 'question'
        );
        Factory::getView("activity/question", $data);
    }

    public function question2(){
        $d = gstr('d');
        if(!in_array($d,array(
            'question',
            'ruleIntroduction',
            'know',
            'fortune',
            'systemEnsure',
            'safetyPayment',
            'complaint',
            'deliveryMoney',
            'sign',
            'noReceive',
            'introduce',
            'serviceAgreement',
            'contact',
            'cooperation',
            'invite',
            'qq',
        ))) exit;
        $login_user = app_get_login_user(1, 1);
        $res = file_exists(dirname(CORE_ROOT).'/uploads/other/'.$d.'.html');
        $content = $res ? file_get_contents(dirname(CORE_ROOT).'/uploads/other/'.$d.'.html') : '';
        $data = array(
            'login_user' => $login_user,
            'content' => $content,
            'menu' => 'question',
            'd' => $d,
        );
        Factory::getView("activity/question2", $data);
    }

    /**
     * 保存常见问题
     */
    public function saveQuestion(){
        $content = pstr('content');
        if(empty($content)){
            echo_result(0,'请输入内容');
        }else{
            $res = file_put_contents(dirname(CORE_ROOT).'/uploads/other/question.html',$content);
            echo_result($res);
        }
    }

    public function saveQuestion2(){
        $content = pstr('content');
        $d = pstr('d');
        if(!in_array($d,array(
            'question',
            'ruleIntroduction',
            'know',
            'fortune',
            'systemEnsure',
            'safetyPayment',
            'complaint',
            'deliveryMoney',
            'sign',
            'noReceive',
            'introduce',
            'serviceAgreement',
            'contact',
            'cooperation',
            'invite',
            'qq',
        ))) echo_result(0,'error');
        if(empty($content)){
            echo_result(0,'请输入内容');
        }else{
            $res = file_put_contents(dirname(CORE_ROOT).'/uploads/other/'.$d.'.html',$content);
            echo_result($res);
        }
    }

    /**
     * 规则设置
     */
    public function rule(){
        $login_user = app_get_login_user(1, 1);
        $res = file_exists(dirname(CORE_ROOT).'/uploads/other/one.html');
        $one = $res ? file_get_contents(dirname(CORE_ROOT).'/uploads/other/one.html') : '';
        $res = file_exists(dirname(CORE_ROOT).'/uploads/other/red.html');
        $red = $res ? file_get_contents(dirname(CORE_ROOT).'/uploads/other/red.html') : '';
        $res = file_exists(dirname(CORE_ROOT).'/uploads/other/lottery.html');
        $lottery = $res ? file_get_contents(dirname(CORE_ROOT).'/uploads/other/lottery.html') : '';
        $data = array(
            'login_user' => $login_user,
            'one' => $one,
            'red' => $red,
            'lottery' => $lottery,
            'menu' => 'rule',
        );
        Factory::getView("activity/rule", $data);
    }

    /**
     * 保存规则
     */
    public function saveRule(){
        $one = pstr('one');
        $red = pstr('red');
        $lottery = pstr('lottery');
        $res_one = file_put_contents(dirname(CORE_ROOT).'/uploads/other/one.html',$one);
        $res_red = file_put_contents(dirname(CORE_ROOT).'/uploads/other/red.html',$red);
        $res_lot = file_put_contents(dirname(CORE_ROOT).'/uploads/other/lottery.html',$lottery);
        $res = $res_one||$res_red||$res_lot ? 1 : 0;
        echo_result($res);
    }

    /**
     * 录入物流信息
     */
    public function addExpress(){
        $id = gint('id');
        $code = gstr('code');
        $num = gstr('num');
        $ordernum=gstr('ordernum');
        $phone=gstr('phone');
        $nick=gstr('nick');
        $addr=gstr('addr');  
        $address = urlencode($addr).':'.urlencode($nick).':'.$phone; 
        $row = $this->mod->addExpress($id,$code,$num,$ordernum,$address);
        echo_result($row);
    }

    /**
     * 活动参与记录
     */
    public function record(){
        $login_user = app_get_login_user(1, 1);
        $id = gint('id');
        $page = gint('page');
        $page = $page < 1 ? 1 : $page;
        $num = 15;
        $type=gint('type');
        $info = $this->mod->record($id,$page,$num,$type);
        
        $page_content = page(ceil($info['total']/$num), $page, "?c=activity&a=record&page");
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'menu' => 'activity',
        );
        Factory::getView("activity/record", $data);
    }

    /**
     * 红包活动参与记录
     */
    public function redRecord(){
        $login_user = app_get_login_user(1, 1);
        $id = gint('id');
        $page = gint('page');
        $page = $page < 1 ? 1 : $page;
        $num = 15;
        $info = $this->mod->record($id,$page,$num,true);
        $page_content = page(ceil($info['total']/$num), $page, "?c=activity&a=redRecord&page");
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'menu' => 'activity',
        );
        Factory::getView("activity/red_record", $data);
    }

    /*
     * 分佣设置信息
     */
    public function commission(){
        $login_user = app_get_login_user(1, 1);
        $info = $this->mod->commission();
        $data = array(
            'login_user' => $login_user,
            'info' => $info,
            'menu' => 'commission',
        );
        Factory::getView("activity/commission", $data);
    }

    /**
     * 保存分佣设置
     */
    public function saveCom(){
        $data = pstr('data');
        $res = $this->mod->saveCom($data);
        echo_result($res['state'],$res['msg']);
    }


    /**
     * 分销用户
     */
    public function distribution(){
        $login_user = app_get_login_user(1, 1);
        $page = gint('page');
        $page = $page < 1 ? 1 : $page;
        $num = 10;
        $info = $this->mod->distribution($page,$num);
        $page_content = page(ceil($info['total']/$num), $page, "?c=activity&a=distribution&page");
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'total_money' => $info['total_money'],
            'total_number' => $info['total_number'],
            'menu' => 'distribution',
        );
        Factory::getView("activity/distribution", $data);
    }

    /**
     * 分润详情
     */
    public function profitDetail(){
        $login_user = app_get_login_user(1, 1);
        $uid = gint('uid');
        $page = gint('page');
        $page = $page < 1 ? 1 : $page;
        $num = 15;
        $info = $this->mod->profitDetail($uid,$page,$num);
        $page_content = page(ceil($info['total']/$num), $page, "?c=activity&a=profitDetail&uid={$uid}&page");
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'total_money' => $info['total_money'],
            'total_consume' => $info['total_consume'],
            'menu' => 'distribution',
        );
        Factory::getView("activity/profit", $data);
    }

    /**
     * 系统消息列表
     */
    public function sysMsg(){
        $login_user = app_get_login_user(1, 1);
        $page = gint('page');
        $page = $page < 1 ? 1 : $page;
        $num = 10;
        $keyword = gstr('keyword');
        clean_xss($keyword);
        $search = "";
        if($keyword){
            $search = "&keyword={$keyword}";
        }

        $info = $this->mod->sysMsg($page,$num,$keyword);
        $page_content = page(ceil($info['total']/$num), $page, "?c=activity&a=sysMsg{$search}&page");
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'keyword' => $keyword,
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'menu' => 'sysMsg',
        );
        Factory::getView("activity/sys_msg", $data);
    }

    /**
     * 编辑系统消息页
     */
    public function editSysMsg(){
        $login_user = app_get_login_user(1, 1);
        $id = gint('id');
        $info = $this->mod->sysMsgInfo($id);
        $data = array(
            'login_user' => $login_user,
            'info' => $info,
            'menu' => 'sysMsg',
        );
        Factory::getView("activity/edit_sys_msg", $data);
    }

    /**
     * 保存系统消息
     */
    public function saveSysMsg(){
        $id = gint('id');
        $data = gstr('data');
        $res = $this->mod->saveSysMsg($data,$id);
        echo_result($res['state'],$res['msg']);
    }

    /**
     * 删除系统消息
     */
    public function sysMsgDel(){
        $id = gstr('id');
        $res = $this->mod->sysMsgDel($id);
        echo_result($res['state']);
    }

    /**
     * 提现申请列表
     */
    public function cash(){
        $login_user = app_get_login_user(1, 1);
        $page = gint('page');
        $keyword = gstr('keyword');
        clean_xss($keyword);
        $page = $page < 1 ? 1 : $page;
        $num = 15;
        $search = "";
        if($keyword){
            $search = "&keyword={$keyword}";
        }

        $info = $this->mod->cash($page,$num,$keyword);
        $page_content = page(ceil($info['total']/$num), $page, "?c=activity&a=cash{$search}&page");
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'keyword' => $keyword,
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'menu' => 'cash',
        );
        Factory::getView("activity/cash", $data);
    }

    /**
     * 修改提现申请状态
     */
    public function modifyCash(){
        $id = gint('id');
        $state = gint('state');
        $res = $this->mod->modifyCash($id,$state);
        echo_result($res['state']);
    }
    //新提现功能
   public function cashlist(){
        $login_user = app_get_login_user(1, 1);
        $page = gint('page');
        $keyword = gstr('keyword');
        clean_xss($keyword);
        $page = $page < 1 ? 1 : $page;
        $num = 15;
        $search = "";
        if($keyword){
            $search = "&keyword={$keyword}";
        }

        $info = $this->mod->cashlist($page,$num,$keyword); 
        $page_content = page(ceil($info['total']/$num), $page, "?c=activity&a=cash{$search}&page");
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'keyword' => $keyword,
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'menu' => 'cashlist',
        );
        Factory::getView("activity/cashlist", $data);
    }
   public function modifyCashstatus(){
        $login_user = app_get_login_user(1, 1); 
        if(!$login_user['is_super']){
             echo_result(0,'没有权限!');
        }
        $id = gint('id'); 
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('main', 'cash');
        $time=time();
        $state=gint('state');
        if($state==3){
             $sql = "update {$nc_list->dbConf['tbl']} set `status`=$state ,`ut`=$time where `id`={$id} ";  
             $row= $nc_list->executeSql($sql); 
             echo_result($row,'审批不通过');
        }
        $sql ="SELECT  a.money,a.order_num,a.id,b.yongjin,a.uid,b.nick,w.wx_openid from {$nc_list->dbConf['tbl']} a  LEFT JOIN  ".DATABASE.".t_user b ON   a.`uid`=b.`uid` left join ".DATABASE.".t_wxuser w on w.uid=b.uid  where  status=1  and  a.type=1 and a.id=".$id;     
        $data = $nc_list->getDataBySql($sql,false);
       
        if(!$data) {
             echo_result(0,'id错误');
        }  
     /*   if(!$data[0]['wx_openid']){
             echo_result(0,'当前用户不是微信用户');
        }*/
        $info=$data[0];
        $info['yongjin']=$info['yongjin']-1;//先扣除1元手续费
        $realmoney=$info['yongjin']<$info['money']?$info['yongjin']:$info['money']; 
        if($realmoney < 1){
            $where['id']= $info['id'];
            $data = array( 
                'status' => 5,
                'msg' => '提现失败！佣金余额不足', 
            ); 
            $nc_list->updateData($where, $data); 
            echo_result(0,'提现失败！佣金余额不足');
        }
        try{ 
            //开启事务
            $nc_list->executeSql('START TRANSACTION');
            //先扣除佣金  
            $yongjin=$realmoney+1;//增加1元手续费 //失败通知用户
            $sql = "update ".DATABASE.".t_user set `yongjin`=`yongjin`-$yongjin where `uid`={$info['uid']}";
            $nc_list->executeSql($sql); 
            $xml=$this->mchpay($info['order_num'],$realmoney,$info['uid'],$info['wx_openid']);   
            $msg=  $this->curlmsg($xml); 
            $where['id']=$info['id'];  
            if($msg['result_code']=='SUCCESS'){ 
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
                echo_result(0,$msg['return_msg']);

            }
            $nc_list->updateData($where, $data);
            $nc_list->executeSql('COMMIT');
        }catch(Exception $e){
            $data = array( 
                    'status' => 2,
                    'msg' => '系统繁忙！提现失败！',  
                     
                );
            $nc_list->updateData($where, $data);
            $nc_list->executeSql('ROLLBACK');
             echo_result(0,'系统繁忙！提现失败！');
             
        }


        echo_result(1,'审批通过!');

         
    }
    private  function mchpay($order,$money,$re_user_name,$openid){   
        require_once COMMON_PATH.'libs/wxpay/WxPay.Api.php';   
        $mchPay = new  WxMchPay(); 
        $mchPay->setParameter('openid', $openid);
         // 商户订单号
        $mchPay->setParameter('partner_trade_no', $order); //商户订单号
        // 校验用户姓名选项
        $mchPay->setParameter('check_name', 'NO_CHECK');
        // 企业付款金额  单位为分
        $mchPay->setParameter('amount', $money*100); //单位分
        // 企业付款描述信息
        $mchPay->setParameter('desc', '提现'); 
        $mchPay->setParameter('re_user_name', $re_user_name);  
        // 调用接口的机器IP地址  自定义
       return WxPayApi::wxmchpay($mchPay);  
        

    }
 
    /**
     * 私信消息列表
     */
    public function msg(){
        $login_user = app_get_login_user(1, 1);
        $page = gint('page');
        $page = $page < 1 ? 1 : $page;
        $num = 10;
        $keyword = gstr('keyword');
        clean_xss($keyword);
        $search = "";
        if($keyword){
            $search = "&keyword={$keyword}";
        }
        $info = $this->mod->privateMsg($page,$num,$keyword);
        $page_content = page(ceil($info['total']/$num), $page, "?c=activity&a=msg{$search}&page");
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'keyword' => $keyword,
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'menu' => 'msg',
        );
        Factory::getView("activity/msg", $data);
    }

    /**
     * 编辑或添加私信消息页
     */
    public function editMsg(){
        $login_user = app_get_login_user(1, 1);
        $id = gint('id');
        $info = $this->mod->getMsgInfo($id);
        $user = $this->mod->getUserList();
        $data = array(
            'login_user' => $login_user,
            'info' => $info,
            'user' => $user,
            'menu' => 'msg',
        );
        Factory::getView("activity/edit_msg", $data);
    }

    /**
     * 保存私信消息
     */
    public function saveMsg(){
        $id = gint('id');
        $data = gstr('data');

        $res = $this->mod->saveMsg($data,$id);
        echo_result($res['state'],$res['msg']);
    }

    /**
     * 删除私信消息
     */
    public function msgDel(){
        $id = gstr('id');
        $res = $this->mod->msgDel($id);
        echo_result($res['state']);
    }

    public function share(){
        $login_user = app_get_login_user(1, 1);
        $res = include_once(dirname(CORE_ROOT).'/apps/admin/conf/share.conf.php');

        $data = array(
            'login_user' => $login_user,
            'info' => $res,
            'menu' => 'share',
        );
        Factory::getView("activity/share", $data);
    }

    public function saveShare(){
        $title = pstr('title');
        $sub_title = pstr('sub_title');
        clean_xss($title);
        clean_xss($sub_title);
        $data = array(
            'title' => $title,
            'sub_title' => $sub_title,
        );
        if(trim($data['title']) == ''){
            echo_result(0,'请输入标题');
        }
        if(trim($data['sub_title']) == ''){
            echo_result(0,'请输入副标题');
        }
        $save = var_export($data, true);
        $save = "<?php\r\nreturn {$save};";
        $res = file_put_contents(dirname(CORE_ROOT).'/apps/admin/conf/share.conf.php',$save);
        echo_result($res?1:0,$res?"保存成功":"保存失败");
    }
    
    /**
     * 结束活动
     */
    public function endActivity(){
        $a_id = gint('id');
        $res = $this->mod->endActivity($a_id);
        echo_result($res['state'],$res['msg']);
    }

    //微信内部企业付款参数跟原本微信自带curl参数不同，暂时用此curl参数
    private function curlmsg($data){
        $ch = curl_init ();
        $MENU_URL="https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
        curl_setopt ( $ch, CURLOPT_URL, $MENU_URL );
        curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE ); 
 
        curl_setopt($ch,CURLOPT_SSLCERT,WxPayConfig::SSLCERT_PATH);
        curl_setopt($ch,CURLOPT_SSLKEY,WxPayConfig::SSLKEY_PATH); 
        curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true ); 

        $info = curl_exec ( $ch );

        if (curl_errno ( $ch )) {
            echo_result(0,'Errno' .curl_error ( $ch ));
             
        }

        curl_close ( $ch );
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($info, 'SimpleXMLElement', LIBXML_NOCDATA)), true);        
       
    }

}