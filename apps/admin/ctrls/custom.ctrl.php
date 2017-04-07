<?php
/**
 * Created by PhpStorm.
 * User: hzm
 * Date: 2016/6/3
 * Time: 14:16
 */
class CustomCtrl extends BaseCtrl{
    private $mod;
    public function __construct(){
        $this->mod = Factory::getMod('custom');
    }

    /**
     * 刷单设置页面
     */
    public function index(){
        $login_user = app_get_login_user(1, 1);
        if(!$login_user['is_super']) exit;
        $list = $this->mod->allGoodsList();
        $info = $this->mod->getInfo();
        $data = array(
            'login_user' => $login_user,
            'list' => $list,
            'info' => $info,
            'menu' => 'custom',
        );
        Factory::getView("custom/index", $data);
    }

    /**
     * 保存刷单页面
     */
    public function save(){
        $login_user = app_get_login_user(1, 1);
        if(!$login_user['is_super']) exit;
        $data = pstr('data');
        $res = $this->mod->save($data);
        echo_result($res['state'],$res['msg']);
    }
}