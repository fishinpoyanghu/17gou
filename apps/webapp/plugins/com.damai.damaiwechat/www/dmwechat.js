var exec = require('cordova/exec');

var dmwechat = {

    // 测试输出接口
    echo : function(msg, success, error) {
        exec(success, error, "dmwechat", "echo", [msg]);
    },

    // 初始化APPID KEY
    // var params = {
    //     wechatKey : "wxa2e2ce8a3180bed1",
    //     wechatSecret : "7395136f4dd56798032be054c681a97d",
    //     sinaKey : "1399888483",
    //     sinaSecret : "441ed24920f1800de4f0703848070f2d",
    //     tecentKey : "1104851815",
    //     tecentSecret : "X7FT5BmwiA5Y7nxp"
    // }
    init: function(params, success, error) {
        //微信登录、支付配置
        var params = {
            wechatKey : "",
            wechatSecret : "",
            sinaKey : "",
            sinaSecret : "",
            tecentKey : "",
            tecentSecret : ""
        }
        exec(success, error, "dmwechat", "init", [params]);
    },

    // share
    // type，使用默认样式，传空“”，else: sina wx wx_circle qq qzone
    // 其实这里，充分体现了一个APP作为网页的问题了，调用毕竟是硬伤...
    // var data = {
    //     "title" : "",
    //     "content" : "",
    //     "imgUrl" : "",
    //     "targetUrl" : ""
    // }
    share: function(type, data, success, error) {
        exec(success, error, "dmwechat", "share", [type, data]);
    },

    // login
    // var type = :wechat sina tencent
    // return: 
    // {
    //     "access_token":"",
    //     "open_id":"",
    //     "username":"",
    //     "icon":"",
    //     "data":"all data"
    // }
    login: function(type, success, error) {
        exec(success, error, "dmwechat", "login", [type]);
    },


    // 接入微信支付
    // var params = {
    //    "appId" : "wxd930ea5d5a258f4f",
    //    "partnerId" : "10000100",
    //    "prepayId" : "1101000000140415649af9fc314aa427",
    //    "packageValue" : "Sign=WXPay",
    //    "nonceStr" : "a462b76e7436e98e0ed6e13c64b4fd1c",
    //    "timeStamp" : "1397527777",
    //    "sign" : "582282D72DD2B03AD892830965F428CB16E7A256"
    // };
    wechatPay: function(params, success, error) {
        exec(success, error, "dmwechat", "wechatPay", [params]);
    },

    // 接入支付宝支付，注意微信支付iOS上，还要去代码里边，还有xml里边，修改info type
    // var param = ""
    aliPay: function(param, success, error) {
        exec(success, error, "dmwechat", "aliPay", [param]);
    }

};

window.dmwechat = dmwechat;

