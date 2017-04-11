define(['avalon', 'http/http-factory', 'layer','components/view-left-side/left', 'css!../../../css/page.css', 'css!../../../css/selfInfo/member.min.css','page/userMes/userMes'],
    function(avalon, httpFactory, layer) {

        var balance = avalon.define({
            $id: "balanceCtrl",
            $leftSideOpts:{
                activePage:'balance_detail'
            },
            title:{},
            data: [],
            isLoading: false,
            page: 1,
            pageCount: 10,
            hasNextPage: false,
            getNextPage: function() {
                if (balance.hasNextPage && !balance.isLoading) {
                    balance.page++;
                    balanceDeTail(balance.page);
                } else {
                    return;
                }
            },
            getPrevPage: function() {
                if (balance.page > 1 && !balance.isLoading) {
                    balance.page--;
                    balanceDeTail(balance.page);
                } else {
                    return;
                }
            }
        })

        function balanceDeTail(page) {
            balance.isLoading = true;
            httpFactory.onlyPageParams('balanceDetail',balance.page,function(re) {
                
                re = JSON.parse(re);
                console.log(re);
                if (re.code == 0) {
                    balance.data = re.data;
                    if (balance.data.length == balance.pageCount) {
                        balance.hasNextPage = true;
                    } else {
                        balance.hasNextPage = false;
                    }
                } else {
                    layer.msg(re.msg)
                }
            },function(err){

            },function(){
                balance.isLoading = false;
            })
        }

        function balanceTotal() {
            httpFactory.noParams('balanceTotal',function(re) {
                balance.isLoading = true;
                re = JSON.parse(re);
                console.log(re);
                if (re.code == 0) {
                    balance.title=re.data;
                } else {
                    layer.msg(re.msg)
                }
            },function(err){

            },function(){
                balance.isLoading = false;
            })
        }
        
        balanceTotal();
        balanceDeTail(balance.page);

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'balance_detail';
            }
            $ctrl.$onBeforeUnload = function() {}

            // 指定一个avalon.scan视图的vmodels，vmodels = $ctrl.$vmodels.concact(DOM树上下文vmodels)
            $ctrl.$vmodels = []

        })
    })
