<?php
/**
 * Created by PhpStorm.
 * User: Ace
 * Date: 2015/11/11
 * Time: 0:18
 */

require_once "bbs.Data.php";

class BbsApi{
    //注册功能
    public static function Register($inputObj=null){
        //校验字段
        if(!($inputObj->getUsername()&&$inputObj->getPassword()&&$inputObj->getRpassword())){
            return json_encode(array("status"=>false,"info"=>"请输入邮箱和密码"));
        }
        if ($inputObj->getPassword() != $inputObj->getRpassword()) {
            $msg = array("status"=>false,"info"=>"两次密码不相等");
            return json_encode($msg);
        }
        if(strlen($inputObj->getPassword())<6||strlen($inputObj->getPassword())>12){
            $msg = array("status"=>false,"info"=>"密码长度须为6到12位");
            return json_encode($msg);
        }
        if (!filter_var($inputObj->getUsername(), FILTER_VALIDATE_EMAIL)) {
            $msg = array("status"=>false,"info"=>"邮箱格式错误");
            return json_encode($msg);
        }
        $sendData = $inputObj->GetValues();
        $bbsApiCtrl=Factory::getModuleCtrl("scene","bbsApi");
        $starttime = self::getMillisecond();
        $ret = call_user_func_array(array($bbsApiCtrl,"__register"),array($sendData));//php 5.2 以下无法使用 变量名调用静态函数
        self::reportCostTime("register",$starttime,$ret);
        return $ret;
    }

    public static function Login($inputObj=null){
        //基本字段校验
        $username = $inputObj->getUsername();
        $password = $inputObj->getPassword();
        //验证输入字段
        if(!$username||!$password){
            $text = "账号或密码为空";
            $msg = array("status"=>false,'info'=>$text);
            return json_encode($msg);
        }
        $sendData = $inputObj->GetValues();
        $bbsApiCtrl=Factory::getModuleCtrl("scene","bbsApi");
        $starttime = self::getMillisecond();
        $ret = call_user_func_array(array($bbsApiCtrl,"__login"),array($sendData));//php 5.2 以下无法使用 变量名调用静态函数
        self::reportCostTime("login",$starttime,$ret);
        return $ret;
    }


    public static function Userinfo($inputObj=null){

        if(!$inputObj->getUid()){
            $msg = array("status"=>false,'info'=>"uid不能为空");
            return json_encode($msg);
        }
        $sendData = $inputObj->GetValues();
        $bbsApiCtrl=Factory::getModuleCtrl("scene","bbsApi");
        $starttime = self::getMillisecond();
        $ret = call_user_func_array(array($bbsApiCtrl,"__userinfo"),array($sendData));
        self::reportCostTime("userinfo",$starttime,$ret);
        return $ret;
    }

    /**
     * 找回密码
     */
    public function Find($inputObj){
        if(!$inputObj->getUsername()||!$inputObj->getPassword()||!$inputObj->getRPASSWORD()){
            $msg = array("status"=>false,"info"=>"请输入正确信息");
            return json_encode($msg);
        }
        if ($inputObj->getPassword() != $inputObj->getRpassword()) {
            $msg = array("status"=>false,"info"=>"两次密码不相等");
            return json_encode($msg);
        }
        if(strlen($inputObj->getPassword())<6||strlen($inputObj->getPassword())>12){
            $msg = array("status"=>false,"info"=>"密码长度为6到12位");
            return json_encode($msg);
        }
        if (!filter_var($inputObj->getUsername(), FILTER_VALIDATE_EMAIL)) {
            $msg = array("status"=>false,"info"=>"邮箱格式错误");
            return json_encode($msg);
        }
        $sendData = $inputObj->GetValues();
        $bbsApiCtrl=Factory::getModuleCtrl("scene","bbsApi");
        $starttime = self::getMillisecond();
        $ret = call_user_func_array(array($bbsApiCtrl,"__find"),array($sendData));
        self::reportCostTime("find",$starttime,$ret);
        return $ret;
    }


    /**
     *编辑用户个人资料
     */
    public function EditInfo($inputObj){
        if(!$inputObj->getUid()){
            $msg = array("status"=>false,'info'=>"uid不能为空");
            return json_encode($msg);
        }
        $sendData = $inputObj->GetValues();
        $bbsApiCtrl=Factory::getModuleCtrl("scene","bbsApi");
        $starttime = self::getMillisecond();
        $ret = call_user_func_array(array($bbsApiCtrl,"__editinfo"),array($sendData));
        self::reportCostTime("editinfo",$starttime,$ret);
        return $ret;
    }

    /**
     * 修改账号密码
     *
     */
    public function EditPassword($inputObj){
        //校验字段
        if(!($inputObj->getUid()&&$inputObj->getOldPassword()&&$inputObj->getNewpassword()&&$inputObj->getRpassword())){
            return json_encode(array("status"=>false,"info"=>"请输入正确信息"));
        }
        if ($inputObj->getNewpassword() != $inputObj->getRpassword()) {
            $msg = array("status"=>false,"info"=>"两次密码不相等");
            return json_encode($msg);
        }
        if(strlen($inputObj->getNewpassword())<6||strlen($inputObj->getNewpassword())>12){
            $msg = array("status"=>false,"info"=>"密码长度须为6到12位");
            return json_encode($msg);

        }
        $sendData = $inputObj->GetValues();
        $bbsApiCtrl=Factory::getModuleCtrl("scene","bbsApi");
        $starttime = self::getMillisecond();
        $ret = call_user_func_array(array($bbsApiCtrl,"__editPassword"),array($sendData));
        self::reportCostTime("editpassword",$starttime,$ret);
        return $ret;
    }


    /**
     *
     * 上报数据， 上报的时候将屏蔽所有异常流程
     * @param string $usrl
     * @param int $startTimeStamp
     * @param array $data
     */
    private static function reportCostTime($action, $startTimeStamp, $data)
    {
        $data = json_decode($data,true);
        $data['info'] = json_encode( $data['info'] );
        //如果不需要上报数据
        if(bbsConfig::REPORT_LEVENL == 0){
            return;
        }
        //如果仅失败上报
        if(bbsConfig::REPORT_LEVENL == 1 &&
            array_key_exists("status", $data) &&
            $data["status"] == true)
        {
            return;
        }

        //上报逻辑
        $endTimeStamp = self::getMillisecond();
        $objInput = new bbsApiReport();
        $objInput->setExecutetime($endTimeStamp - $startTimeStamp);
        $objInput->setAction($action);
        $objInput->setInfo($data['info']);
        $objInput->setStatus($data['status']?1:0);

        try{
            $sendData = $objInput->GetValues();
            $bbsApiCtrl=Factory::getModuleCtrl("scene","bbsApi");
            $ret = call_user_func_array(array($bbsApiCtrl,"__report"),array($sendData));
        } catch (bbsException $e){
            //不做任何处理
        }
    }
    /**
     * 获取毫秒级别的时间戳
     */
    private static function getMillisecond()
    {
        //获取毫秒的时间戳
        $time = explode ( " ", microtime () );
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode( ".", $time );
        $time = $time2[0];
        return $time;
    }
}