define(['avalon', 'utils/httpDefaultRequest', 'jquery'],
    function(avalon, httpDefaultRequest) {

        function getHtml(type, onSuccess, onFailed, onFinal) {
            var data = {
                question: 'question',
                // rule: 'one',
                // redRule: 'red',
                // lotteryRule: 'lottery',
                ruleIntroduction: 'ruleIntroduction',           //规则介绍
                know:'know',                                   //了解亿七购
                fortune:'fortune',                            //会员福分经验
                systemEnsure:'systemEnsure',                  //亿七购保障体系
                safetyPayment:'safetyPayment',                //安全支付
                complaint:'complaint',                        //投诉建议
                deliveryMoney:'deliveryMoney',                //配送费用
                sign:'sign',                                  //商品验货与签收
                noReceive:'noReceive',                        //长时间未收到商品问题
                introduce:'introduce',                        //亿七购介绍
                serviceAgreement:'serviceAgreement',           //服务协议
                contact:'contact',                            //联系我们
                cooperation:'cooperation',                    //商务合作
                invite:'invite',                              //邀请
                qq:'qq'                                       //官方QQ交流群
            };
            var url = httpDefaultRequest.getBaseWebappUrl().replace('apps/webapp/www/', '');
            url += 'uploads/other/' + data[type] + '.html?=' + (+new Date());

            return httpDefaultRequest.get(url, {}, onSuccess, onFailed, onFinal);
        }

        /**
         * 获取商品活动列表
         * @param {Number} goods_type_id int(11)    类别id（获取特定分类使用）
         * @param {String} key_word string(60)  关键字（搜索匹配）
         * @param {String} order_key string(60) 按order_key排序
         * @param {String} order_type string(60)    排序类型，asc和desc。默认asc
         * @param {Number} from int(11) 表示从第几条开始返回数据，默认:1，表示从第1条开始
         * @param {Number} count int(11)    表示最多拉取几条消息过来，默认:10
         * @param {Number} status int(11)   活动状态，默认为0。0：还未结束，1：即将揭晓，2：已经揭晓
         * @param {Number} activity_type int(11)    专区，1：全部；2：10元；3：限购
         * @param {Function} [onSuccess]
         * @param onFailed
         * @param onFinal
         * @returns {Object}
         */
        function getActivityList(goods_type_id, key_word, order_key, order_type, from, count, status, activity_type, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_activity&a=activity_list';
            var params = {
                goods_type_id: goods_type_id,
                key_word: key_word,
                order_key: order_key,
                order_type: order_type,
                from: from,
                count: count,
                status: status,
                activity_type: activity_type
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }


        

        /**
         * 获取晒单列表
         *@param {String} goods_id 表示某个商品的晒单，null则是所有晒单
         *@param {String} type     表示按什么排序：'hot':人气；'comment'：评论
         *@param {Number} page     页码
         *@param {Number} my       是否是我的晒单 1：是；0：否（全部）
         */
        function getShare_list(goods_id, type, page,pageCount, my, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_user_show&a=shareList';
            var params = {
                type: type,
                page: page,
                my: my,
                goods_id: goods_id,
                pageCount: pageCount
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        /**
         * 登录
         */
        function login(phonenum, password, onSuccess, onFail, onFinal) {

            var requestUrl = '?c=user&a=login';
            var params = {
                name: phonenum,
                password: password
            };
            return httpDefaultRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }

        /**
         * 注册
         *@param {String} name         表示注册手机号
         *@param {String} password     表示密码
         *@param {Number} sex          性别，传1或0
         *@param {Number} nick         昵称
         *@param {String} mcode        手机验证码
         *@param {String} invite_code  表示邀请码
         *@param {Number} icon         头像
         */
        function register(phoneNum, password, sex, nickname, mcode, inviteCode, icon, onSuccess, onFail, onFinal) {
            var requestUrl = '?c=user&a=reg';
            var params = {
                name: phoneNum,
                password: password,
                sex: sex,
                nick: nickname,
                mcode: mcode,
                invite_code: inviteCode,
                icon: icon
            };
            return httpDefaultRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }

        /**
         * 获取注册短信验证码
         *@param {String} code  表示图形验证码
         */
        function getRegisterSms(mobile, code, onSuccess, onFail, onFinal) {
            var requestUrl = "?c=sms&a=send_reg_mcode";
            var params = {
                mobile: mobile,
                code: code
            };
            return httpDefaultRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }

        //找回密码-发送验证码
        function sendForgotMcode(mobile, code, onSuccess, onFail, onFinal) {
            var requestUrl = "?c=sms&a=send_forgot_mcode";
            var params = {
                mobile: mobile,
                code: code
            };
            return httpDefaultRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }

        //找回密码-校验验证码
        function checkForgotMcode(mobile, mcode, onSuccess, onFail, onFinal) {
            var requestUrl = "?c=user&a=check_forgot_mcode";
            var params = {
                mobile: mobile,
                mcode: mcode
            };
            return httpDefaultRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }


        //找回密码
        function saveForgotPwd(savekey, password, onSuccess, onFail, onFinal) {
            var requestUrl = "?c=user&a=save_forgot_password";
            var params = {
                savekey: savekey,
                password: password
            };
            return httpDefaultRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }


        /**
         * 获取支付订单
         * @param {Array} data array    二维数组，eg:[[activity_id:xxx,num:xxxx]]
         * @returns {*|Object}
         */
        function getOrderInfo(data, onSuccess, onFail, onFinal) {
            var requestUrl = '?c=nc_order&a=order_info';
            var params = {
                data: data
            };
            return httpDefaultRequest.post(requestUrl, params, onSuccess, onFail, onFinal);
        }

        /**
         * 支付
         * @param order_num
         */
        function getNoPay(order_num, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_pay&a=no_pay';
            var params = {
                order_num: order_num
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        /**
         * 支付结果
         * @param order_num
         */
        function getResult(order_num, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_order&a=order_result';
            var params = {
                order_num: order_num
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        /**
         * 充值
         * @param {Number} pay_money int(11)    充值金额
         * @param {Number} pay_type int(11) 支付方式 1:微信；2:支付宝
         */
        function getRecharge(pay_money, pay_type, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_pay&a=recharge';
            var params = {
                pay_money: pay_money,
                pay_type: pay_type
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }




        /**
         * 评论   
         */
        function comment(show_id, text, comment_uid, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_user_show&a=comment';
            var params = {
                show_id: show_id,
                text: text,
                comment_uid: comment_uid
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        /**
         * 获取评论列表
        
         */
        function getCommentList(id, page, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_user_show&a=commentList';
            var params = {
                show_id: id,
                page: page
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        /**
         * TA的中奖记录
         * @param {Number} uid int(11)  如果有，则获取该uid的云购记录，如果没有，则获取当前登录用户的云购记录
         * * @param {Number} logistics_stat int(11) 物流状态，0：未发货，1：未签收，2：已签收，不传时为全部。
         * * @param {Number} status int(11) 不传为全部，0：未读，1：已读
         * @param {Number} from int(11) 表示从第几条开始返回数据，默认:1，表示从第1条开始
         * @param {Number} count int(11)    表示最多拉取几条消息过来，默认:10
         */
        function getWinRecordList(uid, logistics_stat, status, from, count, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_record&a=win_record_list';
            var params = {
                uid: uid,
                from: from,
                count: count,
                logistics_stat: logistics_stat,
                status: status
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }


        /**
         * TA的云购记录
         * @param {Number} uid int(11)  如果有，则获取该uid的云购记录，如果没有，则获取当前登录用户的云购记录
         * @param {Number} from int(11) 表示从第几条开始返回数据，默认:1，表示从第1条开始
         * @param {Number} count int(11)    表示最多拉取几条消息过来，默认:10
         * @param {Number} status int(11)   活动状态，不传为全部。0：还未结束，1：即将揭晓，2：已经揭晓，3：正在进行。
         */
        function getRecordList(uid, from, count, status, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_record&a=record_list';
            var params = {
                uid: uid,
                from: from,
                count: count,
                status: status
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        // TA的中奖记录 查看我的号码
        function getRecordListNum(activity_id, uid, order_num, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_record&a=activity_num';
            var params = {
                activity_id: activity_id,
                uid: uid,
                order_num: order_num
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        
        // 删除收货地址
        function DeleteAddress(address_id, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_user&a=address_delete';
            var params = {
                address_id: address_id
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        

        // 红包详情页
        function grabRedPacket(activity_id, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_red&a=detail';
            var params = {
                activity_id: activity_id
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        //抢红包
        function joinRed(activity_id, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_red&a=joinRed';
            var params = {
                activity_id: activity_id
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        // 
        function joinWait(order_id, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_red&a=joinWait';
            var params = {
                order_id: order_id
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

       

       

        //修改密码
        function changePwd(password_old, password, onSuccess, onFailed, onFinal) {
            var url = '?c=user&a=modify_password';
            var params = {
                password_old: password_old,
                password: password
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }


        /**
         *  
         * @param {Number}activityId  期号
         */
        function getGoodsDetail(activityId, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_goods&a=detail';
            var params = {
                activity_id: activityId
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        //我的消息-通知消息
        function getMyNew(type, from, count, onSuccess, onFailed, onFinal) {
            var url = '?c=msg&a=notify_list';
            var params = {
                type: type,
                from: from,
                count: count
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }
        //我的消息-系统消息
        function getSysList(from, count, onSuccess, onFailed, onFinal) {
            var url = '?c=msg&a=sys_list';
            var params = {
                from: from,
                count: count
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        

        //图文详情
        function getImgDetials(goods_id, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_goods&a=img_detail';
            var params = {
                goods_id: goods_id
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        //计算详情
        function getCountDetials(activity_id, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_goods&a=lucky_num_detail';
            var params = {
                activity_id: activity_id
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        //参与记录
        function getJoinList(activity_id, from, count, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_goods&a=join_list';
            var params = {
                activity_id: activity_id,
                from: from,
                count: count
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        //获取微信扫码支付结果
        function getWechatPayResult(order_num, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_order&a=orderStat';
            var params = {
                order_num: order_num
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        //地址编辑，增加
        function AddressEdit(type, data, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_user&a=address_' + type;
            var params = data;
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }
        //填写收货地址
        function fillInAddress(address_id, activity_id, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_user&a=fill_in_address';
            var params = {
                address_id: address_id,
                activity_id:activity_id
                
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        //不带参数的接口
        function noParams(type, onSuccess, onFailed, onFinal) {
            var urls = {
                getLoginCode: '?c=weixin&a=wxQrcode', //获取微信登录二维码
                getLogin:'?c=user&a=get_login',        //获取用户个人信息
                getInviteInfo:'?c=nc_user&a=inviteInfo', //获取佣金
                getPoint:'?c=nc_user&a=point',           //获取积分
                getInviteRecord:'?c=nc_user&a=inviteUser',   //邀请好友记录
                getInviteData:'?c=nc_invite&a=rebate_info',   //邀请好友
                sign:'?c=nc_user&a=sign',                    //签到
                getAddressList:'?c=nc_user&a=address_list',  // 收货地址列表
                getHotGoods:'?c=nc_activity&a=remen',        //首页获取热门推荐
                getCategoryList:'?c=nc_activity&a=category_list',    //获取分类列表
                getluckyInfo:'?c=nc_activity&a=luckyInfo',           //获取最近中奖纪录
                getBanner:'?c=nc_user&a=pcbanner',                   //获取首页广告
                getMyPointTotal:'?c=nc_user&a=point',                //积分汇总
                balanceTotal:'?c=nc_user&a=money',                   //余额明细
                getShare:'?c=app&a=share',                            //获取分享文案
            };  
            var params = {

            };
            return httpDefaultRequest.post(urls[type], params, onSuccess, onFailed, onFinal);
        }

        //只带页码参数的接口
        function onlyPageParams(type,page,onSuccess, onFailed, onFinal) {
            var urls = {
                inviteMoney: '?c=nc_user&a=inviteMoney',        //获取佣金明细
                getNotice:'?c=app&a=notice',                    //获取公告
                getRedList:'?c=nc_red&a=redList',              //红包列表
                getMyPoint:'?c=nc_user&a=point_detail',         // 我的积分详情
                balanceDetail:'?c=nc_user&a=money_detail'      //余额明细
            };  
            var params = {
                page:page
            };
            return httpDefaultRequest.post(urls[type], params, onSuccess, onFailed, onFinal);
        }


        //获取微信扫码登录结果
        function getWechatLoginResult(sign, id, onSuccess, onFailed, onFinal) {
            var url = '?c=weixin&a=checkQrcode';
            var params = {
                sign2: sign,
                id: id
            };
            return httpDefaultRequest.get(url, params, onSuccess, onFailed, onFinal);
        }

        

        //修改昵称
        function modifyNick(nick, onSuccess, onFailed, onFinal) {
            var url = '?c=user&a=modify_user_info';
            var params = {
                nick:nick
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        //投诉
        function complain(yijian, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_user&a=yijian';
            var params = {
                yijian:yijian
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        //签收
        function confirmReceive(activity_id, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_user&a=check_receive';
            var params = {
                activity_id:activity_id
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        //编辑晒单
        function shareRelease(params, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_user_show&a=do_share';
           
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        //获取晒单详情
        function getShareDetails(show_id, onSuccess, onFailed, onFinal) {
            var url = '?c=nc_user_show&a=shareInfo';
            var params = {
                show_id:show_id
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

        //绑定邀请码
        function bindInviteCode(invite_code, onSuccess, onFailed, onFinal) {
            var url = '?c=user&a=bind_invite_code';
            var params = {
                invite_code:invite_code
            };
            return httpDefaultRequest.post(url, params, onSuccess, onFailed, onFinal);
        }
		//获取分页的按钮
		function getBtnNum(avalon_obj, num){//分页
            	var Index = parseInt(avalon_obj.newPageIndex),arr = [];
            	if (num < 6 ) {
            		for(var i = 1; i <= num; i++){
            			arr[i-1] = i;
            		}
            		avalon_obj.arrCount = arr;
            	} else{
            		if (Index >= 3) {
	            		avalon_obj.arrCount = [Index-2,Index-1,Index,Index+1,Index+2];
            		}else{
            			avalon_obj.arrCount = [1,2,3,4,5];
            		}
            		if (num - Index < 2) {
            			avalon_obj.arrCount = [num-4,num-3,num-2,num-1,num];
            		}
            	}
        }
        return {
        	getBtnNum:getBtnNum,
            setSessid: httpDefaultRequest.setSessid, //设置cookie中的sessId
            getSessid: httpDefaultRequest.getSessid, //获取cookie中的sessId
            getDefaultParams:httpDefaultRequest.getDefaultParams,
            removeSessid: httpDefaultRequest.removeSessid, //移除cookie中的sessId
            getBaseApiUrl: httpDefaultRequest.getBaseApiUrl, //获取接口地址
            getBasePcUrl: httpDefaultRequest.getBasePcUrl, //获取Pc端根地址
            isLogin: httpDefaultRequest.isLogin, //是否已登录
            getHtml: getHtml,
            getActivityList: getActivityList,
            getShare_list: getShare_list, //获取晒单列表
            login: login, //登录
            getRegisterSms: getRegisterSms, //获取注册手机验证码
            register: register, //注册
            sendForgotMcode: sendForgotMcode, //找回密码-发送验证码
            checkForgotMcode:checkForgotMcode, //找回密码-校验验证码
            saveForgotPwd:saveForgotPwd, //找回密码
            getOrderInfo: getOrderInfo, //生成订单
            getNoPay: getNoPay, //支付
            getResult: getResult, //支付结果
            getRecharge: getRecharge, //充值
            comment: comment, //评论
            getCommentList: getCommentList, //获取评论列表
            getWinRecordList: getWinRecordList, //查看TA的中奖记录
            getRecordList: getRecordList, //TA的云购记录
            getRecordListNum: getRecordListNum, //查看TA的中奖记录 查看我的号码
            AddressEdit: AddressEdit, //收货地址
            DeleteAddress: DeleteAddress, //删除收货地址
            fillInAddress: fillInAddress, //使用收货地址
            grabRedPacket: grabRedPacket, //红包详情
            joinRed: joinRed, //抢红包
            joinWait: joinWait, //
            changePwd: changePwd, //修改密码
            getGoodsDetail: getGoodsDetail, //商品详情
            getMyNew: getMyNew, //我的信息-通知信息 
            getSysList: getSysList, //我的信息-系统信息
            getImgDetials: getImgDetials, //图文详情
            getCountDetials: getCountDetials, //计算详情
            getJoinList: getJoinList, //获取参与记录
            getWechatPayResult: getWechatPayResult, //获取微信扫码支付结果
            noParams: noParams, //不带参数的接口
            onlyPageParams:onlyPageParams,      //只带页码参数的接口
            getWechatLoginResult: getWechatLoginResult, //获取微信扫码登录结果
            modifyNick:modifyNick,                       //修改昵称
            complain:complain,                        //投诉
            confirmReceive:confirmReceive,           //签收
            shareRelease:shareRelease,              //编辑晒单
            getShareDetails:getShareDetails,        //获取晒单详情
            bindInviteCode:bindInviteCode
        }






    })
