define([
  'app',
],function (app) {
  app
    .config(['$stateProvider', '$urlRouterProvider','$ionicConfigProvider','$sceDelegateProvider','$provide',function($stateProvider, $urlRouterProvider,$ionicConfigProvider,$sceDelegateProvider,$provide) {

      //$sceDelegateProvider.resourceUrlWhitelist([
      //  // Allow same origin resource loads.
      //  'self',
      //  // Allow loading from our assets domain.  Notice the difference between * and **.
      $sceDelegateProvider.resourceUrlWhitelist([
        // Allow same origin resource loads.
        'self',
        // Allow loading from our assets domain.  Notice the difference between * and **.
        'http://c.damaiplus.com/yungou/apps/api/**']);

      if(!isDebug){
        $provide.decorator("$exceptionHandler",function($delegate){
          return function(exception,cause){
            $delegate(exception, cause);
            ssjjLog.log('AngularJS error:');
            ssjjLog.error({
              msg: exception.message,
              stack: exception.stack
            });
            ga(
              'send',
              'event',
              'AngularJS error',
              exception.message,
              exception.stack,
              0,
              true
            );
          }
        });
      }

        $urlRouterProvider.otherwise('/tab/mainpage');
        //$urlRouterProvider.otherwise('/tab/mainpage2');
        //$urlRouterProvider.otherwise('/turntable2');
      //$urlRouterProvider.otherwise('/boostrap/');


      $stateProvider
        .state('boostrap',{
          url: '/boostrap/:invite_code',
          cache : false,
          templateUrl: function() {return 'webApp/html/login_register/login.html'},
          controller: 'LoginCtrl',
          controllerUrl: 'html/login_register/js/controller_login',
          dependencies: [
            'components/view-input/view_input',
            'models/model_user'
          ],  
          onEnter: function() {
            writeTitle('注册');
          }
        })

        // setup an abstract state for the tabs directive
        /*.state('tab', {
          url: '/tab',
          abstract: true,
          controllerUrl:'html/main_page/js/controller_main_page',
          templateUrl: 'webApp/html/tabs/tabs.html'
        })*/
            .state('tab', {
          url: '/tab',
          abstract: true,
          controllerUrl:'html/main_page/js/controller_main_page2',
          templateUrl: 'webApp/html/tabs/tabs2.html'
        })


        /*.state('tab.mainpage', {
          url: '/mainpage',
          views: {
            'tab-mainpage': {
              templateUrl: function() { return 'webApp/html/main_page/main_page.html'},
              controllerUrl:'html/main_page/js/controller_main_page',
              controller: 'MainPageController',
              dependencies: []
            }
          },
          onEnter: function() {
            writeTitle('首页');
          }
        })*/
          .state('tab.mainpage', {
          url: '/mainpage',
          views: {
            'tab-mainpage': {
              templateUrl: function() { return 'webApp/html/main_page/main_page2.html'},
              controllerUrl:'html/main_page/js/controller_main_page2',
              controller: 'MainPageController',
              dependencies: []
            }
          },
          onEnter: function() {
            writeTitle('首页');
          }
        })
          /*.state('mainpage2', {
          url: '/mainpage2',
              templateUrl: function() { return 'webApp/html/main_page/main_page2.html'},
              controllerUrl:'html/main_page/js/controller_main_page2',
              controller: 'MainPageController',
              dependencies: []
          ,
          onEnter: function() {
            writeTitle('首页');
          }
        })*/



        //支付
        .state('pay',{
          cache:false,
          url: '/payInfo',
          templateUrl: function() { return 'webApp/html/pay/pay.html'},
          controller: 'PayController',
          controllerUrl: 'html/pay/js/controller_pay',
          onEnter: function() {
            writeTitle('确认订单');
          }
        })

        .state('payTransfer',{
          cache:false,
          url: '/pay',
          templateUrl: function() { return 'webApp/html/pay/pay_transfer_page.html'},
          controller: 'PayTransferController',
          controllerUrl: 'html/pay/js/controller_pay_transfer',
        })

        //支付结果
        .state('payResult',{
          cache:false,
          url: '/payResult/:oderNum',
          templateUrl: function() {return 'webApp/html/pay/pay_result.html'},
          controller: 'PayResultController',
          controllerUrl: 'html/pay/js/controller_pay_result',
          onEnter: function() {
            writeTitle('支付结果');
          },
          params: {
            oderNum: null,
          }
        })

          //充值
          .state('chongzhi',{
              url: '/chongzhi/:rechargeType',
              templateUrl: function() {return 'webApp/html/pay/chongzhi.html'},
              controller: 'rechargeCtrl',
              controllerUrl: 'html/pay/js/controller_recharge',
              params:{
              	rechargeType:1
              }
          })

          /*二人拼团*/
        .state('tab.twoPeople', {
          cache:false,
          url: '/twoPeople',
          views: {

            'tab-twoPeople': {
              templateUrl: function() {return 'webApp/html/twoPeople/twoPeople.html'},
              controller: 'TwoPeopleCtrl',
              controllerUrl:'html/twoPeople/js/twoPeople_controller',
              dependencies: []
            }
          },
          onEnter: function() {
            writeTitle('二人拼团');
          }

        })
        .state('tab.trolley', {
          cache:false,
          url: '/trolley',
          views: {

            'tab-trolley': {
              templateUrl: function() {return 'webApp/html/trolley/trolley.html'},
              controller: 'TrolleyCtrl',
              controllerUrl:'html/trolley/trolley_controller',
              dependencies: ['components/view-trolley/view_trolley_list']
            }
          },
          onEnter: function() {
            writeTitle('购物车');
          }

        })
        //购物车单独界面
          .state('trolley', {
          cache:false,
               url: '/trolley',
              templateUrl: function() {return 'webApp/html/trolley/trolley2.html'},
              controller: 'TrolleyCtrl',
              controllerUrl:'html/trolley/trolley_controller',
              dependencies: ['components/view-trolley/view_trolley_list'],
          onEnter: function() {
            writeTitle('购物车');
          }

        })

        //最新揭晓页面
        /*.state('tab.publish', {
          url: '/publish',
          views: {
            'tab-publish': {
              //templateUrl: 'webApp/html/publish/publish.html'},
              templateUrl: function () {return 'webApp/html/publish/publish.html'},
              controller: 'PublishCtrl',
              controllerUrl: 'html/publish/publish_controller'
            }
          },
          onEnter: function() {
            writeTitle('最新揭晓');
          }
        })*/
          .state('publish', {
              url: '/publish',
              cache: false,
              templateUrl: function() {return 'webApp/html/publish/publish.html'},
              controller: 'PublishCtrl',
              controllerUrl: 'html/publish/publish_controller',
              onEnter: function() {
                  writeTitle('最新揭晓');
              }
          })


        //发现页面
        .state('tab.discovery', {
          url: '/discovery',
          views: {
            'tab-discovery': {
              templateUrl: function () {return 'webApp/html/discovery/discovery.html'},
              controller: 'discoveryCtrl',
              controllerUrl: 'html/discovery/discovery_controller'
            }
          },
          onEnter: function() {
            writeTitle('发现');
          }
        })

        /*.state('tab.classify', {
          url: '/classify',
          views: {
            'tab-classify': {
              templateUrl: function () {return 'webApp/html/classification/classify.html'},
              controller: 'classifyController',
              controllerUrl: 'html/classification/classify'
            }
          },
          onEnter: function() {
            writeTitle('所有商品');
          }
        })*/


        .state('tab.shareOrder', {
          url: '/tabShareOrder',
          views: {
            'tab-shareOrder': {
              templateUrl: function () {return 'webApp/html/share_order/share_order.html'},
              controller: 'ShareOrderCtrl',
              controllerUrl: 'html/share_order/js/controller_share_order',
              dependencies: [
                'components/view-image-list/view-image-list',
              ],
              params: {
                'uid' : null,
                'goodsId' : null,
                'pageTitle': null
              }
            }
          },
          onEnter: function() {
            writeTitle('晒单');
          }
        })


        //    一元购的宝贝详情
          /*.state('activity-goodsDetail', {
              url: '/activity/:activityId',
              templateUrl: function() {return 'webApp/html/detail2/goods_detail.html'},
              controller: 'GoodsDetailCtrl',
              controllerUrl: 'html/detail2/goods_detail',
              dependencies: [
                  'models/model_user'
              ],
              params: {
                  activityId: null,
              },
              onEnter: function() {
                  writeTitle('宝贝详情');
              }
          })*/


        //活动详情页面
        .state('activity-goodsDetail', {
          url: '/activity/:activityId',
          cache: false,
          templateUrl: function() {return 'webApp/html/detail/goods_detail.html'},
          controller: 'GoodsDetailCtrl',
          controllerUrl: 'html/detail/controller/goods_detail',
          params: {
            activityId: null,
          }
        }).state('activity-fullIntroduce', {
          url: '/activity/:activityId/fullIntroduce/:goodsId',
          templateUrl: function() {return 'webApp/html/detail/full_introduce.html'},
          controller: 'GoodsDetailFullIntroduceController',
          controllerUrl: 'html/detail/controller/full_introduce',
          params: {
            activity: null,
            activityId: null,
            goodsId: null
          }
        }).state('activity-lastPublishes', {
          url: '/activity/:activityId/lastPublishes/:goodsId',
          templateUrl: function() {return 'webApp/html/detail/last_publishes.html'},
          controller: 'LastPublishesCtrl',
          controllerUrl: 'html/detail/controller/last_publishes',
          params: {
            activity: null,
            activityId: null,
            goodsId: null
          }
        }).state('activity-joinRecords', {
          url: '/activity/:activityId/joinRecords',
          templateUrl: function() {return 'webApp/html/detail/join_record.html'},
          controller: 'joinRecordCtrl',
          controllerUrl: 'html/detail/controller/join_record',
          params: {
            activity: null,
            activityId: null,
            goodsId: null
          }
        }).state('activity-shareOrder', {
          url: '/activity/:activityId/shareOrder/:goodsId',
          templateUrl: function () {return 'webApp/html/detail/share_order.html'},
          controller: 'shareOrderCtrl',
          controllerUrl: 'html/detail/controller/share_order',
          params: {
            activity: null,
            activityId: null,
            goodsId: null
          }
        }).state('activityRule', {
            cache:false,
            url: '/activityRule/:type',
            templateUrl: function() {return 'webApp/html/detail/activity_rule.html'},
            controller: 'activityCtrl',
            controllerUrl: 'html/detail/controller/controller_activity_rule',
            onEnter: function() {
              writeTitle('红包玩法');
            }
        })

         /* .state('tab.classify', {
              url: '/classify',
              views: {
                  'tab-classify': {
                      templateUrl: function () {return 'webApp/html/classification/classify.html'},
                      controller: 'classifyController',
                      controllerUrl: 'html/classification/classify'
                  }
              },
              onEnter: function() {
                  writeTitle('所有商品');
              }
          })*/
          /*.state('classify',{
              cache : true,
              templateUrl: function() {return 'webApp/html/classification/classify.html'},
              controller: 'classifyController',
              controllerUrl: 'html/classification/classify',
              onEnter: function() {
                  writeTitle('分类浏览');
              }
          })*/
      //分类
        .state('classification',{
          cache : true,
          url: '/classification',
          templateUrl: function() {return 'webApp/html/classification/classification_page.html'},
          controller: 'ClassificationController',
          controllerUrl: 'html/classification/controller_classification',
          onEnter: function() {
            writeTitle('分类浏览');
          }
        }).state('shopClassificationList',{
          url: '/shopClassificationList/:type/:title',
          templateUrl: function() {return 'webApp/html/classification/shop_classification_list_page.html'},
          controller: 'shopClassificationListController',
          controllerUrl: 'html/classification/controller_shop_classification_list',
        })
        .state('limitArea',{
          url: '/limitArea',
          templateUrl: function() {return 'webApp/html/classification/limit_area.html'},
          controller: 'limitAreaController',
          controllerUrl: 'html/classification/limit_area',
        })

        .state('tenYuan',{
          url: '/tenYuan',
          templateUrl: function() {return 'webApp/html/classification/tenYuan.html'},
          controller: 'tenYuanController',
          controllerUrl: 'html/classification/tenYuan',
        })

        .state('classify',{
          url: '/classify',
          templateUrl: function() {return 'webApp/html/classification/classify.html'},
          controller: 'classifyController',
          controllerUrl: 'html/classification/classify',
        })

        //搜索
        .state('search',{
          cache:true,
          url: '/search/:productType',
          //加产品类型  1：代表一元购  2：拼团
          templateUrl: function() {return 'webApp/html/search/search_page.html'},
          controller: 'SearchController',
          controllerUrl: 'html/search/controller_search',
        })
          //搜索2测试
        .state('search2',{
          cache:true,
          url: '/search2',
          templateUrl: function() {return 'webApp/html/search/search_page2.html'},
          controller: 'SearchController2',
          controllerUrl: 'html/search/controller_search2',
        })





        //搜索结果
        .state('searchResult',{
          cache:false,
          url: '/searchResult/:productType/:keyword',
          templateUrl: function() {return 'webApp/html/search/search_result_page.html'},
          controller: 'SearchResultController',
          controllerUrl: 'html/search/controller_search_result',
        })

        //login view begin
        .state('login', {
          cache: true,
          url:'/login',
          templateUrl: function() {return 'webApp/html/login_register/login.html'},
          controller: 'LoginCtrl',
          controllerUrl: 'html/login_register/js/controller_login',
          dependencies: [
            'components/view-input/view_input',
            'models/model_user'
          ],
          params : {
            state : null
          },
          onEnter: function() {
            writeTitle('登录');
          }
        })

        .state('loginTransferPage',{
          cache: false,
          url: '/loginTransferPage/:sessid',
          templateUrl: function() {return 'webApp/html/login_register/login_transfer_page.html'},
          controller: 'LoginTransferPageCtrl',
          dependencies: ['html/login_register/js/controller_login_transfer_page'],
          params : {
            'sessid' : null
          }
        })

        .state('registerFirst',{
          cache : false,
          url: '/registerFirst',
          templateUrl: function() {return 'webApp/html/login_register/register_first.html'},
          controller: 'RegisterFirstCtrl',
          controllerUrl: 'html/login_register/js/controller_register_first',
          dependencies: [
            'components/view-input/view_input',
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('注册');
          }
        })

        /*绑定手机号码*/
          .state('BoundPhoneNumber',{
          cache : false,
          url: '/BoundPhoneNumber',
          templateUrl: function() {return 'webApp/html/login_register/bound_phone_number.html'},
          controller: 'BoundPhoneNumberCtrl',
          controllerUrl: 'html/login_register/js/controller_bound_phone_number',
          dependencies: [
            'components/view-input/view_input',
            'models/model_user'
          ],
          params: {
            'redpacket': null             
          },
          onEnter: function() {
            writeTitle('绑定手机号码');
          }
        })






        .state('registerSecond',{
          cache: false,
          url: '/registerSecond',
          templateUrl: function() {return 'webApp/html/login_register/register_second.html'},
          controller: 'registerSecondCtrl',
          controllerUrl: 'html/login_register/js/controller_register_second',
          dependencies: [
            'components/view-input/view_input',
            'models/model_user'
          ],
          params: {
            'phoneNumber': null ,
            'password' : null ,
            'mcode' : null,
            'inviteCode' : null
          },
          onEnter: function() {
            writeTitle('注册');
          }
        })


        .state('findPassword',{
          cache: false,
          url: '/findPassword',
          templateUrl: function() {return 'webApp/html/login_register/find_password.html'},
          controller: 'FindPasswordCtrl',
          controllerUrl: 'html/login_register/js/controller_find_password',
          dependencies: [
            'components/view-input/view_input',
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('忘记密码');
          }
        })

        .state('resetPassword',{
          cache: false,
          url: '/resetPassword',
          templateUrl: function() {return 'webApp/html/login_register/reset_password.html'},
          controller: 'ResetPasswordCtrl',
          controllerUrl: 'html/login_register/js/controller_reset_password',
          dependencies: [
            'components/view-input/view_input',
            'models/model_user'
          ],
          params: {'saveKey': null},
          onEnter: function() {
            writeTitle('重置密码');
          }
        })

        .state('modifyPassword',{
          cache: false,
          url: '/modifyPassword',
          templateUrl: function() {return 'webApp/html/login_register/modify_password.html'},
          controller: 'ModifyPasswordCtrl',
          controllerUrl: 'html/login_register/js/controller_modify_password',
          dependencies: [
            'components/view-input/view_input',
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('确认新密码');
          }
        })

        .state('modifyNick',{
          cache: false,
          url: '/modifyNick',
          templateUrl: function() {return 'webApp/html/login_register/modify_nick.html'},
          controller: 'ModifyNickCtrl',
          controllerUrl: 'html/login_register/js/controller_modify_nick',
          dependencies: [
            'components/view-input/view_input',
            'models/model_user'
          ],
          params : {
            'nick' : null
          },
          onEnter: function() {
            writeTitle('修改昵称');
          }
        })

        //我
        .state('tab.account', {
          cache: true,
          url: '/account',
          views: {
            'tab-account': {
              templateUrl: function() {return 'webApp/html/user/tab_account.html'},
              controller: 'AccountCtrl',
              controllerUrl: 'html/user/js/controller_account',
              dependencies: [
                'models/model_user'
              ]
            }
          },
          onEnter: function() {
            writeTitle('我');
          }
        })
        //我
        .state('tab.account2', {
          cache: true,
          url: '/account2',
          views: {
            'tab-account2': {
              templateUrl: function() {return 'webApp/html/user/tab_account2.html'},
              controller: 'AccountCtrl2',
              controllerUrl: 'html/user/js/controller_account2',
              dependencies: [
                'models/model_user'
              ]
            }
          },
          onEnter: function() {
            writeTitle('我');
          }
        })

        //消息界面
        .state('myNews',{
          url: '/myNews',
          templateUrl: function() {return 'webApp/html/news/news.html'},
          controller: 'NewsController',
          controllerUrl: 'html/news/js/controller_news',
          cache: false,
          onEnter: function() {
            writeTitle('消息');
          }
        })

        //个人信息
        .state('userDetail', {
          url: '/userDetail',
          templateUrl: function() {return 'webApp/html/user/user_detail.html'},
          controller: 'UserDetailCtrl',
          controllerUrl: 'html/user/js/controller_user_detail',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('个人信息');
          }

        })

        //代理管理
        .state('userAgency', {
          url: '/userAgency',
          templateUrl: function() {return 'webApp/html/userAgency/userAgency_index.html'},
          controller: 'userAgencyCtrl',
          controllerUrl: 'html/userAgency/js/controller_userAgency_index',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('亿七代理');
          }

        })
        
        //代理管理的数据统计
        .state('data_statistics', {					//每一行都要改
          url: '/data_statistics',
          templateUrl: function() {return 'webApp/html/userAgency/data_statistics/data_statistics_index.html'},
          controller: 'dataStatisticsCtrl',
          controllerUrl: 'html/userAgency/data_statistics/js/controller_data_statistics_index',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('数据统计');
          }

        })
        
        
        //代理管理的我的粉丝
        .state('my_fans', {
          url: '/my_fans',
          templateUrl: function() {return 'webApp/html/userAgency/my_fans/my_fans_index.html'},
          controller: 'myFansCtrl',
          controllerUrl: 'html/userAgency/my_fans/js/controller_my_fans_index',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('我的粉丝');
          }

        })
        
        
        //代理管理的收入管理
        .state('earning_manage', {
          url: '/earning_manage',
          templateUrl: function() {return 'webApp/html/userAgency/earningManage/earning_manage_index.html'},
          controller: 'earningManageCtrl',
          controllerUrl: 'html/userAgency/earningManage/js/controller_earning_manage_index',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('收入管理111');
          }

        })
        
        //代理管理的我的推广
        .state('my_generlize', {
          url: '/my_generlize',
          templateUrl: function() {return 'webApp/html/userAgency/my_generlize/my_generlize_index.html'},
          controller: 'myGenerlizeCtrl',
          controllerUrl: 'html/userAgency/my_generlize/js/controller_my_generlize_index',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('我的推广');
          }

        })
        

        //常见问题
        .state('question',{
          cache:false,
          url:'/question/:viewName',
          templateUrl: function($stateParams) {
            var view_name = 'question';
            view_name  = angular.isString($stateParams.viewName) ? $stateParams.viewName : view_name;
            return 'webApp/html/user/view_'+view_name+'.html';
          },
          controller: 'UserQuestionController',
          controllerUrl: 'html/user/js/controller_user_question',
          params: {
            'viewName' : null
          },
        })

        .state('questionReport',{
          cache:true,
          url:'/questionReport',
          templateUrl: function($stateParams) {
            return 'webApp/html/user/questionReport.html';
          },
          controller: 'questionReportController',
          controllerUrl: 'html/user/js/controller_questionReport'
         
        })

        .state('setting', {
          cache:false,
          url: '/setting',
          templateUrl: function() {return 'webApp/html/setting/setting.html'},
          controller: 'SettingCtrl',
          controllerUrl: 'html/setting/js/controller_setting',
          dependencies: [],
          onEnter: function() {
            writeTitle('设置');
          }
        })


        //关于我们
        .state('aboutUs',{
          cache:false,
          url: '/aboutUs',
          templateUrl: function() {return 'webApp/html/user/view_about_us.html'},
          onEnter: function() {
              writeTitle('关于我们');
          }})

        //晒单
        .state('shareOrder', {
          cache:false,
          url: '/shareOrder/:uid/:goodsId/:pageTitle',
          templateUrl: function() {return 'webApp/html/share_order/share_order.html'},
          controller: 'ShareOrderCtrl',
          controllerUrl: 'html/share_order/js/controller_share_order',
          dependencies: [
            'components/view-image-list/view-image-list',
          ],
          params: {
            'uid' : null,
            'goodsId' : null,
            'pageTitle': null
          }
        })


        //地址列表
        .state('addressList', {
          cache:false,
          url: '/addressList',
          templateUrl: function() {return 'webApp/html/address/address_list.html'},
          controller: 'AddressListCtrl',
          controllerUrl: 'html/address/js/controller_address_list',
          dependencies: [],
          onEnter: function() {
            writeTitle('收货地址');
          }
        })

        //选择收货地址
        .state('addressSelect', {
          cache:false,
          url: '/addressSelect/:activity_id/:type',
          templateUrl: function() {return 'webApp/html/address/address_select.html'},
          controller: 'AddressSelectCtrl',
          controllerUrl: 'html/address/js/controller_address_select',
          dependencies: [],
          params : {
            // 'activity_id' : null,
            // 'type':null
          },
          onEnter: function() {
            writeTitle('选择收货地址');
          }
        })

        //增加地址页面
        .state('addressAdd', {
          cache:false,
          url: '/addressAdd',
          templateUrl: function() {return 'webApp/html/address/address_add.html'},
          controller: 'AddressAddCtrl',
          controllerUrl: 'html/address/js/controller_address_add',
          dependencies: ['components/view-area-selector/view_area_selector'],
          onEnter: function() {
            writeTitle('新增收货地址');
          }
        })

        //修改地址页面
        .state('addressUpdate', {
          cache:false,
          url: '/addressUpdate',
          templateUrl: function() {return 'webApp/html/address/address_update.html'},
          controller: 'AddressUpdateCtrl',
          controllerUrl: 'html/address/js/controller_address_update',
          dependencies: ['components/view-area-selector/view_area_selector'],
          params : {
            'address' : null
          },
          onEnter: function() {
            writeTitle('我的收货地址');
          }
        })

        //邀请返利
        .state('invite', {
          cache:false,
          url: '/invite',
          templateUrl: function() {return 'webApp/html/invite/invite.html'},
          controller: 'InviteCtrl',
          controllerUrl: 'html/invite/js/controller_invite',
          dependencies: [],
          onEnter: function() {
            writeTitle('邀请有礼');
          }
        })

        //返利详情
        .state('rebateList', {
          cache:false,
          url: '/rebateList',
          templateUrl: function() {return 'webApp/html/invite/rebate_list.html'},
          controller: 'RebateListCtrl',
          controllerUrl: 'html/invite/js/controller_rebate_list',
          dependencies: [],
          onEnter: function() {
            writeTitle('返利详情');
          }
        })

        //我的红包
        .state('redPacket', {
          cache:false,
          url: '/redPacket',
          templateUrl: function() {return 'webApp/html/red_packet/red_packet.html'},
          controller: 'RedPacketCtrl',
          controllerUrl: 'html/red_packet/js/controller_red_packet',
          dependencies: [],
          onEnter: function() {
            writeTitle('我的红包');
          }
        })

        //红包玩法
        .state('redPacketRule', {
            cache:false,
            url: '/redPacketRule',
            templateUrl: function() {return 'webApp/html/red_packet_list/red_packet_rule.html'},
            controller: 'redPacketRuleCtrl',
            controllerUrl: 'html/red_packet_list/js/controller_redPacket_rule',
            onEnter: function() {
              writeTitle('红包玩法');
            }
        })


        //我的云购记录
        .state('myIndianaRecord', {
          cache:false,
          url: '/myIndianaRecord',
          templateUrl: function() {return 'webApp/html/indiana_record/mypage.html'},
          controller: 'MyPageCtrl',
          controllerUrl: 'html/indiana_record/js/controller_mypage',
          dependencies: [],
          params:{
            uid:null
          },
          onEnter: function() {
           /* writeTitle('购买记录');*/
            writeTitle('云购订单');
          }
        })

        //他的云购记录
        .state('hispage', {
          url: '/hispage/:uid/:unick',
          cache:false,
          controllerUrl:'html/indiana_record/js/controller_hispage',
          controller: 'hisPageCtrl',
          templateUrl: function() {return 'webApp/html/indiana_record/hispage.html'},
          params:{
            uicon:null
          },
          onEnter: function() {
            writeTitle('TA的购买记录');
          }
        })

        //我的中奖纪录
        .state('winningRecord', {
          cache:false,
          url: '/winningRecord',
          templateUrl: function() {return 'webApp/html/winning_record/winning_record.html'},
          controller: 'WinningRecordCtrl',
          controllerUrl: 'html/winning_record/js/controller_winning_record',
          params : {
            'activity_id' : null ,
            'address' : null
          },
          onEnter: function() {
            writeTitle('中奖记录');
          }
        })

        .state('editShareOrder', {
          cache:false,
          url: '/editShareOrder',
          templateUrl: function() {return 'webApp/html/share_order/edit_share_order.html'},
          controller: 'EditShareOrderCtrl',
          controllerUrl: 'html/share_order/js/controller_edit_share_order',
          params : {
            'activity_id' : null ,
            'address' : null
          },
          onEnter: function() {
            writeTitle('编辑晒单');
          }
        })

          //计算规则
          .state('countRule', {
              cache:false,
              url: '/countRule',
              templateUrl: function() {return 'webApp/html/count/count_rule.html'},
              controller: '',
              controllerUrl: '',
              onEnter: function() {
                writeTitle('计算规则');
              }
          })

          //计算详情
          .state('countDetail', {
              cache:false,
              url: '/countDetail/:activityId',
              templateUrl: function() {return 'webApp/html/count/count_detail.html'},
              controller: 'CountDetailCtrl',
              controllerUrl: 'html/count/controller_count_detail',
              params: {
                activityId: null
              },
              onEnter: function() {
                writeTitle('计算详情');
              }
          })

          //我的积分
          .state('myPoint', {
              cache:false,
              url: '/myPoint',
              templateUrl: function() {return 'webApp/html/my_point/my_point.html'},
              controller: 'myPointCtrl',
              controllerUrl: 'html/my_point/js/controller_my_point',

              onEnter: function() {
                writeTitle('我的积分');
              }
          })

          //积分玩法
          .state('pointRule', {
              cache:false,
              url: '/pointRule',
              templateUrl: function() {return 'webApp/html/my_point/point_rule.html'},
              controller: 'pointRuleCtrl',
              controllerUrl: 'html/my_point/js/controller_point_rule',
              onEnter: function() {
                writeTitle('积分玩法');
              }
          })
          //积分明细
          .state('pointDetails', {
              cache:false,
              url: '/pointDetails',
              templateUrl: function() {return 'webApp/html/point_details/point_details.html'},
              controller: 'pointDetailsCtrl',
              controllerUrl: 'html/point_details/js/controller_point_details',

              onEnter: function() {
                writeTitle('积分明细');
              }
          })
          //邀请好友明细
          .state('inviteDetails', {
              cache:false,
              url: '/inviteDetails',
              templateUrl: function() {return 'webApp/html/invite_details/invite_details.html'},
              controller: 'inviteDetailsCtrl',
              controllerUrl: 'html/invite_details/js/controller_invite_details',

              onEnter: function() {
                writeTitle('邀请好友明细');
              }
          })

          //师徒收益明细
          .state('commissionDetails', {
              cache:false,
              url: '/commissionDetails',
              templateUrl: function() {return 'webApp/html/commission_details/commission_details.html'},
              controller: 'commissionDetailsCtrl',
              controllerUrl: 'html/commission_details/js/controller_commission_details',

              onEnter: function() {
                writeTitle('师徒收益明细');
              }
          })

          //邀请好友
          .state('inviteFriends', {
              cache:false,
              url: '/inviteFriends',
              templateUrl: function() {return 'webApp/html/invite_friends/invite_friends.html'},
              controller: 'inviteFriendsCtrl',
              controllerUrl: 'html/invite_friends/js/controller_invite_friends',

              onEnter: function() {
                writeTitle('邀请注册');
              }
          })

          //邀请注册详情
          .state('recommend_detail',{
              url: '/recommend_detail',
              templateUrl: function() { return 'webApp/html/recommend_detail/recommend_detail.html'},
              controller: 'recommend_detailController',
              controllerUrl: 'html/recommend_detail/js/controller_recommend_detail',
              onEnter: function() {
                  writeTitle('邀请注册详情');
              }
          })
           //推荐消费详情
          .state('invite_detail',{
              url: '/invite_detail',
              templateUrl: function() { return 'webApp/html/invite_detail/invite_detail.html'},
              controller: 'invite_detailController',
              controllerUrl: 'html/invite_detail/js/controller_invite_detail',
              onEnter: function() {
                  writeTitle('推荐消费详情');
              }
          })


          //红包列表
          .state('redPacketList', {
            cache:false,
            url: '/redPacketList',
            templateUrl: function() {return 'webApp/html/red_packet_list/red_packet_list.html'},
            controller: 'redPacketListCtrl',
            controllerUrl: 'html/red_packet_list/js/controller_red_packet_list',
            onEnter: function() {
              writeTitle('红包列表');
            }
          })

          //抢红包
          .state('grabRedPacket', {
            cache:false,
            url: '/grabRedPacket/:activity_id',
            templateUrl: function() {return 'webApp/html/grab_red_packet/grab_red_packet.html'},
            controller: 'grabRedPacketCtrl',
            controllerUrl: 'html/grab_red_packet/js/controller_grab_red_packet',
            onEnter: function() {
              writeTitle('抢红包');
            }
          })

             //圣诞愿望盒
          .state('Christmas_Day',{
              cache:false,
              url:'/Christmas_Day',
              templateUrl: function() {return 'webApp/html/christmas_day/christmas_day.html'},
              controller: 'Christmas_DayCtrl',
              controllerUrl: 'html/christmas_day/js/controller_christmas_day',
              params: {
                  userid : null
              },
              onEnter: function() {
                  writeTitle('圣诞愿望盒');
              },
          })

          //圣诞愿望盒2
            .state('Christmas_Day2', {
            cache:false,
            url: '/Christmas_Day2/:userid',
            templateUrl: function() {return 'webApp/html/christmas_day/christmas_day2.html'},
            controller: 'Christmas_DayCtrl2',
            controllerUrl: 'html/christmas_day/js/controller_christmas_day2',
            params: {
            userid : null
            },
            onEnter: function() {
              writeTitle('圣诞愿望盒');
            }
          })

            //小鸟游戏
          .state('bird_game', {
              cache:false,
              url: '/bird_game',
              templateUrl: function() {return 'webApp/html/active/bird_game/bird_game.html'},
              controller: 'birdGameCtrl',
              controllerUrl: 'html/active/bird_game/js/controller_bird_game',
              dependencies: [],
              onEnter: function() {
                  writeTitle('小鸟游戏');
              }
          })




          //红包参与记录
          .state('redPacketJoinRecord', {
            cache:false,
            url: '/redPacketJoinRecord/:activity_id',
            templateUrl: function() {return 'webApp/html/grab_red_packet/redPacketJoinRecord.html'},
            controller: 'redPacketJoinRecordCtrl',
            controllerUrl: 'html/grab_red_packet/js/redPacketJoinRecord',
            onEnter: function() {
              writeTitle('红包参与记录');
            }
          })

          //红包往期揭晓
          .state('redPacketLastPublished', {
            cache:false,
            url: '/redPacketLastPublished/:red_id',
            templateUrl: function() {return 'webApp/html/grab_red_packet/redPacketLastPublished.html'},
            controller: 'redPacketLastPublishedCtrl',
            controllerUrl: 'html/grab_red_packet/js/redPacketLastPublished',
            onEnter: function() {
              writeTitle('红包往期揭晓');
            }
          })

          //我的余额
          .state('myAccount', {
              cache:false,
              url: '/myAccount',
              templateUrl: function() {return 'webApp/html/my_account/my_account.html'},
              controller: 'myAccountCtrl',
              controllerUrl: 'html/my_account/js/controller_my_account',

              onEnter: function() {
                writeTitle('我的余额');
              }
          })
          //余额明细
          .state('myAccountDetails', {
              cache:false,
              url: '/myAccountDetails',
              templateUrl: function() {return 'webApp/html/my_account_details/my_account_details.html'},
              controller: 'myAccountDetailsCtrl',
              controllerUrl: 'html/my_account_details/js/controller_my_account_details',

              onEnter: function() {
                writeTitle('余额明细');
              }
          })
          //余额详情
          .state('mybalanceDetail', {
              cache:false,
              url: '/mybalanceDetail',
              templateUrl: function() {return 'webApp/html/mybalanceDetail/mybalanceDetail.html'},
              controller: 'myBalanceDetailCtrl',
              controllerUrl: 'html/mybalanceDetail/js/controller_mybalanceDetail',

              onEnter: function() {
                writeTitle('余额明细');
              }
          })

          //评论
          .state('commentDetails', {
              cache:false,
              url: '/commentDetails/:id',
              templateUrl: function() {return 'webApp/html/comment_details/comment_details.html'},
              controller: 'commentDetailsCtrl',
              controllerUrl: 'html/comment_details/js/controller_comment_details',

              onEnter: function() {
                writeTitle('评论');
              }
          })
             //冒出爱心
          .state('showlove', {
              url: '/showlove',
              templateUrl: function () {return 'webApp/html/showlove/showlove.html'},
              controller: 'showLoveCtrl',
              controllerUrl: 'html/showlove/js/showlove',
              dependencies: [
                  'components/view-image-list/view-image-list',
              ],
              onEnter: function() {
                  writeTitle('冒出爱心');
              }
          })


              //沛要求先屏蔽抽奖页面
          //抽奖
          .state('turntable', {
              cache:false,
              url: '/turntable',
              templateUrl: function() {return 'webApp/html/turntable/turntable.html'},
              controller: 'turntableCtrl',
              controllerUrl: 'html/turntable/js/controller_turntable',
              params: {
                  invite_code_share : null
              },
              onEnter: function() {
                writeTitle('抽奖');
              }
          })
          //抽奖2
          /*.state('turntable2', {
              cache:false,
              url: '/turntable2',
              templateUrl: function() {return 'webApp/html/turntable/turntable2.html'},
              controller: 'turntableCtrl2',
              controllerUrl: 'html/turntable/js/controller_turntable2',
              params: {
                  invite_code_share : null
              },
              onEnter: function() {
                writeTitle('抽奖');
              }
          })*/



          .state('luckyLottery', {
              cache:false,
              url: '/luckyLottery',
              templateUrl: function() {return 'webApp/html/turntable/luckyLottery.html'},
              controller: 'luckyLotteryCtrl',
              controllerUrl: 'html/turntable/js/luckyLottery',
              onEnter: function() {
                writeTitle('抽奖');
              }
          })

          //抽奖玩法
          .state('turntableRule', {
              cache:false,
              url: '/turntableRule',
              templateUrl: function() {return 'webApp/html/turntable/turntable_rule.html'},
              controller: 'turntableRuleCtrl',
              controllerUrl: 'html/turntable/js/controller_turntable_rule',
              onEnter: function() {
                writeTitle('抽奖玩法');
              }
          })
          //引导页
          .state('guide_page', {
              cache:false,
              url: '/guide_page',
              templateUrl: function() {return 'webApp/html/guide_page/guide_page.html'},
              controller: 'guide_pageCtrl',
              controllerUrl: 'html/guide_page/guide_page',
              
              onEnter: function() {
                writeTitle('引导页');
              }
          })

           //提现记录
          .state('withdrawCashRecord', {
              cache:false,
              url: '/withdraw_cash_record',
              templateUrl: function() {return 'webApp/html/withdraw_cash/withdraw_cash_record.html'},
              controller: 'withdrawCashRecordCtrl',
              controllerUrl: 'html/withdraw_cash/js/controller_withdraw_cash_record',

              onEnter: function() {
                writeTitle('提现申请记录');
              }
          })

          //申请提现
          .state('applyWithdrawCash', {
              cache:false,
              url: '/apply_withdraw_cash',
              templateUrl: function() {return 'webApp/html/withdraw_cash/apply_withdraw_cash.html'},
              controller: 'applyWithdrawCashCtrl',
              controllerUrl: 'html/withdraw_cash/js/controller_apply_withdraw_cash',

              onEnter: function() {
                writeTitle('申请提现');
              }
          })

          //评论
          .state('test', {
              cache:false,
              url: '/test',
              templateUrl: function() {return 'webApp/html/test/test.html'},
              controller: 'testCtrl',
              controllerUrl: 'html/test/js/test',
              
              onEnter: function() {
                writeTitle('评论');
              }
          })
	  
	  //积分兑换余额
          .state('changePoint', {
              cache:false,
              url: '/changePoint',
              templateUrl: function() {return 'webApp/html/changePoint/changePoint.html'},
              controller: 'changePointCtrl',
              controllerUrl: 'html/changePoint/js/controller_changePoint',

              onEnter: function() {
                writeTitle('积分兑换余额');
              }
          })

          .state('shareDetails', {
            cache:false,
            url: '/shareDetails/:show_id',
            templateUrl: function() {return 'webApp/html/share_order/shareDetails.html'},
            controller: 'shareDetailsCtrl',
            controllerUrl: 'html/share_order/js/shareDetails',
            dependencies: [
              'components/view-image-list/view-image-list',
            ],
            onEnter: function() {
                writeTitle('晒单详情');
              }
           
          })
          .state('attention', {
            cache:false,
            url: '/attention',
            templateUrl: function() {return 'webApp/html/attention/attention.html'},
            controller: 'attentionCtrl',
            controllerUrl: 'html/attention/attention',
            dependencies: [
              
            ],
            onEnter: function() {
                writeTitle('关注亿七购官方微信');
              }
           
          })


          //所有商品中活动专区的各个分类的独立页面
          //年货专区
          .state('nian_huo',{
              url: '/nian_huo',
              templateUrl: function() { return 'webApp/html/classification/nian_huo.html'},
              controller: 'nian_huoController',
              controllerUrl: 'html/classification/nian_huo',
              onEnter: function() {
                  writeTitle('所有商品');
              }
          })
          //圣诞专辑
          .state('christmas_album',{
              url: '/christmas_album',
              templateUrl: function() { return 'webApp/html/classification/christmas_album.html'},
              controller: 'christmas_albumController',
              controllerUrl: 'html/classification/christmas_album',
              onEnter: function() {
                  writeTitle('所有商品');
              }
          })
          //返现购
          .state('return_cash',{
              url: '/return_cash',
              templateUrl: function() { return 'webApp/html/classification/return_cash.html'},
              controller: 'return_cashController',
              controllerUrl: 'html/classification/return_cash',
              onEnter: function() {
                  writeTitle('所有商品');
              }
          })
          //二人拼团
          .state('two_men_together',{
              url: '/two_men_together',
              templateUrl: function() { return 'webApp/html/classification/two_men_together.html'},
              controller: 'two_men_togetherController',
              controllerUrl: 'html/classification/Two_men_together',
              onEnter: function() {
                  writeTitle('所有商品');
              }
          })
        //十元专区
          .state('ten_yuan_area',{
              url: '/ten_yuan_area',
              templateUrl: function() { return 'webApp/html/classification/ten_yuan_area.html'},
              controller: 'ten_yuan_areaController',
              controllerUrl: 'html/classification/ten_yuan_area',
              onEnter: function() {
                  writeTitle('所有商品');
              }
          })

          //
          .state('line_chart',{
              url: '/line_chart',
              templateUrl: function() { return 'webApp/html/line_chart/line_chart.html'},
              controller: 'line_chartController',
              controllerUrl: 'html/line_chart/js/controller_line_chart',
              onEnter: function() {
                  writeTitle('折线图');
              }
          })



          .state('qiangRedPacket',{
              url: '/qiangRedPacket',
              templateUrl: function() { return 'webApp/html/qiang_red_packet/red_packet.html'},
              controller: 'RedPacketController',
              controllerUrl: 'html/qiang_red_packet/js/controller_red_packet',
              onEnter: function() {
                  writeTitle('抢红包');
              }
          })
          .state('qiangRedPacket2',{
              url: '/qiangRedPacket2',
              templateUrl: function() { return 'webApp/html/qiang_red_packet/red_packet2.html'},
              controller: 'RedPacketController2',
              controllerUrl: 'html/qiang_red_packet/js/controller_red_packet2',
              onEnter: function() {
                  writeTitle('缘分测试');
              }
          })
              //加关注后抽奖
        .state('attention_Share',{
          cache:false,
          url:'/attention_Share',
          templateUrl: function() { return 'webApp/html/attention_and_share/attention_and_share.html'},
          controller: 'Attention_Share2',
          controllerUrl: 'html/attention_and_share/js/controller_attention_and_share',
            params: {
            invite_code_share : null
          },
           onEnter: function() {
                  writeTitle('加关注后抽奖');
           },
        })
        //自动路由跳转带参数。用于避免某些路由一进入未登录就跳转到登录页面 例子： 可用于微信分享。。
        .state('auto_state',{
          cache:false,
          url:'/autostate/:routeurl',
          templateUrl: function() { return ''},
          controller: 'auto_state',
          controllerUrl: 'html/auto_state/js/controller_auto_state',
            params: {
            invite_code_share : null
          },
           onEnter: function() {
                  writeTitle('亿七购');
           },
        })


        //快递查询
          .state('express_query', {
              cache:false,
              url: '/express_query/:activity_id/:logistics_num/:logistics_id/:type',
              templateUrl: function() {return 'webApp/html/express_query/express_query.html'},
              controller: 'express_queryCtrl',
              controllerUrl: 'html/express_query/js/express_query',
              params : {
                  activity_id : null ,
                  logistics_num : null,
                  logistics_id : null
              },
              dependencies: [
                  'models/model_goods'
              ],
              onEnter: function() {
                  writeTitle('快递查询');
              }
          })




