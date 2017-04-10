/**
 * Created by suiman on 16/1/14.
 */

define(['app'], function (app) {
  app.directive('viewInfiniteScroll', viewInfiniteScroll);

  viewInfiniteScroll.$inject = ['$ionicScrollDelegate'];
  function viewInfiniteScroll($ionicScrollDelegate) {



    return {
      restrict: 'E',
      scope: {
        onInfinite: '&'  //上拉的回调
      },
      require: ['?^$ionicScroll'],

      link: function postLink(scope, iElem, iAttrs, ctrls) {

        var scrollCtrl = ctrls[0];
        scrollCtrl.$element.on('scroll', function () {
          //console.log('view-infinite-scroll scroll');
          checkWhenPullUp();
        });

        function checkWhenPullUp() {
          if(isAtBottom()) {
            console.log('is in bottom');
            scope.onInfinite();
          }else {
            //console.log('is not in bottom');
          }
        }

        scope.$on('destroy', function () {
            console.log('view-infinite-scroll is destroyed');
        });


        //判断是否已经上拉到底部
        function isAtBottom() {
          var scrolltop= ionic.scroll.lastTop;
          var elemBottom = iElem[0].offsetTop;
          var windowBottom = document.documentElement.clientHeight;


          //console.log('scrolltop is: '+ scrolltop);
          //console.log('elemBootom is: '+elemBottom);
          //console.log('windowBottom is: '+windowBottom);


          return ((elemBottom-scrolltop)<windowBottom && elemBottom>windowBottom);
        }

      }

    }
  }
})
