define(['avalon', 'http/http-factory', 'lib/fileuploador/fileuploador', 'css!../../lib/layer/skin/layer.css'],
    function(avalon, httpFactory,Uploador) {
        var sign = avalon.define({
            $id: "signCtrl",
            userData: {},
            hasSign: false,
            signFinish: true,
            passwordObject: {
                oldPassword: '',
                newPassPassword: '',
                newPasswordAgain: ''
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
                    } else {
                        layer.msg(re.msg);
                    }
                }, function(err) {

                }, function() {
                    sign.signFinish = true;
                })
            },
            init_changePwd_box: function() {
                var a = $(window).width(),
                    c = $(window).height();
                var bg = $(".a_world_bg");
                var changePwd_box = $(".js-changePwd");
                changePwd_box.hide();
                bg.hide();
                bg.css({ height: c + "px" });
                changePwd_box.css({ left: (a - changePwd_box.outerWidth()) / 2 + "px", top: (c - changePwd_box.outerHeight()) / 2 + "px" });
                changePwd_box.show();
                bg.show();
            },
            hidePwdBox: function() {
                var bg = $(".a_world_bg");
                var changePwd_box = $(".js-changePwd");
                changePwd_box.hide();
                bg.hide();
                sign.passwordObject.oldPassword = '';
                sign.passwordObject.newPassPassword = '';
                sign.passwordObject.newPasswordAgain = '';
            },
            changePwd: function() {
                var oldPassword = sign.passwordObject.oldPassword;
                var newPassPassword = sign.passwordObject.newPassPassword;
                var newPasswordAgain = sign.passwordObject.newPasswordAgain;
                if (newPassPassword == newPasswordAgain) {
                    if (oldPassword == newPassPassword) {
                        layer.msg('新密码与旧密码不能一样!');

                    } else {
                        httpFactory.changePwd(oldPassword, newPassPassword, function(re) {
                            re = JSON.parse(re);
                            if (re.code === 0) {
                                console.log(re);
                                sign.hidePwdBox();
                                layer.msg('密码修改成功，请重新登录');
                                avalon.router.go('login');
                            } else {
                                layer.msg(re.msg);
                            }
                        })
                    }
                } else {
                    layer.msg('两次输入新密码不相同！');
                    return;
                }
            },
            modifyNick:function() {
                layer.prompt({
                  formType: 0,
                  value: '',
                  title: '请输入昵称'
                }, function(value, index, elem){
                    modifyNick(value)
                });
            },
            upLoadImage:function() {

            },
            iconHover:function() {
                if($('#imgWrap').find('.input-file').length > 0) {
                    return;
                } else {
                    $('#imgWrap').append($(uploador.submitForm));
                    $(uploador.fileInput).addClass("input-file").prop('title','修改头像').prop('name','filename');
                }
            }
        })
        
        function modifyNick(nick) {
            httpFactory.modifyNick(nick,function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    layer.msg('修改昵称成功')
                    sign.userData.nick = nick
                } else {
                    layer.msg(re.msg)
                }
            },function(re) {
                layer.msg(JSON.parse(re).msg)
            })
        }
        
        //获取用户个人信息
        function getLogin() {
            httpFactory.noParams('getLogin',function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    sign.userData = re.data;
                    setTimeout(function() {
                         $('#imgWrap').append($(uploador.submitForm));
                         $(uploador.fileInput).addClass("input-file").prop('title','修改头像').prop('name','filename');
                    })
                }
            })
        }
        
       

        var params = httpFactory.getDefaultParams();
        var url =  httpFactory.getBaseApiUrl() + '?c=user&a=upload_icon2';
        for(var i in params) {
            url += '&' + i + '=' + params[i];
        }

        var uploador = new Uploador({
            accept: "image/jpeg,image/png,image/gif",
            submitUrl: url
        });
      
        // 添加要上传的图片
        uploador.on("uploadstart", function() {
            layer.msg('头像上传中')
        }).on('finish', function(re) {
            re = JSON.parse(re);
            if (re.code == 0) {
                sign.userData.icon = re.data.icon;
                layer.msg('头像上传成功');
            } else {
                layer.msg(re.msg);
            }
        });
        
        $(window).resize(function() {
            if ($(".js-changePwd").css('display') == 'block') {
                sign.init_changePwd_box();
            }
        });

        getLogin()

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {

            }
            $ctrl.$onBeforeUnload = function() {}

            // 指定一个avalon.scan视图的vmodels，vmodels = $ctrl.$vmodels.concact(DOM树上下文vmodels)
            $ctrl.$vmodels = []

        })
    })
