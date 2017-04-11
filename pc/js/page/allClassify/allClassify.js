define(['avalon','http/http-factory', 'jquery','css!../../../css/page.css','css!../../../css//goods.css'],
    function(avalon,httpFactory) {

        var viewModel = vm = avalon.define({
            $id: "allClassifyCtrl",
            classifyList:[],
            goodsList: [],
            isLoading:false,
            page:1,
            //测试页面
            newPageIndex:1,
			totalPages:1,
            count:0,
            arrCount: [],
            isDisplayAnimation: true,
			//结束测试页面
           
            pageCount:15,
            order_key:'ing',
            order_type:'',
            pageOver:false,
            goods_type_id:'',
            changeGoodsType:function(goods_type_id) {
                if(viewModel.isLoading) return;
                viewModel.page = 1;
                console.log(goods_type_id)
                viewModel.goods_type_id = goods_type_id;
                getGoodList();

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
            },
            prevPage:function() {
                if(viewModel.page <= 1) return;
                if(viewModel.isLoading) return;
                viewModel.page--;
                viewModel.newPageIndex = viewModel.page;
                viewModel.hasNextPage = true;
                getGoodList();
            },
            nextPage:function(adjust) {
//              if(viewModel.pageOver) return;
//              if(viewModel.isLoading) return;
//              viewModel.page++;
				var me = $(this);
            	if (me.hasClass('current')) {//判断是不是按钮跳转
            		viewModel.page = me.text()-1;
            	}
	            	if (typeof adjust != 'number') {//判断是不是数字跳转
	                    viewModel.page++;
	            	}else{
	            		viewModel.page = viewModel.newPageIndex;
	            	}
	            	//如果获取当前页超过了最大的页数，就把页数换成最大的值
	            	viewModel.page > viewModel.totalPages && (viewModel.page = viewModel.totalPages);
	            	//如果获取当前页为0，就把页数换成1
	            	!parseInt(viewModel.page) && (viewModel.page = 1);
	            	viewModel.newPageIndex = viewModel.page;
	            	viewModel.hasNextPage = !(viewModel.totalPages == viewModel.page);
                getGoodList();
            }
          
        })


        getClassify();
        function getClassify() {
            var classify = avalon.cookie.get("classifyList");
            if(classify == 'unfinshed') {
                setTimeout(function () {
                   getClassify();
                },100)
            } else {
                viewModel.classifyList = JSON.parse(classify).list;
                // if(viewModel.classifyList.length > 8) viewModel.classifyList.length = 8;
            }
        }

        function getGoodList () {
            viewModel.isLoading = true;
            var url = window.location.href;
            if (url.split('order_key=')[1] == 'weight' ) {
            console.log(url.split('?order_key')[0])
            	var order_key = url.split('?')[1].split('=')[1];
				viewModel.order_key = order_key
	            window.location.href = url.split('?order_key')[0];
            }
            httpFactory.getActivityList(viewModel.goods_type_id == 'all' ? '' : viewModel.goods_type_id,'',viewModel.order_key,viewModel.order_type,(viewModel.page - 1)*viewModel.pageCount + 1,viewModel.pageCount,0,null,function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    var data = re.data;
                    viewModel.goodsList = data;
//                  console.log(viewModel.goodsList)
                     //测试数据
//					re.count = 36;                    
                    viewModel.isDisplayAnimation = false;
                    
                    viewModel.count = re.count;
                    viewModel.totalPages = Math.ceil(re.count / viewModel.pageCount);
                    viewModel.totalPages == 1 && (viewModel.hasNextPage = false);
                    httpFactory.getBtnNum( viewModel, viewModel.totalPages);
                    
                    viewModel.newPageIndex = viewModel.page;

                    //结束测试数据
                    if(data.length == viewModel.pageCount) {
                        viewModel.pageOver = false;
                    } else {
                        viewModel.pageOver = true;
                    }
                } else {
                    layer.msg(re.msg)
                }

            }, function(err) {
                layer.msg(JSON.parse(err).msg)
            },function() {
                viewModel.isLoading = false;
            })
        }

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function(state) {
                avalon.vmodels["headerEidget"].activePage = 'allClassify';
                viewModel.goods_type_id = state.params.goods_type_id;
                viewModel.page = 1;
                viewModel.newPageIndex = 1
                viewModel.isDisplayAnimation = true;
                viewModel.good = [];
                getGoodList();
            }
            $ctrl.$onBeforeUnload = function() {}

            // 指定一个avalon.scan视图的vmodels，vmodels = $ctrl.$vmodels.concact(DOM树上下文vmodels)
            $ctrl.$vmodels = []

        })
    })
