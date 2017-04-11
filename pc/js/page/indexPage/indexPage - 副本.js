define(['avalon', 'jquery', 'http/http-factory','components/view-countdown/count','slider'],
    function(avalon, $, httpFactory,countdowm) {
        //轮播图
        var banner = avalon.define({
                $id: "banner",
                pics: [],
            })

        //轮播图图片数据
        httpFactory.noParams('getBanner',function(re) {
            re = JSON.parse(re);
            if (re.code == 0) {
                banner.pics = re.data;
                $('#banner').flexslider({
                        animation: "slide",
                        direction: "horizontal",
                        easing: "swing"
                    });
            } else {

            }

        }, function(err) {

        });

        function timeoutCallback(activity_id) {
            httpFactory.getGoodsDetail(activity_id, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    var newGoodList = indexVM.newGoodList;
                    for(var i = 0,len = newGoodList.length;i < len;i++) {
                        if(newGoodList[i].activity_id == activity_id) {
                            var newActivity = re.data;
                            newGoodList[i].status = newActivity.status;
                            newGoodList[i].lucky_unick = newActivity.lucky_unick;
                            newGoodList[i].lucky_num = newActivity.lucky_num;
                            newGoodList[i].lucky_user_num = newActivity.lucky_user_num;
                            newGoodList[i].lucky_ip = newActivity.lucky_ip;
                            avalon.scan();
                            setTimeout(function() {
                              getNewGoods()
                            },10000)
                            return;
                        }
                    }
                } else {

                }

            }, function(err) {

            });
        }

        //最新揭晓数据

        function getNewGoods() {
            indexVM.isGetNewGoods = true;
            httpFactory.getActivityList('', '', '', '', 1, 5, 3, 0, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    indexVM.newGoodList = [];
                    indexVM.newGoodList = re.data;
                    indexVM.palyNewsList = indexVM.newGoodList;
                    indexVM.nowTime = +new Date() + 1000;
                    countdowm.start(function(activity_id) {
                        timeoutCallback(activity_id)
                    })
                } else {
                    layer.msg(re.msg)
                }

            }, function(err) {
                layer.msg(JSON.parse(err).msg)
            },function() {
                indexVM.isGetNewGoods = false;
            });
        }





        //中奖消息
        httpFactory.noParams('getluckyInfo',function(re) {
            re = JSON.parse(re);
            if (re.code == 0) {
                indexVM.luckyData = [];
                indexVM.luckyData = re.data;
                setTimeout(function() {
                    function a() {
                        b -= 44;
                        b >= 44 * -($(".yscroll_list_left li").length - 2) ? $(".yscroll_list_left").animate({ marginTop: b }, 2E3) : $(".yscroll_list_left").animate({ marginTop: b }, 2E3, function() {
                            b = 0;
                            $(".yscroll_list_left").css({ marginTop: 0 })
                        })
                    }
                    var b = 0;
                    $($(".yscroll_list_left li")[0]).clone(!0).insertAfter($($(".yscroll_list_left li")[$(".yscroll_list_left li").length - 1]));
                    var e = setInterval(a, 4E3);
                    $(".yscroll_list_left").hover(function() { clearInterval(e) }, function() { e = setInterval(a, 4E3) });
                    $(".yscroll_list_rightli1").click(function() {
                        if (!$(".yscroll_list_left").is(":animated")) {
                            var a = parseInt($(".yscroll_list_left").css("marginTop").slice(0, -2));
                            a > -(44 * ($(".yscroll_list_left li").length - 2)) && $(".yscroll_list_left").animate({ marginTop: a - 44 }, 2E3)
                        }
                    });
                    $(".yscroll_list_rightli2").click(function() {
                        if (!$(".yscroll_list_left").is(":animated")) {
                            $(".yscroll_list_left").stop();
                            var a = parseInt($(".yscroll_list_left").css("marginTop").slice(0, -2)); - 44 >= a && $(".yscroll_list_left").animate({ marginTop: a + 44 }, 2E3)
                        }
                    });
                    $(".yscroll_list_right li").hover(function() { clearInterval(e) }, function() {
                        b = $(".yscroll_list_left").css("marginTop");
                        e = setInterval(a, 4E3)
                    });
                    $(".yConulout").hover(function() { $(this).find(".yConuloutbtn").show() }, function() { $(this).find(".yConuloutbtn").hide() });
                    $(document).keyup(function(a) {
                        switch (a.keyCode) {
                            case 37:
                                a = $(window).scrollTop();
                                120 <= a & 754 >= a && $(".yConuloutLeft").click();
                                604 <= a & 1087 >= a && $($(".y_btn_left")[0]).click();
                                1070 <= a & 1570 >= a && $($(".y_btn_left")[1]).click();
                                1640 <= a & 2128 >= a && $($(".y_btn_left")[2]).click();
                                2093 <= a & 2591 >= a && $($(".y_btn_left")[3]).click();
                                2659 <= a & 3153 >= a && $($(".y_btn_left")[4]).click();
                                3127 <= a & 3625 >= a && $($(".y_btn_left")[5]).click();
                                3618 <= a & 4086 >= a && $($(".y_btn_left")[6]).click();
                                break;
                            case 39:
                                a = $(window).scrollTop(), 120 <= a & 754 >= a && $(".yConuloutright").click(), 604 <= a & 1087 >= a && $($(".y_btn_right")[0]).click(), 1070 <= a & 1570 >= a && $($(".y_btn_right")[1]).click(), 1640 <= a & 2128 >= a && $($(".y_btn_right")[2]).click(), 2093 <= a & 2591 >= a && $($(".y_btn_right")[3]).click(), 2659 <= a & 3153 >= a && $($(".y_btn_right")[4]).click(), 3127 <= a & 3625 >= a && $($(".y_btn_right")[5]).click(), 3618 <= a & 4086 >= a && $($(".y_btn_right")[6]).click()
                        }
                    })
                }, 500);
            } else {

            }

        }, function(err) {

        });


        var indexVM = avalon.define({
                $id: "indexPageCtrl",
                goodsBlock:[],
                blockIndex:0,
                isLoading:false,
                newGoodList:[],
                noticeList:[],
                nowTime:'',
                luckyData:[],
                isGetNewGoods:false,
                isGetHotGoods:false,
                isGetNotice:false,
                isShare: false,
                isDisplayNav: false,  		//是否显示首页侧面固定导航，
                navItem:[],					//侧面固定导航的标题
                timer:[],					//定时器
                shareList: [],
                hasGetClassifyList:false,
                good:[],
                classifyList:[],
                changeActiveType:'',
                //玩家动态
                palyNewsList:[],
                scrollTop: function(){		//返回顶部
                	$(document).scrollTop(0)
                },
                playNews: function(b){
                	if (b) {
                		indexVM.palyNewsList = indexVM.newGoodList;
                	}else{
	                	indexVM.palyNewsList = [];
                	}
                },
                //结束玩家动态
                changeType:function(goods_type_id) {
                    var index = getBlockIndex();
                    if(index > -1 && !indexVM.isLoading) {
                        indexVM.changeActiveType = goods_type_id;
                        changeGoodList(goods_type_id,null,'ing',null,1,5,0,1,index);
                    }
                }

        })
		avalon(window).bind("scroll", function(e) { 
			if ($('.yCon0').length) {
					var offsetHeight = $('.yCon0').offset().top,
						height = $('.yCon0').height(),
						elHeight = $(document).scrollTop()
					;
			        if ($(document).scrollTop() > offsetHeight-200) {
			//      	console.log($('#nav_fixed'));
			      		indexVM.isDisplayNav = true;
			        }else{
			        	indexVM.isDisplayNav = false;
			        }
			        //点亮楼层
//			        if (elHeight > offsetHeight) {
//				        console.log(Math.ceil((elHeight-offsetHeight)/height))
//			        }
			}
	    });
	    var timer;
	    $(document).delegate('#nav_fixed .toolbar-item:not(.toolbar-item-no)','mouseenter',function(event){
	    	
	    	var me = this,
	    		parent = $(me).parent(),
	    		idx = $(me).parent().children().index(me);
	    	if ($(me).children().length==1) {
		    	$(me).append('<span style="color:#FFFFFF"></span>')
		    	$(me).children().eq(1).html(indexVM.navItem[idx]).show();
		    	$(me).children().eq(0).hide();
	    	}
	    })
	    $(document).delegate('#nav_fixed .toolbar-item:not(.toolbar-item-no)','mouseleave',function(event){
		    var me = this;
		    if ($(me).children().length == 2) {
		    	$(me).children().eq(1).remove();
		    	$(me).children().eq(0).show();
	    	}
	    })
	    $(document).delegate('#nav_fixed .toolbar-item:not(.toolbar-item-no)','click',function(){
//	    	$(this).parent().css('background-color','#E21931');
//	    	$(this).parent().siblings().css('background-color','#626262');
	    	var index = $('#nav_fixed>ul>li').index(this)-1,
	    		height = $('.yCon0').height(),
	    		scrollTop = $(document).scrollTop(),
	    		fixNav = $('.yNavIndexOut_fixed').height(),
	    		elHeight = 0,													//用户判断处于哪一楼层
	    		offsetHeight = $('.yCon0').offset().top + 3*height,				//手机电脑到页面顶部的高度
	    		x = 20;
	    	if (timer !== 'undefined') {
	    		clearInterval(timer)
	    	}
	    	timer = setInterval(function(){
		    		$(document).scrollTop($(document).scrollTop()+height/2);
		    		if($(document).scrollTop() >= (offsetHeight+height*index)){
//		    			$(document).scrollTop($(document).scrollTop()-fixNav-height/2-100)
		    			$(document).scrollTop(offsetHeight+index*height+100)
		    			clearInterval(timer)
		    		}
	    		},500)
	    	$(document).scrollTop(offsetHeight+index*height+100);
	    	
	    }) 
        function changeGoodList (goods_type_id,key_word,order_key,order_type,from,count,status,activity_type,index) {
            indexVM.isLoading = true;
            httpFactory.getActivityList(goods_type_id,key_word,order_key,order_type,from,count,status,activity_type,function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    var data = re.data;
                    indexVM.goodsBlock[index].goodsList = re.data;
                    $("img.lazyload").lazyload({
                        threshold : 100,
                        placeholder:'img/loading-200.gif'
                    });
                } else {
                    layer.msg(re.msg)
                }
            }, function(err) {
                layer.msg(JSON.parse(err).msg)
            },function() {
                indexVM.isLoading = false;
            });
        }

        function getBlockIndex() {
            // var len = indexVM.goodsBlock.length;
            var goodsBlock = indexVM.goodsBlock;
            for (var i = 0,len = goodsBlock.length; i < len; i++) {
                if(goodsBlock[i].blockTitle == '即将揭晓') {
                    return i;
                }
            };
            return -1;
        }

        //热门推荐数据
        function getHotGoods() {
            indexVM.isGetHotGoods = true;
            httpFactory.noParams('getHotGoods',function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    indexVM.good = re.data;
                } else {
                    layer.msg(re.msg)
                }

            }, function(err) {
                layer.msg(JSON.parse(err).msg)
            },function() {
                indexVM.isGetHotGoods = false;
            });
        }

        getHotGoods()

        var classifyList = [];
        getClassify()
        function getClassify() {
            var classify = avalon.cookie.get("classifyList");
            if(classify == 'unfinshed') {
                setTimeout(function () {
                   getClassify()
                },100)
            } else {
                classify = JSON.parse(classify).list;
                classifyList.push({
                    name:'即将揭晓',
                    goods_type_id:null,
                    shou:1
                })
//              classifyList.push({
//                  name:'新品上架',
//                  goods_type_id:1111,
//                  shou:1
//              })
                classifyList.push({
                    name:'十元专区',
                    goods_type_id:11112,
                    shou:1
                })
//              indexVM.navItem = [].concat(classifyList);
                var allClassify = [];
                for(var i = 0,len = classify.length;i < len;i++) {
                    if(classify[i].shou == 1) {
                        classifyList.push(classify[i])
                    }
                    if(classify[i].name != '全部') {
                        allClassify.push(classify[i])
                    }
                }

                // if(allClassify.length > 14) allClassify.length = 14;
                
//              indexVM.classifyList = allClassify;
//              indexVM.navItem = indexVM.navItem.concat(indexVM.classifyList)
//              主页晒单
				classifyList.push({
                    name:'晒单分享',
                    goods_type_id:true,
                    shou:1
                })
//				indexVM.shareList.push({
//                  name:'晒单分享',
//                  goods_type_id:true,
//                  shou:1
//              })
//				主页晒单 end
                indexVM.hasGetClassifyList =true;
            }
        }

        $(window).on('scroll', function () {
          var scrollTop = $(this).scrollTop();
          var winHeight = $(this).height();
          var docHeight = $(document).height();
          if (scrollTop + winHeight + 500 > docHeight) {
            if(!indexVM.hasGetClassifyList) return;
            if(indexVM.blockIndex < classifyList.length) {
                if(indexVM.isLoading) return;
                if(classifyList[indexVM.blockIndex].name == '即将揭晓') {
                    getGoodList(classifyList[indexVM.blockIndex].goods_type_id,null,'ing',null,1,5,0,0);
                } else if(classifyList[indexVM.blockIndex].name == '新品上架') {
                    getGoodList(null,null,'weight',null,1,5,0,0);
                } else if(classifyList[indexVM.blockIndex].name == '十元专区') {
                    getGoodList(null,null,'ing',null,1,5,0,2);
                }else if(classifyList[indexVM.blockIndex].name == '晒单分享'){
                	if (indexVM.isShare) {
                		return;
                	}
                	!indexVM.isShare && get_share_index();
                    indexVM.isShare = true;
                }else {
                    getGoodList(classifyList[indexVM.blockIndex].goods_type_id,null,null,null,1,5,0,0);
                }

            }
          }
        });
		function get_share_index(){
			httpFactory.getShare_list(null,'hot',1,4,1,function(re){
				re = JSON.parse(re);
                if (re.code == 0) {
                    var data = re.data;
                    if(data.length > 0) {
//                      indexVM.goodsBlock.push({
//                          blockTitle:classifyList[indexVM.blockIndex].name,
//                          goods_type_id:classifyList[indexVM.blockIndex].goods_type_id,
//                          goodsList:data
//                      });
                        indexVM.shareList.push({
                            blockTitle:'晒单分享',
                            goods_type_id:true,
                            shareIndex: indexVM.goodsBlock.length+2,
                            goodsList:data
                        })
                    }
                    indexVM.blockIndex++;
                    $("img.lazyload").lazyload({
                        threshold : 100,
                        placeholder:'img/loading-200.gif'
                    });
                } else {
                    layer.msg(re.msg)
                }
			})
		}
        function getGoodList (goods_type_id,key_word,order_key,order_type,from,count,status,activity_type) {
            indexVM.isLoading = true;
            httpFactory.getActivityList(goods_type_id,key_word,order_key,order_type,from,count,status,activity_type,function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    var data = re.data;
                    if(data.length > 0) {
                        indexVM.goodsBlock.push({
                            blockTitle:classifyList[indexVM.blockIndex].name,
                            goods_type_id:classifyList[indexVM.blockIndex].goods_type_id,
                            goodsList:data
                        })
//                   indexVM.navItem.push(classifyList[indexVM.blockIndex].name); 
                    }
                    indexVM.blockIndex++;
                    $("img.lazyload").lazyload({
                        threshold : 100,
                        placeholder:'img/loading-200.gif'
                    });
                } else {
                    layer.msg(re.msg)
                }
            }, function(err) {
                layer.msg(JSON.parse(err).msg)
            },function() {
                indexVM.isLoading = false;
            });
        }
        function navItem(){		//获取首页导航数据
	        httpFactory.noParams('getCategoryList',function(re) {
	        	re = JSON.parse(re);
	        	$.each(re.data,function(i,n){
//	        		console.log(n.shou)
	        		if (n.name == '亿七公益' || n.shou==0) {
//	        			console.log('亿七公益')
	        			return true;
	        		}
	        		indexVM.navItem[indexVM.navItem.length] = n.name;
	        	})
	      	});
        }
        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function(state) {
                if(state.params.inviteCode) {

                    avalon.cookie.set('inviteCode',state.params.inviteCode.split('#')[0])
                }
                avalon.vmodels["headerEidget"].activePage = 'index';
//              var timer = setInterval(function(){
//	                	if (indexVM.navItem.length) {
//	                		console.log('end')
//	                		clearInterval(timer)
//	                	}
				if (!indexVM.navItem.length) {		//获取首页导航数据，暂时不用
//					console.log('加载数据')
	                navItem()
				}
//              	},1000)
                getNewGoods()
                indexVM.isDisplayNav = false;
                $("img.lazyload").lazyload({
                    threshold : 100,
                    placeholder:'img/loading-200.gif'
                });
                if(banner.pics.length > 1) {
                    $('#banner').flexslider({
                            animation: "slide",
                            direction: "horizontal",
                            easing: "swing"
                    });
                }


            }

            $ctrl.$onBeforeUnload = function() {
            	clearInterval(timer)
            }

            $ctrl.$vmodels = []

        })
    })
