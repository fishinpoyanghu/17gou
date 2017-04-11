define(['avalon', 'http/http-factory'],
    function(avalon,  httpFactory) {

        var viewModel = vm = avalon.define({
            $id : 'noticeCtrl',
            title: '活动公告',
            page: 1,
            isGetNotice:false,
            noticeList:[],
            noticeId:'',
            pageOver:false,
            showContent:function(notice) {
                if(notice.show) {
                    notice.show = false;
                } else {
                    initShow(viewModel.noticeList,notice.id)
                }
                
            },
            prevPage:function() {
                if(viewModel.page <= 1) return;
                if(viewModel.isGetNotice) return;
                viewModel.page--;
                getNotice()
            },
            nextPage:function() {
                if(viewModel.pageOver) return;
                if(viewModel.isGetNotice) return;
                viewModel.page++;
                getNotice()
            }

        })

        function initShow(data,id) {
            for(var i = 0,len = data.length;i < len;i++) {
                if(data[i].id == id) {
                    data[i].show = true;
                } else {
                    data[i].show = false;
                }
            }
            // return data;
        }

        function getNotice() {
            viewModel.isGetNotice = true;
            httpFactory.onlyPageParams('getNotice',viewModel.page,function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    var data = re.data;
                    initShow(data,viewModel.noticeId)
                    viewModel.noticeList = data;
                    if(data.length == 10) {
                        viewModel.pageOver = false;
                    } else {
                        viewModel.pageOver = true;
                    }
                } else {
                    layer.msg(re.msg)
                }

            }, function(err) {
                layer.msg(JSON.parse(err).msg)
            },function() {
                viewModel.isGetNotice = false;
            });
        } 
      
        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function(state) {
                avalon.vmodels["headerEidget"].activePage = 'notice';
                viewModel.noticeId = state.params.notice_id;
                viewModel.page = 1;
                getNotice();
            }
            $ctrl.$onBeforeUnload = function() {}

            $ctrl.$vmodels = []

        })
    })
