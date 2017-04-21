/**
 * Created by suiman on 16/1/5.
 */

define(
  [
    'app',
    'utils/httpRequest'
  ],
  function (app) {
    app.factory('GoodsModel', goodsModel);
    goodsModel.$inject = ['httpRequest'];

    function goodsModel(httpRequest) {
        return {
          getGoodsDetail: getGoodsDetail,       //宝贝详情
          getGoodsImgDetail:getGoodsImgDetail,  //图文详情
          getJoinRecordList:getJoinRecordList,  //参与记录
          getHistoryList: getHistoryList,       //往期揭晓
          getShowOrderList:getShowOrderList,    //晒单分享
          editShowOrderList:editShowOrderList,  //发表晒单
          publishImg:publishImg,                //上传图片
          getCountDetail: getCountDetail,       //计算详情
          checkReceive:checkReceive,            //签收

          getHistoryUrl : getHistoryUrl,        //获取往期揭晓URL
          getJoinRecordUrl: getJoinRecordUrl,  //获取参与记录URL
          getShowOrderUrl: getShowOrderUrl,      //获取晒单分享URL
            getExpressQuery: getExpressQuery,      //获取快递路径
            getSysnotify: getSysnotify,      //获取宝贝详情的提示信息数据
//        baituan_createTuan: baituan_createTuan,      //申请团
//        baituan_getDetail_info: baituan_getDetail_info,      //获取团的详情
//        baituan_homepage:baituan_homepage,				//百团首页获取数据的接口
//        baituan_detail:baituan_detail,				//百团详情页面
//        baituan_getGoodsDetail_info:baituan_getGoodsDetail_info,		//进入商品详情页面调用的方法
//        baituan_jointeam:baituan_jointeam,					//加入别人的团
//        baituan_getGoodsImgDetail:baituan_getGoodsImgDetail,   // 获取商品的详情介绍
//        baituan_myTeam_info:baituan_myTeam_info,				//获取个人中心我的团的数据
          checkReceiveChoujiang:checkReceiveChoujiang
        };
		
//		//百团大战的数据接口
////		百团首页的请求数据
//		function baituan_homepage(goodsId,onSuccess,onFailed,onFinal){
//			var url = '?c=team&a=teamgoodlist';
//	        var params = {
////	            goods_id: goodsId
//	        };
//	        return httpRequest.post(url, params, onSuccess, onFailed,onFinal);
//		}
////		申请新团
//		function baituan_createTuan(goodsId,addressId,onSuccess,onFailed){
//			var url = '?c=team&a=createteam';
//	        var params = {
//	            goods_id: goodsId,
//	            address_id: addressId
//	        };
//	        return httpRequest.post(url, params, onSuccess, onFailed);
//		}
//		//		进入商品详情页面调用的方法
//		function baituan_getGoodsDetail_info(good_id,onSuccess,onFailed){
//			var url = '?c=team&a=baituandetail';
//	        var params = {
//	            goods_id: good_id
//	        };
//	        return httpRequest.post(url, params, onSuccess, onFailed);
//		}
//		//		获取商品的详情介绍
//		function baituan_getGoodsImgDetail(good_id,onSuccess,onFailed){
//			var url = '?c=team&a=imgDetail';
//	        var params = {
//	            goods_id: good_id
//	        };
//	        return httpRequest.post(url, params, onSuccess, onFailed);
//		}
//		
//		//		根据团的team获取数据
//		function baituan_getDetail_info(team,onSuccess,onFailed,onFinal){
//			var url = '?c=team&a=teamdetail';
//	        var params = {
//	            team: team
//	        };
//	        return httpRequest.post(url, params, onSuccess, onFailed,onFinal);
//		}
//
////		参加别人团:weiwanc
//		function baituan_jointeam(timeId,onSuccess,onFailed){
//			var url = '?c=team&a=jointeam';
////			需要两个参数,一个是要参加团的id和地址id
//	        var params = {
//	            time_id: timeId,
//	        };
//	        return httpRequest.post(url, params, onSuccess, onFailed);
//		}
////		获取我的团的数据wei
//		function baituan_myTeam_info(param,onSuccess,onFailed,onFinal){
//			var url = '?c=team&a=myteam';
//	        var params = {
//	            
//	        };
//	        return httpRequest.post(url, params, onSuccess, onFailed,onFinal);
//		}
////		百团大战的详情页
//		function baituan_detail(goodsId,onSuccess,onFailed){
//			var url = '?c=team&a=goodsdetail';
//	        var params = {
//	            goods_id: goodsId,
//	        };
//	        return httpRequest.post(url, params, onSuccess, onFailed);
//		}
//		
		
      /**
       *
       * @param {Number}activityId  期号
       * @param onSuccess
       * @param onFailed
       * @param onFinal
       * @returns {*|Object}
       */
        function getGoodsDetail(activityId, onSuccess, onFailed, onFinal) {
          var url = '?c=nc_goods&a=detail';
          var params = {
            activity_id: activityId
          };
          return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
        }

      /**
       *
       * @param {Number}goodsId 商品id
       * @param onSuccess
       * @param onFailed
       * @param onFinal
       * @returns {*|Object}
       */
        function getGoodsImgDetail(goodsId, onSuccess, onFailed, onFinal) {
          var url = '?c=nc_goods&a=img_detail';
          var params = {
            goods_id: goodsId
          };
          return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
        }



      /**
       *
       * @param {Number}activityId  期号
       * @param {Number}from        表示从第几条开始返回数据，默认:1，表示从第1条开始
       * @param {Number}count       表示最多拉取几条消息过来，默认:10
       * @param onSuccess
       * @param onFailed
       * @returns {*|Object}
       */
        function getJoinRecordList(activityId, from, count, onSuccess, onFailed) {
          //var url = '?c=nc_goods&a=join_record_list';
          var url = '?c=nc_goods&a=join_list&';
          var params = {
            activity_id: activityId,
            from:from,
            count:count
          };
          return httpRequest.post(url, params, onSuccess, onFailed);
        }

      function getJoinRecordUrl() {
        return '?c=nc_goods&a=join_list&';
      }

      /**
       *
       * @param {Number}goodsId 商品id
       * @param {Number}from    表示从第几条开始返回数据，默认:1，表示从第1条开始
       * @param {Number}count   表示最多拉取几条消息过来，默认:10
       * @param onSuccess
       * @param onFailed
       * @param onFinal
       * @returns {*|Object}
       */
        function getHistoryList(goodsId, from, count, onSuccess, onFailed,onFinal) {
          var url = '?c=nc_goods&a=history_list';
          var params = {
            goods_id: goodsId,
            from:from,
            count:count
          };

          return httpRequest.post(url, params, onSuccess, onFailed,onFinal);
        }

        function getHistoryUrl(){
          return '?c=nc_goods&a=history_list';
        }


       /**
       *
       * @param {Number}goodsId 商品id
       * @param {Number}uid     过滤
       * @param {Number}from    表示从第几条开始返回数据，默认:1，表示从第1条开始
       * @param {Number}count   表示最多拉取几条消息过来，默认:10
       * @param onSuccess
       * @param onFailed
       * @returns {*|Object}
       */
        function getShowOrderList(goodsId,uid, from, count, onSuccess, onFailed) {
          var url = '?c=nc_user_show&a=share_list';
          var params = {
            goods_id: goodsId,
            uid:uid,
            from:from,
            count:count
          };
          return httpRequest.post(url, params, onSuccess, onFailed,null);
        }

      function getShowOrderUrl() {
        return '?c=nc_user_show&a=share_list';
      }



       function editShowOrderList(activityId,show_title,show_desc,img,onSuccess,onFailed){
         var url='?c=nc_user_show&a=do_share';
         var params={
           activity_id:activityId,
           show_title:show_title,
           show_desc:show_desc,
           img:img

         };

         return httpRequest.post(url, params, onSuccess, onFailed);

       }

      function publishImg(file,onUpLoadSuccess,onUpLoadFail){

        var params={
          file:file
        };
        httpRequest.post('?c=bbs&a=upload_img_base64&',params,onUpLoadSuccess,
          onUpLoadFail);

      }

      function getCountDetail(activityId, onSuccess, onFailed, onFinal) {
        var url = '?c=nc_goods&a=lucky_num_detail';
        var params = {
          activity_id: activityId
        };
        return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
      }

      function checkReceive(activityId, onSuccess, onFailed, onFinal){
        var url = '?c=nc_user&a=check_receive';
        var params = {
          activity_id: activityId
        };
        return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
      }

      function checkReceiveChoujiang(id, onSuccess, onFailed, onFinal){
        var url = '?c=nc_user&a=check_receive2';
        var params = {
          id: id
        };
        return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
      }


        //获取快递路径
        function getExpressQuery(logistics_id,type, onSuccess, onFailed) {
            var url = '?c=nc_record&a=logistic';
            var params = {
                logistics_id: logistics_id,
                type: type
            };
            return httpRequest.post(url, params, onSuccess, onFailed);
        }

        /**
         *宝贝详情的提示信息
         * @param {Number}goodsId 商品id
         * @param onSuccess
         * @param onFailed
         * @param onFinal
         * @returns {*|Object}
         */
        function getSysnotify(type, onSuccess, onFailed, onFinal) {
            var url = '?c=msg&a=getsysnotify';
            var params = {
                type: type
            };
            return httpRequest.post(url, params, onSuccess, onFailed, onFinal);
        }



    }
  });
