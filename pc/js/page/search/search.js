define(['avalon', 'jquery', 'http/http-factory'],
    function(avalon, $, httpFactory) {

        var viewModel = avalon.define({
            $id: "searchCtrl",
            page:1,
            goodsList:[],
            isLoading:false,
            pageCount:15,
            key_word:'',
            order_key:'ing',
            order_type:'',
            pageOver:false,
            prevPage:function() {
                if(viewModel.page <= 1) return;
                if(viewModel.isLoading) return;
                viewModel.page--;
                getGoodList()
            },
            nextPage:function() {
                if(viewModel.pageOver) return;
                if(viewModel.isLoading) return;
                viewModel.page++;
                getGoodList()
            },
            changeOrderKey:function(order_key,order_type) {
                if(viewModel.isLoading) return;
                viewModel.page = 1;
                viewModel.order_key = order_key;
                if(order_type == 'none') {
                  viewModel.order_type = '';
                } else if(order_type == 'asc') {
                  viewModel.order_type = 'desc';
                } else {
                  viewModel.order_type = 'asc';
                }
                getGoodList()
            }
        })

        function getGoodList () {
            viewModel.isLoading = true;
            httpFactory.getActivityList('' ,viewModel.key_word,viewModel.order_key,viewModel.order_type,(viewModel.page - 1)*viewModel.pageCount + 1,viewModel.pageCount,0,null,function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    var data = re.data;
                    viewModel.goodsList = data;
                    if(data.length == viewModel.pageCount) {
                        viewModel.pageOver = false;
                    } else {
                        viewModel.pageOver = true;
                    }

                } else {

                }

            }, function(err) {

            },function() {
                viewModel.isLoading = false;
            });
        }



        avalon.vmodels["headerEidget"].searchGoods = function(key) {
            viewModel.key_word = key;
            getGoodList();
        }

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'search';
                viewModel.key_word = avalon.vmodels["headerEidget"].searchKey;
                getGoodList();
            }
            $ctrl.$onBeforeUnload = function() {}

            // 指定一个avalon.scan视图的vmodels，vmodels = $ctrl.$vmodels.concact(DOM树上下文vmodels)
            $ctrl.$vmodels = []

        })
    })
