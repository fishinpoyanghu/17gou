define(function(require, exports, module) {
	
	require('artdialog');
	 
	var multiCheck = $('input[name="multi_delete"]');

	//商品分类搜索
	$('#searchBtn').on('click',function(){
		var key = $('input[name=keyword]').val();
		if(key){
			location.href = '?c=goods&a=classify&keyword='+key;
		}
	});

	//商品搜索
	$('#search').on('click',function(){
		 search();
		 
	});
	function search(){
		var key = $.trim($('input[name=keyword]').val());
		var cate = $('select[name=cate]').val();
		var type = $('select[name=type]').val();   
	    location.href = '?c=user&a=userdata&keyword='+key
	    //+'&type='+type+'&cate='+cate;
	}
	$('[name=type],[name=cate]').on('change',function(){
		search();
	})
	 
  

     
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
