define(['avalon', 'http/http-factory','components/view-countdown/count'],
    function(avalon, httpFactory,countdowm) {

        var announced = avalon.define({
            $id: "announcedCtrl",
            newest: [],
            hasNextPage: true,
            pageIndex: 1,
			//测试页面
            newPageIndex:1,
			totalPages:1,
            count:0,
            arrCount: [],
            isDisplayAnimation: true,
			//结束测试页面
			nextShowGoods: [],	//即将揭晓的数据
            isFinished: true,
            pageCount:12,
            nowTime:'',
            array: ["","","","","","","",""],
            text:"",
            timer:"",
            toggleStart:true,
            toggleLi:false,
            toggleTxt:false,
            timer2:"",
            start:function(){
            	tanchu();
                announced.timer=setInterval(function(){
                    announced.toggleStart=false;
                    announced.toggleLi=true;
                    for(var i=0;i<announced.array.length;i++){
                        announced.array.set(i,Math.floor(Math.random() * 10));
                    }
                }, 50);
                announced.timer2=setInterval(function(){
                    clearInterval(announced.timer);
                    if(announced.array[1]==1){
                        announced.toggleTxt=true;
                        /*announced.text="恭喜你拿到幸运号码！";*/

                    }
                    else{
                        announced.toggleTxt=true;
                       /* announced.text="遗憾，下次再来一次。";*/
                    }
                    clearInterval(announced.timer2);
                },3000)
            },
            getNextPage: function(adjust) {
            	var me = $(this);
            	if (me.hasClass('current')) {//判断是不是按钮跳转
            		announced.pageIndex = me.text()-1;
            	}
//              if (announced.hasNextPage && announced.isFinished) {
	            	if (typeof adjust != 'number') {//判断是不是数字跳转
	                    announced.pageIndex++;
	            	}else{
	            		announced.pageIndex= announced.newPageIndex;
	            	}
	            	//如果获取当前页超过了最大的页数，就把页数换成最大的值
	            	announced.pageIndex > announced.totalPages && (announced.pageIndex = announced.totalPages);
	            	//如果获取当前页为0，就把页数换成1
	            	!parseInt(announced.pageIndex) && (announced.pageIndex = 1);
	            	announced.newPageIndex = announced.pageIndex;
	            	announced.hasNextPage = !(announced.totalPages == announced.pageIndex);
//	            	if (announced.totalPages == announced.pageIndex) {
//	            		announced.hasNextPage = false;
//	            		console.log("no pages");
//	            	}
                    getNewest();
//              } else {
                    return;
//              }
            },
            getPrevPage: function() {
                if (announced.pageIndex > 1) {
                    announced.pageIndex--;
                    //分页修改pageindex和hasNextPage
                    announced.newPageIndex = announced.pageIndex;
                    announced.hasNextPage = true;
                    //结束分页修改
                    getNewest();
                } else {
                    return;
                }
            }
//          getBtnNum: function(num){
//          	var Index = parseInt(announced.pageIndex),arr = [];
//          	if (num < 6 ) {
//          		for(var i = 1; i <= num; i++){
//          			arr[i-1] = i;
//          		}
//          		announced.arrCount = arr;
//          	} else{
//          		if (Index >= 3) {
//	            		announced.arrCount = [Index-2,Index-1,Index,Index+1,Index+2];
//          		}else{
//          			announced.arrCount = [1,2,3,4,5];
//          		}
//          		if (num - Index < 2) {
//          			announced.arrCount = [num-4,num-3,num-2,num-1,num];
//          		}
//          	}
////          	console.log(announced.arrCount)
////          	console.log(Index)
//          }
            
        })
		
        function timeoutCallback(activity_id) {
            httpFactory.getGoodsDetail(activity_id, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    if(re.data.status != 2) {
                        setTimeout(function() {
                            timeoutCallback(activity_id)
                        },1000)
                        return;
                    }
                    var newGoodList = announced.newest;
                    for(var i = 0,len = newGoodList.length;i < len;i++) {
                        if(newGoodList[i].activity_id == activity_id) {
                            var newActivity = re.data;
                            newGoodList[i].status = newActivity.status;
                            newGoodList[i].lucky_uicon = newActivity.lucky_uicon;
                            newGoodList[i].publish_time = newActivity.publish_time;
                            newGoodList[i].lucky_unick = newActivity.lucky_unick;
                            newGoodList[i].lucky_num = newActivity.lucky_num;
                            newGoodList[i].lucky_user_num = newActivity.lucky_user_num;
                            newGoodList[i].lucky_ip = newActivity.lucky_ip;
                           
                            return;
                        }
                    }
                } else {

                }

            }, function(err) {

            });
        }

        //最新揭晓
        function getNewest() {
//          announced.isFinished = false;
//          console.log(announced.pageIndex)
            httpFactory.getActivityList('', '', '', '', (announced.pageIndex - 1) * announced.pageCount + 1, announced.pageCount, 3, '', function(re) {
                re = JSON.parse(re)
                if (re.code == 0) {
                    announced.newest = [];
                    announced.newest = re.data;
                    announced.nowTime = +new Date() + 1000;
                    //测试数据
//					re.count = 36;                    
                    
                    announced.count = re.count;
                    announced.totalPages = Math.ceil(re.count / announced.pageCount);
                    announced.totalPages == 1 && (announced.hasNextPage = false);
                    httpFactory.getBtnNum( announced, announced.totalPages);
                    announced.isDisplayAnimation = false;
                    //结束测试数据
//                  console.log(announced.count)
//                  console.log(announced.totalPages)
                    countdowm.start(function(activity_id) {
                        timeoutCallback(activity_id)
                    })
//                  if (re.data.length == announced.pageCount) {
//                      announced.hasNextPage = true;
//                  }else{
//                      announced.hasNextPage = false;
//                  }
                }else{

                }

            }, function(err) {

            }, function() {
                announced.isFinished = true;
            });
        }
		function willShowGoods(){
			httpFactory.getActivityList('','','ing','',1,4,0,0,function(re) {
				re = JSON.parse(re);
				announced.nextShowGoods = re.data;
				$("img.lazyload").lazyload({
                    threshold : 100,
                    placeholder:'img/loading-200.gif'
                });
				console.log(re)
			})
		}
    
        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'announced';
                announced.pageIndex = 1;
                announced.newPageIndex = 1;
                getNewest();
                willShowGoods()
            }
            $ctrl.$onBeforeUnload = function() {}

            // 指定一个avalon.scan视图的vmodels，vmodels = $ctrl.$vmodels.concact(DOM树上下文vmodels)
            $ctrl.$vmodels = [];

        })
    })
