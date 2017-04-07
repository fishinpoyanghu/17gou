<?php
class TeamCtrl extends BaseCtrl{

    public function index(){
        $login_user = app_get_login_user(1, 1);
        $mod = Factory::getMod('team');
        $id = gint('id');
        $type = $mod->goodsType();
        $info = $mod->teamgoods($id);
        $data = array(
            'login_user' => $login_user, 
            'type' => $type['list'],
            'info' => $info,
            'id' => $id,
            'menu' => 'teamList',
        );
        Factory::getView("team/team_goods", $data);
    }

    public function teamGoodsList(){
        $login_user = app_get_login_user(1, 1);
        $mod = Factory::getMod('team');
        $page = gint('page');
        $num = 10;
        $keyword = gstr('keyword');
        clean_xss($keyword);
        $type = $mod->goodsType();
        $search = "";
        if($keyword){
            $search = "&keyword={$keyword}";
        }

        $info = $mod->teamGoodsList($page,$num,$keyword);
        $page_content = page(ceil($info['total']/$num), $page, "?c=team&a=teamGoodsList{$search}&page");
 
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'keyword' => $keyword,
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'menu' => 'teamList',
            'type' => $type['list'],
        );
        Factory::getView("team/team_list", $data);
    }
    
    public function remen(){
        $id = gint('id');
        $mod = Factory::getMod('team');
        $state = $mod->remen($id);
        echo_result(0,'成功');
    }

    /**
     * 批量修改商品
     */
    public function modifyGoods(){
        $ids = pstr('ids');
        $type = pint('type');
        $mod = Factory::getMod('team');
        $state = $mod->modifyGoods($ids,$type);
        echo_result($state['state'],$state['msg']);
    }

    public function delGoods(){
        $id = gint('id');
        $mod = Factory::getMod('team');
        $res = $mod->delGoods($id);
        echo_result($res['state'],$res['msg']);
    }

     

      
 

    /**
     * 富文本图片上传
     */
    public function editorUpload(){
        $action = gstr('action');
        $mod = Factory::getMod('editor');
        $result = $mod->editor($action);
        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match('/^[\w_]+$/', $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array('state' => ''));
            }
        } else {
            echo json_encode($result);
        }
    }

    /**
     * 保存添加的商品
     */
    public function saveTeamGoods(){
        $data = pstr('data');
        $id = pint('id');
        $mod = Factory::getMod('team');  

       
        $res = $mod->saveTeamGoods($data,$id); 
        echo_result($res['state'],$res['msg']);
    }
 
 

 

 

 

    public function activity(){
        $login_user = app_get_login_user(1, 1);
        $page = gint('page'); 
        $keyword = gstr('keyword');
        clean_xss($keyword);
        $search = "";
        if($keyword){
            $search = "&keyword={$keyword}";
        }
        $page = $page < 1 ? 1 : $page;
        $num = 15;
        $mod = Factory::getMod('team');
        $info = $mod->activity($page,$num,$keyword); 
        $page_content = page(ceil($info['total']/$num), $page, "?c=team&a=activity{$search}&page");
 
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'keyword' => $keyword,
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'menu' => 'teamactivity',
        ); 
        
        Factory::getView("team/activity", $data);
    }
     public function start(){
        $login_user = app_get_login_user(1, 1);
        $id = gint('id');
        if(!$id){
             echo_result(0,'缺小商品id');
        }
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('team', 'team_goods');
        $sql = "update {$nc_list->dbConf['tbl']} set `status`=1  where `goods_id`={$id}";  
        $row= $nc_list->executeSql($sql); 
        echo_result($row);
    }

    public function startactivity(){
        $login_user = app_get_login_user(1, 1);
        $id = gint('id');
        if(!$id){
             echo_result(0,'缺小商品id');
        } 
        $id = gint('id');
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('team', 'team_goods');
        $sql = "update {$nc_list->dbConf['tbl']} set `is_in_activity`=2  where `goods_id`={$id}";  
        $row= $nc_list->executeSql($sql); 
        echo_result($row); 

    }
    public function stop(){
        $login_user = app_get_login_user(1, 1);
        $id = gint('id');
        if(!$id){
             echo_result(0,'缺小商品id');
        }
        $mod = Factory::getMod('team');
        $res = $mod->stop($id);
        if($res){
            echo_result($res,'下架成功');
        }else{
            echo_result($res,'下架失败，当前商品仍在参团中,请先结束活动！');
        }
         
    }

    public function endActivity(){
        $a_id = gint('id');
       $mod = Factory::getMod('team');
        $res = $mod->endActivity($a_id);
        echo_result($res['state'],$res['msg']); 
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
        $mod = Factory::getMod('team');
        $info = $mod->record($id,$page,$num,$type);
            
        $page_content = page(ceil($info['total']/$num), $page, "?c=team&a=record&page");
       
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'menu' => 'teamactivity',
        );
        Factory::getView("team/record", $data);
    }

    public function order(){  //团购订单..
        $login_user = app_get_login_user(1, 1);
        $page = gint('page');
        $page = $page < 1 ? 1 : $page;
        $state = gint('state');
        $state = in_array($state,array(0,1,2)) ? intval($state) : 0;
        $num = 15;
        $mod = Factory::getMod('team');
        $info = $mod->order($page,$num,$state);
        $page_content = page(ceil($info['total']/$num), $page, "?c=team&a=order&state=".$state."&page");
        $express = include get_app_root().'/conf/express.conf.php';
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'state' => $state,
            'express' => $express,
            'menu' => 'teamorder',
        );
        Factory::getView("team/order_list", $data);
    }

    public function baituanorder(){   
        $login_user = app_get_login_user(1, 1);
        $page = gint('page');
        $page = $page < 1 ? 1 : $page;
        $state = gint('state');
        $state = in_array($state,array(0,1,2)) ? intval($state) : 0;
        $num = 15;
        $mod = Factory::getMod('team');
        $info = $mod->baituan_order($page,$num,$state);
        $page_content = page(ceil($info['total']/$num), $page, "?c=team&a=baituanorder&state=".$state."&page");
        $express = include get_app_root().'/conf/express.conf.php';
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'state' => $state,
            'express' => $express,
            'menu' => 'baituan',
        );
        Factory::getView("team/baituan_order_list", $data);  
    }

     /**
     * 拼团商品分类列表
     */
    public function teamclassify(){
        $login_user = app_get_login_user(1, 1);
        $mod = Factory::getMod('team');
        $t_mod = Factory::getMod('table');
        $base_cfg = $t_mod->getBaseCfg();
        $page = gint('page');
        $num = 10;
        $keyword = gstr('keyword');
        clean_xss($keyword);
        $search = "";
        if($keyword){
            $search = "&keyword={$keyword}";
        }  
        $res = $mod->goodsType($page,$num,$keyword);

        $page_content = page(ceil($res['total']/$num), $page, "?c=team&a=teamclassify{$search}&page");
        $data = array(
            'login_user' => $login_user,
            'global_cfg' => array('nav'=>$base_cfg['nav'], 'menu_id'=>$base_cfg['menu_id']),
            'list' => $res['list'],
            'keyword' => $keyword,
            'page_content' => $page_content,
            'page_total' => $res['total'],
            'page_num' => $num,
            'menu' => 'teamclassify',
        );

        Factory::getView("team/teamclassify", $data);
    }
     /**
     * 添加商品分类页面
     */
    public function addGoodsCfy(){
        $login_user = app_get_login_user(1, 1);
        $id = gint('id');
        $pid = gint('pid');
        $data = array('login_user'  => $login_user,'menu' => 'teamclassify');
        if($id){
            $mod = Factory::getMod('team');
            $data['info'] = $mod->getGoodsCfy($id);
        }
        if($pid){
            $data['pid'] = $pid;
        }
        Factory::getView("team/add_goods_cfy",$data);
    }

    /**
     * 添加或编辑商品分类
     */
    public function editGoodsCfy(){
        $id = gint('id');
        $pid = gint('pid');
        $name = gstr('name');
        $url = gstr('url');
        $sort = gstr('sort');
        $mod = Factory::getMod('team');
        $state = $mod->editGoodsCfy($name,$url,$id,$pid,$sort);
        echo_result($state['state'],$state['msg']);
    }

    /**
     * 删除商品分类
     */
    public function delGoodsCfy(){
        $ids = gstr('ids');
        $ids = explode(',',$ids);
        foreach($ids as &$i){
            $i = intval($i);
        }
        $ids = implode(',',$ids);
        $mod = Factory::getMod('team');
        $res = $mod->delGoodsCfy($ids);
        
        echo_result($res,'');
    }
     

    public function showindex(){
        $id = pint('id');
        $show=pint('show');
        $mod = Factory::getMod('team');
        $res = $mod->showindex($id,$show);
        echo_result($res['state'],$res['msg']);
    }

}