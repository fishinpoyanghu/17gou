/**
 * 用户接口数据请求模块
 * Created by luliang on 2015/11/26.
 */
define(['app', 'utils/httpRequest', 'html/common/geturl_service'], function(app) {

	app.factory('PintuanModel', ['httpRequest', 'MyUrl', function(httpRequest, MyUrl) {

		//百团大战的数据接口
		//		百团首页的请求数据
		function baituan_homepage(goodsId, onSuccess, onFailed, onFinal) {
			var url = '?c=team&a=teamgoodlist';
			var params = {
				//	            goods_id: goodsId
			};
			return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
		}
		//		申请新团
		function baituan_createTuan(goodsId, addressId, onSuccess, onFailed) {
			var url = '?c=team&a=createteam';
			var params = {
				goods_id: goodsId,
				address_id: addressId
			};
			return httpRequest.post(url, params, onSuccess, onFailed);
		}
		//		进入商品详情页面调用的方法
		function baituan_getGoodsDetail_info(good_id, onSuccess, onFailed) {
			var url = '?c=team&a=baituandetail';
			var params = {
				goods_id: good_id
			};
			return httpRequest.post(url, params, onSuccess, onFailed);
		}
		//		获取商品的详情介绍
		function baituan_getGoodsImgDetail(good_id, onSuccess, onFailed) {
			var url = '?c=team&a=imgDetail';
			var params = {
				goods_id: good_id
			};
			return httpRequest.post(url, params, onSuccess, onFailed);
		}

		//		根据团的team获取数据
		function baituan_getDetail_info(team, onSuccess, onFailed, onFinal) {
			var url = '?c=team&a=teamdetail';
			var params = {
				team: team
			};
			return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
		}

		//		参加别人团:weiwanc
		function baituan_jointeam(timeId, onSuccess, onFailed) {
			var url = '?c=team&a=jointeam';
			//			需要两个参数,一个是要参加团的id和地址id
			var params = {
				time_id: timeId,
			};
			return httpRequest.post(url, params, onSuccess, onFailed);
		}
		//		获取我的团的数据wei
		function baituan_myTeam_info(param, onSuccess, onFailed, onFinal) {
			var url = '?c=team&a=myteam';
			var params = {

			};
			return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
		}
		//		百团大战的详情页
		function baituan_detail(goodsId, onSuccess, onFailed) {
			var url = '?c=team&a=goodsdetail';
			var params = {
				goods_id: goodsId,
			};
			return httpRequest.post(url, params, onSuccess, onFailed);
		}
		/**
		 *   首页获取最新揭晓
		 */
		function getbaituanNewPublish(onSuccess, onFailed, onFinal) {
			var url = '?c=team&a=activity_list&_=' + (+new Date());
			var params = {
				goods_type_id: null,
				key_word: null,
				order_key: null,
				order_type: null,
				from: null,
				count: null,
				status: 3,
				activity_type: null,
				from: 1,
				count: 4
			};
			return httpRequest.post(url, params, onSuccess, onFailed, null);
		}
		/**
		 *   获取团的计算详情
		 */
		function baituan_getCountDetail_info(activityId, onSuccess, onFailed, onFinal) {
			var url = '?c=team&a=lucky_num_detail';
			var params = {
				activity_id: activityId
			};
			return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
		}
		/**
		 * 获取云购号--旧版接口 不建议继续使用
		 * @param {Number} [activity_id] int(11)	活动id，期号
		 * @param {Number} [uid] int(11)	用户id，如果不传，获取当前用户
		 * @param onSuccess
		 * @param onFailed
		 * @param onFinal
		 * @returns {*|Object}
		 */
		function getActivityPinTuanNum(activity_id, uid, onSuccess, onFailed, onFinal) {
			return getActivityNumNew(activity_id, uid, null, onSuccess, onFailed, onFinal);
		}

		/**
		 * 获取云购号--新版接口
		 * @param {Number} [activity_id] int(11)	活动id，期号
		 * @param {Number} [uid] int(11)	用户id，如果不传，获取当前用户
		 * @param {String} [order_num] string(25)	订单号
		 * @param onSuccess
		 * @param onFailed
		 * @param onFinal
		 * @returns {*|Object}
		 */
		function getActivityNumNew(activity_id, uid, order_num, onSuccess, onFailed, onFinal) {
			var url = '?c=nc_record&a=activity_num';
			var params = {
				activity_id: activity_id,
				uid: uid,
				team: activity_id,
				order_num: order_num
			};
			return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
		}
		//	      确认收货
		function checkTeamReceive(activityId, onSuccess, onFailed, onFinal) {
			var url = '?c=nc_user&a=check_team_receive';
			var params = {
				activity_id: activityId
			};
			return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
		}

		//拼团的数据接口，基本一样，只是加个type=1，
		//		拼团首页的请求数据
        function pintuan_homepage(goods_type_id,key_word,order_key,order_type,from,count,status,activity_type,onSuccess,onFailed,onFinal) {
            var url = '?c=team&a=goodslist';
            if(angular.isString(goods_type_id)){
                goods_type_id = parseInt(goods_type_id);
            }
            var params = {
                goods_type_id : goods_type_id,
                key_word : key_word,
                order_key : order_key,
                order_type : order_type,
                from : from,
                count : count,
                status : status,
                activity_type : activity_type
            };
            return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
        }

        //拼团分类列表页的数据接口
        function getPintuanList(goods_type_id,activity_type,onSuccess,onFailed,onFinal) {
            var url = '?c=team&a=categoryList';
            if(angular.isString(goods_type_id)){
                goods_type_id = parseInt(goods_type_id);
            }
            var params = {
            	type:1,
                goods_type_id : goods_type_id,
                activity_type : activity_type
            };
            return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
        }

		//		申请新团
		function pintuan_createTuan(goodsId, addressId, onSuccess, onFailed) {
			var url = '?c=team&a=createteam';
			var params = {
				goods_id: goodsId,
				address_id: addressId
			};
			return httpRequest.post(url, params, onSuccess, onFailed);
		}
		//		进入商品详情页面调用的方法
		function pintuan_getGoodsDetail_info(good_id, onSuccess, onFailed,onFinal) {
			var url = '?c=team&a=baituandetail';
			var params = {
				goods_id: good_id
			};
			return httpRequest.post(url, params, onSuccess, onFailed,onFinal);
		}
		//		获取商品的详情介绍
		function pintuan_getGoodsImgDetail(good_id, onSuccess, onFailed) {
			var url = '?c=team&a=imgDetail';
			var params = {
				goods_id: good_id
			};
			return httpRequest.post(url, params, onSuccess, onFailed);
		}

		//		根据团的team获取数据
		function pintuan_getDetail_info(team, onSuccess, onFailed, onFinal) {
			var url = '?c=team&a=teamdetail';
			var params = {
				team: team
			};
			return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
		}

		//		参加别人团:weiwanc
		function pintuan_jointeam(timeId, onSuccess, onFailed) {
			var url = '?c=team&a=jointeam';
			//			需要两个参数,一个是要参加团的id和地址id
			var params = {
				time_id: timeId
			};
			return httpRequest.post(url, params, onSuccess, onFailed);
		}
		//		获取我的团的数据wei
		function pintuan_myTeam_info(param, onSuccess, onFailed, onFinal) {
			var url = '?c=team&a=myteam';
			var params = {

			};
			return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
		}
		//		百团大战的详情页
		function pintuan_detail(goodsId, onSuccess, onFailed) {
			var url = '?c=team&a=goodsdetail';
			var params = {
				goods_id: goodsId
			};
			return httpRequest.post(url, params, onSuccess, onFailed);
		}
		/**
		 *   首页获取最新揭晓
		 */
		function getpintuanNewPublish(onSuccess, onFailed, onFinal) {
			var url = '?c=team&a=activity_list&_=' + (+new Date());
			var params = {
				goods_type_id: null,
				key_word: null,
				order_key: null,
				order_type: null,
				status: 3,
				activity_type: null,
				from: 1,
				count: 4
			};
			return httpRequest.post(url, params, onSuccess, onFailed, null);
		}
		/**
		 *   获取团的计算详情
		 */
		function pintuan_getCountDetail_info(activityId, onSuccess, onFailed, onFinal) {
			var url = '?c=team&a=lucky_num_detail';
			var params = {
				activity_id: activityId
			};
			return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
		}

		//	 新拼团 确认收货,取消订单
		function updateorderstatus(order_num,status,msg,onSuccess,onFailed, onFinal) {
			var url = '?c=team&a=orderstatus';
			var params = {
				order_num: order_num,
				status: status,
				msg: msg
			};
			return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
		}
		//	      获取收藏列表
		function getCollectList(onSuccess, onFailed, onFinal) {  
			var url = '?c=team&a=collectlist';
			var params = {
			};
			return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
		}
		//	      添加收藏
		function addCollect(goodsId, onSuccess, onFailed, onFinal) {
			var url = '?c=team&a=addcollect';
			var params = {
				goods_id: goodsId
			};
			return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
		}
		//	      取消收藏
		function removeCollect(goods_id, onSuccess, onFailed, onFinal) {
			var url = '?c=team&a=canclecollect';
			var params = {
				goods_id: goods_id
			};
			return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
		}
		//	      上传历史记录
		function updataHistory(goods_id, onSuccess, onFailed, onFinal) {
			var url = '?c=nc_user_show&a=getuserfollow';
			var params = {
				goods_id: goods_id
			};
			return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
		}
		////获取拼团订单信息付款
		function getpayorder(order_num, onSuccess, onFailed, onFinal) {
			var url = '?c=team&a=getpayorder';
			var params = {
				order_num: order_num
			};
			return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
		}
		return {
			updataHistory:updataHistory,		//上传拼团的商品浏览记录
			getCollectList:getCollectList,		//获取收藏列表
			addCollect:addCollect,				//添加收藏
			removeCollect:removeCollect,		//取消收藏
			updateorderstatus:updateorderstatus, //新拼团 确认收货,取消订单
			checkTeamReceive: checkTeamReceive, //确认收货
			getActivityPinTuanNum: getActivityPinTuanNum, //获取云购号
			baituan_getCountDetail_info: baituan_getCountDetail_info, //获取团的计算详情
			getbaituanNewPublish: getbaituanNewPublish, //获取百团大战的即将揭晓
			baituan_createTuan: baituan_createTuan, //申请团
			baituan_getDetail_info: baituan_getDetail_info, //获取团的详情
			baituan_homepage: baituan_homepage, //百团首页获取数据的接口
			baituan_detail: baituan_detail, //百团详情页面
			baituan_getGoodsDetail_info: baituan_getGoodsDetail_info, //进入商品详情页面调用的方法
			baituan_jointeam: baituan_jointeam, //加入别人的团
			baituan_getGoodsImgDetail: baituan_getGoodsImgDetail, // 获取商品的详情介绍
			baituan_myTeam_info: baituan_myTeam_info, //获取个人中心我的团的数据

			//下面是拼团的内容
			pintuan_getCountDetail_info: pintuan_getCountDetail_info, //获取团的计算详情
			getpintuanNewPublish: getpintuanNewPublish, //获取百团大战的即将揭晓
			pintuan_createTuan: pintuan_createTuan, //申请团
			pintuan_getDetail_info: pintuan_getDetail_info, //获取团的详情
			pintuan_homepage: pintuan_homepage, //拼团首页获取数据的接口
            getPintuanList: getPintuanList, //获取拼团分类列表数据的接口
			pintuan_detail: pintuan_detail, //百团详情页面
			pintuan_getGoodsDetail_info: pintuan_getGoodsDetail_info, //进入商品详情页面调用的方法
			pintuan_jointeam: pintuan_jointeam, //加入别人的团
			pintuan_getGoodsImgDetail: pintuan_getGoodsImgDetail, // 获取商品的详情介绍
			pintuan_myTeam_info: pintuan_myTeam_info, //获取个人中心我的团的数据
			getpayorder:getpayorder,//获取拼团订单信息付款
		}

	}]);

});