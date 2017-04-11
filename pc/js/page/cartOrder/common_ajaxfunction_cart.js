/* 
 * Description : 会员记录接口 请求函数
 * Author :guowendong@ddtkj.com
 * Time : 2015年 9月16日
 *   */
/*
 * 方法描述：购物车列表
 * 参数1： data 时间 状态参数
 * 参数2：callbackFun 回调函数
 * 返回值：Json格式   往期揭晓集合
 */
function ajaxCartList(callbackFun) {
	var cart = JSON.stringify(eval(localStorage.getItem("cart")))
	if(cart=="null"){
		cart='';
	}
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		data:{
			cart:cart
		},
		url: "/cart/select.do",
		beforeSend: ajaxBeforeSendOfList,
		complete: ajaxCompleteOfList,
		success: function(result) {
			//调用绑定页面数据方法
			callbackFun(result);//调用回调函数 传输json结果
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}
/*
 * 方法描述：商品检查
 * 参数1： data 时间 状态参数
 * 参数2：callbackFun 回调函数
 * 返回值：Json格式   往期揭晓集合
 */
function ajaxCartCheck(data,callbackFun,a) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		data:data,
		async: false,
		cache: true,
		url: "/cart/check.do",
		beforeSend: ajaxBeforeSendOfList,
		complete: ajaxCompleteOfList,
		success: function(result) {
			//调用绑定页面数据方法
			callbackFun(result,a);//调用回调函数 传输json结果
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}
/*
 * 方法描述：购物车商品列表
 * 参数1： data 时间 状态参数
 * 参数2：callbackFun 回调函数
 * 返回值：Json格式   往期揭晓集合
 */
function ajaxCartGoodsList(data,callbackFun) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		data:data,
		url: "/goods/getGoods.do",
		beforeSend: ajaxBeforeSendOfList,
		complete: ajaxCompleteOfList,
		success: function(result) {
			//调用绑定页面数据方法
			callbackFun(result);//调用回调函数 传输json结果
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}
/*
 * 方法描述：购物车结算列表
 * 参数1： data 时间 状态参数
 * 参数2：callbackFun 回调函数
 * 返回值：Json格式   往期揭晓集合
 */
function ajaxCartOrderList(data,callbackFun) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		data:data,
		url: "/cart/orderList.do",
		beforeSend: ajaxBeforeSendOfList,
		complete: ajaxCompleteOfList,
		success: function(result) {
			//调用绑定页面数据方法
			callbackFun(result);//调用回调函数 传输json结果
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}
/*
 * 方法描述：购物车结算列表
 * 参数1： data 时间 状态参数
 * 参数2：callbackFun 回调函数
 * 返回值：Json格式   往期揭晓集合
 */
function ajaxGetCartToken(callbackFun) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		async: false,
		cache: true,
		url: "/cart/getCartToken.do?randomTime=" + (new Date()).getTime(),
		beforeSend: ajaxBeforeSendOfList,
		complete: ajaxCompleteOfList,
		success: function(result) {
			//调用绑定页面数据方法
			callbackFun(result);//调用回调函数 传输json结果
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}
/*
 * 方法描述：购物车地址
 * 参数1： data 时间 状态参数
 * 参数2：callbackFun 回调函数
 * 返回值：Json格式   往期揭晓集合
 */
function ajaxGetAddress(callbackFun) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		async: false,
		url: "/member/address/addressList.do?r="+(new Date()).getTime(),
		beforeSend: ajaxBeforeSendOfList,
		complete: ajaxCompleteOfList,
		success: function(result) {
			//调用绑定页面数据方法
			callbackFun(result);//调用回调函数 传输json结果
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}
/*
 * 方法描述：购物车结算
 * 参数1： data 时间 状态参数
 * 参数2：callbackFun 回调函数
 * 返回值：Json格式   往期揭晓集合
 */
function ajaxSettlement(url,data,callbackFun) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		url: url,
		data:data,
		beforeSend: ajaxBeforeSendOfList,
		complete: ajaxCompleteOfList,
		success: function(result) {
			//调用绑定页面数据方法
			callbackFun(result);//调用回调函数 传输json结果
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}
/*****************************************************以下为免税***********************************************************************/
/*
 * 方法描述：购物车列表
 * 参数1： data 时间 状态参数
 * 参数2：callbackFun 回调函数
 * 返回值：Json格式   往期揭晓集合
 */
function ajaxDfCartList(callbackFun) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		url: "/cart/selectDf.do",
		beforeSend: ajaxBeforeSendOfList,
		complete: ajaxCompleteOfList,
		success: function(result) {
			//调用绑定页面数据方法
			callbackFun(result);//调用回调函数 传输json结果
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}
/*
 * 方法描述：购物车免税商品列表
 * 参数1： data 时间 状态参数
 * 参数2：callbackFun 回调函数
 * 返回值：Json格式   往期揭晓集合
 */
function ajaxDfGoodsList(data,callbackFun) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		data:data,
		url: "/free/list.do",
		beforeSend: ajaxBeforeSendOfList,
		complete: ajaxCompleteOfList,
		success: function(result) {
			//调用绑定页面数据方法
			callbackFun(result);//调用回调函数 传输json结果
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}
/*
 * 方法描述：商品检查
 * 参数1： data 时间 状态参数
 * 参数2：callbackFun 回调函数
 * 返回值：Json格式   往期揭晓集合
 */
function ajaxDfCartCheck(data,callbackFun,a) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		data:data,
		cache: true,
		url: "/cart/checkDf.do",
		beforeSend: ajaxBeforeSendOfList,
		complete: ajaxCompleteOfList,
		success: function(result) {
			//调用绑定页面数据方法
			callbackFun(result,a);//调用回调函数 传输json结果
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}
/*
 * 方法描述：购物车结算列表
 * 参数1： data 时间 状态参数
 * 参数2：callbackFun 回调函数
 * 返回值：Json格式   往期揭晓集合
 */
function ajaxDfCartOrderList(data,callbackFun) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		data:data,
		url: "/cart/dfOrderList.do",
		beforeSend: ajaxBeforeSendOfList,
		complete: ajaxCompleteOfList,
		success: function(result) {
			//调用绑定页面数据方法
			callbackFun(result);//调用回调函数 传输json结果
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}