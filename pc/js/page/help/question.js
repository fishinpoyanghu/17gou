define(['avalon', 'http/http-factory'],
    function(avalon,  httpFactory) {

        var viewModel = vm = avalon.define({
            $id : 'questionCtrl',
            'title' : '常见问题',
            'content' : '',
            isLoading:false
        })

        function getQuestion(){
            viewModel.isLoading = true;
            httpFactory.getHtml('question',function(re){
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
