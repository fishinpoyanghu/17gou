define(['avalon', 'http/http-factory'],
    function(avalon, httpFactory) {
        var redpacket = avalon.define({
            $id: "redpacketCtrl",
            redList: [],
            redDetail: {},
            page:1,
            isLoading:false,
            join: function(data) {
                if (!httpFactory.isLogin()) {
                    avalon.router.go('login');
                    return;
                } 
                if (data.flag != 0) {
                    layer.msg('该红包已经不能参与~');
                    return;
                }
                // if (data.already != 0) {
                //     layer.msg('您已经参与过该红包了~');
                //     return;
                // }
                joinRed(data.activity_id);
            },
            pageOver:true,
            prevPage:function() {
                if(redpacket.page <= 1) return;
                if(redpacket.isLoading) return;
                redpacket.page--;
                getRedList()
            },
            nextPage:function() {
                if(redpacket.pageOver) return;
                if(redpacket.isLoading) return;
                redpacket.page++;
                getRedList()
            },
            packetDetails:function(packet) {
                $('#packetModal').modal('show')
                packetDetails(packet.activity_id)
            },
            isGetDetails:false
        })
        
        function packetDetails (activity_id) {
            redpacket.isGetDetails = true;
            httpFactory.grabRedPacket(activity_id, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    redpacket.redDetail = re.data;
                } else {
                    layer.msg(re.msg);
                }
            }, function(err) {
                layer.msg('获取红包详情失败');
            }, function() {
                redpacket.isGetDetails = false;
            })
        }

        function joinRed(activity_id) {
            httpFactory.joinRed(activity_id, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    joinWait(re.data.order_id);
                } else {
                    layer.msg(re.msg);
                }
            }, function(err) {
                layer.msg('参与红包失败');
            }, function() {

            })
        }

        function joinWait(order_id) {
            httpFactory.joinWait(order_id, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    timer && clearTimeout(timer);
                    redpacket.redDetail.already = 1;
                    redpacket.redDetail.lucky_num = re.data.lucky_num;
                    redpacket.redDetail.user_num = Number(redpacket.redDetail.user_num) + 1;
                    layer.confirm('抢红包成功,幸运号为' + re.data.lucky_num, {
                        btn: ['确定']
                    });
                    getRedList();
                } else if (re.code == 2) {
                    timer = setTimeout(function() {
                        joinWait(order_id);
                    }, 1000)
                } else {
                    layer.msg(re.msg);
                }
            }, function(err) {
                layer.msg('参与红包失败');
            })
        }

        function getRedList() {
            redpacket.isLoading = true;
            httpFactory.onlyPageParams('getRedList',redpacket.page, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    redpacket.redList = re.data;
                    if(re.data.length == 10) {
                        redpacket.pageOver = false;
                    } else {
                        redpacket.pageOver = true;
                    }
                } else {
                    layer.msg(re.msg)
                }
            },function(re) {
                re = JSON.parse(re);
                layer.msg(re.msg)
            },function() {
                redpacket.isLoading = false;
            })
        }

        

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'redpacket';
                redpacket.page = 1;
                getRedList();
            }

            $ctrl.$onBeforeUnload = function() {}

            $ctrl.$vmodels = []

        })
    })
