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


	//审核通过
	$('.js-pass').on('click',function(){
		var id = $(this).attr('data-id');
		changeStat(id,'通过',2,1);
	});
	//审核不通过
	$('.js-no-pass').on('click',function(){
		var id = $(this).attr('data-id');
		changeStat(id,'不通过',3,1);
	});
	//删除
	$('.js-del').on('click',function(){
		var id = $(this).attr('data-id');
		changeStat(id,'删除',1,1);
	});
	//晒单列表批量删除
	$('.js-multi').on('click',function(){
		multiDel(getCheckedId(),'?c=activity&a=multiShowDel',1);
	});


	//评论审核通过
	$('.js-com-pass').on('click',function(){
		var id = $(this).attr('data-id');
		changeStat(id,'通过',2,0);
	});
	//评论审核不通过
	$('.js-com-no-pass').on('click',function(){
		var id = $(this).attr('data-id');
		changeStat(id,'不通过',3,0);
	});
	//评论删除
	$('.js-com-del').on('click',function(){
		var id = $(this).attr('data-id');
		changeStat(id,'删除',1,0);
	});
	//评论审核批量删除
	$('.js-com-multi').on('click',function(){
		multiDel(getCheckedId(),'?c=activity&a=multiShowDel',0);
	});


	//获取选中的id
	function getCheckedId(){
		var id = [];
		$('input[name=multi_delete]:checked').each(function(){
			id.push($(this).val());
		});
		return id;
	}

	//弹出提示
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


	/**
	 * 修改状态
	 * @param id
	 * @param msg
	 * @param stat
	 * @param type
	 */
	function changeStat(id,msg,stat,type){
		dialog({
			content:'确定'+msg+'吗？',
			width: '150px',
			okValue: "确定",
			cancelValue: '取消',
			cancel: true,
			quickClose: true,
			ok: function() {
				this.close().remove();
				$.getJSON('?c=activity&a=modifyShow',{"id":id,"stat":stat,"type":type},function(re){
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
	}



	/**
	 * 批量删除
	 * @param id
	 * @param url
	 * @param type   1：晒单批量删除   0：评论批量删除
	 */
	function multiDel(id,url,type){
		dialog({
			content:'确定批量删除吗？',
			width: '150px',
			okValue: "确定",
			cancelValue: '取消',
			cancel: true,
			quickClose: true,
			ok: function() {
				this.close().remove();
				$.getJSON(url,{"id":id,"type":type},function(re){
					if(re.code > 0){
						location.reload();
					}else{
						altDialog('操作失败！');
					}
				});
			}
		}).show();
	}



});
