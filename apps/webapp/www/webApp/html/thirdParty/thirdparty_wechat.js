/**
 * APP调用微信功能
 * Created by luliang on 2015/11/5.
 */
define([
  'app',
  'utils/md5'
],function(app){

  app.factory('WeChatShare',['$q','md5Utils',function($q,md5Utils){
//js中有5种数据类型：Undefined、Null、Boolean、Number和String。
//还有一种复杂的数据类型Object，Object本质是一组无序的名值对组成的
//Undefined类型只有一个值，即undefined，使用var声明变量，但是未对初始化的，这个变量就是Undefined类型的
    var WechatShareScene = {
      SESSION:  0, // 聊天界面
        TIMELINE: 1, // 朋友圈
        FAVORITE: 2  // 收藏
    };

    var pluginIsInstalled = function(){
      return (typeof Wechat !== 'undefined');
    };

    var isWeChatInstalled = function(){
      var q = $q.defer();
      if(!pluginIsInstalled()){
        q.reject('插件未安装');
        return q.promise;
      }
      Wechat.isInstalled(function (installed) {
        if (installed) {
        // Only show wechat login button if the Wechat App was detected
          q.resolve('微信已安装');
        }else{
          q.reject('微信未安装');
        }
      }, function (reason) {});
      return q.promise;
    };
    //默认发送到朋友圈
    if(typeof Wechat !== 'undefined'){
      var params = {scene:Wechat.Scene.TIMELINE};
      console.log('默认发送到朋友圈, params: '+params);
    }
    var shareToWeChat = function (shareScene,params) {
      var q = $q.defer();
      if (typeof Wechat !== 'undefined') {
        Wechat.isInstalled(function (installed) {
          if (installed) {
            // Only show wechat login button if the Wechat App was detected
            //params.scene = shareScene ? shareScene : Wechat.Scene.TIMELINE;
            params.scene = shareScene;
            Wechat.share(params, function () {
              q.resolve("微信分享成功");
            }, function (reason) {
              q.reject(reason);
            });
          }else{
            q.reject('微信未安装');
          }
        }, function (reason) {
          q.reject('微信打开错误：'+reason);
        });
      }else{
        q.reject('微信插件未安装');
      }
      return q.promise;
    };

    /**
     * 方法说明
     * @method 方法名
     * @param {String} mTitle 内容标题
     * @param {String} mDescription 内容描述
     * @return {Object} void
     * @param mTitle
     * @param mDescription
     * @param mThumbPath
     * @returns {{scene: number}}
     */
    var buildDefaultParams = function(mTitle,mDescription,mThumbPath){
      params.message = {
        title: mTitle ? mTitle : (appConfig.appName || "大脉"),
        description: mDescription ? mDescription : (appConfig.appName || "大脉应用分享"),
        mediaTagName: "TEST-TAG-001",
        messageExt: (appConfig.appName || "大脉扩展字段"),
        messageAction: "<action>dotalist</action>",
        media: {}
      };
      if(mThumbPath != null){
        params.message.thumb = mThumbPath;
      }
      return params;
    };

    /**
     * 分享文字
     * @method shareText
     * @param {WechatShareScene} shareScene 分享方式，朋友圈，好友，收藏
     * @param {String} text 分享文字内容
     * @return {Boolean} false为分享失败
     */
    var shareText = function(shareScene,text){
      params.text = text;
      return shareToWeChat(shareScene,params);
    };

    /**
     * 分享图片
     * @param {WechatShareScene} shareScene 分享方式，朋友圈，好友，收藏
     * @return {Boolean} false为分享失败
     * @param shareScene
     * @param mTitle
     * @param mDescription
     * @param mThumbPath
     * @param imagePath
     */
    var shareImage = function(shareScene,mTitle,mDescription,mThumbPath,imagePath){
      //图片支持本地和网络图片
      params = buildDefaultParams(mTitle,mDescription,mThumbPath);
      params.message.media.image = imagePath;
      params.message.media.type = Wechat.Type.IMAGE;
      return shareToWeChat(shareScene,params);
    };

    var shareLink = function(shareScene,mTitle,mDescription,mThumbPath,mLink){
      //缩略图支持本地和网络图片
      params = buildDefaultParams(mTitle,mDescription,mThumbPath);
      params.message.media.type = Wechat.Type.LINK;
      params.message.media.webpageUrl = mLink ? mLink : baseUrl;
      return shareToWeChat(shareScene,params);
    };

    /**
     * 分享音频链接
     * @param {WechatShareScene} shareScene 分享方式，朋友圈，好友，收藏
     * @param {String} mMusicUrl 网站链接
     * @param {String} mMusicDataUrl 音频数据链接
     * @return {Boolean} false为分享失败
     * @param shareScene
     * @param mTitle
     * @param mDescription
     * @param mThumbPath
     * @param mMusicUrl
     * @param mMusicDataUrl
     */
    var shareMusic = function (shareScene,mTitle, mDescription, mThumbPath, mMusicUrl,mMusicDataUrl) {
      //message.media.musicUrl 网站链接 message.media.musicDataUrl 音频数据链接
      params = buildDefaultParams(mTitle,mDescription,mThumbPath);
      params.message.media.type = Wechat.Type.MUSIC;
      params.message.media.musicUrl = mMusicUrl;
      params.message.media.musicDataUrl = mMusicDataUrl;
      return shareToWeChat(shareScene,params);
    };

    /**
     *分享视频链接
     * @param {WechatShareScene} shareScene 分享方式，朋友圈，好友，收藏
     * @param {String} mVideoLink 视频链接
     * @return {Boolean} false为分享失败
     * @param shareScene
     * @param mTitle
     * @param mDescription
     * @param mThumbPath
     * @param mVideoLink
     */
    var shareVideo = function(shareScene, mTitle, mDescription, mThumbPath, mVideoLink){
      params = buildDefaultParams(mTitle,mDescription,mThumbPath);
      params.message.media.type = Wechat.Type.VIDEO;
      params.message.media.videoUrl = mVideoLink;
      return shareToWeChat(shareScene,params);
    };

    var shareApp = function(shareScene, mTitle, mDescription, mThumbPath, mAPPLink){
      //不太懂？
      params = buildDefaultParams(mTitle,mDescription,mThumbPath);
      params.message.media.type = Wechat.Type.APP;
      params.message.media.videoUrl = mVideoLink;
      params.message.media.extInfo = "<xml>extend info</xml>";
      params.message.media.url = mAPPLink;
      return shareToWeChat(shareScene,params);
    };

    var shareGif = function(shareScene, mTitle, mDescription, mGifLocalPath){
      //message.media.emotion 本地 jpg图片或者gif图
      params = buildDefaultParams(mTitle,mDescription,null);
      params.message.media.type = Wechat.Type.EMOTION;
      params.message.media.emotion = mGifLocalPath;
      return shareToWeChat(shareScene,params);
    };

    var shareFile = function(shareScene,mTitle, mDescription, mThumbPath,mFileLocalPath){
      //message.media.file 本地文件
      params = buildDefaultParams(mTitle,mDescription,null);
      params.message.media.type = Wechat.Type.FILE;
      params.message.media.file = mFileLocalPath;
      return shareToWeChat(shareScene,params);
    };

    function loginToWechat(){
      var q = $q.defer();
      try {
        if (angular.isUndefined(Wechat)) {
          q.reject('该apk未装入微信插件');
          return q.promise;
        }
        Wechat.auth("snsapi_userinfo", function (response) {
          // you may use response.code to get the access token.
          //{"code":'token',"state":'',"country":"CN","lang":"zh_CN"}
          //console.info(window.JSON.stringify(response));
          //返回token
          q.resolve(response.code);
        }, function (reason) {
          q.reject(reason);
        });
      } catch (e) {
        console.error('loginToWechat：'+ e.name+':'+ e.message);
        q.reject(e.message);
      }
      return q.promise;
    }

    /**
     * 微信客户端支付
     *
     * 公众账号ID	appid	String(32)	是	wx8888888888888888	微信分配的公众账号ID
     * 商户号	partnerid	String(32)	是	1900000109	微信支付分配的商户号
     * 预支付交易会话ID	prepayid	String(32)	是	WX1217752501201407033233368018	微信返回的支付交易会话ID
     * 扩展字段	package	String(128)	是	Sign=WXPay	暂填写固定值Sign=WXPay
     * 随机字符串	noncestr	String(32)	是	5K8264ILTKCH16CQ2502SI8ZNMTM67VS	随机字符串，不长于32位。推荐随机数生成算法
     * 时间戳	timestamp	String(10)	是	1412000000	时间戳，请见接口规则-参数规定
     * 签名	sign	String(32)	是	C380BEC2BFD727A4B6845133519F3AD6	签名，详见签名生成算法
     *
     * @param {String} partnerid 商户号	partnerid	String(32)	是	1900000109	微信支付分配的商户号
     * @param {String} prepayid 预支付交易会话ID	prepayid	String(32)	微信返回的支付交易会话ID
     * @param {String} noncestr 随机字符串	noncestr	String(32) 随机字符串，不长于32位。推荐随机数生成算法
     * @param timestamp 时间戳	String(10)	时间戳，请见接口规则-参数规定
     * @param sign 签名 String(32)		签名，详见签名生成算法
     * @param onSuccess
     * @param onFailed
     */
    function weChatPayByApp(partnerid,prepayid,noncestr,timestamp,sign,onSuccess,onFailed){
      var params = {
        partnerid: partnerid, // merchant id
        prepayid: prepayid, // prepay id
        noncestr: noncestr, // nonce
        timestamp: timestamp, // timestamp
        sign: sign, // signed string
      };
      Wechat.sendPaymentRequest(params, onSuccess, onFailed);
      //var params = {
      //  partnerid: '10000100', // merchant id
      //  prepayid: 'wx201411101639507cbf6ffd8b0779950874', // prepay id
      //  noncestr: '1add1a30ac87aa2db72f57a2375d8fec', // nonce
      //  timestamp: '1439531364', // timestamp
      //  sign: '0CB01533B8C1EF103065174F50BCA001', // signed string
      //};
      //Wechat.sendPaymentRequest(params, function () {
      //  alert("Success");
      //}, function (reason) {
      //  alert("Failed: " + reason);
      //});
    }

    return{
      WechatShareScene : WechatShareScene,
      checkPluginIsInstalled : pluginIsInstalled,
      isWeChatInstalled: isWeChatInstalled,
      shareText : shareText,
      shareImage : shareImage,
      shareLink : shareLink,
      //以下暂不支持
      //shareMusic : shareMusic,
      //shareVideo : shareVideo,
      //shareGif : shareGif,
      //shareApp : shareApp,
      //shareFile : shareFile,
      loginToWechat : loginToWechat,
      weChatPayByApp : weChatPayByApp
    }
}]);

});
