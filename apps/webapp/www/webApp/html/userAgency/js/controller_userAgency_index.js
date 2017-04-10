define([
  'app',
  'html/common/service_user_info',
  'models/model_user',
'html/common/agency_info',
'models/model_agency',
  'utils/toastUtil',
  'utils/exif',
  'utils/PhotoUtils'
],function(app){

  app.controller('userAgencyCtrl',
  ['$scope','$state','$ionicHistory','$stateParams','$ionicActionSheet','$ionicLoading','userInfo','userModel','agencyInfo','agencyModel','ToastUtils',
  function($scope, $state, $ionicHistory, $stateParams, $ionicActionSheet, $ionicLoading,userInfo,userModel,agencyInfo,agencyModel,ToastUtils) {

		$scope.eyesImg = "img/agency/closeEyes.png";
//		$scope.agencyCash = parseInt(userInfo.getUserInfo().agencyData.data.withdraw_deposit);
		isNaN($scope.agencyCash) && ($scope.agencyCash = '*');
		$scope.agencyCash = '***';
    $scope.showPhone = function(phone) {
      return (/^(13|18|15|14|17)\d{9}$/i.test(phone))
    }

		$scope.$on('$ionicView.beforeEnter', function() {
			agencyInfo.getagencymsg();
    })
		
		
    /**
     * 点击眼睛图片显示或隐藏金额
     */
//  $scope.hideOrshowMoney = function(me){
//  	var closeImg = "img/agency/closeEyes.png",
//  			openImg = "img/agency/openEyes.png",
////  			showMoney = parseInt(userInfo.getUserInfo().agencyData.data.withdraw_deposit),
//  			hideMoney = '****';
//  	isNaN(showMoney) && (showMoney = '*');
//			$scope.eyesImg == closeImg ? ($scope.eyesImg=openImg,$scope.agencyCash = hideMoney) : ($scope.eyesImg=closeImg,$scope.agencyCash = showMoney);
//  };
    
    /**
     * 跳转到修改昵称页面
     */
    $scope.startToModifyNick = function(){
      $state.go('modifyNick');
    };
    
    
    /**
     * 跳转到数据统计页面
     */
    $scope.startToDataStatistics = function(){
      $state.go('data_statistics');
    };
    
    
    /**
     * 跳转到我的粉丝首页页面
     */
    $scope.startToMyfans = function(){
      $state.go('my_fans');
    };
    
    
    /**
     * 跳转到我的收入管理页面
     */
    $scope.startToEarningManage = function(){
      $state.go('earning_manage');
    };
	
    /**
     * 跳转到我的我的推广页面
     */
    $scope.startToMyGenerlize = function(){
      $state.go('my_generlize');
    };
	
	
	
		//获取当前用户信息
		$scope.getCurrUserInfo = function(){
	      return userInfo.getUserInfo(); 
	  };
    /**
     * 显示图片选择页面
     */
    $scope.showImageSelector = function() {
      if(navigator.camera){//移动端
        $ionicActionSheet.show({
          titleText : '更换头像',
          cancelText: '取消',
          buttons   : [{text:'拍照'},{text:'从相册中选取'}],
          cancel    : function(){
            // add cancel code..
          },
          buttonClicked : function(index){
            switch(index){
              case 1://选择本地图片
                // PhotoUtils.getLocalPictureByApp(true,function(imageData){
                //   showUploadingDialog();
                //   userModel.updateHeadIcon(imageData,uploadSuccess,uploadFail,null);
                // },function(errMsg){
                //   //获取图片失败
                //   ToastUtils.showError(errMsg);
                // });
                  PhotoUtils.takePictureByHtml5(function(imageData){
                    showUploadingDialog();
                    userModel.updateHeadIcon(imageData,uploadSuccess,uploadFail,null);
                  },function(errMsg){
                    //获取图片失败
                    ToastUtils.showError(errMsg);
                  });
                break;
              case 0:
              default://拍照
                PhotoUtils.takePhotoByApp(true,function(imageData){
                  showUploadingDialog();
                  userModel.updateHeadIcon(imageData,uploadSuccess,uploadFail,null);
                },function(errMsg){
                  //获取图片失败
                  ToastUtils.showError(errMsg);
                });
                break;
            }
            return true;
          }
        });

      }else{//浏览器
        PhotoUtils.takePictureByHtml5(function(imageData){
          showUploadingDialog();
          userModel.updateHeadIcon(imageData,uploadSuccess,uploadFail,null);
        },function(errMsg){
          //获取图片失败
          ToastUtils.showError(errMsg);
        });
      }



    };


    /**
     * 上传图片成功回调（用base64上传）
     * @param response
     * @param data
     * @param status
     * @param headers
     * @param config
     * @param statusText
     */
    function uploadSuccess(response, data,status,headers,config,statusText){
      if(data.code === 0) {
        hideUploadingDialog();
        userInfo.updateHeadIcon(data.data.icon);
      }else{
        hideUploadingDialog();
      }
    }

    /**
     * 上传图片失败回调（用base64上传）
     * @param response
     * @param data
     * @param status
     * @param headers
     * @param config
     * @param statusText
     */
    function uploadFail(response, data,status,headers,config,statusText){
      hideUploadingDialog();
    }

    function showUploadingDialog(){
      $ionicLoading.show({
        template: '上传中...' + '<ion-spinner icon="android"></ion-spinner>',
        noBackdrop: true
      });
    }

    function hideUploadingDialog(){
      $ionicLoading.hide();
    }

  }]);

});



