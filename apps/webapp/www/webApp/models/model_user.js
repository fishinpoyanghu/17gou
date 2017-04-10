/**
 * 用户接口数据请求模块
 * Created by luliang on 2015/11/26.
 */
define(['app', 'utils/httpRequest', 'html/common/geturl_service'], function(app) {

    app.factory('userModel', ['httpRequest', 'MyUrl', function(httpRequest, MyUrl) {

        /**
         * 发送忘记密码短信验证
         */
        function sendForgetPasswordSms(phoneNumber,code, onSuccess, onFail) {
            var requestUrl = '?c=sms&a=send_forgot_mcode';
            var params = {
                mobile: phoneNumber,
                code:code
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail);
        }
       /**
         * 发送绑定手机短信验证
         */
        function sendBindPhonedSms(phoneNumber,code, onSuccess, onFail) {
            var requestUrl = '?c=sms&a=send_bind_mcode';  
            var params = {
                mobile: phoneNumber,
                code:code
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail);
        }

        /**
         * 校验忘记密码的短信验证码
         */
        function checkVerificationCode(phoneNumber, verificationCode, onSuccess, onFail) {
            var requestUrl = '?c=user&a=check_forgot_mcode';
            var params = {
                mobile: phoneNumber,
                mcode: verificationCode
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail);
        }

        /**
         * 重置密码（忘记密码后重置）
         */
        function resetPassword(saveKey, newPassword, onSuccess, onFail) {
            var requestUrl = '?c=user&a=save_forgot_password';
            var params = {
                savekey: saveKey,
                password: newPassword
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail);
        }


        /**
         * 登录
         */
        function login(phonenum, password, onSuccess, onFail) {

            var requestUrl = '?c=user&a=login';
            var params = {
                name: phonenum,
                password: password
            };
            var request = httpRequest.post(requestUrl, params, onSuccess, onFail);
        }

        /**
         * 我添加绑定手机号码
         */
        function BoundPhoneNumber(phonenum, onSuccess, onFail) {

            var requestUrl = '?c=user&a=BoundPhoneNumber';
            var params = {
                name: phonenum,
            };
            var request = httpRequest.post(requestUrl, params, onSuccess, onFail);
        }


        /**
         * 注册
         */
        function register(registerRequest, onSuccess, onFail) {

            var requestUrl = '?c=user&a=reg';
            var request = httpRequest.post(requestUrl, registerRequest, onSuccess, onFail);
        }

        /**
         * 获取注册短信验证码
         */
        function getRegisterSms(number,code, onSuccess, onFail) {
            var requestUrl = "?c=sms&a=send_reg_mcode";
            var request = httpRequest.post(requestUrl, {
                    mobile: number,
                    code:code
                },
                onSuccess, onFail
            );
        }

        function logout(onSuccess, onFail) {
            var requestUrl = '?c=user&a=logout';
            var request = httpRequest.post(requestUrl, {}, onSuccess, onFail);
        }

        /**
         * 修改密码（知道旧密码）
         */
        function modifyPassword(oldPassword, newPassword, onSuccess, onFail) {
            var requestUrl = '?c=user&a=modify_password';
            var params = {
                password: newPassword,
                password_old: oldPassword
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail);
        }


        /**
         * 获取当前登录的用户信息
         */
        function getLoginUserInfo(onSuccess, onFail) {
            var requestUrl = '?c=user&a=get_login';
            var params = {};
            httpRequest.post(requestUrl, params, onSuccess, onFail);
        }

        /**
         * 获取用户代理的信息，在用户进入代理模块时调用获取
        */
//		function getagencymsg (){
//			alert('进入代理模块')
//			var requestUrl = '?nc_user';
//          httpRequest.post(requestUrl);
//		}
		
		
		
        /**
         * 获取用户是否关注公众号
         */
        function getwxmsg(onSuccess, onFail) {
            var requestUrl = '?c=nc_user&a=getwxinfo';
            var params = {};
            httpRequest.post(requestUrl, params, onSuccess, onFail);
        }



        /**
         * 修改个人信息
         */
        function modifyUserInfo(nickname, signatures, onSuccess, onFail) {
            var requestUrl = '?c=user&a=modify_user_info';
            var params = {
                nick: nickname,
                signature: signatures
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail);
        }

        /**
         * 修改昵称
         */
        function modifyNick(nickname, onSuccess, onFail) {
            var requestUrl = '?c=user&a=modify_user_info';
            var params = {
                nick: nickname
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail);
        }

        /**
         * 修改个性签名
         */
        function modifySignature(signatures, onSuccess, onFail) {
            var requestUrl = '?c=user&a=modify_user_info';
            var params = {
                signature: signatures
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail);
        }


        /**
         * 获取我的收藏列表
         */
        function getCollectList(from, count, onSuccess, onFail, onFinal) {
            var requestUrl = '?c=fav&a=fav_list';
            var params = {
                from: from,
                count: count
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }

        /**
         * 更新用户用户头像
         * @param file
         * @param onSuccess
         * @param onFail
         * @param onFinal
         */
        function updateHeadIcon(file, onSuccess, onFail, onFinal) {
            var requestUrl = '?c=user&a=upload_icon';
            var params = {
                file: file
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }

        /**
         * 浏览器打开微信授权登录
         */
        function weChatLoginFromBrowser(msg) {

            var requestUrl = '?c=weixin&a=go_wx';
            var params = MyUrl.getDefaultParams();
            if(msg && msg['invite_code']){
              params['invite_code']=msg['invite_code'];
            }
            for (var key in params) {
                requestUrl = requestUrl + '&' + key + '=' + params[key];
            }             
            window.location.href = httpRequest.getBaseUrl() + requestUrl;

        }
        /**
         * 客户端微信sdk登录
         * @param token
         * @param onSuccess
         * @param onFail
         * @param onFinal
         */
        function weChatLoginFromSDK(access_token,open_id, onSuccess, onFail, onFinal) {
            var requestUrl = '?c=weixin&a=login_wx';
            var params = {
                accessToken: access_token,
                openId:open_id
            };

            httpRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }

        function sinaLogin(access_token,open_id, onSuccess, onFail, onFinal) {
            var requestUrl = '?c=wb&a=loginWb';
            var params = {
                code: open_id + '|||' + access_token
            };

            httpRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }

        function qqLogin(access_token,open_id, onSuccess, onFail, onFinal) {
            var requestUrl = '?c=qq&a=loginQq';
            var params = {
                code: open_id + '|||' + access_token
            };

            httpRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }


        /**
         * 上传图片
         * Base64编码的图片字符串，多图的话是字符串数组
         * 支持png,jpg,gif
         * 本接口支持单图和多图上传
         */
        function uploadImage(file, onSuccess, onFail, onFinal) {
            var requestUrl = '?c=bbs&a=upload_img_base64';
            var params = {
                file: file
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }

        /**
         * 绑定邀请码
         */
        function bindInviteCode(invite_code, onSuccess, onFail) {
            var requestUrl = '?c=user&a=bind_invite_code';
            var params = {
                invite_code: invite_code
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail, null);
        }
        /**
         * 获取我的积分
         */
        function getMyPoint(onSuccess, onFail) {
            var requestUrl = '?c=nc_user&a=point';
            var params = {

            };
            httpRequest.post(requestUrl, params, onSuccess, onFail, null);
        }
        /**
         * 获取我的积分明细
         */
        function getMyPointDetails(page, onSuccess, onFail, onFinal) {
            var requestUrl = '?c=nc_user&a=point_detail';
            var params = {
                page: page
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }
        /**
         * 获取我的余额明细
         */
        function getAccountDetails(page, onSuccess, onFail, onFinal) {
            var requestUrl = '?c=nc_user&a=money_detail';
            var params = {
                page: page
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }
        /**
         * 获取我的余额
         */
        function getAccount(onSuccess, onFail, onFinal) {
            var requestUrl = '?c=nc_user&a=money';
            var params = {

            };
            httpRequest.get(requestUrl, params, onSuccess, onFail, onFinal);
        }
        /**
         * 获取签到列表
         */
        function getSignList(onSuccess, onFail, onFinal) {
            var requestUrl = '?c=nc_user&a=sign_already';
            var params = {

            };
            httpRequest.get(requestUrl, params, onSuccess, onFail, onFinal);
        }
        /**
         * 签到
         */
        function sign(onSuccess, onFail, onFinal) {
            var requestUrl = '?c=nc_user&a=sign';
            var params = {

            };
            httpRequest.get(requestUrl, params, onSuccess, onFail, onFinal);
        }
        /**
         * 师徒收益明细
         */
        function getInviteMoney(page,onSuccess, onFail, onFinal) {
            var requestUrl = '?c=nc_user&a=inviteMoney';
            var params = {
              page:page
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }
        /**
         * 邀请好友明细
         */
        function getInviteUser(onSuccess, onFail, onFinal) {
            var requestUrl = '?c=nc_user&a=inviteUser';
            var params = {

            };
            httpRequest.get(requestUrl, params, onSuccess, onFail, onFinal);
        }
        /**
         * 邀请好友数和师徒收益数
         */
        function getInviteInfo(onSuccess, onFail, onFinal) {
            var requestUrl = '?c=nc_user&a=inviteInfo';
            var params = {

            };
            httpRequest.get(requestUrl, params, onSuccess, onFail, onFinal);
        }
        /**
         * 获取抽奖奖品lotteryRun
         */
        function getLottery(onSuccess, onFail, onFinal) {
            var requestUrl = '?c=nc_user&a=lottery';
            var params = {

            };
            httpRequest.get(requestUrl, params, onSuccess, onFail, onFinal);
        }
        /**
         * 抽奖
         */
        function getLotteryRun(params,onSuccess, onFail, onFinal) {
            var requestUrl = '?c=nc_user&a=lotteryRun';            
            httpRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }
        /**
         * 抽奖奖品列表toAddPoint
         */
        function getLotteryList(page,onSuccess, onFail, onFinal) {
            var requestUrl = '?c=nc_user&a=lotteryList';
            var params = {
              page:page
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }

        function getLotteryList_1(page,each,onSuccess, onFail, onFinal) {
            var requestUrl = '?c=nc_user&a=luckyLottery';
            var params = {
              page:page,
              each:each
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }
        /**
         * 分享通知加积分
         */
        function toAddPoint(onSuccess, onFail, onFinal) {
            var requestUrl = '?c=nc_user&a=share';
            var params = {

            };
            httpRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }
        /**
         * 提现记录
         */
        function getCashRecord(page,onSuccess, onFail, onFinal) {
            var requestUrl = '?c=nc_user&a=cashRecord';
            var params = {
              page:page
            };
            httpRequest.get(requestUrl, params, onSuccess, onFail, onFinal);
        }
        /**
         * 申请提现
         */
        function getApplyCash(params,onSuccess, onFail, onFinal) {
            var requestUrl = '?c=nc_user&a=cash';
            httpRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }
        /**
         * 云币兑换余额
         */
        function changePoint(params,onSuccess, onFail, onFinal) {
            var requestUrl = '?c=nc_user&a=changePoint';
            httpRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }
        function getredpacket(onSuccess, onFail) {
            var requestUrl = '?c=nc_user&a=getredpacket';
            var params = {};
            httpRequest.post(requestUrl, params, onSuccess, onFail);
        }
        function bindphone(params,onSuccess, onFail) {
            var requestUrl = '?c=nc_user&a=bindphone'; 
            httpRequest.post(requestUrl, params, onSuccess, onFail);
        }


        //获取他/她写的心愿清单
        function christmas_wish(params,onSuccess, onFail) {
            var requestUrl = '?c=nc_user&a=christmas_wish';
            httpRequest.post(requestUrl, params, onSuccess, onFail);
        }
        function getchristmas_wish(params,onSuccess, onFail) {
            var requestUrl = '?c=nc_user&a=getchristmas_wish';
            httpRequest.post(requestUrl, params, onSuccess, onFail);
        }
        //获取折线图的数据
        function getline_chart(year,month,type,params,onSuccess, onFail) {
            var requestUrl = '?c=nc_user_show&a=getlineChart';
            var params = {
                year:year,
                month:month,
                type:type
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail);
        }


        /**
         * 公盘记录
         */
        function getDisk(onSuccess, onFail, onFinal) {
            var requestUrl = '?c=nc_user_show&a=disk';
            var params = {

            };
            httpRequest.get(requestUrl, params, onSuccess, onFail, onFinal);
        }

        /**
         * 获取最新动态的数据
         */
        function getRecentOrderList(from, count, onSuccess, onFail, onFinal) {
            var requestUrl = '?c=nc_user_show&a=recentOrder';
            var params = {
                from: from,
                count: count
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }
		
        //		获取福袋的数据
    		function getLuckyBagList(from,count, onSuccess, onFail, onFinal){
    			var requestUrl = '?c=nc_user_show&a=luckypacket';
                var params = {
                    count: count,
                    from:from
                };
                httpRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
    		}

        //获取开启福袋的数据
        function getPacketOrder(num,onSuccess, onFail) {
            var requestUrl = '?c=nc_order&a=packetorder';
            var params = {
                num:num
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail);
        }
        ////推荐消费绑定七天关系
        function bindshare(obj,onSuccess, onFail) {
            var requestUrl = '?c=nc_user&a=bindshare';
            var params = {
                goodsid:obj.goodsid,
                type:obj.type,
                invite_code:obj.invite_code

            };
            httpRequest.post(requestUrl, params, onSuccess, onFail);
        }
        //统计进各页面的次数
        function statisPageNum(type,onSuccess, onFail,onFinal) {
            //1 加盟 2 提现 3消费全返 4我的收益 5我的团队 6成为区域   7 消费全返介绍 8商品页面 玩法介绍
            var requestUrl = '?c=nc_user&a=user_dynamic';
            var params = {
                type:type
            };
            httpRequest.post(requestUrl, params, onSuccess, onFail,onFinal);
        }
        return {
        	getLuckyBagList:getLuckyBagList,				//获取福袋的数据
            sendForgetPasswordSms: sendForgetPasswordSms, //发送忘记短信验证码
//          getagencymsg: getagencymsg,		//获取用户代理信息
            checkVerificationCode: checkVerificationCode, //检测验证码合法性
            resetPassword: resetPassword, //重置密码
            login: login, //登录
            logout: logout, //注销
            register: register, //注册
            getRegisterSms: getRegisterSms, //获取注册短信验证码
            sendBindPhonedSms: sendBindPhonedSms, //获取注册短信验证码
            modifyPassword: modifyPassword, //修改密码
            getLoginUserInfo: getLoginUserInfo, //获取登录用户信息
            modifyUserInfo: modifyUserInfo, //修改用户信息
            modifyNick: modifyNick, //修改昵称
            modifySignature: modifySignature, //修改个性签名
            getCollectList: getCollectList, //获取收藏列表
            updateHeadIcon: updateHeadIcon, //上传用户头像
            weChatLoginFromBrowser: weChatLoginFromBrowser, //浏览器打开微信授权登录
            weChatLoginFromSDK: weChatLoginFromSDK, //客户端微信sdk登录
            uploadImage: uploadImage, // 上传图片
            bindInviteCode: bindInviteCode, //绑定邀请码
            getMyPoint: getMyPoint, //获取我的积分
            getMyPointDetails: getMyPointDetails, //获取我的积分明细
            getAccountDetails: getAccountDetails, //获取我的余额明细
            getAccount: getAccount, //获取我的余额
            getSignList: getSignList, //获取签到列表
            sign: sign, //签到
            getInviteMoney:getInviteMoney,    //师徒收益明细
            getInviteUser:getInviteUser,    //邀请好友明细
            getInviteInfo:getInviteInfo,     //邀请好友数和师徒收益数
            getLottery:getLottery,        //获取抽奖奖品
            getLotteryRun:getLotteryRun,     //抽奖
            getLotteryList:getLotteryList,    //获取抽奖列表
            getLotteryList_1:getLotteryList_1,
            toAddPoint:toAddPoint,
            getCashRecord:getCashRecord, //提现申请记录
            getApplyCash:getApplyCash,   //提现申请
            sinaLogin:sinaLogin,  
            qqLogin:qqLogin,
	    changePoint: changePoint, //云币兑换余额
       bindphone:bindphone,     //绑定手机号码
      getredpacket:getredpacket, //获取红包
            getwxmsg:getwxmsg,   //获取用户有没有关注公众号
            christmas_wish:christmas_wish,  //获取他/她写的心愿清单
            getchristmas_wish:getchristmas_wish, //获取他/她写的心愿清单
            getline_chart:getline_chart, //获取折线图的所有数据
            getDisk:getDisk, //获取公盘的数据
            getRecentOrderList:getRecentOrderList, //获取最新动态的数据
            getPacketOrder:getPacketOrder, //获取开启福袋的数据
            bindshare:bindshare, //推荐消费绑定七天关系
            statisPageNum:statisPageNum //统计进各页面的次数
        }

    }]);


});
