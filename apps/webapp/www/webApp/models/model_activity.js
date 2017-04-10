/**
 * Created by suiman on 16/1/4.
 */

define(['app','utils/httpRequest'], function (app) {
  app.factory('ActivityModel', ['httpRequest',function (httpRequest) {
    return {
      getCategoryList:getCategoryList,
      getActivityList: getActivityList,
      getActivityInfo : getActivityInfo,
      getActivityList_1:getActivityList_1,
      getluckyInfo:getluckyInfo,
      getBanner:getBanner,
      getHomeNewPublish:getHomeNewPublish,
      getHotGoods:getHotGoods
    }

     /**
      *   首页获取最新揭晓
      */
      function getHomeNewPublish(onSuccess, onFailed, onFinal) {
        var url = '?c=nc_activity&a=activity_list&_=' + (+new Date());
        var params = {
          goods_type_id : null,
          key_word : null,
          order_key : null,
          order_type : null,
          from : null,
          count : null,
          status : 3,
          activity_type : null,
          from:1,
          count:4
        };
        return httpRequest.post(url,params, onSuccess, onFailed, null);
      }
      function getHotGoods(onSuccess, onFailed, onFinal) {
        var url = '?c=nc_activity&a=remen';
        var params = {
        
        };
        return httpRequest.post(url,params, onSuccess, onFailed, onFinal);
      }
    //function getActivityList() {
    //  return [dataInStatus1, dataInStatus1, dataInStatus2, dataInStatus2, dataInStatus3, dataInStatus3, dataInStatus3]
    //}

    /**
     * 获取分类列表
     * @param onSuccess
     * @param onFailed
     * @param onFinal
     * @returns {Object}
     */
    function getCategoryList(onSuccess,onFailed,onFinal){
      var url = '?c=nc_activity&a=category_list';
      var params = {
        
      };
      return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
    }

    /**
     * 获取商品活动列表
     * @param {Number} goods_type_id int(11)	类别id
     * @param {String} key_word string(60)	关键字
     * @param {String} order_key string(60)	按order_key排序
     * @param {String} order_type string(60)	排序类型，asc和desc。默认asc
     * @param {Number} from int(11)	表示从第几条开始返回数据，默认:1，表示从第1条开始
     * @param {Number} count int(11)	表示最多拉取几条消息过来，默认:10
     * @param {Number} status int(11)	活动状态，默认为0。0：还未结束，1：即将揭晓，2：已经揭晓
     * @param {Number} activity_type int(11)	是否是十元专区，1：否；2：是
     * @param {Function} [onSuccess]
     * @param onFailed
     * @param onFinal
     * @returns {Object}
     */
    function getActivityList(goods_type_id,key_word,order_key,order_type,from,count,status,activity_type,onSuccess,onFailed,onFinal) {
      var url = '?c=nc_activity&a=activity_list';
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
        activity_type : activity_type,
      };
      return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
    }

    /**
     * 获取活动信息
     * @param {Number} activity_id int(11)	活动id
     * @param onSuccess
     * @param onFailed
     * @param onFinal
     * @returns {*|Object}
     */
    function getActivityInfo(activity_id,onSuccess,onFailed,onFinal){
      var url = '?c=nc_activity&a=activity_info';
      var params = {
        activity_id : activity_id
      };
      return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
    }

    /**
     * 获取活动列表
     * @param  order_key weight:人气，time:最新，ing：进度，num:总须人数
     * @param  order_type desc:倒序，asc:顺序
     * @param onSuccess
     * @param onFailed
     * @param onFinal
     * @returns {*|Object}
     */
    function getActivityList_1(order_key,order_type,goods_type_id,from,count,onSuccess,onFailed,onFinal){
      var url = '?c=nc_activity&a=activity_list';
      var params = {
        order_key : order_key,
        order_type:order_type,
        from:from,
        count:count,
        goods_type_id:goods_type_id
      };
      return httpRequest.post(url,params,onSuccess,onFailed,onFinal);
    }

    /**
     * 获取最近中奖纪录
     */
    function getluckyInfo(onSuccess,onFailed,onFinal){
      var url = '?c=nc_activity&a=luckyInfo';
      var params = {
      };
      return httpRequest.get(url,params,onSuccess,onFailed,onFinal);
    }
    /**
     * 获取首页广告
     *
     * 获取活动信息
     * @param type   1.一元购的轮播图   2.拼团的轮播图
     * @param onSuccess
     * @param onFailed
     * @param onFinal
     * @returns {*|Object}
     */

    function getBanner(type,onSuccess,onFailed,onFinal){
      var url = '?c=nc_user&a=banner';
      var params = {
          type:type
      };
      return httpRequest.get(url,params,onSuccess,onFailed,onFinal);
    }

  }]);

})

