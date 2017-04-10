
define(function(require) {

  var app = require('app');
  app.directive('viewTrolleyItem',
    [
      function () {


        return {
          restrict: 'E',
          scope:{
            goods:'=data',
            allgoods:'=datalist'

          },
          templateUrl: function (elem, attr) {
            var path = "webApp/components/view-trolley/";
            var fileName = "view_trolley_" + attr.type + ".html";
            return path + fileName;
          },
          controller:['$scope','$attrs','$state','$ionicPopup',function($scope,$attrs,$state,$ionicPopup){


						if ($scope.goods.activity_type == 6) {
							$scope.selectedRow = $scope.goods.hot_luckyBuy;
						}

            function getNumberList(){

	            for(var index in $scope.allgoods){
	              console.log('id'+index+'join_number');
	              var myNumber=document.getElementById(index+'join_number').value;
	              $scope.allgoods.join_number.push(myNumber);
	            }

            }


            $scope.editJoinNumber = function (goods) {
              var unit = (goods.activity_type==2) ? 10 : 1;
              var joinNumber = goods.join_number;
              var remainNumber = goods.remain_num;
              if(goods.activity_type==3 && goods.remain_num > 10) {
                remainNumber = 10;
              }

                /*我添加商品类型为4时，购物次数为2*/
               /* else if(goods.activity_type==4) {
                  if(goods.remain_num > 2){
                      $scope.goods.join_number=$scope.goods.need_num;
                  }
                }*/
              if(joinNumber==''){
	        					$scope.goods.join_number = unit;
                return
              }
              var match = joinNumber.match(/^[1-9]\d*/);
              if(match==null) {
                joinNumber = unit;
              }else {
                joinNumber = parseInt(match[0]);
                joinNumber = joinNumber<=0 ? unit : joinNumber;
                joinNumber = joinNumber>remainNumber ? remainNumber : joinNumber;
              }
              joinNumber = joinNumber - joinNumber % unit;
              joinNumber = joinNumber<=0 ? unit : joinNumber;
              $scope.goods.join_number = joinNumber;
            }



            $scope.addNumber=function(i){
            	console.log(i)
                /*添加被选中就添加背景色+字色*/
               if ($scope.goods.activity_type !=4 ) {
                	$scope.selectedRow = i;
               }else{
               		if($scope.goods.remain_num < $scope.goods.need_num){
               			 $ionicPopup.alert({
			                  title: '温馨提示',
			                  template: '<div style="text-align:center">本次商品只剩下一次购买机会<div>',
			                  okText: '确定',
			              }).then(function (res) {
			                  /*console.log('Thank you for not eating my delicious ice cream cone');*/
			              });
               		}else{
               			$scope.selectedRow = i;
               		}
               }
                 /*yu添加$scope.goods.activity_type==6),*/
              if($scope.goods.activity_type==6){
                    $scope.goods.hot_luckyBuy = 2;
              }
              getNumberList();

              if($scope.goods.join_number<$scope.goods.remain_num){

                if($scope.goods.join_number==''){
                  $scope.goods.join_number=0;

                }

                if($scope.goods.activity_type==1){

                  $scope.goods.join_number++;
                } else if($scope.goods.activity_type==3) {
                  if($scope.goods.join_number < 10) {
                    $scope.goods.join_number++;
                  }
                } /*我添加$scope.goods.activity_type==2)*/else if($scope.goods.activity_type==2){

                  $scope.goods.join_number=$scope.goods.join_number- $scope.goods.join_number%10;

                  $scope.goods.join_number+=10;

                }
                /*我添加$scope.goods.activity_type==4),每次点击加功能仍然是2*/
                else if($scope.goods.activity_type==4){
                    $scope.goods.join_number=$scope.goods.need_num;
                }
                 /*yu添加$scope.goods.activity_type==7),*/
                else if($scope.goods.activity_type==7){
                    $scope.goods.join_number++;
                }
                
              }


            };
            $scope.minusNumber=function(i){
            	console.log(i)
                /*添加被选中就添加背景色+字色*/
                $scope.selectedRow = i;
                if ($scope.goods.activity_type==2){
                  if($scope.goods.join_number>10){
                    $scope.goods.join_number=$scope.goods.join_number- $scope.goods.join_number%10;

                    $scope.goods.join_number-=10
                  }

                } else if($scope.goods.activity_type==1 || $scope.goods.activity_type==7){
                  if($scope.goods.join_number>1){
                    $scope.goods.join_number--;
                  }

                }
                /*我添加二人云购*/
                else if($scope.goods.activity_type==4){
                    $scope.goods.join_number=$scope.goods.need_num/2;
                }
                
                /*yu添加幸运购*/
                else if($scope.goods.activity_type==6){
                     $scope.goods.hot_luckyBuy = 1;
                }
            };

            $scope.chooseGoods=function(goods){
              if(goods.isSelected){
                goods.isSelected=false;
                //发送选中通知
                $scope.$emit('reduceSelectNumber',$scope.goods.activity_id);

              }else{
                goods.isSelected=true;
                //发送取消选中通知
                $scope.$emit('addSelectNumber',$scope.goods.activity_id);
              }
            };

            $scope.goToDetail=function(activity_id){

              $state.go('activity-goodsDetail', {activityId:activity_id});
            	console.log('activity_id'+activity_id)
            };



            //var alertPopup = $ionicPopup.alert({
            //  template: '你的晒单已提交，三天内若审核通过，既可获得最高为10元的随机红包'
            //});




          }]
        }
      }]);
});
