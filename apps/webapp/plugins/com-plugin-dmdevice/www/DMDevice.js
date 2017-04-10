var exec = require('cordova/exec');
var DMDevice = {

    // echo接口，msg为传入的参数，success, error为回调函数
    echo : function(msg, success, error) {
        exec(success, error, "DMDevice", "echo", [msg]);
    },

    // 传入type，可以支持wechat，或者urlScheme....
    checkApp : function(type, success, error) {
        exec(success, error, "DMDevice", "checkApp", [type]);
    }

};

window.DMDevice = DMDevice;