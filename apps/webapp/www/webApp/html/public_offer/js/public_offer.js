define([
    'app',
    'models/model_user',
    'models/public_function',
    'utils/toastUtil',
    'html/common/global_service',
    'components/view-progress/view_progress'
], function(app) {

    app.controller(
        'PublicOfferCtrl', ['$scope', '$ionicHistory', '$location', '$state', '$stateParams', '$timeout', 'userModel','publicFunction', 'ToastUtils','$rootScope',
            function($scope, $ionicHistory, $location, $state, $stateParams, $timeout, userModel,publicFunction, ToastUtils,$rootScope) {
                $scope.listDatas = [];
                $scope.diskData = [];
                $scope.count = '';
                $scope.isLoadFinished = true;
//              getData();

                $scope.getData = getData;
                $scope.getRecentOrderList = getRecentOrderList;
                /*$scope.doRefresh = doRefresh;*/

                //获取公盘数据
                function getData(doRefresh) {
                    userModel.getDisk( function(xhr, re) {
                        var code = re.code;
                        if (code == 0) {
                            var data = re.data;
                            $scope.diskData=data;
                        } else {
                            ToastUtils.showMsgWithCode(code, re.msg);
                        }
                    }, function(response, data) {
                        /*ToastUtils.showMsgWithCode(7, '获取提现申请记录失败：' + '状态码：' + response.status);*/
                    }, function() {
                        /*$scope.isLoadFinished = true;*/
                        if (doRefresh) $scope.$broadcast('scroll.refreshComplete');
                    })
                }

                //获取最新动态数据
                function getRecentOrderList(){
                    userModel.getRecentOrderList($scope.pageData.from, $scope.pageData.count, function(xhr, re) {
                        var code = re.code;
                        if (code == 0) {
                            var data = re.data;
                            $scope.listDatas = $scope.listDatas.concat(data);
                            $scope.listDatas = publicFunction.unique($scope.listDatas, $scope.pageData.keyword);
                            //							console.log(publicFunction.unique([1,2,3,4,5,1,2,3,4,6]));
                            $scope.pageData.pageOver = !($scope.pageData.count == data.length);
                            $scope.pageData.from += $scope.pageData.count;
                        } else {
                            ToastUtils.showMsgWithCode(code, re.msg);
                        }
                    }, function(response, data) {
                        /*ToastUtils.showMsgWithCode(7, '获取提现申请记录失败：' + '状态码：' + response.status);*/
                    }, function() {
                        /*$scope.isLoadFinished = true;*/
                        $scope.$broadcast('scroll.infiniteScrollComplete');
                        if(doRefresh) $scope.$broadcast('scroll.refreshComplete');
                    })
                };

                $scope.go_mainpage=function(){
                    $state.go("tab.mainpage")
                }



                /*getRecentOrderList();
                getData('');*/
                $scope.doRefresh = function() {
                    $scope.pageData.pageOver = false;
                    $scope.pageData.from = 1;
                    $scope.listDatas = [];
                    getData('doRefresh');
                    getRecentOrderList();
                }

                $scope.goBack = function() {
                    $ionicHistory.goBack();
                };

                /*跳转到宝贝详情页*/
                $scope.gotoDetail = function(id) {
                    $state.go('activity-goodsDetail', { activityId: id});
                };

                /**
                 * 跳转到TA的页面
                 * @param uicon
                 * @param unick
                 */
                $scope.goToHisPage = function(uicon,unick,uid){
                    $state.go('hispage',{uicon:uicon,unick:unick,uid:uid});
                };

                $scope.$on('$ionicView.beforeEnter',function(ev,data){
                    $scope.pageData = {
                        from: 1,
                        count: 4,
                        pageOver: false,
                        keyword: 'order_id' //用于去除重复数据的关键字
                    }
                    getData('doRefresh');
                    getRecentOrderList();
                })
                $rootScope.$on('$locationChangeSuccess',function(){//返回前页时，刷新前页
                });

            }
        ])

});
