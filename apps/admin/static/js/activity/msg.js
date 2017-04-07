define(function(require, exports, module) {
	require('artdialog');
	require('libs/select2');

	$(".js-user-select").select2();
	var multiCheck = $('input[name="multi_delete"]');


	//搜索
	$('#search').on('click',function(){
		var key = $('input[name=keyword]').val();
		if(key){
			location.href = '?c=activity&a=msg&keyword='+key;
		}
	});

	// 全选
	$('#checkAll').on('change', function() {
		if ($(this).prop('checked')) {
			multiCheck.prop('checked', true);
			if(multiCheck.filter(':checked').length >=1){
				$('#multi_operate').show();
			}
		} else {
			multiCheck.prop('checked', false);
			$('#multi_operate').hide();
		}
	});
	//勾选显示按钮
	multiCheck.on('change',function(){
		if(multiCheck.filter(':checked').length >=1){
			$('#multi_operate').show();
		}else{
			$('#multi_operate').hide();
		}
	});



	/**
	 * 保存系统消息
	 */
	$('.js-sub').on('click',function(){
		var id = $('input[name=id]').val();
		var data = {
			"uid":$("select[name=to_uid]").val(),
			"content":$('textarea[name=content]').val()
		};

		$.getJSON("?c=activity&a=saveMsg",{"data":data,"id":id},function(re){
			if(re.code > 0){
				altDialog('保存成功',function(){
					location.reload();
				});
			}else{
				altDialog(re.state);
			}
		});
	});

	//单个删除
	$('.js-del').on('click',function(){
		var id = [$(this).attr('data-id')];
		sysMsgDel(id);
	});

	//批量删除
	$('.js-multi').on('click',function(){
		var id = [];
		$('input[name=multi_delete]:checked').each(function(){
			id.push($(this).val());
		});
		sysMsgDel(id);
	});

	function sysMsgDel(id){
		dialog({
			content:'确定删除吗？',
			width: '150px',
			okValue: "确定",
			cancelValue: '取消',
			cancel: true,
			quickClose: true,
			ok: function() {
				this.close().remove();
				$.getJSON("?c=activity&a=msgDel",{"id":id},function(re){
					if(re.code > 0){
						location.reload();
					}else{
						altDialog('删除失败');
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
