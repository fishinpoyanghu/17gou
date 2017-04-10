/**
 * 简易轮询
 * Created by luliang on 2016/1/28.
 */
define([
  'app',
  'utils/httpRequest'
],function(app){
  app
    .factory('PushService',['httpRequest',function(httpRequest){
      var TYPE_POST = 'post';
      var TYPE_GET = 'get';
      var _interval_time = 3000;
      var _requestUrl;
      var _requestParams;
      var _onSuccess;
      var _onFailed;
      var _timeId;
      var _type = TYPE_POST;
      var _isEnable = false;
      function noop(){

      }
      _onSuccess = noop;
      _onFailed = noop;

      function init(config){
        _interval_time = angular.isNumber(config.intervalTime) ? config.intervalTime : 3000;
        setRequestData(config.requestUrl,config.requestParams);
        setSuccessReceiver(config.onSuccess);
        setFailedReceiver(config.onFailed);
        startService();
      }

      function startService(){
        if(!_isEnable){
          stopService();
          throw new Error('stop service... params have error');
        }
        _timeId = setTimeout(function(){
          push();
          startService();
        },_interval_time);
      }

      function stopService(){
        if(_timeId){
          clearTimeout(_timeId);
        }
      }

      function push(){
        try {
          switch (_type) {
            case TYPE_POST:
              return httpRequest.post(_requestUrl, _requestParams, _onSuccess, _onFailed);
            case TYPE_GET:
              return httpRequest.get(_requestUrl, _requestParams, _onSuccess, _onFailed);
            default:
              return httpRequest.post(_requestUrl, _requestParams, _onSuccess, _onFailed);
          }
        } catch (e) {
          ssjjLog.error(e.message || e);
        }
      }

      function setSuccessReceiver(onSuccess){
        _onSuccess = angular.isFunction(onSuccess) ? onSuccess : noop;
      }

      function setFailedReceiver(onFailed){
        _onFailed = angular.isFunction(onFailed) ? onFailed : noop;
      }

      /**
       * 设置请求数据 失败 则停止轮询
       * @param {String} requestUrl
       * @param {Object} requestParams
       */
      function setRequestData(requestUrl,requestParams){
        _requestUrl = angular.isString(requestUrl) ? requestUrl : _requestUrl;
        _requestParams = angular.isObject(requestParams) ? requestParams : _requestParams;
        checkRequestData();
      }

      function checkRequestData(){
        _isEnable = angular.isString(_requestUrl) && angular.isObject(_requestParams);
        if(!_isEnable){
          throw new Error('requestData Error： requestUrl must be url and requestParams must be Object');
        }
        return _isEnable;
      }


      return {
        init : init,
        startService : startService,
        stopService : stopService,
        setSuccessReceiver : setSuccessReceiver,
        setFailedReceiver : setFailedReceiver,
        setRequestData : setRequestData
      }
    }])
});
