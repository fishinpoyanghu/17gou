define(['avalon', 'http/http-factory', 'css!../../lib/layer/skin/layer.css'],
    function(avalon, httpFactory) {

        var login = avalon.define({
            $id: "loginCtrl",
            error_tips:'',
            phone:'',
            password:'',
            isInLogin:false,
            loginSubmit2:function() {
                logIn2()
            },
            enter:function(e){
                if(e.keyCode == 13) {
                    logIn2()
                }
            }
        });

        function logIn2() {
            if(login.isInLogin) return;
            var phone = login.phone,
                password = login.password;
            if(phone == '') {
                login.error_tips = '手机号不能为空';
                return;
            }
            // if(!/^(13|18|15|14|17)\d{9}$/i.test(phone)) {
            //     login.error_tips = '手机号格式不正确';
            //     return;
            // }
            if(password == '') {
                login.error_tips = '密码不能为空';
                return;
            }
            login.error_tips = '';
            login.isInLogin = true;
            httpFactory.login(phone,password,function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    httpFactory.setSessid(re.sessid,re.data.nick);
                    avalon.vmodels["headerEidget"].isLogin = true;
                    if(window.history.length > 1){
                        history.back();
                    }else{
                        avalon.router.go('index');
                    }
                } else {
                    login.error_tips = re.msg;
                }
            }, function(err) {
                login.error_tips = err.msg;
            },function() {
                login.isInLogin = false;
            });
        }


        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'login';
                avalon.vmodels["footEidget"].hideFooterIn = true;
                if(httpFactory.isLogin()){
                    avalon.router.go('index');
                }
            }
            $ctrl.$onBeforeUnload = function() {}

            // 指定一个avalon.scan视图的vmodels，vmodels = $ctrl.$vmodels.concact(DOM树上下文vmodels)
            $ctrl.$vmodels = []

        })
    })
