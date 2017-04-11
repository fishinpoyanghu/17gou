define(['avalon', 'http/http-factory','components/view-left-side/left','css!../../../css/selfInfo/member.min.css','page/userMes/userMes'], 
    function(avalon, httpFactory) {

        var recordBuy = avalon.define({
            $id: "recordBuyCtrl",
            $leftSideOpts:{
                activePage:'recordBuy'
            },
            data: [],
            myNum: [],
            pageIndex: 1,
            hasNextPage: false,
            isFinished: true,
            startIndex:1,
            hidePage:false,
            hasBorderTop:true,
            curIndex:3,
            isInGetNum:false,
            changeLi:function(index){
               recordBuy.curIndex=index; 
               recordBuy.startIndex =1;
               recordBuy.pageIndex =1;
            },
            getNextPage: function() {
                if (recordBuy.hasNextPage && recordBuy.isFinished) {
                    recordBuy.pageIndex++;
                    recordBuy.startIndex += 6;
                    getrecordBuyData(recordBuy.startIndex);
                } 
            },
            getPrevPage: function() {
                if (recordBuy.pageIndex > 1 && recordBuy.isFinished) {
                    recordBuy.pageIndex--;
                    recordBuy.startIndex -= 6;
                    getrecordBuyData(recordBuy.startIndex);
                } 

            },
            checkMyNum: function(activity_id) {
                recordBuy.isInGetNum = true;
                recordBuy.myNum = [];
                httpFactory.getRecordListNum(activity_id, null, null, function(re) {
                    re = JSON.parse(re)
                    if (re.code == 0) {
                        recordBuy.myNum = re.data;
                    } else {
                        layer.msg(re.msg)
                    }

                }, function(err) {
                    layer.msg(JSON.parse(err).msg)
                },function() {
                    recordBuy.isInGetNum = false;
                });
            },
            changerecordBuyData:function(curIndex){
                recordBuy.data=[];
                getrecordBuyData(curIndex);
            }
        })

        function getrecordBuyData(startIndex,curIndex) {
            recordBuy.isFinished = false;
            httpFactory.getRecordList(null,recordBuy.startIndex,6,recordBuy.curIndex,function(re) {
                re = JSON.parse(re)
                if (re.code == 0) {
                    recordBuy.data = re.data;
                    if(re.msg=='数据为空') {
                        recordBuy.hidePage = true;
                    }
                    if(recordBuy.data.length==6) {
                        recordBuy.hasNextPage = true;
                    }else{
                        recordBuy.hasNextPage =false;
                    }
                } else {

                }

            }, function(err) {

            },function(){
                recordBuy.isFinished = true;
            });
        }

        getrecordBuyData(recordBuy.startIndex,recordBuy.curIndex);



        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'recordBuy';
            }
            $ctrl.$onBeforeUnload = function() {}

            // 指定一个avalon.scan视图的vmodels，vmodels = $ctrl.$vmodels.concact(DOM树上下文vmodels)
            $ctrl.$vmodels = []

        })
    })
