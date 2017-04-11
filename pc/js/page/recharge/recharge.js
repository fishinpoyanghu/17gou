define(['avalon','http/http-factory'],
    function(avalon,httpFactory) {

        var rechargeVM = avalon.define({
            $id: "rechargeCtrl",
            moneyData:['20','50','100','200','500'],
            selectIndex:0,
            inputMoney:'',
            payWay:'2',
            isCreatOrder:false,
            showWechatPay:false,
            isgetWechatPayResult:false,
            order_num:'',
            codeUrl:'',
            selectPayWay:function(payWay) {
                rechargeVM.payWay = payWay;
            },
            selectThisMoney:function(index) {
                rechargeVM.selectIndex = index;
            },
            resultTimeout:'',
            getRecharge:function() {
                if(rechargeVM.isCreatOrder) return;
                var money = getMoney();
                if(!/^[1-9]\d*$/.test(money)) {
                  alert('请输入正确的充值金额');
                  return;
                }
                rechargeVM.isCreatOrder = true;
                httpFactory.getRecharge(money,rechargeVM.payWay,function(re) {
                    re = JSON.parse(re);
                    if (re.code == 0) {
                        var data = re.data;
                       if(rechargeVM.payWay == '2') {  
                            window.location.href = data.pay_url2;
                       } else {  //微信
                            rechargeVM.order_num = data.order_num;
                            rechargeVM.codeUrl = 'img/loading.gif';
                            var img = new Image();
                            img.src = data.pay_url2;
                            img.onload = function() {
                                rechargeVM.codeUrl = data.pay_url2;
                                rechargeVM.resultTimeout = setTimeout(getWechatPayResult,1000)
                                avalon.scan()
                            }
                            rechargeVM.showWechatPay = true;
                            $('#wechatPayModal').modal('show');
                            $('#wechatPayModal').on('hidden.bs.modal', function (e) {
                                rechargeVM.resultTimeout && clearTimeout(rechargeVM.resultTimeout)
                            })
                       }
                    } else {
                        layer.msg(re.msg);
                    }

                }, function(err) {

                },function() {
                    rechargeVM.isCreatOrder = false;
                });
            },


        })

       
        function getWechatPayResult() {
            if(rechargeVM.isgetWechatPayResult) return;
            rechargeVM.isgetWechatPayResult = true;
            httpFactory.getWechatPayResult(rechargeVM.order_num,function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    if(re.data.status == 1) {
                        layer.msg('充值成功，请到个人中心查看余额');
                        $('#wechatPayModal').modal('hide');
                    } else {
                        rechargeVM.resultTimeout = setTimeout(getWechatPayResult,1000)
                    }
                    
                } else {
                    layer.msg(re.msg);
                }

            }, function(err) {
                layer.msg(JSON.parse(err).msg);
            },function() {
                rechargeVM.isgetWechatPayResult = false;
            });
        }

        function getMoney() {
            if(rechargeVM.selectIndex != '-1') {  //选择的金额
                return rechargeVM.moneyData[rechargeVM.selectIndex];
            } else {
                return rechargeVM.inputMoney;
            }
        }
        

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'recharge';
            }
            $ctrl.$onBeforeUnload = function() {}

            $ctrl.$vmodels = []

        })
    })
