define(['avalon', 'jquery', 'http/http-factory'],
    function(avalon, $, httpFactory) {

        var max_buy = avalon.define({
            $id: "max_buyCtrl",
            pageIndex: 1,
            data: [],
            hasNextPage: false,
            isFinished : true,
            startIndex:1,
            hidePage:false,
            nowTime:+(new Date()),
            getNextPage: function() {
                if (max_buy.hasNextPage&&isFinished) {
                    max_buy.pageIndex++;
                    max_buy.startIndex += 10;
                    getMax_buy(max_buy.startIndex);
                }else{
                    return;
                }

            },
            getPrevPage: function() {
                if (max_buy.pageIndex > 1) {
                    max_buy.pageIndex--;
                    max_buy.startIndex-= 10;
                    getMax_buy(max_buy.startIndex);
                } else {
                    return;
                }

            }
        })
        function getMax_buy(startIndex) {
            max_buy.isFinished=false;
            httpFactory.getActivityList('', '', '', '', startIndex, 10, 0, 3, function(re) {
                re = JSON.parse(re)
                if (re.code == 0) {
                    console.log(re);
                    max_buy.data = re.data;
                    if(re.msg=='数据为空') {
                        max_buy.hidePage = true;
                    }
                    if (max_buy.data.length == 10) {
                        max_buy.hasNextPage = true;
                    }else{
                        max_buy.hasNextPage = false;
                    }
                } else {
                    layer.msg(re.msg)
                }

            }, function(err) {
                layer.msg(JSON.parse(err).msg)
            },function(){
                max_buy.isFinished = true;
            });
        }


        getMax_buy(max_buy.startIndex);

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'max_buy';
            }
            $ctrl.$onBeforeUnload = function() {}

            $ctrl.$vmodels = []

        })
    })
