/**
 * Created by luliang on 2015/11/14.
 */
define(['app'],function(app){
  'use strict';
  app
    .directive('viewSlideBox', ['$ionicSlideBoxDelegate','$timeout',function($ionicSlideBoxDelegate,$timeout) {
      return {
        restrict:'E',
        scope: {
          compId:'=',
          dpSlides:'='
        },
//    @ 这是一个单项绑定的前缀标识符
//    使用方法：在元素中使用属性，好比这样<div my-directive my-name="{{name}}"></div>，注意，属性的名字要用-将两个单词连接，因为是数据的单项绑定所以要通过使用{{}}来绑定数据。
//
//    = 这是一个双向数据绑定前缀标识符
//    使用方法：在元素中使用属性，好比这样<div my-directive age="age"></div>,注意，数据的双向绑定要通过=前缀标识符实现，所以不可以使用{{}}。
//
//    & 这是一个绑定函数方法的前缀标识符
//    使用方法：在元素中使用属性，好比这样<div my-directive change-my-age="changeAge()"></div>，注意，属性的名字要用-将多个个单词连接。
//      $scope - 当前元素关联的作用域。
//      $element - 当前元素
//      $attrs - 当前元素的属性对象。
//      $transclude - 模板链接功能前绑定到正确的模板作用

        compile:function compile(tElement, tAttrs, transclude){
//        Pre-linking function 在子元素被链接前执行。不能用来进行DOM的变形，以为可能导致链接函数找不到正确的元素来链接。
//        Post-linking function 所有元素都被链接后执行。可以操作DOM的变形
          return{
            pre:function preLink(scope, iElement, iAttrs, controller){

            },
            post:function postLink(scope, iElement, iAttrs, controller){
              scope.$watch('dpSlides', function(newValue, oldValue) {
                if(angular.isArray(newValue) && newValue.length > 0) {
                  $ionicSlideBoxDelegate.update();
                }
              })

              scope.slideHasChanged = function(index) {
                if(scope.dpSlides.length == 2) {
                  if(index == 1 || index == 0) {
                    scope.slidePageHack = false;
                  } else {
                    scope.slidePageHack = true;
                  }
                }
              }
              iElement.bind('$destroy',function(){
                //dom释放资源
                //取消网络异步请求，生命周期大于dom的移除监听
              });
            }
          }
        },
        templateUrl: function(elem, attr) {
          var path = "webApp/components/view-slidebox/";
          var fileName = "view_slidebox_" + attr.type + ".html";
          return path+fileName;
        }
      };
    }]);
});
