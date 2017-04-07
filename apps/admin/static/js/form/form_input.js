define(function(require, exports, module) {
	
	var get_ipt_vlist = function(form_id) {
		
		var $ipt_v = {}; // 添加表单的值列表
		var $ipt_m = {}; // 添加表单的值是否获取的mark列表
		
		var $ipt_list = $('#'+form_id+' [data-form="'+form_id+'"]');
		$.each($ipt_list, function() {
			
			if ($(this).attr("row-type") == "radio") {
				var _key = $(this).prop('name');
				
				if ($ipt_m[_key] == undefined) {
					$ipt_m[_key] = 1;
					$ipt_v[_key] = $('#'+form_id+' input:radio[name="'+$(this).prop('name')+'"]:checked').val();
				}
			}
			else if ($(this).attr("row-type") == "checkbox") {
				var _key = $(this).prop('name');
				
				if ($ipt_m[_key] == undefined) {
					$ipt_m[_key] = 1;
					var _tmp = [];
					$.each($('#'+form_id+' input:checkbox[name="'+$(this).prop('name')+'"]:checked'), function() {
						_tmp.push($(this).val());
					});
					$ipt_v[_key] = _tmp.join(',');
				}
			}
			else {
				$ipt_v[$(this).prop('name')] = $(this).val();
			}
		});
		
		return $ipt_v;
	}
	
	module.exports = get_ipt_vlist;
});
