/**
 * Created by luliang on 2016/1/7.
 */
define(
  [
    'app',
    'components/view-broad/view_broad',
    'html/classification/service_classification_list',
    'utils/toastUtil'
  ],
  function(app){
    'use strict';
    app
      .controller('shopClassificationListController',['$scope','$stateParams','$timeout','ToastUtils','ClassificationService', function($scope,$stateParams,$timeout,ToastUtils,ClassificationService){
        $scope.expandUp = true;
        var headListBar = document.getElementById('headListBar');
        var slider = document.getElementById('slide_box');
        var slider_ul = document.getElementById('slide_ul');
        var s_height = Math.ceil(slider_ul.childNodes.length / 3 / 4) * 42 + 2;
        //console.log(s_height);

        function initial(classType,classTitle){
          $scope.activityType = (-10 == classType) ? 2 : 0;
          $scope.goods_type_id = null;
          try {
            $scope.goods_type_id = (-10 == classType) ? null : parseInt(classType);
            getClasses();
          } catch (e) {
            console.error(e.message || e);
          }
          $scope.oderKey = "weight";
          $scope.oderType = "desc";
          $scope.navTitle = classTitle;
          writeTitle(classTitle);

        }

        function getClasses(){
          ClassificationService.getClasses(function(data){
            $scope.categoryList = data;
          },function(reason){
            console.error(reason);
          });
        }

        function showHeadBar(){
          if($scope.expandUp == true){
            headListBar.className = headListBar.className.replace('dp-headList','dp-headList dp-headList--open');
            slider.style.height = s_height + "px";
            $scope.expandUp = false;
          }else{
            headListBar.className = headListBar.className.replace('dp-headList dp-headList--open','dp-headList');
            slider.style.height =  '0px';
            $scope.expandUp = true;
          }
        }

        $scope.showHeadBar = showHeadBar;

        $scope.clickClass = function(classId){
          $scope.goods_type_id = classId;
          //showHeadBar(false);
          headListBar.className = headListBar.className.replace('dp-headList dp-headList--open','dp-headList');
          slider.style.height =  '0px';
          $scope.expandUp = true;
          $timeout(function(){
            //$scope.expandUp = false;
            $scope.doRefresh('3-2-001');
          },0);
        };

        //=================注册事件===================
        $scope.$on('view_broad.request_finished',function(event,data){
          $scope.$broadcast('scroll.refreshComplete');
          $scope.$broadcast('scroll.infiniteScrollComplete');
          $scope.isLoadFinished = true;
          stopPropagation(event,data.scope);
        });
        $scope.$on('view_broad.request_success',function(event,data){
          $scope.isMoreData = !data.data.isFinish;
          //console.info("$scope.isMoreData："+$scope.isMoreData);
          //ToastUtils.showSuccess('刷新成功');
          stopPropagation(event,data.scope);
        });

        function stopPropagation(event,scope){
          if(scope >= SCOPE_CLASS.PAGE){
            event.stopPropagation();
          }
        }
        //
        $scope.isMoreData = false;
        $scope.isLoadFinished = true;
        //
        $scope.doRefresh = function(id){
          $scope.$broadcast('broad.refresh',id);
          //$timeout(function(){
          //  $scope.$broadcast('scroll.refreshComplete');
          //},1000);
        };

        $scope.loadMore = function(id){
          $scope.isLoadFinished = false;
          $scope.$broadcast('broad.loadMore',id);
        };

        initial($stateParams.type,$stateParams.title);
      }]);
  });
