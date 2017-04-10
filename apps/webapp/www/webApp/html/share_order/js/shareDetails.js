/**
 * Created by songmars on 16/1/5.
 */

define(
  [ 'app',
    'models/model_app',
    'utils/toastUtil',
    'html/common/geturl_service'
  ],
  function (app) {
    app.controller('shareDetailsCtrl', ShowOrderCtrl);

    ShowOrderCtrl.$inject = ['$scope','$state','$stateParams','AppModel','ToastUtils','MyUrl','$ionicHistory'];
    function ShowOrderCtrl($scope,$state, $stateParams,AppModel,ToastUtils,MyUrl,$ionicHistory) {
      $scope.show_id = $stateParams.show_id ;
      $scope.orderlist = [] ;
      ToastUtils.showLoading('加载中....');
      $scope.type = 'hot';

      getShareOrderData(true);

      /**
       * 刷新
       */
      $scope.doRefresh = function(){
        getShareOrderData(true);
      };



      var isConnect = true ;//网络是否连接
      /**
       * 显示断网页面
       * @returns {boolean}
       */



      /**
       * 跳转到TA的页面
       * @param uicon
       * @param unick
       */
      $scope.goToHisPage = function(uicon,unick,uid){
        $state.go('hispage',{uicon:uicon,unick:unick,uid:uid});
      };

      // $scope.doRefresh();
      $scope.goToMyAccount=function(){
        $state.go('tab.account')


      };

      

      $scope.back = function() {
          $ionicHistory.goBack();
      }


      /**
       * 获取晒单数据
       * @param isRefresh
       */
      function getShareOrderData(isRefresh){
        AppModel.getShareDetails($scope.show_id,function(response){
          isConnect = true ;
          var code = response.data.code ;
          var msg = response.data.msg ;
          switch (code){
            case 0 :
              var dataList = response.data.data ;
              $scope.orderlist = dataList;
              break;

            default :
              ToastUtils.showError(msg);
              break ;
          }
        },function(response){
          //onFail
          isConnect = false ;
          ToastUtils.showError('请检查网络状态，状态码：' + response.status);
        },function(){
          //onFinal
          $scope.isLoadFinished = true ;
          $scope.$broadcast('scroll.refreshComplete');
          ToastUtils.hideLoading();
        });
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

    }


  });


