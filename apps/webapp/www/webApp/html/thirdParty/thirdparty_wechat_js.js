/**
 * 微信浏览器调用微信功能
 * Created by luliang on 2016/1/15.
 *
 * 在线接入文档：https://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html#.E6.AD.A5.E9.AA.A4.E4.BA.8C.EF.BC.9A.E5.BC.95.E5.85.A5JS.E6.96.87.E4.BB.B6
 *
 */
define([
  'app',
  'weChatJs',
  'models/model_app',
  'utils/toastUtil'
],function(app,wx){
  app.factory('weChatJs',['$q','$window','AppModel','ToastUtils',function($q,$window,AppModel,ToastUtils){

    function isWxEnable(){
      var ua = navigator.userAgent.toLowerCase();
      return ua.match(/MicroMessenger/i) == "micromessenger";
    }

    function wxConfigInit(success,failed){
      var onSuccess = angular.isFunction(success) ? success : angular.noop;
      var onFailed = angular.isFunction(failed) ? failed : angular.noop;
      var currentUrl=location.href.split('#')[0];

      AppModel.getWechatConfig(currentUrl,onWechatSuccess,onWechatFail);




      function onWechatSuccess(response){
        var code = response.data.code;
        var data=response.data.data;
        if(code==0){
          var webAppId = data.appId;
          var timestamp = data.timestamp;
          var nonceStr = data.nonceStr;
          var signature = data.signature;

          wx.config({
            debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: webAppId, // 必填，公众号的唯一标识
            //url:currentUrl,
            timestamp: timestamp, // 必填，生成签名的时间戳
            nonceStr: nonceStr, // 必填，生成签名的随机串
            signature: signature,// 必填，签名，见附录1
            jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
          });
          wx.ready(function () {
            onSuccess();
          });

          wx.error(function (res) {
//          alert("config err...:"+res.errMsg);
          });

        }else{
          ToastUtils.showError('初始化失败：'+data.msg);
        }

      }

      function onWechatFail(response,data){
        ToastUtils.showError('网络异常：'+'状态码：'+response.statusText);
        onFailed();
      }


    }


    /**
     * 微信分享到朋友圈
     */
    function wxShareToTimeline(title,link,imgUrl,onSuccess,onCancel){
      wx.onMenuShareTimeline({
        title: title, // 分享标题
        link: link, // 分享链接
        imgUrl: imgUrl, // 分享图标
        success: function () {
          // 用户确认分享后执行的回调函数
          onSuccess();
        },
        cancel: function () {
          // 用户取消分享后执行的回调函数
          onCancel();
        }
      });
    }

    /**
     * 分享给朋友
     */
    function wxShareToAppMessage(title,desc,link,imgUrl,onSuccess,onCancel){
      wx.onMenuShareAppMessage({
        title: title, // 分享标题
        desc: desc, // 分享描述
        link: link, // 分享链接
        imgUrl: imgUrl, // 分享图标
        type: 'link', // 分享类型,music、video或link，不填默认为link
        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
        success: function () {
          // 用户确认分享后执行的回调函数
          onSuccess();
        },
        cancel: function () {
          // 用户取消分享后执行的回调函数
          onCancel();
        }
      });
    }


    /**
     * 分享到QQ
     */
    function wxShareToQQ(title,desc,link,imgUrl,onSuccess,onCancel){
      wx.onMenuShareQQ({
        title: title, // 分享标题
        desc: desc, // 分享描述
        link: link, // 分享链接
        imgUrl: imgUrl, // 分享图标
        success: function () {
          // 用户确认分享后执行的回调函数
          onSuccess();
        },
        cancel: function () {
          // 用户取消分享后执行的回调函数
          onCancel();
        }
      });
    }


    /**
     * 分享到腾讯微博
     */
    function wxShareToWeibo(title,desc,link,imgUrl,onSuccess,onCancel){
      wx.onMenuShareWeibo({
        title: title, // 分享标题
        desc: desc, // 分享描述
        link: link, // 分享链接
        imgUrl: imgUrl, // 分享图标
        success: function () {
          // 用户确认分享后执行的回调函数
          onSuccess();
        },
        cancel: function () {
          // 用户取消分享后执行的回调函数
          onCancel();
        }
      });
    }

    /**
     * 分享到QQ空间
     */
    function wxShareToQZone(title,desc,link,imgUrl,onSuccess,onCancel){
      wx.onMenuShareQZone({
        title: title, // 分享标题
        desc: desc, // 分享描述
        link: link, // 分享链接
        imgUrl: imgUrl, // 分享图标
        success: function () {
          // 用户确认分享后执行的回调函数
          onSuccess();
        },
        cancel: function () {
          // 用户取消分享后执行的回调函数
          onCancel();
        }
      });
    }

    //检查API
    function wxCheckIsSupport(){

      wx.checkJsApi({
        jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage'], // 需要检测的JS接口列表，所有JS接口列表见附录2,
        success: function(checkResult) {
          // 以键值对的形式返回，可用的api值true，不可用为false
          // 如：{"checkResult":{"chooseImage":true},"errMsg":"checkJsApi:ok"}
          return checkResult.chooseImage;
        }
      });


    }

    function wxPayByWebSpec(url){
      $window.location.href = url;
    }


    function weChatPayByWxWeb(appId,nonceStr,prepay_id,paySign,onSuccess,onFailed,onCancel){
      //wx.chooseWXPay({
      //  timestamp: 1414723227,
      //  nonceStr: 'noncestr',
      //  package: 'addition=action_id%3dgaby1234%26limit_pay%3d&bank_type=WX&body=innertest&fee_type=1&input_charset=GBK&notify_url=http%3A%2F%2F120.204.206.246%2Fcgi-bin%2Fmmsupport-bin%2Fnotifypay&out_trade_no=1414723227818375338&partner=1900000109&spbill_create_ip=127.0.0.1&total_fee=1&sign=432B647FE95C7BF73BCD177CEECBEF8D',
      //  signType: 'SHA1', // 注意：新版支付接口使用 MD5 加密
      //  paySign: 'bd5b1933cda6e9548862944836a9b52e8c9a2b69'
      //});
      //
      //wx.chooseWXPay({
      //  timestamp: 0, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
      //  nonceStr: '', // 支付签名随机串，不长于 32 位
      //  package: '', // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=***）
      //  signType: '', // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
      //  paySign: '', // 支付签名
      //  success: function (res) {
      //    // 支付成功后的回调函数
      //  }
      //});




      function emptyFunction(){

      }

      var _onSuccess = (typeof onSuccess == "function") ? onSuccess : emptyFunction;
      var _onFailed = (typeof onFailed == "function") ? onFailed : emptyFunction;
      var _onCancel = (typeof onCancel == "function") ? onCancel : emptyFunction;

      function onBridgeReady(){
        WeixinJSBridge.invoke(
          'getBrandWCPayRequest', {
            "appId" : appId,     //公众号名称，由商户传入
            "timeStamp":""+new Date().getTime(),         //时间戳，自1970年以来的秒数
            "nonceStr" : nonceStr, //随机串
            "package" : "prepay_id="+prepay_id,
            "signType" : "MD5",         //微信签名方式：
            "paySign" : paySign //微信签名
            //
          },
          function(res){
            if(res.err_msg == "get_brand_wcpay_request：ok" ) {// 使用以上方式判断前端返回,微信团队郑重提示：res.err_msg将在用户支付成功后返回    ok，但并不保证它绝对可靠。
              _onSuccess();
            }else if(res.err_msg == "get_brand_wcpay_request：cancel"){
              _onFailed();
            }else if(res.err_msg == "get_brand_wcpay_request：fail"){
              _onCancel();
            }else{
              ssjjLog.log('waiting wechat pay result……');
            }
          }
        );
        //    WeixinJSBridge.invoke(
        //      'getBrandWCPayRequest', {
        //        "appId" : "wx2421b1c4370ec43b",     //公众号名称，由商户传入
        //      "timeStamp":" 1395712654",         //时间戳，自1970年以来的秒数
        //      "nonceStr" : "e61463f8efa94090b1f366cccfbbb444", //随机串
        //      "package" : "prepay_id=u802345jgfjsdfgsdg888",
        //      "signType" : "MD5",         //微信签名方式：
        //      "paySign" : "70EA570631E4BB79628FBCA90534C63FF7FADD89" //微信签名
        //  },
        //  function(res){
        //    if(res.err_msg == "get_brand_wcpay_request：ok" ) {}     // 使用以上方式判断前端返回,微信团队郑重提示：res.err_msg将在用户支付成功后返回    ok，但并不保证它绝对可靠。
        //  }
        //);
      }
      if (typeof WeixinJSBridge == "undefined"){
        if( document.addEventListener ){
          document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
        }else if (document.attachEvent){
          document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
          document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
        }
      }else{
        onBridgeReady();
      }
    }

    return{
      isWxEnable : isWxEnable,
      wxConfigInit:wxConfigInit,
      wxCheckIsSupport:wxCheckIsSupport,
      wxPayByWebSpec:wxPayByWebSpec,
      weChatPayByWxWeb : weChatPayByWxWeb ,
      wxShareToTimeline : wxShareToTimeline,
      wxShareToAppMessage : wxShareToAppMessage,
      wxShareToQQ : wxShareToQQ,
      wxShareToWeibo : wxShareToWeibo,
      wxShareToQZone : wxShareToQZone
    }
  }])
});
