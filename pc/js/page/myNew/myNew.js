define(['avalon', 'http/http-factory', 'layer','components/view-left-side/left', 'css!../../../css/page.css', 'css!../../../css/selfInfo/member.min.css', 'page/userMes/userMes'],
    function(avalon, httpFactory, layer) {

        var myNew = avalon.define({
            $id: "myNewCtrl",
            $leftSideOpts:{
                activePage:'myNew'
            },
            systemData: [],
            notifyData: [],
            isLoading: false,
            page: 1,
            pageCount: 10,
            hasNextPage: false,
            li_index: 1,
            getNextPage: function() {
                if (myNew.hasNextPage && !myNew.isLoading) {
                    myNew.page++;
                    if (myNew.li_index == 1) {
                        getMyNew();
                    } else {
                        getSysList();
                    }
                } 
            },
            getPrevPage: function() {
                if (myNew.page > 1 && !myNew.isLoading) {
                    myNew.page--;
                    if (myNew.li_index == 1) {
                        getMyNew();
                    } else {
                        getSysList();
                    }
                } 
            },
            changeLi: function(index) {
                myNew.li_index = index;
                myNew.page = 1;
                if (index == 1) {
                    getMyNew();
                } else {
                    getSysList();
                }
            }
        })

        function getMyNew() {
            myNew.isLoading = true;
            httpFactory.getMyNew(0, (myNew.page - 1) * myNew.pageCount + 1, myNew.pageCount, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    myNew.notifyData = re.data;
                    if (myNew.notifyData.length == myNew.pageCount) {
                        myNew.hasNextPage = true;
                    } else {
                        myNew.hasNextPage = false;
                    }
                } else {
                    layer.msg(re.msg)
                }
            }, function(err) {
                layer.msg(JSON.parse(err).msg)
            }, function() {
                myNew.isLoading = false;
            })
        }

        function getSysList() {
            myNew.isLoading = true;
            httpFactory.getSysList((myNew.page - 1) * myNew.pageCount + 1, myNew.pageCount, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    myNew.systemData = re.data;
                    if (myNew.systemData.length == myNew.pageCount) {
                        myNew.hasNextPage = true;
                    } else {
                        myNew.hasNextPage = false;
                    }
                } else {
                    layer.msg(re.msg)
                }
            }, function(err) {
                layer.msg(JSON.parse(err).msg)
            }, function() {
                myNew.isLoading = false;
            })
        }

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'myNew';
                myNew.page = 1;
                myNew.li_index = 1
                getMyNew();
            }
            $ctrl.$onBeforeUnload = function() {}

            $ctrl.$vmodels = []

        })
    })
