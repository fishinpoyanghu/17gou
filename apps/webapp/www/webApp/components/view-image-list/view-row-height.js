
define(['app'],function(app){
  'use strict';
  app.directive('viewRowHeight',viewRowHeight);

  viewRowHeight.$inject = [];

  function viewRowHeight(){

    return {
      restrict: 'A',
      link : link
    };

    function link(scope, element, attrs) {
      element.css('height',element[0].offsetWidth * parseInt(scope.$parent.responsiveGrid)/100 + 'px');
    }
  }
});
