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
      .controller('limitAreaController',['$scope','$stateParams','$timeout','ToastUtils','ClassificationService', function($scope,$stateParams,$timeout,ToastUtils,ClassificationService){
        function initial(){
          $scope.activityType = 3;
          $scope.goods_type_id = null;
          
          $scope.oderKey = "weight";
          $scope.oderType = "desc";
          writeTitle('限购专区');

        }




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

        initial();
      }]);
  });
