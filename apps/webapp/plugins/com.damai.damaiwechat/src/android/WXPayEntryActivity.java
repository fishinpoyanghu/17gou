package com.damaiapp.yunke.wxapi;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;

import com.damai.damaiwechat.pay.wx.Constants;
import com.damaiapp.yunke.R;
import com.tencent.mm.sdk.modelbase.BaseReq;
import com.tencent.mm.sdk.modelbase.BaseResp;
import com.tencent.mm.sdk.modelpay.PayResp;
import com.tencent.mm.sdk.openapi.IWXAPI;
import com.tencent.mm.sdk.openapi.IWXAPIEventHandler;
import com.tencent.mm.sdk.openapi.WXAPIFactory;

/**
 * 需放于包名同一级下
 */
public class WXPayEntryActivity extends Activity implements IWXAPIEventHandler {

	private IWXAPI api;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_wx_pay_result);
		api = WXAPIFactory.createWXAPI(this, Constants.APP_ID);
		api.handleIntent(getIntent(), this);
	}

	@Override
	protected void onNewIntent(Intent intent) {
		super.onNewIntent(intent);
		setIntent(intent);
		api.handleIntent(intent, this);
	}

	@Override
	public void onReq(BaseReq req) { // 微信发送请求到第三方应用时，会回调到该方法
	}

	@Override
	public void onResp(BaseResp resp) { // 第三方应用发送到微信的请求处理后的响应结果，会回调到该方法
		// 0:支付成功，-2：取消支付
		PayResp payResp = (PayResp) resp;
		int code = payResp.errCode;
		String msg = payResp.errStr;
		Intent intent = new Intent();
		intent.setAction("com.damaiapp.kbm.wxpay");
		intent.putExtra("code", code);
		intent.putExtra("msg", msg);
		sendBroadcast(intent);
		finish();
	}
}
