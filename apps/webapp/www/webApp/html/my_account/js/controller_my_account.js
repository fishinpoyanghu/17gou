/**
 * 获奖记录
 * Created by Administrator on 2016/1/7.
 */
define([
    'app',
    'models/model_user',
    'utils/toastUtil',
    'html/common/storage'
], function(app) {

    app.controller(
        'myAccountCtrl', ['$scope', '$ionicHistory', '$location', '$state', '$stateParams', '$timeout', 'userModel', 'ToastUtils', 'Storage', '$q',
            function($scope, $ionicHistory, $location, $state, $stateParams, $timeout, userModel, ToastUtils, Storage, $q) {
                var sessId = Storage.get("sessId");
                getAccount();
                $scope.goBack = function() {
                    $ionicHistory.goBack();
                };

                function getAccount() {
                    ToastUtils.showLoading('加载中....');
                    userModel.getAccount(function(xhr, re) {
                        if (re.code == 0) {
                            $scope.money = re.data.money;
                            Storage.set('myAccountData_' + sessId, $scope.money);
                        } else {
                            ToastUtils.showError(re.msg);
                        }
                    }, function(xhr, re) {
                        ToastUtils.showError(re.msg);
                    }, function() {
                        ToastUtils.hideLoading();
                    })
                }


            }
        ])

});
