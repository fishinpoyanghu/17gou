/**
 * 字符串工具类
 * Created by luliang on 2015/12/4.
 */
define(['app'],function(app){
  'use strict';
  app
    .factory('StringUtils',[function(){
      return{
        isStringNotEmpty : function(string){
          return angular.isString(string)&&(string.replace(/(^s*)|(s*$)/g, "").length > 0);
        }
      }
    }]);
});
