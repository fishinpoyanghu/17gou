define([
  'app',
  'models/model_address',
  'utils/toastUtil',
  'html/common/global_service',
  'html/common/service_user_info'
],function(app){

  app.controller('AddressAddCtrl',
  ['$scope','$state','$ionicHistory','$ionicPopup','$ionicPlatform','addressModel','ToastUtils','Global','userInfo', 
  function($scope, $state,$ionicHistory,$ionicPopup,$ionicPlatform,addressModel,ToastUtils,Global,userInfo) {

    $scope.newAddressInfo = {
      name : '',
      mobile : userInfo.getUserInfo().phone,
      province : '',
      city : '',
      area : '',
      detail : '',
      is_default : 0
    };

    $scope.updateSelection = function(){
      if($scope.newAddressInfo.is_default===0){
        $scope.newAddressInfo.is_default = 1 ;
      }else if($scope.newAddressInfo.is_default===1){
        $scope.newAddressInfo.is_default = 0 ;
      }
    };

    /**
     * 返回键监听
     * @type {Function}
     */
    var deleteRegister = $ionicPlatform.registerBackButtonAction(function(e){
      showConfirm();
    },101);


    /**
     * 新增地址
     */
    $scope.addAddress = function(){
      if($scope.addingAddress) return;
      $scope.addingAddress = true;
      addressModel.addAddress($scope.newAddressInfo, function(response){
        //onSuccess
        $scope.addingAddress = false;
        var code = response.data.code;
        var msg = response.data.msg;
        switch(code){
          case 0:
            ToastUtils.showSuccess(msg);
            back()
            break ;
          case 6:
            ToastUtils.showWarning(msg);
            $state.go('login');
            break ;
          default:
            ToastUtils.showError(msg);
            break;
        }
      },function(response){
        //onFail
        $scope.addingAddress = false;
        ToastUtils.showError('请检查网络状态，状态码：' + response.status);
      });
    };

    $scope.isShowSelector = {
      show : false,
      type : ''
    };

    /**
     * 显示地区选择器
     * @param type ${ PROVINCE, CITY, COUNTY }
     */
    $scope.showAreaSelector = function(type){
      $scope.isShowSelector.show = true;
      $scope.isShowSelector.type = type ;
    };


    $scope.goBack=function(){
      showConfirm();
    };

    function showConfirm(){

      if( $scope.newAddressInfo.name!=''
        || $scope.newAddressInfo.mobile!=''
        || $scope.newAddressInfo.province!=''
        || $scope.newAddressInfo.city!=''
        || $scope.newAddressInfo.area!=''
        || $scope.newAddressInfo.detail!=''
      ){
        $ionicPopup.confirm({
          title: '您的编辑未保存，确定要返回吗？',
          cancelText: '取消',
          cancelType: 'button-default',
          okText: '确定',
          okType: 'button-assertive'
        }).then(function (res) {
          if (res) {
            if(deleteRegister){
              deleteRegister();
            }
            back()
          }
        });
      }else{
        if(deleteRegister){
          deleteRegister();
        }
        back()
      }


    }

    function back() {
      if (Global.isInweixinBrowser()) {
        history.back();
      } else {
        $ionicHistory.goBack();
      }
    }

  }]);

});



