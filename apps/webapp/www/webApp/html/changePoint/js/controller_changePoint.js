define([
    'app',
    'models/model_user',
    'utils/toastUtil',
    'html/common/storage'
], function(app) {

    app.controller(
        'changePointCtrl', ['$scope', '$ionicHistory', '$location', '$state', '$stateParams', '$timeout', 'userModel', 'ToastUtils', '$state', 'Storage',
            function($scope, $ionicHistory, $location, $state, $stateParams, $timeout, userModel, ToastUtils, $state, Storage) {
                var sessId, myPointData;
                var isSubmiting = false;
                $scope.$on('$ionicView.beforeEnter', function(ev, data) {
                    //获取用户信息

                    sessId = Storage.get("sessId");
                    myPointData = Storage.get('myPointData_' + sessId);
                    $scope.hasPoint = Number(myPointData.point);   // 现有云币
                });
                $scope.inputData = {
                    point: ''
                };
                $scope.tipsMsg = '';
                $scope.canGetMoney = 0;
                $scope.inviteMoney = sessionStorage.getItem('inviteMoney');

                function validate() {
                    var msg = "false";
                    var point = Number($scope.inputData.point);
                    if (point === '') {
                        msg = "积分不能为空";
                    } else if (point > $scope.hasPoint) {
                        msg = "兑换不能大于当前积分数";
                    } else {
                        msg = "true";
                    }

                    return msg;
                }

                $scope.changePoint = function() {
                    var point = Number($scope.inputData.point) || 0;

                    $scope.canGetMoney = parseInt(point / 100);
                }

                $scope.submit = function() {
                    if (isSubmiting) return;
                    isSubmiting = true;
                    var validateMsg = validate();
                    $scope.tipsMsg = '';
                    if (validateMsg !== 'true') {
                        ToastUtils.showTips(validateMsg);
                        return;
                    }
                    var postData = {
                        point: $scope.inputData.point
                    };
                    userModel.changePoint(postData, function(xhr, re) {
                        var code = re.code;
                        if (code == 0) {
                            // $scope.tipsMsg = '兑换成功！请在余额及明细中查询所得';
                            ToastUtils.showSuccess('兑换成功！请在余额及明细中查询所得', 5000);
                            $state.go('tab.account');
                        } else {
                            ToastUtils.showMsgWithCode(code, re.msg);
                        }
                    }, function(response, data) {
                        ToastUtils.showMsgWithCode(7, '兑换失败：' + '状态码：' + response.status);
                    }, function() {
                        isSubmiting = false;
                    });
                };

                $scope.goBack = function() {
                    $ionicHistory.goBack();
                };

            }
        ])

});