//      活动页面
				//百团大战
        .state('baituandazhan', {
          url: '/baituandazhan',
          templateUrl: function() {return 'webApp/html/active/baituandazhan/baituandazhan.html'},
          controller: 'baituandazhanCtrl',
          controllerUrl: 'html/active/baituandazhan/js/baituandazhan',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('百团大战');
          }

        })
        //百团大战个人中心
        .state('baituan_center', {
          url: '/baituan_center',
          templateUrl: function() {return 'webApp/html/active/baituandazhan/baituan_center/baituan_center.html'},
          controller: 'baituanCenterCtrl',
          controllerUrl: 'html/active/baituandazhan/baituan_center/js/baituan_center',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('百团大战个人中心');
          }
        })
        //百团大战揭晓页面
        .state('baituan_publish', {
          url: '/baituan_publish',
          templateUrl: function() {return 'webApp/html/active/baituandazhan/baituan_publish/baituan_publish.html'},
          controller: 'baituanPublishCtrl',
          controllerUrl: 'html/active/baituandazhan/baituan_publish/js/baituan_publish',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('百团大战揭晓页面');
          }
        })
        //百团大战计算详情
        .state('baituan_countDetail', {
          url: '/baituan_countDetail/:activityId',
          templateUrl: function() {return 'webApp/html/active/baituandazhan/baituan_countDetail/baituan_countDetail.html'},
          controller: 'baituanCountDetailCtrl',
          controllerUrl: 'html/active/baituandazhan/baituan_countDetail/js/baituan_countDetail',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('百团大战揭晓页面');
          }
        })
        //百团大战详情页
        .state('baituan_detail', {
          url: '/baituan_detail/:goods_id',
          templateUrl: function() {return 'webApp/html/active/baituandazhan/baituan_detail/baituan_detail.html'},
          controller: 'baituanDetailCtrl',
          controllerUrl: 'html/active/baituandazhan/baituan_detail/js/baituan_detail',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('百团大战详情页');
          }
        })
        
        //百团大战申请
        .state('baituan_apply', {
          url: '/baituan_apply/:activityId',
          templateUrl: function() {return 'webApp/html/active/baituandazhan/baituan_apply/baituan_apply.html'},
          controller: 'baituanApplyCtrl',
          controllerUrl: 'html/active/baituandazhan/baituan_apply/js/baituan_apply',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('百团大战申请页面');
          }
        })
        //百团大战成员
        .state('baituan_member', {
          url: '/baituan_member/:team',
          templateUrl: function() {return 'webApp/html/active/baituandazhan/baituan_member/baituan_member.html'},
          controller: 'baituanMemberCtrl',
          controllerUrl: 'html/active/baituandazhan/baituan_member/js/baituan_member',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('百团大战成员管理');
          }
        })
