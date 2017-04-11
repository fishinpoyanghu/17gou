avalon.config({debug: false})
var hackHasInit = false;
require(['avalon', 
         'mmRouter', 
         'mmHistory', 
         'mmPromise', 
         'mmState', 
         'jquery',
         'bootstrap',
         // 'mmRequest',
         'utils/trolley_fly',
         'page/cartOrder/cartOrder',
         'components/view-header/header',
        'components/view-login-box/login_box',
        'components/view-foot/foot',
        'layer',
        'lazyload'
   


         ], 
function (avalon, mmRouter, mmHistory, mmPromise, mmState,$,bootstrap,cartFly,cartOrder){
  if(hackHasInit) return;      //hack该文件运行两次问题
  hackHasInit = true;
  var app = {
    start: function (){
      this.initRoute();
    },
    initRoute: function (){
      //利用cookie的有效期解决localStorage本地持久化存储的问题
      if(JSON.parse(avalon.cookie.get('cartInfo') || '[]').length == 0) avalon.Storage.remove('cartInfo')
      var root = avalon.define({
            $id: "root",
            p_cartInfo:avalon.Storage.get('cartInfo') || [],
            addCartItem:addCartItem,             //添加购物车方法
            removeCartItem:removeCartItem,       //从购物车中删除商品方法
            getCartInfo:getCartInfo,             //获取购物车信息方法
            getCartTotalNum:getCartTotalNum,     //获取购物车商品总个数
            clearCartItem:clearCartItem,         //清空购物车方法
            cartCheckAll:cartCheckAll,          //全选
            removeCartMutiItem:removeCartMutiItem,
            showLoading:true,

      })
      function getCartInfo(){
        return root.p_cartInfo;
      }


      // root.$watch("p_cartInfo", function(val) {      //bug！！！框架监听不到该属性变化
      //    avalon.cookie.set('cartInfo',JSON.stringify(root.p_cartInfo.$model))
      // })

      // function showMutiDel() {
      //   var goodsList = root.p_cartInfo;
      //   for(var i = 0,len = goodsList.length;i < len;i++) {
      //     if(goodsList[i].isSelected) return true;
      //   }
      //   return false;
      // }  

      function cartCheckAll($event) {
        var goodsList = root.p_cartInfo;
        var isChecked = true;
        if(!$event.target.checked) {
          isChecked = false;
        }
        for(var i = 0,len = goodsList.length;i < len;i++) {
          goodsList[i].isSelected = isChecked;
        }
      }

      function removeCartMutiItem(){
        var goodsList = root.p_cartInfo;
        for(var i = 0;i < goodsList.length;i++) {
          if(goodsList[i].isSelected) {
            root.p_cartInfo.removeAt(i)
            i--;
          }
        }
        avalon.cookie.set('cartInfo',JSON.stringify(root.p_cartInfo.$model),{setmaxage:-1})
        avalon.Storage.set('cartInfo',root.p_cartInfo.$model)
      }

      function getCartTotalNum(){
        var total = 0;
        var goodsList = root.p_cartInfo;
        for(var i = 0,len = goodsList.length;i < len;i++) {
          if(goodsList[i].isSelected) {
                total += Number(goodsList[i].join_number);
          }
        }
        return total;
      }

      function addCartItem($event,goodsItem,addNum,home) {  
        if(home=='home'){ //表示首页直接跳转到支付页面  
          var shopData=[{ //拼接json以符合数组参数
            activity_id:goodsItem.activity_id,
            goods_title:goodsItem.goods_title, 
            join_number:goodsItem.activity_type==2?goodsItem.join_number*10:goodsItem.join_number, 
            isSelected:true
          }]   
          cartOrder.cartobj(shopData);
          return false;
        } 
        if(addNum == 'false') {
          addItem(goodsItem)
          avalon.cookie.set('cartInfo',JSON.stringify(root.p_cartInfo.$model),{setmaxage:-1})
          avalon.Storage.set('cartInfo',root.p_cartInfo.$model)
        } else {
          var img = new Image();
          img.src = goodsItem.goods_img;
          var $img = $(img);
          $img.show();
          $img.addClass('eleFlyElement')
          $img.css({
            top: $event.pageY + 'px',
            left: $event.pageX  + 'px'
          });
          $(document.body).append($img);
          $img.show();
          var toEle = document.getElementById('cart_icon');
          
          var myParabola = cartFly(img,toEle, {
            speed: 500, //抛物线速度
            curvature: 0.0008, //控制抛物线弧度
            complete: function() {
              $img.hide();
              $img.remove()
              addItem(goodsItem,addNum)
              avalon.cookie.set('cartInfo',JSON.stringify(root.p_cartInfo.$model),{setmaxage:-1})
              avalon.Storage.set('cartInfo',root.p_cartInfo.$model)
            }
          })
          myParabola.position().move(); 
        }
         
      }

      function addItem(goodsItem,addNum) { 
          var goodsList = root.p_cartInfo;
          goodsItem.join_number = addNum || 1;
          goodsItem.join_number = Number(goodsItem.join_number);
          var goodsIndex = isGoodsExisted(goodsItem);
          if (goodsIndex != null) {
              var number = Number(goodsList[goodsIndex].join_number);
              if (goodsItem.activity_type == 2) { //十元
                  if (goodsItem.join_number * 10 + number <= goodsItem.remain_num) {
                      number = goodsItem.join_number * 10 + number;
                  }
              } else if (goodsItem.activity_type == 3) { //限购
                  if (goodsItem.join_number + number <= 10) {
                      number = goodsItem.join_number + number;
                  } else {
                      number = 10;
                  }
              } else {
                  if (goodsItem.join_number + number <= goodsItem.remain_num) {
                      number = goodsItem.join_number + number;
                  }
              }
              goodsList[goodsIndex].join_number = number;
          } else {
              if (goodsItem.activity_type == 2) {
                  goodsItem.join_number = goodsItem.join_number * 10;
              }
              goodsItem.isSelected = true;
              goodsList.push(goodsItem);
          }
          avalon.cookie.set('cartInfo',JSON.stringify(root.p_cartInfo.$model),{setmaxage:-1})
          avalon.Storage.set('cartInfo',root.p_cartInfo.$model)
      }
      function removeCartItem(index){
        root.p_cartInfo.removeAt(index)
        avalon.cookie.set('cartInfo',JSON.stringify(root.p_cartInfo.$model),{setmaxage:-1})
        avalon.Storage.set('cartInfo',root.p_cartInfo.$model)
      }


      function clearCartItem(){
        root.p_cartInfo.clear();
        avalon.cookie.set('cartInfo',JSON.stringify(root.p_cartInfo.$model),{setmaxage:-1})
        avalon.Storage.set('cartInfo',root.p_cartInfo.$model)
      }

      //判断是否已存在购物车中 存在则返回对应index
      function isGoodsExisted(goodsItem){
        var goodsList = root.p_cartInfo;
        for(var i = 0,len = goodsList.length;i < len;i++){
          if(goodsItem.activity_id==goodsList[i].activity_id){
            return i;
          }
        }
        return null;
      }  
      var headerEidget = avalon.define({
            $id: "headerCtrl",
            $opts: {
                activePage:'index',
                showTop:true
            }
      })
      var login_boxEidget = avalon.define({
            $id: "login_boxCtrl",
            $opts: {
               cartInfo:root.p_cartInfo
            }
      })

        var footEidget = avalon.define({
            $id: "footCtrl",
            $opts: {
            }
        })

      avalon
      .state("index", {
        url: "/",
        views: {
          "": {
            templateUrl: "html/indexPage.html", 
            controllerUrl: "page/indexPage/indexPage.js"
          }
        }
      })
      .state("bootstrap", {
        url: "/bootstrap/{inviteCode}",
        views: {
          "": {
            templateUrl: "html/reg.html", 
            controllerUrl: "page/reg/reg.js" 
          }
        }
      })
      .state("max_buy", {
        url: "/max_buy",
        views: {
          "": {
            templateUrl: "html/max_buy.html", 
            controllerUrl: "page/max_buy/max_buy.js" 
          }
        }
      })
      .state("tenYuan", {
        url: "/tenYuan",
        views: {
          "": {
            templateUrl: "html/tenYuan.html", 
            controllerUrl: "page/tenYuan/tenYuan.js" 
          }
        }
      })
      .state("announced", {
        url: "/announced",
        views: {
          "": {
            templateUrl: "html/announced.html", 
            controllerUrl: "page/announced/announced.js" 
          }
        }
      })
      .state("share", {
        url: "/share",
        views: {
          "": {
            templateUrl: "html/share.html", 
            controllerUrl: "page/share/share.js" 
          }
        }
      })
      .state("recordBuy", {
        url: "/recordBuy",
        views: {
          "": {
            templateUrl: "html/recordBuy.html", 
            controllerUrl: "page/recordBuy/recordBuy.js" 
          }
        }
      })
      .state("recordWin", {
        url: "/recordWin",
        views: {
          "": {
            templateUrl: "html/recordWin.html", 
            controllerUrl: "page/recordWin/recordWin.js" 
          }
        }
      })
      .state("expose", {
        url: "/expose",
        views: {
          "": {
            templateUrl: "html/expose.html", 
            controllerUrl: "page/expose/expose.js" 
          }
        }
      })
      .state("redpacket", {
        url: "/redpacket",
        views: {
          "": {
            templateUrl: "html/redpacket.html", 
            controllerUrl: "page/redpacket/redpacket.js" 
          }
        }
      })
      .state("invite", {
        url: "/invite",
        views: {
          "": {
            templateUrl: "html/invite.html", 
            controllerUrl: "page/invite/invite.js" 
          }
        }
      })
      .state("address", {
        url: "/address/{winId}",
        views: {
          "": {
            templateUrl: "html/address.html", 
            controllerUrl: "page/address/address.js" 
          }
        }
      })
      //商品详情
      .state("goods", {
        url: "/goods/{activity_id}",
        views: {
          "": {
            templateUrl: "html/goods.html", 
            controllerUrl: "page/goods/goods.js" 
          }
        }
      })
      .state("cartOrder", {
        url: "/cartOrder",
        views: {
          "": {
            templateUrl: "html/cartOrder.html", 
            controllerUrl: "page/cartOrder/cartOrder.js" 
          }
        }
      })
      .state("comment", {
        url: "/comment/{show_id}",
        views: {
          "": {
            templateUrl: "html/comment.html", 
            controllerUrl: "page/comment/comment.js" 
          }
        }
      })
      .state("reg", {
        url: "/reg",
        views: {
          "": {
            templateUrl: "html/reg.html", 
            controllerUrl: "page/reg/reg.js" 
          }
        }
      })
          .state("login", {
              url: "/login",
              views: {
                  "": {
                      templateUrl: "html/login.html",
                      controllerUrl: "page/reg/login.js"
                  }
              }
          })
          .state("question", {
              url: "/question",
              views: {
                  "": {
                      templateUrl: "html/question.html", 
                      controllerUrl: "page/help/question.js" 
                  }
              }
          })
          .state("rule", {
              url: "/rule",
              views: {
                  "": {
                      templateUrl: "html/rule.html", 
                      controllerUrl: "page/help/rule.js" 
                  }
              }
          })
          .state("redRule", {
              url: "/redRule",
              views: {
                  "": {
                      templateUrl: "html/redRule.html", 
                      controllerUrl: "page/help/redRule.js" 
                  }
              }
          })
          .state("lotteryRule", {
              url: "/lotteryRule",
              views: {
                  "": {
                      templateUrl: "html/lotteryRule.html", 
                      controllerUrl: "page/help/lotteryRule.js" 
                  }
              }
          })

      //支付页
      .state("payOrder", {
        url: "/payOrder/{order_num}/{buyNow}",
        views: {
          "": {
            templateUrl: "html/payOrder.html", 
            controllerUrl: "page/payOrder/payOrder.js" 
          }
        }
      })

      //支付结果
      .state("payResult", {
        url: "/payResult/{order_num}",
        views: {
          "": {
            templateUrl: "html/payResult.html", 
            controllerUrl: "page/payResult/payResult.js" 
          }
        }
      })
      .state("recharge", {
        url: "/recharge",
        views: {
          "": {
            templateUrl: "html/recharge.html", 
            controllerUrl: "page/recharge/recharge.js" 
          }
        }
      })
      //全部分类
      .state("allClassify", {
        url: "/allClassify/{goods_type_id}",
        views: {
          "": {
            templateUrl: "html/allClassify.html", 
            controllerUrl: "page/allClassify/allClassify.js" 
          }
        }
      })
      // .state("userMes", {
      //   url: "/userMes",
      //   views: {
      //     "": {
      //       templateUrl: "html/userMes.html", 
      //       controllerUrl: "page/userMes/userMes.js" 
      //     }
      //   }
      // })
      .state("search", {
        url: "/search",
        views: {
          "": {
            templateUrl: "html/search.html", 
            controllerUrl: "page/search/search.js" 
          }
        }
      })
      .state("zhengzaijinxing", {
        url: "/zhengzaijinxing",
        views: {
          "": {
            templateUrl: "html/zhengzaijinxing.html", 
            controllerUrl: "page/zhengzaijinxing/zhengzaijinxing.js" 
          }
        }
      })
      .state("dengdaijiexiao", {
        url: "/dengdaijiexiao",
        views: {
          "": {
            templateUrl: "html/dengdaijiexiao.html", 
            controllerUrl: "page/dengdaijiexiao/dengdaijiexiao.js" 
          }
        }
      })
      .state("myPoint", {
        url: "/myPoint",
        views: {
          "": {
            templateUrl: "html/myPoint.html", 
            controllerUrl: "page/myPoint/myPoint.js" 
          }
        }
      })

      .state("inviteMoney", {
        url: "/inviteMoney",
        views: {
          "": {
            templateUrl: "html/inviteMoney.html", 
            controllerUrl: "page/inviteMoney/inviteMoney.js" 
          }
        }
      })
      .state("balance_detail", {
        url: "/balance_detail",
        views: {
          "": {
            templateUrl: "html/balance_detail.html", 
            controllerUrl: "page/balance_detail/balance_detail.js" 
          }
        }
      })
      .state("myNew", {
        url: "/myNew",
        views: {
          "": {
            templateUrl: "html/myNew.html", 
            controllerUrl: "page/myNew/myNew.js" 
          }
        }
      })
      .state("selfRecord", {
        url: "/selfRecord/{uid}",
        views: {
          "": {
            templateUrl: "html/selfRecord.html", 
            controllerUrl: "page/selfRecord/selfRecord.js" 
          }
        }
      })
      .state("findPwd", {
        url: "/findPwd",
        views: {
          "": {
            templateUrl: "html/findPwd.html", 
            controllerUrl: "page/findPwd/findPwd.js" 
          }
        }
      })

      .state("userCenter", {
        url: "/userCenter",
        views: {
          "": {
            templateUrl: "html/userCenter.html", 
            controllerUrl: "page/userCenter/userCenter.js" 
          }
        }
      })

      .state("notice", {
        url: "/notice/{notice_id}",
        views: {
          "": {
            templateUrl: "html/notice.html", 
            controllerUrl: "page/notice/notice.js" 
          }
        }
      })

      .state("shareDetails", {
        url: "/shareDetails/{show_id}",
        views: {
          "": {
            templateUrl: "html/shareDetails.html", 
            controllerUrl: "page/shareDetails/shareDetails.js" 
          }
        }
      })

      .state("help", {
          url: "/help/{page}",
          views: {
              "": {
                  templateUrl: "html/help.html", 
                  controllerUrl: "page/help/help.js" 
              }
          }
      })

      .state("loginTransferPage", {
          url: "/loginTransferPage/{sessid}",
          views: {
              "": {
                  templateUrl: "html/transferPage.html", 
                  controllerUrl: "page/reg/transferPage.js" 
              }
          }
      })

      avalon.state.config({
        onError: function() {
          console.log(arguments);

        }, 
        onLoad: function(oldState, newState) {

          avalon.vmodels["headerEidget"].isLogin = avalon.cookie.get("sessId") ? true : false
          if(newState.stateName == 'login' || newState.stateName == 'reg') {
            avalon.vmodels["footEidget"].hideFooterIn = true;
          } else {
            avalon.vmodels["footEidget"].hideFooterIn = false;
          }
        },
       
        onBeforeUnload: function(newNode, oldNode) {
          
        }
      })

      avalon.history.start({
        // basepath: "/mmRouter"
      });
      

      avalon.scan();
    }
  };
      
  //start app
  $(function (){

    app.start();

  })

  return app;



})
