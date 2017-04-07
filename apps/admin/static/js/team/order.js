define(function(require, exports, module) {
	
	require('artdialog');


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


	$('.js-entering').on('click',function(){
		 window.id = $(this).attr('data-id');
		 window.ordernum=$(this).attr('data-ordernum');
		 var trobj=$(this).closest('tr');
		 console.log($(this).closest('tr').find('.get-name').html())
		 $('#express').find('[name=phone]').val(trobj.find('.get-phone').html())
		 $('#express').find('[name=nick]').val(trobj.find('.get-name').html())
		 $('#express').find('[name=addr]').val(trobj.find('.get-add').html())
	});

	$('.js-sub').on('click',function(){
		$('#express').modal('hide');
		var code = $('select[name=express]').val();
		var logistics_num = $('input[name=logistics_num]').val();
		var phone = $('input[name=phone]').val();
		var nick = $('input[name=nick]').val();
		var addr = $('input[name=addr]').val();
		console.log(phone);console.log(addr);
		$.getJSON('?c=activity&a=addExpress',{"id":window.id,"code":code,"num":logistics_num,"ordernum":window.ordernum,"addr":addr,"nick":nick,"phone":phone},function(re){
			if(re.code > 0){
				altDialog('录入成功',function(){
					location.reload();
				});
			}else{
				altDialog('录入失败！');
			}
		});
	});

});