//      end 百团大战
        
//      end 活动页面
        //缘分游戏  自己做,未完成,后来给嘉欣做了
        .state('yuanfen', {
          url: '/yuanfen',
          templateUrl: function() {return 'webApp/html/active/yuanfen.html'},
          controller: 'yuanfenCtrl',
          controllerUrl: 'html/active/js/yuanfen',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('缘分游戏');
          }

        })

        //最新版的一元购商品分类
          .state('commodity_classification',{
              url: '/commodity_classification',
              templateUrl: function() { return 'webApp/html/commodity_classification/commodity_classification.html'},
              controller: 'commodity_classificationController',
              controllerUrl: 'html/commodity_classification/js/controller_commodity_classification',
              onEnter: function() {
                  writeTitle('商品分类');
              }
          })
          //分类详情（默认是热门推荐）
          .state('classification_details',{
              url: '/classification_details',
              templateUrl: function() {return 'webApp/html/classification_details/classify.html'},
              controller: 'classifyController',
              controllerUrl: 'html/classification_details/classify',
          })
          //分类详情的最新商品
          .state('new_goods',{
              url: '/new_goods',
              templateUrl: function() {return 'webApp/html/classification_details/new_goods.html'},
              controller: 'new_goodsController',
              controllerUrl: 'html/classification_details/new_goods',
          })

        //分类详情的返现购
            .state('return_cash2',{
                url: '/return_cash2',
                templateUrl: function() {return 'webApp/html/classification_details/return_cash.html'},
                controller: 'return_cashController',
                controllerUrl: 'html/classification_details/return_cash',
            })
             //分类详情的二人购
            .state('twopeople2',{
                url: '/twopeople2',
                templateUrl: function() {return 'webApp/html/classification_details/twopeople.html'},
                controller: 'twoPeopleController',
                controllerUrl: 'html/classification_details/twopeople',
            })




            //最新版的拼团商品分类
          .state('pintuan_commodity_classification',{
              url: '/pintuan_commodity_classification',
              templateUrl: function() { return 'webApp/html/pintuan/pintuan_commodity_classification/pintuan_commodity_classification.html'},
              controller: 'commodity_classificationController',
              controllerUrl: 'html/pintuan/pintuan_commodity_classification/js/controller_pintuan_commodity_classification',
              onEnter: function() {
                  writeTitle('拼团商品分类');
              }
          })
          //拼团分类详情（默认是热门推荐）
          .state('pintuan_classification_details',{
              url: '/pintuan_classification_details',
              templateUrl: function() {return 'webApp/html/pintuan/pintuan_classification_details/classify.html'},
              controller: 'classifyController',
              controllerUrl: 'html/pintuan/pintuan_classification_details/classify',
          })
              //拼团分类详情（最新商品）
          .state('pintuan_new_goods',{
              url: '/pintuan_new_goods',
              templateUrl: function() {return 'webApp/html/pintuan/pintuan_classification_details/new_goods.html'},
              controller: 'new_goodsController',
              controllerUrl: 'html/pintuan/pintuan_classification_details/new_goods',
          })


				//拼团页面
          /*.state('pintuan_main_page', {
              url: '/pintuan_main_page',
              views: {
                  'tab-mainpage': {
                      templateUrl: function() { return 'webApp/html/main_page/main_page.html'},
                      controllerUrl:'html/main_page/js/controller_main_page',
                      controller: 'MainPageController',
                      dependencies: []
                  }
              },
              onEnter: function() {
                  writeTitle('首页');
              }
          })*/

			//拼团首页
        .state('pintuan_main_page', {
          url: '/pintuan_main_page',
          templateUrl: function() {return 'webApp/html/pintuan_main_page/pintuan_main_page.html'},
          controller: 'Pintuan_MainpageController',
          controllerUrl: 'html/pintuan_main_page/js/controller_pintuan_main_page',
          dependencies: [

          ],
          onEnter: function() {
            writeTitle('拼团');
          }
        })
          //优惠活动
          .state('tab.preferential_activities', {
              url: '/preferential_activities',
              views: {
                  'tab-preferential_activities': {
                      templateUrl: function () {return 'webApp/html/preferential_activities/preferential_activities.html'},
                      controller: 'preferential_activitiesController',
                      controllerUrl: 'html/preferential_activities/js/preferential_activities'
                  }
              },
              onEnter: function() {
                  writeTitle('优惠活动');
              }
          })

          //8人团/团长免费首页页面
          .state('eight_people_main_page', {
              url: '/eight_people_main_page',
              templateUrl: function() {return 'webApp/html/eight_people_main_page/eight_people_main_page.html'},
              controller: 'Eightpeople_MainpageController',
              controllerUrl: 'html/eight_people_main_page/js/controller_eight_people_main_page',
              dependencies: [

              ],
              onEnter: function() {
                  writeTitle('8人团/团长免费首页');
              }
          })

          //拼团订单页面
          .state('pintuan_order', {
              cache:false,
              url: '/pintuan_order',
              templateUrl: function() {return 'webApp/html/pintuan/pintuan_center2/pintuan_center2.html'},
              controller: 'PintuanCenterCtrl',
              controllerUrl: 'html/pintuan/pintuan_center2/js/controller_pintuan_center2',
              dependencies: [],
              params:{
                  uid:null
              },
              onEnter: function() {
                  writeTitle('拼团订单');
              }
          })


          //百团大战
        .state('pintuan', {
          url: '/pintuan',
          templateUrl: function() {return 'webApp/html/pintuan/pintuan.html'},
          controller: 'pintuanCtrl',
          controllerUrl: 'html/pintuan/js/pintuan',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('拼团');
          }

        })
        //拼团个人中心
        .state('pintuan_center', {
          url: '/pintuan_center',
          templateUrl: function() {return 'webApp/html/pintuan/pintuan_center/pintuan_center.html'},
          controller: 'pintuanCenterCtrl',
          controllerUrl: 'html/pintuan/pintuan_center/js/pintuan_center',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('拼团个人中心');
          }
        })
        //拼团揭晓页面
        .state('pintuan_publish', {
          url: '/pintuan_publish',
          templateUrl: function() {return 'webApp/html/pintuan/pintuan_publish/pintuan_publish.html'},
          controller: 'pintuanPublishCtrl',
          controllerUrl: 'html/pintuan/pintuan_publish/js/pintuan_publish',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('拼团揭晓页面');
          }
        })
        //拼团计算详情
        .state('pintuan_countDetail', {
          url: '/pintuan_countDetail/:activityId',
          templateUrl: function() {return 'webApp/html/pintuan/pintuan_countDetail/pintuan_countDetail.html'},
          controller: 'pintuanCountDetailCtrl',
          controllerUrl: 'html/pintuan/pintuan_countDetail/js/pintuan_countDetail',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('拼团揭晓页面');
          }
        })
        //拼团详情页
        .state('pintuan_detail', {
          url: '/pintuan_detail/:goods_id',
          templateUrl: function() {return 'webApp/html/pintuan/pintuan_detail/pintuan_detail.html'},
          controller: 'pintuanDetailCtrl',
          controllerUrl: 'html/pintuan/pintuan_detail/js/pintuan_detail',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('拼团详情页');
          }
        })
        
        //拼团申请
        .state('pintuan_apply', {
          url: '/pintuan_apply/:activityId/:orderType/:teamwarId',
          templateUrl: function() {return 'webApp/html/pintuan/pintuan_apply/pintuan_apply.html'},
          controller: 'pintuanApplyCtrl',
          controllerUrl: 'html/pintuan/pintuan_apply/js/pintuan_apply',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('拼团申请页面');
          }
        })
        //拼团成员
        .state('pintuan_member', {
          url: '/pintuan_member/:team',
          templateUrl: function() {return 'webApp/html/pintuan/pintuan_member/pintuan_member.html'},
          controller: 'pintuanMemberCtrl',
          controllerUrl: 'html/pintuan/pintuan_member/js/pintuan_member',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('拼团成员管理');
          }
        })
        //拼团订单
        .state('pintuan_collect', {
          url: '/pintuan_collect',
          templateUrl: function() {return 'webApp/html/pintuan/pintuan_collect/pintuan_collect.html'},
          controller: 'pintuanCollectCtrl',
          controllerUrl: 'html/pintuan/pintuan_collect/js/pintuan_collect',
          dependencies: [
            'models/model_user'
          ],
          onEnter: function() {
            writeTitle('拼团收藏');
          }
        })
