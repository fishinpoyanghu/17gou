define(["avalon", "text!components/view-header/header.html",'http/http-factory','css!../../lib/layer/skin/layer.css'], function(avalon, template,httpFactory) {

	var widget = avalon.ui["header"] = function(element, data, vmodels) {

       var options = data.headerOptions;

       var activePage = options.activePage;
       
       options.template = options.getTemplate(template, options);
       
       $('div').delegate('.yMenuIndex li','click onload',function(){
       		var me = this,
       		parent = $(me).parent(),
       		idx = parent.children().index(me),
//     		spanLeft = $('.yMenuIndex li:first-child').offset().right,
       		spanLeft = 0,
       		activeleft =spanLeft + (idx)*132;
       		if (!idx) {
       			$('.yMenuIndex li span').css('left',activeleft + 20 + 'px');
       		}else{
	       		$('.yMenuIndex li span').css('left',activeleft + 'px');
       		}
       		$('.yMenuIndex li span').css('height','40px');
       })
       $(document).ready(function(){
       		var url = window.location.href,
       		rute = url.split('#!/')[1];
       		console.log(rute)
       		switch (rute){
       			case 'twoPersons': 
       					$('.yMenuIndex li span').css('left',132 + 'px');
       				break;
       			case 'tenYuan': 
       					$('.yMenuIndex li span').css('left',132*2 + 'px');
       				break;
       			case 'redpacket': 
       					$('.yMenuIndex li span').css('left',132*3 + 'px');
       				break;
       			case 'announced': 
       					$('.yMenuIndex li span').css('left',132*4 + 'px');
       				break;
       			case 'share': 
       					$('.yMenuIndex li span').css('left',132*5 + 'px');
       				break;
       			case 'help/question': 
       					$('.yMenuIndex li span').css('left',132*6 + 'px');
       				break;
       			default:
       					$('.yMenuIndex li span').css('left',20 + 'px');
       				break;
       		}
       		console.log(url.split('#!/')[1])
       		var parent = $('.yMenua').parent();
       })
       $('div').delegate('.pullDown .pullDownTitle','click',function(){
       		$('.yMenuIndex li span').css('height','0px');
       })
       var vmodel = avalon.define(data.headerId, function(vm) {
            avalon.mix(vm, options);
            vm.$skipArray = ["template", "widgetElement"]
            vm.widgetElement = element
            vm.showTop = options.showTop;
           vm.hasSign = false;
           vm.signFinish =  true;
            var inited 
            vm.$init = function() {
                if (inited) return
                getClassify();  
                inited = true;
                element.style.display = "none";
                var pageHTML = options.template;    
                element.innerHTML = pageHTML;  
                element.style.display = "block"; 
                avalon.scan(element, [vmodel].concat(vmodels))
               if (typeof options.onInit === "function" ){
                   options.onInit.call(element, vmodel, options, vmodels)
               }
            }
            vm.$remove = function() {  
                element.innerHTML = element.textContent = ""  
            }

            vm.goLogin = function() {
                avalon.router.go('login');
            }
            vm.loginOut = function() {
              httpFactory.removeSessid();
              vm.isLogin = false;
              avalon.router.go('index');
            }

            vm.jumpPage = function(toName) {
              if(httpFactory.isLogin()) {
                avalon.router.go(toName);
              } else {
                  avalon.router.go('login');
              }
            }

           vm.sign = function() {
               if(httpFactory.isLogin()) {
                   if (!vm.signFinish) return;
                   if (vm.hasSign) {
                       layer.msg('你已经签到过了');
                       return;
                   }
                   vm.signFinish = false;
                   httpFactory.noParams('sign',function(re) {
                       re = JSON.parse(re);
                       if (re.code == 0) {
                           layer.msg('签到成功');
                           vm.hasSign = true;
                       } else {
                           layer.msg(re.msg);
                       }
                   }, function(err) {
                      layer.msg(JSON.parse(err).msg);
                   }, function() {
                       vm.signFinish = true;
                   })
               } else {
                   avalon.router.go('login');
               }
           }

            vm.scrollTop = function() {
               $(document.documentElement).animate({ scrollTop: 0 }, 200);
               $(document.body).animate({ scrollTop: 0 }, 200);
            }

            vm.searchStart = function() {
              if(vm.searchKey.trim() == '') {
                layer.msg('请输入搜索关键词');
                return;
              }
              if(avalon.router.getLastPath() == 'search') {
                vm.searchGoods(vm.searchKey);
              } else {
                avalon.router.go('search');
              }
            }
            vm.keyupSearch = function(e) {
              if(e.keyCode == 13) {
                vm.searchStart()
              }
            }

           vm.AddFavorite = function(title,url) {
               try {
                   window.external.addFavorite(url, title);
               }
               catch (e) {
                   try {
                       window.sidebar.addPanel(title, url, "");
                   }
                   catch (e) {
                       alert("抱歉，您所使用的浏览器无法完成此操作。\n\n加入收藏失败，请使用Ctrl+D进行添加");
                   }
               }
           }

          vm.complain = function() {
              if(vm.isLogin) {
                layer.open({
				  type: 1,
				  title: false,
				  closeBtn: 2,
				  shadeClose: true,
				  title:'<h3 style="height:42px;line-height:42px">感谢您对我们平台的建议</h3>',
				  area:['500px', '400px'],
				  skin: 'yourclass',
				  btn: ['提交', '取消'],
				  yes: function(index, layero){
				  			var userReprot = {},total=0;
				  			userReprot.uname = $('.layui-layer-content #userReport #uname').val();
				  			userReprot.contactInfo = $('.layui-layer-content #userReport #contact_info').val();
				  			userReprot.content = $('.layui-layer-content #userReport #suggessContent').val();
				  			userReprot.uname.trim() && total++;
				  			userReprot.contactInfo.trim() && total++;
				  			userReprot.content.trim() && total++;
				  			if (total == 3) {
				  				complain(JSON.stringify(userReprot));
				  			}else{
				  				layer.msg('昵称或联系方式或建议内容没填，请填写完整后再提交');
				  			}
				    		//按钮【按钮一】的回调
				  		},btn2: function(index, layero){
				    		//按钮【按钮二】的回调
				  		},
				  content: '<dl id="userReport"><div class="dlLast">'
				  			+'<span>姓&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名:</span>&nbsp;<input type="text" id="uname" value="" placeholder="您的姓名" /><br>'
				  			+'<span>联系方式:</span>&nbsp;<input type="text" id="contact_info" value="" placeholder="您的联系方式" /><br>'
				  			+'<span>建&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;议:</span>&nbsp;<textarea id="suggessContent" placeholder="建议"></textarea>'
				  			+'<p class="ps">如果您想要的产品我们平台没有，可以在此处提出建议（可以贴商品的地址），我们将会及时反馈</p>'
                			+'</div></dl>'
				});
              } else {
                layer.msg('投诉需要登录')
                  avalon.router.go('login');
              }
          }
//        vm.complain = function() {
//            if(vm.isLogin) {
//              layer.prompt({
//                formType: 2,
//                value: '',
//                title: '请输入意见'
//              }, function(value, index, elem){
//                  complain(value)
//              });
//            } else {
//              layer.msg('投诉需要登录')
//                avalon.router.go('login');
//            }
//        }

          vm.goToClassify = function(goods_type_id) {
            avalon.router.go('allClassify',{goods_type_id:goods_type_id})
          }

           
       })

      function complain(yijian) {
          httpFactory.complain(yijian,function(re) {
              re = JSON.parse(re);
              if (re.code == 0) {
                  layer.msg('投诉成功')
                  $('.layui-layer-content #userReport #suggessContent').val('');
              } else {
                  layer.msg(re.msg)
              }
          },function(re) {
              layer.msg(JSON.parse(re).msg)
          })
      }

       vmodel.$watch("isLogin", function(val) {
         vmodel.nick = decodeURIComponent(avalon.cookie.get('nick')) || ''
       })

       
       return vmodel
   }


   widget.defaults = {
       activePage:'index',
       showTop:true,
       isLogin: httpFactory.getSessid() ? true : false,
       searchKey:'',
       eachNavHeight:0,
       nick:decodeURIComponent(avalon.cookie.get('nick')) || '',
       classifyList:{

       },
       getTemplate: function(str, options) {
           return str;
       }

   }
       //侧面导航 end
    avalon(window).bind("scroll", function(e) { 
        if($(document).scrollTop() >= 158) {
          avalon.vmodels["headerEidget"].showTop = false;
        } else {
          avalon.vmodels["headerEidget"].showTop = true;
        }
    })
    function caculateNavHeight(len) {
      avalon.vmodels["headerEidget"].eachNavHeight = 396.45/len - 1;
    }

    function getClassify() {
      avalon.cookie.set("classifyList","unfinshed");
      httpFactory.noParams('getCategoryList',function(re) {
          re = JSON.parse(re);
          if (re.code == 0) {
             avalon.vmodels["headerEidget"].classifyList = re.data;
             var data = re.data;
             var len = 0;
             var classifyList = [];
             for(var i in data) {
                // if(data[i].sub) {
                //   for(var j = 0,len = data[i].sub.length;j < len;j++) {
                //       classifyList.push(data[i].sub[j]);
                //   }
                // }
                classifyList.push(data[i]);
                len++;
             }
             avalon.cookie.set("classifyList",JSON.stringify({list:classifyList}));
             if(len > 0) {
                caculateNavHeight(len)
             }
          } else {
            layer.msg(re.msg)
          }

      }, function(err) {
        layer.msg(JSON.parse(err).msg)
      });
    }

   return avalon
  
    
      

})
