define(['avalon', 'jquery','http/http-factory'],
    function(avalon,$,httpFactory) {

        var viewModel = vm = avalon.define({
            $id: "cartPageCtrl",
            isMakingOrder:false,
            selectGoods:function($event,goodsItem) {
                goodsItem.isSelected = !goodsItem.isSelected;
            },
            decrease:function (goodsItem,unit,index) { //index 用于判断是否是首页，首页的次数默认为1不为10
                unit = unit || 1;
                if(goodsItem.activity_type == 2) unit = 10;
                if(goodsItem.activity_type == 4) unit = goodsItem.need_num/2;
                if(index=='index') unit =1;
                goodsItem.join_number = Number(goodsItem.join_number);
                goodsItem.join_number = goodsItem.join_number - goodsItem.join_number % unit;
                goodsItem.join_number = goodsItem.join_number - unit;
                if (goodsItem.join_number <= 0) {
                    goodsItem.join_number = unit;
                }
            },
            increase:function (goodsItem,unit,index) { //index 用于判断是否是首页，首页的次数默认为1不为10
                unit = unit || 1;
                if(goodsItem.activity_type == 2) unit = 10;
                if(goodsItem.activity_type == 4) unit = goodsItem.need_num/2;
                if(index=='index') unit =1;
                var join_number = Number(goodsItem.join_number);
                join_number = join_number + unit;
                if (join_number >= goodsItem.remain_num) {
                    goodsItem.join_number = goodsItem.remain_num;
                } else {
                    goodsItem.join_number = join_number
                }
                if(goodsItem.activity_type == 3 && goodsItem.join_number > 10) goodsItem.join_number = 10;
                if(goodsItem.activity_type == 2 && (join_number >= goodsItem.remain_num/10)){
                	if (index == 'index') {
	                	goodsItem.join_number = goodsItem.remain_num/10;
                	}
                }
                if(goodsItem.activity_type == 4 && (join_number>=2)){
                	if (index == 'index') {
	                	goodsItem.join_number = 2 - (Math.ceil((goodsItem.need_num - goodsItem.remain_num)/(goodsItem.need_num/2)));
	                	if (goodsItem.join_number <= 0) {
	                    	goodsItem.join_number = unit;
	                	}
                	}
                }
            },
            editJoinNumber:function (goodsItem,unit,index) {//index 用于判断是否是首页，首页的次数默认为1不为10
                unit = unit || 1;
                if(goodsItem.activity_type == 2) unit = 10;
                if(goodsItem.activity_type == 4) unit = goodsItem.need_num/2;
                if(index=='index') unit =1;
                var joinNumber = '' + Math.ceil(goodsItem.join_number/unit) * unit;
                if (joinNumber == '') {
                    goodsItem.join_number = 1;
                    if(goodsItem.activity_type == 2) goodsItem.join_number = 10;
                    if(goodsItem.activity_type == 4) goodsItem.join_number = goodsItem.need_num/2;
                    return;
                }
                var remainNumber = goodsItem.remain_num;
                if(goodsItem.activity_type==3 && goodsItem.remain_num > 10) {
                    remainNumber = 10;
                }
                var match = joinNumber.match(/^[1-9]\d*/);
                if (match == null) {
                    joinNumber = unit;
                } else {
                    joinNumber = parseInt(match[0]);
                    joinNumber = joinNumber > remainNumber ? remainNumber : joinNumber;
                    joinNumber = joinNumber <= 0 ? unit : joinNumber;
                }
                goodsItem.join_number = Math.ceil(joinNumber/unit) * unit;
                if(goodsItem.activity_type == 2 && (joinNumber>=goodsItem.remain_num/10)){
                	if (index == 'index') {
	                	goodsItem.join_number = goodsItem.remain_num/10;
                	}else{
                	}
                }
                if(goodsItem.activity_type == 4 && (joinNumber>=2)){
                	if (index == 'index') {
	                	goodsItem.join_number = 2 - (Math.ceil((goodsItem.need_num - goodsItem.remain_num)/(goodsItem.need_num/2)));
	                	if (goodsItem.join_number <= 0) {
		                    goodsItem.join_number = unit;
		                }
                	}
                }
            },
            makeOrder:function (goodsList) {
                if(httpFactory.isLogin()) {
                    if(checkSelect(goodsList)) {
                        makeOrder(goodsList);
                    } else {
                        layer.msg('请先选择商品');
                    }
                } else {
                    avalon.router.go('login');
                }
                
            }
            
        })

        function makeOrder (goodsList) {
            if(vm.isMakingOrder) return;
            vm.isMakingOrder = true;
            var orderData = getOrderData(goodsList)
            
            httpFactory.getOrderInfo(orderData,function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                   avalon.cookie.set("orderData",JSON.stringify(re.data));
                   avalon.router.go('payOrder',{order_num: 'payOrder_' + re.data.order_num,buyNow:'false'})
                } else if(re.code == 1) {
                    layer.confirm('订单金额为' + re.data.order_money +'元，余额不足，请先充值', {icon: 2, title:'温馨提示',btn:['去充值','取消']}, function(index){
                        avalon.router.go('recharge')
                        layer.close(index);
                    });
                } else {
                    layer.msg('生成订单失败' + re.msg);
                }

            }, function(err) {
              vm.error_tips = err.msg;
            },function() {
              vm.isMakingOrder = false;
            });
        }
        
        function getOrderData (goodsList) {
            var orderData = [];
            for(var i = 0,len = goodsList.length;i < len;i++) {
              if(goodsList[i].isSelected) {
                orderData.push({
                    activity_id:goodsList[i].activity_id,
                    goods_title:goodsList[i].goods_title,
                    num:goodsList[i].join_number
                })
              }
            }
            return orderData;
        }

        function checkSelect (goodsList) {
            for(var i = 0,len = goodsList.length;i < len;i++) {
              if(goodsList[i].isSelected) return true;
            }
            return false;
        } 
        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
               avalon.vmodels["headerEidget"].activePage = 'cartOrder';
            }
            $ctrl.$onBeforeUnload = function() {}
            $ctrl.cartobj=vm.makeOrder; //返回订单接口方法，让首页立即购买可以调用
            // 指定一个avalon.scan视图的vmodels，vmodels = $ctrl.$vmodels.concact(DOM树上下文vmodels)
            $ctrl.$vmodels = []

        })
    })
