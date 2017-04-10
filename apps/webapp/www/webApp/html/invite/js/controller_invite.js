/**
 * Created by Administrator on 2015/12/29.
 */
define([
  'app',
  'models/model_invite',
  'html/common/constants',
  'utils/toastUtil',
  'html/thirdParty/thirdparty_wechat',
  'html/thirdParty/thirdparty_wechat_js',
  'utils/clipboard',
  'utils/clipboard.min',
  'html/common/global_service',
  'models/model_app'
],function(app){

  app.controller(
    'InviteCtrl',['$scope','$ionicHistory','$ionicActionSheet','$state','inviteModel','ToastUtils','WeChatShare','weChatJs','Global','AppModel',
      function($scope,$ionicHistory,$ionicActionSheet,$state,inviteModel,ToastUtils,WeChatShare,weChatJs,Global,AppModel){
        weChatJs.wxCheckIsSupport();
        weChatJs.wxConfigInit(onInitSuccess,onInitFailed);
        $scope.isShowShare=false;
        $scope.mLink = baseUrl;
        $scope.rebateInfo = {
          title:'无',
          content:'无',
          rebate_money : '',
          invite_code : '',
          qrcode : ''
        };
        setTimeout(getRebateInfo,500);

        //微信分享
        var mTitle = '一元就能买iphone，还送10元亿七购红包，叫我雷锋不谢';
        var mImgUrls = '';
        var mLink = 'http://testh6.4399houtai.com/#/boostrap/';
        var mContent='我和你都能拿到红包哟';

        $scope.startToRebateList = function(){
          $state.go('rebateList');
        };


        /**q
         *
         * 获取邀请返利信息
         */
        function getRebateInfo(){
          inviteModel.getRebateInfo(function(response){
            //onSuccess
            var code = response.data.code ;
            var msg = response.data.msg ;
            switch (code){
              case 0 :
                var data = response.data.data ;
                $scope.rebateInfo.rebate_money = data.rebate_money ;
                $scope.rebateInfo.invite_code = data.invite_code ;
                $scope.rebateInfo.qrcode = data.qrcode ;
                 mLink = 'http://testh6.4399houtai.com/#/boostrap/'+$scope.rebateInfo.invite_code;
                $scope.mLink=mLink;
                ssjjLog.log('mlink'+mLink);
                break;
              default :
                ToastUtils.showError(msg);
                break;

            }
          },function(response){
            //onFail
            ToastUtils.showError('请检查网络状态，状态码：' + response.status);
          });
        }



        $scope.shareToWechat=function(){



          if(Global.isInweixinBrowser()){
            showGuide();
            weChatJs.wxShareToTimeline(mTitle,mLink,mImgUrls,onShareSuccess,onShareCancel);
            weChatJs.wxShareToAppMessage(mTitle,mContent,mLink,mImgUrls,onSendSuccess,onSendCancel);
            //ToastUtils.showShortNow(STATE_STYLE.GOOD, '请打开右上角分享');
            //显示引导分享界面
          }
          else if(Global.isInAPP()){
            //app或非微信浏览器弹出分享框
            WeChatShare.isWeChatInstalled().then(function(){
              $scope.isShowShare=true;
            },function(){

              ToastUtils.showShortNow(STATE_STYLE.WARNING, "微信未安装");
              return;

            });

          }
          else{
            //uc等浏览器
            clipSuccess();
          }

        };
        //app分享到朋友圈
        $scope.shareToFriendsCircle=function(){
          var shareScene = WeChatShare.WechatShareScene.TIMELINE;
          if (Global.isInAPP()) {
            //app分享

            var  wechatRequest = WeChatShare.shareLink(shareScene, mTitle, mContent, mImgUrls, mLink);
            wechatRequest.then(function () {
                ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
              },
              function (error) {
                ToastUtils.showShortNow(STATE_STYLE.ERROR, error);
              }
            );

          }

        };

        //app分享给好友
        $scope.shareToFriends=function(){
          var shareScene = WeChatShare.WechatShareScene.SESSION;
          if (Global.isInAPP()) {
            var  wechatRequest=WeChatShare.shareLink(shareScene, mTitle, mContent, mImgUrls, mLink);
            wechatRequest.then(function () {
                ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
              },
              function (error) {
                ToastUtils.showShortNow(STATE_STYLE.ERROR, error);
              }
            );

          }

        };

        //复制链接
        $scope.copyLink=function(){

          if (Global.isInAPP()) {

            cordova.plugins.clipboard.copy(mLink);
            ToastUtils.showShortNow(STATE_STYLE.GOOD, '复制成功：'+mLink);

          }


        };

        $scope.hidePop=function(){

          $scope.isShowShare=false;

        };

        function showGuide(){
          document.getElementById("guidepop").style.display = 'block';

        }
        function hideGuide(){
          document.getElementById("guidepop").style.display = 'none';

        }

        function clipSuccess(){

          var clipboard=new Clipboard('.dp-button--yellow');
          clipboard.on('success', function(e) {
            ssjjLog.info('Action:', e.action);
            ssjjLog.info('Text:', e.text);
            ssjjLog.info('Trigger:', e.trigger);
            e.clearSelection();
            ToastUtils.showShortNow(STATE_STYLE.GOOD, '已复制，请粘贴到微信浏览器中打开：');
          });


        }
        function onShareSuccess(){
          hideGuide();
          ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
        }
        function onShareCancel(){
          hideGuide();

          ToastUtils.showShortNow(STATE_STYLE.ERROR,'用户已取消分享');

        }

        function onSendSuccess(){
          hideGuide();

          ToastUtils.showShortNow(STATE_STYLE.GOOD, '发送给好友成功');
        }
        function onSendCancel(){
          hideGuide();

          ToastUtils.showShortNow(STATE_STYLE.ERROR,'用户已取消发送');

        }
        function onInitSuccess(){

          ToastUtils.showShortNow(STATE_STYLE.GOOD, 'wxconfig初始化成功');

        }
        function onInitFailed(){

          ToastUtils.showShortNow(STATE_STYLE.ERROR,'wxconfig初始化失败');

        }


      }])

});
