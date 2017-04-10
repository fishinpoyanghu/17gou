/**
 * Created by suiman on 16/1/12.
 */

define(
  [
    'app',
    'models/model_goods'
  ],
  function (app) {
  app.controller('CountDetailCtrl', countCtrl);

  countCtrl.$inject = ['$scope', '$stateParams', 'GoodsModel', 'ToastUtils']
  function countCtrl($scope, $stateParams, GoodsModel, ToastUtils) {



    (function init() {
      $scope.isNetError = false;
      $scope.activityId = parseInt($stateParams.activityId);
      $scope.refresh = refresh;
      $scope.expendDetailOK = false;
      $scope.jianTouImg = 'img/blue_down_jiantou.png';
      refresh();
    })();

    function refresh() {
      GoodsModel.getCountDetail($scope.activityId,
        function onSuccess(response, data) {
          if(data.code==0) {
            $scope.detail = data.data;
//          $scope.detail.status = 1
            // addRecordId($scope.detail);
            // var value_a = $scope.detail.value_a || 0;
            // var value_b = $scope.detail.value_b || 0;
            // var need = $scope.detail.need_num;
            // $scope.detail.lucky_num = getLuckyNum(value_a, value_b, need);
              console.log($scope.detail);
          }else {
            ToastUtils.showError(data.msg);
          }
      },
        function onFailed(response) {
          if(response.status!=200) {
            $scope.isNetError = true;
          }
        })
      
      	$scope.jumpBaiDuCaiPiao = function(){
      		window.location.href = 'http://touch.lecai.com/?agentId=5615#path=page%2Faward-result%2Flist/?lotteryType=CQSSC';
     	}
		$scope.expendDetail =function(){
			var downJianTou = 'img/blue_down_jiantou.png',
				upJianTou = 'img/blue_up_jiantou.png';
			$scope.expendDetailOK = !$scope.expendDetailOK;
			$scope.jianTouImg = ($scope.expendDetailOK ? upJianTou : downJianTou);
		}
		
        //下面是我添加的，目的是获取揭晓结果的数据。
        GoodsModel.getGoodsDetail($scope.activityId, function onSuccess(response, data) {
                if(data.code==0) {
                    $scope.activity = data.data;
                    // addRecordId($scope.detail);
                    // var value_a = $scope.detail.value_a || 0;
                    // var value_b = $scope.detail.value_b || 0;
                    // var need = $scope.detail.need_num;
                    // $scope.detail.lucky_num = getLuckyNum(value_a, value_b, need);
                    console.log($scope.activity);
                }else {
                    ToastUtils.showError(data.msg);
                }
            },
            function onFailed(response) {
                if(response.status!=200) {
                    $scope.isNetError = true;
                }
            });
    }

    //计算公式：(A+B)%所需人数 + 10000001
    function getLuckyNum(a, b, need) {
      return (a+b)%need + 10000001;
    }

    //根据参与时间获得记录编号，例如：2015-12-29 15:49:23:749 转化为：154923749
    function formatTime(time) {
      var s = time.replace(':', '');
      return s.split(' ')[1];
    }

    //给所有记录增加记录编号字段
    function addRecordId(data) {
      var len = data.length;
      for(var i=0; i<len; i++) {
        var record = data[len];
        record.record_id = formatTime(record.time);
      }
    }


  }
})
