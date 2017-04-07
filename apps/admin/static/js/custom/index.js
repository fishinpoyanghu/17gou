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

	//多个删除
	$('#multi_delete').on('click',function(){
		var id = [];
		$('input[name=multi_delete]:checked').each(function(){
			id.push($(this).val());
		});
		dialog({
			content:'确定批量删除吗？',
			width: '150px',
			okValue: "确定",
			cancelValue: '取消',
			cancel: true,
			quickClose: true,
			follow: this,
			ok: function() {
				this.close().remove();
				delGoodsCfy(id);
			}
		}).show();
	});


	$('.js-save').on('click',function(){
		//刷单时间
		var time = [];
		$('input[name=hour]:checked').each(function(){
			time.push($(this).val());
		});
		var hour = time.join(',');
		//选中的商品id
		var id = [];
		$('input[name=multi_delete]:checked').each(function(){
			id.push($(this).val());
		});
		var goods_id = id.join(',');
		var data = {
			"jiange1" :$('input[name=interval_1]').val(),
			"jiange2" :$('input[name=interval_2]').val(),
			"stop" :$('input[name=stop]').val(),
			"state" :$('input[type=radio]:checked').val(),
			"money1":$('input[name=money1]').val(),
			"money2":$('input[name=money2]').val(),
			"hour" :hour,
			"goods" :goods_id
		};
		$.post("?c=custom&a=save",{"data":data},function(re){
			if(re.code > 0){
				altDialog('保存成功',function(){
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
