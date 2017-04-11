define(['avalon', 'http/http-factory'],
    function(avalon,  httpFactory) {

        var viewModel = vm = avalon.define({
            $id : 'helpCtrl',
            title : '',
            content : '',
            currentPage:'',
            isLoading:false
        })

        function getQuestion(){
            viewModel.isLoading = true;
            httpFactory.getHtml(viewModel.currentPage,function(re){
                viewModel.content = re;
                viewModel.isLoading = false;
            });
        }

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function(state) {
                avalon.vmodels["headerEidget"].activePage = 'question';
                viewModel.currentPage = state.params.page || 'question';
                var allPage = {
                    ruleIntroduction:'规则介绍',
                    // redRule:'红包玩法',
                    // lotteryRule:'抽奖玩法',
                    question:'常见问题',
                    know:'了解亿七购',
                    fortune:'会员福分经验',
                    systemEnsure:'亿七购保障体系',
                    safetyPayment:'安全支付',
                    complaint:'投诉建议',
                    deliveryMoney:'配送费用',
                    sign:'商品验货与签收',
                    noReceive:'长时间未收到商品问题',
                    introduce:'亿七购介绍',
                    serviceAgreement:'服务协议',
                    contact:'联系我们',
                    cooperation:'商务合作',
                    invite:'邀请',
                    qq:'官方QQ交流群'
                }
                viewModel.title = allPage[viewModel.currentPage]
                if(allPage[viewModel.currentPage]) {
                    getQuestion();
                } else {
                    avalon.router.go('index');
                }
                
            }
            $ctrl.$onBeforeUnload = function() {}

            $ctrl.$vmodels = []

        })
    })
