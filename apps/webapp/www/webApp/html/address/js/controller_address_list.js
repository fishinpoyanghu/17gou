define([
  'app',
  'models/model_address',
  'utils/toastUtil'
],function(app){

  app.controller('AddressListCtrl',
  ['$scope','$state','$ionicHistory','addressModel','ToastUtils',
  function($scope, $state,$ionicHistory,addressModel,ToastUtils) {


    $scope.addressList = [];//收货地址列表
    $scope.isLoadFinished = false ;//是否加载结束


    /**
     * 跳转到新增收货地址页面
     */
    $scope.startToAddressAdd = function(){
      $state.go('addressAdd');
    };

    /**
     * 跳转到更新收货地址页面
     */
    $scope.startToAddressUpdate = function(addr){
      $state.go('addressUpdate',{address:addr});
    };

    /**
     * 重新加载
     */
    $scope.reload = function(){
      getAddressList();
    };

    getAddressList();//获取收货地址
    function getAddressList(){
      addressModel.getAddressList(function(response){
        //onSuccess
        isConnect = true ;
        var code = response.data.code;
        var msg = response.data.msg;
        switch(code){
          case 0:
            $scope.addressList = response.data.data;
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
        isConnect = false ;
        ToastUtils.showError('请检查网络状态');
      },function(){
        //onFinal
        $scope.isLoadFinished = true ;
      });


      /**
       * 数据是否为空
       * @returns {boolean}
       */
      $scope.isDataEmpty = function(){
        var isEmpty = true ;
        if($scope.addressList.length>0){
          isEmpty = false ;
        }
        return isEmpty ;
      };

      var isConnect = true ;//网络是否连接
      /**
       * 显示断网页面
       * @returns {boolean}
       */
      $scope.isShowDisconnect = function(){
        return $scope.isDataEmpty() && $scope.isLoadFinished && !isConnect ;
      };


    }
  }]);

});



