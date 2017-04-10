
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
          controller:['$scope','$attrs','$state',function($scope,$attrs,$state){


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
                /*添加被选中就添加背景色+字色*/
                $scope.selectedRow = i;
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
              }


            };
            $scope.minusNumber=function(i){
                /*添加被选中就添加背景色+字色*/
                $scope.selectedRow = i;
                if ($scope.goods.activity_type==2){
                  if($scope.goods.join_number>10){
                    $scope.goods.join_number=$scope.goods.join_number- $scope.goods.join_number%10;

                    $scope.goods.join_number-=10
                  }

                } else if($scope.goods.activity_type==1){
                  if($scope.goods.join_number>1){
                    $scope.goods.join_number--;
                  }

                }
                /*我添加二人云购*/
                else if($scope.goods.activity_type==4){
                    $scope.goods.join_number=$scope.goods.need_num/2;
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
