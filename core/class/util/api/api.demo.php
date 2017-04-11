<?php
/**
 * 客户端API接口使用例子
 *
 */
class UavnBBsApi extends ClientApi {
    
    function getUserPost() {
        $ret = "这里从CACHE中取数据!";
        if ($ret) {
            return $ret;
        } 
        
        //如果CACHE失败
        //通过http接口取数据
        $ret = $this->doApi("bbs","get_user_post",array("user_id"=>20110));
        
        return $ret;
    }
}


/**
 * 各个项目自己提供的http接口的服务端API的例子
 *
 */
class ApiController extends ServerApi {
    
    /**
     * 如果ApiController有构造函数 
     * 
     * 一定要在构造函数 结束的最后一个加入
     * 
     * parent::__construct();
     * 
     * parent::__construct()中，会对参数做验证
     *
     */
    public function __construct() {
        parent::__construct();
    }
    
    public function getUserPost() {
        $ret = "这里是执行module返回的数据结果";
        
        //执行完以后，调用统一的返回结果的方法
        if ($ret) {
            //code = 1 时表示成功，其它情况都是失败
            //根据自己的设定设置msg
            $this->sendApiResult(1,"成功",$ret);
        } else {
            //如果是失败，根据自己的业务设定code 和 msg
            $this->sendApiResult(0,"失败");
        }
    }
}
?>