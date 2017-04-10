/**
 * Created by suiman on 16/1/25.
 */

define(['app'], function (app) {

  app.directive('viewCardRadio', viewCardRadio);

  viewCardRadio.$inject = ['$parse']
  function viewCardRadio($parse) {

    return {
      restrict: 'E',
      templateUrl: 'webApp/components/view-card-radio/view_card_radio.html',
      scope: {
        callback: '='
      },
      link: function postLink(scope, elem, attrs) {

        var money;
        var _callback = {
          selectMoney: scope.callback.selectMoney || angular.noop
        };

        scope.data = [20,50,100,200,500];

        scope.select = function(i) {
          clearCSS();
          var selectItem =  elem.find('li')[i];
          angular.element(selectItem).addClass('selected');
          money = (i===scope.data.length) ? scope.inputMoney : scope.data[i];
          _callback.selectMoney(money)
        }

        scope.onInputChange = function() {
          var match = scope.inputMoney.match(/(^[1-9]\d*)/); //匹配正整数
          money = (match==null) ? null : parseInt(match[0]);
          scope.inputMoney = money;
          _callback.selectMoney(money)
        }

        function clearCSS() {
          var elems = elem.find('li');
          for(var i=0; i<elems.length; i++) {
            angular.element(elems[i]).removeClass('selected');
          }
        }
      }
    }
  }
})
