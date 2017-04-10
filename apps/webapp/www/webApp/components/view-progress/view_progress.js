/**
 * Created by suiman on 16/1/5.
 */

/**
 * 1.百分数（字符串）
 * <span class="num" view-progress progress="20%"></span>
 * 2.数字（0~1）
 * <span class="num" view-progress progress="0.2"></span>
 * 3.变量/表达式（注意：不要加{{}}）
 * <span class="num" view-progress progress="percent"></span>
 * <span class="num" view-progress progress="(remain_num / need_num)"></span>
 *
 */

define(
  ['app'],
  function (app) {
    app.directive('viewProgress', viewProgress);

    viewProgress.$inject = ['$parse'];
    function viewProgress($parse) {
      return {
        restrict: 'A',
        compile: function compile(tElem, tAttrs) {
          return {
            pre: function preLink() {},
            post: function postLink(scope, elem, attrs) {
              

            
              scope.$watch(attrs.progress,function() {
                var match;
                var percent;
                if(match = matchPercent(attrs.progress)) {
                  percent = parseInt(match[1]);
                }else {
                  var progress = $parse(attrs.progress)(scope, progress);
                  if(angular.isNumber(progress)) {
                    percent = progress*100;
                  }else {
                    percent = (match = matchPercent(progress)) ? parseInt(match[1]) : 0;
                  }
                }
                elem[0].style.width = percent+'%';
              })
              function matchPercent(str) {
                var regex = /(\d+.*\d+)%$/; //匹配百分数，例如33%的33
                return str.match(regex);
              }
            }
          }
        }
      }
    }
  })
