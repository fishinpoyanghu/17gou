define(function(require, exports, module) {
	require('artdialog');
	require('libs/WdatePicker');

	$('.js_picker').on('click',function(){
		WdatePicker({skin:'blueFresh',dateFmt:'yyyy-MM-dd',isShowWeek:false});
	});


	//搜索
	$('#query').on('click',function(){

		var people = $('#people').val();   
		var start=$('[name="start"]').val();
		var end=$('[name="end"]').val();
		$('#allmoney_info').find('td').html('');
		$.ajax({ 
				type: "POST",
			    url: '?c=finance&a=ajaxachievement', 
				dataType: 'json', 
				data:{'uid':people,'start':start,'end':end},
				success: function(r) {
				    if(r.code!=0){
						var $d = dialog({
							content: '<font color="red">'+r.msg+'</font>',
							width: '150px',
							quickClose: true,
							 
						}); 
						$d.show();
					 }else{
					 	 $('#allmoney_info').find('td').eq(0).html(r.count)
					 	 $('#allmoney_info').find('td').eq(1).html(r.money)
					 }
			 }
			});
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
