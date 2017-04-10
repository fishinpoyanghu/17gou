/**
 * 收货地址相关接口
 *
 */
define(['app','utils/httpRequest'],function(app){

  app.factory('versionModel',['httpRequest',function(httpRequest){

    /**
     * 检查版本接口
     */
    function checkVersion(onSuccess,onFail){
      var requestUrl = '?c=app&a=check_version';
      var params = {};
      httpRequest.post(requestUrl, params, onSuccess,onFail,null);
    }



    return {
      checkVersion : checkVersion // 检查版本接口
    }

  }]);


});
