/**
 * Created by luliang on 2015/11/14.
 */
define(
  ['app',
      'html/trolley/trolley_fly',
      'html/trolley/trolley_service',
    'components/view-progress/view_progress',
    'lib/ng-lazyload',
    'utils/toastUtil',
    'models/model_activity',
    'html/common/global_service'
  ],function(app,funParabola){
  'use strict';
  app
    .directive('viewBroad',['$ionicScrollDelegate','$state','trolleyInfo','ActivityModel','ToastUtils',function($ionicScrollDelegate,$state,trolleyInfo,ActivityModel,ToastUtils){
      return {
        restrict:'E',
        scope: {
          compId:'=',
          dpTopBarTitle: '=',
          dpSetTopBar: '=',
          dpIsAutoLoad: '=',
          dpIsShortList:'=',
          dpPageSize:'=',
          dpFilterClass:'=',
          dpFilterTitle:'@',
          dpFilterOrder:'@',
          dpFilterOrderType:'@',
          dpFilterStatus:'@',
          dpFilterType:'@',
          dpToggleDate:'=',
          dpToggleComment:'=',
          dpSplitSize:'='
        },
        controller: ['$scope','$element','$attrs','$transclude',function($scope,$element,$attrs,
                                                                         $transclude){
          $scope.broadlist = [];

          $scope.requestParams = {
            goods_type_id:$scope.dpFilterClass,
            key_word:$scope.dpFilterTitle || null,
            order_key:$scope.dpFilterOrder || null,
            order_type:$scope.dpFilterOrderType || null,
            status :$scope.dpFilterStatus || null,
            activity_type : $scope.dpFilterType || null,
            from:1,
            count:$scope.dpPageSize || 10,
          };

          $scope.removeTheBroad = function(brodData){
            var k = $scope.broadlist.indexOf(brodData);
            if(k != -1){
              $scope.broadlist.splice(k,1);
            }
          };
          $scope.addTheBroadData = function(broadData){
            $scope.broadlist.push(broadData);
          };
          $scope.addTheBroadList = function(broadList){
            return $scope.broadlist.concat(broadList);
          };
        }],
        compile:function compile(tElement, tAttrs, transclude){
          return{
            pre:function preLink(scope, iElement, iAttrs, controller){

            },
            post:function postLink(scope, iElement, iAttrs, controller){
              var perPageLoadData = 0;
              //scope.style_isCartBtnClick = false;
              var isStartHttp = false;
              scope.disconnected = false;
              scope.isLoadFinished = true;
              scope.isFinish = false;
              scope.resetFresh = resetFresh;
              scope.loadMore = loadMore;
              scope.reloadPrePage = reloadPrePage;
              scope.loadNextPage = loadNextPage;

              function _dpFilterClass(){
                return scope.dpFilterClass || null;
              }

              function getRequestParams(){
                scope.requestParams.goods_type_id = _dpFilterClass();
                return scope.requestParams;
              }
              var startLoadEvent = scope.$on('broad.loadMore',function(event,id){
                if(scope.compId == id&&scope.isEnableScrollToLoad()){
                  loadMore();
                }else{
                  scope.$emit('view_broad.request_finished',
                    {id:scope.did,scope:SCOPE_CLASS.LIST,data:{}});
                }
              });
              var startRefreshEvent = scope.$on('broad.refresh',function(event,id){
                if(scope.compId == id){
                  resetFresh();
                }
              });

              iElement.bind('$destroy',function(){
                //dom释放资源,中断请求
                startLoadEvent();
                startLoadEvent = null;
                startRefreshEvent();
                startRefreshEvent = null;
              });

              scope.isEnableScrollToLoad = function(){
                if(typeof scope.dpSplitSize === 'undefined'){
                  return true;
                }else{
                  return (perPageLoadData　<　scope.dpSplitSize);
                }
              };

              function setPageLoadedData(length){
                if(typeof scope.dpSplitSize === 'undefined'){
                  return ;
                }
                perPageLoadData  += length;　
              }

              function reloadPrePage(){
                var params = getRequestParams();
                params.from -= (perPageLoadData + scope.dpSplitSize);
                if(params.from <= 0){
                  params.from = 1;
                }

                scope.requestParams = params;
                scope.broadlist = [];
                perPageLoadData = 0;
                $ionicScrollDelegate.scrollBottom();
                doRefresh();
              }

              function loadNextPage(){
                perPageLoadData = 0;
                scope.broadlist = [];
                $ionicScrollDelegate.scrollTop();
                loadMore();
              }

              function resetFresh(){
                scope.requestParams.from = 1;
                doRefresh();
              }

              function doRefresh(){
                isStartHttp = true;
                //console.info('id:'+scope.compId+'开始刷新');
                var params = getRequestParams();
                ActivityModel.getActivityList(params.goods_type_id,params.key_word,params.order_key,params.order_type,
                  params.from,params.count,params.status,params.activity_type,function(response,data){
                  var code = data.code;
                  if(code == 0){
                    var dataArray = data.data;
                    var length = dataArray.length;
                    scope.broadlist = dataArray;
                    perPageLoadData = length;
                    scope.requestParams.from += length;
                    scope.isFinish = params.count > length;
                    scope.$emit('view_broad.request_success',
                      {id:scope.did,scope:SCOPE_CLASS.LIST,data:{isFinish:scope.isFinish}});
                  }else{
                    ToastUtils.showMsgWithCode(code,data.msg);
                  }
                },function(response,data){
                  ToastUtils.showMsgWithCode(7,'刷新商品列表失败：'+'状态码：'+response.status);
                    scope.disconnected = true;
                },onFinal);
              }

              function loadMore() {
                if(isStartHttp){
                  return ;
                }
                isStartHttp = true;
                scope.isLoadFinished = false;
                //console.info('id:'+scope.compId+'开始加载更多');
                var params = getRequestParams();
                ActivityModel.getActivityList(params.goods_type_id,params.key_word,params.order_key,params.order_type,
                  params.from,params.count,params.status,params.activity_type, function(response,data){
                    var code = data.code;
                    if(code == 0){
                      var dataArray = data.data;
                      var length = dataArray.length;
                      if(length == 0){
                        scope.isFinish = true;
                        scope.$emit('view_broad.request_success',
                          {id:scope.did,scope:SCOPE_CLASS.LIST,data:{isFinish:scope.isFinish}});
                        return ;
                      }
                      setPageLoadedData(length);
                      scope.broadlist = scope.addTheBroadList(dataArray);
                      scope.isFinish = params.count > length;
                      scope.requestParams.from += length;
                      scope.$emit('view_broad.request_success',
                        {id:scope.did,scope:SCOPE_CLASS.LIST,data:{isFinish:scope.isFinish}});
                    }else{
                      ToastUtils.showMsgWithCode(code,"加载文章列表失败："+data.msg);
                    }
                  },function(response,data){
                    ToastUtils.showMsgWithCode(7,'加载更多文章列表失败：'+'状态码'+response.status);
                    scope.disconnected = true;
                  }, onFinal);
              }

              function onFinal(result){
                //console.info(window.JSON.stringify(result));
                scope.isLoadFinished = true;
                isStartHttp = false;
                scope.$emit('view_broad.request_finished',
                  {id:scope.did,scope:SCOPE_CLASS.LIST,data:{}});
              }

              scope.showTheListAll = function(){
                $state.go('shopClassificationList',{type:scope.dpFilterClass || '全部',title:'亿七购'});
              };

              function addToCart(broad,$event){
                  var img = new Image();
                  img.src = broad.goods_img;
                  img.style.display = 'none';
                  img.className = 'eleFlyElement';
                  img.style.top = $event.y + 'px';
                  img.style.left = $event.x  + 'px';
                  angular.element(document.body).append(img);
                  img.style.display = 'block';
                  var myParabola = funParabola(img, document.querySelector("#ion-ios-cart444"), {
                      speed: 300, //抛物线速度
                      curvature: 0.0008, //控制抛物线弧度
                      complete: function() {
                          img.style.display = 'none';
                          angular.element(img).remove()
                          var shopItem = broad;
                          shopItem.join_number = 1;
                          scope.$apply(function() {
                              trolleyInfo.addGoodsItem(shopItem);
                          })
                      }
                  });

                  myParabola.position().move();
              };

              scope.addToCart = addToCart;

              scope.addListToCart = function(list){
                var key;
                for(key in list){
                  addToCart(list[key]);
                }
                ToastUtils.showSuccess('全部添加成功');
              };

              scope.getPercentageProgress = function(remain_num,need_num){
                return Math.round((need_num - remain_num) * 100 / need_num);
              };

              scope.gotoDetail = function(id){
                $state.go('activity-goodsDetail', {activityId:id});
              };
              if(scope.dpIsAutoLoad){
                resetFresh();
              }
            }
          }
        },
        templateUrl: function(elem, attr) {
          var path = "webApp/components/view-broad/";
          var fileName = "view_broad_" + attr.type + ".html";
          return path+fileName;
        }
      };
    }]);


});
