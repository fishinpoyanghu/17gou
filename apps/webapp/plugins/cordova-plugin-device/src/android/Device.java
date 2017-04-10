/*
       Licensed to the Apache Software Foundation (ASF) under one
       or more contributor license agreements.  See the NOTICE file
       distributed with this work for additional information
       regarding copyright ownership.  The ASF licenses this file
       to you under the Apache License, Version 2.0 (the
       "License"); you may not use this file except in compliance
       with the License.  You may obtain a copy of the License at

         http://www.apache.org/licenses/LICENSE-2.0

       Unless required by applicable law or agreed to in writing,
       software distributed under the License is distributed on an
       "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
       KIND, either express or implied.  See the License for the
       specific language governing permissions and limitations
       under the License.
*/
package org.apache.cordova.device;

import java.util.TimeZone;

import org.apache.cordova.CordovaWebView;
import org.apache.cordova.CallbackContext;
import org.apache.cordova.CordovaPlugin;
import org.apache.cordova.CordovaInterface;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.provider.Settings;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.provider.Settings;
import android.telephony.TelephonyManager;
import android.util.DisplayMetrics;
import android.view.WindowManager;
import android.content.Context;

public class Device extends CordovaPlugin {
    public static final String TAG = "Device";

    public static String platform;                            // Device OS
    public static String uuid;                                // Device UUID

    private static final String ANDROID_PLATFORM = "Android";
    private static final String AMAZON_PLATFORM = "amazon-fireos";
    private static final String AMAZON_DEVICE = "Amazon";

    /**
     * Constructor.
     */
    public Device() {
    }

    /**
     * Sets the context of the Command. This can then be used to do things like
     * get file paths associated with the Activity.
     *
     * @param cordova The context of the main Activity.
     * @param webView The CordovaWebView Cordova is running in.
     */
    public void initialize(CordovaInterface cordova, CordovaWebView webView) {
        super.initialize(cordova, webView);
        Device.uuid = getUuid();
    }

    /**
     * Executes the request and returns PluginResult.
     *
     * @param action            The action to execute.
     * @param args              JSONArry of arguments for the plugin.
     * @param callbackContext   The callback id used when calling back into JavaScript.
     * @return                  True if the action was valid, false if not.
     */
    public boolean execute(String action, JSONArray args, CallbackContext callbackContext) throws JSONException {
        if (action.equals("getDeviceInfo")) {
            JSONObject r = new JSONObject();
            r.put("uuid", Device.uuid);
            r.put("version", this.getOSVersion());
            r.put("platform", this.getPlatform());
            r.put("model", this.getModel());
            r.put("manufacturer", this.getManufacturer());
            r.put("nm", this.getNm());
            r.put("mno", this.getMno());
            r.put("did",this.getDid());
            r.put("dm", this.getDm());
            callbackContext.success(r);
        }
        else {
            return false;
        }
        return true;
    }

    //--------------------------------------------------------------------------
    // LOCAL METHODS
    //--------------------------------------------------------------------------

    /**
     * Get the OS name.
     *
     * @return
     */
    public String getPlatform() {
        String platform;
        if (isAmazonDevice()) {
            platform = AMAZON_PLATFORM;
        } else {
            platform = ANDROID_PLATFORM;
        }
        return platform;
    }

    /**
     * Get the device's Universally Unique Identifier (UUID).
     *
     * @return
     */
    public String getUuid() {
        String uuid = Settings.Secure.getString(this.cordova.getActivity().getContentResolver(), android.provider.Settings.Secure.ANDROID_ID);
        return uuid;
    }

    public String getModel() {
        String model = android.os.Build.MODEL;
        return model;
    }

    public String getProductName() {
        String productname = android.os.Build.PRODUCT;
        return productname;
    }

    public String getManufacturer() {
        String manufacturer = android.os.Build.MANUFACTURER;
        return manufacturer;
    }
    /**
     * Get the OS version.
     *
     * @return
     */
    public String getOSVersion() {
        String osversion = android.os.Build.VERSION.RELEASE;
        return osversion;
    }

    public String getSDKVersion() {
        @SuppressWarnings("deprecation")
        String sdkversion = android.os.Build.VERSION.SDK;
        return sdkversion;
    }

    public String getTimeZoneID() {
        TimeZone tz = TimeZone.getDefault();
        return (tz.getID());
    }

    /**
     * Function to check if the device is manufactured by Amazon
     *
     * @return
     */
    public boolean isAmazonDevice() {
        if (android.os.Build.MANUFACTURER.equals(AMAZON_DEVICE)) {
            return true;
        }
        return false;
    }

  /**
	 * 获取联网的方式如：wifi
	 * @return
	 */
	public String getNm(){
		 ConnectivityManager cm = (ConnectivityManager) this.cordova.getActivity().getSystemService(Context.CONNECTIVITY_SERVICE);
	     NetworkInfo info = cm.getActiveNetworkInfo();
	     if(info != null){
	    	 String typeName = info.getTypeName();
	    	 return typeName;
	     }
	     return "";
	}




		  /**
       * 返回运营商 需要加入权限 <uses-permission android:name="android.permission.READ_PHONE_STATE"/> <BR>
       *
       * @return 1,代表中国移动，2，代表中国联通，3，代表中国电信，0，代表未知
       * @author youzc@yiche.com
       */
      public String getMno() {
          // 移动设备网络代码（英语：Mobile Network Code，MNC）是与移动设备国家代码（Mobile Country Code，MCC）（也称为“MCC /
          // MNC”）相结合, 例如46000，前三位是MCC，后两位是MNC 获取手机服务商信息
          String OperatorsName = "未知";
          String IMSI =  ((TelephonyManager) this.cordova.getActivity().getSystemService(Context.TELEPHONY_SERVICE)).getDeviceId();
          // IMSI号前面3位460是国家，紧接着后面2位00 运营商代码
          if(IMSI==null){
          	return OperatorsName ;
          }
          if (IMSI.startsWith("46000") || IMSI.startsWith("46002") || IMSI.startsWith("46007")) {
              OperatorsName = "中国移动";
          } else if (IMSI.startsWith("46001") || IMSI.startsWith("46006")) {
              OperatorsName = "中国联通";
          } else if (IMSI.startsWith("46003") || IMSI.startsWith("46005")) {
              OperatorsName = "中国电信";
          }
          return OperatorsName;
      }

        /**
       	 *  获取设备id
       	 * @return
       	 */
          public String getDid() {
              String did = "0000000000000";
              String deviceId =  ((TelephonyManager) this.cordova.getActivity().getSystemService(Context.TELEPHONY_SERVICE)).getDeviceId();
              // IMSI号前面3位460是国家，紧接着后面2位00 运营商代码
              if(deviceId==null){
              	return did ;
              }
          	  did = deviceId ;
              return did;
          }


    /**
    	 * 获取屏幕分辨率
    	 * @return
    	 */
    	public String getDm(){
    		DisplayMetrics dm = new DisplayMetrics();
    		WindowManager window = ((WindowManager) this.cordova.getActivity().getSystemService(Context.WINDOW_SERVICE));
    		window.getDefaultDisplay().getMetrics(dm);
        int nowWidth = dm.widthPixels;
        int nowHeigth = dm.heightPixels;
        return nowWidth + "*" + nowHeigth ;
    	}


}
