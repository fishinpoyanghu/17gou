/**
 * Created by Administrator on 2015/12/30.
 */
define(['app',
	'html/common/service_user_info',
	'models/model_user',
	'models/model_app',
	'utils/toastUtil',
	'html/common/global_service',
	'utils/exif',
	'utils/PhotoUtils',
	'html/common/storage',

], function(app) {
	app.controller('AccountCtrl2', ['$scope', '$state', '$ionicHistory', '$ionicActionSheet', '$ionicLoading', '$ionicPopup', 'userInfo', 'userModel', 'AppModel','ToastUtils', 'Storage', '$timeout', '$document', '$window','$ionicScrollDelegate',
		function($scope, $state, $ionicHistory, $ionicActionSheet, $ionicLoading, $ionicPopup, userInfo, userModel, AppModel, ToastUtils, Storage, $timeout, $document, $window,  $ionicScrollDelegate) {


//			$scope.fudai.num = 1;
			$scope.luckylists = [];
			
//			开启福袋和购买福袋
			$scope.fudai = {
				num:1,
				buyLuckypacketNum:20,
                maxnum:10       //单次最多开10个
			}

            function bangdingPhone(){
                $timeout(function(){
                    var bdphone=userInfo.getUserInfo().phone;
                    console.log(userInfo.getUserInfo().phone);
                    $scope.showPhone = function(phone) {
                        return(/^(13|18|15|14|17)\d{9}$/i.test(phone))
                    };
                    if(!$scope.showPhone(bdphone)){
                        document.getElementById("account").style.top="44px";
                    }
                    else if($scope.showPhone(bdphone)){
                        document.getElementById("account").style.top="0px";
                    }

                },1000);
            };
            bangdingPhone();

            var sessId;
			$scope.$on('$ionicView.beforeEnter', function(ev, data) {
                $ionicScrollDelegate.scrollTop(true);
				//获取用户信息
				sessId = Storage.get("sessId");
				userInfo.requestInfo();
				/* var bindphonemsg=Storage.get('bindphonemsg');
				 if(typeof(bindphonemsg)=='undefined' && sessId){
				      bindphone(); 已经在微信注册入口处绑定判断是否绑定手机
				 }*/
				getMyPoint();

				if(ionic.Platform.isWebView() && (ionic.Platform.isIOS() || ionic.Platform.isIPad())) {
					var huixiao = Storage.get('huixiao');
					if(huixiao && huixiao == 'huixiao') {
						$scope.showChongZhi = false;
					} else {
						$scope.showChongZhi = true;

					}
				} else {
					$scope.showChongZhi = true;
				}

                bangdingPhone();

			});





			function getMyPoint() {
				userModel.getMyPoint(function(xhr, re) {
					if(re.code == 0) {
						$scope.pointData = re.data || {
							point: 0,
							total: 0,
							use: 0
						};
						Storage.set('myPointData_' + sessId, $scope.pointData);
					} else {
						ToastUtils.showError(re.msg);
					}
				}, function(xhr, re) {
					ToastUtils.showError(re.msg);
				})
			}

			function getPoppup_box() {
				$ionicPopup.confirm({
					title: '当前用户未绑定手机，现在就去绑定！',
					cancelText: '取消',
					cancelType: 'button-default',
					okText: '确定',
					okType: 'button-positive'
				}).then(function(res) {
					if(res) { //logout
						$state.go('BoundPhoneNumber');
					} else {
						//cancel logout
					}
				});
			}

			function bindphone() { //此处跟getCurrUserInfo方法获取一致信息。后续解决.
				userModel.getLoginUserInfo(function(response) {
					var code = response.data.code;
					var data = response.data.data;
					Storage.set("datatype", data.type);
					Storage.set("bindphonemsg", 'yes');
					if(code === 0) {
						if(!(/^(13|18|15|14|17)\d{9}$/i.test(response.data.data.phone))) {
							getPoppup_box();
						}
					}
				}, function(response) {
					ToastUtils.showError('请检查网络状态！');
				});

			}

			/*function lucky_pack(){
			    userModel.getLoginUserInfo(function(response) {
			        var code = response.data.code;
			        var data = response.data.data;
			        if(code === 0) {
			            console.log(data.lucky_packet);
			        }
			    }, function(response) {
			        /!*ToastUtils.showError('请检查网络状态！');*!/
			    });
			}
			lucky_pack();*/
			/**
			 * 跳转到修改昵称页面
			 */
			$scope.startToModifyNick = function() {
				//		      $state.go('modifyNick');
			};

			/**
			 * 获取当前用户信息
			 * @returns {*}
			 */
			//			$scope.getCurrUserInfo();
			$scope.getCurrUserInfo = function() {

				return userInfo.getUserInfo();
			};

			var n = userInfo.getUserInfo()

			/**
			 * 跳转到个人资料页面
			 */
			$scope.startToUserDetail = function() {

				$state.go('userDetail');
			};
            /**
			 * 跳转到绑定手机号页面
			 */
			$scope.startToBoundPhoneNum = function() {

				$state.go('BoundPhoneNumber');
			};



			/**
			 * 跳转到余额详情页面
			 */
			$scope.startToBalanceDetail = function() {
				return;
				$state.go('mybalanceDetail');
			};
			/**
			 * 跳转到邀请有礼页面
			 */
			$scope.startToInvite = function() {
				$state.go('invite');
			};

			/**
			 * 跳转到我的红包页面
			 */
			$scope.startToRedPacket = function() {
				$state.go('redPacket');
				//$state.go('editShareOrder');
			};

			/**
			 * 跳转到云购记录页面
			 */
			$scope.startToMyPartRecord = function() {
				$state.go('myIndianaRecord');
			};

            /**
			 * 跳转到拼团记录页面
			 */
			$scope.startToPintuanOrder = function() {
				$state.go('pintuan_order');
			};

            /**
			 * 跳转到邀请注册页面
			 */
			$scope.startToInviteFriends = function() {
				$state.go('inviteFriends');
			};

            /**
			 * 跳转到购物车页面
			 */
			$scope.startToTrolley = function() {
				$state.go('trolley');
			};
            /**
			 * 跳转到我的收藏页面
			 */
			$scope.startToPintuanCollect = function() {
				$state.go('pintuan_collect');
			};


			/**
			 * 跳转到我的晒单页面
			 */
			$scope.startToMyShareOrder = function() {
				$state.go('shareOrder', {
					uid: userInfo.getUserInfo().uid,
					goodsId: '',
					pageTitle: '我的晒单'
				});
			};

			/**
			 * 跳转到我的公盘页面
			 */
			$scope.go_public_offer = function() {
				/*$state.go('public_offer');*/
				$state.go('public_offer', {}, {
					reload: true
				});
			};

            /**
			 * 跳转到我的公盘页面
			 */
			$scope.go_luckyBag = function() {
				/*$state.go('public_offer');*/
				$state.go('luckyBag', {}, {
					reload: true
				});
			};

			/**
			 * 跳转到中奖记录页面
			 */
			$scope.startToWinningRecord = function() {
				$state.go('winningRecord');
			};

			$scope.goPre = function() {
				$state.go('tab.trolley');
			};

			$scope.goNews = function() {
				$state.go('myNews');
			};

			$scope.goChongZhi = function() {
				Storage.set('needCheckPaySuccess', 'noNeed')
				$state.go('chongzhi');
				al_app_pay();
			};

            /**
             * 跳转到设置页面
             */
            $scope.startToSetting = function() {
                $state.go('setting');
            };



			/*充值*/
			$scope.fudai.buyLuckypacketNum = 20;
			$scope.mousemove = function() {
			}

			$scope.onInputChange = function(me) {

			}
			$scope.select = function(money2) {
				$scope.fudai.buyLuckypacketNum = parseInt(money2);
			}
			//福袋数据 start

			

			//减法的按钮
			$scope.decrease = function() {
				/*$scope.fudai.num--;
				if($scope.fudai.num <= 1) {
					$scope.fudai.num = 1;

				}
				var m = parseInt(userInfo.getUserInfo().lucky_packet);
				if(m <= 0) {
					$scope.fudai.num = 1;
				}*/
			}

			//加法的按钮
			$scope.increase = function() {
				/*$scope.fudai.num++;
				var m = parseInt(userInfo.getUserInfo().lucky_packet);
				if($scope.fudai.num > m) {
					if(m <= 0) {
						$scope.fudai.num = 1;
					} else {
						$scope.fudai.num = Math.floor(userInfo.getUserInfo().lucky_packet);
					}

				} else if(m <= 0) {
					$scope.fudai.num = 1;
				}*/
			}
			//选择数量的按钮
			$scope.choose = function(num) {
				var t = parseInt(num);
//				$scope.fudai.num = t;
				
				var m = parseInt(userInfo.getUserInfo().lucky_packet);
				if(m >= t) {
					if($scope.fudai.num < m) {
						switch(t) {
							case 1:
								$scope.fudai.num = t;
								break;
							case 2:
								$scope.fudai.num = t;
								break;
							case 5:
								$scope.fudai.num = t;
								break;
							case 10:
								$scope.fudai.num = t;
								break;
						}
					}

				}
			}
			//福袋的输入框失去焦点的时候
			$scope.input_LdNum = function(me) {
                if($scope.fudai.num>$scope.fudai.maxnum){
                    $scope.fudai.num=10;
                }else{
                    $scope.choose($scope.fudai.num);
                }

			}

			//弹出开启福袋个数的框
			$scope.open_luckyBag = false;
			$scope.open_fd = function() {
				$scope.fudai.num = 1;
				$scope.fudai.buyLuckypacketNum = 20;
				$timeout(function() {
					$scope.open_luckyBag = true;
					$scope.close_money = false;
				}, 200);
				$timeout(function() {
					//document.getElementById('input_rang').max = userInfo.getUserInfo().lucky_packet;
				}, 300);

			}

			//福袋的立即购买
			$scope.close_money = false;
			$scope.showAlert = function() {
				$scope.close_money = true;
			}
			$scope.closeAlert = function() {
				$scope.close_money = false;
			}
            var gold1=document.getElementById("goldenLight");
            var gold2=document.getElementById("goldenLight2");
            var silvery1=document.getElementById("silveryLight");
            var silvery2=document.getElementById("silveryLight2");
            var ua2 = navigator.userAgent.toLowerCase();
            if (/android/.test(ua2)) {
                console.log("android");
                gold1.style.display="none";
                gold2.style.display="block";
                silvery1.style.display="none";
                silvery2.style.display="block";

            }
            else{
                console.log("pingguo");
                gold2.style.display="none";
                gold1.style.display="block";
                silvery2.style.display="none";
                silvery1.style.display="block";

            }

			//弹出福袋图片
            $scope.display={
                fd_img : false,        //福袋的大图
                red_bag:false,           //红色的袋子
                yellow_paper:false,  //黄色的纸
                luckyimg:false,  //幸运儿
                dt_bg:false,
                bgImgSize:'100%',
                fadeInDown:'',
                golden:false,
                silvery:false
            }
//判断苹果和安卓


          /*  var goldBg=document.getElementById("goldBg");//金币
*/
			$scope.open_fdImg = function() {
				getPacketOrder();
                $scope.display.red_bag=true;
                $timeout(function(){
                    $scope.display.yellow_paper=true;
                    $timeout(function(){
                        $scope.display.red_bag=false;
                    },10);
                },3000);
			}
			//
			//获取开启福袋的数据
			function getPacketOrder() {
				userModel.getPacketOrder($scope.fudai.num, function(reponse, xhr) {
					var code = reponse.data.code;
					var data = reponse.data.data;
					var m = parseInt(userInfo.getUserInfo().lucky_packet);
					if(code == 0) {
						$scope.luckylists = data.list;
						$scope.luckynum = data.luckynum;
                        console.log(data.show);
                        //data.show为1时显示炫酷特效，反之不显示。
                        if(data.show==0){
                            $timeout(function(){
                                goldBg.style.display="block";
                                $timeout(function(){
                                    $scope.display.fadeInDown='fadeInDown';
                                    goldBg.style.display="none";
                                },3000);
                            },5000);

                        }else if(data.show==1){
                            $timeout(function(){
                                $scope.display.silvery=true;
                            }, 320)

                        }
                        else if(data.show==2){
                            $scope.display.golden=true;
                            $timeout(function(){
                                $scope.display.luckyimg=true;
                                $timeout(function(){
                                    $scope.display.luckyimg=false;
                                }, 3000)
                            }, 4000)
                        }

						$timeout(function() {
							$scope.open_luckyBag = false;
							$scope.display.fd_img = true;
						}, 200);
						m -= $scope.fudai.num;
						changeLuckyPacket(m)
						if(userInfo.getUserInfo().lucky_packet < 0) {
							ToastUtils.showError(reponse.data.msg);
						}
					} else {
						ToastUtils.showError(reponse.data.msg);

					}
				}, function() {

				});
			}

			function changeLuckyPacket(m) {
				var target_p = document.querySelectorAll('.balance_account2>.yu_e_zhanghao>.lucky_packet_balance')[0];
				var timer = setInterval(function() {
					target_p.style.top = parseInt(target_p.style.top) + 2 + 'px';
					if(parseInt(target_p.style.top) > 50) {
						target_p.style.top = '0px';
						clearInterval(timer);
					}
					userInfo.getUserInfo().lucky_packet = m;
				}, 10)
			}
			$scope.order_num;

			/*充值的数据*/
			function al_app_pay() {
				AppModel.al_app_pay($scope.order_num, function(reponse, xhr) {

					var code = reponse.data.code;

					if(code == 0) {
						var data = reponse.data.data;

					} else {
						ToastUtils.showError(reponse.data.msg);

					}
				}, function() {

				}, function() {

				});
			}
			al_app_pay();
			//点击“X”关闭
			$scope.close_fdImg = function() {
				$timeout(function() {
					$scope.display.fd_img = false;
                    $scope.display.yellow_paper=false;
					$scope.close_money = false;
					$scope.display.golden = false;
					$scope.display.silvery = false;
				}, 200);
			}
			$scope.close_fdkj = function() {
				$timeout(function() {
					$scope.open_luckyBag = false;
					$scope.close_money = false;
				}, 200);
			}


			//福袋数据 end

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
			$scope.startToPay = function() {
				$scope.open_luckyBag = false;
				$scope.close_money = false;
				$timeout(function() {
					var commitData = {
						activity_id: -1,
						goods_title: 'fudai',
						activity_type: '0',
						need_num: $scope.fudai.buyLuckypacketNum,
						join_number: $scope.fudai.buyLuckypacketNum,
						num: $scope.fudai.buyLuckypacketNum,
						orderType: 6,
						remain_num: $scope.fudai.buyLuckypacketNum
					}
					Storage.set('commitData', [commitData])
					$state.go('pay');
				}, 10)

			};

			$scope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
				$scope.open_luckyBag = false;
				$scope.close_money = false;
			});

            /*金币雨 start*/
            /*var myCanvas = document.getElementById('goldCanvas');
            var cxt = myCanvas.getContext('2d');
            var snowcount = 30; /// 显示数量
            var index = 0;

            imageList = {}; /// 图片列表
            imageSource = ['', 'img/account/jb_03.png']; /// 图片源文件类表
            var snowlist = [];


            /// 初始化
            function init() {
                /// 画布

                // 加载图片
                loadImages();
            }

            /// 加载图片
            function loadImages() {
                for (var i in imageSource) {
                    imageList[i] = new Image();
                    imageList[i].src = imageSource[i];

                    imageList[i].onload = function () {
                        index++;
                        if (imageSource.length >= index++) {
                            callImages();
                        }
                    }
                }
            }

            function callImages() {
                for (var i = 0; i < snowcount; i++) {
                    snowlist.push(new gold());
                }

                setInterval(function () {
                    cxt.clearRect(0, 0, myCanvas.width, myCanvas.height);
                    fillBackground();

                    for (var i in snowlist) {
                        snowlist[i].Go();

                        if (snowlist[i].Y >= myCanvas.height) {
                            snowlist[i] = new gold();
                        }
                    }
                }, 1);
            }

            function fillBackground() {
                cxt.drawImage(imageList[0], 0, 0, imageList[0].width, imageList[0].height, 0,0, myCanvas.width, myCanvas.height);
            }

            ///金币
            function gold()
            {
                this.X = parseInt(Math.random() * myCanvas.width);
                this.Y = 0;
                this.Size = parseInt(Math.random() * 71);
                this.Transparency = 10;
                this.Speed = parseInt(Math.random()*(1-10+1)+10);
            }

            /// 动起来
            gold.prototype.Go = function () {
                this.Y  = this.Y + this.Speed;
                cxt.drawImage(imageList[1], 0, 0, imageList[1].width, imageList[1].height, this.X, this.Y , imageList[1].width - this.Size , imageList[1].height - this.Size);
            }

            init();*/
            /*金币雨 end*/




		}
	]);

});