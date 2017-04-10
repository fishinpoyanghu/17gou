/**
 * 整合微信浏览器和微信APP调用
 * Created by luliang on 2016/1/26.
 */

define([
  'app',
  'html/thirdParty/thirdparty_wechat_js',
  'html/thirdParty/thirdparty_wechat'
],function(app){
  app
    .factory('OneWeChat',['$window','weChatJs','WeChatShare',function($window,weChatJs,WeChatShare){
      var _isWxEnable = false;
      var envWx = -1;//0微信浏览器 1微信APP
      var WeChatShareScene = {
        SESSION:  0, // 朋友
        TIMELINE: 1, // 朋友圈
        FAVORITE: 2  // 收藏 微信APP有
      };
      function initWx(_env_wx,onSuccess,onFailed){
        try {
          switch (_env_wx) {
            case 0:
              weChatJs.wxConfigInit(onSuccess,onFailed);
              break;
            case 1:
              onSuccess();
              break;
          }
        } catch (e) {
          console.error('微信初始化失败：'+(e.message || e));
          onFailed(e);
        }
      }

      function initial(onSuccess,onFailed){
        isWxEnable(function(){
          initWx(envWx,onSuccess,onFailed);
        },onFailed);
      }

      function isWxEnable(onSuccess,onFailed){
        try {
          if (weChatJs.isWxEnable()) {
            _isWxEnable = true;
            envWx = 0;
            onSuccess('在微信浏览器环境下');
          } else {
            var weChatInstalledPromise = WeChatShare.isWeChatInstalled();
            weChatInstalledPromise.then(function(){
              _isWxEnable = true;
              envWx = 1;
              onSuccess();
            }, function(){
              _isWxEnable = false;
              onFailed();
            });
          }
        } catch (e) {
          _isWxEnable = false;
          envWx = -1;
        }
      }

      function shareWxLink(shareScene,mTitle,mDescription,mLink,mThumb,onSucess,onFailed){
        if(!_isWxEnable){
          onFailed('分享失败，请稍候再使用……');
        }else{
          switch (envWx){
            case 0:
              if(WeChatShareScene.SESSION == shareScene){
                weChatJs.wxShareToAppMessage(mTitle,mDescription,mLink,mThumb,onSucess,onFailed);
              }else{
                weChatJs.wxShareToTimeline(mTitle,mLink,mThumb,onSucess,onFailed);
              }
              break;
            case 1:
              var sharePromise = WeChatShare.shareLink(shareScene, mTitle, mDescription, mThumb, mLink);
              sharePromise.then(onSucess,onFailed);
              break;
          }
        }
      }

      function pay(params,onSucess,onFailed){
        if(!_isWxEnable){
          onFailed('支付失败，请稍候再使用……');
        }else{
          switch (envWx){
            case 0:
              var url = params.url;
              weChatJs.wxPayByWebSpec(url);
              break;
            case 1:
              WeChatShare.weChatPayByApp(params.partnerid,params.prepayid,params.noncestr,params.timestamp,params.sign,onSucess,onFailed);
              break;
          }
        }
      }


      return {
        WeChatShareScene : WeChatShareScene,
        envWx : envWx,
        init : initial ,
        isWxEnable : isWxEnable,
        /**
         * 分享链接
         * @param shareScene 分享给 whom
         * @param mTitle 标题
         * @param mDescription 描述
         * @param mLink 链接
         * @param mThumb 缩略图
         * @param onSucess
         * @param onFailed
         */
        shareWxLink : shareWxLink,
        /**
         * 微信支付
         * @param params web:{url:'xxxxxx'} app:{partnerid: 'xx',prepayid: 'xx',noncestr: 'xx',timestamp: 'xx', sign: 'xx',
         * @param onSucess
         * @param onFailed
         */
        pay : pay
      }
    }]);
});
