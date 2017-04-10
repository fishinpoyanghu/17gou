define(['app','html/common/global_service'], function(app) {
    app.controller('attentionCtrl', ['$scope', '$state','Global', '$ionicHistory','$timeout',function discoveryCtrl($scope, $state,Global,$ionicHistory,$timeout) {
      if(Global.isInweixinBrowser()) {
        $scope.text = '长按二维码图片，识别图中二维码 关注';
      } else {
        $scope.text = '截图到微信中打开<br/>长按二维码图片，识别图中二维码 关注';
      }

      $scope.back = function() {
      if (Global.isInweixinBrowser()) {
        history.back();
      } else {
        $ionicHistory.goBack();
      }
    }
       $scope.img=false;
        $scope.show_img=function(){
            $scope.img=true;
        }
        $scope.hide_img=function(){
            $scope.img=false;
        }

    }]);

})
