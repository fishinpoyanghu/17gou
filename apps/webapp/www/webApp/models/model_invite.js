/**
 * 收货地址相关接口
 *
 */
define(['app','utils/httpRequest'],function(app){

  app.factory('inviteModel',['httpRequest',function(httpRequest){

    /**
     * 获取邀请返利信息
     * input: null
     *
     * return
     * code： 返回值：0、1、5、201、202
     * msg: 返回消息说明
     * data: [{rebate_money，invite_code}]
     */
    function getRebateInfo(onSuccess,onFail){
      var requestUrl = '?c=nc_invite&a=rebate_info';
      var params = {};
      httpRequest.post(requestUrl, params, onSuccess,onFail,null);
    }

    /**
     * 返利详情
     * input:
     * @param from 表示从第几条开始返回数据，默认:1，表示从第1条开始
     * @param count 表示最多拉取几条数据过来，默认:10
     * @param onSuccess
     * @param onFail
     *
     * return:
     * code： 返回值：0、1、5
     * msg: 返回消息说明
     * data: [{rebate_id，rebate_uid, rebate_unick, pay_money,pay_time, rebate_money}...]
     */
    function getRebateList(from,count,onSuccess,onFail){
      var requestUrl = '?c=nc_invite&a=rebate_list';
      var params = {
        from: from ,
        count: count
      };
      httpRequest.post(requestUrl, params, onSuccess,onFail,null);
    }

    return {
      getRebateInfo : getRebateInfo, // 获取邀请返利信息
      getRebateList : getRebateList // 返利详情
    }

  }]);


});
