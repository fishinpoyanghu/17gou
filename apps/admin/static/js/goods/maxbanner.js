define(function(require, exports, module) {
	require('libs/select2');
	require('artdialog');
	var Piclib = require('piclib');

    $('.js-upload-one').on('click',function(){
        var one_pic = $(".one-pic");
        var piclib = new Piclib({
            userPic: false,  // 没有我的图片
            iconLib: false,
            size:'1910*177',
            tipsnew:'上传的图片小于1MB，尺寸为1910*177，格式为jpg，png。'
        }, function(url) {
            var box = $('<div class="picbox item"><div class="dm-thumbpic"><input type="hidden" name="cover" value="'+url+'"><img class="js-img" src="'+url+'" width="1024" height="120" alt=""></div><a class="delete one-pic-delete"><i class="g-icon-close g-icon"></i></a></div>');
            one_pic.find('.item').hide();
            one_pic.prepend(box);
        });
    });

    $('.js-sub').on('click',function(e){
        e.preventDefault();
        var img = $('input[name=cover]').val();

        $.post("?c=goods&a=saveMax",{"img":img},function(re){
            if(re.code > 0){
                altDialog(re.msg,function(){
                    location.href = "?c=goods&a=maxbanner";
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
