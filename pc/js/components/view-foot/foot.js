define(["avalon", "text!components/view-foot/foot.html",'http/http-factory'], function(avalon, template,httpFactory) {


    var widget = avalon.ui["foot"] = function(element, data, vmodels) {

        var options = data.footOptions

        options.template = options.getTemplate(template, options);

        var vmodel = avalon.define(data.footId, function(vm) {

            avalon.mix(vm, options);
            vm.$skipArray = ["template", "widgetElement"]
            vm.widgetElement = element
            var inited
            vm.$init = function() {
                if (inited) return
                inited = true;
                element.style.display = "none";
                var pageHTML = options.template;
                element.innerHTML = pageHTML;
                element.style.display = "block";
                avalon.scan(element, [vmodel].concat(vmodels))
            }
            vm.$remove = function() {
                element.innerHTML = element.textContent = ""
            }
			vm.complain_yu = function(){
				var reportMsg = {},
					me = this,
					parent = $(me).parent();
				reportMsg.reportName = parent.children().filter('#uname').val();
				reportMsg.reportContact = parent.children().filter('#contact_info').val();
				reportMsg.reportContent = parent.children().filter('textarea').val();
				if (!reportMsg.reportContent) {
					alert('请输入建议内容')
				}else{
					complain(reportMsg)
				}
			}
            vm.complain = function() {
                if(httpFactory.isLogin()) {
                    layer.prompt({
                        formType: 2,
                        value: '',
                        title: '请输入意见'
                    }, function(value, index, elem){
                        complain(value)
                    });
                } else {
                    layer.msg('投诉需要登录')
                    avalon.router.go('login');
                }
            }


        })

        function complain(yijian) {
        	console.log(yijian)
            httpFactory.complain(yijian,function(re) {
                re = JSON.parse(re);
                alert()
                if (re.code == 0) {
                    layer.msg('投诉成功')
                } else {
                    layer.msg(re.msg)
                }
            },function(re) {
                layer.msg(JSON.parse(re).msg)
            })
        }


        return vmodel
    }


    widget.defaults = {
        hideFooterIn:false,
        getTemplate: function(str, options) {
            return str;
        }
    }

    return avalon

})
