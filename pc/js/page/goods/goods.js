define(['avalon', 'jquery', 'http/http-factory','components/view-countdown/count','slider'],
    function(avalon, $, httpFactory,countdowm) {

        var viewModel = vm = avalon.define({
            $id: "goodsPageCtrl",
            activity_id:'',
            isLogin:false,
            nowTime:'',
            goodsData: {},
            blockIndex:'0',
            ImgDetials:'',
            countDetials:{},
            joinListData:{
                hasLoad:false,
                isLoading:false,
                page:1,
                pageCount:10,
                hasNext:true,
                data:[]
            },
            shareListData:{
                hasLoad:false,
                isLoading:false,
                page:1,
                // pageCount:10,
                hasNext:true,
                data:[]
            },
            myAllNum:[],
            myAllNumLoading:false,
            imgIndex:0,
            bigImg:'',
            goodsImgNextShow:[],				//即将揭晓下面的hover变动显示
            join_number:1,
            isLoading:false,
            login:function() {
                avalon.router.go('login');
            },
            jumpActiveIssue:function(id){
            	window.location.href="#!/goods/"+id;
            },
            changeBlock:function(index) {
                viewModel.blockIndex = index;
                if(index == '1') {
                    getJoinList()
                } else if(index == '2' && !viewModel.shareListData.hasLoad) {
                    getShareList()
                }
            },
            getShowMyCodes:function(uid) {
                if(uid !="me") {
                    getRecordListNum(uid)
                } else {
                    if(viewModel.goodsData.user_lucky_num.length < 12) {
                        var myAllNum = [];
                        var temp = [],len = viewModel.goodsData.user_lucky_num.length;
                        if(len <= 3) {
                            for(var i = 0;i < len;i++ ) {
                                temp.push(viewModel.goodsData.user_lucky_num[i]);
                            }
                            myAllNum.push(temp)
                        } else {
                            for(var i = 0;i < len;i++ ) {
                                temp.push(viewModel.goodsData.user_lucky_num[i]);
                                if((i + 1)%3 == 0) {
                                    myAllNum.push(temp);
                                    temp = [];
                                } else if(i == len -1) {
                                    myAllNum.push(temp);
                                }
                            }
                        }
                        
                        viewModel.myAllNum = myAllNum;
                        $('#goodsNumModal').modal('show')
                    } else {
                        getRecordListNum()
                    }
                }
                
            },
            nextJoinPage:function() {
                if(viewModel.joinListData.isLoading) return;
                if(!viewModel.joinListData.hasNext) return;
                viewModel.joinListData.page++;
                getJoinList()
            },
            prevJoinPage:function() {
                if(viewModel.joinListData.isLoading) return;
                if(viewModel.joinListData.page <= 1) return;
                viewModel.joinListData.page--;
                getJoinList()
            },
            nextSharePage:function() {
                if(viewModel.shareListData.isLoading) return;
                if(!viewModel.shareListData.hasNext) return;
                viewModel.shareListData.page++;
                getShareList()
            },
            prevSharePage:function() {
                if(viewModel.shareListData.isLoading) return;
                if(viewModel.shareListData.page <= 1) return;
                viewModel.shareListData.page--;
                getShareList()
            },
            clickSmallImg:function(index,src) {
                viewModel.imgIndex = index;
                viewModel.bigImg = src;
            },
            decrease:function (goodsItem,unit) {
                unit = unit || 1;
                // goodsItem.join_number = Number(goodsItem.join_number);
//              if(goodsItem.activity_type == 2) unit = 10;
                viewModel.join_number = viewModel.join_number - viewModel.join_number % unit;
                viewModel.join_number = viewModel.join_number - unit;
                if (viewModel.join_number <= 0) {
                    viewModel.join_number = unit;
                }
            },
            increase:function (goodsItem,unit) {
                unit = unit || 1;
//              if(goodsItem.activity_type == 2) unit = 10;
                var join_number = Number(viewModel.join_number);
                join_number = join_number + unit;
                if (join_number >= goodsItem.remain_num) {
                    viewModel.join_number = goodsItem.remain_num;
                } else {
                    viewModel.join_number = join_number
                }
                if(goodsItem.activity_type == 3 && viewModel.join_number > 10) viewModel.join_number = 10;
                if(goodsItem.activity_type == 2 && (join_number>=goodsItem.remain_num/10)){
                	goodsItem.join_number = goodsItem.remain_num/10;
                }
                if(goodsItem.activity_type == 4 && (join_number>=2)){
                	viewModel.join_number = 2 - (Math.ceil((goodsItem.need_num - goodsItem.remain_num)/(goodsItem.need_num/2)));
                	if (goodsItem.join_number <= 0) {
	                    goodsItem.join_number = unit;
	                }
                }
            },
            editJoinNumber:function (goodsItem,unit) {
                unit = unit || 1;
//              if(goodsItem.activity_type == 2) unit = 10;
                var joinNumber = viewModel.join_number;
                if (joinNumber == '') {
                    viewModel.join_number = 1;
                    if(goodsItem.activity_type == 2) viewModel.join_number = 10;
                    return;
                }
                var remainNumber = goodsItem.remain_num;
                if(goodsItem.activity_type==3 && goodsItem.remain_num > 10) {
                    remainNumber = 10;
                }
                var match = joinNumber.match(/^[1-9]\d*/);
                if (match == null) {
                    joinNumber = unit;
                } else {
                    joinNumber = parseInt(match[0]);
                    joinNumber = joinNumber > remainNumber ? remainNumber : joinNumber;
                    joinNumber = joinNumber <= 0 ? unit : joinNumber;
                }
                viewModel.join_number = joinNumber;
                if(goodsItem.activity_type == 2 && (joinNumber>=goodsItem.remain_num/10)){
                	viewModel.join_number = goodsItem.remain_num/10;
                }
                if(goodsItem.activity_type == 4 && (joinNumber >= 2)){
                	viewModel.join_number = 2 - (Math.ceil((goodsItem.need_num - goodsItem.remain_num)/(goodsItem.need_num/2)));
                	if (goodsItem.join_number <= 0) {
	                    goodsItem.join_number = unit;
	                }
                }
            },
            isMakingOrder:false,
            makeOrder:function (goodsData) {
                if(httpFactory.isLogin()) {
                    makeOrder(goodsData)
                } else {
                    avalon.router.go('login');
                }
                
            }
        })

        function makeOrder (goodsData) {
            if(viewModel.isMakingOrder) return;
            viewModel.isMakingOrder = true;
            var orderData = [{
                    activity_id:goodsData.activity_id,
                    goods_title:goodsData.goods_title,
                    num:goodsData.activity_type==2?viewModel.join_number*10:
                    	goodsData.activity_type==4?viewModel.join_number*(goodsData.need_num/2)
                    								:viewModel.join_number
                }]
            httpFactory.getOrderInfo(orderData,function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                   avalon.cookie.set("orderData",JSON.stringify(re.data));
                   avalon.router.go('payOrder',{order_num: 'payOrder_' + re.data.order_num,buyNow:'true'})
                } else if(re.code == 1) {
                    layer.msg('余额不足，请先充值');
                } else {
                    layer.msg(re.msg);
                }

            }, function(err) {

            },function() {
              viewModel.isMakingOrder = false;
            });
        }

        function getRecordListNum(uid) {
            if(viewModel.myAllNumLoading) return;
            viewModel.myAllNumLoading = true;
            httpFactory.getRecordListNum(viewModel.goodsData.activity_id,uid,null, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                   viewModel.myAllNum = re.data;
                   $('#goodsNumModal').modal('show')
                } else {
                    layer.msg(re.msg)
                }

            }, function(err) {

            },function() {
                viewModel.myAllNumLoading = false;
            });
        }

        function timeoutCallback(activity_id) {
            httpFactory.getGoodsDetail(activity_id, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    var data = re.data;
                    if(data.status == 1) {
                        timeoutCallback(activity_id)
                    } else {
                        viewModel.goodsData = data;
                        getCountDetials(activity_id);
                    }
                } else {
                    layer.msg(re.msg)
                }

            }, function(err) {

            });
        }

        function getGoodsDetials(join_number) {
            viewModel.isLoading = true;
            httpFactory.getGoodsDetail(viewModel.activity_id, function(re) {
                re = JSON.parse(re);
                if(re.data.activity_type == 4){
//	                if ((re.data.remain_num != re.data.need_num) && (re.data.remain_num != re.data.need_num/2)&&(re.data.remain_num == 0)) {
//	                	layer.msg('对不起，数据错误，正在前往二人购商品列表')
//						avalon.router.go('twoPersons');
//						//添加向后台返回错误的内容的地方
//	                }
                }
                if (re.code == 0) {
                    var data = re.data;
                    viewModel.goodsData = data;
                    viewModel.join_number = join_number || 1;
                    viewModel.bigImg = data.goods_img[0];
                    viewModel.goodsImgNextShow = data.goods_img.slice(0,3);
                    if(data.status == 2) {
                        getCountDetials(data.activity_id);
                    } else {
                        getImgDetials(data.goods_id)
                    }
                    if(data.status == 1) {
                        viewModel.nowTime = +new Date() + 1000;
                        countdowm.start(function() {
                            timeoutCallback(data.activity_id)
                        })
                    }
                    
                } else {
                    layer.msg(re.msg)
                }

            }, function(err) {
                layer.msg(JSON.parse(err).msg)
            },function() {
                viewModel.isLoading = false;
            });
        }

        function getImgDetials(goods_id) {
            httpFactory.getImgDetials(goods_id, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    viewModel.ImgDetials = re.data.html;
                } else {
                    layer.msg(re.msg)
                }

            }, function(err) {

            });
        }

        function getJoinList() {
//          if(viewModel.joinListData.isLoading) return;
            viewModel.joinListData.isLoading = true;
            httpFactory.getJoinList(viewModel.goodsData.activity_id,(viewModel.joinListData.page - 1)*viewModel.joinListData.pageCount + 1,viewModel.joinListData.pageCount, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    var data = re.data;
                    viewModel.joinListData.data = data;
                    viewModel.joinListData.hasLoad = true;
                    if(data.length == viewModel.joinListData.pageCount) {
                        viewModel.joinListData.hasNext = true;
                    } else {
                        viewModel.joinListData.hasNext = false;
                    }
                } else {
                    layer.msg(re.msg)
                }

            }, function(err) {

            },function() {
                viewModel.joinListData.isLoading = false;
            });
        }

        function getShareList() {
            if(viewModel.shareListData.isLoading) return;
            viewModel.shareListData.isLoading = true;
            httpFactory.getShare_list(viewModel.goodsData.goods_id,'hot',viewModel.shareListData.page,'0',1, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    var data = re.data;
                    viewModel.shareListData.data = data;
                    viewModel.shareListData.hasLoad = true;
                    if(data.length == viewModel.shareListData.pageCount) {
                        viewModel.shareListData.hasNext = true;
                    } else {
                        viewModel.shareListData.hasNext = false;
                    }
                } else {
                    layer.msg(re.msg)
                }

            }, function(err) {

            },function() {
                viewModel.shareListData.isLoading = false;
            });
        }

        function getCountDetials(activity_id) {
            httpFactory.getCountDetials(activity_id, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    viewModel.countDetials = re.data;
                } else {
                    layer.msg(re.msg)
                }

            }, function(err) {

            });
        }


        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function(state) {
                avalon.vmodels["headerEidget"].activePage = 'goods';
                viewModel.isLogin = httpFactory.isLogin();
                viewModel.activity_id = state.params.activity_id;
                viewModel.blockIndex = '0',
                getGoodsDetials()
            }
            $ctrl.$onBeforeUnload = function() {}

            
            $ctrl.$vmodels = []

        })
    })
