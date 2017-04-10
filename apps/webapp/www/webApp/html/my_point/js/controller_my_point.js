/**
 * 获奖记录
 * Created by Administrator on 2016/1/7.
 */
define([
    'app',
    'models/model_user',
    'utils/toastUtil',
    'html/common/storage',
    'components/view-sign/view_sign',
], function(app) {

    app.controller(
        'myPointCtrl', ['$scope', '$ionicHistory', '$location', '$state', '$stateParams', '$timeout', 'userModel', 'ToastUtils', 'Storage',
            function($scope, $ionicHistory, $location, $state, $stateParams, $timeout, userModel, ToastUtils, Storage) {
                var sessId = Storage.get("sessId");
                getMyPoint();
                $scope.goBack = function() {
                	$state.go('tab.account2');
//                  $ionicHistory.goBack();
                };

                function getMyPoint() {
                    userModel.getMyPoint(function(xhr, re) {
                        if (re.code == 0) {
                            $scope.data = re.data;
                            Storage.set('myPointData_' + sessId, re.data);
                        } else {
                            ToastUtils.showError(re.msg);
                        }
                    }, function(xhr, re) {
                        ToastUtils.showError(re.msg);
                    })
                }

                $scope.signList = [];
                $scope.hasSign = false;
                // var cachSignList = Storage.get(getNowDay() + '_signList_' + sessId);
                // if(cachSignList) {
                //   $scope.signList = cachSignList;
                //   $scope.hasSign = checkSign();
                // } else {
                //   getSignList()
                // }
                getSignList()

                function getSignList() {
                    ToastUtils.showLoading('加载中....');
                    userModel.getSignList(function(xhr, re) {
                        if (re.code == 0) {
                            $scope.signList = re.data.list;
                            // Storage.set(getNowDay() + '_signList_' + sessId, re.data.list);
                            $scope.hasSign = checkSign();
                        } else {
                            ToastUtils.showError(re.msg);
                        }
                    }, function(xhr, re) {
                        ToastUtils.showError(re.msg);
                    },function() {
                        ToastUtils.hideLoading();
                    })
                }
                $scope.signFinsh = true;

                $scope.sign = function() {
                    if (!$scope.signFinsh) return;
                    if ($scope.hasSign) {
                        ToastUtils.showSuccess('您已经签到过了');
                        return;
                    }
                    $scope.signFinsh = false;
                    ToastUtils.showLoading('签到中....');
                    userModel.sign(function(xhr, re) {
                        if (re.code == 0) {
                            $scope.signList.push(getNowDay());
                            // Storage.set(getNowDay() + '_signList_' + sessId, $scope.signList);
                            ToastUtils.showSuccess('签到成功');
                            $scope.hasSign = true;
                            getMyPoint()
                        } else {
                            ToastUtils.showError(re.msg);
                        }
                    }, function(xhr, re) {
                        ToastUtils.showError(re.msg);
                    }, function() {
                        ToastUtils.hideLoading();
                        $scope.signFinsh = true;
                    })
                }

                function checkSign() {
                    var index = $scope.signList.indexOf(getNowDay());
                    if (index > -1) return true;
                    return false;
                }

                function getNowDay() {
                    var date = new Date();
                    var year = date.getFullYear();
                    var month = date.getMonth() + 1;
                    var day = date.getDate();
                    var nowDay = '' + year + '-' + (month < 10 ? '0' + month : month) + '-' + (day < 10 ? '0' + day : day);
                    return nowDay;
                }
            }
        ])

});
