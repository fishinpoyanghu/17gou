
define(
  [
    'app',
    'html/common/geturl_service',
    'utils/toastUtil',
    'html/common/service_check_version',
    'html/common/global_service',
    'html/common/local_database',
    'html/thirdParty/thirdparty_wechat_js',
    'models/model_app',
    'html/common/storage',
  ]
  ,function(app){
    app
      .run(['$rootScope','$ionicPlatform','$location','$state','$ionicHistory','MyUrl','ToastUtils','checkVersionService','Global','localDatabase','weChatJs','AppModel','Storage','$cordovaInAppBrowser',
        function($rootScope,$ionicPlatform,$location,$state,$ionicHistory,MyUrl,ToastUtils,checkVersionService,Global,localDatabase,weChatJs,AppModel,Storage,$cordovaInAppBrowser) {

          $ionicPlatform.ready(function() {
            // Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
            // for form inputs)
            if (window.cordova && window.cordova.plugins && window.cordova.plugins.Keyboard) {
              cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
              cordova.plugins.Keyboard.disableScroll(true);

            }
            if (window.StatusBar) {
              // org.apache.cordova.statusbar required
              StatusBar.styleLightContent();
            }
          });

          //双击退出
          $ionicPlatform.registerBackButtonAction(function (e) {
            //判断处于哪个页面时双击退出
            //console.log('页面路径'+$location.path());
            var backButtonAction = {
            pay:'doubleBack',
            payResult:'threeBack',
            "tab.mainpage":'doubleExit',
            "tab.publish":'doubleExit',
            "tab.trolley":'doubleExit',
            "tab.account":'doubleExit',
            "tab.discovery":'doubleExit'
          }
            var pageState = $state.current.name;
            var path = $location.path();
            if (backButtonAction[pageState] == 'doubleExit') {
              if ($rootScope.backButtonPressedOnceToExit) {
                ionic.Platform.exitApp();
              } else {
                $rootScope.backButtonPressedOnceToExit = true;
                //console.info('再按一次将退出应用');
                ToastUtils.showTips('再按一次将退出应用',2000);
                setTimeout(function () {
                  $rootScope.backButtonPressedOnceToExit = false;
                }, 2000);
              }
            } else if(backButtonAction[pageState] == 'doubleBack') {
              $ionicHistory.goBack(-2);
            }else if(backButtonAction[pageState] == 'threeBack') {
              $ionicHistory.goBack(-3);
            }
            else if ($ionicHistory.backView()) {
              //ToastUtils.showNormal('返回的view'+$ionicHistory.backView());
              $ionicHistory.goBack();
            } else {
            }
            e.preventDefault();
            return false;
          }, 101);
          if(Global.isInweixinBrowser()) {
            weChatJs.wxCheckIsSupport();
            weChatJs.wxConfigInit(function(){},function(){});
          }

          if(ionic.Platform.isWebView() && (ionic.Platform.isIOS() || ionic.Platform.isIPad())){
            AppModel.getHuixiao(function(xhr,re) {
              if(re.code == 0) {
                var data = re.data;
                Storage.set('huixiao',data.huixiao)
              }
            })
          }

          //处理首页banner a标签外链跳转
            function a_click(e) {
                var target = e.target.parentNode;
                if (target && target.tagName == 'A' && target.href.indexOf('http') > -1) {
                    e.preventDefault();
                    var options = {
                          location: 'no',
                          clearcache: 'no',
                          toolbar: 'yes'
                        };
                    $cordovaInAppBrowser.open(target.href,'_blank',options)
                }
            }
            if(ionic.Platform.isWebView()) {
              angular.element(document).on('click', a_click);
            }
            

          AppModel.getShare(function(xhr,re) {
            if(re.code == 0) {
              var data = re.data;
              appConfig.shareTitle = appConfig.inWeixinShare.shareTitle = data.title;
              appConfig.shareContent = appConfig.inWeixinShare.shareContent = data.sub_title;
            }
          })
          var ionicNavNode = document.querySelector('#ionicNavNode');
          var hasDealNavBar = false;
          $rootScope.$on('$ionicView.enter',function(event,toState, toParams, fromState, fromParams){
            var name = toState.stateName;
            var defaultShare = {
              boostrap:true,
              inviteFriends:true,
              "activity-goodsDetail":true
            }
            if(Global.isInweixinBrowser() && !defaultShare[name]) {
              weChatJs.wxShareToTimeline(appConfig.inWeixinShare.shareTitle, appConfig.inWeixinShare.shareLink, appConfig.inWeixinShare.shareImgUrls, function() {
              }, function(){});
              weChatJs.wxShareToAppMessage(appConfig.inWeixinShare.shareTitle, appConfig.inWeixinShare.shareContent, appConfig.inWeixinShare.shareLink, appConfig.inWeixinShare.shareImgUrls, function() {
              }, function(){});
            }
            var showBar = {
              "activity-goodsDetail":true,
              redPacketList:true,
              grabRedPacket:true,
              search:true,
              turntable:true,
              loginTransferPage:true,
              editShareOrder:true
            }
            if(Global.isInweixinBrowser() && !hasDealNavBar&&showBar[name]) {
              ionicNavNode.classList.remove('hide-nav-bar')
              hasDealNavBar = true;
            }
            
          })
          $rootScope.$on('$stateChangeStart',function(event,toState, toParams, fromState, fromParams){
            var name = toState.name;
            if(name == 'login' && fromState.name != 'registerFirst') {
              Storage.set('fromState',fromState.name)
              Storage.set('fromParams',fromParams)
            }
            if((name == 'tab.account') || (name == 'payTransfer') || (name == 'turntable') || name == 'chongzhi'){
              try {
                if (!MyUrl.isLogin()) {
                  event.preventDefault();
                  $state.go('login',{'state':STATUS.LOGIN_ABNORMAL});
                  // ToastUtils.showNow('请先登录！！','weui_icon_warn',800);
                } else {
                }
              } catch (e) {
                console.error('登录判断跳转出错'+ e.name+'：'+ e.message);
              }
            }
          });
          
          if(ionic.Platform.isWebView()) {
            dmwechat.init();
          }

          if(ionic.Platform.isWebView() && (ionic.Platform.isIOS() || ionic.Platform.isIPad())){
            $rootScope.isIosApp = true;
          }

          //初始化应用删除旧纪录
          (function clearCache(){
            localDatabase.clearUnlessCache();
          })();

          //获取APP手机设备信息
          (function getDevice(){
            document.addEventListener("deviceready", function(){
              var did = device.did ;
              var os = device.platform ;
              var nm = device.nm ;
              var mno = device.mno ;
              var dm = device.dm ;
              MyUrl.setDeviceInfo(did, os, nm, mno, dm);
            });
          })();

          $rootScope.isHideNaviBar = Global.isInweixinBrowser();
          checkVersionService.checkVersion(false);//检查更新
          ionicNavNode.classList.remove('dis-n');
          document.querySelector('#loadingToast').classList.add('dis-n')
          if(Global.isInweixinBrowser()) {
            ionicNavNode.classList.add('hide-nav-bar')
          }
        }]);
});
