define(function(require, exports, module) {
	
	require('artdialog');


	$('.js-edit').on('click',function(){
		var id = $(this).attr('data-id');
		var text = $(this).text();
		dialog({
			content:'确定'+text+'吗？',
			width: '180px',
			okValue: "确定",
			cancelValue: '取消',
			cancel: true,
			quickClose: true,
			follow: this,
			ok: function() {
				this.close().remove();
				$.getJSON('?c=activity&a=modifyAssign',{"id":id},function(re){
					if(re.code > 0){
						location.reload();
					}else{
						altDialog('操作失败');
					}
				});
			}
		}).show();
	});


	/**
	 * 添加指定中奖名单
	 */
	$('.js-add').on('click',function(e){
		e.preventDefault();
		var name = $('textarea[name=list]').val();
		$.post('?c=activity&a=addAssign',{"name":name},function(re){
			$('#add').modal('hide');
			if(re.code > 0){
				altDialog('添加成功',function(){
					location.reload();
				});
			}else{
				altDialog(re.msg);
			}
		},'json');
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
