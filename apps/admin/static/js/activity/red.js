define(function(require, exports, module) {

	require('artdialog');
	var Piclib = require('piclib');
	var multiCheck = $('input[name="multi_delete"]');


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


	//批量修改
	$('.js-modify').on('click',function(){
		var id = [];
		var type = $('select[name=type]').val();
		$('input[name=multi_delete]:checked').each(function(){
			id.push($(this).val());
		});

		$.post('?c=activity&a=modifyRed',{"ids":id,"type":type},function(re){
			$('#m_modify').modal('hide');
			if(re.code > 0){
				altDialog('修改成功',function(){
					location.reload();
				});
			}else{
				altDialog(re.msg);
			}
		},'json');
	});

	//删除
	$('.js-del').on('click',function(){
		var id = $(this).attr('data-id');
		dialog({
			content:'确定删除吗？',
			width: '150px',
			okValue: "确定",
			cancelValue: '取消',
			cancel: true,
			quickClose: true,
			follow: this,
			ok: function() {
				this.close().remove();
				$.getJSON('?c=activity&a=delRed',{"id":id},function(re){
					if(re.code > 0){
						altDialog('删除成功！',function(){
							location.reload();
						});
					}else{
						altDialog('删除失败！');
					}
				});
			}
		}).show();
	});

	//添加/编辑红包
	$('.js-sub').on('click',function(){
		var id = $('input[name=id]').val();
		var data = {
			"title":$('input[name=title]').val(),
			"sub_title":$('input[name=sub_title]').val(),
			"price":$('input[name=price]').val(),
			"need_num":$('input[name=need_num]').val()
		};

		$.post('?c=activity&a=addRed',{"data":data,"id":id},function(re){
			if(re.code > 0){
				altDialog(re.msg,function(){
					location.reload();
				});
			}else{
				altDialog(re.msg);
			}
		},'json');
	});


	//开始第一期
	$('.js-start').on('click',function(){
		var id = $(this).attr('data-id');
		dialog({
			content:'确定开始第一期吗？',
			width: '150px',
			okValue: "确定",
			cancelValue: '取消',
			cancel: true,
			quickClose: true,
			follow: this,
			ok: function() {
				this.close().remove();
				$.getJSON('?c=activity&a=redStart',{"id":id},function(re){
					if(re.code > 0){
						altDialog('操作成功！',function(){
							location.reload();
						});
					}else{
						altDialog('操作失败！');
					}
				});
			}
		}).show();
	});

});
