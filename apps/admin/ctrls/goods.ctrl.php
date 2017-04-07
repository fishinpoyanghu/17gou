<?php
class GoodsCtrl extends BaseCtrl{

    public function index(){
        $login_user = app_get_login_user(1, 1);
        $mod = Factory::getMod('goods');
        $id = gint('id');
        $type = $mod->goodsType();
        $info = $mod->goods($id);
        $data = array(
            'login_user' => $login_user,
            'type' => $type['list'],
            'info' => $info,
            'id' => $id,
            'menu' => 'goodsList',
        );
        Factory::getView("goods/goods", $data);
    }

    public function goodsList(){
        $login_user = app_get_login_user(1, 1);
        $mod = Factory::getMod('goods');
        $page = gint('page');
        $num = 10;
        $keyword = gstr('keyword');
        $activity_type = gstr('activity_type')+0;
        $cate = gstr('cate')+0;
        clean_xss($keyword);
        $search = "";
        if($keyword){
            $search = "&keyword={$keyword}";
        }
        $search.=$activity_type?'&activity_type='.$activity_type:'';
        $search.=$cate?'&cate='.$cate:''; 
        $sarchArr['activity_type']=$activity_type;
        $sarchArr['cate']=$cate;
        $sarchArr['keyword']=$keyword;
        $type = $mod->goodsType();
        $info = $mod->goodsList($page,$num,$sarchArr);
        $page_content = page(ceil($info['total']/$num), $page, "?c=goods&a=goodsList{$search}&page");
        $data = array(
            'login_user' => $login_user,
            'list' => $info['list'],
            'keyword' => $keyword,
            'page_content' => $page_content,
            'page_total' => $info['total'],
            'page_num' => $num,
            'menu' => 'goodsList',
            'activity_type'=>$activity_type,
            'cate'=>$cate,
            'type' => $type['list'],
        );
        Factory::getView("goods/goods_list", $data);
    }
    
    public function remen(){
        $id = gint('id');
        $mod = Factory::getMod('goods');
        $state = $mod->remen($id);
        echo_result(0,'成功');
    }

    /**
     * 批量修改商品
     */
    public function modifyGoods(){
        $ids = pstr('ids');
        $type = pint('type');
        $mod = Factory::getMod('goods');
        $state = $mod->modifyGoods($ids,$type);
        echo_result($state['state'],$state['msg']);
    }

    public function delGoods(){
        $id = gint('id');
        $mod = Factory::getMod('goods');
        $res = $mod->delGoods($id);
        echo_result($res['state'],$res['msg']);
    }

    /**
     * 商品分类列表
     */
    public function classify(){
        $login_user = app_get_login_user(1, 1);
        $mod = Factory::getMod('goods');
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

        $page_content = page(ceil($res['total']/$num), $page, "?c=goods&a=classify{$search}&page");
        $data = array(
            'login_user' => $login_user,
            'global_cfg' => array('nav'=>$base_cfg['nav'], 'menu_id'=>$base_cfg['menu_id']),
            'list' => $res['list'],
            'keyword' => $keyword,
            'page_content' => $page_content,
            'page_total' => $res['total'],
            'page_num' => $num,
            'menu' => 'classify',
        );

        Factory::getView("goods/classify", $data);
    }
     

    public function shouin(){
        $id = pint('id');
        $mod = Factory::getMod('goods');
        $res = $mod->shouin($id);
        echo_result($res['state'],$res['msg']);
    }

    public function shouout(){
        $id = pint('id');
        $mod = Factory::getMod('goods');
        $res = $mod->shouout($id);
        echo_result($res['state'],$res['msg']);
    }

    /**
     * 添加商品分类页面
     */
    public function addGoodsCfy(){
        $login_user = app_get_login_user(1, 1);
        $id = gint('id');
        $pid = gint('pid');
        $data = array('login_user'  => $login_user,'menu' => 'classify');
        if($id){
            $mod = Factory::getMod('goods');
            $data['info'] = $mod->getGoodsCfy($id);
        }
        if($pid){
            $data['pid'] = $pid;
        }
        Factory::getView("goods/add_goods_cfy",$data);
    }

    /**
     * 添加或编辑商品分类
     */
    public function editGoodsCfy(){
        $id = gint('id');
        $pid = gint('pid');
        $name = gstr('name');
        $url = gstr('url');
        $mod = Factory::getMod('goods');
        $state = $mod->editGoodsCfy($name,$url,$id,$pid);
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
        $mod = Factory::getMod('goods');
        $res = $mod->delGoodsCfy($ids);
        
        echo_result($res,'');
    }

