define(function(require, exports, module) {
	
	require('artdialog');
	var Piclib = require('piclib');
	var multiCheck = $('input[name="multi_delete"]');

	 $('#search').on('click',function(){
		var key = $('input[name=keyword]').val();
		var type=$('#type_id').val();
		var wherestr='';
		  wherestr+= type ? '&type='+type:'';
		  wherestr+=key?'&keyword='+key:'';

		if(key){
			location.href = '?c=activity'+wherestr;
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

	//结束活动
	$('.js-end').on('click',function(){
		var a_id = $(this).attr('data-id');
		dialog({
			content:'确定结束该活动吗？',
			width: '180px',
			okValue: "确定",
			cancelValue: '取消',
			cancel: true,
			quickClose: true,
			follow: this,
			ok: function() {
				this.close().remove();
				$.getJSON('?c=activity&a=endActivity',{"id":a_id},function(re){
					if(re.code > 0){
						altDialog(re.msg,function(){
							location.reload();
						});
					}else{
						altDialog(re.msg);
					}
				});
			}
		}).show();
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

	//单个指定中奖or取消指定
	$('.js-edit').on('click',function(){
		var id = $(this).attr('data-id');
		var text = $(this).text();
		dialog({
			content:'确定'+text+'吗？',
			width: '150px',
			okValue: "确定",
			cancelValue: '取消',
			cancel: true,
			quickClose: true,
			follow: this,
			ok: function() {
				this.close().remove();
				$.getJSON('?c=activity&a=assign',{"id":id},function(re){
					if(re.code > 0){
						location.reload();
					}else{
						altDialog('操作失败');
					}
				});
			}
		}).show();
	});

	//批量指定中奖
	$('.js-multi').on('click',function(){
		var id = [];
		$('input[name=multi_delete]:checked').each(function(){
			id.push($(this).val());
		});
		dialog({
			content:'确定批量指定中奖吗？',
			width: '150px',
			okValue: "确定",
			cancelValue: '取消',
			cancel: true,
			quickClose: true,
			follow: this,
			ok: function() {
				this.close().remove();
				$.post('?c=activity&a=multiAssign',{"id":id},function(re){
					if(re.code > 0){
						altDialog('操作成功',function(){
							window.location.href=window.location.href;
						});
					}else{
						altDialog('操作失败');
					}
				},'json');
			}
		}).show();
	});


	//参与记录查看夺宝号
	$('.js-look').on('click',function(){   
		var uid = $(this).attr('data-id');
		$("tr[data-id="+uid+"]").toggleClass('hide');
	});
	$('.js-chosenactivity').on('click',function(){
		var activity_id = $(this).attr('data-activity-id');
		var uid = $(this).attr('data-uid');  
		dialog({
			content:'确定选中此人吗？',
			width: '150px',
			okValue: "确定",
			cancelValue: '取消',
			cancel: true,
			quickClose: true,
			follow: this,
			ok: function() {
				this.close().remove();
				$.post('?c=activity&a=choseactivity',{"uid":uid,"activity_id":activity_id},function(re){
					if(re.code > 0){
						 altDialog('操作成功',function(){
							window.location.href=window.location.href;
						});
					}else{
						 altDialog('操作失败');
					}
				},'json');
			}
		}).show();
	});  
	$('.js-calcle-chosenactivity').on('click',function(){
		var activity_id = $(this).attr('data-activity-id');
		var uid = $(this).attr('data-uid');  
		dialog({
			content:'确定取消选中吗？',
			width: '150px',
			okValue: "确定",
			cancelValue: '取消',
			cancel: true,
			quickClose: true,
			follow: this,
			ok: function() {
				this.close().remove();
				$.post('?c=activity&a=choseactivity',{"uid":uid,"activity_id":activity_id,"cancel":1},function(re){
					if(re.code > 0){
						 altDialog('操作成功',function(){
							window.location.href=window.location.href;
						});
					}else{
						 altDialog('操作失败');
					}
				},'json');
			}
		}).show();
	});


 
 
});
