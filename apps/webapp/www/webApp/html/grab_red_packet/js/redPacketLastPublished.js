define(
    [
        'app',
        'models/model_red_packet',
        'utils/toastUtil'
    ],
    function(app) {
        app.controller('redPacketLastPublishedCtrl', redPacketLastPublishedCtrl);

        redPacketLastPublishedCtrl.$inject = ['$stateParams', '$scope', '$state', 'redPacketModel', 'ToastUtils','$ionicHistory'];

        function redPacketLastPublishedCtrl($stateParams, $scope, $state, redPacketModel, ToastUtils,$ionicHistory) {
            ToastUtils.showLoading('加载中....');
            $scope.redPacketList = [];
            $scope.page = 0;
            getRedPacketLastPublished()
            $scope.getRedPacketLastPublished  = getRedPacketLastPublished;
            function getRedPacketLastPublished() {
                $scope.page++;
                $scope.isLoadFinished = false;
                redPacketModel.getRedPacketLastPublished($state.params.red_id,$scope.page, function(xhr,re) {
                    var code = re.code;
                    if (code == 0) {
                        var dataList = re.data;
                        if(dataList.length>=10){
                          $scope.hasMoreData = true ;
                        }else{
                          $scope.hasMoreData = false ;
                        }
                        $scope.redPacketList = $scope.redPacketList.concat(dataList);
                    } else {
                        ToastUtils.showMsgWithCode(code, re.msg);
                    }
                }, function(response) {
                    ToastUtils.showError('获取往期记录失败，请检查网络状态，状态码：' + response.status);

                }, function() {
                    //onFinal
                    $scope.isLoadFinished = true;
                    ToastUtils.hideLoading();
                });
            }
            $scope.back = function() {
                $ionicHistory.goBack();
            }

        }
    }
)
