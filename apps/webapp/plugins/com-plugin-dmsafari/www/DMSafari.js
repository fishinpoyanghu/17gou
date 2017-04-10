var exec = require('cordova/exec');
var DMSafari = {

    // transWeb接口，url为传入的参数，success, error为回调函数
    transWeb : function(url, success, error) {
        exec(success, error, "DMSafari", "transWeb", [url]);
    }
    
};

window.DMSafari = DMSafari;