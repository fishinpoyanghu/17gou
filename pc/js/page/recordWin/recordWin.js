define(['avalon','jquery','http/http-factory', 'lib/fileuploador/fileuploador','components/view-left-side/left','css!../../../css/selfInfo/member.min.css','page/userMes/userMes'],
    function(avalon,$,httpFactory, Uploador) {

        var recordWin = avalon.define({
            $id: "recordWinCtrl",
            $leftSideOpts:{
                activePage:'recordWin'
            },
            data: [],
            picList: [],
            myNum: [],
            page: 1,
            pageCount:6,
            hasNextPage: false,
            isFinshed: false,
            hidePage: false,
            getNextPage: function() {
                if (recordWin.hasNextPage && recordWin.isFinshed) {
                    recordWin.page++;
                    getrecordWin();
                }
            },
            getPrevPage: function() {
                if (recordWin.page > 1 && recordWin.isFinshed) {
                    recordWin.page--;
                    getrecordWin();
                } 

            },
            checkMyNum: function(activity_id) {
                httpFactory.getRecordListNum(activity_id, null, null, function(re) {
                    re = JSON.parse(re)
                    if (re.code == 0) {
                        recordWin.myNum = re.data;
                    } else {
                        layer.msg(re.msg)
                    }

                }, function(err) {

                },function() {});
            },
            isInConfirm:false,
            confirmReceive:function(record) {
                confirmReceive(record)
            },
            inShare:false,
            share:function(record) {
                recordWin.shareData = {
                    activity_id:record.activity_id,
                    show_title: '',
                    show_desc: '',
                    img: []
                }
                $('#shareEdit').modal('show')
            },
            shareData:{
                activity_id:"",
                show_title: '',
                show_desc: '',
                img: []
            },
            isInShareRelease:false,
            shareRelease:function() {
                shareRelease()
            }

        })

        function shareRelease() {
            if(recordWin.isInShareRelease) return;
            if(recordWin.shareData.show_title == '') {
                layer.msg('晒单标题不能为空')
                return;
            }
            if(recordWin.shareData.show_title.length   > 20) {
                layer.msg('晒单标题最大长度为20')
                return;
            }
            if(recordWin.shareData.show_desc == '') {
                layer.msg('晒单内容不能为空')
                return;
            }
            if(recordWin.shareData.show_desc.length    > 140) {
                layer.msg('晒单内容最大长度为140')
                return;
            }
            if(recordWin.shareData.img.length == 0) {
                layer.msg('请至少选择一张晒单图片')
                return;
            }
            recordWin.isInShareRelease = true;
            var shareData = avalon.mix({},recordWin.shareData.$model);

            shareData.img = shareData.img.join(',');
            httpFactory.shareRelease(shareData, function(re) {
                re = JSON.parse(re)
                if (re.code == 0) {
                   layer.msg('发布成功');
                   $('#shareEdit').modal('hide')
                } else {
                    layer.msg(re.msg)
                }

            }, function(err) {
                layer.msg(JSON.parse(err).msg)
            }, function() {
                recordWin.isInShareRelease = false;
            });
        }
        
        function confirmReceive(record) {
            if(recordWin.isInConfirm) return;
            layer.msg('签收中');
            recordWin.isInConfirm = true;
            httpFactory.confirmReceive(record.activity_id, function(re) {
                re = JSON.parse(re)
                if (re.code == 0) {
                   layer.msg('签收成功');
                   record.logistics_stat = 2;
                } else {
                    layer.msg(re.msg)
                }

            }, function(err) {
                layer.msg(JSON.parse(err).msg)
            }, function() {
                recordWin.isInConfirm = false;
            });
        }

        function getrecordWin() {
            recordWin.isFinshed = false;
            httpFactory.getWinRecordList(null, null, null, (recordWin.page - 1) * recordWin.pageCount, recordWin.pageCount, function(re) {
                re = JSON.parse(re)
                if (re.code == 0) {
                    var data = re.data;
                    recordWin.data = data;
                    if (data.length == recordWin.pageCount) {
                        recordWin.hasNextPage = true;
                    } else {
                        recordWin.hasNextPage = false;
                    }
                } else {
                    layer.msg(re.msg)
                }

            }, function(err) {

            }, function() {
                recordWin.isFinshed = true;
            });
        }

        



        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'recordWin';
                getrecordWin();
               var params = httpFactory.getDefaultParams();
               var url =  httpFactory.getBaseApiUrl() + '?c=bbs&a=uploadImg';
               for(var i in params) {
                   url += '&' + i + '=' + params[i];
               }

               var uploador = new Uploador({
                   accept: "image/jpeg,image/png,image/gif",
                   submitUrl: url
               });

               $('#shareUpLoad').append($(uploador.submitForm));
               $(uploador.fileInput).addClass("input-file").prop('title','上传图片').prop('name','filename');

                // 添加要上传的图片
                uploador.on("uploadstart", function() {
                    layer.msg('图片上传中')
                }).on('finish', function(re) {
                    re = JSON.parse(re);
                    if (re.code == 0) {
                        recordWin.shareData.img.push(re.data[0].icon);
                        layer.msg('图片上传成功');
                    } else {
                        layer.msg(re.msg);
                    }
                });
            }
            $ctrl.$onBeforeUnload = function() {}

            // 指定一个avalon.scan视图的vmodels，vmodels = $ctrl.$vmodels.concact(DOM树上下文vmodels)
            $ctrl.$vmodels = []

        })
    })
