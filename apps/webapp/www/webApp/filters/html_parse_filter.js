/**
 * 转化html过滤器
 * Created by luliang on 2015/11/27.
 */

define(['app'],function(app){
  app
    .filter('trustHtmlFilter',['$sce',function($sce){
      return function(input){
        return $sce.trustAsHtml(input);
      }
    }]);
});

