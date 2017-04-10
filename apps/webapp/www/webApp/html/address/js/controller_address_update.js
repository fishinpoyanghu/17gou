define([
  'app',
  'models/model_address',
  'utils/toastUtil',
  'html/common/global_service'
],function(app){

  app.controller('AddressUpdateCtrl',
  ['$scope','$state','$ionicHistory','$stateParams','addressModel','ToastUtils','$ionicPopup','Global',
  function($scope, $state,$ionicHistory,$stateParams,addressModel,ToastUtils,$ionicPopup,Global) {

    var address = $stateParams.address ;
    $scope.isShowDefaultSelect = !address.is_default;
    $scope.addressInfo = {
      address_id : address.address_id,
      name : address.name,
      mobile : address.mobile,
      province :address.province,
      city : address.city,
      area : address.area,
      detail : address.detail,
      is_default : address.is_default
    };

    $scope.goBack = function() {

      if (Global.isInweixinBrowser()) {
        history.back();
      } else {
        $ionicHistory.goBack();
      }
    };

    $scope.updateSelection = function(){
      if($scope.addressInfo.is_default===0){
        $scope.addressInfo.is_default = 1 ;
      }else if($scope.addressInfo.is_default===1){
        $scope.addressInfo.is_default = 0 ;
      }
    };

    $scope.getChecked = function(){
      var checked = false ;
      if($scope.addressInfo.is_default===1){
        checked = true ;
      }
      return checked ;
    };

    /**
     * 删除地址
     */
    $scope.deleteAddress = function(){
      $ionicPopup.confirm({
        title: '确定删除？',
        cancelText: '取消',
        cancelType: 'button-default',
        okText: '确定',
        okType: 'button-assertive'
      }).then(function (res) {
        if (res) {
          addressModel.deleteAddress( $scope.addressInfo.address_id , function(response){
            //onSuccess
            var code = response.data.code;
            var msg = response.data.msg;
            switch(code){
              case 0:
                ToastUtils.showSuccess('已删除');
                $scope.goBack()
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
            ToastUtils.showError('请检查网络状态，状态码：' + response.status);
          });
        }
      });


    };


    /**
     * 更新地址
     */
    $scope.updateAddress = function(){
      if($scope.addressInfo.province==''){
        ToastUtils.showWarning('请选择所在省！');
        return ;
      }else if($scope.addressInfo.province!='' && $scope.addressInfo.city==''){
        ToastUtils.showWarning('请选择所在市！');
        return ;
      }else if($scope.addressInfo.province!='' && $scope.addressInfo.city!='' && $scope.addressInfo.area==''){
        ToastUtils.showWarning('请选择所在县！');
        return ;
      }
      addressModel.updateAddress($scope.addressInfo , function(response){
        //onSuccess
        var code = response.data.code;
        var msg = response.data.msg;
        switch(code){
          case 0://更新成功
            ToastUtils.showSuccess('修改成功');
            $scope.goBack()
            break ;
          case 6:
            ToastUtils.showWarning(msg);
            $state.go('login');
            break ;
          default :
            ToastUtils.showError(msg);
            break;
        }
      },function(response){
        //onFail
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

  }]);

});



