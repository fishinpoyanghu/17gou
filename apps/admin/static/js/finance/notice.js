define(function(require, exports, module) {
	require('artdialog');
    $('.ziding').click(function(){
        var id = $(this).attr('data-id');
        $.post('?c=finance&a=noticeZiding',{"id":id},function(re){
            if(re.code > 0){
                altDialog(re.msg,function(){
                    location.href = "?c=finance&a=notice";
                });
            }else{
                altDialog(re.msg);
            }
        },'json');
    });

    $('.quxiaoziding').click(function(){
        var id = $(this).attr('data-id');
        $.post('?c=finance&a=noticeQuxiaoziding',{"id":id},function(re){
            if(re.code > 0){
                altDialog(re.msg,function(){
                    location.href = "?c=finance&a=notice";
                });
            }else{
                altDialog(re.msg);
            }
        },'json');
    });

    $('.shanchu').click(function(){
        var id = $(this).attr('data-id');
        $.post('?c=finance&a=noticeShanchu',{"id":id},function(re){
            if(re.code > 0){
                altDialog(re.msg,function(){
                    location.href = "?c=finance&a=notice";
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
