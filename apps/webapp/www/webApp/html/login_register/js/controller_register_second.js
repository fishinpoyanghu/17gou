/**
 * Created by Administrator on 2015/11/27.
 */
define([
  'app',
  'utils/httpRequest',
  'models/model_user',
  'html/common/service_user_info',
  'utils/toastUtil',
  'utils/exif',
  'utils/PhotoUtils'
],function(app){

  app.controller('registerSecondCtrl',
  ['$scope','$state','$ionicHistory','$stateParams','$ionicActionSheet', '$ionicLoading'
    ,'httpRequest','userModel','userInfo','ToastUtils',
  function(
    $scope, $state,$ionicHistory, $stateParams, $ionicActionSheet, $ionicLoading,
    httpRequest, userModel, userInfo,ToastUtils
  ){
    var phoneNumber = $stateParams.phoneNumber; //用户名
    var password = $stateParams.password; //密码
    var mcode = $stateParams.mcode; //验证码
    var inviteCode = $stateParams.inviteCode; //邀请码

    $scope.iconUrl = '' ;//头像

    $scope.registerInfo = {
      sex : '1',
      nickname : ''
    };

    $scope.getSex = function(Sex){
      $scope.registerInfo.sex = Sex.type ;//type类型：1：男，2：女
    };

    $scope.registerUser = function (){
      var nickname= $scope.registerInfo.nickname;
      var sex =  $scope.registerInfo.sex ;
      var icon =  $scope.iconUrl ;
      var registerRequest = {
        name : phoneNumber ,
        password : password ,
        sex : sex ,
        nick : nickname ,
        mcode : mcode,
        invite_code : inviteCode,
        icon : icon

      };
      showRegisteringDialog();
      userModel.register(registerRequest,onSuccess,onFail)

    };


    /**
     * 显示图片选择
     */
    $scope.showImageUploadChoices = function() {
      if(navigator.camera){//移动端
        $ionicActionSheet.show({
          titleText : '更换头像',
          cancelText: '取消',
          buttons   : [{text:'拍照'},{text:'从相册中选取'}],
          cancel    : function(){
            // add cancel code..
          },
          buttonClicked : function(index){
            switch(index){
              case 1://选择本地图片
//                PhotoUtils.getLocalPictureByApp(true,function(imageData){
//                  $scope.$apply(function(){
//                    $scope.iconUrl = imageData ;
//                  });
//                },function(errMsg){
//                  ToastUtils.showError(errMsg);
//                });
                PhotoUtils.takePictureByHtml5(function(imageData){
                  $scope.$apply(function(){
                    $scope.iconUrl = imageData ;
                  });
                },function(errMsg){
                  ToastUtils.showError(errMsg);
                });
                break;
              case 0:
              default://拍照
                PhotoUtils.takePhotoByApp(true,function(imageData){
                  $scope.$apply(function(){
                    $scope.iconUrl = imageData ;
                  });
                },function(errMsg){
                  ToastUtils.showError(errMsg);
                });
                break;
            }
            return true;
          }
        });

      }else{//浏览器
        PhotoUtils.takePictureByHtml5(function(imageData){
          $scope.$apply(function(){
            $scope.iconUrl = imageData ;
          });
        },function(errMsg){
          ToastUtils.showError(errMsg);
        });
      }

    };

    /**
     * 注册按钮是否能够点击
     * @returns {boolean}
     */
    $scope.isEnableRegister2 = function(){
      return ($scope.registerInfo.nickname.length > 0) ;
    };


    /**
     * 显示正在注册等待框
     */
    function showRegisteringDialog(){
      $ionicLoading.show({
        template: '正在注册...' + '<ion-spinner icon="android"></ion-spinner>',
        noBackdrop: true
      });
    }

    /**
     * 隐藏正在注册等待框
     */
    function hideRegisteringDialog(){
      $ionicLoading.hide();
    }


    /**
     * 注册失败回调
     * @param response
     */
    function onFail(response){
      hideRegisteringDialog();
      ToastUtils.showError('请检查网络状态，状态码：' + response.status);
    }

    /**
     * 注册成功回调
     * @param response
     */
    function onSuccess(response){
      hideRegisteringDialog();
      var code = response.data.code;
      var msg = response.data.msg;
      var data = response.data.data;
      switch(code){
        case 0:
          ToastUtils.showSuccess(msg);
          userInfo.saveUserInfo(data);//保存用户信息
          $state.go('tab.mainpage');
          break;
        default :
          ToastUtils.showError(msg);
          break;
      }
    }

  }]);

});
