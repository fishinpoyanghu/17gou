/**
 * Created by luliang on 2016/2/2.
 */
define([
  'app',
  'models/model_app',
  'components/view-list-view/view_list_view',
  'angularSanitize',
  'filters/html_parse_filter',
],function(app){
  app
    .controller('NewsController',['$scope','$state','$sanitize','$window','AppModel',function($scope,$state,$sanitize,$window,AppModel){

      $scope.goBack = function(){
        $state.go('tab.account2');
//      $window.history.back();
      };

      //消息请求
      $scope.sysUrl = AppModel.getSysListUrl();
      $scope.notifyUrl = AppModel.getNotifyListUrl();

      $scope.sysCallBack = {
        setData: function setData(data) {
          $scope.sysList = data;
        },
        setEmpty: function setEmpty(isEmpty){
          $scope.sysEmpty = isEmpty;
        }
      };

      $scope.notifyCallBack = {
        setData: function setData(data) {
          $scope.notifyList = data;
        },
        setEmpty: function setEmpty(isEmpty){
          $scope.notifyEmpty = isEmpty;
        }
      };

      $scope.sysRequestParams = {
      };

      $scope.notifyRequestParams = {
        type:0
      };

      $scope.doRefresh = function(id){
        $scope.$broadcast('view_list_view.refresh',id);
      }
    }])
});
