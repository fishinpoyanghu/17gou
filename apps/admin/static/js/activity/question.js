define(function(require, exports, module) {
	
	require('artdialog');
	var Piclib = require('piclib');


	require.async(window.STATIC_LIST+'/js/libs/ueditor/ueditor.config.js', function () {
		require.async(window.STATIC_LIST+'/js/libs/ueditor/ueditor.all.min.js', function () {
			var editor = UE.getEditor('detail', {
				sourceEditor: "codemirror",
				wordImagePath: '',
				wordCount: false,
				lang: 'zh-cn',
				elementPathEnabled: false,
				initialContent: '',
				initialFrameWidth: '80%',
				initialFrameHeight: 600,
				maximumWords: 0,
				autoClearEmptyNode: false,
				autoHeightEnabled: false,
				initialStyle: 'body{font-size:12px}',
				toolbars: [
					['fullscreen', 'source', '|', 'undo', 'redo', '|',
						'bold', 'italic', 'underline', 'strikethrough', 'removeformat', 'formatmatch', 'autotypeset', '|',
						'pasteplain', '|', 'forecolor', 'backcolor', '|', 'customstyle',
						'paragraph', '|', 'lineheight', '|', 'fontfamily', 'fontsize', '|',
						'indent',
						'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|',
						'link', 'unlink', 'anchor', '|', 'insertimage', 'insertvideo', '|',
						'inserttable', '|',
						'preview', 'searchreplace', 'help']
				],
				webAppKey: "",
				removeFormatTags: 'b,big,code,del,dfn,i,ins,kbd,q,samp,small,strike,sub,sup,tt,u,var',
				removeFormatAttributes: 'lang,hspace',
				maxUndoCount: 50
			});
			editor.ready(function () {
				editor.setContent($('#aaa').html());
			});

			editor.addListener('contentChange', function () {
				window.QUESTION_DETAIL = editor.getContent();
			});
		});
	});

	$('.js-sub').on('click',function(){
		$.post('?c=activity&a=saveQuestion',{"content":window.QUESTION_DETAIL},function(re){
			if(re.code > 0){
				altDialog('保存成功')
			}else{
				altDialog(re.msg?re.msg : '保存失败');
			}
		},'json');
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
