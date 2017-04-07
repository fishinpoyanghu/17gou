(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['jquery'], factory);
	}
	else if (typeof define === 'function' && define.cmd) {
		// SeaJS
		define(function(require, exports, module) {
			module.exports = factory(require('jquery'));
		});
	}
	else if (typeof module === 'object' && module.exports) {
		// Node/CommonJS
		module.exports = factory(require('jquery'));
	}
	else {
		// Browser globals
		factory(jQuery);
	}
}(function ($) {
	var util = {};
	
	util.log = function(msg) {
		if (console || window['console']) {
			console.log(msg);
		}
	}
	
	/**
	 * 得到当前时间戳
	 */
	util.timestamp = function() {
		return (Date.parse(new Date())) / 1000;
	}
	
	/**
	 * 登录
	 * @param string name,
	 * @param string pwd,
	 * @param string code, 验证码，没有就传空字符串
	 */
	util.login = function($name, $pwd, $code) {
//		alert($dp_global_config.js_domain);
//		alert($dp_global_config.css_domain);
//		alert($dp_global_config.img_domain);
//		alert($dp_global_config.upload_domain);
				

		var $data = {name:"13761188500", password:"days123"};
		
		$.ajax({
			type: "POST",
			url: $login_url,
			dataType: 'jsonp',
			data: $data,
			success: function(r) {
				if (r.code == 0) {
					
				}
				alert(r.msg);
			}
		});
	}
	
	util.logout = function() {
		
	}
	
    /**
     * days 分页
     */
    util.pageHtml2 = function ($dom, $total, $current, $pagesize) {
        $total_page = Math.ceil(parseInt($total) / $pagesize);
        $half_per = 6;
        $current = parseInt($current);
        
        $re = "<li"+(($current>1)?"":" class=\"disabled\"")+"><a href=\"javascript:;\" data-page=\"1\"><i class=\" icon-double-angle-left\"></i></a></li>\n<li"+(($current > 1)?"":" class=\"disabled\"")+"><a href=\"javascript:;\" data-page=\""+($current-1)+"\"><i class=\"icon-angle-left\"></i></a></li>\n"; 
        
        $i = $current - $half_per;
        $j = $current + $half_per
        if ($i < 1) $i = 1;
        if ($j > $total_page) $j = $total_page;
        for (;$i <= $j ;$i++) {
            
            $re += "<li"+(($i == $current)?" class=\"disabled\"":"")+"><a href=\"javascript:;\" data-page=\""+$i+"\">"+$i+"</a></li>\n";
        }
        
        $re += "<li"+(($current >= $total_page)?" class=\"disabled\"":"")+"><a href=\"javascript:;\" data-page=\""+$total_page+"\"><i class=\"icon-angle-right\"></i></a></li>\n<li"+(($current >= $total_page)?" class=\"disabled\"":"")+"><a href=\"javascript:;\" data-page=\""+($current+1)+"\"><i class=\" icon-double-angle-right\"></i></a></li>\n";
        
        if($total_page>1) {
            $re="<ul class=\"pagination pagination-sm\">"+$re+"</ul>";
        }
        else {
            $re = "";
        }
        
        $re = "<nav class=\"dp-page-right\">"+$re+"</nav><div class=\"page-cnt\">共"+$total+"条，每页"+$pagesize+"条</div>";
        
        $dom.html($re);
    }
  /*
  * 分页
  * */
  util.pageHtml = function(dom,on_class,current, count, size, max, dom_class) {
    var type = arguments[7]?arguments[7]:'';
//    dom.attr('class','');
    var last = Math.ceil(parseInt(count) / size);
    current = parseInt(current);

    count = Math.ceil(count / size);
    if (count < 2) {
      dom.html('');
      return;
    }
    dom.html('<a href="javascript:;" class="first" data-page="1">首页</a>');
    if (current != 1) {
      dom.append('<a href="javascript:;"  class="prev" data-page="' + (current - 1) + '"><</a>');
    }
    for (var i = 1; i <= count; i++) {
      dom.append(i == current ? '<a href="javascript:;"  class="each '+on_class+'" data-page="'+i+'">' + i + '</a>' : '<a href="javascript:;" class="each"  data-page="'+i+'">' + i + '</a>');
    }
    if (current != count) dom.append('<a class="next" href="javascript:;"  data-page="'+(current+1)+'">></a>');
    dom.append('<a href="javascript:;"  class="last" data-page="'+last+'">末页</a>');
    if (last > max) {
      var max_ceil = Math.ceil(max / 2), max_floor = Math.floor(max / 2);
      if (current <= max_ceil) {
        dom.find('a:lt(' + (last + (current == 1 ? 0 : 1)) + '):gt(' + (max - (current == 1 ? 1 : 0)) + ')').hide();
        dom.find('a:eq(' + (last) + ')').before('<a class="ellipsis" href="javascript:;" data-page="' + (Math.ceil((last - max) / 2) + max) + '">...</a>');
      } else if (current > last - max_ceil) {
        dom.find('a:gt(1):lt(' + (last - max) + ')').hide();
        dom.find('a:eq(1)').after('<a class="ellipsis" href="javascript:;" data-page="' + (Math.ceil((last - max) / 2)) + '">...</a>');
      } else {
        dom.find('a:gt(1):lt(' + (current - max_ceil) + ')').hide();
        dom.find('a:lt(' + (last + 1) + '):gt(' + (current + max_floor) + ')').hide();
        dom.find('a:eq(1)').after('<a class="ellipsis" href="javascript:;" data-page="' + (Math.ceil((current - max_ceil) / 2)) + '">...</a>');
        var n_c_last = current + max_floor;
        dom.find('a:last').prev().before('<a class="ellipsis" href="javascript:;" data-page="' + (n_c_last + Math.ceil((last - n_c_last) / 2)) + '">...</a>');
      }
    }
//    dom.addClass(dom_class);
    if(type) dom.attr('data-type',type);
  }

  return util;
}));
