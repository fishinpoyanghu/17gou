/**
 * Created by luliang on 2016/1/7.
 */
define(
  [
    'app',
    'html/classification/service_classification_list',
    'utils/toastUtil'
  ],
  function(app){
    'use strict';
    app
      .controller('ClassificationController',['$scope','ClassificationService','ToastUtils',function($scope,ClassificationService,ToastUtils){


        function refresh(){
          ClassificationService.getClasses(function(categoryList){
            $scope.categoryList = categoryList;
          },function(reason){
            console.error(reason);
          });
        }

        function initial(){
          refresh();
        }

        $scope.show_sub = function(category) {
          if(category.show) {
            category.show = false
          } else {
            for(var i in $scope.categoryList) {
              $scope.categoryList[i].show = false;
            }
            category.show = true;
          }
        }

        initial();
      }]);
  });
