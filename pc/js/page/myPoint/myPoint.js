define(['avalon', 'http/http-factory','components/view-left-side/left', 'css!../../../css/selfInfo/member.min.css','page/userMes/userMes'],
    function(avalon, httpFactory) {

        var myPoint = avalon.define({
                $id: "myPointCtrl",
                $leftSideOpts:{
                    activePage:'myPoint'
                },
                totalPoint: {},
                data: [],
                page: 1,
                hasNextPage: true,
                isLoading: false,
                getNextPage: function() {
                    if (myPoint.hasNextPage && !myPoint.isLoading) {
                        myPoint.page++;
                        getMyPoint(myPoint.page);
                    } else {
                        return;
                    }
                },
                getPrevPage: function() {
                    if (myPoint.page > 1) {
                        myPoint.page--;
                        getMyPoint(myPoint.page);
                    } else {
                        return;
                    }
                }
            })
            //(myPoint.page - 1)*myPoint.pageCount + 1
        function getMyPoint(page) {
            myPoint.isLoading = true;
            httpFactory.onlyPageParams('getMyPoint',page, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    myPoint.data = re.data;
                    if (myPoint.data.length == 10) {
                        myPoint.hasNextPage = true;
                    } else {
                        myPoint.hasNextPage = false;
                    }
                } else {
                    layer.msg(re.msg);
                }
            }, function(err) {

            }, function() {
                myPoint.isLoading = false;
            })
        }
        

        function getMyPointTotal() {
            httpFactory.noParams('getMyPointTotal',function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    console.log(re);
                    myPoint.totalPoint = re.data;
                    avalon.cookie.set('totalPoint',re.data);
                } else {
                    layer.msg(re.msg);
                }
            }, function(err) {

            }, function() {
                
            })
        }

        
        

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'myPoint';
                var totalPoint =avalon.cookie.get(totalPoint);
                if (totalPoint) {
                    myPoint.totalPoint = totalPoint;
                }else{
                    getMyPointTotal();
                }
                myPoint.page = 1;
                getMyPoint(myPoint.page);
            }
            $ctrl.$onBeforeUnload = function() {}

            // 指定一个avalon.scan视图的vmodels，vmodels = $ctrl.$vmodels.concact(DOM树上下文vmodels)
            $ctrl.$vmodels = []

        })
    })
