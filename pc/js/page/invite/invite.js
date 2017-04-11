define(['avalon','http/http-factory','utils/clipboard','page/userMes/userMes','components/view-left-side/left','css!../../../css/selfInfo/member_comm.css','css!../../../css/selfInfo/member.min.css'],
    function(avalon,httpFactory,Clipboard) {
        
        var invite = avalon.define({
            $id: "inviteCtrl",
            $leftSideOpts:{
                activePage:'invite'
            },
            goInviteData: {},
            recordData:[],
            li_index:1,
            isInGetCode:false,
            isGetInviteUser:false,
            hasGetInviteUser:false,
            shareData:{},
            hasGetShareData:false,
            hasGetCode:false,
            changeLi:function(index) {
                invite.li_index = index;
                if(index == 2 && !invite.hasGetInviteUser) {
                    getInviteUser()
                }
            },
            getBasePcUrl:httpFactory.getBasePcUrl() ,
            copyLink:function(nodeId) {
                if(invite.isInGetCode) {
                    layer.msg('正在获取邀请码中，请稍后再操作')
                    return;
                }
                var clipboard = new Clipboard(nodeId);
                clipboard.on('success', function(e) {
                    e.clearSelection();
                    layer.msg('复制成功');
                });
                clipboard.on('error', function(e) {
                    layer.msg('复制失败，请手动复制');
                });
            }
          
        })
        
        function initShare() {
            window._bd_share_config = {
                "common": {
                    "bdSnsKey": {
                        "tsina": "989077055",
                        "tqq": "1105435002"
                    },
                    "bdText": invite.shareData.title,
                    "bdDes":invite.shareData.sub_title,
                    "bdMini": "1",
                    "bdMiniList": false,
                    "bdPic": httpFactory.getBasePcUrl() + "img/share_icon.jpg",
                    "bdUrl":httpFactory.getBasePcUrl() + '#!/bootstrap/' + invite.goInviteData.invite_code,
                    "bdStyle": "0",
                    "bdSize": "32"
                },
                "share": {}
            };
            with(document) 0[(getElementsByTagName('head')[0] || body).appendChild(createElement('script')).src = 'http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion=' + ~(-new Date() / 36e5)];
        }
        

        function getShare() {
            
            httpFactory.noParams('getShare',function(re){
                re = JSON.parse(re);
                if(re.code==0) {
                    invite.shareData = re.data;
                    invite.hasGetShareData = true;
                    if(invite.hasGetCode) initShare()
                }else{
                    layer.msg(re.msg);
                }
            },function(re) {
                re = JSON.parse(re);
                layer.msg(re.msg);
            },function() {
                
            })
        }

        function getInviteData() {
            invite.isInGetCode = true;
            httpFactory.noParams('getInviteData',function(re){
                re = JSON.parse(re);
                if(re.code==0) {
                    invite.goInviteData = re.data;
                    invite.hasGetCode = true;
                    if(invite.hasGetShareData) initShare()
                    
                }else{
                    layer.msg(re.msg);
                }
            },function(re) {
                re = JSON.parse(re);
                layer.msg(re.msg);
            },function() {
                invite.isInGetCode = false;
            })
        }
        

        function getInviteUser() {
            invite.isGetInviteUser = true;
            httpFactory.noParams('getInviteRecord',function(re){
                re = JSON.parse(re);
                if(re.code==0) {
                    invite.recordData = re.data;
                }else{
                    layer.msg(re.msg);
                }
            },function(re) {
                re = JSON.parse(re);
                layer.msg(re.msg);
            },function() {
                invite.isGetInviteUser = false;
                invite.hasGetInviteUser = true;
            })
        }
        
        getInviteData()
        getShare()
        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'invite';
            }
            $ctrl.$onBeforeUnload = function() {}

            $ctrl.$vmodels = []

        })
    })
