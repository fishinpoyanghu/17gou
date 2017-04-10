/**
 * 富文本组件
 * Created by luliang on 2016/1/6.
 */
define(function(){
  'use strict';
  angular
    .module('starter.directives')
    .directive('viewRichText', ['$sanitize',function($sanitize) {
      return {
        restrict:'E',
        scope: {
          compId:'=',
          dpRichText:'@',
          styleList: '=styleList'
        },
        compile:function compile(tElement, tAttrs, transclude){
//        Pre-linking function 在子元素被链接前执行。不能用来进行DOM的变形，以为可能导致链接函数找不到正确的元素来链接。
//        Post-linking function 所有元素都被链接后执行。可以操作DOM的变形
          return{
            pre:function preLink(scope, iElement, iAttrs, controller){

            },
            post:function postLink(scope, iElement, iAttrs, controller){
              iElement.bind('$destroy',function(){
                //dom释放资源
                //取消网络异步请求，生命周期大于dom的移除监听
              });
            }
          }
        },
        templateUrl: function(elem, attr) {
          var path = "webApp/components/view-rich-text/";
          var fileName = "view_rich_text_" + attr.type + ".html";
          return path+fileName;
        }
      };
    }]);
});
