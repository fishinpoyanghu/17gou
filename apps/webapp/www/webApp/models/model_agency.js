/**
 * 用户接口数据请求模块
 * Created by luliang on 2015/11/26.
 */
define(['app', 'utils/httpRequest', 'html/common/geturl_service'], function(app) {

    app.factory('agencyModel', ['httpRequest', 'MyUrl', function(httpRequest, MyUrl) {


        /**
         * 获取用户代理的信息，在用户进入代理模块时调用获取
        */
		function getagencymsg (){
			 
			 var requestUrl = '?c=agency&a=getagencymsg';
            httpRequest.post(requestUrl);
		}


        return {
            getagencymsg: getagencymsg		//获取用户代理信息
        }

    }]);


});
