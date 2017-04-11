<?php
/**
 * Created on 2010-5-27
 *
 * API通用父类，供子类继承
 * 
 * 本类的目的就是为了统一游玩网API调用规范，要求：
 * 1. 所有的API接口都需要继承本class来
 * 2. 所有的接口都需要统一输出格式
 * 3. 原则上，能通过GET访问的，不要使用POST
 * 4. 当出现命名相同时，GET优先级高于POST
 * 
 * 返回值规范定义：
 * array (
 * 	code  => 1/0/-1/-2/-3
 * 	msg   => '',
 * 	data  => array(),
 * )
 * 
 * code定义：
 * 1， 表示API函数执行成功
 * 除1以外表示执行失败 	如果code<0 时，失败信息会被记录日志中
 * 
 * msg:成功或是失败时提示信息
 * 
 * data  : 返回的数据信息
 * 
 * @author wangyihuang
 */

class ServerApi extends BaseController {

    public function __construct() {
    	
    	// 统一返回值定义
		// ret = array(code, msg, data)
		// code: 成功、失败信息
		// msg:  返回消息
		// data:如果需要返回数据集，保存在这里
		$ret = array('code'=>-1, 'msg'=>'未执行任何操作', 'data'=>array());
		$_token  = $this->getVar("_token");
		$server = $this->getVar("server");
		$do     = $this->getVar('do');
		if ($_token != md5(sprintf('%u', crc32("sse3$%3667&". strrev($server."|".$do))))) {
		    $ret = array('code'=>-1, 'msg'=>'非法调用API，接口验证不正确 ', 'data'=>array());
		} else {
    		$do || $do = "index";
    		if (!($do && method_exists($this, $do))) {
    			$ret['code'] = -1;
    			$ret['msg'] = '非法操作，API调用的处理函数不存在';
    		}  else {			
    			$ret = $this->$do($ret);
    		}
		}
		echo json_encode($ret);
		exit;
    }
    
    /**
	 * 得到API传输过来的一个变量
	 * 处理方法，如果在$_GET里定义，直接返回$_GET里的值
	 * 如果在$_POST里定义，返回$_POST里的值
	 * 
	 * $_GET优先于$_POST
	 * 
	 * 否则，返回false
	 */
	protected function getVar($var_name) {
		
		if (empty($var_name)) return null;
		
		if (isset($_GET[$var_name])) return $_GET[$var_name];
		if (isset($_POST[$var_name])) return $_POST[$var_name];
		
		return null;
	}
    
	/**
	 * 用于controller返回数据结果
	 *
	 * @param $code
	 * @param $msg
	 * @param $data
	 * @return $arr
	 */
    public function sendApiResult($code=0, $msg='未知错误', $data=array(), $total='') {
    	
    	$ret = array(
			'code'=>$code,
			'msg' =>$msg,
		);
		
		if (is_numeric($total)) $ret['total'] = $total;
		$ret['data'] = $data;
		
		$format = 'json';
		if (isset($_GET['format']) && $_GET['format']) $format = $_GET['format'];
		
		if ($format == 'json') {
			
			$callback = isset($_GET['callback']) ? $_GET['callback'] : '';
			
			if ($callback) {
				echo $callback.'('.json_encode($ret).')';
			} 
			else {
				echo json_encode($ret);
			}
		}
		else {
			echo 'unsupport format error!';
		}
	    exit;
    }
}
?>