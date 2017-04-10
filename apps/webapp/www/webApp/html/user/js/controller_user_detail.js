define([
	'app',
	'html/common/service_user_info',
	'models/model_user',
	'utils/toastUtil',
	'utils/exif',
	'utils/PhotoUtils'
], function(app) {

	app.controller('UserDetailCtrl', ['$scope', '$state', '$ionicHistory', '$stateParams', '$ionicActionSheet', '$ionicLoading', 'userInfo', 'userModel', 'ToastUtils',
		function($scope, $state, $ionicHistory, $stateParams, $ionicActionSheet, $ionicLoading, userInfo, userModel, ToastUtils) {

			$scope.getCurrUserInfo = function() {
				return userInfo.getUserInfo();
			};

			$scope.showPhone = function(phone) {
				return(/^(13|18|15|14|17)\d{9}$/i.test(phone))
			}

			/**
			 * 跳转到修改昵称页面
			 */
			$scope.startToModifyNick = function() {
				//    $state.go('modifyNick');
			};

			$scope.goBack = function() {
				$state.go('tab.account2');
				//					$ionicHistory.goBack();
			};

			/**
			 * 跳转到收货地址页面
			 */
			$scope.startToShippingAddress = function() {
				$state.go('addressList');
			};

			/**
			 * 跳转到绑定手机页面
			 */
			$scope.bindphpone = function() {
				$state.go('BoundPhoneNumber');
			};
			/**
			 * 跳转到设置页面
			 */
			$scope.startToSetting = function() {
				$state.go('setting');
			};

			/**
			 * 显示图片选择页面
			 */
			$scope.showImageSelector = function() {
				if(navigator.camera) { //移动端
					$ionicActionSheet.show({
						titleText: '更换头像',
						cancelText: '取消',
						buttons: [{
							text: '拍照'
						}, {
							text: '从相册中选取'
						}],
						cancel: function() {
							// add cancel code..
						},
						buttonClicked: function(index) {
							switch(index) {
								case 1: //选择本地图片
									// PhotoUtils.getLocalPictureByApp(true,function(imageData){
									//   showUploadingDialog();
									//   userModel.updateHeadIcon(imageData,uploadSuccess,uploadFail,null);
									// },function(errMsg){
									//   //获取图片失败
									//   ToastUtils.showError(errMsg);
									// });
									PhotoUtils.takePictureByHtml5(function(imageData) {
										showUploadingDialog();
										userModel.updateHeadIcon(imageData, uploadSuccess, uploadFail, null);
									}, function(errMsg) {
										//获取图片失败
										ToastUtils.showError(errMsg);
									});
									break;
								case 0:
								default: //拍照
									PhotoUtils.takePhotoByApp(true, function(imageData) {
										showUploadingDialog();
										userModel.updateHeadIcon(imageData, uploadSuccess, uploadFail, null);
									}, function(errMsg) {
										//获取图片失败
										ToastUtils.showError(errMsg);
									});
									break;
							}
							return true;
						}
					});

				} else { //浏览器
					PhotoUtils.takePictureByHtml5(function(imageData) {
						showUploadingDialog();
						userModel.updateHeadIcon(imageData, uploadSuccess, uploadFail, null);
					}, function(errMsg) {
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
			function uploadSuccess(response, data, status, headers, config, statusText) {
				if(data.code === 0) {
					hideUploadingDialog();
					userInfo.updateHeadIcon(data.data.icon);
				} else {
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
			function uploadFail(response, data, status, headers, config, statusText) {
				hideUploadingDialog();
			}

			function showUploadingDialog() {
				$ionicLoading.show({
					template: '上传中...' + '<ion-spinner icon="android"></ion-spinner>',
					noBackdrop: true
				});
			}

			function hideUploadingDialog() {
				$ionicLoading.hide();
			}

		}
	]);

});