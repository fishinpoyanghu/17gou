
angular.module('starter.directives')

  .directive('viewShowOrder', function() {
    return {
      restrict: 'E',
      templateUrl: function(elem, attr) {
        var path = "webApp/components/view-showorder/";
        var fileName = "view_show_order_" + attr.type + ".html";
        return path+fileName;
      }
    }
  });
