//by a-yi
//上传预览

(function ($) {
    $.fn.extend({
        picpreview:function (options) {
            var settings = {
                width: $(this).width(),
                url:''
            };
            if(options) $.extend(settings, options);
            var url;
            $(this).hover(function (e) {
                    var thisVal = $(this).val() || $(this).attr('href');
                    thisVal = !thisVal ? $(this).attr('data-url') : thisVal;
                    if (!thisVal) {
                        if($("#picPreview").length){
                            $("#picPreview").remove();
                        }
                        return;
                    }

                    if($(this).attr('data-width')){
                        settings.width = $(this).attr('data-width');
                    }

                    if (thisVal.indexOf('http://') != -1) {
                        url = thisVal;
                    } else {
                        url = settings.url + thisVal;
                    }
                    if(url.indexOf('.jpg') != -1 ||  url.indexOf('.png') != -1 || url.indexOf('.gif') != -1){
                        url = url + '?t=' + (new Date().getTime());
                        $("body").append("<p id='picPreview' style='position:absolute;border:1px solid #ccc;background:#333;padding:5px;display:none;color:#fff;z-index:10000;width: " + (settings.width + 12) + "px'><img src='" + url + "' width='" + settings.width + "' /></p>");
                    }else if(url.indexOf('.swf') != -1){
                        url = url + '?t=' + (new Date().getTime());
                        $("body").append('<p id="picPreview" style="position:absolute;border:1px solid #ccc;background:#333;padding:5px;display:none;color:#fff;z-index:10000; width: ' + (settings.width + 12) + 'px"><object width="100%" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"><param value="' + url +'" name="movie"><param value="high" name="quality"><param value="transparent" name="wmode"><embed width="100%" wmode="transparent" quality="high" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" src="' + url + '"></object></p>');
                    }else{
                        return;
                    }
                    $("#picPreview").css("top", ($(this).offset().top + $(this).outerHeight() - 1) + "px");

                    if($(this).offset().left + $("#picPreview").width() > $(window).width()){
                        $("#picPreview").css("left", ($(this).offset().left - $("#picPreview").width() + $(this).outerWidth()) + "px");
                    }else{
                        $("#picPreview").css("left", $(this).offset().left + "px");
                    }

                    $("#picPreview").fadeIn("fast");
                },
                function () {
                    var thisVal = $(this).val() || $(this).attr('href');
                    if (thisVal == '') {
                        return;
                    }
                    if($("#picPreview").length){
                        $("#picPreview").remove();
                    }
                });
        }
    });
})(jQuery);