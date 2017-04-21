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
		location.href = "?c=finance&a=consume"+search;
	});
	$(function(){

		var key = $('input[name=keyword]').val();
		var start = $('input[name=start]').val();
		var end = $('input[name=end]').val();
		var search = "";
		search += start ? "&start="+start :"";
		search += end ? "&end="+end :"";
		search +=  key ? "&keyword="+key : "";
 
		$.ajax({ 
			type: "POST",
		    url: '?c=finance&a=ajaxtotal'+search, 
			dataType: 'json', 
			//data:{'uid':people,'start':start,'end':end},
			success: function(r) { 
			    $('.totalheji').html('合计:'+r.total);
		 }
		});



	})
	$('.js-export').on('click',function(){
		var start = $('input[name=start]').val();
		var end = $('input[name=end]').val();
		var search = "";
		search += start ? "&start="+start :"";
		search += end ? "&end="+end :"";
		location.href = "?c=finance&a=download"+search;
	});

	//查看夺宝号
	$('.js-look').on('click',function(){
		var id = $(this).attr('data-id');
		$('tr[data-id='+id+']').toggleClass('hide');
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
