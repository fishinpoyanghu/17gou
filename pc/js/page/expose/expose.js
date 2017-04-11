define(['avalon','http/http-factory','components/view-left-side/left','css!../../../css/page.css','css!../../../css/selfInfo/member.min.css','page/userMes/userMes'],
    function(avalon,httpFactory) {

        var expose = avalon.define({
            $id: "exposeCtrl",
            $leftSideOpts:{
                activePage:'expose'
            },
            data: [],
            isLoading:false,
            page:1,
            pageCount:6,
            hasNextPage:false,
            getNextPage:function(){
                if (expose.hasNextPage && !expose.isLoading) {
                    expose.page++;
                    getExpose();
                }else{
                    return;
                }
            },
            getPrevPage:function(){
                if(expose.page > 1 && !expose.isLoading) {
                    expose.page--;
                    getExpose();
                }else{
                    return;
                }
            }
        })
        function getExpose(){
            expose.isLoading = true;
            httpFactory.getShare_list(null,'hot',(expose.page - 1) * expose.pageCount + 1,1,function(re){
                re = JSON.parse(re);
                if(re.code==0) {
                    expose.data = re.data;
                    if(expose.data.length == expose.pageCount) {
                        expose.hasNextPage = true;
                    } else {
                        expose.hasNextPage = false;
                    }
                }else{
                    layer.msg(re.msg)
                }
            },function(re) {
                re = JSON.parse(re);
                layer.msg(re.msg)
            },function() {
                expose.isLoading = false;
            })
        }
        
        getExpose();

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'expose';

            }
            $ctrl.$onBeforeUnload = function() {}

            // 指定一个avalon.scan视图的vmodels，vmodels = $ctrl.$vmodels.concact(DOM树上下文vmodels)
            $ctrl.$vmodels = []

        })
    })
