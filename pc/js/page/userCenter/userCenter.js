define(['avalon', 'http/http-factory','components/view-left-side/left','css!../../../css/selfInfo/member.min.css','page/userMes/userMes'], 
    function(avalon, httpFactory) {

        var userCenterViewModel = avalon.define({
            $id: "userCenterCtrl",
            $leftSideOpts:{
                activePage:'userCenter'
            },
            inviteInfo:'0.00',
            money:'0.00',
            point:'0.00',
            isLoadingMoney:false,
            isLoadingInviteInfo:false,
            isLoadingPoint:false
            
        })

        function getLogin() {
            userCenterViewModel.isLoadingMoney = true;
            httpFactory.noParams('getLogin',function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    userCenterViewModel.money = re.data.money;
                } else {
                    layer.msg(re.msg)
                }
            },function(re) {
                layer.msg(JSON.parse(re).msg)
            },function() {
                userCenterViewModel.isLoadingMoney = false;
            })
        }

        function getInviteInfo() {
            userCenterViewModel.isLoadingInviteInfo = true;
            httpFactory.noParams('getInviteInfo',function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    userCenterViewModel.inviteInfo = re.data.money;
                } else {
                    layer.msg(re.msg)
                }
            },function(re) {
                layer.msg(JSON.parse(re).msg)
            },function() {
                userCenterViewModel.isLoadingInviteInfo = false;
            })
        }

        function getPoint() {
            userCenterViewModel.isLoadingPoint = true;
            httpFactory.noParams('getPoint',function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    userCenterViewModel.point = re.data.point;
                } else {
                    layer.msg(re.msg)
                }
            },function(re) {
                layer.msg(JSON.parse(re).msg)
            },function() {
                userCenterViewModel.isLoadingPoint = false;
            })
        }

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'userCenter';
                setTimeout(getLogin,100)
                getInviteInfo()
                getPoint()
            }
            $ctrl.$onBeforeUnload = function() {}

            $ctrl.$vmodels = []

        })
    })
