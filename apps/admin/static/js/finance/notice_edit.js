define(function(require, exports, module) {
	require('artdialog');

    $('.js-sub').on('click',function(e){
        e.preventDefault();
        var id = $('input[name=id]').val();
        var data = {
            "title":$('input[name=title]').val(),
            "content":$('textarea[name=content]').val(),
            "name":$('input[name=name]').val()
        };

        $.post("?c=finance&a=noticeSave",{"data":data,"id":id},function(re){
            if(re.code > 0){
                altDialog(re.msg,function(){
                    location.href = "?c=finance&a=notice";
                });
            }else{
                altDialog(re.msg);
            }
        },'json');
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
