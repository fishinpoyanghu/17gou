/**
 * Created by luliang on 2016/1/15.
 */
define([
  'app',
  'html/common/global_service',
  'html/common/storage'
],function(app){
  app
    .controller('AppBoostrapController',['$scope','$stateParams','$location','Global','$state','Storage',
      function($scope,$stateParams,$location,Global,$state,Storage){

      var inviteCode = $stateParams.invite_code;

      function dispatchPath(){
        if(inviteCode&&inviteCode!=null){
          Global.setInviteCode(inviteCode);
          if(Global.isInAPP() && appConfig.hasGuide && !Storage.get('firstOpen')) {
            enter('guide_page');
          } else {
            enter('tab.mainpage');
          }
          
        }else{
          if(Global.isInAPP() && appConfig.hasGuide && !Storage.get('firstOpen')) {
            enter('guide_page');
          } else {
            enter('tab.mainpage');
          }
        }
      }
      function enter(path){
        $state.go(path)
      }

      dispatchPath();
    }])
});