    /**
     * banner列表
     */
    public function banner(){
        $login_user = app_get_login_user(1, 1);
        $mod = Factory::getMod('goods');
        $t_mod = Factory::getMod('table');
        $base_cfg = $t_mod->getBaseCfg();
        $page = gint('page');
        $num = 15;
        $info = $mod->banner($page,$num);
        $goods_list = $mod->activityList();
        $page_content = page(ceil($info['total']/$num), $page, "?c=goods&a=banner&page");
        $data = array(
            'login_user' => $login_user,
            'global_cfg' => array('nav'=>$base_cfg['nav'], 'menu_id'=>$base_cfg['menu_id']),
            'list' => $info['list'],
            'goods_list' => $goods_list,
            'page_total' => $info['total'],
            'page_num' => $num,
            'page_content' => $page_content,
            'menu' => 'banner',
        );
        Factory::getView("goods/banner", $data);
    }

    public function pcbanner(){
        $login_user = app_get_login_user(1, 1);
        $mod = Factory::getMod('goods');
        $t_mod = Factory::getMod('table');
        $base_cfg = $t_mod->getBaseCfg();
        $page = gint('page');
        $num = 15;
        $info = $mod->pcbanner($page,$num);
        $goods_list = $mod->activityList();
        $page_content = page(ceil($info['total']/$num), $page, "?c=goods&a=pcbanner&page");
        $data = array(
            'login_user' => $login_user,
            'global_cfg' => array('nav'=>$base_cfg['nav'], 'menu_id'=>$base_cfg['menu_id']),
            'list' => $info['list'],
            'goods_list' => $goods_list,
            'page_total' => $info['total'],
            'page_num' => $num,
            'page_content' => $page_content,
            'menu' => 'pcbanner',
        );
        Factory::getView("goods/pcbanner", $data);
    }

    /**
     * 关闭或发布banner
     */
    public function editBanner(){
        $id = gint('id');
        $mod = Factory::getMod('goods');
        $state = $mod->editBanner($id);
        echo_result($state,'');
    }

    /**
     * 删除banner
     */
    public function delBanner(){
        $id = gint('id');
        $mod = Factory::getMod('goods');
        $state = $mod->delBanner($id);
        echo_result($state,'');
    }

    /**
     * 添加banner
     */
    public function addBanner(){
        $login_user = app_get_login_user(2, 2);
        $url = gstr('url');
        $mod = Factory::getMod('goods');
        $state = $mod->addBanner($url,$login_user['name']);
        echo_result($state,'');
    }

    /**
     * 修改banner排序
     */
    public function sortBanner(){
        $id = gint('id');
        $sort = gint('sort');
        $mod = Factory::getMod('goods');
        $state = $mod->sortBanner($id,$sort);
        echo_result($state['state'],$state['msg']);
    }

    /**
     * 添加banner跳转链接
     */
    public function addLink(){
        $id = gint('id');
        $type=gstr('type');
        $goods_id = gint('goods_id');
        $url = urldecode(gstr('url'));
        $mod = Factory::getMod('goods');
        $state = $mod->addLink($id,$goods_id,$url,$type);
        echo_result($state['state'],$state['msg']);
    }

    public function editpcBanner(){
        $id = gint('id');
        $mod = Factory::getMod('goods');
        $state = $mod->editpcBanner($id);
        echo_result($state,'');
    }

    /**
     * 删除banner
     */
    public function delpcBanner(){
        $id = gint('id');
        $mod = Factory::getMod('goods');
        $state = $mod->delpcBanner($id);
        echo_result($state,'');
    }

    /**
     * 添加banner
     */
    public function addpcBanner(){
        $login_user = app_get_login_user(2, 2);
        $url = gstr('url');
        $mod = Factory::getMod('goods');
        $state = $mod->addpcBanner($url,$login_user['name']);
        echo_result($state,'');
    }


    /**
     * 修改banner排序
     */
    public function sortpcBanner(){
        $id = gint('id');
        $sort = gint('sort');
        $mod = Factory::getMod('goods');
        $state = $mod->sortpcBanner($id,$sort);
        echo_result($state['state'],$state['msg']);
    }

    /**
     * 添加banner跳转链接
     */
    public function addpcLink(){
        $id = gint('id');
        $goods_id = gint('goods_id');
        $url = urldecode(gstr('url'));
        $mod = Factory::getMod('goods');
        $state = $mod->addpcLink($id,$goods_id,$url);
        echo_result($state['state'],$state['msg']);
    }
    /**
     *获取积分规则信息
     */
    public function pointRule(){
        $login_user = app_get_login_user(1, 1);
        $mod = Factory::getMod('goods');
        $res = $mod->getPointRule();
        $data = array(
            'login_user'  => $login_user,
            'info' => $res,
            'menu' => 'pointRule',
        );
        Factory::getView("goods/point_rule", $data);
    }

    /**
     * 积分规则详情页面
     */
    public function pointRuleDetail(){
        $login_user = app_get_login_user(1, 1);
        $res = file_exists(dirname(CORE_ROOT).'/uploads/other/one.html');
        $point = $res ? file_get_contents(dirname(CORE_ROOT).'/uploads/other/point.html') : '';
        $data = array(
            'login_user' => $login_user,
            'point' => $point,
            'menu' => 'pointRule',
        );
        Factory::getView("goods/point", $data);
    }

