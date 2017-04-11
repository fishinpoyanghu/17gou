define(['avalon', 'http/http-factory', 'css!../../lib/layer/skin/layer.css'],
    function(avalon, httpFactory) {

        var reg = avalon.define({
            $id: "regCtrl",
            codeTxt: "点击获取验证码",
            checked: false,
            data: {
                phone: '',
                nickname: '',
                picCode: '',
                password1: '',
                password2: '',
                inviteCode: avalon.cookie.get('inviteCode') || '',
                mcode: '',
            },
            goLogin: function() {

                avalon.router.go('login');
            },
            picCode: httpFactory.getBaseApiUrl() + "?c=user&a=code&t=" + (+new Date()),
            changeGetCodeUrl: function() {
                reg.picCode = httpFactory.getBaseApiUrl() + "?c=user&a=code&t=" + (+new Date());
            },
            getMesCode: function() {
                var phone = reg.data.phone;
                var picCode = reg.data.picCode;
                if (phone == "") {
                    layer.msg('手机号不能为空');
                    return;
                }
                if (!/^(13|18|15|14|17)\d{9}$/i.test(phone)) {
                    layer.msg('手机号格式错误');
                    return;
                }

                if (reg.codeTxt != '点击获取验证码') {
                    return;
                }
                httpFactory.getRegisterSms(phone, picCode, function(re) {
                    re = JSON.parse(re);
                    if (re.code == 0) {
                        var startTime = 0;
                        var endTime = 60;
                        var timer = setInterval(function() {
                            if (endTime - startTime > 0) {
                                reg.codeTxt = endTime - startTime + '秒后重新获取';
                                startTime++;
                            } else {
                                reg.codeTxt = '点击获取验证码';
                                clearInterval(timer);
                                reg.changeGetCodeUrl();
                            }
                        }, 1000);
                    } else {
                        layer.msg(re.msg);
                        reg.changeGetCodeUrl();
                        reg.data.picCode = '';
                    }

                }, function(err) {

                });
            },
            doRegister: function() {
                var phone = reg.data.phone;
                var password = reg.data.password1;
                var mcode = reg.data.mcode;
                var inviteCode = reg.data.inviteCode;
                var nickname = reg.data.nickname;
                if (phone == "") {
                    layer.msg('手机号不能为空');
                    return;
                }
                if (nickname == "") {
                    layer.msg('昵称不能为空');
                    return;
                }
                if (!/^(13|18|15|14|17)\d{9}$/i.test(phone)) {
                    layer.msg('手机号格式错误');
                    return;
                }
                if (password == '') {
                    layer.msg('密码不能为空');
                    return;
                }
                if (password.length < 6) {
                    layer.msg('密码不能少于6位');
                    return;
                }
                if (reg.data.password1 != reg.data.password2) {
                    layer.msg('密码输入不一致');
                    return;
                }
                httpFactory.register(phone, password, '1', nickname, mcode, inviteCode, '', function(re) {
                    re = JSON.parse(re);
                    console.log(re);
                    if (re.code == 0) {
                        // httpFactory.setSessid(re.sessid);
                        layer.msg('注册成功');
                        avalon.router.go('login');

                    } else {
                        layer.msg(re.msg);
                        reg.changeGetCodeUrl();
                        reg.codeTxt = '点击获取验证码';
                    }

                }, function(err) {

                });
            }
        })

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function(state) {
                if(state.params.inviteCode) { 
                    avalon.cookie.set('inviteCode',state.params.inviteCode.split('#')[0]);
                    reg.data.inviteCode=state.params.inviteCode.split('#')[0];
                }
                avalon.vmodels["headerEidget"].activePage = 'reg';
                if(httpFactory.isLogin()){
                    avalon.router.go('index');
                }
            }
            $ctrl.$onBeforeUnload = function() {}

            // 指定一个avalon.scan视图的vmodels，vmodels = $ctrl.$vmodels.concact(DOM树上下文vmodels)
            $ctrl.$vmodels = []

        })
    })
