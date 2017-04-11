    
	$(function(){
		//左侧添加样式
		$($(".c_out_a")[13]).addClass("c_click_newa"); 
		var param={
			page:1,
			pageSize:10
		}
		tableId='#scoreSource'
		//获得积分来源列表
		ajaxMyOrder(param,bindOrderList);//初始化查询 page=1
	});
	
	/*
	 * 积分来源页面
	 * 购买商品获得积分、签到获得积分、邀请好友获得积分、晒单获得积分
	 * */
	function ajaxMyOrder(param,bindOrderList){
		$.ajax({
			type:'get',
			url:"/valentinesDay/myOrder.do?randomTime=" + (new Date()).getTime(),
			data:param,
			dataType:'json',
			success:function(result){
				bindOrderList(param,result)
			}
		});
	}
	
	
	//按具体时间搜索
	function orderSearch(){
		var startTime = $('#startTime').val();
		var endTime = $('#endTime').val();
		if(startTime==""&&endTime!=''){
			layer.tips('请选择开始时间!', '#startTime');
		}else if(endTime==""&&startTime!=''){
			layer.tips('请选择结束时间!', '#endTime');
		}else if(endTime<startTime){
			layer.tips('开始时间应该小于结束时间!', '#endTime');
		}else{
			var param={
					page:1,
					pageSize:10,
					stareTime:startTime,
					endTime:endTime
				}
			ajaxMyOrder(param,bindOrderList);
		}
	}
	
	/*
	 * 描述：绑定奖励专区积分来源记录
	 * 参数：json 会员积分来源结果
	 * */
	function bindOrderList(param,json){
				
		var str = "<tr class='b_part_title'>";
			str	+="<th class='b_th1'>流水号</th>";
			str	+="<th class='b_th2'>商品名称</th>";
			str +="<th class='b_th3'>购买时间</th>";
			str +="<th class='b_th4'>人次</th>";
			str +="<th class='b_th5'>夺宝号码</th>";
			str +="</tr>"; 	
		if (json.pageModelList.totalPage > 0) {	
			for(i=0;i<json.myOrder.length;i++){
				str += "<tr style='height: 70px;'>";
				str += "<td>"+json.myOrder[i].orderNo+"</td>";     
				str += "<td>情人节活动</td>";     
				str += "<td class='time"+i+"'>"+formatDate(json.myOrder[i].addTime.time)+"</td>";     
				str += "<td class='times"+i+"'>"+json.myOrder[i].buyTimes+"</td>";                  
				str += "<td>"+getNo(json.myOrder[i].codes,i)+"</td>";     
				str += "</tr>";     	
			}
			$("#scoreSource").html(str);	
		}else{
			isNull("no_num","scoreSource",165);		
		}
		
		pageCount=json.pageModelList.totalPage;//总页码
		page=json.pageModelList.page;//当前页
		orderListPage(param);//生成分页
		
		if(json.tPublish){
			$(".a_cloud_goods").show();
			$("#nickname").html(json.tPublish.nickname);
			$("#code").html(json.tPublish.code);
			$("#money").html(json.winSumAll);
		}
		if(json.win){
			$(".a_cloud_goods").html('<span>恭喜您获得豪华旅游大奖</span>');
		}
	}	
	
	/*数据分页*/
	function orderListPage(param){
		$("#pageStr").createPage({
			pageCount:pageCount,
			current:page,
			backFn:function(p){
				param.page=p;
			    ajaxMyOrder(param,bindOrderList);
			}
		});
	}
	/*处理夺宝号码
	 * str 夺宝号码
	 * c 行号
	 * */
	function getNo(str, c) {
		var strArray = str.split(",");
		var result = "";
		for (var i = 0; i < 8 && i < strArray.length; i++) {
			result += "<i>" + strArray[i] + " </i>";
		}
		result = result.replace(/\"/g, "");
		result = result.replace("[", "");
		result = result.replace("]", "");
		if (strArray.length < 8) {
			return "<div class='b_cloud_code'>" + result + "<span class='code" + c
					+ "' style='display:none'>" + str + "</span></div>"
		} else {
			return "<div class='b_cloud_code'>" + result + "<span class='code" + c
					+ "' style='display:none'>" + str
					+ "</span><a href='javascript:void(0);' id='more_"+c+"' onclick='more(" + c
					+ ");' class='b_periods_num'>查看更多&gt;</a></div>"
		}
	}	
/*显示全部夺宝号码
 * b 行号 
 * */
function more(b){
	$("#b_cloud_window").show();
	$(".b_msgbox_bj").show();
	//展示用户购买的夺宝号码
	$("#b_cloud_window .m-detail-codesDetail-wrap").html("");
	$('#b_cloud_window h3').html('您本期总共参与了<span class="txt-red">'+$(".times"+b).text()+'</span>人次');
	var str = [];
	var arr = '';
	str.push('<dl class="m-detail-codesDetail-list f-clear">');
	str.push('<dt>投资时间：'+$('.time'+b).text()+'</dt>');
	arr = $('.code'+b).text().replace('[','').replace(']','').replace(/"/g,'').replace(/,/g,'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').split(',');
	str.push("<p style='word-wrap: break-word;'>"+arr+"</p>");
	$("#b_cloud_window .m-detail-codesDetail-wrap").html(str.join(''));
	$("#b_cloud_window").css({left:($(window).width()-$("#b_cloud_window").width())/2,top:($(window).height()-$("#b_cloud_window").height())/2});
	$(".b_msgbox_bj").height($("body").height());
}
/*关闭夺宝号码窗口*/
function closeCodesPane(){
	$("#b_cloud_window").hide();
	$(".b_msgbox_bj").hide();
}