define(["avalon", "text!components/view-login-box/login_box.html",'http/http-factory'], function(avalon, template,httpFactory) {
 

   var widget = avalon.ui["login_box"] = function(element, data, vmodels) {

        var options = data.login_boxOptions
       
       options.template = options.getTemplate(template, options);

        var vmodel = avalon.define(data.login_boxId, function(vm) {
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
               if (typeof options.onInit === "function" ){
                   options.onInit.call(element, vmodel, options, vmodels)
               }
            }
            vm.$remove = function() {  
                element.innerHTML = element.textContent = ""  
            }

            vm.show_login_box = function(callback) {
              if(typeof callback == 'function') {
                vm.callback = callback;
              } else {
                vm.callback = function() {
                  //先将购物车中的数据缓存起来，刷新后赋值回去
                  avalon.cookie.set('cartInfo',JSON.stringify(vm.cartInfo.$model))
                  location.reload();
                }
              }
              vm.showWechatBlock = false;
              vm.showBox = true;
              init_login_box();
            }

            vm.hide_login_box = function() {
              vm.showBox = false;
              vm.resultTimeout && clearTimeout(vm.resultTimeout);
            }

            vm.loginSubmit = function() {
              var phone = vm.phone,
                  password = vm.password;
              if(phone == '') {
                vm.error_tips = '手机号不能为空';
                return;
              }
              if(!/^(13|18|15|14|17)\d{9}$/i.test(phone)) {
                vm.error_tips = '手机号格式不正确';
                return;
              }
              if(password == '') {
                vm.error_tips = '密码不能为空';
                return;
              }
              vm.error_tips = '';
              login(phone,password)
            }
            vm.hideWechatLogin = function() {
              vm.showWechatBlock = false;
              vm.resultTimeout && clearTimeout(vm.resultTimeout);
            }
            vm.wechatLogin = function() {
              vm.showWechatBlock = true;
              if(vm.isinGetCode) return;
              vm.isinGetCode = true;
              httpFactory.noParams('getLoginCode',function(re) {
                  re = JSON.parse(re);
                  if (re.code == 0) {
                    var data = re.data;
                     vm.codeUrl = 'img/loading.gif';
                     var img = new Image();
                     img.src = data.img;
                     img.onload = function() {
                         vm.codeUrl = data.img;
                         vm.sign = data.sign;
                         vm.loginId = data.id;
                         vm.resultTimeout = setTimeout(getWechatLoginResult,1000)
                         avalon.scan()
                     }
                  } else {
                    
                  }

              }, function(err) {
               
              },function() {
                vm.isinGetCode = false;
              });
            }

           
       })
      
       return vmodel
   }


   widget.defaults = {
        password:'',
        phone:'',
        error_tips:'',
        isLoginFinshed:true,
        showBox:false,
        isinGetCode:false,
        codeUrl:'img/loading.gif',
        showWechatBlock:false,
        resultTimeout:'',
        loginId:'',
        sign:'',
        isgetWechatLoginResult:false,
        cartInfo:[],
        callback:function(){
          location.reload();
        },
        getTemplate: function(str, options) {
           return str;
        }

   }

   function getWechatLoginResult() {
      var vm = avalon.vmodels["login_boxEidget"];
      if(vm.isgetWechatLoginResult) return;
      vm.isgetWechatLoginResult = true;
      httpFactory.getWechatLoginResult(vm.sign,vm.loginId,function(re) {
          re = JSON.parse(re);
          if (re.code == 0) {
                layer.msg('登录成功');
                httpFactory.setSessid(re.sessid,re.data.nick);
                avalon.vmodels["headerEidget"].isLogin = true;
                vm.callback() 
                vm.hide_login_box();
          } else {
              vm.resultTimeout = setTimeout(getWechatLoginResult,1000)
          }

      }, function(err) {
          layer.msg(JSON.parse(err).msg);
      },function() {
          vm.isgetWechatLoginResult = false;
      });
   } 

   function init_login_box() {
      var a = $(window).width(),
      c = $(window).height();
      var bg = $(".js-login-mask");
      var login_box = $(".js-login-box");
      login_box.hide();
      bg.hide();
      bg.css({ height: c + "px" });
      login_box.css({ left: (a - login_box.outerWidth()) / 2 + "px", top: (c - login_box.outerHeight()) / 2 + "px" });
      // $(".a_register_fixed_box").css({ left: (a - $(".a_register_fixed_box").outerWidth()) / 2 + "px", top: (c - $(".a_register_fixed_box").outerHeight()) / 2 + "px" });
      login_box.show();
      bg.show();
   }


    $(window).resize(function() {
          if($(".js-login-box").css('display') == 'block') {
            init_login_box();
          }
    });
   
    function login(phone,password) {
      var vm = avalon.vmodels["login_boxEidget"];
      if(!vm.isLoginFinshed) return;
      vm.isLoginFinshed = false;
      httpFactory.login(phone,password,function(re) {
          re = JSON.parse(re);
          if (re.code == 0) {
              httpFactory.setSessid(re.sessid,re.data.nick);
              avalon.vmodels["headerEidget"].isLogin = true;
              
             vm.callback();
             vm.hide_login_box();
             
          } else {
             vm.error_tips = re.msg;
          }

      }, function(err) {
        vm.error_tips = err.msg;
      },function() {
        vm.isLoginFinshed = true;
      });
    }

   return avalon
  
    
      

})
