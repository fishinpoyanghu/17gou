/**
 * 收货地址相关接口
 *
 */
define(['app','utils/httpRequest'],function(app){

  app.factory('addressModel',['httpRequest',function(httpRequest){

    /**
     * 收货地址列表
     * input: null
     *
     * return
     * code： 返回值：0、1、5、201、202
     * msg: 返回消息说明
     * data: [{address_id,name,mobile,province,city,area,detail,is_default},...]
     */
    function getAddressList(onSuccess,onFail,onFinal){
      var requestUrl = '?c=nc_user&a=address_list';
      var params = {};
      httpRequest.post(requestUrl, params, onSuccess,onFail,onFinal);
    }

    /**
     * 新增收货地址
     * input:
     * params: {name,mobile,province,city,area,detail,is_default}
     *
     * return :
     * code:  返回值：0、1、5、201、202
     * msg: 返回消息说明
     */
    function addAddress(params,onSuccess,onFail){
      var requestUrl = '?c=nc_user&a=address_add';
      httpRequest.post(requestUrl, params, onSuccess,onFail,null);
    }

    /**
     * 更新收货地址
     * input:
     * params: {address_id,name,mobile,province,city,area,detail,is_default}
     *
     * return:
     * code:  返回值：0、1、5、201、202
     * msg：返回消息说明
     */
    function updateAddress(params,onSuccess,onFail){
      var requestUrl = '?c=nc_user&a=address_update';
      httpRequest.post(requestUrl, params, onSuccess,onFail,null);
    }

    /**
     * 删除收货地址
     * input:
     * address_id: 地址id
     *
     * return:
     * code:  返回值：0、1、5、201、202
     * msg：返回消息说明
     */
    function deleteAddress(addressId,onSuccess,onFail){
      var requestUrl = '?c=nc_user&a=address_delete';
      var params = {
        address_id : addressId
      };
      httpRequest.post(requestUrl, params, onSuccess,onFail,null);
    }


    /**
     * 确认收货地址
     * input:
     * activityId：期号
     * address_id: 地址id
     *
     * return:
     * code:  返回值：0、1、2、5、6、9
     * msg：返回消息说明
     */
    function confirmAddress(activityId,addressId,onSuccess,onFail){
      var requestUrl = '?c=nc_user&a=fill_in_address';
      var params = {
        address_id : addressId ,
        activity_id: activityId
      };
      httpRequest.post(requestUrl, params, onSuccess,onFail,null);
    }

    function confirmPinTuanAddress(activityId,addressId,onSuccess,onFail){
      var requestUrl = '?c=nc_user&a=team_fill_in_address';
      var params = {
        address_id : addressId ,
        activity_id: activityId
      };
      httpRequest.post(requestUrl, params, onSuccess,onFail,null);
    }
      /*拼团修改地址*/
      function confirmPinTuanAddress2(order_num,addressId,onSuccess,onFail){
          var requestUrl = '?c=team&a=editaddress';
          var params = {
              address_id : addressId ,
              order_num: order_num
          };
          httpRequest.post(requestUrl, params, onSuccess,onFail,null);
      }

    function confirmAddressChoujiang(id,address,receive,phone,onSuccess,onFail){
      var requestUrl = '?c=nc_user&a=lotteryAddress';
      var params = {
        id : id ,
        address: address,
        receive:receive,
        phone:phone
      };
      httpRequest.post(requestUrl, params, onSuccess,onFail,null);
    }

      function confirmGameAddress(order_num,addressId,onSuccess,onFail){
          var requestUrl = '?c=nc_games&a=editaddress';
          var params = {
              address_id : addressId ,
              order_num: order_num
          };
          httpRequest.post(requestUrl, params, onSuccess,onFail,null);
      }

    return {
      getAddressList : getAddressList, // 收货地址列表
      addAddress : addAddress ,// 新增收货地址
      updateAddress : updateAddress , //更新收货地址
      deleteAddress : deleteAddress, //删除收货地址
      confirmAddress : confirmAddress, //确认收货地址
      confirmPinTuanAddress:confirmPinTuanAddress, //拼团确认地址
      confirmPinTuanAddress2:confirmPinTuanAddress2, //拼团确认地址
      confirmAddressChoujiang:confirmAddressChoujiang,
        //游戏确认地址
        confirmGameAddress:confirmGameAddress
    }

  }]);


});
