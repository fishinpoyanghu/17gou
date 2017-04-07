define(function(require, exports, module) {
	require('artdialog');
	require('libs/WdatePicker');

	$('.js_picker').on('click',function(){
		WdatePicker({skin:'blueFresh',dateFmt:'yyyy-MM-dd',isShowWeek:false});
	});


	//搜索
	$('#search').on('click',function(){
		var key = $('input[name=keyword]').val();
		var start = $('input[name=start]').val();
		var end = $('input[name=end]').val();
		var search = "";
		search += start ? "&start="+start :"";
		search += end ? "&end="+end :"";
		search +=  key ? "&keyword="+key : "";
		location.href = "?c=finance&a=recharge"+search;
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
