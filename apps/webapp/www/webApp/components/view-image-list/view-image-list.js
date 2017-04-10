/**
 * Created by Administrator on 2015/11/17.
 */

define([
  'app',
  './view-image-list-data',
  './view-image-scale',
  './view-row-height'
],function(app){
  'use strict';
  app.directive('viewImageList',viewImageList);

  viewImageList.$inject = ['viewImageListData','$ionicModal'] ;

  function viewImageList(viewImageListData){
    return {
        restrict: 'AE',
        controller:controller,
        scope: {
          dpData: '=dpData',
          dpLargeData: '=dpLargeData',
          dpColumns: '=dpColumns',
          dpRows: '=dpRows',
          dpToggle: '=dpToggle'
        },
        replace: true,
        templateUrl:'webApp/components/view-image-list/view-image-list.html'
      };

    function controller($scope,$ionicModal,$ionicSlideBoxDelegate){

      $ionicModal.fromTemplateUrl('webApp/components/view-image-list/view-image-list-modal.html', {
        scope: $scope,
        animation: 'fade-in'
      }).then(function(modal) {
        $scope.modal = modal;
      });
      $scope.closeModal = function() {
        $scope.modal.hide();
      };
      //当我们用到模型时，清除它！
      $scope.$on('$destroy', function() {
        if($scope.modal) $scope.modal.remove();
        
      });

      $scope.openModal = function(imgObject,parentIndex,childIndex){
        if($scope.dpToggle){
          if(isNaN($scope.dpColumns) === true){
            $scope.dpColumns = 3;
          }
          var index = parentIndex * $scope.dpColumns + childIndex ;
          $scope.modal.show();
          $ionicSlideBoxDelegate.$getByHandle('image-slide').slide(index)
        }
      };

      // $scope.dpLargeData = transformBigImage($scope.dpData);
      $scope.dpLargeData = $scope.dpData;


      /**
       * 图片转为大图
       * @param imgUrls
       * @returns {Array}
       */
      function transformBigImage(imgUrls){
        var newImgUrls = [] ;
        for(var i=0;i<imgUrls.length;i++){
          var imgUrl = imgUrls[i] ;
          var index = imgUrl.indexOf('_n');
          var newImageUrl = '' ;
          for(var j=0;j<index;j++){
            newImageUrl = newImageUrl + imgUrl[j] ;
          }
          newImageUrl = newImageUrl + '_n_big.jpg' ;
          newImgUrls.push(newImageUrl);
        }
        return newImgUrls ;
      }

      viewImageListData.setImages($scope.dpData);
      viewImageListData.setColumnSize(parseInt($scope.dpColumns));
      viewImageListData.setMaxRowSize(parseInt($scope.dpRows));

      var _drawGallery = function(){
        $scope.items = viewImageListData.buildImageList();
        $scope.responsiveGrid = viewImageListData.getGridSize();
      };

      _drawGallery();

      (function () {
        $scope.$watch(function () {
          return $scope.dpData.length;
        }, function (newVal, oldVal) {
          if(newVal !== oldVal){
            viewImageListData.setImages($scope.dpData);
            _drawGallery();

          }
        });
      }());
    }

  }
});

