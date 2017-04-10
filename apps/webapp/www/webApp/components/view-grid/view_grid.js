/**
 * Created by luliang on 2015/11/14.
 */
define(['app'],function(app){
  'use strict';
  app
    .directive('viewGrid', [function () {
      return {
        restrict:'E',
        scope: {
          compId:'=',
          dpGrids:'=',
          styleList: '=styleList'
        },
        compile:function compile(tElement, tAttrs, transclude){
          return{
            pre:function preLink(scope, iElement, iAttrs, controller){

            },
            post:function postLink(scope, iElement, iAttrs, controller){
              iElement.bind('$destroy',function(){
                //dom释放资源
              });
            }
          }
        },
        templateUrl: function(elem, attr) {
          var path = "webApp/components/view-grid/";
          var fileName = "view_grid_" + attr.type + ".html";
          return path+fileName;
        }
      };
    }]);

});
