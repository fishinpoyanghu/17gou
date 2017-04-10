/**
 * Created by luliang on 2016/1/7.
 */
define(
    [
        'app',
        'models/model_app',
        'utils/toastUtil'
    ],
    function(app){
        'use strict';
        app
            .controller('SearchController2',['$scope','$state','$location','AppModel','ToastUtils',function($scope,$state,$location,AppModel,ToastUtils){
                $scope.searchContent = '';
                $scope.keywords = undefined;
                function isContains(str, substr) {
                    return new RegExp(substr).test(str);
                }

                //暂时添加最近搜索的数据
                $scope.textarea=[];
                $scope.newtextarea=[];
                $scope.zj_search=false;
                $scope.num=0;
                $scope.numb;
                //清除输入的文本
                $scope.clear_date=function(){
                    $scope.searchContent = '';
                }

                //从一个数组里不重复的元素复制到另一个数组里。
                function copy_newtextarea(){
                    for (var i = 0; i < $scope.textarea.length; i++) {
                        if ($scope.keywords.length == 0) {
                            $scope.isEmpty = true;
                            $scope.zj_search = true;
                        } else {
                            $scope.isEmpty = false;
                            $scope.zj_search = true;
                        }
                        if ($scope.newtextarea.indexOf($scope.textarea[i]) == -1) {
                            $scope.newtextarea.unshift($scope.textarea[i]);
                        }
                    }
                }


                //当输入关键词时，就把该关键词调到最近搜索方框里的第一个位置。
                function at_first(){

                    for (var j = 0; j < $scope.newtextarea.length; j++) {
                        $scope.zj_search = true;
                        if ( $scope.searchContent==$scope.newtextarea[j]) {
                            $scope.newtextarea.splice(j, 1);
                            $scope.zj_search = true;
                            break;
                        }
                    }
                    $scope.newtextarea.unshift($scope.searchContent);
                }


                $scope.search = function search(e) {

                    var keycode = window.event ? e.keyCode : e.which;
                    if (keycode == 13) {
                    if ($scope.searchContent.length <= 0) {
                        return;
                    }
                    $scope.keywords = undefined;

                    AppModel.getSearchWord($scope.searchContent, function (response, data) {

                        var code = data.code;
                        if (0 == code) {
                            $scope.keywords = data.data;
                            $scope.textarea.unshift($scope.searchContent);
                            copy_newtextarea();
                            at_first();
                        }
                        else {
                        }

                    }, function (response) {
                        ToastUtils.showError('网络异常：' + '状态码:' + response.status);
                        $scope.isEmpty = true;
                        $scope.zj_search = true;
                    });
                }
                };


                /*最右侧的搜索*/
                $scope.sousuo=function(){
                        if ($scope.searchContent.length <= 0) {
                            return;
                        }
                        $scope.keywords = undefined;

                        AppModel.getSearchWord($scope.searchContent, function (response, data) {

                            var code = data.code;
                            if (0 == code) {
                                $scope.keywords = data.data;
                                $scope.textarea.unshift($scope.searchContent);
                                copy_newtextarea();
                                at_first();
                            }
                            else {
                            }

                        }, function (response) {
                            ToastUtils.showError('网络异常：' + '状态码:' + response.status);
                            $scope.isEmpty = true;
                            $scope.zj_search = true;
                        });
                }

                /*删除最近搜索的所有数据*/
                $scope.clear_newtextarea=function(){
                    $scope.zj_search=false;
                    $scope.keywords=false;
                    $scope.isEmpty=false;
                    $scope.textarea.splice(0,$scope.textarea.length);
                    $scope.newtextarea.splice(0,$scope.newtextarea.length);
                    console.log($scope.textarea.length+$scope.newtextarea.length);
                }

                $scope.go_mainpage=function(){
                    $state.go("tab.mainpage")
                }

                $scope.goToResult = function(keyword){
                    // $location.path('/searchResult/'+keyword).replace();
                    $state.go('searchResult',{keyword:keyword});
                }
                $scope.$on('$ionicView.beforeEnter', function(ev, data) {
                    setTimeout(function() {
                        var oInput = document.getElementById("searchInput");
                        oInput.focus();
                    },100)

                })
            }]);
    });
