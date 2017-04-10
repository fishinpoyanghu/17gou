/**
 * Created by suiman on 16/1/22.
 */

//弹窗类型
var POP_TYPE_CART = 0;   //加入购物车
var POP_TYPE_BUY = 1;    //立即购买
var POP_TYPE_ADD = 2;    //追加云购
var POP_TYPE_OTHERS = 3; //其他

define(['app','html/common/storage'], function (app) {
  app.directive('viewBuyPop', viewBuyPop);

  viewBuyPop.$inject = ['trolleyInfo', '$state','$ionicPopup', 'ToastUtils','Storage','$timeout'];

  function viewBuyPop(trolleyInfo, $state, $ionicPopup,ToastUtils,Storage,$timeout) {
    return {
      restrict: 'E',
      templateUrl: 'webApp/components/view-buy-pop/view_buy_pop.html',
      scope: {
        headTitle: '@',
        buttonText: '='
      },
      link: function postLink(scope, elem, attrs) {
        scope.isShowAddPay = false;
        scope.good_type4 = false;           //商品类型为4时，good_type4为真就显示，good_type4为假就不显示。
        scope.good_type13 = false;          //商品类型为1~3时，good_type13为真就显示，good_type4为假就不显示。
        var unit;
        var buttonType;

        scope.$on('view-buy-pop.show', function (event, goodsInfo) {
          scope.showAddPay(goodsInfo);
        });
        scope.$on('view-buy-pop.hide', function (event) {
          scope.closeAddPay();
        });

        /*根据按钮的内容，判断弹窗的类型*/
        function getPopType(text) {
          if (text == '加入购物车') {
            return POP_TYPE_CART;
          } else if (text == '立即购买') {
            return POP_TYPE_BUY;
          } else if (text == '追加购买') {
            return POP_TYPE_ADD;
          } else {
            return POP_TYPE_OTHERS;
          }
        }

        scope.getButtonClass = function () {
          buttonType = getPopType(scope.buttonText);
          if (buttonType == POP_TYPE_CART) {
            return 'dp-button--yellow'
          } else {
            return 'dp-button--red'
          }
        }

        //追加云购
        scope.addPay = {
          activity_id: '',
          activity_type: '',
          need_num: '',
          remain_num: '',
          goods_img: '',
          goods_title: '',
          isSelected: false,
          join_number: '',
        };

          /*添加提示框*/
          /*function getDecrease_box() {
              $ionicPopup.alert({
                  title: '温馨提示',
                  template: '买家，实在太抱歉！产品用完',
                  okText: '取消',
                  okType: '取消',
              }).then(function (res) {
                  /!*console.log('Thank you for not eating my delicious ice cream cone');*!/
              });
          }*/
          function getIncrease_box() {
              $ionicPopup.alert({
                  title: '温馨提示',
                  template: '买家，实在太抱歉！产品只有一半，您可以点击“一半”按钮',
                  okText: '取消',
                  okType: '取消',
              }).then(function (res) {
                  /*console.log('Thank you for not eating my delicious ice cream cone');*/
              });
          }

        /**
         * 显示追加页面
         * @param goodsInfo
         */
        scope.showAddPay = function (goodsInfo) {
          scope.isShowAddPay = true;
          scope.addPay.activity_id = goodsInfo.activity_id;
          scope.addPay.activity_type = goodsInfo.activity_type;
          scope.addPay.need_num = goodsInfo.need_num;
          scope.addPay.remain_num = goodsInfo.remain_num;
          if(angular.isArray(goodsInfo.goods_img) && goodsInfo.goods_img.length>0) {
            scope.addPay.goods_img = goodsInfo.goods_img[0];
          } else {
            scope.addPay.goods_img = goodsInfo.goods_img;
          }
          scope.addPay.goods_title = goodsInfo.goods_title;
          if (scope.addPay.activity_type == 1) {//非限购专区
              scope.good_type13 = true;
              scope.good_type4=false;
            scope.addPay.join_number = 1;
            unit = 1;
          } else if (scope.addPay.activity_type == 2) { //十元
              scope.good_type13 = true;
              scope.good_type4=false;
            scope.addPay.join_number = 10;
            unit = 10;
          } else if(scope.addPay.activity_type == 3) { //限购专区
              scope.good_type13 = true;
              scope.good_type4=false;
            scope.addPay.join_number = 1;
            unit = 1;
          } else if(scope.addPay.activity_type == 4) { //二人云购
              scope.good_type4=true;
              scope.good_type13 = false;
              /*console.log(scope.addPay.remain_num);
              scope.addPay.remain_num=(scope.addPay.remain_num/scope.addPay.remain_num)*2;
              scope.addPay.need_num=(scope.addPay.need_num/scope.addPay.need_num)*2;*/
              scope.addPay.join_number = scope.addPay.need_num/2;
              unit = scope.addPay.need_num/2;
          }
        };

        /**
         * 关闭追加页面
         */
        scope.closeAddPay = function () {
          scope.isShowAddPay = false;
        };


		scope.xingyungou = function(){
			var myPopup = $ionicPopup.show({
		     template: '',
		     title: '请选择奇偶幸运牌',
		     buttons: [
		       { text: '奇数幸运牌' ,
		       	 onTap: function(e) {
		           alert('奇数幸运牌')
//					scope.startToPay()
		         }
		       },
		       {
		         text: '<b>偶数幸运牌</b>',
//		         type: 'button-positive',
		         onTap: function(e) {
		           alert('偶数幸运牌')
//					scope.startToPay()
		         }
		       },
		     ]
		   });
		   myPopup.then(function(res) {
		     console.log('Tapped!', res);
		   });
		   $timeout(function() {
		      myPopup.close(); //由于某种原因3秒后关闭弹出
		   }, 3000000);
		}
		
		
        /**
         * 追加云购并跳转到购物车页面
         */
        scope.startToPay = function (num) {
        	var parent_invite_code = '';
        	console.log(window.localStorage.getItem('parent_invite_code'))
          	$timeout(function() {
              num= scope.addPay.join_number = validJoinNum(scope.addPay.join_number);

            if (buttonType == POP_TYPE_BUY || buttonType == POP_TYPE_OTHERS || buttonType == POP_TYPE_ADD) {
            	var commitData = {
	            		activity_id:scope.addPay.activity_id,
		                goods_title:scope.addPay.goods_title,
		                activity_type:scope.addPay.activity_type,
		                need_num:scope.addPay.need_num,
		                join_number:scope.addPay.join_number,
		                num:scope.addPay.join_number,
		                remain_num:scope.addPay.remain_num
	            	}
        	if (window.localStorage.getItem('parent_invite_code')) {
					var parent_invite_code = window.localStorage.getItem('parent_invite_code');
					commitData.parent_invite_code = parent_invite_code;
				}
				Storage.set('commitData',[commitData])
//              Storage.set('commitData',[{
//              activity_id:scope.addPay.activity_id,
//              goods_title:scope.addPay.goods_title,
//              activity_type:scope.addPay.activity_type,
//              need_num:scope.addPay.need_num,
//              join_number:scope.addPay.join_number,
//              num:scope.addPay.join_number,
//              parent_invite_code: parent_invite_code,
//              remain_num:scope.addPay.remain_num
//            }]);
              $state.go('pay');
            }
              if(buttonType == POP_TYPE_CART ) {
              var goodsItem = angular.merge({},scope.addPay);
              trolleyInfo.addGoodsItem(goodsItem);

              scope.closeAddPay();
              ToastUtils.showSuccess('已加入购物车中');
            }
          },10)
          
        };


        /**
         * 追加页面-
         */
        scope.decrease = function () {
          scope.addPay.join_number = scope.addPay.join_number - scope.addPay.join_number % unit;
          scope.addPay.join_number = scope.addPay.join_number - unit;
          if (scope.addPay.join_number <= 0) {
            scope.addPay.join_number = unit;
          }

            /*点击“一半”按钮，若剩余人次为0就弹窗*/
           /* if(scope.addPay.activity_type==4){
                if(scope.addPay.remain_num==0){
                    if(scope.addPay.join_number>scope.addPay.remain_num)
                        getDecrease_box();
                }
            }*/
            if(scope.addPay.activity_type==4){
                scope.addPay.join_number=scope.addPay.need_num/2;
            }

        };

        /**
         * 追加页面+
         */
        scope.increase = function (addNum) {
          if(addNum) {
            if(addNum == 'remain_num') {
              scope.addPay.join_number = scope.addPay.join_number + scope.addPay.remain_num;
            } else {
              scope.addPay.join_number = scope.addPay.join_number + Number(addNum);
            }
          }
          else{
            scope.addPay.join_number = scope.addPay.join_number + unit;
          }
          scope.addPay.join_number = scope.addPay.join_number - scope.addPay.join_number % unit;
          if (scope.addPay.join_number >= scope.addPay.remain_num) {
            scope.addPay.join_number = scope.addPay.remain_num;

          }

          if(scope.addPay.activity_type == 3 && scope.addPay.join_number > 10) scope.addPay.join_number = 10;

            /*if(scope.addPay.activity_type == 4 && scope.addPay.join_number >= 2){
                scope.addPay.join_number = scope.addPay.join_number - scope.addPay.join_number % unit;
                scope.addPay.join_number = 2;
            }*/

            /*点击“全部”按钮，剩余人次不够全部的话*/
            if(scope.addPay.activity_type==4 && scope.addPay.need_num>scope.addPay.remain_num){
                getIncrease_box();
            }
            console.log(scope.addPay.join_number);
        };

        scope.editJoinNumber = function (addPay) {
          var joinNumber = '' + addPay.join_number;
          if (joinNumber == '') {
            scope.addPay.join_number = unit;
            return;
          }
          var remainNumber = scope.addPay.remain_num;
          if(addPay.activity_type==3 && addPay.remain_num > 10) {
                remainNumber = 10;
          }
          if(addPay.activity_type==3 && addPay.remain_num > 10) {
                remainNumber = 10;
          }
            /*if(addPay.activity_type==4 && addPay.remain_num > 2) {
                remainNumber = 2;
            }*/
          var match = joinNumber.match(/^[1-9]\d*/);
          if (match == null) {
            joinNumber = unit;
          } else {
            joinNumber = parseInt(match[0]);
            joinNumber = joinNumber > remainNumber ? remainNumber : joinNumber;
            joinNumber = joinNumber <= 0 ? unit : joinNumber;
          }
          joinNumber = joinNumber - joinNumber % unit;
          joinNumber = joinNumber <= 0 ? unit : joinNumber;
          scope.addPay.join_number = joinNumber;
        };

        function validJoinNum(num) {
          if (num == '' || num < unit) {
            return unit;
          } else {
              /*if(scope.addPay.activity_type==4) {
                  if(num==1){
                      num=scope.addPay.need_num/2;
                  }else if(num==2){
                      num=scope.addPay.need_num;
                  }
              }*/
              return num;
          }
        }
      }
    }
  }
})




