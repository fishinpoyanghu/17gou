/**
 * Created by Administrator on 2015/12/30.
 */
define(['app',
  'html/common/service_user_info',
  'models/model_user',
  'utils/toastUtil',
  'utils/exif',
  'utils/PhotoUtils',
  'html/common/storage'
  ], function(app){
    app.controller('AccountCtrl',
      ['$scope','$state','$ionicHistory','$ionicActionSheet','$ionicLoading','$ionicPopup','userInfo','userModel','ToastUtils','Storage',
        function($scope, $state,$ionicHistory,$ionicActionSheet,$ionicLoading,$ionicPopup,userInfo,userModel,ToastUtils,Storage){
          var sessId; 
          $scope.$on('$ionicView.beforeEnter', function(ev, data) {
            //获取用户信息
            sessId = Storage.get("sessId"); 
            userInfo.requestInfo(); 
           /* var bindphonemsg=Storage.get('bindphonemsg');  
            if(typeof(bindphonemsg)=='undefined' && sessId){
                 bindphone(); 已经在微信注册入口处绑定判断是否绑定手机 
            }*/
            getMyPoint();
             
			    if(ionic.Platform.isWebView() && (ionic.Platform.isIOS() || ionic.Platform.isIPad())){
			            var huixiao = Storage.get('huixiao');
			            if(huixiao && huixiao == 'huixiao') {
			                  $scope.showChongZhi = false;
			            } else {
			                  $scope.showChongZhi = true;
		                      
			            }
			    } else {
			            $scope.showChongZhi = true;
			    }

          })

          function getMyPoint() {
            userModel.getMyPoint(function(xhr, re) {
                if (re.code == 0) {
                    $scope.pointData = re.data || {point:0,total:0,use:0};
                    Storage.set('myPointData_' + sessId, $scope.pointData);
                } else {
                    ToastUtils.showError(re.msg);
                }
            }, function(xhr, re) {
                ToastUtils.showError(re.msg);
            })
          }

               function getPoppup_box(){
                   $ionicPopup.confirm({
                       title: '当前用户未绑定手机，现在就去绑定！',
                       cancelText : '取消',
                       cancelType : 'button-default',
                       okText : '确定',
                       okType : 'button-positive'
                   }).then(function(res) {
                       if(res) {//logout
                           $state.go('BoundPhoneNumber');
                       } else {
                           //cancel logout
                       }
                   });
               }

         function bindphone(){  //此处跟getCurrUserInfo方法获取一致信息。后续解决.
            userModel.getLoginUserInfo(function(response){  
            var code = response.data.code;
            Storage.set("bindphonemsg",'yes');  
            if(code === 0){       
                if(!(/^(13|18|15|14|17)\d{9}$/i.test(response.data.data.phone))){
                    getPoppup_box();
                }  
            }
            },function(response){
              ToastUtils.showError('请检查网络状态！');
            });
           
          }
          /**
		     * 跳转到修改昵称页面
		     */
		    $scope.startToModifyNick = function(){
//		      $state.go('modifyNick');
		    };
          /**
           * 获取当前用户信息
           * @returns {*}
           */
          $scope.getCurrUserInfo = function(){
              return userInfo.getUserInfo(); 
          };


          /**
           * 跳转到个人资料页面
           */
          $scope.startToUserDetail = function(){
            $state.go('userDetail');
          };

          /**
           * 跳转到邀请有礼页面
           */
          $scope.startToInvite = function(){
            $state.go('invite');
          };

          /**
           * 跳转到我的红包页面
           */
          $scope.startToRedPacket = function(){
            $state.go('redPacket');
            //$state.go('editShareOrder');
          };

          /**
           * 跳转到云购记录页面
           */
          $scope.startToMyPartRecord = function(){
            $state.go('myIndianaRecord');
          };

          /**
           * 跳转到我的晒单页面
           */
          // console.log('ddddd'+userInfo.getUserInfo().uid)
          $scope.startToMyShareOrder = function(){
            $state.go('shareOrder',{uid:userInfo.getUserInfo().uid, goodsId:'', pageTitle:'我的晒单'});
          };
          
          /**
           * 跳转到代理管理页面
           */
          $scope.startToMyUserAgency = function(){
            $state.go('userAgency');
          };

          /**
           * 跳转到中奖记录页面
           */
          $scope.startToWinningRecord = function(){
            $state.go('winningRecord');
          };

          $scope.goPre = function(){
            $state.go('tab.trolley');
          };

          $scope.goNews = function(){
            $state.go('myNews');
          };

          $scope.goChongZhi = function(){
            Storage.set('needCheckPaySuccess','noNeed')
            $state.go('chongzhi');
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
