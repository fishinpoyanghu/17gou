define(['avalon', 'jquery','http/http-factory'],
    function(avalon, $, httpFactory) {
        var share = avalon.define({
            $id: "shareCtrl",
            good:[],
            pageIndex:1,
            hasNextPage:true,
            //测试页面
            newPageIndex:1,
			totalPages:1,
            count:0,
            arrCount: [],
            isDisplayAnimation: true,
			//结束测试页面
			pageCount:12,
            isFinshed:true,
            nowTime:+new Date(),
            type:'hot',
            getNextPage:function(adjust){
//              if (share.hasNextPage&&share.isFinshed){
//                  share.pageIndex++;
//                  getShare();
//              }         
				var me = $(this);
            	if (me.hasClass('current')) {//判断是不是按钮跳转
            		share.pageIndex = me.text()-1;
            	}
//              if (share.hasNextPage && share.isFinished) {
	            	if (typeof adjust != 'number') {//判断是不是数字跳转
	                    share.pageIndex++;
	            	}else{
	            		share.pageIndex= share.newPageIndex;
	            	}
	            	//如果获取当前页超过了最大的页数，就把页数换成最大的值
	            	share.pageIndex > share.totalPages && (share.pageIndex = share.totalPages);
	            	//如果获取当前页为0，就把页数换成1
	            	!parseInt(share.pageIndex) && (share.pageIndex = 1);
	            	share.newPageIndex = share.pageIndex;
	            	share.hasNextPage = !(share.totalPages == share.pageIndex);
	            	getShare();
            },
            getPrevPage:function(){
                if (share.pageIndex>1 &&share.isFinshed) {
                    share.pageIndex--;
                    share.newPageIndex = share.pageIndex;
                    share.hasNextPage = true;
                    getShare();
                }
            },
            changeType:function(type) {
                share.pageIndex = 1;
                share.type = type;
                share.good = [];
                getShare();
            }
        })
        function getShare() {
            share.isFinshed = false;
            httpFactory.getShare_list(null,share.type,share.pageIndex,share.pageCount,1,function(re) {
                re = JSON.parse(re);
                if(re.code == 0) {
                    var data = re.data;
                    //测试数据
//					re.count = 20;                   //暂时没有数据，模拟一个
                    
                    share.count = re.count;
                    share.totalPages = Math.ceil(re.count / share.pageCount);
                    share.totalPages == 1 && (share.hasNextPage = false);
                    httpFactory.getBtnNum( share, share.totalPages);
                    share.newPageIndex = share.pageIndex ;
                    share.isDisplayAnimation = false;
                    //结束测试数据
                    for(var i = 0,len = data.length;i < len;i++) {
                        if(data[i].show_imgs[0] && data[i].show_imgs[0].indexOf('_big.jpg' == 0)) {
                            data[i].show_imgs[0] = data[i].show_imgs[0].replace('.jpg','_big.jpg');
                        }
                    }
                    share.good = data;
//                  if (share.good.length == 10) {
//                      share.hasNextPage=true;
//                  }else{
//                      share.hasNextPage=false;              
//                  }
                } else {
                    layer.msg(re.msg)
                }

            },function(err) {
                layer.msg(JSON.parse(err).msg)
            },function(){
                share.isFinshed = true;
            });
        }
        
        getShare();

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'share';
                share.pageIndex = 1;
                share.isDisplayAnimation = true;
                share.good = [];
                getShare();
            }
            $ctrl.$onBeforeUnload = function() {}

            // 指定一个avalon.scan视图的vmodels，vmodels = $ctrl.$vmodels.concact(DOM树上下文vmodels)
            $ctrl.$vmodels = [];

        })
})
