<?php
/**
 *
 *
 * 微信Oauth登录
 *
 *
 */

require_once('wx.Exception.php');
require_once('wx.Tool.php');
require_once('wx.Config.php');
require_once('wxLog.class.php');

class wxOauth{

    private static $APPID;
    private static $APPSECRET;

    private static $instance = null;

    const API_BASE_URL_PREFIX = 'https://api.weixin.qq.com'; //以下API接口URL需要使用此前缀
    const OAUTH_PREFIX = 'https://open.weixin.qq.com/connect/oauth2';

    const OAUTH_TOKEN_URL = '/sns/oauth2/access_token?';
    const OAUTH_USERINFO_URL = '/sns/userinfo?';
    const OAUTH_AUTH_URL = '/sns/auth?';
    const OAUTH_AUTHORIZE_URL = '/authorize?';

    private  function __construct(){
    }

    private  function __clone(){

    }

    public static function GetInstance($appid='',$appsecret=''){
        if(!$appid||!$appsecret){
            self::$APPID = wxConfig::$APPID;
            self::$APPSECRET = wxConfig::$APPSECRET;
        }

        if(!self::$instance instanceof self)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * oauth 授权跳转接口
     * @param string $callback 回调URI
     * @return string
     */
    public function getOauthRedirect($callback,$state='',$scope='snsapi_userinfo'){
        return self::OAUTH_PREFIX.self::OAUTH_AUTHORIZE_URL.'appid='.self::$APPID.'&redirect_uri='.urlencode($callback).'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
    }

    /**
     * 通过code获取Access Token
     * @return array {access_token,expires_in,refresh_token,openid,scope}
     */
    public function getOauthAccessToken($code=''){
       // $code = isset($_GET['code'])?$_GET['code']:'';
        if (!$code) return false;
        $result = wxTool::http_get(self::API_BASE_URL_PREFIX.self::OAUTH_TOKEN_URL.'appid='.self::$APPID.'&secret='.self::$APPSECRET.'&code='.$code.'&grant_type=authorization_code');
        if ($result)
        {
            $json = json_decode($result,true);


            $logHandler= new CLogFileHandler("../../../logs/wxOauth.log");
            $log = WxLog::Init($logHandler, 15);
            $log->DEBUG($result);


            if (!$json || !empty($json['errcode'])) {
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 刷新access token并续期
     * @param string $refresh_token
     * @return boolean|mixed
     */
    public function getOauthRefreshToken($refresh_token){
        $result = wxTool::http_get(self::API_BASE_URL_PREFIX.self::OAUTH_REFRESH_URL.'appid='.$this->appid.'&grant_type=refresh_token&refresh_token='.$refresh_token);
        if ($result)
        {



            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取授权后的用户资料
     * @param string $access_token
     * @param string $openid
     * @return array {openid,nickname,sex,province,city,country,headimgurl,privilege,[unionid]}
     * 注意：unionid字段 只有在用户将公众号绑定到微信开放平台账号后，才会出现。建议调用前用isset()检测一下
     */
    public function getOauthUserinfo($access_token,$openid){
        $result = wxTool::http_get(self::API_BASE_URL_PREFIX.self::OAUTH_USERINFO_URL.'access_token='.$access_token.'&openid='.$openid);
        if ($result)
        {

            $logHandler= new CLogFileHandler("../../../logs/wxOauth.log");
            $log = WxLog::Init($logHandler, 15);
            $log->DEBUG($result);

            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 检验授权凭证是否有效 如果希望严谨，在调用getOauthUserinfo 前需要调用getOauthAuth去校验access_token openid
     * @param string $access_token
     * @param string $openid
     * @return boolean 是否有效
     */
    public function getOauthAuth($access_token,$openid){
        $result = wxTool::http_get(self::API_BASE_URL_PREFIX.self::OAUTH_AUTH_URL.'access_token='.$access_token.'&openid='.$openid);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                return false;
            } else
                if ($json['errcode']==0) return true;
        }
        return false;
    }






}