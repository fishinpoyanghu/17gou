/**
 * Created by Administrator on 2015/11/27.
 */
define([
  'app',
  'models/model_user',
  'models/model_agency',
  'html/common/global_service',
  'utils/toastUtil'],function(app){

  app.factory('agencyInfo', agencyInfo);

  agencyInfo.$inject = ['$state','userModel','agencyModel','Global','ToastUtils'] ;

  function agencyInfo($state,userModel,agencyModel,Global,ToastUtils){


    /**
     * 获取用户代理相关信息
     */
    function getagencymsg(){
      agencyModel.getagencymsg();
    }


    return {
      getagencymsg: getagencymsg			//代理模块
    }

  }

});
