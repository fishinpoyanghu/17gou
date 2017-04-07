var $global_editor_list = {};
define(function(require, exports, module) {
	
	require('wangEditor')($);	
	
	exports.init = function() {
				
		$.each($('textarea[row-type="editor"]'), function() {
			var $editor_key = $(this).attr('name');
			
			var $editor = $(this).wangEditor({
				'uploadUrl' : '/?c=pic&a=editor_assist_page',
				'pasteUrl' : '/?c=pic&a=paste_upload',
				'menuConfig': [
				               ['bold', 'underline', 'italic', 'foreColor', 'backgroundColor', 'strikethrough'],
				               ['blockquote', 'fontSize', 'setHead', 'list', 'justify'],
				               ['createLink', 'unLink', 'insertTable'],
				               ['insertImage', 'insertVideo', 'insertLocation','insertCode'],
				               ['undo', 'redo', 'fullScreen']
				           ]
			});
			
			$global_editor_list[$editor_key] = $editor;
		});
	}
});
