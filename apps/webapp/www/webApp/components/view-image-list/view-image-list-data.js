/**
 * Created by Administrator on 2015/11/18.
 */
define(['app'],function(app){
  'use strict';
  app.service('viewImageListData',viewImageListData);

  viewImageListData.$inject = [] ;

  function viewImageListData(){

    var _this = this ;
    var images ;
    var imageSize ;
    var columnSize ;
    var maxRowSize ;

    _this.setImages = function(items){
      images = items ;
      _this.setImageSize(items.length)
    };

    this.getImages = function(){
      return images ;
    };

    _this.setImageSize = function(size){
      imageSize = size ;
    };

    this.getImageSize = function(){
      return imageSize ;
    };

    _this.setColumnSize = function(size){
      var length = _this.getImageSize();
      var colSize;

      if(isNaN(size) === true){
        colSize = 3;
      }
      else if(size > length){
        colSize = length;
      }
      else if(size <= 0){
        colSize = 1;
      }
      else{
        colSize = size;
      }
      columnSize = colSize ;
    };

    this.getColumnSize = function(){
      return columnSize;
    };

    _this.setMaxRowSize = function(size){
      if(isNaN(size) === true){
        maxRowSize = 2^31-1 ;
      }else if(size <= 0){
        maxRowSize = 2^31-1 ;
      }else {
        maxRowSize = size ;
      }
    };

    this.getMaxRowSize = function(){
      return maxRowSize ;
    };

    this.buildImageList = function() {
      var images = this.getImages();
      var columnSize = this.getColumnSize();
      var maxRowSize = this.getMaxRowSize();
      var items = [];
      var row = -1;
      var col = 0;

      for (var i = 0; i < images.length; i++) {

        if (i % columnSize === 0) {
          row++;
          if(row >= maxRowSize){
            break ;
          }
          items[row] = [];
          col = 0;

        }

        items[row][col] = images[i];
        col++;
      }

      return items;
    };

    this.getGridSize = function(){
      return parseInt((1/this.getColumnSize())* 100);
    };
  }

});

