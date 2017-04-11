define(['avalon', 'jquery', 'http/http-factory'],
    function(avalon, $, httpFactory) {

        var twoPersons = avalon.define({
            $id: "twoPersonsCtrl",
            pageIndex: 1,
            data: [],
            hasNextPage: true,
            isFinished : true,
            startIndex:1,
            //测试页面
            newPageIndex:1,
			totalPages:1,
			pageCount:15,
            count:0,
            arrCount: [],
            isDisplayAnimation: false,				//是否显示动画，在数据加载之前
			//结束测试页面
            hidePage:false,
            order_key:'ing',
            order_type:'',
            nowTime:+(new Date()),
            getNextPage: function(adjust) {
            	var me = $(this);
            	if (me.hasClass('current')) {//判断是不是按钮跳转
            		twoPersons.pageIndex = me.text()-1;
            	}
	            	if (typeof adjust != 'number') {//判断是不是数字跳转
	                    twoPersons.pageIndex++;
	            	}else{
	            		twoPersons.pageIndex= twoPersons.newPageIndex;
	            	}
	            	//如果获取当前页超过了最大的页数，就把页数换成最大的值
	            	twoPersons.pageIndex > twoPersons.totalPages && (twoPersons.pageIndex = twoPersons.totalPages);
	            	//如果获取当前页为0，就把页数换成1
	            	!parseInt(twoPersons.pageIndex) && (twoPersons.pageIndex = 1);
	            	twoPersons.newPageIndex = twoPersons.pageIndex;
	            	twoPersons.hasNextPage = !(twoPersons.totalPages == twoPersons.pageIndex) ;
                    getData(twoPersons.pageIndex);
                    return;

            },
            changeOrderKey:function(order_key,order_type) {
                if(twoPersons.isLoading) return;
                twoPersons.startIndex = 1;
                twoPersons.order_key = order_key;
                if(order_type == 'none') {
                  twoPersons.order_type = '';
                } else if(order_type == 'asc') {
                  twoPersons.order_type = 'desc';
                } else {
                  twoPersons.order_type = 'asc';
                }
                getData(twoPersons.startIndex)
            },
            getPrevPage: function() {
                if (twoPersons.pageIndex > 1) {
                    twoPersons.pageIndex--;
//                  twoPersons.startIndex-= 10;
					twoPersons.newPageIndex = twoPersons.pageIndex; //YU 新增
                    getData(twoPersons.pageIndex);
                } else {
                    return;
                }

            }
        })
        function getData(startIndex) {
//          httpFactory.getActivityList('', '', '', '', (twoPersons.pageIndex - 1) * twoPersons.pageCount + 1, twoPersons.pageCount, 0, 4, function(re) {
            httpFactory.getActivityList('', '', twoPersons.order_key, twoPersons.order_type, (twoPersons.pageIndex - 1) * twoPersons.pageCount + 1, twoPersons.pageCount, 0, 4, function(re) {
                re = JSON.parse(re);
                var reDataArr = [];
                if (re.code == 0) {
                	$.each(re.data,function(i,n){
//              		console.log(i)
//              		console.log(n.remain_num)
//              		console.log(n.need_num)
                		if ((n.remain_num == n.need_num) || (n.remain_num == n.need_num/2)) {
							reDataArr[reDataArr.length] = n;
							//添加向后台返回错误的内容的地方
		                }
                	})
//              	twoPersons.data = re.data;
//					twoPersons.data = [];
                	twoPersons.data = reDataArr;
                    twoPersons.count = re.count;
                    twoPersons.totalPages = Math.ceil(re.count / twoPersons.pageCount);
                    twoPersons.totalPages == 1 && (twoPersons.hasNextPage = false);
                    httpFactory.getBtnNum( twoPersons, twoPersons.totalPages);
                    twoPersons.isDisplayAnimation = true;
                    if (twoPersons.newPageIndex < twoPersons.totalPages) {
                        twoPersons.hasNextPage = true;
                    }else{
                        twoPersons.hasNextPage = false;
                    }
                } else {
                    layer.msg(re.msg)
                }

            }, function(err) {
                layer.msg(JSON.parse(err).msg)
            },function(){
                twoPersons.isFinished = true;
            });
        }


        

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'twoPersons';
                twoPersons.startIndex = 1;
                twoPersons.data.length && (twoPersons.isDisplayAnimation = false);
                getData(twoPersons.startIndex);
            }
            $ctrl.$onBeforeUnload = function() {}

           		$ctrl.$vmodels = []

        })
    })
