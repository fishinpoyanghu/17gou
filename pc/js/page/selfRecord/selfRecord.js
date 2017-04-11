define(['avalon', 'http/http-factory', 'css!../../../css/selfInfo/member.min.css'],
    function(avalon, httpFactory) {

        var selfRecord = avalon.define({
            $id: "selfRecordCtrl",
            uid: '',
            buyData: [],
            winData:[],
            li_index: 1,
            page: 1,
            pageCount: 10,
            isLoading: false,
            hasNextPage: false,
            getNextPage: function() {
                if (selfRecord.hasNextPage && !selfRecord.isLoading) {
                    selfRecord.page++;
                    if(selfRecord.li_index == 1) {
                        getRecordList();
                    } else {
                        getWinRecordList()
                    }
                    
                } else {
                    return;
                }
            },
            getPrevPage: function() {
                if (selfRecord.page > 1) {
                    selfRecord.page--;
                    if(selfRecord.li_index == 1) {
                        getRecordList();
                    } else {
                        getWinRecordList()
                    }
                } else {
                    return;
                }
            },
            changeLi: function(index) {
                selfRecord.li_index = index;
                selfRecord.page =1 ;
                if (index == 1) {
                    getRecordList();
                } else {
                    getWinRecordList();
                }
            },
            isInGetNum:false,
            myNum:[],
            checkNum: function(activity_id) {
                $('#numModal').modal('show')
                selfRecord.isInGetNum = true;
                httpFactory.getRecordListNum(activity_id, selfRecord.uid, null, function(re) {
                    re = JSON.parse(re)
                    if (re.code == 0) {
                        selfRecord.myNum = re.data;
                    } else {

                    }

                }, function(err) {

                },function() {
                    selfRecord.isInGetNum = false;
                });
            }

        })

        //购买记录
        function getRecordList() {
            selfRecord.isLoading = true;
            httpFactory.getRecordList(selfRecord.uid, (selfRecord.page - 1) * selfRecord.pageCount + 1, selfRecord.pageCount, null, function(re) {
                re = JSON.parse(re);
                console.log(re);
                if (re.code == 0) {
                    selfRecord.buyData = [];
                    selfRecord.buyData = re.data;
                    if (selfRecord.buyData.length == selfRecord.pageCount) {
                        selfRecord.hasNextPage = true;
                    } else {
                        selfRecord.hasNextPage = false;
                    }
                } else {
                    layer.msg(re.msg);
                }
            }, function(err) {

            }, function() {
                selfRecord.isLoading = false;
            })
        }

        //中奖记录
        function getWinRecordList() {
            selfRecord.isLoading = true;
            httpFactory.getWinRecordList(selfRecord.uid,null,null,(selfRecord.page - 1) * selfRecord.pageCount + 1, selfRecord.pageCount, function(re) {
                re = JSON.parse(re);
                console.log(re);
                if (re.code == 0) {
                    selfRecord.winData = re.data;
                    if (selfRecord.winData.length == selfRecord.pageCount) {
                        selfRecord.hasNextPage = true;
                    } else {
                        selfRecord.hasNextPage = false;
                    }
                } else {
                    layer.msg(re.msg);
                }
            }, function(err) {

            }, function() {
                selfRecord.isLoading = false;
            })
        }


        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function(state) {
                avalon.vmodels["headerEidget"].activePage = 'selfRecord';
                selfRecord.uid = state.params.uid;
                getRecordList();
                
            }
            $ctrl.$onBeforeUnload = function() {}

            // 指定一个avalon.scan视图的vmodels，vmodels = $ctrl.$vmodels.concact(DOM树上下文vmodels)
            $ctrl.$vmodels = []

        })
    })
