define(['avalon', 'http/http-factory', 'css!../../../css/selfInfo/member.min.css','css!../../../css/page.css'],
    function(avalon,  httpFactory) {
        var viewModel = vm = avalon.define({
            $id : 'shareDetailsCtrl',
            isLoading:false,
            shareData:{},
            shareList:[],
            isGetShareList:false
        })

        function getShareDetails() {
            viewModel.isLoading = true;
            httpFactory.getShareDetails(viewModel.show_id, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    var data = re.data;
                    for(var i = 0,len = data.length;i < len;i++) {
                        for(var j = 0,len1 = data[i].show_imgs.length;j < len1;j++) {
                            if(data[i].show_imgs[j].indexOf('_big.jpg' == 0)) {
                                data[i].show_imgs[j] = data[i].show_imgs[j].replace('.jpg','_big.jpg');
                            }
                        }
                       
                    }
                    if(data.length > 0) {
                        viewModel.shareData = re.data[0];
                    } 
                    
                } else {
                    layer.msg(re.msg);
                }
            }, function(err) {
                layer.msg(JSON.parse(err).msg);
            }, function() {
                viewModel.isLoading = false;
            })
        }

        var shareListModel  = avalon.define({
            $id : 'shareListCtrl',
            shareList:[],
            isGetShareList:false
        })

        function getShareList() {
            shareListModel.isGetShareList = true;
            httpFactory.getShare_list(null,'time',1,0,function(re) {
                re = JSON.parse(re)
                if(re.code == 0) {
                    var data = re.data;
                    for(var i = 0,len = data.length;i < len;i++) {
                        if(data[i].show_id == viewModel.show_id) {
                            data.splice(i,1)
                            break;
                        }
                    }
                    if(data.length > 4) data.length = 4;
                    shareListModel.shareList = data;
                } else {
                    layer.msg(re.msg)
                }

            },function(err) {
                layer.msg(JSON.parse(err).msg)
            },function(){
                shareListModel.isGetShareList = false;
            });
        }


        var comment = avalon.define({
                $id: "commentCtrl",
                pageIndex: 1,
                show_id: '',
                isRespondUser: false,
                nick: '',
                uid: '',
                data: [],
                commentText: '',
                hasNextPage: false,
                isFinished: true,
                respondUser: function(data) {
                    comment.isRespondUser = true;
                    if (data.comment_uid!=0) {
                        comment.nick = data.comment_nick;
                        comment.uid = data.comment_uid;
                    } else {
                        comment.nick = data.nick;
                        comment.uid = data.uid;
                    }           
                },
                cancelRespond: function() {
                    comment.isRespondUser = false;
                },
                getNextPage: function() {
                    if (comment.hasNextPage && comment.isFinished) {
                        comment.pageIndex++;
                        getComment(comment.pageIndex);
                    } else {
                        return;
                    }

                },
                getPrevPage: function() {
                    if (comment.pageIndex > 1 && comment.isFinished) {
                        comment.pageIndex--;
                        getComment(comment.pageIndex);
                    } else {
                        return;
                    }
                },
                submitCom: function() {
                    if (!comment.isRespondUser) {
                        doComment(0);
                    } else {
                        doComment(comment.uid);
                    }

                },
                isLogin:false,
            }) 

        function doComment(comment_uid) {

            httpFactory.comment(comment.show_id, comment.commentText, comment_uid, function(re) {
                re = JSON.parse(re)
                if (re.code == 0) {
                    layer.msg('评论成功');
                    comment.isRespondUser = false;
                    comment.commentText = '';
                    // getComment();
                } else {
                    layer.msg(re.msg);
                }

            }, function(err) {

            });
        }

        function getComment() {
            comment.isFinished = false;
            httpFactory.getCommentList(comment.show_id, comment.pageIndex, function(re) {
                re = JSON.parse(re)
                if (re.code == 0) {
                    comment.data = re.data;
                    if (comment.data.length >= 20) {
                        comment.hasNextPage = true;
                    } else {
                        comment.hasNextPage = false;
                    }

                } else {
                    layer.msg(re.msg)
                }

            }, function(err) {
                layer.msg(JSON.parse(err).msg)
            }, function() {
                comment.isFinished = true;
            });
        }

        

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function(state) {
                avalon.vmodels["headerEidget"].activePage = 'shareDetails';
                viewModel.show_id = state.params.show_id;
                getShareDetails()

                comment.isLogin = httpFactory.isLogin();
                comment.show_id = state.params.show_id;
                comment.data = [];
                comment.pageIndex = 1;
                getComment();
                getShareList()

            }
            $ctrl.$onBeforeUnload = function() {}

            $ctrl.$vmodels = []

        })
    })
