/**
 * 分页加载+下拉刷新+上拉加载
 * Created by luliang on 2016/1/12.
 */

/*列表类型*/
var TYPE_INFINATE = 'infinite';  //无限滚动
var TYPE_PAGE = 'page';          //分页模式

/*请求超时*/
var REQUEST_TIMEOUT = 15000;      //刷新超时

define(
  ['app',
    'utils/toastUtil',
    'utils/httpRequest',
    'components/view-infinite-scroll/view_infinite_scroll'
  ], function (app) {
    'use strict';
    app
      .directive('viewListView', ['$ionicScrollDelegate', '$state', 'httpRequest', 'ToastUtils',
        function ($ionicScrollDelegate, $state, httpRequest, ToastUtils) {
          return {
            restrict: 'E',
            transclude: true,
            scope: {
              compId: '=',
              uniqueId:'@',
              dpPerRequestSize: '=',
              dpRequestUrl: '@',
              dpRequestParams: '=',
              dpCallBack: '=',
              dpAutoRefresh: '@'
            },
            compile: function compile(tElement, tAttrs, transclude) {
              return {
                pre: function preLink(scope, iElement, iAttrs, controller) {

                },
                post: function postLink(scope, iElement, iAttrs, controller) {
                  scope.$watch('dpCallBack',function(newValue,oldValue) {
                      _call_back = {
                        refreshSuccess: scope.dpCallBack.refreshSuccess || emptyFun,
                        refreshError: scope.dpCallBack.refreshError || emptyFun,
                        refreshFinal: scope.dpCallBack.refreshFinal || emptyFun,
                        loadSuccess: scope.dpCallBack.loadSuccess || emptyFun,
                        loadError: scope.dpCallBack.loadError || emptyFun,
                        loadFinal: scope.dpCallBack.loadFinal || emptyFun,
                        setData: scope.dpCallBack.setData || emptyFun,
                        setEmpty: scope.dpCallBack.setEmpty || emptyFun
                      };
                  })
                  var _page_size = scope.dpPerRequestSize || 10;
                  var requestParams = {};
                  requestParams = scope.dpRequestParams;
                  requestParams.from = 1;
                  requestParams.count = scope.dpPerRequestSize || 10;
                  var dataToSave = [];
                  var dataToShow = [];
                  var currentPage = 1;
                  var finalPage = 9999;
                  var _call_back = {
                    refreshSuccess: scope.dpCallBack.refreshSuccess || emptyFun,
                    refreshError: scope.dpCallBack.refreshError || emptyFun,
                    refreshFinal: scope.dpCallBack.refreshFinal || emptyFun,
                    loadSuccess: scope.dpCallBack.loadSuccess || emptyFun,
                    loadError: scope.dpCallBack.loadError || emptyFun,
                    loadFinal: scope.dpCallBack.loadFinal || emptyFun,
                    setData: scope.dpCallBack.setData || emptyFun,
                    setEmpty: scope.dpCallBack.setEmpty || emptyFun
                  };

                  scope.isEmpty = true;
                  scope.isNetError = false;
                  scope.isLoading = false;
                  scope.isFirstPage = isFirstPage;
                  scope.isFinalPage = isFinalPage;

                  scope.refresh = refresh;
                  scope.loadMore = loadMore;
                  scope.displayDiXian = false
                  scope.loadPrePage = loadPrePage;
                  scope.loadNextPage = loadNextPage;

                  scope.$on('view_list_view.refresh', function (event, id) {
                    if (id == scope.compId || id == scope.uniqueId) {
                      refresh();
                    }
                  });


                  iElement.bind('$destroy', function () {
                    //dom释放资源,中断请求
                  });

                  //初始化数据
                  (function init() {
                    //默认自动刷新
                    if(scope.dpAutoRefresh==undefined || scope.dpAutoRefresh==true) {
                      refresh()
                    }
                    _call_back.setEmpty(false);
                  })();

                  function emptyFun() {

                  }

                  function isFirstPage() {
                    return (currentPage === 1);
                  }

                  function isFinalPage() {
                    return (currentPage === finalPage);
                  }

                  function refresh() {
                    scope.isLoading = true;
                    finalPage = 1;
                    scope.isFinish = false;
                    requestParams.from = 1;
                    var request = httpRequest.post(scope.dpRequestUrl, requestParams, refreshSuccess, refreshError, refreshFinal);
                    setTimeout(function () {
                      try {
                        request.cancelRequest();
                      } catch (e) {
                      }
                    }, REQUEST_TIMEOUT)
                  }

                  function refreshSuccess(response, data) {
                    scope.isNetError = false;
                    var code = data.code;
                    if (0 == code) {
                      afterRefresh(response, data.data);
                    } else {
                      ToastUtils.showError('加载失败：' + data.msg);
                    }
                  }

                  function afterRefresh(response, data) {
                    dataToSave = data;
                    dataToShow = data;
                    _call_back.setData(dataToShow);

                    var len = data.length;
                    currentPage = 1;
                    requestParams.from += len;
                    scope.isFinish = (len < requestParams.count);
                    finalPage = (scope.isFinish) ? currentPage : 9999;
                    scope.isEmpty = (len===0);
                    _call_back.setEmpty(scope.isEmpty);
                  }

                  function refreshError(response, data) {
                    _call_back.refreshError(response, data);
                     console.log('因超时客户端主动断开请求（这个只是可能的情况，不一定是真的）');
                    if (dataToSave.length === 0 && response.status!=200) {
                      scope.isNetError = true;
                    }else {
                      ToastUtils.showError('网络异常');
                    }
                  }

                  function refreshFinal(result) {
                    scope.$broadcast('scroll.refreshComplete');
                    _call_back.refreshFinal(result);
                    scope.isLoading = false;
                  }


                  function loadMore() {
                  	if (scope.isFinish && !scope.isEmpty) {
                  		return;
                  	}
                  	if (scope.isEmpty) {
                  		return;
                  	}
	                  	scope.displayDiXian = true;
                    scope.isLoading = true;
                    var request = httpRequest.post(scope.dpRequestUrl, requestParams, loadSuccess, loadError, loadFinal);
                    setTimeout(function () {
                      try {
                        request.cancelRequest();
                      } catch (e) {
                      }
                    }, REQUEST_TIMEOUT)
                  }

                  function loadPrePage() {
                    currentPage = currentPage - 1;
                    dataToShow = getDataByPage(currentPage);
                    _call_back.setData(dataToShow);
                    $ionicScrollDelegate.scrollBottom();
                  }

                  function loadNextPage() {
                    scope.isLoading = true;
                    currentPage = currentPage + 1;
                    var hasNextPage = (dataToSave.length > (currentPage - 1) * _page_size);
                    if (hasNextPage) {
                      dataToShow = getDataByPage(currentPage);
                      _call_back.setData(dataToShow);
                      scope.isLoading = false;
                    } else {
                      var request = httpRequest.post(scope.dpRequestUrl, requestParams, loadSuccess, loadError, loadFinal);
                      setTimeout(function () {
                        try {
                          request.cancelRequest();
                        } catch (e) {
                        }
                      }, REQUEST_TIMEOUT)
                    }
                    $ionicScrollDelegate.scrollTop();
                  }

                  function loadSuccess(response, data) {
                    var code = data.code;
                    if (0 == code) {
                      afterLoad(response, data.data);
                    } else {
                      ToastUtils.showError('加载失败：' + data.msg);
                    }
                  }

                  function afterLoad(response, data) {
                    scope.isNetError = false;
                    var length = data.length;
                    requestParams.from += length;
                    scope.isFinish = requestParams.count > length;
                    dataToSave = dataToSave.concat(data);

                    if (iAttrs.type == TYPE_INFINATE) {
                      dataToShow = dataToSave;
                    } else {
                      if (length === 0) {
                        currentPage = currentPage - 1;
                      }
                      finalPage = (scope.isFinish) ? currentPage : 9999;
                      dataToShow = getDataByPage(currentPage);
                    }
                    _call_back.setData(dataToShow);
                  }

                  function getDataByPage(page) {
                    var start = (page - 1) * _page_size;
                    var end = start + _page_size;
                    if (end > dataToSave.length) {
                      end = dataToSave.length
                    }
                    return dataToSave.slice(start, end);

                  }

                  function loadError(response, data) {
                    ToastUtils.showError('网络异常');
                    _call_back.loadError(response, data);
                  }

                  function loadFinal(result) {
                    scope.$broadcast('scroll.infiniteScrollComplete');
                    _call_back.loadFinal(result);
                    scope.isLoading = false;
                  }

                }
              }
            },
            templateUrl: function (elem, attr) {
              var path = "webApp/components/view-list-view/";
              var type = attr.type || TYPE_PAGE;
              var fileName = "view_list_view_" + type + ".html";
              return path + fileName;
            }
          };
        }]);
  });
