define(['avalon','http/http-factory'],
    function(avalon,httpFactory) {

        var viewModel = vm = avalon.define({
            $id: "payResultCtrl",
            order_num:'',
            getResult:getResult,
            _interval_check:undefined,
            result_data:[],
            inGetResult:false,
          
        })

        function getResult() {
            viewModel.inGetResult = true;
            var loadIcon = layer.load(1, {shade: false,offset: '300px'}); 
            httpFactory.getResult(viewModel.order_num,function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                  if(!re.data || re.data.length == 0) {
                    viewModel._interval_check = setTimeout(function(){
                      getResult();
                    }, 3000);
                  } else {
                    if(viewModel._interval_check){
                      clearTimeout(viewModel._interval_check);
                      viewModel._interval_check = undefined;
                    }

                    viewModel.result_data = re.data;
                    layer.close(loadIcon); 
                    viewModel.inGetResult = false;
                  }
                } else {
                    layer.close(loadIcon); 
                    layer.msg('获取结果失败')
                }

            }, function(err) {
                layer.close(loadIcon); 
            });
        }

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function(state) {
                avalon.vmodels["headerEidget"].activePage = 'payResult';
               viewModel.order_num = state.params.order_num.replace('payOrder_','');
               viewModel.result_data = [];
               getResult()
            }
            $ctrl.$onBeforeUnload = function() {}

            $ctrl.$vmodels = []

        })
    })
