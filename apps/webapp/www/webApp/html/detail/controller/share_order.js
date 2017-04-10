define(
  [
    'app',
    'components/view-list-view/view_list_view',
    'components/view-list-item/view_list_item',
    'components/view-image-list/view-image-list',
    'components/view-buy-footer/view_buy_footer',
    'models/model_goods',
    'models/model_app',
    'html/common/geturl_service'
  ],
  function (app) {

    app.controller('shareOrderCtrl', shareOrderCtrl);

    shareOrderCtrl.$inject = ['$stateParams', '$scope', '$state', 'GoodsModel','ToastUtils','AppModel','MyUrl']
    function shareOrderCtrl($stateParams, $scope, $state, GoodsModel,ToastUtils,AppModel,MyUrl) {
      ToastUtils.showLoading('加载中....');
      (function init() {
        $scope.goodsId = parseInt($stateParams.goodsId);
        $scope.activityId = parseInt($stateParams.activityId);
        $scope.isEmpty = false;
       
        if($stateParams.activity==null) {
          setTimeout(function() {
            getActivity($scope.activityId);
          }, 300);
        }else {
          $scope.activity = $stateParams.activity;
        }
      })();



      var isDoRefreshing = false ;//是否正在做刷新操作
      $scope.isLoadFinished = false ;

      $scope.my = 0;
      $scope.orderlist = [] ;
      
      $scope.page = 0 ;
      $scope.type = 'hot';
      $scope.hasMoreData = true ;

      getShareOrderData(true);

      /**
       * 刷新
       */
      $scope.doRefresh = function(){
        $scope.page = 0 ;
        $scope.hasMoreData = true ;
        isDoRefreshing = true ;
        getShareOrderData(isDoRefreshing);
      };

      /**
       * 加载更多
       */
      $scope.doLoadMore = function(){
        if(!isDoRefreshing){
          getShareOrderData(false);
        }

      };

      /**
       * 数据是否为空
       * @returns {boolean}
       */
      $scope.isDataEmpty = function(){
        var isEmpty = true ;
        if($scope.orderlist.length>0){
          isEmpty = false ;
        }
        return isEmpty ;
      };


      $scope.changeType = function(type) {
        if(!isDoRefreshing){
          $scope.type = type;
          $scope.page = 0 ;
          $scope.hasMoreData = true ;
          isDoRefreshing = true ;
          ToastUtils.showLoading('加载中....');
          getShareOrderData(true);
        }
      }

      /**
       * 获取晒单数据
       * @param isRefresh
       */
      function getShareOrderData(isRefresh){
        $scope.page++;
        AppModel.getShare_list($scope.type,$scope.page,$scope.my,function(response){
          //onSuccess
          isConnect = true ;
          var code = response.data.code ;
          var msg = response.data.msg ;
          switch (code){
            case 0 :
              var dataList = response.data.data ;
              if(isRefresh){
                $scope.orderlist = [] ;
                isDoRefreshing = false ;
              }
              if(dataList.length>=10){
                $scope.hasMoreData = true ;
                // dataList.pop();
              }else{
                $scope.hasMoreData = false ;
              }
              $scope.orderlist = $scope.orderlist.concat(dataList);
              break;

            default :
              ToastUtils.showError(msg);
              break ;
          }
        },function(response){
          //onFail
          isConnect = false ;
          if(!$scope.isDataEmpty()){
            ToastUtils.showError('请检查网络状态，状态码：' + response.status);
          }
        },function(){
          //onFinal
          $scope.isLoadFinished = true ;
          $scope.$broadcast('scroll.refreshComplete');
          $scope.$broadcast('scroll.infiniteScrollComplete');
          ToastUtils.hideLoading();
        },$scope.goodsId);
      }

      $scope.zan = function(order) {
        try {
          if (!MyUrl.isLogin()) {
            event.preventDefault();
            $state.go('login',{'state':STATUS.LOGIN_ABNORMAL});
            ToastUtils.showWarning('请先登录！！');
            return;
          } else {
          }
        } catch (e) {
          console.error('登录判断跳转出错'+ e.name+'：'+ e.message);
        }
        if(order.is_zan) {
          ToastUtils.showSuccess('您已经赞过~');
          return;
        }
        AppModel.zan(order.show_id, function(xhr, re) {
            var code = re.code;
            if (code == 0) {
              order.zans = Number(order.zans) + 1;
              order.is_zan = true;
              ToastUtils.showSuccess('点赞成功');
            } else {
                ToastUtils.showMsgWithCode(code, re.msg);
            }
        }, function(response, data) {
            ToastUtils.showMsgWithCode(7, '点赞失败：' + '状态码：' + response.status);
        }, null)

      }




      $scope.gotoHisPage = function (order) {
        var uicon = order.uicon;
        var unick = order.unick;
        var uid = order.uid;
        $state.go('hispage', {uicon: uicon, unick: unick, uid:uid});
      }

      function getActivity(activityId) {
        GoodsModel.getGoodsDetail(activityId,
          function onSuccess(response, data) {
            if (data.code == 0) {
              $scope.activity = data.data;
            }
          });
      }
    }

  });
