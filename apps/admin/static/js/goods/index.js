define(function(require, exports, module) {
	
	require('artdialog');
	var Piclib = require('piclib');
	var body = $('body');


	require.async(window.STATIC_LIST+'/js/libs/ueditor/ueditor.config.js', function () {
		require.async(window.STATIC_LIST+'/js/libs/ueditor/ueditor.all.min.js', function () {
			var editor = UE.getEditor('detail', {
				sourceEditor: "codemirror",
				wordImagePath: '',
				wordCount: false,
				lang: 'zh-cn',
				elementPathEnabled: false,
				initialContent: '',
				initialFrameWidth: '50%',
				initialFrameHeight: 200,
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
				window.GOODS_DETAIL = editor.getContent();
			});
		});
	});


	//展示图片上传
	$('.js-upload-one').on('click',function(){
		var one_pic = $(".one-pic");
		var piclib = new Piclib({
			userPic: false,  // 没有我的图片
			iconLib: false,
			size:'330*240',
			tipsnew:'上传的图片小于1MB，尺寸为330*240，格式为jpg，png。'
		}, function(url) {
			var box = $('<div class="picbox item"><div class="dm-thumbpic"><input type="hidden" name="cover" value="'+url+'"><img class="js-img" src="'+url+'" height="60" width="60" alt=""></div><a class="delete one-pic-delete"><i class="g-icon-close g-icon"></i></a></div>');
			one_pic.find('.item').hide();
			one_pic.prepend(box);
		});
	});


	//标题图片上传
	$('.js-upload').on('click',function(){
		var piclib = new Piclib({
			userPic: false,  //没有我的图片
			iconLib: false,
			size:'750*750',
			tipsnew:'上传的图片小于1MB，尺寸为750*750，格式为jpg，png。'
		}, function(url) {
			var box = $('<div class="picbox item"><div class="dm-thumbpic"><img class="title_img" src="'+url+'" height="60" width="60" attr="'+url+'"></div><a class="delete"><i class="g-icon-close g-icon"></i></a></div>');
			$('.more-pic').prepend(box);
		});
	});

	//删除图片
	body.on('click','.delete',function(){
		$(this).parent('.picbox').remove();
	});

	//显示上传框
	body.on('click','.one-pic-delete',function(){
		$('.add-pic-button').show();
	});

	//选择限购专区隐藏分类
	/*$('select[name=activity_type]').on('change',function(){
		var val = $(this).val();
		if(val==3){
			$('.js-type').addClass('hide');
		}else{
			$('.js-type').removeClass('hide');
		}
	});*/

	//提交
	$('.js-sub').on('click',function(e){
		e.preventDefault();
		var id = $('input[name=id]').val();
		var title_img = [];
		$('.title_img').each(function(){
			title_img.push($(this).attr('src'));
		});
		var img = title_img.join(',');

		var data = {
			"goods_type_id":$('select[name=type]').val(),
			"activity_type":$('select[name=activity_type]').val(),
			"is_in_activity":$('select[name=is_in_activity]').val(),
			"is_auto_false":$('select[name=is_auto_false]').val(),
            "need_num":$('input[name=need_num]').val(),
            "value":$('input[name=value]').val(),
			"title":$('input[name=title]').val(),
			"sub_title":$('textarea[name=sub_title]').val(),
			"description":$('textarea[name=description]').val(),
			"main_img":$('.js-img').attr('src'),
			"title_img":img,
			"rate_percent":$('input[name=rate_percent]').val(),  
			"weight":$('input[name=weight]').val(),
			"detail":window.GOODS_DETAIL
		};
		

		$.post("?c=goods&a=saveGoods",{"data":data,"id":id},function(re){
			if(re.code > 0){
				altDialog(re.msg,function(){
					location.href = document.referrer;
				});
			}else{
				altDialog(re.msg);
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
