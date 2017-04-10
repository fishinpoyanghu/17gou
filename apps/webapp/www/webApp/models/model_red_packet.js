/**
 * 收货地址相关接口
 *
 */
define(['app','utils/httpRequest'],function(app){

  app.factory('redPacketModel',['httpRequest',function(httpRequest){



    /**
     * 获取红包列表
     * input:
     * @param {Number} status int(11)	不传为全部；1：可使用；2：已经使用或者过期
     * @param from 表示从第几条开始返回数据，默认:1，表示从第1条开始
     * @param count 表示最多拉取几条数据过来，默认:10
     * @param onSuccess
     * @param onFail
     * @param onFinal
     *
     * return:
     * code： 返回值：0、1、5
     * msg: 返回消息说明
     * data: [{rebate_id，rebate_uid, rebate_unick, pay_money,pay_time, rebate_money}...]
     */
    function getRedPacketList(status,from,count,onSuccess,onFail,onFinal){
      var requestUrl = getUrlRedPacket();
      var params = {
        status:status,
        from: from ,
        count: count
      };
      httpRequest.post(requestUrl, params, onSuccess,onFail,onFinal);
    }

    function getUrlRedPacket(){
      return '?c=nc_packet&a=packet_list';
    }

    function getRedPacketList1(page,onSuccess,onFail,onFinal){
      var requestUrl = '?c=nc_red&a=redList';
      var params = {
        page:page
      };
      httpRequest.post(requestUrl, params, onSuccess,onFail,onFinal);
    }

    function getRedPacketDetail(activity_id,onSuccess,onFail,onFinal){
      var requestUrl = '?c=nc_red&a=detail';
      var params = {
        activity_id:activity_id
      };
      httpRequest.post(requestUrl, params, onSuccess,onFail,onFinal);
    }

    function joinRed(activity_id,onSuccess,onFail,onFinal){
      var requestUrl = '?c=nc_red&a=joinRed';
      var params = {
        activity_id:activity_id
      };
      httpRequest.post(requestUrl, params, onSuccess,onFail,onFinal);
    }

    function joinWait(order_id,onSuccess,onFail,onFinal){
      var requestUrl = '?c=nc_red&a=joinWait';
      var params = {
        order_id:order_id
      };
      httpRequest.post(requestUrl, params, onSuccess,onFail,onFinal);
    }

    function getJoinRecord(activity_id,onSuccess,onFail,onFinal){
      var requestUrl = '?c=nc_red&a=joinList';
      var params = {
        activity_id:activity_id
      };
      httpRequest.post(requestUrl, params, onSuccess,onFail,onFinal);
    }

    function getRedPacketLastPublished(red_id,page,onSuccess,onFail,onFinal){
      var requestUrl = '?c=nc_red&a=joinHistory';
      var params = {
        red_id:red_id,
        page:page
      };
      httpRequest.post(requestUrl, params, onSuccess,onFail,onFinal);
    }

    function getJoinResult(activity_id,onSuccess,onFail,onFinal){
      var requestUrl = '?c=nc_red&a=joinResult';
      var params = {
        activity_id:activity_id
      };
      httpRequest.post(requestUrl, params, onSuccess,onFail,onFinal);
    }

    return {
      getRedPacketList : getRedPacketList, // 返利详情
      getUrlRedPacket : getUrlRedPacket, // 返利详情
      getRedPacketList1:getRedPacketList1,
      getRedPacketDetail:getRedPacketDetail,
      joinRed:joinRed,
      joinWait:joinWait,
      getJoinRecord:getJoinRecord,
      getRedPacketLastPublished:getRedPacketLastPublished,
      getJoinResult:getJoinResult
    }

  }]);


});
