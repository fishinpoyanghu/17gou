define(function(require, exports, module) {
	require('artdialog');
	var multiCheck = $('input[name="multi_delete"]');


	//搜索
	$('#search').on('click',function(){
		var key = $('input[name=keyword]').val();
		var search = key? "&keyword="+key : "";
		if(key){
			location.href = '?c=activity&a=cash'+search;
		}
	});

	//通过
	$('.js-pass').on('click',function(){
		var id = $(this).attr('data-id');
		var text = $(this).text();
		modifyState(id,text,2);
	});
	//确认打款
	$('.js-affirm').on('click',function(){
		var id = $(this).attr('data-id');
		modifyState(id,'打款',4);
	});

	function modifyState(id,text,state){
		dialog({
			content:'确定'+text+'吗？',
			width: '150px',
			okValue: "确定",
			cancelValue: '取消',
			cancel: true,
			quickClose: true,
			ok: function() {
				this.close().remove();
				$.getJSON("?c=activity&a=modifyCash",{"id":id,"state":state},function(re){
					if(re.code > 0){
						altDialog(text+'成功',function(){
							location.reload();
						});
					}else{
						altDialog(text+'失败');
					}
				});
			}
		}).show();
	}

	//提示
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
