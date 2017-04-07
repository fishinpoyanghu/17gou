/* 
* @Desc:表格异步显示封装类
*/

define(function(require, exports, module) {
	
  var template = require('libs/artTemplate');
  var util = require('mods/util');
  require('artdialog');
  
  var $tbl = $("#tbl").val();
  
  var Tablelib = function(options, callback) {
    this.options = {
      tbl_id : 'dp-staff-tbl',
      dep_id : 0,
      page : 1,
      kw : ''
    }
    this.init(options);
    if (typeof callback === 'function') {
        this.callback = callback;
    }
  };

  Tablelib.prototype.init = function(options) {
    this.initElements(options);
    this.initTable(this.options.dep_id, this.options.page, this.options.kw);
  }

  Tablelib.prototype.initElements = function(options) {
    this.options = $.extend(this.options, options);
    
    this.ajaxTpl =   '{{each tbl_data as item i}}' +
                     '<tr>' +
                     '{{each item as ele m}}'+
                     '{{if m == "admin_id"}}'+
                     '<td><div class="fl"><input name="multi_delete" class="dp-checkbox" type="checkbox" value="{{ele}}" /></div><div class="dp-pic-and-txt fl"><a href="" title="点击预览" target="_blank">{{ele}}</a></div></td>'+
                     '{{else}}'+
                     '<td><p>{{#ele}}</p></td>' +
                     '{{/if}}'+
                     '{{/each}}'+
                     '</tr>' +
                     '{{/each}}';
  }
  
  // 初始化表格
  Tablelib.prototype.initTable = function($dep_id, $page, $kw) {
	  var $postData = {
		        page: $page,
		        dep_id: $dep_id,
		        kw: $kw
		      }
	  this.updata($postData, $("#"+this.options.tbl_id+" tbody"));
  }

  // Ajax更新数据
  Tablelib.prototype.updata = function(postData, target) {
    var self = this;
    var $page_dom = $("#dp-tbl-page");
    var render = template.compile(self.ajaxTpl);
    
    self.options.page = postData.page;
    self.options.dep_id = postData.dep_id;
    self.options.kw = postData.kw;
    
    $.post('?c=department&a=ajax_get_staff_list&dep_id='+postData.dep_id+'&page='+postData.page+'&kw='+encodeURIComponent(postData.kw), function(res) {
      res = JSON.parse(res);      
      data = res.data;
      
      var _html = render(data);
      target.html(_html);
      self.bindTableEvent();
      
      util.pageHtml2($page_dom, data.tbl_count, data.page, data.pagesize);
      self.bindPageEvent($page_dom, postData, target);
      
    });
  }
  
  // 绑定表格的相关事件
  Tablelib.prototype.bindTableEvent = function() {
	  var self = this;
	  
	  // table hover over out
	  $("#"+this.options.tbl_id+" tbody tr").hover(function() {
		  $(this).find('span[row-type="td-ops"]').show();
	  }, function() {
		  $(this).find('span[row-type="td-ops"]').hide();
      });
	  
	// 全选
	  var $multiCheck = $('input[name="multi_delete"]');
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
		                	var $postData = {
		            		        page: self.options.page,
		            		        dep_id: self.options.dep_id,
		            		        kw: self.options.kw
		            		      };
		                	self.updata($postData, $("#"+self.options.tbl_id+" tbody"));
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
			                	var $postData = {
			            		        page: self.options.page,
			            		        dep_id: self.options.dep_id,
			            		        kw: self.options.kw
			            		      };
			                	self.updata($postData, $("#"+self.options.tbl_id+" tbody"));
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
	    
	 // 搜索 start
		$('#searchBtn').on('click', function() {
			var $kw = $('#kw').val();
			
			var $postData = {
    		        page: self.options.page,
    		        dep_id: self.options.dep_id,
    		        kw: $kw
    		      };
        	self.updata($postData, $("#"+self.options.tbl_id+" tbody"));
		});
		
		// 如果有modal edit，做相关的事件绑定处理
			
		$('a[data-open="dialog"]').on('click', function() {
			
			dialog({
				title: '编辑员工',
			    url: '/?c=staff&a=edit&admin_id='+$(this).attr('data-id'),
			    width: 600,
			    height: 450,
			    onclose: function() {
			    	if (this.returnValue) {
			    		
			    		if (parseInt(self.options.dep_id) == parseInt(this.returnValue.department_id)) {
				    		var $postData = {
		            		        page: self.options.page,
		            		        dep_id: self.options.dep_id,
		            		        kw: self.options.kw
		            		      };
		                	self.updata($postData, $("#"+self.options.tbl_id+" tbody"));
			    		}
			    		else {
			    			self.callback(this.returnValue.department_id);
			    			//$('#deptree .list-group-item[node-id="'+$('#deptree .node-selected').attr('node-id')+'"]').click();
			    		}
			    	}
			    }
			}).showModal();
		});
		// modal edit end
  }

 // 绑定分页按钮点击事件
 Tablelib.prototype.bindPageEvent = function (dom, postData, target) {
    var self = this;

    dom.find('a').click(function () {
      var thisPage = $(this).attr('data-page');
      postData.page = parseInt(thisPage);
      self.updata(postData, target);
    });
    
    $('button[btn-type="refresh_btn"]').click(function() {
    	self.updata(postData, target);
    });
  }
  module.exports = Tablelib;
});