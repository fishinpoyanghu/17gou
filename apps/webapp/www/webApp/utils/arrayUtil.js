/**
 * Created by suiman on 16/1/29.
 */

define(['app'],function(app){
  'use strict';
  app.factory('ArrayUtil',arrayUtil);

  function arrayUtil() {

    return {
      splitArray: splitArray
    };

    /**
     * 拆分数组
     * @param from 要拆分的数组
     * @param n    子数组的长度
     * @returns {Array}
     */
    function splitArray(from, n) {
      if(!Array.isArray(from)) {
        return [];
      }

      var to = [];
      var len = parseInt(from.length) + 1;
      for(var i=0; i<len; i++) {
        var child = [];
        for(var j=0; j<n; j++) {
          var index = i*n + j;
          child.push(from[index])
        }
        to.push(child);
      }
      return to;
    }
  }
});
