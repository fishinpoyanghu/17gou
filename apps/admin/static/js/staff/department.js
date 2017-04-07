define(function(require, exports, module) {
	
	var Tablelib = require('components/tablelib');
	require('artdialog');
		
	function tree_node_expand($treeid, $nodeid) {
		
		$.each($('#'+$treeid+' .list-group-item[node-pid="'+$nodeid+'"]'), function() {
			$(this).show();
		});
	}
	function tree_node_collapse($treeid, $nodeid) {
		$.each($('#'+$treeid+' .list-group-item[node-pid="'+$nodeid+'"]'), function() {
			$(this).hide();
			$(this).attr('expand', "0");
			$(this).find('.expand-icon').removeClass('icon-caret-down').addClass('icon-caret-right');
			tree_node_collapse($treeid, $(this).attr('node-id'));
		});
	}
	
	// 选中指定节点，本函数不做数据加载
	function tree_select_node($treeid, $nodeid) {
		
		var $node = $('#'+$treeid+' .list-group-item[node-id="'+$nodeid+'"]');
		$node.show();
		
		var $node_pid = $node.attr('node-pid');
		
		while ($('#'+$treeid+' .list-group-item[node-id="'+$node_pid+'"]').length > 0) {
			
			$('#'+$treeid+' .list-group-item[node-id="'+$node_pid+'"]').show();
			$('#'+$treeid+' .list-group-item[node-id="'+$node_pid+'"]').attr('expand', "1");
			$('#'+$treeid+' .list-group-item[node-id="'+$node_pid+'"]').find('.expand_icon').removeClass('icon-caret-right').addClass('icon-caret-down');
			
			$node_pid = $('#'+$treeid+' .list-group-item[node-id="'+$node_pid+'"]').attr('node-pid');
		}
	}
	
	exports.init = function() {
		
		var $tbl_id = 'dp-staff-tbl';
		var $company_id = parseInt($("#dp_company_id").val());
		
		var tablelib = new Tablelib({
        	tbl_id : $tbl_id,
        	dep_id : $company_id
        }, function($dep_id) {
        	tree_select_node('deptree', $dep_id);
    		$('#deptree .list-group-item[node-id="'+$dep_id+'"]').click();
        });
		
		// 默认选中最高级别的节点
		$('#deptree .list-group-item[node-id="'+$company_id+'"]').addClass('node-selected');
				
		$.each($('#deptree .list-group-item'), function() {
			$(this).bind({
					click: function() {
						// 先移除其他的选中node
						$('#deptree .node-selected').find('.node-icon').removeClass('icon-folder-open').addClass('icon-folder-close');
						$('#deptree .node-selected').removeClass('node-selected');
						$(this).addClass('node-selected');
						$(this).find('.node-icon').removeClass('icon-folder-close').addClass('icon-folder-open');
						
						// 加载这个节点的员工
						tablelib.initTable($(this).attr('node-id'), 1, '');
					},
					
					mouseenter: function() {
						$(this).find('.badge').show();
					}, 
					mouseleave: function() {
						$(this).find('.badge').hide();
					}
			});
		});
		
		// 节点展开、缩起图标处理
		$.each($('#deptree .expand-icon'), function() {
			$(this).bind({
				click: function() {
					if ($(this).parent().attr('expand') == "1") {
						$(this).parent().attr('expand', "0");
						$(this).removeClass('icon-caret-down').addClass('icon-caret-right');
						
						tree_node_collapse('deptree', $(this).parent().attr('node-id'));
						
					}
					else {
						$(this).parent().attr('expand', "1");
						$(this).removeClass('icon-caret-right').addClass('icon-caret-down');
						
						tree_node_expand('deptree', $(this).parent().attr('node-id'));
					}
				}
			});
		});
		
		// 编辑节点处理
		$.each($('#deptree .badge i[node-ops="dep_edit"]'), function() {
			$(this).bind({
				click: function() {
					var self = $(this).parent().parent();
					
					var $d = dialog({
					    title: '编辑部门',
					    url: '/?c=department&a=edit&dep_id='+$(this).parent().parent().attr('node-id'),
					    onclose: function() {
					    	if (this.returnValue) {
					    		
					    		self.find('.node-text').html(this.returnValue.name);
					    	}
					    }
					});
					$d.showModal();
				}
			});
		});
		
		// 创建子节点处理
		$.each($('#deptree .badge i[node-ops="dep_add"]'), function() {
			$(this).bind({
				click: function() {
					var self = $(this).parent().parent();
					var $node = self.clone(true);
					
					// 节点展开
					var $d = dialog({
					    title: '创建部门',
					    url: '/?c=department&a=add&dep_pid='+$(this).parent().parent().attr('node-id'),
					    onclose: function() {
					    	if (this.returnValue) {
					    		
					    		if (self.attr('expand') == "0") {
					    			self.find('.expand-icon').click();
					    		}					    		
					    		
					    		var $level = parseInt(self.attr('node-level'))+1;
					    		
					    		$node.removeClass('node-selected');
					    		$node.attr('node-id', this.returnValue.department_id);
					    		$node.attr('node-pid', this.returnValue.pid);
					    		$node.attr('node-level', $level);
					    		$node.attr('expand', 0);
					    		$node.find('.indent').after('<span class="indent"></span>');
					    		$node.find('.expand-icon').removeClass('icon-caret-down').addClass('icon-caret-right');
					    		$node.find('.node-icon').removeClass('icon-folder-open').addClass('icon-folder-close');
					    		$node.find('.node-text').html(this.returnValue.name);
					    		$node.find('.node-staffcount').html('(0)');
					    		$node.find('.badge').hide();
					    		
					    		self.after($node);
					    		//alert(self.attr('node-level'));
					    	}
					    }
					});
					$d.showModal();
					//$(this).parent().parent().after('<li class="list-group-item" node-id="3" node-pid="2" node-level="3"><span class="indent"></span><span class="indent"></span><span class="icon expand-icon icon-caret-right"></span><span class="icon node-icon icon-folder-close"></span>页游平台<span class="badge" style="display: none;"><i class="icon-plus-sign" title="创建子部门" node-ops="dep_add"></i><i class="icon-trash" title="删除" node-ops="dep_delete"></i></span></li>');
				}
			});
		});
		
		// 删除节点
		$.each($('#deptree .badge i[node-ops="dep_delete"]'), function() {
			var self = this;
			$(this).bind({				
				click: function() {
					var $dep_id = $(this).parent().parent().attr('node-id');
					var $dep_pid = $(this).parent().parent().attr('node-pid');
					var $expand = $(this).parent().parent().attr('expand');
					
					// 节点展开
					var $d = dialog({
					    title: '提示',
					    content: '删除部门不会删除部门下的成员，确定删除？',
					    okValue: '删除',
					    ok: function() {
					    	$.post('?c=department&a=ajax_del_department&dep_id='+$dep_id, function(res) {
					    	      res = JSON.parse(res);      
					    	      
					    	      $d.close().remove();
					    	      // 删除成功
					    	      if (res.code == 0) {
					    	    	  
					    	    	  $('#deptree .list-group-item[node-id="'+$dep_pid+'"]').click();
					    	    	  $('#deptree .list-group-item[node-id="'+$dep_id+'"]').remove();
					    	      }
					    	      else {
					    	    	  alert(res.msg);
					    	      }
					    	});
					    },
					    cancelValue: '取消',
					    cancel: true,
					    quickClose: true,
					    follow: this
					});
					$d.show();
					// 输入节点名字，并保存
					
					//$(this).parent().parent().after('<li class="list-group-item" node-id="3" node-pid="2" node-level="3"><span class="indent"></span><span class="indent"></span><span class="icon expand-icon icon-caret-right"></span><span class="icon node-icon icon-folder-close"></span>页游平台<span class="badge" style="display: none;"><i class="icon-plus-sign" title="创建子部门" node-ops="dep_add"></i><i class="icon-trash" title="删除" node-ops="dep_delete"></i></span></li>');
				}
			});
		});
		
		// 添加 员工 事件处理
		$("#add_tableRow").on('click', function() {
			dialog({
				title: '添加员工',
			    url: '/?c=staff&a=add&dep_id='+$('#deptree .node-selected').attr('node-id'),
			    width: 600,
			    height: 450,
			    onclose: function() {
			    	if (this.returnValue) {
			    		
			    		tree_select_node('deptree', this.returnValue.department_id);
			    		$('#deptree .list-group-item[node-id="'+this.returnValue.department_id+'"]').click();
			    	}
			    }
			}).showModal();
		});
	}
});
