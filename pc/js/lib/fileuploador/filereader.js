/**
 * 无上传预览图片
 * @example : 
 *  <input type="file" id="myfile" name="myfile" />
    <div id="img">

    </div>
 * $('#myfile').filereader({
        type:['jpg','jpeg','png'],//允许上传的图片类型
        max_size:600,//允许上传的图片大小 单位：KB
        preview:$('#img'),//图片预览容器
        error:function(){//错误时回调函数
            alert(err_msg)//系统错误信息
        },
        success:function(){ //正确预览后回调函数
            alert('已经ok啦')
        }, 
        preview_attr:{id:'ttt', height:'200px', width:'160px'}//预览图片的属性
    });
 * 
 * @author 王长宏 2015/11/20
 */
;

(function($){
    $.fn.extend({
        filereader:function(p){
            var dom = this, err_msg='';
            var _p = {
                type: ['jpg','jpeg','png'], //允许图片类型
                max_size: 999999999, //允许图片最大 KB
                preview: '', //预览图容器
                preview_attr : {},//预览图属性
                error:function(){alert(err_msg)}, 
                success:function(){alert('is_ok')}
            };
            var setting = $.extend(_p, p);
            
            var checkSize = function( size ){
                size = size / 1024;
                return setting.max_size >= size;
            }
            
            var checkType = function( type ){
                var t = type.split('/')[1];
                var ret = false;
                for(var i=0; i< setting.type.length; i++){
                    if(setting.type[i] == t){
                        ret = true;
                        break;
                    }
                }
                
                return ret;
            }
            
            var loadImg = function(){
                var file = dom.get(0).files[0];
                if( !checkType(file.type) ){
                    err_msg = '图片格式不对';
                    setting.error();
                    return;
                }
                
                if( !checkSize(file.size) ){
                    err_msg = '图片不能大于 ' + setting.max_size + 'KB ';
                    setting.error();
                    return;
                }
                
                var reader = new FileReader();  
                //将文件以Data URL形式读入页面  
                reader.readAsDataURL(file);  
                reader.onload=function(e){
                    //显示文件  
                    setting.preview.empty().append( 
                            $('<img>')
                            .attr('src', this.result)
                            .attr(setting.preview_attr)
                            );
                    setting.success();
                }
            }
            
            dom.change(loadImg);
        }
    })
})(jQuery);