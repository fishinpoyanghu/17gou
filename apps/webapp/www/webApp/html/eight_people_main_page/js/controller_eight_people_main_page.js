define(
    [
        'app',
        'components/view-slidebox/view_slidebox',
        'components/view-broad/view_broad',
        'utils/toastUtil',
        'models/model_activity',
        'components/view-progress/view_progress',
        'html/trolley/trolley_service',
        'components/view-text-scoller/view_text_scoller',
        'html/common/storage',
        'components/view-countdown/view_countdown',
        'models/model_goods',
        'models/model_pintuan',
        'models/model_app',
        'html/common/global_service',
        'html/thirdParty/thirdparty_wechat_js',
    ],
    function(app) {

        'use strict';
        app
            .controller('Eightpeople_MainpageController', ['$scope', '$state', 'ToastUtils', 'ActivityModel', 'trolleyInfo', 'Storage', 'GoodsModel', 'PintuanModel', '$timeout', 'AppModel', '$ionicPopup', 'Global', 'weChatJs', '$ionicSlideBoxDelegate',
                function($scope, $state, ToastUtils, ActivityModel, trolleyInfo, Storage, GoodsModel, PintuanModel,$timeout, AppModel, $ionicPopup, Global, weChatJs, $ionicSlideBoxDelegate) {
                    //拼团的图标数据
                    /*$scope.pintuan_icons=[
                        {img:"img/pintuan_icon/pintuan_icon_1.jpg",title:"超值大牌",url:""},
                        {img:"img/pintuan_icon/pintuan_icon_2.jpg",title:"潮流电器",url:""},
                        {img:"img/pintuan_icon/pintuan_icon_3.jpg",title:"吃货控",url:""},
                        {img:"img/pintuan_icon/pintuan_icon_4.jpg",title:"居家生活",url:""},
                        {img:"img/pintuan_icon/pintuan_icon_5.jpg",title:"团长免费",url:""},
                        {img:"img/pintuan_icon/pintuan_icon_6.jpg",title:"数码周边",url:""},
                        {img:"img/pintuan_icon/pintuan_icon_7.jpg",title:"优选水果",url:""},
                        {img:"img/pintuan_icon/pintuan_icon_8.jpg",title:"更多",url:""}
                    ];*/
                    $scope.categoryList = [];
                    //获取拼团的数据



                    $scope.show_attention = true;
                    if (Global.isInweixinBrowser()) {
                        $scope.inWechatB = true;
                    } else {
                        $scope.inWechatB = false;
                    }
                    $scope.close_attention = function($event) {
                        $event.stopPropagation()
                        $scope.show_attention = false;
                    }
                    $scope.go_attention = function() {
                        $state.go('attention');
                    }
                    $scope.go_search = function() {
                        $state.go('search');
                    }



//                  去返现购专区
                    $scope.gotoReturnCash = function(){
                    	
            			$state.go('return_cash');
                    	var ruleImgAlert = 'img/fanxiangouRule.png';
            			var fanxian = $ionicPopup.show({
            				template:'<img id="fanxiangouRule" src="'+ruleImgAlert+'"><i></i>',
            				buttons:[{
            					text:'确定',
            					type:'button-default',
            					onTap: function(e){
            						console.log(e)
            					}
            				}]
            			})
            			var timer = $timeout(function(){
									var fanxiangouRule = document.getElementById('fanxiangouRule'),
										popupBody = fanxiangouRule.parentElement,
										popup = popupBody.parentElement,
										span = fanxiangouRule.querySelector('span'),
										popupheader = popup.getElementsByClassName('popup-head')[0];
										popupheader.style.display = 'none';
										popup.style.height = '286px';
										popup.style.width = '300px';
//										popup.style.backgroundColor = 'transparent';
										/*返回用户不是微信账户的错误*/
									},10)
                    }
                    getBanner();
                    //获取广告图
                    function getBanner() {
                        ActivityModel.getBanner(null,function(xhr, re) {
                            var code = re.code;
                            if (code == 0) {
                                $scope.slides = re.data;
                            } else {
                                ToastUtils.showMsgWithCode(code, re.msg);
                            }
                        }, function(response, data) {
                            ToastUtils.showMsgWithCode(7, '获取广告图失败：' + '状态码：' + response.status);
                        }, null)
                    }
                    function getcategoryList() {
                        PintuanModel.getPintuanList($scope.goods_type_id,null,'',$scope.order_type,($scope.page-1) * 20 + 1,20,'0',$scope.activity_type, function(xhr, re) {
                            var code = re.code;
                            if (code == 0) {
                                var data = re.data;
                                var len = data.length;
                                $scope.activity_type=3;
                                console.log(len);
                                for(var i=0;i<len;i++){
                                    data[i].img="img/pintuan_icon/"+data[i].goods_type_id+".jpg";
                                }
                                $scope.categoryList = data;
                                //$scope.categoryList.push(data);
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


                    function initConfig() {
                        $scope.isLoadFinished = true;
                        $scope.pageConfig = [{
                            index: 1,
                            isLoading: false,
                            data: [],
                            goods_type_id: '',
                            order_key: 'weight', //类别
                            oder_type: '',
                            activity_type: 3, //1、非10元；2、10元；3、限购  //原本0改成-1 是让首页不显示二人购
                            emptyData: false,
                            page: 0,
                            pageOver: false,
                            pageCount: 20
                        }]
                        getcategoryList();
                        $scope.getData(true)

                    }

                    $scope.doRefresh = function() {
                        initConfig()
                    };



                    $scope.changeActive = function(order_key, order_type, config) {
                        if (!$scope.isLoadFinished) return;
                        config.order_key = order_key;
                        config.isLoading = true;
                        if (order_type == 'none') {
                            config.order_type = '';
                        } else if (order_type == 'asc') {
                            config.order_type = 'desc';
                        } else {
                            config.order_type = 'asc';
                        }
                        config.page = 0;
                        config.data = [];
                        $scope.getData(true);


                    }
                    $scope.getMoreData = function() {
                        if ($scope.pageConfig[0].isLoading) return;
                        $scope.getData()
                    }
                    $scope.getData = function(doRefresh) {
                        var postData = $scope.pageConfig[0];
                        postData.isLoading = true;
                        postData.page++
                        PintuanModel.pintuan_homepage(null, null, postData.order_key, postData.order_type, (postData.page - 1) * postData.pageCount + 1, postData.pageCount, '0', postData.activity_type, function(xhr, re) {
                                var code = re.code;
                                if (code == 0) {
                                    var data = re.data;
                                    var len = data.length;
                                    postData.activity_type=3;
                                    for (var i = 0; i < len; i++) {
                                        postData.data.push(data[i]);
                                    }
                                    if (len == postData.pageCount) {
                                        postData.pageOver = false;
                                    } else {
                                        postData.pageOver = true;
                                    }
                                } else {
                                    ToastUtils.showMsgWithCode(code, re.msg);
                                }
                            }, function(response, data) {
                                ToastUtils.showMsgWithCode(7, '获取商品列表失败：' + '状态码：' + response.status);
                            }, function() {
                                postData.isLoading = false;
                                if (doRefresh) $scope.$broadcast('scroll.refreshComplete');
                                $scope.$broadcast('scroll.infiniteScrollComplete');
                            })
                        /*PintuanModel.baituan_homepage('', function(xhr, re) {
                            var code = re.code;
                            if (code == 0) {
                                var data = re.data;
                                var len = data.length;
                                for (var i = 0; i < len; i++) {
                                    postData.data.push(data[i]);
                                }
                                if (len == postData.pageCount) {
                                    postData.pageOver = false;
                                } else {
                                    postData.pageOver = true;
                                }
                            } else {
                                ToastUtils.showMsgWithCode(code, re.msg);
                            }
                        }, function(response, data) {
                            ToastUtils.showMsgWithCode(7, '获取商品列表失败：' + '状态码：' + response.status);
                        }, function() {
                            postData.isLoading = false;
                            if (doRefresh) $scope.$broadcast('scroll.refreshComplete');
                            $scope.$broadcast('scroll.infiniteScrollComplete');
                        });*/
                    }



                    $scope.getPercentageProgress = function(remain_num, need_num) {
                        return Math.round((need_num - remain_num) * 100 / need_num);
                    };


                    //跳转到商品名字对应的商品分类详情页面
                    $scope.go_classify=function(goods_type_id){
                        $scope.goods_type_id=goods_type_id;
                        console.log(goods_type_id);
                        $state.go('pintuan_classification_details');
                        Storage.set('pintuan_goods_type_id',$scope.goods_type_id)
                    }

                    //点击“更多”跳转到拼团商品分类页面
                    $scope.go_commodity_classify=function(){
                        $state.go('pintuan_commodity_classification');
                    }

                    //跳转到2人团商品详情页
                    $scope.gotoPintuan_Detail = function(id) {
                        $state.go('pintuan_detail', {
                            goods_id: id
                        });
                    };


                    //添加一个计算详情功能
                    $scope.gotoCountDetail = function(id) {
                        $state.go('countDetail', { activityId: id});
                    };


                    $scope.$on('$ionicView.beforeEnter', function(ev, data) {
                        initConfig();
                        $timeout(function() {
                            $ionicSlideBoxDelegate.next();
                        }, 1000)

                    })

                    $scope.$on('$destroy', function() {
                        if ($scope.getHomeNewPublishTimeOut) $timeout.cancel($scope.getHomeNewPublishTimeOut);
                    });

                }
            ]);
    });
