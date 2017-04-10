<?php
/**
 * Created by PhpStorm.
 * User: Ace
 * Date: 2015/11/11
 * Time: 0:19
 */

/**
 * Class bbsDataBase
 * 定义基本行为
 */

require_once "bbs.Tool.php";
require_once "bbs.Exception.php";
require_once "bbs.Config.php";

class bbsDataBase{

    protected $values;

    /**
     * 获取设置的值
     */
    public function GetValues()
    {
        return $this->values;
    }

}


class LoginData extends bbsDataBase{

    public function setUsername($username=null){
        $this->values['username'] = $username;
    }

    public function setPassword($password=null){
        $this->values['password'] = $password;
    }

    public function getUsername(){
       return $this->values['username'];
    }

    public function getPassword(){
        return $this->values['password'];
    }

}


class RegisterData extends bbsDataBase{

    public function setUsername($username=null){
        $this->values['username'] = $username;
    }

    public function setPassword($password=null){
        $this->values['password'] = $password;
    }

    public function setRpassword($rpassword=null){
        $this->values['rpassword'] = $rpassword;
    }

    public function getUsername(){
        return $this->values['username'];
    }

    public function getPassword(){
        return $this->values['password'];
    }

    public function getRpassword(){
        return $this->values['rpassword'];
    }

}

/**
 * Class UserData
 * 获取用户信息
 */
class UserInfoData extends bbsDataBase{

    public function getUid(){
        return $this->values['uid'];
    }
    public function setUid($uid){
        $this->values['uid'] = $uid;
    }

}

/**
 * Class retSetData
 * 重置找回密码
 */
class FindData extends bbsDataBase{

    public function setUsername($username){
        $this->values['username'] = $username;
    }
    public function setPassword($password){
        $this->values['password'] = $password;
    }
    public function setRpassword($rpassword){
        $this->values['rpassword'] = $rpassword;
    }
    public function getUsername(){
        return $this->values['username'];
    }
    public function getPassword(){
        return $this->values['password'];
    }
    public function getRpassword(){
        return $this->values['rpassword'];
    }

}



class EditInfoData extends bbsDataBase{

    public function setUid($uid){
        $this->values['uid'] = $uid;
    }
    public function setInfo($info){
        $this->values['info'] = $info;
    }
    public function getUid(){
       return  $this->values['uid'];
    }
    public function getInfo(){
       return  $this->values['info'];
    }

}

/**
 * Class editpassword
 * 修改密码
 */
class EditPasswordData extends bbsDataBase{

    public function setUid($uid){
        $this->values['uid'] = $uid;
    }
    public function setOldPassword($oldpassword){
        $this->values['oldpassword'] = $oldpassword;
    }
    public function setNewpassword($newpassword){
        $this->values['newpassword'] = $newpassword;
    }
    public function setRpassword($rpassword){
        $this->values['rpassword'] = $rpassword;
    }
    public function getUid(){
        return $this->values['uid'];
    }
    public function getOldPassword(){
        return $this->values['oldpassword'];
    }
    public function getNewpassword(){
        return $this->values['newpassword'];
    }
    public function getRpassword(){
        return $this->values['rpassword'];
    }

}

//bbs 结果上报
class bbsApiReport extends bbsDataBase{

    public function setExecutetime($time){
        $this->values['time'] = $time;
    }

    public function getExecutetime(){
       return $this->value['time'];
    }
    public function setStatus($status){
        $this->values['status'] = $status;
    }

    public function getStatus(){
        return $this->values;
    }

    public function setInfo($info){
        $this->values['info'] = $info;
    }

    public function getInfo(){
        return $this->values['info'];
    }

    public function setAction($action){
        $this->values['action'] = $action;
    }

    public function getAction(){
        return $this->values['action'];
    }
}




