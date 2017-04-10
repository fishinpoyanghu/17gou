/**
 * Created by songmars on 15/12/29.
 */

define(
  [
    'app',
    'html/trolley/trolley_service',
    'components/view-trolley/view_trolley_list'
    ,'utils/toastUtil',
    'components/view-recomend/view_recomend_list',
    'models/model_activity',
    'html/common/storage',
    'models/model_app',
  ],
  function(app){
    'use strict';
    app.controller('TrolleyCtrl',['$rootScope','$scope','$state','trolleyInfo','$ionicPopup','$location','ToastUtils','ActivityModel','$ionicHistory','Storage','AppModel',
      function($rootScope,$scope,$state,trolleyInfo,$ionicPopup,$location,ToastUtils,ActivityModel,$ionicHistory,Storage,AppModel){
      $scope.deleteList=[];
      $scope.isSelectedAll=false;
      $scope.selectNumber=0;
      
      $scope.allgoods=trolleyInfo.getGoodsInfo();//for test
      $scope.$on('changtoedit', changeToEdit);
      $scope.$on('changtocount', changeToCount);
      $scope.$on('reduceSelectNumber', reduceSelectNumber);
      $scope.$on('addSelectNumber', addSelectNumber);
      $scope.$on('$stateChangeStart',function(event, toState, toParams, fromState, fromParams){
          $scope.cancallEdit();
      }
      );
      $scope.$on('$ionicView.beforeEnter',function(){
          Storage.set('payVisit',false)
          Storage.remove('payCommitData');
      });

      $scope.isLoadFinished = true;
      function getRecommendData() {
        if(!$scope.isLoadFinished) return;
         $scope.isLoadFinished = false;
        ActivityModel.getActivityList_1('weight', 'asc','',1,10, function(xhr, re) {
            var code = re.code;
            if (code == 0) {
              $scope.recommendGoodsList = re.data;
            } else {
                ToastUtils.showMsgWithCode(code, re.msg);
            }
        }, function(response, data) {
            ToastUtils.showMsgWithCode(7, '获取推荐列表失败：' + '状态码：' + response.status);
        }, function() {
           $scope.isLoadFinished = true;
        })
      }
      function changeToEdit(){
        if($state.current.name == 'tab.trolley') {
          var goodsList=document.getElementById('goodslist');
        } else {
          var goodsList=document.getElementById('goodslist_1');
        }
        goodsList.className=('dp-itemList dp-itemList--editing');
      }
      function changeToCount(){
        if($state.current.name == 'tab.trolley') {
          var goodsList=document.getElementById('goodslist');
        } else {
          var goodsList=document.getElementById('goodslist_1');
        }
        if(goodsList) goodsList.className=('dp-itemList');
      }
      function reduceSelectNumber(event,data){
        $scope.selectNumber--;

        changeChooseAll();
        removeToDeleteList(data);

      }
      function addSelectNumber(event,data){
        $scope.selectNumber++;
        changeChooseAll();
        addToDeleteList(data);

      }
      //添加到删除列表
      function addToDeleteList(data){

        for( var index in $scope.allgoods){
          if(data==$scope.allgoods[index].activity_id){
            $scope.deleteList.push($scope.allgoods[index]);
          }
        }
      }
      //删除所有
      function addAllToDeleteList(){

        for( var index in $scope.allgoods){
            $scope.deleteList.push($scope.allgoods[index]);
        }
      }

       function removeToDeleteList(data){

        for( var index in $scope.deleteList){
          if(data==$scope.deleteList[index].activity_id){
            $scope.deleteList.splice(index,1);
          }
        }
      }

      //改变全选状态
      function changeChooseAll(){
        if($scope.selectNumber==$scope.allgoods.length){
          $scope.isSelectedAll=true;

        }else{
          $scope.isSelectedAll=false;
        }
      }

      function check_goods_isSelected() {
        var allgoods = $scope.allgoods,num = 0;
        for(var i = 0,len = allgoods.length;i < len;i++) {
          if(allgoods[i].isSelected) {
            num++
          } 
        }
        return num;

      }
      //编辑按钮
      $scope.editGoods=function(){
        $scope.$emit('changtoedit');
        $scope.selectNumber = check_goods_isSelected();
        if($state.current.name == 'tab.trolley') {
          var countbox=document.getElementById('countbox');
          var editbox=document.getElementById('editbox');
        } else {
          var countbox=document.getElementById('countbox_1');
          var editbox=document.getElementById('editbox_1');
        }
        if(countbox) countbox.style.display="none";
        if(editbox) editbox.style.display="block";
      };
      //取消按钮
      $scope.cancallEdit=function(){
        $scope.$emit('changtocount');
        if($state.current.name == 'tab.trolley') {
          var countbox=document.getElementById('countbox');
          var editbox=document.getElementById('editbox');
        } else {
          var countbox=document.getElementById('countbox_1');
          var editbox=document.getElementById('editbox_1');
        }
        if(countbox) countbox.style.display="block";
        if(editbox) editbox.style.display="none";
      };

      $scope.chooseAllGoods=function(){
        if($scope.isSelectedAll ){
          $scope.isSelectedAll=false;
          $scope.selectNumber=0;
          //清空删除列表
          $scope.deleteList.length=0;
          for (var index in $scope.allgoods){
            $scope.allgoods[index].isSelected=false;
          }
        }
        else{
          addAllToDeleteList();
          $scope.isSelectedAll=true;
          $scope.selectNumber=$scope.allgoods.length;
          for (var index in $scope.allgoods){
            $scope.allgoods[index].isSelected=true;
          }
        }
      };

    //删除商品
      function removeGoods(){
      trolleyInfo.removeGoodsItem($scope.deleteList);
        $scope.selectNumber=0;
        $scope.isSelectedAll=false;
      };

    //动态获取商品总价
      $scope.getGoodsPrice=function(){
        var countPrice=0;
        for(var index = 0,len = $scope.allgoods.length;index < len;index++){
          var number
          if(isNaN(parseInt($scope.allgoods[index].join_number))){
            var minNum=$scope.allgoods[index].activity_type==1?1:10;
            number=minNum;

          }else{
            number=parseInt($scope.allgoods[index].join_number)
          }

          countPrice+= number
        }
        return countPrice;
      }

      $scope.goNext = function(){
        $state.go('tab.account');
      };

      $scope.goPre = function(){
        $state.go('tab.publish');
      }

      $scope.goToMainPage=function(){

        $state.go('tab.mainpage')
      }
      $scope.goBack = function() {
          $ionicHistory.goBack();
      };

      //修正不正确参数
      $scope.fixOrder=function(){

        for(var index in $scope.allgoods){
          if(isNaN(parseInt($scope.allgoods[index].join_number))){
            var minNum=$scope.allgoods[index].activity_type==1?1:10;
            $scope.allgoods[index].join_number=minNum;
            console.log($scope.allgoods[index].join_number)
          }
          if($scope.allgoods[index].activity_type==2){
            $scope.allgoods[index].join_number=$scope.allgoods[index].join_number-($scope.allgoods[index].join_number%10);
            console.log($scope.allgoods[index].join_number)
          }
            if($scope.allgoods[index].activity_type==4){
                if($scope.allgoods[index].join_number==2){
                    $scope.allgoods[index].join_number=2;
                }
                else if($scope.allgoods[index].join_number==1){
                    $scope.allgoods[index].join_number=1;
                }
            }
        }
        var huixiao = Storage.get('huixiao');
        
        if(huixiao && huixiao == 'huixiao' && ionic.Platform.isWebView() && (ionic.Platform.isIOS() || ionic.Platform.isIPad())){
          searchTrolley();
        } else {
          $state.go('payTransfer')
        }
        

      };

      function searchTrolley() {
          var goodsList = trolleyInfo.getGoodsInfo();
          var commitData = [];
          var _good;
          var goodKey;
          var good;
          for (goodKey in goodsList) {
              try {
                  good = goodsList[goodKey];
                  _good = {
                      activity_id: good.activity_id,
                      goods_title: good.goods_title,
                      num: good.join_number
                  };
                  commitData.push(_good);
              } catch (e) {
                  console.log('failed to transfer trolley data : ' + e.message);
              }
          }

          // ToastUtils.showLoading('正在获取订单……');

          AppModel.getOrderInfo(commitData, function(response, data) {
              // ToastUtils.hideLoading();
              try {
                  var code = data.code;
                  if (0 == code) {
                    DMSafari.transWeb(baseApiUrl + "?c=app&a=pay_page&order_num=" + data.data.order_num + "&sign=" + data.data.sign, function(){
                        
                    }, function(){

                    });
                    $scope.allgoods = [];
                    trolleyInfo.clear();
                  } else if(code == 1) {
                      var huixiao = Storage.get('huixiao');
                      if(huixiao && huixiao == 'huixiao') {
                        $scope.chongzhiPopup = $ionicPopup.confirm({
                            title: '<b>订单生成失败：' + data.msg + '</b>',
                            scope: $scope,
                            buttons: [{
                                text: '取消',
                                onTap: function(e) {
                                    $scope.chongzhiPopup.close();
                                    return false;
                                }
                            }]
                        });
                      } else {
                        $scope.chongzhiPopup = $ionicPopup.confirm({
                            title: '<b>订单生成失败：' + data.msg + '</b>',
                            scope: $scope,
                            buttons: [{
                                text: '取消',
                                onTap: function(e) {
                                    $scope.chongzhiPopup.close();
                                    return false;
                                }
                            }, {
                                text: '<b>去充值</b>',
                                type: 'button-assertive',
                                onTap: function(e) {
                                    Storage.set('payCommitData',commitData);
                                    $state.go('chongzhi');
                                    return false;
                                }
                            }]
                        });
                        
                      } 
                      
                  } else if(code == 2) {
                      $scope.chongzhiPopup = $ionicPopup.confirm({
                          title: '<b>订单生成失败：' + data.msg + '</b>',
                          scope: $scope,
                          buttons: [{
                              text: '取消',
                              onTap: function(e) {
                                  $scope.chongzhiPopup.close();
                                  return false;
                              }
                          }]
                      });
                  } else {
                      $scope.chongzhiPopup = $ionicPopup.confirm({
                          title: '<b>订单生成失败：' + data.msg + '</b>',
                          scope: $scope,
                          buttons: [{
                              text: '取消',
                              onTap: function(e) {
                                  $scope.chongzhiPopup.close();
                                  return false;
                              }
                          }]
                      });
                  }
              } catch (e) {
                  console.error('failed 订单获取失败：' + e.message);
                  goBack();
              }
          }, function(response, data) {
              ToastUtils.hideLoading();
              ToastUtils.showError('网络异常：' + '状态码：' + response.status);
              goBack();
          }, function() {
              
          });
      }


      $scope.showPop = function () {

        if($scope.selectNumber>0){
          $ionicPopup.confirm({
            title: '确定删除？',
            cancelText: '取消',
            cancelType: 'button-default',
            okText: '确定',
            okType: 'button-assertive'
          }).then(function (res) {
            if (res) {
              removeGoods();
              $scope.cancallEdit();
              if($scope.allgoods.length == 0 && $scope.recommendGoodsList.length == 0) {
                getRecommendData();
              }
            } else {
              
            }
          })
        }else{
          ToastUtils.showError('请选择要删除的商品')

        }

      };

      $scope.recommendGoodsList = [];
      if($scope.allgoods.length == 0) {
        getRecommendData();
      }
      


    }])
  });

