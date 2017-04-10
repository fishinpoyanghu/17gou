/**
 * Created by daihua on 2015/11/16.
 */
define([
  'app'
],function(app){

  app.factory('ThirdTencent',function($q,$http,ToastUtils){

    return{
      // qq登录
      qqLogin :function(){
        var deferred = $q.defer();
        var promise = deferred.promise;

        //var APP_ID = '1104879535';
        YCQQ.ssoLogin(onSuccess, onFail);
        function onSuccess(JSONObject){
          var token = JSONObject.access_token;
          var openId = JSONObject.userid;
          ToastUtils.showShortNow(STATE_STYLE.NORMAL,'token : ' + token + ' openId'+ openId);
          deferred.resolve(JSONObject);
        }
        function onFail(message){
          deferred.reject(message);
        }
        promise.success = function (fn) {
          promise.then(fn);
          return promise;
        };
        promise.error = function (fn) {
          promise.then(null, fn);
          return promise;
        };
        return promise;
      }
    }

  });
});
