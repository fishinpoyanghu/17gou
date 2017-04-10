/**
 * Created by suiman on 16/1/22.
 */

//弹窗类型
var POP_TYPE_CART = 0;   //加入购物车
var POP_TYPE_BUY = 1;    //立即购买
var POP_TYPE_ADD = 2;    //追加云购
var POP_TYPE_OTHERS = 3; //其他

define(['app','html/common/storage'], function (app) {
  app.directive('viewBuyNaturePop', viewBuyNaturePop);

  viewBuyNaturePop.$inject = ['trolleyInfo', '$state','$ionicPopup', 'ToastUtils','Storage','$timeout'];

  function viewBuyNaturePop(trolleyInfo, $state, $ionicPopup,ToastUtils,Storage,$timeout) {
    return {
      restrict: 'E',
      templateUrl: 'webApp/components/view-buy-nature-pop/view_buy_nature_pop.html',
      scope: {
        headTitle: '@',
        buttonText: '='
      },
      link: function postLink(scope, elem, attrs) {
      	scope.isShowAddPay = false;
      	scope.goodsInfo = {};
      	scope.choiceNature = [];
        scope.$on('view-buy-nature-pop.show', function (event, goodsInfo) {
        	console.log(goodsInfo)
        	scope.goodsInfo = goodsInfo;
			scope.natures = goodsInfo.natures;
			angular.forEach(scope.natures,function(data,i,arr){
				scope.choiceNature[i] = {};
				scope.choiceNature[i].name = data.name;
			})
        	
        	scope.showNaturePan(goodsInfo)
        });
        scope.$on('view-buy-nature-pop.hide', function (event) {
          scope.hideNaturePan();
        });
		scope.showNaturePan = function(goodsInfo){
			scope.isShowAddPay = true;
		}
		scope.hideNaturePan = function(){
			scope.isShowAddPay = false;
		}
		scope.choiceWhice = function(me){
			console.log(me)
//			scope.choiceNature[me.$parent.$index] = {};
//			scope.choiceNature[me.$parent.$index].name = me.$parent.nature.name;
			if (scope.choiceNature[me.$parent.$index].value==me.$parent.nature.values[me.$index]) {
				scope.choiceNature[me.$parent.$index].value='';
			}else{
				scope.choiceNature[me.$parent.$index].value = me.$parent.nature.values[me.$index];
			}
			
			console.log(scope.choiceNature)
//			console.log(me.className)
		}
      }
    }
  }
})




