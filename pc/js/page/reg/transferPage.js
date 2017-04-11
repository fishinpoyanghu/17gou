define(['avalon', 'http/http-factory', 'css!../../lib/layer/skin/layer.css'],
    function(avalon, httpFactory) {

        var reg = avalon.define({
            $id: "transferPageCtrl",
            inviteCode: avalon.cookie.get('inviteCode') || '',
            isLoading:false,
            sessid:'',
            isBinding:false,
            doBind:function() {
                if(reg.isBinding) return;
                if(!reg.inviteCode) return;
                doBind()
            }
        })

        function doBind() {
            reg.isBinding = true;
            httpFactory.bindInviteCode(reg.inviteCode, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    layer.msg('绑定成功')
                    avalon.cookie.remove('inviteCode');
                    avalon.router.go('index')
                } else {
                    layer.msg(re.msg);
                }

            }, function(re) {
                layer.msg(JSON.parse(re).msg);
            },function() {
                reg.isBinding = false;
            });
        }

        function getLogin() {
            reg.isLoading = true;
            httpFactory.noParams('getLogin',function(re){
                re = JSON.parse(re);
                if(re.code == 0) {
                    var data = re.data;
                    // httpFactory.setSessid(reg.sessid,data.nick);
                    avalon.cookie.set('nick',data.nick)
                    avalon.vmodels["headerEidget"].nick = data.nick;
                    if(data.rebate_uid == 0){
                        if(reg.inviteCode) {
                            doBind()
                        }
                    }else{
                        //跳转到首页
                        avalon.router.go('index')
                    }
                }else{
                    layer.msg(re.msg);
                }
            },function(re) {
                re = JSON.parse(re);
                layer.msg(re.msg);
            },function() {
                reg.isLoading = false;
            })
        }

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function($state) {
                avalon.vmodels["headerEidget"].activePage = 'reg';
                reg.sessid =  $state.params.sessid;
                httpFactory.setSessid(reg.sessid,'');
                getLogin();
            }
            $ctrl.$onBeforeUnload = function() {}

            $ctrl.$vmodels = []

        })
    })
