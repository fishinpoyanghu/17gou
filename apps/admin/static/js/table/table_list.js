define(function(require, exports, module) {
	
	require('artdialog');
	var $modalUploadFlag = $("#modal_uploadFlag").val();  // 是否有modal add
	var Piclib = require('piclib');
	
	exports.init = function() {
		
		var $tbl = $("#tbl").val();
		
		// table hover over out
		/*
		$("#dp-list-tbl tbody tr").hover(function() {
			$(this).find('span[row-type="td-ops"]').show();
		}, function() {
			$(this).find('span[row-type="td-ops"]').hide();
		});
		*/
		
		// 如果有上传按钮，做相关的事件处理
		$('#table_upload_btn').on('click', function() {
			var piclib = new Piclib({
	            userPic: false,
	            iconLib: '',
	            uploadNum: 'multi',
	            tbl: $tbl
	        }, function(url) {
	          if (url) {	       
	            location.reload();
	          }
	        });
		});
		
		// 搜索 start
		$('#searchBtn').on('click', function() {
			
			var $url = "./?c=table&tbl="+$tbl;
			
			$.each($('.dp-page-head [data-type="search"]'), function() {
				$url += '&'+$(this).attr('name')+'='+encodeURIComponent($(this).val());
			});
			
			location.href = $url+'&random='+new Date().getTime();
		});
		
		var $multiCheck = $('input[name="multi_delete"]');
		
		// 全选
	    $('#checkAll').on('change', function() {
	        if ($(this).prop('checked')) {
	            $multiCheck.prop('checked', true);
	            $('#multi_operate').show();
	        } else {
	            $multiCheck.prop('checked', false);
	            $('#multi_operate').hide();
	        }
	    });
	    
	    // 单条删除
	    $('body').on('click', '.table .js-delete', function() {
	        var $id = $(this).attr('data-id');
	        
	        dialog({
			    content: $(this).attr('msg-confirm'),
			    okValue: '确定',
			    ok: function() {
			    	this.close().remove();
			    	$.getJSON('?c=table&a=del&tbl='+$tbl, {'id': $id}, function(r) {
			    		if (r.code == 0) {
		                	location.reload();
		                } else {
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
	    
	    // 多条删除
	    $('body').on('click', '#multi_delete', function() {
	        var $a = [];

	        $.each($('input[name=multi_delete]:checked'), function() {
	            $a.push($(this).val());
	        });
	        
	        if ($a.length == 0) {
	        	dialog({
					content: '至少应该选择一项',
					width: '150px',
					okValue: "确定",
					ok: function() {
						this.close().remove();
					},
				    follow: this
				}).show();
	        }
	        else {
		        var $b = $a.join(',');
		        
		        dialog({
				    content: $(this).attr('msg-confirm'),
				    okValue: '确定',
				    ok: function() {
				    	this.close().remove();
				    	$.getJSON('?c=table&a=del&tbl='+$tbl, {'ids': $b}, function(r) {				    		
			                if (r.code == 0) {
			                	location.reload();
			                } else {
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
	        }
	    });
	    
	    // 如果有modal reset，做相关的事件绑定处理	    
	    // 重置密码
	    $('body').on('click', '.table .js-reset', function() {
	        var $id = $(this).attr('data-id');
	        
	        dialog({
			    content: '确定要重置密码？',
			    okValue: '确定',
			    ok: function() {
			    	this.close().remove();
			    	$.getJSON('?c=table&a=reset&tbl='+$tbl, {'id': $id}, function(r) {
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
		
		// 如果有$classify modify，做相关的事件处理
			
		$('#modify_classify_btn').on('click', function() {
			
			var $a = [];

	        $.each($('input[name=multi_delete]:checked'), function() {
	            $a.push($(this).val());
	        });
	        
	        if ($a.length == 0) {
	        	dialog({
					content: '至少应该选择一项',
					width: '150px',
					okValue: "确定",
					ok: function() {
						this.close().remove();
					},
				    follow: this
				}).show();
	        }
	        else {
		        var $b = $a.join(',');
				dialog({
					
				    url: '/?c=table&a=classify&tbl='+$tbl+'&pkids='+$b,
				    width: 300,
				    height: 100,
				    onclose: function() {
				    	if (this.returnValue) {
				    		
				    		tree_select_node('deptree', this.returnValue.department_id);
				    		$('#deptree .list-group-item[node-id="'+this.returnValue.department_id+'"]').click();
				    	}
				    },
				    follow: this
				}).show();
	        }
		});
	}
});
