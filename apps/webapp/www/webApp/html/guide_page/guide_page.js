define([
    'app',
    'html/common/storage'
], function(app) {

    app.controller(
        'guide_pageCtrl', ['$scope', '$state', '$timeout', '$ionicModal','Storage','$ionicPlatform','$compile',
            function($scope, $state, $timeout, $ionicModal,Storage,$ionicPlatform,$compile) {
                $scope.isHideNaviBar = true;
                $scope.goToIndex = function(flag) {
                    if(flag) Storage.set('firstOpen',true)
                    $state.go('tab.mainpage')
                }
                if(!appConfig.hasGuide) {
                    $scope.goToIndex();
                    return;
                }
                $scope.imgs = appConfig.guideImages;
                $scope.currentIndex = 0;
                $scope.prevIndex = 0;
                var tpl =  '<div style="position:absolute;bottom:10px;z-index:20;text-align:center;color:#fff;width:100%;left:0;right:0;">'+
                                '<i class="guide-dot {{$index == currentIndex ? \'current\' : \'\'}}" ng-repeat="item in imgs"></i>'+
                            '</div>';
                tpl = angular.element(tpl);
                angular.element(document.body).append(tpl)
                $compile(tpl)($scope);
                $scope.changePage = function() {
                    if($scope.currentIndex == $scope.imgs.length - 1) { //最后一张
                        $scope.goToIndex(true)
                    } 
                }
                $scope.getPrevImg = function() {
                    return $scope.imgs[$scope.prevIndex]
                }
                $scope.getCurrendImg = function() {
                    return $scope.imgs[$scope.currentIndex]
                }
                $scope.swipeRight = function($event) {
                    if($scope.currentIndex == 0) { //第一张
                        return;
                    } 
                    $scope.prevIndex = $scope.currentIndex;
                    $scope.currentIndex--;
                    
                    $ionicModal.fromTemplateUrl("guide_page_modal.html", {
                        scope: $scope,
                        animation: "dm-slide-in-right"
                    }).then(function(modal) {
                        $scope.modal && ($scope.modal.hide() && $scope.modal.remove());
                        $scope.modal = modal;
                        modal.show()
                    });
                    
                }
                $scope.swipeLeft = function($event) {
                    if($scope.currentIndex == $scope.imgs.length - 1) { //最后一张

                    } else {
                        $scope.prevIndex = $scope.currentIndex;
                        $scope.currentIndex++;
                        $ionicModal.fromTemplateUrl("guide_page_modal.html", {
                            scope: $scope,
                            animation: "dm-slide-in-left"
                        }).then(function(modal) {
                            $scope.modal && ($scope.modal.hide() && $scope.modal.remove());
                            $scope.modal = modal;
                            modal.show()
                        });
                        
                    }
                }
                $ionicModal.fromTemplateUrl("guide_page_modal.html", {
                    scope: $scope,
                    animation: "dm-slide-in-left"
                }).then(function(modal) {
                    $scope.modal_left = modal;
                });

                $ionicModal.fromTemplateUrl("guide_page_modal.html", {
                    scope: $scope,
                    animation: "dm-slide-in-right"
                }).then(function(modal) {
                    $scope.modal_right = modal;
                });

                $scope.$on("$destroy", function() {
                    offBackAction()
                    offHideModalAction()
                    $scope.modal && $scope.modal.remove();
                });

                var offBackAction = $ionicPlatform.registerBackButtonAction(function (e) {
                    e.preventDefault();
                    return false;
                }, 101);
                var offHideModalAction = $ionicPlatform.registerBackButtonAction(function (e) {
                    e.preventDefault();
                    return false;
                }, 201);

            }
        ])

});
