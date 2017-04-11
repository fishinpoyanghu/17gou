define(['avalon', 'http/http-factory'],
    function(avalon, httpFactory) {

        var findPwd = avalon.define({
            $id: "findPwdCtrl",
            codeTxt: '点击获取验证码',
            data: {
                phone: '',
                picCode: '',
                 mcode: '',
                password1: '',
                password2: '',
                savekey:''
            },
            picCode: httpFactory.getBaseApiUrl() + "?c=user&a=code&t=" + (+new Date()),
            changeGetCodeUrl: function() {
                findPwd.picCode = httpFactory.getBaseApiUrl() + "?c=user&a=code&t=" + (+new Date());
            },
            getMesCode: function() {
                var phone = findPwd.data.phone;
                var picCode = findPwd.data.picCode;
                if (phone == "") {
                    layer.msg('手机号不能为空');
                    return;
                }
                if (!/^(13|18|15|14|17)\d{9}$/i.test(phone)) {
                    layer.msg('手机号格式错误');
                    return;
                }

                if (findPwd.codeTxt != '点击获取验证码') {
                    return;
                }
                httpFactory.sendForgotMcode(phone, picCode, function(re) {
                    re = JSON.parse(re);
                    if (re.code == 0) {
                        var startTime = 0;
                        var endTime = 60;
                        var timer = setInterval(function() {
                            if (endTime - startTime > 0) {
                                findPwd.codeTxt = endTime - startTime + '秒后重新获取';
                                startTime++;
                            } else {
                                findPwd.codeTxt = '点击获取验证码';
                                clearInterval(timer);
                                findPwd.changeGetCodeUrl();
                            }
                        }, 1000);
                    } else {
                        layer.msg(re.msg);
                        findPwd.changeGetCodeUrl();
                    }
                })
            },
            doFindPwd:function(){
                var phone = findPwd.data.phone;
                var mcode = findPwd.data.mcode;
                var password = findPwd.data.password1;
                if (password!=findPwd.data.password2) {
                    layer.msg('密码输入不一致');
                    return;
                }
                if(password == '' ) {
                    layer.msg('密码不能为空');
                    return;
                }
                httpFactory.checkForgotMcode(phone,mcode,function(re){
                    re = JSON.parse(re);
                    console.log('checkcode');
                    if(re.code ==0 ) {
                        findPwd.data.savekey = re.data.savekey;
                        httpFactory.saveForgotPwd(findPwd.data.savekey,password,function(re){
                            re = JSON.parse(re);
                            console.log(re);
                            if(re.code==0) {
                                layer.msg('操作成功!');
                                avalon.router.go('login');
                                findPwd.codeTxt = '点击获取验证码';
                            }else{
                                layer.msg(re.msg);
                                findPwd.codeTxt = '点击获取验证码';
                                findPwd.changeGetCodeUrl();
                            }
                        })
                    }else{
                        layer.msg(re.msg);
                        findPwd.changeGetCodeUrl();
                    }
                })
            }
        })

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'findPwd';
            }
            $ctrl.$onBeforeUnload = function() {}

            // 指定一个avalon.scan视图的vmodels，vmodels = $ctrl.$vmodels.concact(DOM树上下文vmodels)
            $ctrl.$vmodels = []

        })
    })
