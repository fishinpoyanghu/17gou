define(['avalon','utils/md5','utils/avalon.cookie'], 
    function(avalon,md5Utils) {

        var sessid = encodeURIComponent("");
        var appkey = "NKFSD-IJNBT-LDGAV-XYNVV";
        var sign,time;
        function getSessid() {
            if (!isStringNotEmpty(sessid)) {
                var _sessId = avalon.cookie.get("sessId");
                if (_sessId) {
                    sessid = _sessId;
                }
            }
            return sessid;
        }

        function getTimeStamp() {
            time = new Date().getTime();
            return time;
        }

        function getFormMd5(formId, time, formData) {
            return md5Utils.md5(appkey + formId + time + formData);
        }

        function isLogin() {
            return isStringNotEmpty(getSessid());
        }

        function getSign() {
            sign = md5Utils.md5(appkey + time + getSessid());
            return sign;

        }

        function setSessid(value,value2) {
            var encodeId = encodeURIComponent(value);
            var sessIdString = getSessid();
            if (isStringNotEmpty(value) && (encodeId != sessIdString)) {
                sessid = encodeId;
                avalon.cookie.set("sessId", sessid);
                avalon.cookie.set("nick", value2);
            }
        }

        function removeSessid() {
            sessid = '';
            avalon.cookie.remove("sessId");
        }

        var getDefaultParams = function() {
            return {
                sessid: getSessid(),
                appid: 10002,
                v: '1.0.1',
                ct: 1,
                did: '',
                os: '',
                nm: '',
                mno: '',
                dm: '',
                time: getTimeStamp(),
                sign: getSign()
            };
        };

        function isStringNotEmpty(string) {
            return isString(string) && (string.replace(/(^s*)|(s*$)/g, "").length > 0);
        }

        function isString(value) {return typeof value === 'string';}

        var getBaseApiUrl = function(){
            return 'http://'+window.location.host+'/apps/api/www/';
        };
        var getBaseWebappUrl = function(){
            return 'http://'+window.location.host+'/apps/webapp/www/';

        };

        var getBasePcUrl = function() { 
          return 'http://'+window.location.host+'/pc/';
        }

        function get(extraUrl,extraParams,onSuccess,onFailed,onFinal){
          var url = getBaseApiUrl();
          if(extraUrl.indexOf("http")>=0){
            if(isString(extraUrl)){
              url=extraUrl;
            }
          }else{
            if(isString(extraUrl)){
              url+=extraUrl;
            }
          }
          var params = getDefaultParams();
          for(var key in extraParams){
            params[key] = extraParams[key];
          }
          
          return requestBase('GET',url,params,onSuccess,onFailed,onFinal);
        }

        function post(extraUrl,data,onSuccess,onFailed,onFinal){
          var url = getBaseApiUrl();
          if(extraUrl.indexOf("http")>=0){
            if(isString(extraUrl)){
              url=extraUrl;
            }
          }else{
            if(isString(extraUrl)){
              url+=extraUrl;
            }
          }
          var params = getDefaultParams();
          for(var i in params) {
            url += '&' + i + '=' + params[i];
          }
          return requestBase('POST',url,data,onSuccess,onFailed,onFinal);
        }

        function requestBase(type,url,data,onSuccess,onFailed,onFinal) {
          $.ajax({
            url: url,
            type: type,
            // dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
            data: data
          })
          .done( function(data) {
            var code;
            try {
              code= JSON.parse(data).code;
            } catch (e) {
              
            }
             
            // if(code == 0){
            //   try {
            //     var saveSessid = JSON.parse(data).sessid;
            //     setSessid(saveSessid);
            //   } catch (e) {
            //     avalon.error('sessionId 保存出错：'+ e.name+"："+ e.message);
            //   }
            //   // Global.setNoticeNew(data.new);
            // } else
            if(code == 6) {
              avalon.router.go('login')
              return;
            }
            onSuccess(data);
          })
          .fail(onFailed)
          .always(onFinal);
          
        }

       

        return {
            getDefaultParams: getDefaultParams,
            getBaseApiUrl:getBaseApiUrl,
            getBaseWebappUrl:getBaseWebappUrl,
            get:get,
            post:post,
            setSessid: setSessid,
            getSessid: getSessid,
            getTimeStamp: getTimeStamp,
            getFormMd5: getFormMd5,
            isLogin: isLogin,
            removeSessid: removeSessid,
            getBasePcUrl:getBasePcUrl
        }


})
