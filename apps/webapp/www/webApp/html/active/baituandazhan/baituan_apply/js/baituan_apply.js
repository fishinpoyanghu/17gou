/**
 * Created by songmars on 15/12/29.
 */

define(
  [
    'app',
    'components/view-progress/view_progress',
    'components/view-countdown/view_countdown',
    'components/view-buy-footer/view_buy_footer',
    'components/view-buy-number-pop/view_buy_number_pop',
    'models/model_goods',
    'models/model_pintuan',
    'models/model_address',
    'html/common/service_user_info',
    'html/common/geturl_service',
    'html/common/global_service',
    'html/thirdParty/thirdparty_wechat_js'
  ],
  function(app) {
    "use strict";

    app.controller('baituanApplyCtrl', baituanApplyCtrl);
      baituanApplyCtrl.$inject = ['$scope', '$state', '$stateParams','$ionicPopup', 'GoodsModel','PintuanModel','addressModel', 'MyUrl', 'ToastUtils', 'userInfo','Global','weChatJs','$ionicHistory'];
    function baituanApplyCtrl($scope, $state, $stateParams,$ionicPopup, GoodsModel,PintuanModel, addressModel,MyUrl, ToastUtils, userInfo,Global,weChatJs,$ionicHistory) {
      (function init() {
        $scope.isLogin = MyUrl.isLogin();
//      请求的商品期数,暂时定位13
        $scope.activityId = parseInt($stateParams.activityId);
        $scope.addressList = [];//收货地址列表
        $scope.displayAddressList = [];  //显示在页面的地址
    	$scope.isLoadFinished = false ;//是否加载结束
        $scope.refresh = refresh;
        refresh();
      })();
      $scope.$on('$ionicView.enter',function(){
      	 getAddressList()
         if(!$ionicHistory.backView() && !Global.isInAPP()) {
            $scope.firstInIsGoodsPage = true;
         } else {
            $scope.firstInIsGoodsPage = false;
         }
      });

      $scope.gotoMainPage = function() {
        $state.go('tab.mainpage')
      }


      function refresh() {
        ToastUtils.showLoading('加载中...');
//      getAddressList()
       PintuanModel.baituan_getGoodsDetail_info($scope.activityId, onSuccess, onFailed, onFinal);
      }
      
      //获取地址信息
//    getAddressList()
		function getAddressList(){
	      addressModel.getAddressList(function(response){
	        //onSuccess
	        isConnect = true ;
	        var code = response.data.code;
	        var msg = response.data.msg;
	        $scope.isLoadFinished = true ;
	        switch(code){
	          case 0:
	            $scope.addressList = response.data.data;
	            $scope.isDisplayAddress();
	            break ;
	          case 6:
	            ToastUtils.showWarning(msg);
	            $state.go('login');
	            break ;
	          default :
	            ToastUtils.showError(msg);
	            break;
	        }
	      },function(response){
	        //onFail
	        isConnect = false ;
	        ToastUtils.showError('请检查网络状态');
	      },function(){
	        //onFinal
	        $scope.isLoadFinished = true ;
	         ToastUtils.hideLoading()
	      });
			
//			去支付页面,暂时不用，原来外包做的
			$scope.goToPay = function() {
                    switchPayWay();
                    saveCache();
                    if ($scope.pay.type == '-1') {
                        getNoPay();
                    } else {
                        getThirdPay();
                    }
                };
			
//			去支付页面,假支付,实际上是申请
			$scope.gotobaituanPayInfo = function(){
				if (!$scope.addressList[0]) {
					ToastUtils.showError('请添加地址后再申请');
					return ;
				}
				PintuanModel.baituan_createTuan($scope.activityId,$scope.displayAddressList[0].address_id, onSuccess, onFailed)
				function onSuccess(response,data){
					if(data.code == 0) {
						$scope.baituan_team = data.data.team;
						baituan_alert()
					}else{
						ToastUtils.showError(data.msg);
						$state.go('baituandazhan');
					}
	//			支付页面弹出内容
					function baituan_alert(){
					     var alertPopup = $ionicPopup.alert({
					       template: '<h1 id="baituan_apply_result">申请成功</h1>'
					       			  +'<p id="baituan_apply_content">恭喜成为亿七购主持人快点邀请好友来百团大战获得商品！</p>',
	//				       			  +'<button ng-click=""  class="baituan_apply_sure">确定</button>',
					        buttons:[{
					        	text:'确定',
					        	type:'baituan_apply_sure'
					        }]
					     });
					     alertPopup.then(function(res) {
					     	$scope.gotoBaituanMember();
					     });
					setTimeout(function(){
						var pop = document.getElementById('baituan_apply_result').parentElement.parentElement;
						var popHeader = pop.getElementsByClassName('popup-head')[0];
						var popbutton = pop.getElementsByClassName('popup-buttons')[0];
						var btnSure = popbutton.getElementsByClassName('baituan_apply_sure')[0];
						popbutton.style.border = 'none';
						popbutton.style.fontSize = '16px';
						popbutton.style.width = '85px';
						popbutton.style.margin = '0 auto';
						popHeader.style.display = 'none';
						btnSure.style.border = '1px solid #3389FB';
						btnSure.style.color = '#3389FB';
						btnSure.style.borderRadius = '4px';
					},300)
					}
				}
				function onFailed(response){
					console.log(response)
				}
			}
			
			//申请成功后进入成员管理界面
			$scope.gotoBaituanMember = function(){
				$state.go('baituan_member',{team: $scope.baituan_team});
			}
			
//			end 支付页面
			/**
		     * 跳转到新增收货地址页面
		     */
		    $scope.startToAddressAdd = function(){
		      $state.go('addressAdd');
		    };
	
	      /**
	       * 确认在页面显示地址
	       * @returns {boolean}
	       */
	      $scope.isDisplayAddress = function(){
	        $scope.displayAdress = $scope.addressList.some(diplayAddress);
	        function diplayAddress(address){
	        	return address.is_default;
	        }
	        if ($scope.displayAdress) {
	        	$scope.displayAddressList = $scope.addressList;
	        }else{
	        	$scope.displayAddressList[0] = $scope.addressList[0];
	        }
	      };
	      
	      /**
	       * 数据是否为空
	       * @returns {boolean}
	       */
	      $scope.isDataEmpty = function(){
	        var isEmpty = true ;
	        if($scope.addressList.length>0){
	          isEmpty = false ;
	        }
	        return isEmpty ;
	      };
	
	      var isConnect = true ;//网络是否连接
	      /**
	       * 显示断网页面
	       * @returns {boolean}
	       */
	      $scope.isShowDisconnect = function(){
	        return $scope.isDataEmpty() && $scope.isLoadFinished && !isConnect ;
	      };
		
		
	
	    }
		
//		跳转到修改地址页面
		$scope.startToAddressUpdate = function(addr){
	      $state.go('addressUpdate',{address:addr});
	    };
	    
	    //商品获取之后的回调
		function onSuccess(response, data) {
        if (data.code == 0) {
          $scope.broad = data.data;
        } else {
          ToastUtils.showError(data.msg);
        }
      }

      function onFailed(response) {
        if (response.status !== 200) {
          ToastUtils.showError('请检查网络');
        }
      }

      function onFinal() {
        $scope.$broadcast('scroll.refreshComplete');
        ToastUtils.hideLoading()
      }
	
		
    }
  });


