/**
 * 分类列表缓存
 * Created by luliang on 2016/1/23.
 */
define(
  [
    'app',
    'models/model_activity',
    'html/common/local_database'
  ],
  function(app){
    app
      .factory('ClassificationService',['ActivityModel','localDatabase','$q',function(ActivityModel,localDatabase,$q){
        var _cache_classes;
        function saveCache(value){
          _cache_classes = value;
          localDatabase.setShopClasses(value);
        }

        function readCache(){
          localDatabase.getShopClasses();
        }

        function loadCache(){
          if('undefined' == typeof _cache_classes){
            _cache_classes = readCache();
          }
          return _cache_classes;
        }

        function updateCache(onSuccess,onFailed){
          ActivityModel.getCategoryList(function(response,data){
            var code = data.code;
            if(0 === code){
              var categoryList = data.data;
              _cache_classes = {};
              _cache_classes.categoryList = categoryList;
              _cache_classes.time = new Date().getUTCDay();
              saveCache(_cache_classes);
              onSuccess(categoryList);
            }else{
              onFailed(data.msg);
            }
          },function(){
            onFailed('网络问题');
          });
        }

        function checkCache(onSuccess,onFailed){
          try {
            _cache_classes = loadCache();
            if ('undefined' == typeof _cache_classes) {
              updateCache(onSuccess, onFailed);
              return;
            }
            var oldDay = _cache_classes.time;
            var newDay = new Date().getUTCDay();
            if (newDay > oldDay) {
              updateCache(onSuccess, onFailed);
            } else {
              onSuccess(_cache_classes.categoryList);
            }
          } catch (e) {
            onFailed(e);
          }
        }
        return{
          getClasses : function(onSuccess,onFailed){
            checkCache(onSuccess,onFailed);
          }
        }
      }]);
  }
);
