define([
    'app',
    'models/model_address',
    'utils/toastUtil',
    'html/common/storage',
    'html/common/global_service'
], function(app) {

    app.controller('AddressSelectCtrl', ['$scope', '$state', '$ionicHistory', '$stateParams', '$ionicPopup', 'addressModel', 'ToastUtils','Storage','Global',
        function($scope, $state, $ionicHistory, $stateParams, $ionicPopup, addressModel, ToastUtils,Storage,Global) {


            $scope.addressList = []; //收货地址列表
            var activity_id = $stateParams.activity_id; //期号
            var type = $stateParams.type;
            
            /**
             * 跳转到地址列表
             */
            $scope.startToAddressList = function() {
                $state.go('addressList');
            };


            /**
             * 确认选择地址？
             * @param addr
             */
            $scope.showConfirmSelectDialog = function(addr) {
            	console.log(type)
                $ionicPopup.confirm({
                    title: '确认提交吗？',
                    cancelText: '取消',
                    cancelType: 'button-default',
                    okText: '确定',
                    okType: 'button-positive'
                }).then(function(res) {
                    if (res) { //sure
                        if (type == 'duobao') {
                            confirmSelectAddress(activity_id, addr.address_id);
                        } else if (type == 'pintuan') {
                        	addressModel.confirmPinTuanAddress(activity_id, addr.address_id, function(response) {
			                    //onSuccess
			                    var code = response.data.code;
			                    var msg = response.data.msg;
			                    switch (code) {
			                        case 0:
			                            var address = response.data.data.address;
			                            back();
			                            break;
			                        case 6:
			                            ToastUtils.showWarning(msg);
			                            $state.go('login');
			                            break;
			                        default:
			                            ToastUtils.showError(msg);
			                            break;
			                    }
			                }, function(response) {
			                    //onFail
			                    ToastUtils.showError('请检查网络,状态码：' + response.status);
			                })
                        }else if (type == 'pintuan2') {
                        	addressModel.confirmPinTuanAddress2(activity_id, addr.address_id, function(response) {
			                    //onSuccess
			                    var code = response.data.code;
			                    var msg = response.data.msg;
			                    switch (code) {
			                        case 0:
			                            var address = response.data.data.address;
			                            back();
			                            break;
			                        case 6:
			                            ToastUtils.showWarning(msg);
			                            $state.go('login');
			                            break;
			                        default:
			                            ToastUtils.showError(msg);
			                            break;
			                    }
			                }, function(response) {
			                    //onFail
			                    ToastUtils.showError('请检查网络,状态码：' + response.status);
			                })
                        }
                        else{
                           addressModel.confirmAddressChoujiang(activity_id, addr.province+addr.city+addr.area+'  '+addr.detail,addr.name,addr.mobile, function(response) {
                                //onSuccess
                                var code = response.data.code;
                                var msg = response.data.msg;
                                switch (code) {
                                    case 0:
                                        // var address = response.data.data.address;
                                        back();
                                        break;
                                    case 6:
                                        ToastUtils.showWarning(msg);
                                        $state.go('login');
                                        break;
                                    default:
                                        ToastUtils.showError(msg);
                                        break;
                                }
                            }, function(response) {
                                //onFail
                                ToastUtils.showError('请检查网络,状态码：' + response.status);
                            })
                        }

                    } else {
                        //cancel
                    }
                });
            };

            getAddressList(); //获取收货地址

            /**
             * 获取收货地址列表
             */
            function getAddressList() {
                addressModel.getAddressList(function(response) {
                    //onSuccess
                    var code = response.data.code;
                    var msg = response.data.msg;
                    switch (code) {
                        case 0:
                            $scope.addressList = response.data.data;
                            break;
                        case 6:
                            ToastUtils.showWarning(msg);
                            $state.go('login');
                            break;
                        default:
                            ToastUtils.showError(msg);
                            break;
                    }
                }, function(response) {
                    //onFail
                    ToastUtils.showError('请检查网络,状态码：' + response.status);
                });
            }

            /**
             * 确认收货地址
             */
            function confirmSelectAddress(addressId, activityId) {

                addressModel.confirmAddress(addressId, activityId, function(response) {
                    //onSuccess
                    var code = response.data.code;
                    var msg = response.data.msg;
                    switch (code) {
                        case 0:
                            var address = response.data.data.address;
                            back();
                            break;
                        case 6:
                            ToastUtils.showWarning(msg);
                            $state.go('login');
                            break;
                        default:
                            ToastUtils.showError(msg);
                            break;
                    }
                }, function(response) {
                    //onFail
                    ToastUtils.showError('请检查网络,状态码：' + response.status);
                })
            }


            function back() {
              if (Global.isInweixinBrowser()) {
                history.back();
              } else {
                $ionicHistory.goBack();
              }
            }

        }
        
    ]);

});
