/**
 * 用于消息提示 加载提示
 * Created by luliang on 2015/12/2.
 */
define(['app'],function(app){

  app.factory('ToastUtils',[function(){

    var _document = document;

    var _dom_toast_loading = _document.getElementById('loadingToast');
    var _dom_toast_loading_msg = _document.getElementById('loadingTips');
    var _dom_toast = _document.getElementById('toast');
    var _dom_toast_msg = _document.getElementById('toastMsg');
    var _dom_toast_icon = _document.getElementById('toastIcon');
    var mTimeout;
      var _layui_content= _document.getElementById('layui_content');

    function cancelTimeout(){
      if(mTimeout){
        clearTimeout(mTimeout);
        mTimeout = undefined;
      }
    }

    function showNow(msg,classStyle,duration){
      cancelTimeout();
      _dom_toast_msg.innerText = msg;
      _dom_toast.style.display = "block";
      _dom_toast_icon.className = classStyle;
      mTimeout = setTimeout(function(){
        _dom_toast.style.display = "none";

        mTimeout = undefined;
      },duration);
    }

    function showShortNow(type,msg){
      var duration = 1000;
      var classStyle;
      switch (type){
        case STATE_STYLE.ERROR:
          duration = 3000;
          classStyle = 'weui_icon_cancel';
          break;
        case STATE_STYLE.GOOD:
          duration = 1000;
          classStyle = 'weui_icon_toast';
          break;
        case STATE_STYLE.NORMAL:
          duration = 1000;
          classStyle = '';
          break;
        case STATE_STYLE.WARNING:
          duration = 3000;
          classStyle = 'weui_icon_warn';
          break;
        default :
          duration = 1000;
          classStyle = '';
          break;
      }
      showNow(msg,classStyle,duration);
    }

    function showLoading(msg){
      _dom_toast_loading_msg.innerText = msg;
      _dom_toast_loading.style.display = "block";
    }

    function hideLoading(){
      _dom_toast_loading.style.display = "none";

    }

    function showNormal(msg){
      showShortNow(STATE_STYLE.NORMAL,msg);
    }

    function showSuccess(msg){
      showShortNow(STATE_STYLE.GOOD,msg);
    }

    function showWarning(msg){
      showShortNow(STATE_STYLE.WARNING,msg);
    }

    function showError(msg){
      showShortNow(STATE_STYLE.ERROR,msg);
    }


    function showMsgWithCode(code,msg){
      switch (code){
        case -1:
          showNormal(msg);
          break;
        case 0:
          showSuccess(msg);
          break;

        case 6:
          showWarning(msg);
          break;
        default:
          showError(msg);
          break;
      }
    }

    function showTips(msg, timeout) {
      var body = document.getElementsByTagName('body');
      var div = document.createElement('div');
      div.className = "dm-tips";
      div.innerHTML = msg; 
      body[0].appendChild(div);
      var t = timeout || 2000;
      div.style.animation = 'tipsHide ' + t/1000 + 's cubic-bezier(0.42, 0, 0.9, 0.21) forwards';
      setTimeout(function () {
        body[0].removeChild(div);
      }, t);
    }

    return{
      showShortNow:showShortNow,
      showSuccess : showSuccess,
      showNormal : showNormal,
      showWarning : showWarning,
      showError : showError,
      showMsgWithCode : showMsgWithCode,
      showLoading : showLoading,
      hideLoading : hideLoading,
      showTips : showTips,
      showNow:showNow
    }
  }]);

});
