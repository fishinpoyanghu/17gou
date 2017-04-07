define(function(require, exports, module) {
	
	require('artdialog');

	$('#searchBtn').on('click',function(){
		var key = $('input[name=keyword]').val();
		if(key){
			location.href = '?c=goods&a=lotteryRecord&keyword='+key;
		}
	});

	//选择积分或实物
	$('select[name=type]').on('change',function(){
		var type = $('select[name=type] option:selected').val();
		var title = type==1 ? '奖品名称：':(type==2?'金钱：单位元':'积分：') ;
		$('.js-text').text(title);
	});

	//添加奖品
	$('.js-add').on('click',function(){
		var type = $('select[name=type] option:selected').val();
		var content = $('input[name=content]').val();
		$.getJSON('?c=goods&a=addLottery',{"type":type,"content":content},function(re){
			if(re.code > 0){
				location.reload();
			}else{
				$('#add').modal('hide');
				altDialog(re.msg);
			}
		});
	});

	//删除奖品
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
				$.getJSON('?c=goods&a=delLottery',{'id':id},function(re){
					if(re.code > 0){
						altDialog('删除成功',function(){
							location.reload();
						});
					}else{
						altDialog('删除失败');
					}
				});
			}
		}).show();
	});

	//提交中奖率
	$('.js-sub').on('click',function(){
		var percent = [];
		$('input[name=percent]').each(function(){
			percent[$(this).attr('data-id')] = $(this).val();
		});

		$.post('?c=goods&a=saveLottery',{"percent":percent},function(re){
			if(re.code > 0){
				altDialog('操作成功',function(){
					location.reload();
				});
			}else{
				altDialog('提交失败');
			}
		},'json');
	});


	$('.js-entering').on('click',function(){
		window.id = $(this).attr('data-id');
	});


	$('.js-express').on('click',function(){
		$('#express').modal('hide');
		var code = $('select[name=express]').val();
		var logistics_num = $('input[name=logistics_num]').val();
		$.getJSON('?c=goods&a=addExpress',{"id":window.id,"code":code,"num":logistics_num},function(re){
			if(re.code > 0){
				altDialog('录入成功',function(){
					location.reload();
				});
			}else{
				altDialog('录入失败！');
			}
		});
	});


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
