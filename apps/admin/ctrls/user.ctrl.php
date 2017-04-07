<?php
class UserCtrl extends BaseCtrl{


   public function userlist(){
        $login_user = app_get_login_user(1, 1);
        $mod = Factory::getMod('admin');
        $page = gint('page');
        $num = 10;
        $keyword = gstr('keyword');
        $type = gstr('type')+0;
 
        clean_xss($keyword);
        $search = "";
        if($keyword){
            $search = "&keyword={$keyword}";
        }
        $search.=$type?'&type='.$type:''; 
        $sarchArr['type']=$type; 
        $sarchArr['keyword']=$keyword; 
        $info = $mod->userList($page,$num,$sarchArr);
        $page_content = page(ceil($info['total']/$num), $page, "?c=user&a=userlist{$search}&page");
        foreach($info['list'] as $k=>$v){
            if($v['type']==0){
                $info['list'][$k]['icon']=tpf_user_icon_show($v['icon']);
            }else if($v['type']==1){
                $info['list'][$k]['icon']=tpf_img_show($v['icon'],54,54);  
            }
             
        } 
        //var_dump($info['commission_uid']);exit;
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'keyword' => $keyword,
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'menu' => 'userList',
            'commission_user'=>$info['commission_user'],
            'commission_uid'=> explode(',',$info['commission_uid']),
            'type'=>$type,
            'cate'=>$cate,
           
        );
        Factory::getView("user/user_list", $data);
    }
    public function commission(){
        $login_user = app_get_login_user(1, 1);   
        if(!$login_user['is_super']){
            echo_result(0,'没有权限!');
        }
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('main', 'sysset');
        $sql = "select commission_uid from  {$nc_list->dbConf['tbl']}  ";  
        $msg= $nc_list->getdatabysql($sql); 
        $array=explode(',',$msg[0]['commission_uid']);
        if($_GET['chosen']=='1'){
            $array[]=$_GET['id']+0;
        }else{
             $key = array_search($_GET['id'], $array);
             unset($array[$key]);
        }

        $str=implode(',', $array);
         
        $sql = "update {$nc_list->dbConf['tbl']} set `commission_uid`='{$str}'";  
        $row= $nc_list->executeSql($sql); 
         echo_result($row);

    }
    public function addmoney(){
        $login_user = app_get_login_user(1, 1);   
        if(!$login_user['is_super']){
            echo_result(0,'没有权限!');
        }
        $money = gint('money');
        $uid=gint('id');
        if(!($uid && $money)){
             echo_result(0,'金额和用户id不能为空');
        }  
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'user');
        $sql = "update {$nc_list->dbConf['tbl']} set `money`=`money`+$money  where `uid`={$uid}";  
        $row= $nc_list->executeSql($sql); 
        if($row){ 
             file_put_contents('/tmp/add_money_info.log','{"money":'.$money.',"时间":'.'"'.date('Y-m-d H:i:s').'","用户":"'.$login_user['name'].'","uid":'.$login_user['uid'].'},',FILE_APPEND);
        }
        echo_result($row,$money);

    }
 


}
        echo_result($row,$money);

    }
 


}