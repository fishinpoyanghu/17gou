define(['avalon','http/http-factory'],
    function(avalon,httpFactory) {

        var viewModel = vm = avalon.define({
            $id: "payPageCtrl",
            order_num:'',
            pay:pay,
            inPay:false,
            payWay:2,
            order_data:{

            },
            selectPayWay:function(type) {
              viewModel.payWay = type;
            },
            resultTimeout:null,
            isgetWechatPayResult:false,
            codeUrl:'img/loading.gif',
            showWechatPay:false,
            buyNow:false
          
        })

        function removeCartMutiItem(goodsList){
        	console.log(goodsList)
          for(var i = 0;i < goodsList.length;i++) {
            if(goodsList[i].isSelected) {
              goodsList.removeAt(i)
              i--;
            }
          }
          avalon.cookie.set('cartInfo',JSON.stringify(goodsList.$model),{setmaxage:-1})
          
        }

        function pay (goodsList) {
          if(viewModel.order_data.need_money > 0) {
            getThirdPay(goodsList)
          } else {
            getNoPay(goodsList)
          }
          
        }

        function goResult(goodsList) {
          if(viewModel.buyNow == false) removeCartMutiItem(goodsList);
          avalon.cookie.remove("orderData");
          avalon.router.go('payResult',{order_num: 'payOrder_' + viewModel.order_num})
        }

        function getNoPay (goodsList) {
            if(vm.inPay) return;
            vm.inPay = true;
            httpFactory.getNoPay(viewModel.order_num,function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                   //跳转到支付结果
                   goResult(goodsList)
                } else {
                    layer.msg('支付失败')
                }

            }, function(err) {
              vm.error_tips = err.msg;
            },function() {
              vm.inPay = false;
            });
        }

        function getThirdPay(goodsList) {
          if(viewModel.payWay == 2) {
            location.href = viewModel.order_data.al_pay3
          } else {
            // location.href = viewModel.order_data.wx3_pay   
            viewModel.codeUrl = 'img/loading.gif';
            var img = new Image();
            img.src = viewModel.order_data.wx3_pay;
            img.onload = function() {
                viewModel.codeUrl = viewModel.order_data.wx3_pay;
                viewModel.resultTimeout = setTimeout(function() {
                  getWechatPayResult(goodsList)
                },1000)
                avalon.scan()
            }
            viewModel.showWechatPay = true;
            $('#wechatPayModal').modal('show');
            $('#wechatPayModal').on('hidden.bs.modal', function (e) {
                viewModel.resultTimeout && clearTimeout(viewModel.resultTimeout)
            })     
          }
        }

        function getWechatPayResult(goodsList) {
            if(viewModel.isgetWechatPayResult) return;
            viewModel.isgetWechatPayResult = true;
            httpFactory.getWechatPayResult(viewModel.order_num,function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    if(re.data.status == 1) {
                        // goResult(goodsList)
                        $('#wechatPayModal').modal('hide');
                    } else {
                        viewModel.resultTimeout = setTimeout(function() {
                          getWechatPayResult(goodsList)
                        },1000)
                    }
                    
                } else {
                    layer.msg(re.msg);
                }

            }, function(err) {
                layer.msg(JSON.parse(err).msg);
            },function() {
                viewModel.isgetWechatPayResult = false;
            });
        }

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function(state) {
              avalon.vmodels["headerEidget"].activePage = 'payOrder';
               viewModel.order_num = state.params.order_num.replace('payOrder_','');
               viewModel.order_data = JSON.parse(avalon.cookie.get("orderData"));
               viewModel.buyNow = state.params.buyNow;
                
            }
            $ctrl.$onBeforeUnload = function() {}

            

            $ctrl.$vmodels = []

        })
    })
