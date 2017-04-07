//by a-yi
//上传文件，让任意元素可以弹出上传文件选择框 并完成上传，目前暂不支持跨域上传

(function ($) {
    $.fn.extend({
        uploader:function (options) {
            var settings = {
                upload_server:'',
                upload_name:'filename',
                offset:{left:0, top:0},
                timer:0,
                on_uploading:function () {
                    return true;
                },
                on_uploaded:function (data) {

                }
            };

            var self = $(this);
            var t = this;

            if (options) $.extend(settings, options);
            var is_uploading = false;

            if( self.data('form_id')){
                $('#' + self.data('form_id')).attr('action', settings.upload_server);
                return;
            }

            var _form_id = 'upfile' + (new Date()).getTime();
            var _upload_deal = 'upload_deal' + (new Date()).getTime();
            var _form = '<form id="' + _form_id + '" method="post" enctype="multipart/form-data" target="' + _upload_deal + '" action="">';
            _form += '<div class="file_input_a" style="overflow:hidden;position:absolute;z-index:80000;cursor:pointer;"><input type="file" name="' + settings.upload_name + '" style="cursor:pointer;filter:alpha(opacity=0); -moz-opacity:0; -khtml-opacity: 0; opacity: 0; position:absolute; right:0" /></div>';
            _form += '<iframe name="' + _upload_deal + '" style="display:none"></iframe>';
            _form += '</form>';
            self.data('form_id', _form_id);
            $('body').append(_form);

            var file_input = $("#" + _form_id + ' input[type="file"]');

            $('iframe[name="' + _upload_deal + '"]').load(function () {
                if (!is_uploading) return;
                var output = $(this).contents().find("body").text();
                is_uploading = false;
                settings.on_uploaded.call(t, output);

                var c = file_input.clone(true);
                file_input.after(c);
                file_input.remove();
            });

            set_offset();
            //定时判断绑定对象的offset是否发生变化，发生变化则进行重设
            settings.timer = setInterval(function () {
                if (settings.offset.left != self.offset().left || settings.offset.top != self.offset().top) {
                    set_offset();
                }
            }, 200);



            file_input.change(function () {
                if ($(this).val() != '') {
                    var re = settings.on_uploading();
                    if(re === false){
                        return false;
                    }
                    if(settings.upload_server == ''){
                        return false;
                    }
                    is_uploading = true;
                    $('#' + _form_id).attr('action', settings.upload_server).submit();
                }
            });


            function set_offset() {
                settings.offset = {
                    left:self.offset().left,
                    top:self.offset().top
                };
                $("#" + _form_id + ' .file_input_a').css({
                    'left':settings.offset.left,
                    'top':settings.offset.top,
                    'width':self.outerWidth(),
                    'height':self.outerHeight()
                });
            }
        }
    });
})(jQuery);

