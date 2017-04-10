define([
  'app',
  'html/common/service_user_info',
  'models/model_user',
  'utils/toastUtil',
  'utils/exif',
  'utils/PhotoUtils'
],function(app){
		//收入管理首页
  app.controller('earningManageCtrl',
  ['$scope','$state','$ionicHistory','$stateParams','$ionicActionSheet','$ionicLoading','userInfo','userModel','ToastUtils',
  function($scope, $state, $ionicHistory, $stateParams, $ionicActionSheet, $ionicLoading,userInfo,userModel,ToastUtils) {


    $scope.showPhone = function(phone) {
      return (/^(13|18|15|14|17)\d{9}$/i.test(phone))
    }
		
	
	//获取当前用户信息
	$scope.getCurrUserInfo = function(){
//  	console.log(userInfo.getUserInfo())
      return userInfo.getUserInfo(); 
  };


  }]);

});



