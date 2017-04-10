<?php
require_once "WxPay.Config.php";

/**
 * 获取微信接口的参数
 */
class WxApi{
	
	private $noncestr;
	private $timestamp;
	
	public function getNoncestr(){
		if(empty($this->noncestr)){
			$this->noncestr = $this->createNoncestr();
		}
		return $this->noncestr;
	}
	
	public function getTimestamp(){
		if(empty($this->timestamp)){
			$this->timestamp = time();
		}
		return $this->timestamp;
	}
	
	/**
	 * 获取access_token
	 */
	public function getAccessToken(){
		if(RUN_MOD !='local'){
			return 'textnotoken';
		}
		$access_token = do_cache('get', 'wxjsapi', 'access_token');
		if(empty($access_token)){
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential";
			$url .= "&appid=".WxPayConfig::APPID."&secret=".WxPayConfig::APPSECRET;
			$res = json_decode($this->makeCurl($url), true);
			$access_token = $res['access_token'];
			file_put_contents('/tmp/17goutoken.log', 'time:'.date('Y-m-d',time()). var_export(json_encode($res,true) ,true), FILE_APPEND);
			if(!empty($access_token)){
				do_cache('set', 'wxjsapi', 'access_token', $access_token);
			}
		}
		return $access_token;
	}
	
	/**
	 * 获取jsapi_ticket
	 */
	public function getJsApiTicket(){
		$js_api_ticket = do_cache('get', 'wxjsapi', 'js_api_ticket');
		if(empty($js_api_ticket)){
			$access_token = $this->getAccessToken();
			if(!empty($access_token)){
				$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi";
				$res = json_decode($this->makeCurl($url), true);
				$js_api_ticket = $res['ticket'];
				if(!empty($js_api_ticket)){
					do_cache('set', 'wxjsapi', 'js_api_ticket', $js_api_ticket);
				}
			}
		}
		return $js_api_ticket;
	}
	
	/**
	 * 	作用：产生随机字符串，不长于32位
	 */
	public function createNoncestr(){
		$length = rand(10,32);
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		$str ="";
		for($i = 0; $i < $length; $i++){
			$str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
		}
		return $str;
	}
	
	/**
	 * 获取签名
	 */
	public function getSign($url){
		$noncestr = $this->getNoncestr();
		$jsapi_ticket = $this->getJsApiTicket();
		$timestamp = $this->getTimestamp();
		$string = "jsapi_ticket={$jsapi_ticket}&noncestr={$noncestr}&timestamp={$timestamp}&url={$url}";
		$signature = sha1($string);
		return $signature;
	}
	
	/**
	 * curl获取数据
	 */
	private function makeCurl($url){
		//初始化curl
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//运行curl，结果以jason形式返回
		$res = curl_exec($ch);
		curl_close($ch);
		return $res;
	}

    private function curl($url,$post){
        $data_string = json_encode($post);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result,true);
    }

    public function getQrcode($id){
        $access_token = $this->getAccessToken();
       // echo $access_token,'</br>';
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
        $data = array(
            'expire_seconds' => 604800,
            'action_name' => 'QR_SCENE',
            'action_info' => array(
                'scene' => array(
                    'scene_id' => $id,
                ),
            ),
        );
        $result = $this->curl($url,$data);
       	return $result; 
        
    }
    public function getsubscribe($openid){
    	 $access_token = $this->getAccessToken();
    	 $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid";
    	 return  $this->curl($url);
    	 
    }
   

    public function uploadmedia($type='image',$imgurl){ 
     
    	 $access_token = $this->getAccessToken();
    	 $url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=$access_token&type=$type";  
     	 //$data=json_encode(array("media"=>"@$imgurl")); 
     	 $data=array("media"=>"@$imgurl");
  
    	//  $data="@$imgurl";
    	 $msg= $this->http_post($url,$data,true);   
    	 return $msg;
    	// return json_decode($msg,true);
    } 
    public function sendtempmsg($data){
    	 $access_token = $this->getAccessToken();
    	 $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token";  
     	 //$data=json_encode(array("media"=>"@$imgurl")); 
     	     	 
    	 $data=json_encode($data);
    	 $msg= $this->http_post($url,$data,false);  
    	 return json_decode($msg,true);

    }
    /**
	 * POST 请求
	 * @param string $url
	 * @param array $param
	 * @param boolean $post_file 是否文件上传
	 * @return string content
	 */
	private function http_post($url,$param,$post_file=false){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		if (is_string($param) || $post_file) {
			$strPOST = $param;
		} else {
			$aPOST = array();
			foreach($param as $key=>$val){
				$aPOST[] = $key."=".urlencode($val);
			}
			$strPOST =  join("&", $aPOST);
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_POST,true);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}
    //场景二维码
   /* public function getsceneqrcode($data){
    	 $access_token = $this->getAccessToken();
    	 $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$access_token";
    	 return  $this->curl($url,$data);
    	 
    }*/
   
 /*   function accessToken() {
    $tokenFile = "./access_token.txt";//缓存文件名
    $data = json_decode(file_get_contents($tokenFile));
    if ($data->expire_time < time() or !$data->expire_time) {
    $appid = "你的appid";
    $appsecret = "你的appsecret";
    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
      $res = getJson($url);
      $access_token = $res['access_token'];
      if($access_token) {
        $data['expire_time'] = time() + 7000;
        $data['access_token'] = $access_token;
        $fp = fopen($tokenFile, "w");
        fwrite($fp, json_encode($data));
        fclose($fp);
      }
    } else {
      $access_token = $data->access_token;
    }
     return $access_token;
  }*/

}