//      end 拼团


        //公盘
          .state('public_offer', {
              url: '/public_offer',
              templateUrl: function() {return 'webApp/html/public_offer/public_offer.html'},
              reload:true,
              controller: 'PublicOfferCtrl',
              controllerUrl: 'html/public_offer/js/public_offer',
              onEnter: function() {
                  writeTitle('公盘');
              }
          })

            //福袋
          .state('luckyBag', {
              url: '/luckyBag',
              templateUrl: function() {return 'webApp/html/luckyBag/luckyBag.html'},
              reload:true,
              controller: 'luckyBagCtrl',
              controllerUrl: 'html/luckyBag/js/luckyBag',
              onEnter: function() {
                  writeTitle('福袋');
              }
          })
            //福袋的订单
          .state('luckyBag_order', {
              url: '/luckyBag_order',
              templateUrl: function() {return 'webApp/html/luckyBag/luckyBag_order/mypage.html'},
              reload:true,
              controller: 'luckyBagOrderCtrl',
              controllerUrl: 'html/luckyBag/luckyBag_order/js/controller_mypage',
              onEnter: function() {
                  writeTitle('福袋订单');
              }
          })








				//小游戏
					//抓娃娃
          .state('zhuawawa', {
              url: '/zhuawawa',
              templateUrl: function() {return 'webApp/html/active/zhuawawa/zhuawawa_index.html'},
              cache: false,
              reload:true,
              controller: 'zhuawawaCtrl',
              controllerUrl: 'html/active/zhuawawa/js/zhuawawa_index',
              onEnter: function() {
                  writeTitle('抓娃娃首页');
              }
          })
          //日常任务
          .state('dailyTask', {
              url: '/dailyTask',
              templateUrl: function() {return 'webApp/html/active/zhuawawa/dailyTask/dailyTask.html'},
              cache: false,
              reload:true,
              controller: 'dailyTaskCtrl',
              controllerUrl: 'html/active/zhuawawa/dailyTask/js/dailyTask',
              onEnter: function() {
                  writeTitle('日常任务');
              }
          })

          //我的游戏记录
          .state('gameRecord', {
              cache:false,
              url: '/gameRecord',
              templateUrl: function() {return 'webApp/html/active/gameRecord/gameRecord.html'},
              controller: 'gameRecordCtrl',
              controllerUrl: 'html/active/gameRecord/js/controller_gameRecord',
              onEnter: function() {
                  writeTitle('游戏记录');
              }
          })

          
					//
           .state('text1', { //祥测试
            cache:false,
            url: '/text1',
            templateUrl: function() {return 'webApp/text/text1.html'},
            controller: 'text1Ctrl',
            controllerUrl: 'text/text1',
            dependencies: [
              'models/model_user'
            ],
            onEnter: function() {
                writeTitle('关注亿七购官方微信');
              }
           
            })

           .state('text2', {//嘉欣测试
            cache:false,
            url: '/text2',
            templateUrl: function() {return 'webApp/text/text2.html'},
            controller: 'text2Ctrl',
            controllerUrl: 'text/text2',
            dependencies: [
              
            ],
            onEnter: function() {
                writeTitle('关注亿七购官方微信');
              }
           
          })
          .state('text3', { //中专测试
            cache:false,
            url: '/text3',
            templateUrl: function() {return 'webApp/text/text3.html'},
            controller: 'text3Ctrl',
            controllerUrl: 'text/text3',
            dependencies: [
              
            ],
            onEnter: function() {
                writeTitle('关注亿七购官方微信');
              }
           
          });

      // if none of the above states are matched, use this as the fallback
      $ionicConfigProvider.platform.ios.tabs.position('bottom');
      $ionicConfigProvider.platform.android.tabs.position('bottom');
      $ionicConfigProvider.scrolling.jsScrolling(true);

      if(ionic.Platform.ua.toLowerCase().match(/MicroMessenger/i) == "micromessenger") {
          
          $ionicConfigProvider.views.transition('none')
      } 

      $ionicConfigProvider.views.swipeBackEnabled(false);
      $ionicConfigProvider.views.maxCache(5);//优化缓存页数
    }]);
});
