/**
 * Created by songmars on 15/11/27.
 */
var REFRESH_FLAG = 0;
define(['app',
    'models/model_goods',
    'html/common/global_service',
    'utils/toastUtil',
    //要添加下面的PhotoUtils和exif，因为它们与这里有关系。前提是必须在中奖纪录中点击某商品的晒单分享按钮，不然后一直处在发表中状态。
    'utils/PhotoUtils',
    'utils/exif'

], function (app) {
    'use strict';
    app
        .controller('EditShareOrderCtrl', ['$scope', '$state', '$ionicPopup', '$ionicHistory', '$ionicActionSheet', '$stateParams', 'GoodsModel','ToastUtils', 'Global',
                function ($scope, $state, $ionicPopup, $ionicHistory, $ionicActionSheet, $stateParams, GoodsModel, ToastUtils,Global) {
                    $scope.newShowOrder = {
                        title: '',
                        content: '',
                        img1: ''
                    };
                    showExPop();
                    var pics = '';
                    var index = 0;
                    var fileList = document.getElementById("fileList");
                    var activity_id = $stateParams.activity_id;
                    $scope.base64img = [];


                    window.URL = window.URL || window.webkitURL;
                    $scope.choosePicture = function () {


                        try {
                            if (!navigator.camera) {//浏览器端
                                PhotoUtils.takePictureByHtml5(function (imgDataBase64) {//选择图片成功
                                    dealImage(imgDataBase64);
                                }, function (errMsg) {//选择图片失败

                                });
                            } else {//app端
                                $ionicActionSheet.show({
                                    buttons: [
                                        {text: '<b>相册</b>'},
                                        {text: '<b>拍照</b>'}
                                    ],
                                    titleText: '图片上传',
                                    cancelText: '取 消',
                                    cancel: function () {
                                    },
                                    buttonClicked: function (index) {
                                        if (index == 0) {//本地相册选择
//                                        PhotoUtils.getLocalPictureByApp(false, function (imageDataBase64) {
//                                            dealImage(imageDataBase64);
//                                        }, function (errMsg) {
//
//                                        });
                                            PhotoUtils.takePictureByHtml5(function (imgDataBase64) {//选择图片成功
                                                dealImage(imgDataBase64);
                                            }, function (errMsg) {//选择图片失败

                                            });
                                        } else if (index == 1) {//相机拍照
                                            PhotoUtils.takePhotoByApp(false, function (imageDataBase64) {
                                                dealImage(imageDataBase64);
                                            }, function (errMsg) {

                                            });
                                        }
                                        return true;
                                    }
                                });
                            }
                        } catch (e) {
                            alert('当前环境暂不支持：' + e.name + "：" + e.message);
                        }
                    };


                    /**
                     *
                     * @param imageDataBase64
                     */
                    function dealImage(imageDataBase64) {
                        var selectedImage = new Image();
                        selectedImage.src = imageDataBase64;
                        $scope.base64img.push(imageDataBase64);
                        var imgItem = document.createElement("div");
                        var plusIcon = document.getElementById('plusicon');
                        imgItem.className = 'imgBox_item';
                        var closeIcon = document.createElement("div");
                        closeIcon.className = 'icon ion-close-round';
                        imgItem.appendChild(closeIcon);
                        imgItem.appendChild(selectedImage);
                        fileList.insertBefore(imgItem, plusIcon);
                        closeIcon.addEventListener('click', function () {
                            fileList.removeChild(imgItem);
                            $scope.base64img.remove(imageDataBase64);

                            $scope.base64img.length > 5 ? plusIcon.style.display = 'none' : plusIcon.style.display = 'block';

                        });
                        $scope.base64img.length > 5 ? plusIcon.style.display = 'none' : plusIcon.style.display = 'block';

                    }

                    function onUpLoadFail(response) {
                        ToastUtils.hideLoading();
                        ssjjLog.error('上传失败：' + window.JSON.stringify(response));
                        var errmsg = '';
                        try {
                            if (angular.isUndefined(response.data)) {
                                errmsg = '网络异常';
                            } else {
                                errmsg = response.data.msg;
                            }
                            ToastUtils.showShortNow(STATE_STYLE.ERROR, "加载失败：" + errmsg);
                        } catch (e) {
                            ToastUtils.showShortNow(STATE_STYLE.ERROR, "图片过大，上传失败：" + errmsg);
                        }
                    }

                    function onFail(response) {
                        ToastUtils.hideLoading();
                        ToastUtils.showShortNow(STATE_STYLE.ERROR, response.msg);
                    }

                    function onSuccess(response) {
                        var code = response.data.code;
                        var msg = response.data.msg;

                        switch (code) {
                            case 0:
                                REFRESH_FLAG = 1;
                                $ionicPopup.confirm({
                                    title: '晒单成功！',
                                    scope: $scope,
                                    buttons: [{
                                        text: '我知道了',
                                        onTap: function(e) {
                                            back();
                                            return false;
                                        }
                                    }]
                                });


                                break;
                            default :
                                ToastUtils.showShortNow(STATE_STYLE.ERROR, '提交失败' + msg);
                                break;
                        }
                        ToastUtils.hideLoading();
                    }

                    function onUpLoadSuccess(response) {
                        var code = response.data.code;
                        var msg = response.data.msg;

                        var title = $scope.newShowOrder.title;
                        var content = $scope.newShowOrder.content;
                        switch (code) {
                            case 0:
                                var imgdata = [];
                                imgdata = response.data.data;
                                var pic = imgdata[0].icon;
                                var picraw = imgdata[0].iconraw;
                                if (pics == '') {
                                    pics = pic;
                                }
                                else {
                                    pics = pics + ',' + pic;
                                }
                                //图片上传成功开始发帖
                                index++;
                                if (index == $scope.base64img.length) {
                                    GoodsModel.editShowOrderList(activity_id, title, content, pics, onSuccess, onFail);
                                } else {
                                    postShareOrder();
                                }
                                break;
                            default :
                                ToastUtils.hideLoading();
                                ToastUtils.showShortNow(STATE_STYLE.ERROR, '提交失败' + msg);
                                break;
                        }
                    }

                    $scope.postShareOrder = function () {
                        index = 0;
                        postShareOrder();
                    }

                    //发布帖子
                    function postShareOrder() {

                        var title = $scope.newShowOrder.title;
                        var content = $scope.newShowOrder.content;
                        if ($scope.base64img.length > 0) {
                            try {
                                // ToastUtils.showLoading('发布中');

                                if (title.length <= 20 && content.length <= 140) {
                                    ToastUtils.showLoading('发布中');
                                    GoodsModel.publishImg($scope.base64img[index], onUpLoadSuccess, onUpLoadFail);
                                } else if(title.length > 20) {
                                    ToastUtils.hideLoading();
                                    ToastUtils.showError('晒单标题超过最大长度')
                                } else if(content.length > 140) {
                                    ToastUtils.hideLoading();
                                    ToastUtils.showError('晒单内容超过最大长度')
                                }
                            }
                            catch (e) {

                                alert('错误信息' + e.name + e.message);
                            }
                        }
                        //长度等于0表示图片，直接发帖
                        else {


                            if (title.length < 20 && content.length < 140) {
                                ToastUtils.showLoading('发布中');
                                GoodsModel.editShowOrderList(activity_id, title, content, pics, onSuccess, onFail);
                            } else if(title.length > 20) {
                                ToastUtils.hideLoading();
                                ToastUtils.showError('晒单标题超过最大长度')
                            } else if(content.length > 140) {
                                ToastUtils.hideLoading();
                                ToastUtils.showError('晒单内容超过最大长度')
                            }

                        }
                    };

                    $scope.isDisablePost = function () {
                        return $scope.newShowOrder.content.length <= 0;
                    };

                    $scope.goBack = function () {
                        if ($scope.newShowOrder.content.length > 0) {
                            $ionicPopup.confirm({
                                title: '晒单正在编辑中，是否放弃？',
                                cancelText: '取消',
                                cancelType: 'button-default',
                                okText: '确定',
                                okType: 'button-positive'
                            }).then(function (res) {
                                if (res) {
                                    back();
                                } else {

                                }
                            })
                        } else {
                            back();
                        }
                    };

                    function back() {
                        if (Global.isInweixinBrowser()) {
                            history.back();
                        } else {
                            $ionicHistory.goBack();
                        }
                    }


                    $scope.hidePop = function () {
                        document.getElementById("pause").style.display = 'none';
                    };
                    function pickPictureH5() {
                        return document.getElementById('fileElem').click();
                    }

                    Array.prototype.indexOf = function (val) {
                        for (var i = 0; i < this.length; i++) {
                            if (this[i] == val) return i;
                        }
                        return -1;
                    };
                    Array.prototype.remove = function (val) {
                        var index = this.indexOf(val);
                        if (index > -1) {
                            this.splice(index, 1);
                        }
                    };

                    //显示晒单示例弹窗
                    function showExPop(){
                        $ionicPopup.alert({
                            title:'<span class="red">全民来晒单！</span>',
                            subTitle:'成功的晒单是这样的↓',
                            template:'<h3 class="title1">精神饱满</h3><p style="color: #838383">中奖，就要放肆，中都中了不秀岂不浪费~</p><h3 class="title1">配图真实</h3><p style="color: #838383">奖品、快递单晒晒更健康！地址，电话打好马赛克哦~</p>',
                            buttons:[
                                {text:'我知道了'}
                            ]
                        });
                    }
                }]
        );
});




