define(function(require, exports, module) {
	require('artdialog');
	require('echarts');
	require('libs/WdatePicker');


	var myChart = echarts.init(document.getElementById('main'));
	var option = {
		title : {
			text: '总计：'+window._TOTAL_,
			subtext: ''
		},
		tooltip : {
			trigger: 'axis'
		},
		legend: {
			data:['消费额']
		},
		toolbox: {
			show : true,
			feature : {
				dataView : {show: false, readOnly: false},
				magicType : {show: true, type: ['line', 'bar']},
				restore : {show: true},
				saveAsImage : {show: true}
			}
		},
		calculable : true,
		xAxis : [
			{
				type : 'category',
				data : window._TIME_
			}
		],
		yAxis : [
			{
				type : 'value'
			}
		],
		series : [{
				name:'消费额',
				type:'bar',
				stack: '消费额',
				itemStyle: {
					normal: {
						color:'#2f4554'
					}
				},
				label: {
					normal: {
						show: true,
						position: 'top'
					}
				},
				data:window._DATA_,
				markLine : {
					data : [
						{type : 'average', name : '平均值'}
					]
				}
			}
		]
	};

	myChart.setOption(option);


	$('.js_picker').on('click',function(){
		WdatePicker({skin:'blueFresh',dateFmt:'yyyy-MM-dd',isShowWeek:false});
	});

	//查询
	$('.js-screen').on('click',function(){
		var start = $('input[name=start]').val();
		var end = $('input[name=end]').val();
		var search = "";
		search += start ? "&start="+start :"";
		search += end ? "&end="+end :"";
		location.href = "?c=finance"+search;
	});


	//错误提示
	function altDialog(content,callback){
		dialog({
			content:content,
			width: '150px',
			okValue: "确定",
			ok: function() {
				this.close().remove();
				if(typeof callback === 'function'){
					callback();
				}
			}
		}).show();
	}

});
