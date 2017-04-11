define(['avalon', 'jquery', 'http/http-factory'],
    function(avalon, $, httpFactory) {

        var tenYuan = avalon.define({
            $id: "tenYuanCtrl",
            pageIndex: 1,
            data: [],
            hasNextPage: true,
            isFinished : true,
            startIndex:1,
            //测试页面
            newPageIndex:1,
			totalPages:1,
			pageCount:12,
            count:0,
            arrCount: [],
            isDisplayAnimation: true,				//是否显示动画，在数据加载之前
			//结束测试页面
            hidePage:false,
            nowTime:+(new Date()),
            getNextPage: function(adjust) {
            	var me = $(this);
            	if (me.hasClass('current')) {//判断是不是按钮跳转
            		tenYuan.pageIndex = me.text()-1;
            	}
	            	if (typeof adjust != 'number') {//判断是不是数字跳转
	                    tenYuan.pageIndex++;
	            	}else{
	            		tenYuan.pageIndex= tenYuan.newPageIndex;
	            	}
	            	//如果获取当前页超过了最大的页数，就把页数换成最大的值
	            	tenYuan.pageIndex > tenYuan.totalPages && (tenYuan.pageIndex = tenYuan.totalPages);
	            	//如果获取当前页为0，就把页数换成1
	            	!parseInt(tenYuan.pageIndex) && (tenYuan.pageIndex = 1);
	            	tenYuan.newPageIndex = tenYuan.pageIndex;
	            	tenYuan.hasNextPage = !(tenYuan.totalPages == tenYuan.pageIndex) ;
                    getData(tenYuan.pageIndex);
                    return;

            },
            getPrevPage: function() {
                if (tenYuan.pageIndex > 1) {
                    tenYuan.pageIndex--;
//                  tenYuan.startIndex-= 10;
					tenYuan.newPageIndex = tenYuan.pageIndex; //YU 新增
                    getData(tenYuan.pageIndex);
                } else {
                    return;
                }

            }
        })
        function getData(startIndex) {
            tenYuan.isFinished=false;
        //    httpFactory.getActivityList('', '', '', '', startIndex, 12, 0, 2, function(re) {
            httpFactory.getActivityList('', '', '', '', (tenYuan.pageIndex - 1) * tenYuan.pageCount + 1, tenYuan.pageCount, 0, 2, function(re) {
                re = JSON.parse(re)
                if (re.code == 0) {
                    tenYuan.data = re.data;
                    console.log(tenYuan.data)
                    tenYuan.count = re.count;
                    tenYuan.totalPages = Math.ceil(re.count / tenYuan.pageCount);
                    tenYuan.totalPages == 1 && (tenYuan.hasNextPage = false);
                    httpFactory.getBtnNum( tenYuan, tenYuan.totalPages);
                    tenYuan.isDisplayAnimation = false;
//                  if (tenYuan.data.length == 10) {
//                      tenYuan.hasNextPage = true;
//                  }else{
//                      tenYuan.hasNextPage = false;
//                  }
                } else {
                    layer.msg(re.msg)
                }

            }, function(err) {
                layer.msg(JSON.parse(err).msg)
            },function(){
                tenYuan.isFinished = true;
            });
        }


        

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'tenYuan';
                tenYuan.startIndex = 1;
                tenYuan.data.length && (tenYuan.isDisplayAnimation = false);
                getData(tenYuan.startIndex);
            }
            $ctrl.$onBeforeUnload = function() {}

           		$ctrl.$vmodels = []

        })
    })
