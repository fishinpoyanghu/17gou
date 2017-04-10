/**
 * Created by suiman on 16/1/8.
 */

define(['app'], function (app) {
  app.factory('logUtil', function () {

    return {
      log: log,
      info: info,
      warn: warn,
      error: error
    };

    function log(msg) {
      ssjjLog.log(msg);
    }

    function info(msg) {
      ssjjLog.info(msg);
    }

    function warn(msg) {
      ssjjLog.warn(msg);
    }

    function error(msg) {
      ssjjLog.error(msg);
    }
  })
});
