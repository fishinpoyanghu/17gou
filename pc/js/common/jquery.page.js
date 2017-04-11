//分页插件
/**
2014-08-05 ch
**/
(function($){
	var ms = {
		init:function(obj,args){
			return (function(){
				ms.unBindEvent(obj);
				ms.fillHtml(obj,args);
				ms.bindEvent(obj,args);
			})();
		},
		//填充html
		fillHtml:function(obj,args){
			return (function(){
				obj.empty();
				if(args.pageCount==0){
					return;
				}
				//上一页
				
				if(args.current > 1){
					//obj.append('<span><a href="javascript:;" >上一页</a></span>');
					obj.append('<span ><a href="javascript:;" title="上一页" class="prevPage"><i class="f-tran f-tran-prev">&lt;</i>上一页</a></span>')
				}else{
					//obj.remove('.prevPage');
					obj.append('<span class="f-noClick"><a href="javascript:;"><i class="f-tran f-tran-prev">&lt;</i>上一页</a></span>');
				}
				//中间页码
				if(args.current != 1 && args.current >= 4 && args.pageCount != 4){
					obj.append('<span><a href="javascript:;"  class="tcdNumber" >'+1+'</a></span>');
				}
				if(args.current-2 > 2 && args.current <= args.pageCount && args.pageCount > 5){
					obj.append('<span>...</span>');
				}
				var start = args.current -2,end = args.current+2;
				if((start > 1 && args.current < 4)||args.current == 1){
					end++;
				}
				if(args.current > args.pageCount-4 && args.current >= args.pageCount){
					start--;
				}
				for (;start <= end; start++) {
					if(start <= args.pageCount && start >= 1){
						if(start != args.current){
							//obj.append('<span><a href="javascript:;" >'+ start +'</a></span>');
							obj.append('<span><a href="javascript:;" class="tcdNumber">'+ start +'</a></span>');
						}else{
							obj.append('<span class="current"><a>'+ start +'</a></span>');
						}
					}
				}
				if(args.current + 2 < args.pageCount - 1 && args.current >= 1 && args.pageCount > 5){
					obj.append('<span>...</span>');
				}
				if(args.current != args.pageCount && args.current < args.pageCount -2  && args.pageCount != 4){
					//obj.append('<span><a href="javascript:;" >'+args.pageCount+'</a></span>');
					obj.append('<span><a href="javascript:;" class="tcdNumber">'+args.pageCount+'</a></span>');
				}
				//下一页
				if(args.current < args.pageCount){
					//obj.append('<span><a href="javascript:;" >下一页</a></span>');
					obj.append('<span><a title="下一页" href="javascript:;" class="nextPage">下一页<i class="f-tran f-tran-next">&gt;</i></a></span>');
				}else{
					//obj.remove('.nextPage');
					//obj.append('<span class="disabled">下一页</span>');
					obj.append('<span class="f-noClick"><a>下一页<i class="f-tran f-tran-next">&gt;</i></a></span>');
				}
				//共 页  去 页   确定
				obj.append('<span class="f-mar-left">共<em>'+args.pageCount+'</em>页，去第</span>');
				obj.append('<span><input type="text" id="txtGotoPage" value="'+args.current+'">页</span>');
				obj.append('<span class="f-mar-left"><a title="确定" href="javascript:;" id="btnGotoPage">确定</a></span>');
				
			})();
		},
		//绑定事件
		bindEvent:function(obj,args){
			return (function(){
				obj.on("click","a.tcdNumber",function(){
					var current = parseInt($(this).text());
					ms.fillHtml(obj,{"current":current,"pageCount":args.pageCount});
					if(typeof(args.backFn)=="function"){
						args.backFn(current);
					}
				});
				//上一页
				obj.on("click","a.prevPage",function(){
					var current = parseInt(obj.children("span.current").text());
					ms.fillHtml(obj,{"current":current-1,"pageCount":args.pageCount});
					if(typeof(args.backFn)=="function"){
						args.backFn(current-1);
					}
				});
				//下一页
				obj.on("click","a.nextPage",function(){
					var current = parseInt(obj.children("span.current").text());
					ms.fillHtml(obj,{"current":current+1,"pageCount":args.pageCount});
					if(typeof(args.backFn)=="function"){
						args.backFn(current+1);
					}
				});
				obj.on("click","#btnGotoPage",function(){
					var gotoPage = parseInt(obj.children("span").children("input").val());
					if(gotoPage>args.pageCount){
						gotoPage=args.pageCount;
					}
					ms.fillHtml(obj,{"current":gotoPage,"pageCount":args.pageCount});
					if(typeof(args.backFn)=="function"){
						args.backFn(gotoPage);
					}
				});
				obj.on("keydown","#txtGotoPage",function(event){
				   if(event.keyCode == "13"){
						var gotoPage = parseInt(obj.children("span").children("input").val());
						if(gotoPage>args.pageCount){
							gotoPage=args.pageCount;
						}
						ms.fillHtml(obj,{"current":gotoPage,"pageCount":args.pageCount});
						if(typeof(args.backFn)=="function"){
							args.backFn(gotoPage);
						}
			        }
				});
			})();
		},
		//取消绑定事件
		unBindEvent:function(obj){
			obj.empty();
			return (function(){
				obj.off("click","a.tcdNumber");
				//上一页
				obj.off("click","a.prevPage");
				//下一页
				obj.off("click","a.nextPage");
				//确定  跳转到第几页
				obj.off("click","#btnGotoPage");
				//确定  调转到第几页
				obj.off("keydown","#txtGotoPage");
			})();
			
		}
	}
	$.fn.createPage = function(options){
		var args = $.extend({
			pageCount : 10,
			current : 1,
			backFn : function(){}
		},options);
		ms.init(this,args);
	}
	$.fn.deletePage = function(){
		ms.unBindEvent(this);
	}
	
})(jQuery);
