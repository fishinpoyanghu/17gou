/**
 * Created by Administrator on 2015/12/29.
 */
define([
  'app',
  'models/model_invite',
  'html/common/constants',
  'utils/toastUtil'
],function(app){

  app.controller(
    'RebateListCtrl',['$scope','$ionicHistory','$state','inviteModel','ToastUtils',
      function($scope,$ionicHistory,$state,inviteModel,ToastUtils){

        $scope.rebateList = [];

        getRebateList();

        /**
         * 获取邀请详情
         */
        function getRebateList(){
          inviteModel.getRebateList(0,10,function(response){
            //onSuccess
            var code = response.data.code ;
            var msg = response.data.msg ;
            switch (code){
              case 0 :
                var data = response.data.data ;
                $scope.rebateList = data ;
                break;
              default :
                ToastUtils.showError(msg);
                break;

            }
          },function(response){
            //onFail
            ToastUtils.showError('请检查网络状态，状态码：' + response.status);
          });
        }

  }])

});