    /**
     * 保存积分规则设置详情
     */
    public function savePointDetail(){
        $content = pstr('content');
        if(empty($content)){
            echo_result(0,'请输入内容');
        }else{
            $res = file_put_contents(dirname(CORE_ROOT).'/uploads/other/point.html',$content);
            echo_result($res);
        }
    }

    /**
     * 保存积分规则
     */
    public function savePointRule(){
        $data = pstr('data');
        $mod = Factory::getMod('goods');
        $res = $mod->savePointRule($data);
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
    public function saveGoods(){
        $data = pstr('data');
        $id = pint('id');
        $mod = Factory::getMod('goods');
  
        
        if($data['activity_type']==4 && $data['need_num']%2!=0){
            echo_result(0,'二人购商品参与人数必须是双数!');
        }
          if($data['activity_type']==6 && $data['need_num']%2!=0){
            echo_result(0,'幸运购商品参与人数必须是双数!');
        } 
        $res = $mod->saveGoods($data,$id);

        echo_result($res['state'],$res['msg']);
    }

    /**
     * 抽奖设置页面
     */
    public function lottery(){
        $login_user = app_get_login_user(1, 1);
        $mod = Factory::getMod('goods');
        $info = $mod->lottery();
        $data = array(
            'login_user'  => $login_user,
            'info' => $info,
            'menu' => 'lottery',
        );
        Factory::getView("goods/lottery", $data);
    }

    /**
     * 删除奖品
     */
    public function delLottery(){
        $id = gint('id');
        $mod = Factory::getMod('goods');
        $row = $mod->delLottery($id);
        echo_result($row);
    }

    /**
     * 添加奖品
     */
    public function addLottery(){
        $type = gint('type');
        $content = gstr('content');
        $mod = Factory::getMod('goods');
        $res = $mod->addLottery($type,$content);
        echo_result($res['state'],$res['msg']);
    }

    /**
     * 修改中奖率
     */
    public function saveLottery(){
        $percent = pstr('percent');
        $mod = Factory::getMod('goods');
        $res = $mod->saveLottery($percent);
        echo_result($res['state'],$res['msg']);
    }

    /**
     * 抽奖记录
     */
    public function lotteryRecord(){
        $login_user = app_get_login_user(1, 1);
        $page = gint('page');
        $num = 20;
        $keyword = gstr('keyword');
        clean_xss($keyword);
        $search = "";
        if($keyword){
            $search = "&keyword={$keyword}";
        }
        $mod = Factory::getMod('goods');
        $res = $mod->lotteryRecord($page,$num,$keyword);
        $page_content = page(ceil($res['total']/$num), $page, "?c=goods&a=lotteryRecord{$search}&page");
        $express = include get_app_root().'/conf/express.conf.php';
        $data = array(
            'login_user'  => $login_user,
            'list' => $res['list'],
            'page_total' => $res['total'],
            'page_num' => $num,
            'page_content' => $page_content,
            'keyword' => $keyword,
            'express' => $express,
            'menu' => 'lotteryRecord',
        );

        Factory::getView("goods/lottery_record", $data);
    }

    /**
     * 商品开始第一期
     */
    public function startFirst(){
        $id = gint('id');
        $mod = Factory::getMod('goods');
        $row = $mod->startFirst($id);
        echo_result($row);
    }
  /**
     * 停止当前商品相关活动
     */
    public function stop(){
        $id = gint('id'); 
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('team', 'team_goods');
        $sql = "select activity_id from  ".DATABASE.".t_activity   where `goods_id`={$id} and flag<2";  
        $row= $nc_list->getdatabysql($sql);  
        foreach($row as $v){
             $activity_mod = Factory::getMod('activity');
             $res = $activity_mod->endActivity($v['activity_id']);
        } 

         
        echo_result($res['state'],$res['msg']);  
    }

    /**
     * 录入物流信息
     */
    public function addExpress(){
        $id = gint('id');
        $code = gstr('code');
        $num = gstr('num');
        $mod = Factory::getMod('goods');
        $row = $mod->addExpress($id,$code,$num);
        echo_result($row);
    }

    public function showbanner(){
        $login_user = app_get_login_user(1, 1);
        $data = array(
            'login_user'  => $login_user,
            'menu' => 'showbanner',
        );
        Factory::getView("goods/showbanner", $data);
    }

    public function maxbanner(){
        $login_user = app_get_login_user(1, 1);
        $data = array(
            'login_user'  => $login_user,
            'menu' => 'maxbanner',
        );
        Factory::getView("goods/maxbanner", $data);
    }

    public function saveShow(){
        $img = pstr('img');
        $r = file_get_contents($img);
        file_put_contents(dirname(CORE_ROOT).'/pc/img/share_02.jpg',$r);
        echo_result(1,'上传成功');
    }

    public function saveMax(){
        $img = pstr('img');
        $r = file_get_contents($img);
        file_put_contents(dirname(CORE_ROOT).'/pc/img/max.jpg',$r);
        echo_result(1,'上传成功');
    }


}