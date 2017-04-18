/**
 * APP相关接口
 * Created by luliang on 2016/1/5.
 */
define(['app','utils/httpRequest'],function(app){
  app
    .factory('AppModel',['httpRequest',function(httpRequest){
      return{
        getRecordList : getRecordList,
        getRecordList2 : getRecordList2,
        getWinRecordList : getWinRecordList,
        getActivityNum : getActivityNum,
        getOrderInfo : getOrderInfo,
        //getOrderPay : getOrderPay,
        getNoPay : getNoPay,
        getOrderStat : getOrderStat,
        getOrderResult : getOrderResult,
        getRecharge : getRecharge,
        getShareList : getShareList,
        getSearchWord : getSearchWord,
        getSearchHotWord:getSearchHotWord,  //进入搜索页面获取热搜关键字
        getRecordListUrl:getRecordListUrl,
        getRecordListUrl2:getRecordListUrl2,
          getGameOrderUrl:getGameOrderUrl,
        getWinRecordListUrl:getWinRecordListUrl,
        getShareRecordListUrl:getShareRecordListUrl,
        getOderResultUrl : getOderResultUrl,
        getSysListUrl : getSysListUrl,
        getNotifyListUrl : getNotifyListUrl,
        getWechatConfig:getWechatConfig,
        getSysList : getSysList,
        getNotifyList : getNotifyList,
        getShare_list:getShare_list,
        getCommentList:getCommentList,
        comment:comment,
        zan:zan,
        al_app_pay:al_app_pay,
        wx_app_pay:wx_app_pay,
        getShare:getShare,
	getHuixiao:getHuixiao,
        report:report,
        getShareDetails:getShareDetails
      };


      function getHuixiao(onSuccess){
        var url = '?c=app&a=huixiao';
        var params = {}
        return httpRequest.get(url,params,onSuccess,null,null);
      }
      function getShare(onSuccess,onFailed,onFinal){
        var url = '?c=app&a=share';
        var params = {}
        return httpRequest.get(url,params,onSuccess,onFailed,onFinal);
      }
      function report(yijian,onSuccess,onFailed,onFinal){
        var url = '?c=nc_user&a=yijian';
        var params = {
          yijian:yijian
        }
        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
      }

      function al_app_pay(order_num,onSuccess,onFailed,onFinal){
        var url = '?c=nc_pay&a=al_pay2';
        var params = {
          order_num : order_num
        };
        return httpRequest.get(url,params,onSuccess,onFailed,onFinal);
      }

      function wx_app_pay(order_num,onSuccess,onFailed,onFinal){
        var url = '?c=nc_pay&a=wx_pay2';
        var params = {
          order_num : order_num
        };
        return httpRequest.get(url,params,onSuccess,onFailed,onFinal);
      }

      /**
       * TA的云购记录
       * @param {Number} uid int(11)	如果有，则获取该uid的云购记录，如果没有，则获取当前登录用户的云购记录
       * @param {Number} from int(11)	表示从第几条开始返回数据，默认:1，表示从第1条开始
       * @param {Number} count int(11)	表示最多拉取几条消息过来，默认:10
       * @param {Number} status int(11)	活动状态，不传为全部。0：还未结束，1：即将揭晓，2：已经揭晓，3：正在进行。
       * @param onSuccess
       * @param onFailed
       * @param onFinal
       * @returns {Object}
       */
      function getRecordList(uid,from,count,status,onSuccess,onFailed,onFinal){
        var url = '?c=nc_record&a=record_list';
        var params = {
          uid : uid,
          from : from,
          count : count,
          status : status
        };
        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
      }
        function getRecordList2(uid,from,count,status,onSuccess,onFailed,onFinal){
            var url = '?c=nc_record&a=record_list';
            var params = {
                uid : uid,
                from : from,
                count : count,
                status : status
            };
            return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
        }

      /**
       * TA的中奖记录
       * @param {Number} uid int(11)	如果有，则获取该uid的云购记录，如果没有，则获取当前登录用户的云购记录
       * * @param {Number} logistics_stat int(11)	物流状态，1：未发货，2：未签收，3：已签收，不传时为全部。
       * * @param {Number} status int(11)	不传为全部，0：未读，1：已读
       * @param {Number} from int(11)	表示从第几条开始返回数据，默认:1，表示从第1条开始
       * @param {Number} count int(11)	表示最多拉取几条消息过来，默认:10
       * @param onSuccess
       * @param onFailed
       * @param onFinal
       * @returns {Object}
       */
      function getWinRecordList(uid,logistics_stat,status,from,count,onSuccess,onFailed,onFinal){
        var url = getWinRecordListUrl();
        var params = {
          uid : uid,
          from : from,
          count : count,
          logistics_stat : logistics_stat,
          status : status
        };
        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
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
      function getActivityNum(activity_id,uid,onSuccess,onFailed,onFinal){
        return getActivityNumNew(activity_id,uid,null,onSuccess,onFailed,onFinal);
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
      function getActivityNumNew(activity_id,uid,order_num,onSuccess,onFailed,onFinal){
        var url = '?c=nc_record&a=activity_num';
        var params = {
          activity_id : activity_id,
          uid : uid,
          order_num : order_num
        };
        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
      }

      /**
       * 获取支付订单
       * @param {Array} data array	二维数组，eg:[[activity_id:xxx,num:xxxx]]
       * @param onSuccess
       * @param onFailed
       * @param onFinal
       * @returns {*|Object}
       */
      function getOrderInfo(data,onSuccess,onFailed,onFinal){
        var url = '?c=nc_order&a=order_info';
        var params = {

        };
        if(angular.isArray(data)){
          params.data = data;
        }else{
          console.error('输入参数类型必须为数组格式，eg:[[activity_id:xxx,num:xxxx]]');
          return ;
        }
        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
      }

      /**
       * 支付
       * @param {Number} order_num bigint(20)	支付订单号
       * @param {Number} pay_type int(11)	支付方式
       * @param onSuccess
       * @param onFailed
       * @param onFinal
       * @returns {Object}
       */
      function getOrderPay(order_num,pay_type,onSuccess,onFailed,onFinal){
        var url = '?c=nc_pay&a=order_pay';
        var params = {
          order_num : order_num,
          pay_type : pay_type
        };
        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
      }

      /**
       * 支付
       * @param order_num
       * @param onSuccess
       * @param onFailed
       * @param onFinal
       * @returns {*|Object}
       */
      function getNoPay(order_num,onSuccess,onFailed,onFinal){
        var url = '?c=nc_pay&a=no_pay';
        var params = {
          order_num : order_num,
        };
        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
      }

      /**
       * 充值
       * @param {Number} pay_money int(11)	充值金额
       * @param {Number} pay_type int(11)	支付方式
       * @param onSuccess
       * @param onFailed
       * @param onFinal
       * @returns {Object}
       */
      function getRecharge(pay_money,pay_type,onSuccess,onFailed,onFinal){
        var url = '?c=nc_pay&a=recharge';
        var params = {
          pay_money : pay_money,
          pay_type : pay_type,
        };
        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
      }

      /**
       * 订单状态查询
       * @param {String} order_num string(25)	支付订单号
       * @param onSuccess
       * @param onFailed
       * @param onFinal
       * @returns {*|Object}
       */
      function getOrderStat(order_num,onSuccess,onFailed,onFinal){
        var url = '?c=nc_order&a=order_stat';
        var params = {
          order_num : order_num
        };
        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
      }

      /**
       * 订单结果查询
       * @param {String} order_num string(25)	支付订单号
       * @param onSuccess
       * @param onFailed
       * @param onFinal
       * @returns {*|Object}
       */
      function getOrderResult(order_num,onSuccess,onFailed,onFinal){
        var url = getOderResultUrl();
        var params = {
          order_num : order_num
        };
        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
      }

      /**
       * 晒单列表
       * @param {Number}goods_id int(11)	如果没有，获取全部晒单；如果有，则获取该商品的晒单
       * @param {Number}uid int(11)	用户uid
       * @param {Number}from int(11)	表示从第几条开始返回数据，默认:1，表示从第1条开始
       * @param {Number}count int(11)	表示最多拉取几条消息过来，默认:10
       * @param onSuccess
       * @param onFailed
       * @param onFinal
       * @returns {Object}
       */
      function getShareList(goods_id,uid,from,count,onSuccess,onFailed,onFinal){
        var url = '?c=nc_user_show&a=share_list';
        var params = {
          goods_id : goods_id,
          uid : uid,
          from : from,
          count : count
        };

        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
      }

      function getShare_list(type,page,my,view,activity_type,onSuccess,onFailed,onFinal,goods_id,uid){
        var url = '?c=nc_user_show&a=shareList';
        var params = {
          type : type,
          page : page,
          my:my,
          view:view,
          activity_type:activity_type,
          goods_id:goods_id,
            uid : uid
        };
          console.log(params)
        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
      }

      function getShareDetails(show_id,onSuccess,onFailed,onFinal){
        var url = '?c=nc_user_show&a=shareInfo';
        var params = {
          show_id : show_id
        };
        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
      }

      /**
       * 搜索提示
       * @param keywords string(100)	关键字
       * @param onSuccess
       * @param onFailed
       * @param onFinal
       * @returns {*|Object}
       */
      function getSearchWord(keywords,onSuccess,onFailed,onFinal){
        var url = '?c=nc_search&a=word';
        var params = {
          keywords : keywords
        };
        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
      }
      /**
       * 获取热搜关键字
       * @param productType 1是一元购  2是拼团
       * @param onSuccess
       * @param onFailed
       * @param onFinal
       * @returns {*|Object}
       */
      function getSearchHotWord(productType,onSuccess,onFailed,onFinal){
        var url = '?c=nc_search&a=hotword';
        var params = {
          productType : productType
        };
        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
      }

      function getWechatConfig(currentUrl,onSuccess,onFailed){

        var url = '?c=nc_wx_params&a=wx_config';
        var params = {
          url : currentUrl
        };
        return httpRequest.post(url,params,onSuccess,onFailed);

      }

      /**
       * 系统消息
       * @param from
       * @param count
       * @param onSuccess
       * @param onFailed
       * @param onFinal
       * @returns {*|Object}
       */
      function getSysList(from,count,onSuccess,onFailed,onFinal){
        var url = getSysListUrl();
        var params = {
          from : from,
          count : count
        };
        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
      }

      /**
       * 通知消息
       * @param type 类型
       * @param from
       * @param count
       * @param onSuccess
       * @param onFailed
       * @param onFinal
       * @returns {*|Object}
       */
      function getNotifyList(type,from,count,onSuccess,onFailed,onFinal){
        var url = getNotifyListUrl();
        var params = {
          type : type,
          from : from,
          count : count
        };
        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
      }
      /**
       * 获取评论列表
      
       */
      function getCommentList(id,page,onSuccess,onFailed,onFinal){
        var url = '?c=nc_user_show&a=commentList';
        var params = {
          show_id : id,
          page : page,
        };
        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
      }

      /**
       * 评论
      
       */
      function comment(show_id,text,comment_uid,onSuccess,onFailed,onFinal){
        var url = '?c=nc_user_show&a=comment';
        var params = {
          show_id : show_id,
          text : text,
          comment_uid:comment_uid
        };
        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
      }

      /**
       * 点赞
      
       */
      function zan(show_id,onSuccess,onFailed,onFinal){
        var url = '?c=nc_user_show&a=zan';
        var params = {
          show_id : show_id
        
        };
        return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
      }



      function getSysListUrl(){
        return '?c=msg&a=sys_list';
      }

      function getNotifyListUrl(){
        return '?c=msg&a=notify_list';
      }


      function getRecordListUrl(){
       var  url='?c=nc_record&a=record_list';
        return url

      }
        //拼图订单获取订单数据
        function getRecordListUrl2(){
            var  url='?c=team&a=order';
            return url

        }

        //游戏记录数据
        function getGameOrderUrl(){
            var  url='?c=nc_games&a=game_order';
            return url

        }



      function getWinRecordListUrl(){
        var url = '?c=nc_record&a=win_record_list';
        return url
      }
        function getShareRecordListUrl(){
        var url = '?c=nc_user_show&a=shareList';
        return url
      }


      function getOderResultUrl(){
        return '?c=nc_order&a=order_result';
      }
    }]);
});
