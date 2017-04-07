define(function(require, exports, module) {
	
	require('artdialog');
	var Piclib = require('piclib');
	var multiCheck = $('input[name="multi_delete"]');

	//商品分类搜索
	$('#searchBtn').on('click',function(){
		var key = $('input[name=keyword]').val();
		if(key){
			location.href = '?c=team&a=teamclassify&keyword='+key;
		}
	});

	//商品搜索
	$('#search').on('click',function(){
		 search();
		 
	});
	function search(){
		var key = $.trim($('input[name=keyword]').val());
		var cate = $('select[name=cate]').val();
		var activity_type = $('select[name=activity_type]').val();   
	    location.href = '?c=team&a=goodsList&keyword='+key+'&activity_type='+activity_type+'&cate='+cate;
	}
	$('[name=activity_type],[name=cate]').on('change',function(){
		search();
	})
	 
    $('.remen').click(function(){
        var id = $(this).attr('data-id');
        $.getJSON('?c=team&a=remen&id='+id,function(re){
            errorDialog(re.msg,function(){
                location.reload();
            });
        });
    });

    $('.tianjia').click(function(){
        var id = $(this).attr('data-id'); 
        $.post('?c=team&a=showindex',{"id":id,'show':1},function(re){
            if(re.code > 0){
                altDialog(re.msg,function(){
                    location.href = "?c=team&a=teamclassify";
                });
            }else{
                altDialog(re.msg);
            }
        },'json');
    });

    $('.qudiao').click(function(){
        var id = $(this).attr('data-id');
        $.post('?c=team&a=showindex',{"id":id,'show':0},function(re){
            if(re.code > 0){
                altDialog(re.msg,function(){
                    location.href = "?c=team&a=teamclassify";
                });
            }else{
                altDialog(re.msg);
            }
        },'json');
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

	//单个删除
	$('.js-del').on('click',function(){
		var id = [$(this).attr('data-id')];
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
				delGoodsCfy(id);
			}
		}).show();
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

	//删除商品分类
	function delGoodsCfy(id){
		var ids = id.join(',');
		$.getJSON('?c=team&a=delGoodsCfy',{'ids':ids},function(re){
			if(re.code > 0){
				location.reload();
			}else{
				errorDialog('删除失败！');
			}
		});
	}

	//错误提示
	function errorDialog(content,callback){
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


	//图片上传
	$('.js-upload').on('click',function(e){
		e.preventDefault();
		var piclib = new Piclib({
			userPic: false,
			iconLib: ''
		},function(url){
			$('.js-img').attr('src',url).next().hide().parent().show();
			$('.js-upload').hide();
		});
	});

	$('.delete').on('click',function(){
		$(this).prev().attr('src','').parent().hide();
		$('.js-upload').show();
	});

	$('.js-sub').on('click',function(e){
		e.preventDefault();
        var id = $('input[name=id]').val();
        var pid = $('input[name=pid]').val();
		var name = $('input[name=name]').val();
		var url = $('.js-img').attr('src');
		var sort=$('input[name=sort]').val();
		$.getJSON('?c=team&a=editGoodsCfy',{'id':id,'name':name,'url':url,'pid':pid,'sort':sort},function(re){
			if(re.code > 0){
				errorDialog(re.msg,function(){
					location.href = "?c=team&a=teamclassify";
				});
			}else{
				errorDialog(re.msg);
			}
		});
	});



	//商品列表-确定修改
	$('.js-modify').on('click',function(){
		var id = [];
		var type = $('select[name=type] option:selected').val();
		$('input[name=multi_delete]:checked').each(function(){
			id.push($(this).val());
		});

		$.post('?c=team&a=modifyGoods',{"ids":id,"type":type},function(re){
			$('#m_modify').modal('hide');
			if(re.code > 0){
				errorDialog(re.msg,function(){
					location.reload();
				});
			}else{
				errorDialog(re.msg);
			}
		},'json');
	});

	//商品列表-删除商品
	$('.js-goods-del').on('click',function(){
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
				$.getJSON('?c=team&a=delGoods',{"id":id},function(re){
					if(re.code > 0){
						errorDialog('删除成功！',function(){
							location.reload();
						});
					}else{
						errorDialog(re.msg);
					}
				});
			}
		}).show();
	});

	//开始第一期
	$('.js-goods-start').on('click',function(){
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
				$.getJSON('?c=team&a=startFirst',{"id":id},function(re){
					if(re.code > 0){
						errorDialog('操作成功！',function(){
							location.reload();
						});
					}else{
						errorDialog('操作失败！');
					}
				});
			}
		}).show();
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
