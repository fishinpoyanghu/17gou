define(
    [
        'app',
        'models/model_red_packet',
        'utils/toastUtil'
    ],
    function(app) {
        app.controller('redPacketJoinRecordCtrl', joinRecordCtrl);

        joinRecordCtrl.$inject = ['$stateParams', '$scope', '$state', 'redPacketModel', 'ToastUtils','$ionicHistory'];

        function joinRecordCtrl($stateParams, $scope, $state, redPacketModel, ToastUtils,$ionicHistory) {
            ToastUtils.showLoading('加载中....');
            $scope.joinRecordList = [];
            getJoinRecord()
            function getJoinRecord() {
                $scope.isLoadFinished = false;
                redPacketModel.getJoinRecord($state.params.activity_id, function(xhr,re) {
                    var code = re.code;
                    if (code == 0) {
                        $scope.joinRecordList = re.data;
                    } else {
                        ToastUtils.showMsgWithCode(code, re.msg);
                    }
                }, function(response) {
                    ToastUtils.showError('获取参与记录失败，请检查网络状态，状态码：' + response.status);

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
