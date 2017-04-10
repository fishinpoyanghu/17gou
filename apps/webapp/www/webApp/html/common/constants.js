/**
 * Created by suiman on 15/11/10.
 */

//定义全局常量

Tencent_APP_ID = "1104879535";

var isDebug = true;

var STATUS  = {
  LOGIN_NORMAL : 0 ,
  LOGIN_ABNORMAL : 1
};
var STATE_STYLE = {
  NORMAL:0,
  GOOD:1,
  WARNING:2,
  ERROR:3
};
var SCOPE_CLASS = {
  ALL:0,
  PAGE:1,
  LIST:2
};




var baseUrl = 'http://'+window.location.host+'/apps/webapp/www/';
var baseApiUrl = 'http://'+window.location.host+'/apps/api/www/';

//app配置
var appConfig = {
  appName:'亿七购',   //app名称
  hasGuide:false,            //是否开启引导页
  guideImages:['img/guide/guide-1.jpg','img/guide/guide-2.jpg','img/guide/guide-3.jpg'], //引导页图片链接
  shareTitle:'一元就能买iPhone。首充100还能免费领取大容量充电宝！',  //分享标题
  shareImgUrls:'img/share_icon.jpg',                     //分享图片链接
  shareLink:baseUrl + '#/boostrap/',    //分享链接
  shareContent: '一元就能买iPhone。最刺激的商城玩法！',   //分享内容
  inWeixinShare:{   //通过微信自带的分享
    shareTitle:'一元就能买iPhone。首充100还能免费领取大容量充电宝！',  //分享标题
    shareImgUrls:baseUrl + 'img/share_icon.jpg',                     //分享图片链接
    shareLink:baseUrl + '#/tab/mainpage',    //分享链接
    shareContent: '一元就能买iPhone。最刺激的商城玩法！',   //分享内容
  }
}


//监听修改title的事件
function writeTitle(title){
  var doc=document;
  var body = doc.getElementsByTagName('body')[0];
  doc.title = title;
  var iframe = doc.createElement("iframe");
  iframe.title = '';
  //iframe.setAttribute("src", "img/logo.ico");
  iframe.width = 0;
  iframe.height = 0;
  iframe.frameborder="0";
  iframe.style.display='none';
  iframe.addEventListener('load', function() {
    var fn = arguments.callee;
    setTimeout(function() {
      iframe.removeEventListener('load',fn);
      doc.body.removeChild(iframe);
    }, 0);
  });
  doc.body.appendChild(iframe);
}

//log工具和捕获异常
(function(){

  if(isDebug) {
    window.ssjjLog = {
      log: Function.prototype.bind.call(console.log, console),
      info: Function.prototype.bind.call(console.info, console),
      warn: Function.prototype.bind.call(console.warn, console),
      error: Function.prototype.bind.call(console.error, console)
    };
  }else {
    window.ssjjLog = {
      log: function() {},
      info: function() {},
      warn: function() {},
      error: function() {}
    };
  }

})();

(function(){
  // Pure JavaScript errors handler
  if(!isDebug){
    window.addEventListener('error', function(err) {
      ssjjLog.log('[ssjjError]:');
      ssjjLog.error({
        message: err.message,
        filename: err.filename,
        line: err.lineno,
        column: err.colno // might not be present
      });

      var lineAndColumnInfo = err.colno ? ' line:' + err.lineno + ', column:' + err.colno : ' line:' + e.lineno;
      ga(
        'send',
        'event',
        'JavaScript Error',
        err.message,
        err.filename + lineAndColumnInfo + ' -> ' + navigator.userAgent,
        0,
        true
      );
    });
  }
})();
