
define(
    [
        'app',
        'html/trolley/trolley_fly',
        'components/view-broad/view_broad',
        'html/classification/service_classification_list',
        'utils/toastUtil',
        'models/model_activity',
        'html/trolley/trolley_service',
        'html/common/global_service',
    ],
    function(app,funParabola){
        'use strict';
        app
            .controller('TwoPeopleCtrl',['$scope','$stateParams','$timeout','ToastUtils','ClassificationService', 'ActivityModel','trolleyInfo','$window','Global','$ionicScrollDelegate','$state',
                function($scope,$stateParams,$timeout,ToastUtils,ClassificationService,ActivityModel,trolleyInfo,$window,Global,$ionicScrollDelegate,$state){
                    var innerHeight = $window.innerHeight;

                    $scope.categoryList = [];
                    /*下面是我暂时添加数据*/
                    $scope.Activity_zone={"name":"二人拼团","activity_type":4};

                    function getCategoryList(){
                        ClassificationService.getClasses(function(categoryList){
                            $scope.categoryList = categoryList
                            // for(var i in categoryList) {
                            //   if(categoryList[i].sub) {
                            //     for(var j = 0,len = categoryList[i].sub.length;j < len;j++) {
                            //       $scope.categoryList.push(categoryList[i].sub[j]);
                            //     }
                            //   }

                            // }
                        },function(reason){
                            console.error(reason);
                        });
                    }

                    $scope.changeActive = function(order_key,order_type,activity_type) {
                        if(!$scope.isLoadFinished) return;
                        $scope.order_key = order_key;
                        $scope.page = 1;
                        if(order_type == 'none') {
                            $scope.order_type = '';
                        } else if(order_type == 'asc') {
                            $scope.order_type = 'desc';
                        } else {
                            $scope.order_type = 'asc';
                        }

                        if(activity_type == 'tenyuan') {
                         $scope.activity_type = 4;
                         } /*else {
                         $scope.activity_type = -4; //原本null改成-4 是让此不显示二人购
                         }*/
                        $scope.getData(true);
                        console.log($scope.activity_type);

                    }

                    $scope.getData = function(doRefresh) {

                        $scope.isLoadFinished = false;
                        var order_key = $scope.order_key == 'tenyuan' ? null : $scope.order_key;
                        ActivityModel.getActivityList($scope.goods_type_id,null,order_key,$scope.order_type,($scope.page-1) * 20 + 1,20,'0',$scope.activity_type, function(xhr, re) {
                            var code = re.code;
                            if (code == 0) {
                                var data = re.data;
                                var len = data.length;
                                if(doRefresh) {
                                    $scope.broadlist = data;
                                } else {
                                    for(var i = 0;i < len;i++) {
                                        $scope.broadlist.push(data[i]);
                                    }
                                }
                                if(len < 20) {
                                    $scope.isMoreData = false;
                                } else {
                                    $scope.page ++;
                                    $scope.isMoreData = true;
                                }

                            } else {
                                ToastUtils.showMsgWithCode(code, re.msg);
                            }

                        }, function(response, data) {
                            ToastUtils.showMsgWithCode(7, '获取商品列表失败：' + '状态码：' + response.status);
                        }, function() {
                            $scope.isLoadFinished = true;
                            if(doRefresh) {
                                $scope.$broadcast('scroll.refreshComplete');
                                $ionicScrollDelegate.scrollTop()
                            }
                            $scope.$broadcast('scroll.infiniteScrollComplete');
                        })
                    }



                    $scope.doRefresh = function(){
                        $scope.page = 1;
                        $scope.activity_type=4;
                        $scope.getData(true);
                    };

                    $scope.loadMore = function(){
                        if(!$scope.isLoadFinished) return;
                        $scope.getData(false);
                    };

                    $scope.gotoDetail = function(id){
                        $state.go('activity-goodsDetail', {activityId:id});
                    };

                    function addToCart(broad,$event){
                        var img = new Image();
                        img.src = broad.title_img;
                        img.style.display = 'none';
                        img.className = 'eleFlyElement';
                        img.style.top = $event.y + 'px';
                        img.style.left = $event.x  + 'px';
                        angular.element(document.body).append(img);
                        img.style.display = 'block';
                        var toEle;
                        if($state.current.name == 'tab.classify') {
                            toEle = document.querySelector(".js-tab-cart");
                        } else {
                            toEle = document.querySelector("#classify_cart");
                        }
                        var myParabola = funParabola(img, toEle, {
                            speed: 300, //抛物线速度
                            curvature: 0.0008, //控制抛物线弧度
                            complete: function() {
                                img.style.display = 'none';
                                angular.element(img).remove()
                                var shopItem = broad;
                                shopItem.join_number = 1;
                                $scope.$apply(function() {
                                    trolleyInfo.addGoodsItem(shopItem);
                                })

                            }
                        })
                        myParabola.position().move();
                    };

                    $scope.addToCart = addToCart;
                    $scope.$on('$ionicView.beforeEnter', function(ev, data) {
                        $scope.broadlist = []
                        $scope.isMoreData = false;
                        $scope.isLoadFinished = true;
                        $scope.goods_type_id = null;
                        $scope.activity_type = 4;
                        $scope.page = 1;
                        $scope.order_key = 'ing';
                        getCategoryList();
                        /*  getCategoryList3();*/
                        $scope.getData();

                        var gapHeight;
                        if($state.current.name == 'tab.classify') {
                            gapHeight = 49;
                            $scope.hideCartIcon = true;
                            $scope.showCartId = false;
                        } else {
                            gapHeight = 0;
                            $scope.hideCartIcon = false;
                            $scope.showCartId = true;
                        }

                        if(Global.isInweixinBrowser()) {
                            $scope.leftBarHeight = innerHeight - gapHeight;
                            $scope.leftBarTop = '44px';
                            $scope.inWechatB = true;
                        } else {
                            $scope.leftBarHeight = innerHeight - 44 - gapHeight;
                            if(ionic.Platform.isIOS() || ionic.Platform.isIPad()) {
                                $scope.leftBarTop = '104px';
                                $scope.inIosApp = true;
                            } else {
                                $scope.leftBarTop = '88px';
                                $scope.inIosApp = false;
                            }
                            $scope.inWechatB = false;

                        }

                    })

                    $scope.goNext = function(){
                        if($state.current.name == 'tab.classify') {
                            $state.go('tab.publish');
                        }

                    };

                    $scope.goPre = function(){
                        if($state.current.name == 'tab.classify') {
                            $state.go('tab.mainpage');
                        }

                    };


                }]);
    });
