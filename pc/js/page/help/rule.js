define(['avalon', 'http/http-factory'],
    function(avalon,  httpFactory) {

        var viewModel = vm = avalon.define({
            $id : 'ruleCtrl',
            'title' : '玩法规则',
            'content' : '',
            isLoading:false
        })

        function getQuestion(){
            viewModel.isLoading = true;
            httpFactory.getHtml('rule',function(re){
                viewModel.content = re;
                viewModel.isLoading = false;
            });
        }

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function(state) {
                avalon.vmodels["headerEidget"].activePage = 'question';

                getQuestion();
            }
            $ctrl.$onBeforeUnload = function() {}

            $ctrl.$vmodels = []

        })
    })
