<?php
/**
 * Created by PhpStorm.
 * User: hzm
 * Date: 2016/4/6
 * Time: 17:41
 */
class FinanceCtrl extends BaseCtrl{
    private $mod;
    public function __construct(){
        $this->mod = Factory::getMod('finance');
    }

    public function index(){
        $login_user = app_get_login_user(1, 1);
        $start = gstr('start');
        $end = gstr('end');
        clean_xss($start);
        clean_xss($end);
        $info = $this->mod->getFinance($start,$end);
        $data = array(
            'login_user' => $login_user,
            'total' => $info['total'],
            'time' => $info['time'],
            'data' => $info['data'],
            'start' => $info['start'],
            'end' => $info['end'],
            'menu' => 'finance',
        );
        Factory::getView("finance/index", $data);
    }

    public function consume(){
        error_reporting(E_ALL);
        set_time_limit(0);
        ini_set('max_execution_time',0);
        $login_user = app_get_login_user(1, 1);
        $start = gstr('start');
        $end = gstr('end');
        $keyword = gstr('keyword');
        clean_xss($start);
        clean_xss($end);
        clean_xss($keyword);
        $page = gint('page');
        $page = $page < 1 ? 1 : $page;
        $num = 15;
        $search = "";
        if($start) $search .= "&start={$start}";
        if($end) $search .= "&end={$end}";
        if($keyword) $search .= "&keyword={$keyword}";

        $info = $this->mod->consume($start,$end,$page,$num,$keyword);
        $page_content = page(ceil($info['total']/$num), $page, "?c=finance&a=consume{$search}&page");
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
           // 'total_money' => $info['total_money'],
            'start' => $info['start'],
            'end' => $info['end'],
            'keyword' => $keyword,
            'menu' => 'finance',
        );
        Factory::getView("finance/consume", $data);
    }
    public function ajaxtotal(){
        $login_user = app_get_login_user(1, 1);
        $start = gstr('start');
        $end = gstr('end');
        $keyword = gstr('keyword');
        clean_xss($start);
        clean_xss($end);
        clean_xss($keyword);
        $page = gint('page');
        $page = $page < 1 ? 1 : $page;
        $num = 15;
        $search = "";
        if($start) $search .= "&start={$start}";
        if($end) $search .= "&end={$end}";
        if($keyword) $search .= "&keyword={$keyword}";
        $info = $this->mod->ajaxtotal($start,$end,$page,$num,$keyword);
        
    }
    /**
     * 充值记录
     */
    public function recharge(){
        $login_user = app_get_login_user(1, 1);
        $start = gstr('start');
        $end = gstr('end');
        $keyword = gstr('keyword');
        clean_xss($start);
        clean_xss($end);
        clean_xss($keyword);

        $page = gint('page');
        $page = $page < 1 ? 1 : $page;
        $num = 15;
        $search = "";
        if($start) $search .= "&start={$start}";
        if($end) $search .= "&end={$end}";
        if($keyword) $search .= "&keyword={$keyword}";

        $info = $this->mod->recharge($start,$end,$page,$num,$keyword);
        $page_content = page(ceil($info['total']/$num), $page, "?c=finance&a=recharge{$search}&page");
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'start' => $info['start'],
            'end' => $info['end'],
            'keyword' => $keyword,
            'menu' => 'finance',
            'total_money' => $info['total_money'],
        );
        Factory::getView("finance/recharge", $data);
    }


    public function download(){
        $start = gstr('start');
        $end = gstr('end');
        $info = $this->mod->consume($start,$end);
        $this->mod->download($info['list']);
    }

    public function yijian(){
        $login_user = app_get_login_user(1, 1);
        $page = gint('page');
        $page = $page < 1 ? 1 : $page;
        $num = 15;
        $info = $this->mod->yijian($page,$num);
        $page_content = page(ceil($info['total']/$num), $page, "?c=finance&a=yijian&page");
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'menu' => 'yijian',
        );
        Factory::getView("finance/yijian", $data);
    }

    public function notice(){
        $login_user = app_get_login_user(1, 1);
        $page = gint('page');
        $page = $page < 1 ? 1 : $page;
        $num = 15;
        $info = $this->mod->notice($page,$num);
        $page_content = page(ceil($info['total']/$num), $page, "?c=finance&a=notice&page");
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'menu' => 'notice',
        );
        Factory::getView("finance/notice", $data);
    }

    public function noticeEdit(){
        $id = gint('id');
        $login_user = app_get_login_user(1, 1);
        $data = array(
            'login_user' => $login_user,
            'id' => $id,
            'info' => $this->mod->notice_info($id),
            'menu' => 'notice',
        );
        Factory::getView("finance/notice_edit", $data);
    }

    public function noticeSave(){
        $data = pstr('data');
        $data['content'] = str_replace("\n",'<br>',$data['content']);
        $id = pint('id');
        $res = $this->mod->notice_save($data,$id);

        echo_result($res['state'],$res['msg']);
    }
    
    public function noticeZiding(){
        $id = pint('id');
        $res = $this->mod->noticeZiding($id);
        echo_result($res['state'],$res['msg']);
    }

    public function noticeQuxiaoziding(){
        $id = pint('id');
        $res = $this->mod->noticeQuxiaoziding($id);
        echo_result($res['state'],$res['msg']);
    }

    public function noticeShanchu(){
        $id = pint('id');
        $res = $this->mod->noticeShanchu($id);
        echo_result($res['state'],$res['msg']);
    }
    
    public function version(){
        $login_user = app_get_login_user(1, 1);
        $code = include (dirname(dirname(__FILE__)).'/conf/version.conf.php');
        foreach($code as &$v){
            $v = urldecode($v);
        }
        $data = array(
            'login_user' => $login_user,
            'info' => $code,
            'menu' => 'finance',
            'sub_menu' => 'version',
        );

        Factory::getView("finance/version", $data);
    }

    public function savev(){
        $v = urlencode(pstr('v'));
        $size = urlencode(pstr('size'));
        $url = urlencode(pstr('url'));
        $desc = urlencode(pstr('desc'));
        $array = array(
            'v' => $v,
            'size' => $size,
            'url' => $url,
            'desc' => $desc,
        );
        file_put_contents(dirname(dirname(__FILE__)).'/conf/version.conf.php',"<?php\n return ".var_export($array,true).';');
        header("Location: ?c=finance&a=version");
        exit;
    }
    //查询业务员业绩
    public function achievement(){
        $login_user = app_get_login_user(1, 1); 
        $page = gint('page');
        $page = $page < 1 ? 1 : $page;
        $num = 15;
        $search = "";
       

        /*$info = $this->mod->recharge($start,$end,$page,$num,$keyword);
        $page_content = page(ceil($info['total']/$num), $page, "?c=finance&a=recharge{$search}&page");*/
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'start' => $info['start'],
            'end' => $info['end'],
            'keyword' => $keyword,
            'menu' => 'achievement',
            'total_money' => $info['total_money'],
        );
        Factory::getView("finance/achievement", $data);
    }
    //业务业绩查询
    public function ajaxachievement(){
        $login_user = app_get_login_user(1, 1); 
        $uid=pstr('uid')+0; 
        $start = pstr('start');
        $end = pstr('end'); 
        $where='';
        if($start && $end){
            $s=strtotime($start);
            $e=strtotime($end.'23:59:59');
            $where=" and rt >$s and rt < $e ";
        }else{
             $msg['code']=3;
             $msg['msg']='请选择时间';
             echo json_encode($msg);exit;
        }  
        if(!$uid) {
             $msg['code']=2;
             $msg['msg']='请选择用户';
             echo json_encode($msg);exit;
           
        }
        $nc_list = Factory::getMod('nc_list'); 
        $nc_list->setDbConf('main', 'user');
        $sql="select uid from {$nc_list->dbConf['tbl']} where rebate_uid=$uid ";  
        $alluser=  $nc_list->getDataBySql($sql,false);
        $userstr='';
        foreach($alluser as $v){
            $users[]=$v['uid'];
        }
          
        if(!empty($users)){
             $userstr=implode($users,',');
             $nc_list->setDbConf('shop', 'activity_user'); 
             $sql="select SUM(user_num) as money from {$nc_list->dbConf['tbl']} where uid  in ($userstr) $where";  
             $allmoney= $nc_list->getDataBySql($sql,false);
        } 
         
        $msg['code']=0;
        $msg['count']=count($users);
        $msg['money']=$allmoney[0]['money'];

        echo json_encode($msg);exit;
    }

 
    public function refundlist(){
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

        $info = $this->mod->refundlist($page,$num,$keyword); 
        $page_content = page(ceil($info['total']/$num), $page, "?c=finance&a=refundlist{$search}&page");
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'keyword' => $keyword,
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'menu' => 'refundlist',
        );
        //var_dump($info);exit;
        Factory::getView("finance/refundlist", $data);
    }
}