define(['avalon', 'http/http-factory', 'layer', 'css!../../lib/layer/skin/layer.css'],
    function(avalon, httpFactory, layer) {
        var sign = avalon.define({
            $id: "signCtrl",
            data: {},
            hasSign: false,
            signFinish: true,
            showPwdBox:false,
            passwordObject:{
                oldPassword:'',
                newPassPassword:'',
                newPasswordAgain:''
            },
            sign: function() {
                if (!sign.signFinish) return;
                if (sign.hasSign) {
                    layer.msg('你已经签到过了');
                    return;
                }
                sign.signFinish = false;
                httpFactory.noParams('sign',function(re) {
                    re = JSON.parse(re);
                    if (re.code == 0) {
                        layer.msg('签到成功');
                        sign.hasSign = true;
                        getMyPoint();
                    } else {
                        layer.msg(re.msg);
                    }
                }, function(err) {

                }, function() {
                    sign.signFinish = true;
                })
            },
            showBox:function(){
                sign.showPwdBox = true;
            },
            hidePwdBox:function(){
                sign.showPwdBox = false;
                sign.passwordObject.oldPassword='';
                sign.passwordObject.newPassPassword='';
                sign.passwordObject.newPasswordAgain='';
            },
            changePwd:function(){
                var oldPassword = sign.passwordObject.oldPassword;
                var newPassPassword = sign.passwordObject.newPassPassword;
                var newPasswordAgain = sign.passwordObject.newPasswordAgain;
                if (newPassPassword==newPasswordAgain) {
                    if(oldPassword==newPassPassword) {
                        layer.msg('新密码与旧密码不能一样!');

                    }else{
                        httpFactory.changePwd(oldPassword,newPassPassword,function(re){
                            re = JSON.parse(re);
                            if(re.code === 0) {
                                console.log(re);                   layer.msg('密码修改成功，请重新登录');
                                sign.hidePwdBox();
                                avalon.router.go('login');
                            }else{
                                layer.msg(re.msg);
                            }
                        })
                    }
                }else{
                    layer.msg('两次输入新密码不相同！');
                    return;
                }
            }
        })

        function getMyPoint() {
            httpFactory.getMyPoint(function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    console.log('我的积分');
                    console.log(re);
                    sign.data = re.data;
                } else {
                    layer.msg(re.msg);
                }
            })
        }

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {

            }
            $ctrl.$onBeforeUnload = function() {}

            // 指定一个avalon.scan视图的vmodels，vmodels = $ctrl.$vmodels.concact(DOM树上下文vmodels)
            $ctrl.$vmodels = []

        })
    })
