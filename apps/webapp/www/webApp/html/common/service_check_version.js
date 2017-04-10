/**
 * 收货地址相关接口
 *
 */
define([
  'app',
  'utils/toastUtil',
  'models/model_version',
  './geturl_service'
],function(app){

  app.factory('checkVersionService',
    ['versionModel','$ionicPopup','$q','$timeout','$ionicLoading','ToastUtils','MyUrl','$ionicPlatform',
    function(versionModel,$ionicPopup,$q,$timeout,$ionicLoading,ToastUtils,MyUrl,$ionicPlatform){


      var newApk = {
        version : '',
        packageSize : '',
        desc : '',
        url : ''
      };

      var isDeviceReady = false ;

      /**
       * 检查版本接口
       */
      function checkVersion(_isShowToast){

        if(isDeviceReady){
          checkVersion2(_isShowToast);
        }else{
          document.addEventListener("deviceready", function(){
            isDeviceReady = true ;
            checkVersion2(_isShowToast);
          });
        }

      }

      function checkVersion2(_isShowToast){
          if(navigator.userAgent.indexOf('Android') > -1){//安卓客戶端app
            versionModel.checkVersion(function(response){
              //onSuccess
              var code = response.data.code ;
              var msg = response.data.msg ;
              var data = response.data.data ;
              switch (code){
                case 0 ://已经是最新版本
                  if(_isShowToast){
                    ToastUtils.showSuccess(msg);
                  }
                  break ;
                case 201 ://有新版本可以更新
                  newApk.version = data.v ;
                  newApk.packageSize = data.size ;
                  newApk.desc = data.desc ;
                  newApk.url = data.url ;
                  showNewVersionDialog(false);
                  break ;
                case 202 ://需要强制更新
                  newApk.version = data.v ;
                  newApk.packageSize = data.size ;
                  newApk.desc = data.desc ;
                  newApk.url = data.url ;
                  showNewVersionDialog(true);
                  break ;
                default :
                  ToastUtils.showError(msg);
                  break;
              }
            },function(response){
              //onFail
              ToastUtils.showError('请检查网络状态！')
            });
          }
      }

      /**
       * 显示更新内容对话框
       * @param isForceUpdate
       */
      function showNewVersionDialog(isForceUpdate){
        var buttons = [] ;
        if(isForceUpdate){
          buttons = [
            {
              text: '取消',
              type: 'button button-default',
              onTap: function(e) {
               //退出apk
                unRegisterBackButtonAction();
                ionic.Platform.exitApp();
              }
            },
            {
              text: '立即更新',
              type: 'button button-assertive',
              onTap: function(e) {
                unRegisterBackButtonAction();
                updatePackage();
              }
            }
          ] ;
        }else{
          buttons = [
            {
              text: '以后再说',
              type: 'button button-default',
              onTap: function(e) {
                unRegisterBackButtonAction();
              }
            },
            {
              text: '立即更新',
              type: 'button button-assertive',
              onTap: function(e) {
                unRegisterBackButtonAction();
                updatePackage();
              }
            }
          ] ;
        }
        $ionicPopup.show({
          title: '发现新版本',
          template: '最新版本：'+ newApk.version+'<br/>'
          +'新版本大小：'+  newApk.packageSize+'<br/> <br/>'
          +'更新内容： <br/>'
          +  newApk.desc +'<br/>',
          buttons: buttons
        });

        var unRegisterBackButtonAction = $ionicPlatform.registerBackButtonAction(function(e){
          e.preventDefault();
        },401);
      }


      /**
       * 更新apk包
       */
      function updatePackage(){
        var fileNmae = "ninecent_" + newApk.version + ".apk";
        var saveFileURL = cordova.file.externalRootDirectory + fileNmae ;
        cordova.plugins.fileOpener2.open(
          saveFileURL,
          'application/vnd.android.package-archive',
          {
            error : function(e) {
              console.log('Error status: ' + e.status + ' - Error message: ' + e.message);
              downloadNewVersionApk();
            },
            success : function () {
              console.log('file opened successfully');//安装成功
            }
          }
        );
      }

      /**
       * 下载新版本安装包
       */
      function downloadNewVersionApk(){
        $ionicLoading.show({
          template: "已经下载：0%"
        });
        var fileNmae = "ninecent_" + newApk.version + ".apk";
        var downloadURL = encodeURI(newApk.url);
        var saveFileURL = cordova.file.externalRootDirectory + fileNmae ;
        downloadFile(saveFileURL,downloadURL,false).then(function(entry){
          //successCallback
          $ionicLoading.hide();
          cordova.plugins.fileOpener2.open(
            saveFileURL,
            'application/vnd.android.package-archive'
          );
        },function(error){
          //errorCallback
          ToastUtils.showError(error.source)
        },function(progress){
          //notifyCallback
          $timeout(function () {
            var downloadProgress = progress.toFixed(1);
            $ionicLoading.show({
              //template: "已经下载：" + Math.floor(downloadProgress) + "%"
              template: "已经下载：" + downloadProgress + "%"
            });
            if (downloadProgress > 99) {
              $ionicLoading.hide();
            }
          });
        })
      }


      /**
       * 下载方法
       * @param saveFileURL
       * @param downloadURL
       * @param trustAllHosts
       * @param options
       * @returns {*}
       */
      function downloadFile(saveFileURL,downloadURL,trustAllHosts,options){
        var q = $q.defer();
        var downloadFt = new FileTransfer();
        if(!(downloadFt?true:false)){
          q.reject('目前环境并不支持下载文件');
          return q.promise;
        }
        downloadFt.onprogress = function(progressEvent){
          if (progressEvent.lengthComputable) {
            q.notify((progressEvent.loaded / progressEvent.total) * 100);
          } else {
            q.notify(100);
          }
        };
        q.promise.cancelDownload = function(){
          downloadFt.abort();
        };
        var uri = encodeURI(downloadURL);
        options = (options !== undefined && options !== null) ? options : {
          headers: {
            "Authorization": "Basic dGVzdHVzZXJuYW1lOnRlc3RwYXNzd29yZA=="
          }
        };
        trustAllHosts = (trustAllHosts !== undefined && trustAllHosts !== null) ? trustAllHosts : false;
        var timeoutId;
        q.promise.cancelTimeOut = function(){
          try {
            if (timeoutId !== undefined && timeoutId !== null) {
              $timeout.cancel(timeoutId);
              timeoutId = null;
              console.log('cancel download Timer success');
            }
          } catch (e) {
            console.log('cancel download Timer error：'+ e.name + '：'+ e.message);
          }
        };
        if(options && options.timeout !== undefined && options.timeout !== null){
          timeoutId = $timeout(function(){
            downloadFt.abort();
            console.log('download File abort：'+'timeoutlimit!!');
          },options.timeout);
          options.timeout = null;
        }
        downloadFt.download(
          uri,
          saveFileURL,
          function (entry) {
            q.promise.cancelTimeOut();
            q.resolve(entry);
          },
          function (error) {
            q.promise.cancelTimeOut();
            q.reject(error);
          },
          trustAllHosts,
          options
        );
        return q.promise;
      };



      return {
        //checkVersion : checkVersion // 检查版本接口
        checkVersion:function() {}
      }

  }]);

  //<div class="backdrop visible active"></div>
  //  <div class="popup-container popup-showing active">
  //  <div class="popup">
  //  <div class="popup-body">
  //  <h3 style="font-size: 18px; color: #444444; margin-bottom: 10px;">发现新版本</h3>
  //  <p style="color: #838383;">
  //  最新版本：1.1.0 <br/>
  //新版本大小：5.5M <br/> <br/>
  //更新内容： <br/>
  //1.全新UI界面 <br/>
  //2.[首页排版]更优雅
  //</p>
  //</div>
  //<div class="popup-buttons">
  //  <button class="button button-default" ng-click="closeVersionDialog()">以后再说</button>
  //  <button class="button button-assertive">立即更新</button>
  //  </div>
  //  </div>
  //  </div>


});
