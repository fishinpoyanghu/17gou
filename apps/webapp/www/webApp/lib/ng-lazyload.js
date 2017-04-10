
define(['app'], function (app) {
  app.directive('lazyload', lazyload);
  lazyload.$inject = ['$ionicScrollDelegate'];
  function lazyload($ionicScrollDelegate) {
    var defaultImgPreload = 0; //默认预加载距离
    var error_img = 'img/error_img.png';  //错误图片
    var blank_img = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'; //空白
    var goods_img = "img/goods_default.jpg";  //默认商品图片

    var imgsToload = {};

    $(window).bind('touchmove', loadImgs);
    $(window).bind('scroll', loadImgs); //可能无响应

    function $(elem) {
      return angular.element(elem);
    }

    function isVisible(elem, preload) {
      //可见区域的高度
      var h = document.documentElement.clientHeight;
      //元素顶部和可见区域顶部之间的距离
      var t = elem.getBoundingClientRect().top - document.documentElement.scrollTop - preload;
      //console.log(t +'to'+ h);
      return (t<=h);
    }

    function loadImgs() {
      Object.keys(imgsToload).forEach(function (uid) {
        var obj = imgsToload[uid];
        var img = obj.elem;
        var src = obj.src;
        var preload = obj.preload;
        var errorImg = obj.error;
        if(isVisible(img, preload)) {
          loadImg(img, src, errorImg);
        }
      })
    }

    function loadImg(img, src, error) {
      img.src = src;
      var uid = img.uid;
      img.onload = function() {
        if(imgsToload.hasOwnProperty(uid)) {
          delete imgsToload[uid];
        }
      }
      img.onerror = function() {
        img.src = error;
        if(imgsToload.hasOwnProperty(uid)) {
          delete imgsToload[uid];
        }
      }
    }


    return {
      restrict: 'A',
      require: ['?^$ionicScroll'],
      scope: {
        lazySrc: '@',   //图片地址
        preload: '@',  //预加载距离
        errorImg: '@', //错误图片
        defaultImg: '@'  //默认图片
      },
      link: function postLink(scope, elem, attrs, ctrls) {
        var img = elem[0];
        var defaultImg = scope.defaultImg || goods_img;
        var src = scope.lazySrc || goods_img;
        var preload = scope.preload || defaultImgPreload;
        var error = scope.errorImg || goods_img;

        if(isVisible(img, preload)) {
          loadImg(img, src, error);
        }else {
          loadImg(img, defaultImg, error);
          img.uid = scope.$id;
          imgsToload[img.uid] = {
            elem:img,
            src:src,
            preload:preload,
            error:error};
        }

        var scrollCtrl =  ctrls[0];
        scrollCtrl.$element.on('scroll', loadImgs);

      }
    }
  }
})
