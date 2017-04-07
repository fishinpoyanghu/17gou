define(function(require, exports, module) {
	require('libs/select2');
	require('artdialog');
	var Piclib = require('piclib');


	//$(".js-user-select").select2();
	//获取添加链接时的banner_id
	$('body').on('click','.js-link',function(){
		window.BANNER_ID = $(this).attr('data-id');
		var goods_id = $(this).attr('data-goods_id');
        var url = $(this).attr('data-url');
        $('input[name=url]').val(url);
		$("select[name=goods] option[value="+goods_id+"]").prop('selected',1);
		$(".js-user-select").select2();
	});
	//添加链接
	$('.js-add-link').on('click',function(){
		var goods_id = $('select[name=goods]').val();
        var url = $('input[name=url]').val();
        $.getJSON("?c=goods&a=addpcLink",{"id":window.BANNER_ID,"goods_id":goods_id,"url":encodeURIComponent(url)},function(re){
			$('#add_link').modal('hide');
			if(re.code > 0){
				altDialog(re.msg,function(){
					location.reload();
				});
			}else{
				altDialog(re.msg);
			}
		});
	});

	//关闭或发布banner
	$('.js-use').on('click',function(){
		var id = $(this).attr('data-id');
		var title = $(this).text();
		dialog({
			content:'确定'+title+'吗？',
			width: '150px',
			okValue: "确定",
			cancelValue: '取消',
			cancel: true,
			quickClose: true,
			follow: this,
			ok: function() {
				this.close().remove();
				$.getJSON('?c=goods&a=editpcBanner',{'id':id},function(re){
					if(re.code > 0){
						location.reload();
					}else{
						dialog({
							content:title+'失败！',
							width: '150px',
							okValue: "确定",
							ok: function() {
								this.close().remove();
							}
						}).show();
					}
				});
			}
		}).show();
	});

	//删除banner
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
				$.getJSON('?c=goods&a=delpcBanner',{'id':id},function(re){
					if(re.code > 0){
						location.reload();
					}else{
						altDialog('删除失败！');
					}
				});
			}
		}).show();
	});

	//添加banner
	$('.js-add').on('click',function(){
		var piclib = new Piclib({
			userPic: false,
			iconLib: '',
			size:'1920*400',
			tipsnew:'上传的图片小于1MB，尺寸为1920*400，格式为jpg，png。'
		}, function(url) {
			$.getJSON('?c=goods&a=addpcBanner',{'url':url},function(re){
				if(re.code > 0){
					location.reload();
				}else{
					altDialog('添加失败！');
				}
			});
		});
	});

	//排序
	$('input[name=sort]').on('change',function(){
		var sort = $(this).val();
		var id = $(this).attr('data-id');
		if(!sort || sort < 0){
			altDialog('排序不能小于零或空');
			return;
		}
		$.getJSON('?c=goods&a=sortpcBanner',{'id':id,'sort':sort},function(re){
			if(re.code > 0){
				altDialog(re.msg,function(){
					location.reload();
				});
			}else{
				altDialog(re.msg);
			}

		});
	});

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
