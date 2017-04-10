/**
 * 支付中转页面
 * Created by luliang on 2016/1/29.
 */
define([
  'app',
  'html/common/local_database',
  'html/common/storage'
],function(app){
  app
    .controller('PayTransferController',['$scope','$location','$ionicPopup','localDatabase','$state','Storage','$ionicHistory',
      function($scope,$location,$ionicPopup,localDatabase,$state,Storage,$ionicHistory){

      function isCacheEmpty(oderInfo){
        return !oderInfo;
      }

      function checkExitsOldOder(){
        var oderInfo = localDatabase.getOderInfo();
        return !isCacheEmpty(oderInfo);
      }

      var _check_continue = false;
      function showConfirm(){
        var confirmPopup = $ionicPopup.confirm({
          title: '您还有上次订单尚未完成，是否前往？',
          scope: $scope,
          buttons:[
            {
              text:'是',
              onTap:function(e){
                _check_continue = true;
                return _check_continue;
              }
            },
            {
              text:'<b>继续本次</b>',
              type:'button-assertive',
              onTap:function(e){
                _check_continue = false;
                return _check_continue;
              }
            }
          ]
        });
        confirmPopup.then(function(res) {
          if(res){
            //do nothing
          }else{
            localDatabase.removeOderInfo();
          }
          goToPay();
        });
      }

      function goToPay(){
        ssjjLog.log('去支付订单');
        $state.go('pay');
      }

      function start(){
        var result = checkExitsOldOder();
        if(result){
          showConfirm();
        }else{
          if(Storage.get('payVisit')) {
            $state.go('tab.mainpage')
          } else {
            goToPay();
            Storage.set('payVisit',true);
          }
          
        }
      }

      start();
    }])
});
