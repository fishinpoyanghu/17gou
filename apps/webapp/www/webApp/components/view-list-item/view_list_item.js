/**
 * Created by luliang on 2016/1/12.
 */
define(
  ['app',
    'components/view-list-view/view_list_view'
  ],function(app){
    'use strict';
    app
      .directive('viewListItem',[function(){
        return {
          restrict:'E',
          require: '^?viewListView',
          transclude:true,
          compile:function compile(tElement, tAttrs, transclude){
            return{
              pre:function preLink(scope, iElement, iAttrs, controller){

              },
              post:function postLink(scope, iElement, iAttrs, controller){


                iElement.bind('$destroy',function(){
                  //dom释放资源,中断请求
                  //console.log('release page list');
                });
              }
            }
          },
          templateUrl: "webApp/components/view-list-item/view_list_item_normal.html"
          //templateUrl: function(elem, attr) {
          //  var path = "webApp/components/view-list-item/";
          //  var fileName = "view_list_item_" + attr.type + ".html";
          //  return path+fileName;
          //}
        };
      }]);
  });
