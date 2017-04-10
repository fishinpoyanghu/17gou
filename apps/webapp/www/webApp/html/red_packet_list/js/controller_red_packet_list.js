
define([
    'app',
    'models/model_red_packet',
    // 'html/common/constants',
    'components/view-counter/view_counter',
    'components/view-progress/view_progress',
    'utils/toastUtil'
], function(app) {

    app.controller(
        'redPacketListCtrl', ['$scope', '$ionicHistory', '$state', '$location', 'redPacketModel', 'ToastUtils',
            function($scope, $ionicHistory, $state, $location, redPacketModel, ToastUtils) {
                ToastUtils.showLoading('加载中....');
                $scope.redPacketList = [] ;
                $scope.page = 0 ;
                var isDoRefreshing;
                getRedPacketList()
                $scope.doLoadMore = function(){
                  if(!isDoRefreshing){
                    getRedPacketList(false);
                  }

                };

                $scope.doRefresh = function(){
                  $scope.page = 0 ;
                  $scope.hasMoreData = true ;
                  isDoRefreshing = true ;
                  getRedPacketList(isDoRefreshing);
                };

                function getRedPacketList(isRefresh){
                  $scope.page++;
                  redPacketModel.getRedPacketList1($scope.page,function(response){
                    var code = response.data.code ;
                    var msg = response.data.msg ;
                    switch (code){
                      case 0 :
                        var dataList = response.data.data ;
                        if(isRefresh){
                          $scope.redPacketList = [] ;
                          isDoRefreshing = false ;
                        }
                        if(dataList.length>=10){
                          $scope.hasMoreData = true ;
                          // dataList.pop();
                        }else{
                          $scope.hasMoreData = false ;
                        }
                        $scope.redPacketList = $scope.redPacketList.concat(dataList);
                        break;

                      default :
                        ToastUtils.showError(msg);
                        break ;
                    }
                  },function(response){
                    //onFail
                    
                    ToastUtils.showError('获取红包列表失败，请检查网络状态，状态码：' + response.status);
                   
                  },function(){
                    //onFinal
                    $scope.isLoadFinished = true ;
                    $scope.$broadcast('scroll.refreshComplete');
                    $scope.$broadcast('scroll.infiniteScrollComplete');
                    ToastUtils.hideLoading();
                  });
                }
                $scope.back = function() {
                    $ionicHistory.goBack();
                }

               


            }
        ])

});
