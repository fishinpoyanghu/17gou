/**
 * Created by Administrator on 2015/11/5.
 */
define([
  'app',
  'html/common/service_user_info',
  'models/model_user',
  'utils/toastUtil',
  'html/common/storage',
  'html/common/global_service'
],function(app){
  
  app.controller('LoginTransferPageCtrl',
  ['$scope','$state','$stateParams','$ionicPopup','userModel', 'MyUrl','ToastUtils','$timeout','Global','Storage',
  function($scope, $state, $stateParams,$ionicPopup,userModel, MyUrl,ToastUtils,$timeout,Global,Storage){

    $scope.inviteCode = {
      code : ''
    };
    
    function initInviteCode(){
      var inviteCode = Global.getInviteCode();
      if(inviteCode==null || inviteCode=='' || angular.isUndefined(inviteCode)){
        $scope.inviteCode.code = '' ;
      }else{
        $scope.inviteCode.code = inviteCode ;
        Global.removeInviteCode();
        bindInviteCode(inviteCode,true)
      }
    }
    MyUrl.setSessid($stateParams.sessid);

    $scope.isShowBindInviteDialog = false ;
    userModel.getLoginUserInfo(function(response){
        var code = response.data.code;
        var msg = response.data.msg ;
        var wxregister_first=response.data.data.wxregister_first; 
        if(code === 0){  
                  startToPage();
                  return true;
                 if(!(/^(13|18|15|14|17)\d{9}$/i.test(response.data.data.phone))){
                  $state.go('BoundPhoneNumber',{redpacket:'yes'}); //直接改为不询问直接跳转到绑定手机
            /*     $ionicPopup.confirm({
                       title: '绑定手机才能联系你收货，立刻绑定!',
                       cancelText : '取消',
                       cancelType : 'button-default',
                       okText : '确定',
                       okType : 'button-positive'
                       }).then(function(res) {
                           if(res) {//logout
                               $state.go('BoundPhoneNumber',{redpacket:'yes'});

                           } else {
                               //cancel logout
                           }
                   });*/
                // $state.go('tab.mainpage'); 
                 return true;
            } 
            startToPage();
            return true;
            var rltData = response.data.data;
          if(!angular.isUndefined(rltData.rebate_uid) && rltData.rebate_uid==0){
            //显示绑定邀请码对话框
            $scope.isShowBindInviteDialog = true ;
            initInviteCode();
          }else{
            //跳转到首页
            startToPage()
          }
        }else{
          ToastUtils.showError(msg);
        }
    },function(response){
      ToastUtils.showError('请检查网络状态！');
    });



    /**
     * 关闭绑定邀请码对话框
     */
    $scope.closeBindInviteDialog = function(){
      $scope.isShowBindInviteDialog = false ;
      startToPage()
      // bindInviteCode('88888');//邀请码，当用户没有输入时，传88888
    };


    $scope.toBindInviteCode = function(){
        if(!angular.isUndefined($scope.inviteCode.code) && $scope.inviteCode.code!=''){
          bindInviteCode($scope.inviteCode.code);
        }else{
          startToPage()
          // bindInviteCode('88888');//邀请码，当用户没有输入时，传88888
        }
    };


    /**
     * 绑定邀请码
     */
    function bindInviteCode(inviteCode,autoBind){
      userModel.bindInviteCode(inviteCode,function(response){
        var code = response.data.code;
        var msg = response.data.msg ;
        if(code === 0){
          //绑定邀请码成功
          if(!autoBind) ToastUtils.showSuccess('绑定邀请码成功');

          $timeout(function() {//延迟一秒在跳转到主页
            startToPage()
          },1000);

        }else{
          ToastUtils.showError(msg);
        }
      },function(response){
        ToastUtils.showError('请检查网络状态！');
      });
    }

    function startToPage() {
        var fromState = Storage.get('fromState')
        var fromParams = Storage.get('fromParams') || {};
        console.log(fromState,fromParams);
        if(fromState) {
             Storage.remove('fromState');
             Storage.remove('fromParams');
             if(fromState=='Christmas_Day' || fromState=='Christmas_Day2'){
                $state.go('tab.mainpage');
                return false;
             }
            $state.go(fromState,fromParams)
        } else {
            $state.go('tab.mainpage');
        } 
    }

  }]);


});



