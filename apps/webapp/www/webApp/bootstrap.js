/**
 * Created by luliang on 2015/12/30.
 */
define(['ionic', 'angular', 'app', 'app-routes','app-initial'], function (ionic, angular, app) {
  'use strict';

  var $html;

  $html = angular.element(document.getElementsByTagName('html')[0]);
  angular.element($html).ready(function () {
    try {
      angular.bootstrap(document, ['app']);
    } catch (e) {
      console.error(e.stack || e.message || e);
    }
  });

});

