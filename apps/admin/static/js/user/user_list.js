define(function(require, exports, module) {
	
	require('artdialog');
	 
	var multiCheck = $('input[name="multi_delete"]');

	//商品分类搜索
	$('#searchBtn').on('click',function(){
		var key = $('input[name=keyword]').val();
		if(key){
			location.href = '?c=goods&a=classify&keyword='+key;
		}
	});

	//商品搜索
	$('#search').on('click',function(){
		 search();
		 
	});
	function search(){
		var key = $.trim($('input[name=keyword]').val());
		var cate = $('select[name=cate]').val();
		var type = $('select[name=type]').val();   
	    location.href = '?c=user&a=userList&keyword='+key
	    //+'&type='+type+'&cate='+cate;
	}
	$('[name=type],[name=cate]').on('change',function(){
		search();
	})
	  $('body').on('click', '.js-reset', function() {
	        var $id = $(this).attr('data-id');
	        
	        dialog({
			    content: '确定要重置密码？',
			    okValue: '确定',
			    ok: function() {
			    	this.close().remove();
			    	$.getJSON('?c=table&a=reset&tbl=user.list', {'id': $id}, function(r) {
		                if (r.code == 0) {
		                	dialog({
		    					content: '新密码是：'+r.msg,
		    					width: '200px',
		    					okValue: "确定",
		    					ok: function() {
		    						this.close().remove();
		    					}
		    				}).show();
		                }
		                else {
		                	dialog({
		    					content: r.msg,
		    					width: '150px',
		    					okValue: "确定",
		    					ok: function() {
		    						this.close().remove();
		    					}
		    				}).show();
		                }
		            });
			    },
			    cancelValue: '取消',
			    cancel: true,
			    quickClose: true,
			    follow: this
			}).show();
	    });


  $('body').on('click', '.js-commission', function() {
	        var $id = $(this).attr('data-id');
	        var $c = $(this).attr('data-c');
	        dialog({
			    content: '确定分润吗？',
			    okValue: '确定',
			    ok: function() {
			    	this.close().remove();
			    	$.getJSON('?c=user&a=commission', {'id': $id,'chosen':$c}, function(r) {
		                if (r.code == 0) {
		                	dialog({
		    					content: '执行失败',
		    					width: '200px',
		    					okValue: "确定",
		    					ok: function() {
		    						this.close().remove();
		    						window.location.href=location.href
		    					}
		    				}).show();
		                }
		                else {
		                	dialog({
		    					content: '执行成功',
		    					width: '150px',
		    					okValue: "确定",
		    					ok: function() {
		    						this.close().remove();
		    						window.location.href=location.href
		    					}
		    				}).show();
		                }
		            });
			    },
			    cancelValue: '取消',
			    cancel: true,
			    quickClose: true,
			    follow: this
			}).show();
	    });
    //添加金额的函数，弹出带有可填入金额的输入框的对话框，要有保存和确定

    $('body').on('click', '.js-addmoney', function() {
	        var $id = $(this).attr('data-id');
	        dialog({
			    content: '<div id="dialog-form" title="添加金额"> <p class="validateTips" style="line-height: 28px; margin-bottom: 10px;">  </p> ' +
                '<form>'+
                '<fieldset style="padding:0;border: 0;">'+
                '<label for="monkey">添加金额</label>&nbsp;&nbsp;'+
                '<input type="number" name="monkey" id="monkey" class="text ui-widget-content ui-corner-all">'+
                '</fieldset>'+
                '</form>'+
                '</div>',
			    okValue: '保存',
			    ok: function() {
			    	var money=$('#monkey').val(); 
			    	this.close().remove();
                    $.getJSON('?c=user&a=addmoney', {'id': $id,'money':money}, function(r) {
                        if (r.code == 1) {
                            dialog({
                                content: '添加金额:'+r.msg+'元成功！',
                                width: '200px',
                                okValue: "确定",
                                ok: function() {
                                	location.href=location.href;
                                    //this.close().remove();
                                }
                            }).show();
                        }
                        else {
                            dialog({
                                content: r.msg,
                                width: '150px',
                                okValue: "确定",
                                ok: function() {
                                    this.close().remove();
                                }
                            }).show();
                        }
                    });
			    },
			    cancelValue: '取消',
			    cancel: true,
			    quickClose: true,
			    follow: this
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
