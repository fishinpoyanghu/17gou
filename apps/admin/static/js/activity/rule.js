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
				initialFrameWidth: '60%',
				initialFrameHeight: 190,
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
				editor.setContent(window.ONE_CONTENT);
			});

			editor.addListener('contentChange', function () {
				window.ONE_DETAIL = editor.getContent();
			});

			//抢红包规则
			var red = UE.getEditor('red', {
				sourceEditor: "codemirror",
				wordImagePath: '',
				wordCount: false,
				lang: 'zh-cn',
				elementPathEnabled: false,
				initialContent: '',
				initialFrameWidth: '60%',
				initialFrameHeight: 190,
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

			red.ready(function () {
				red.setContent(window.RED_CONTENT);
			});

			red.addListener('contentChange', function () {
				window.RED_DETAIL = red.getContent();
			});



			//抽奖规则
			var lottery = UE.getEditor('lottery', {
				sourceEditor: "codemirror",
				wordImagePath: '',
				wordCount: false,
				lang: 'zh-cn',
				elementPathEnabled: false,
				initialContent: '',
				initialFrameWidth: '60%',
				initialFrameHeight: 190,
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

			lottery.ready(function () {
				lottery.setContent(window.LOTTERY_CONTENT);
			});

			lottery.addListener('contentChange', function () {
				window.LOTTERY_DETAIL = lottery.getContent();
			});

		});
	});

	$('.js-sub').on('click',function(){
		var data = {
			"one":window.ONE_DETAIL,
			"red":window.RED_DETAIL,
			"lottery":window.LOTTERY_DETAIL
		};
		$.post('?c=activity&a=saveRule',data,function(re){
			if(re.code > 0){
				altDialog('保存成功')
			}else{
				altDialog('保存失败');
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
