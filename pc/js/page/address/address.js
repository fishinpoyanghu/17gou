define(['avalon','http/http-factory','utils/AreaData','components/view-left-side/left','css!../../../css/selfInfo/member_comm.css','css!../../../css/selfInfo/member.min.css','css!lib/layer/skin/layer.css','page/userMes/userMes'],
    function(avalon,httpFactory,allAreaData) {
        var addressModel = avalon.define({
            $id: "addressCtrl",
            $leftSideOpts:{
                activePage:'address'
            },
            winId:'all',
            editData: {
                     address_id:'',
                     name:'',
                     mobile:'',
                     province:'',
                     city:'',
                     area:'',
                     detail:'',
                     is_default:0
                },
            data: [],
            allAreaData:allAreaData,
            CityData:[],
            AreaData:[],
            currentProvince:'-1',
            provinceIndex:0,
            currentCity:'-1',
            cityIndex:0,
            currentArea:'-1',
            areaIndex:0,
            showEditBox: false,
            provincesSelect:function(e) {
                var index = e.target.selectedIndex;
                addressModel.provinceIndex = index;
                addressModel.currentProvince = e.target.value;
                provinceChange(index)
            },
            citySelect:function(e) {
                var index = e.target.selectedIndex;
                addressModel.cityIndex = index;
                addressModel.currentCity = e.target.value;
                cityChange(index)
            },
            areaSelect:function(e) {
                var index = e.target.selectedIndex;
                addressModel.areaIndex = index;
                addressModel.currentArea = e.target.value;
            },
            isInEdit:false,
            setDefault:function(data) {
                if(addressModel.isInEdit) return;
                data.is_default = 1;
                addressModel.editBoxTitle = '设置默认地址';
                manageAddress('update',data)
            },
            AddressEdit: function() {
                addressModel.editData.province = addressModel.currentProvince;
                addressModel.editData.city = addressModel.currentCity;
                addressModel.editData.area = addressModel.currentArea;
                if(addressModel.isInEdit) return;
                var msg = addressModel.validateData();
                if(msg != 'true') {
                    layer.msg(msg);
                    return;
                }
                addressModel.isInEdit = true;
                manageAddress(addressModel.editType,addressModel.editData)
            },
            validateData:function() {
                var data = addressModel.editData;
                if(data.name == '') {
                    return '收货人姓名姓名不能为空';
                } else if(data.mobile == '') {
                    return '收货人手机不能为空';
                } else if(!/^(13|18|15|14|17)\d{9}$/i.test(data.mobile)) {
                    return '手机号格式不正确';
                } else if(data.province == '-1' || data.province == '') {
                    return '请选择省份';
                } else if(data.city == '-1' || data.city == '') {
                    return '请选择城市';
                } else if(data.area == '-1' || data.area == '') {
                    return '请选择区、县';
                } else if(data.detail == '') {
                    return '收货人地址不能为空';
                }

                return 'true'
            },
            editBoxTitle:'',
            editType:'',
            editAddress: function(data) {
                init_address_box()
                addressModel.showEditBox = true;
                if(data == 'add') {
                    addressModel.editType = 'add';
                    addressModel.editBoxTitle = '增加';
                    addressModel.editData = {
                             // address_id:'',
                             name:'',
                             mobile:'',
                             province:'',
                             city:'',
                             area:'',
                             detail:'',
                             is_default:0
                        }
                    initProvince();
                    initCityArea();
                } else {
                    addressModel.editType = 'update';
                    addressModel.editBoxTitle = '修改';
                    addressModel.editData = data;
                    setAddress(data.province,data.city,data.area)
                }
                
                
            },
            closePop:function() {
                addressModel.showEditBox = false;
            },
            deleteAddress: function(address_id) {
                layer.confirm('确定要删除这个地址吗?', {
                    btn: ['确定', '取消']
                }, function() {
                    httpFactory.DeleteAddress(address_id,function(re) {
                        re = JSON.parse(re);
                        if (re.code == 0) {
                            layer.closeAll();
                            getAddressList();
                        }else{
                            layer.msg(re.msg)
                        }
                    })
                });
            },
            isInUseAddress:false,
            useAddress:function(address_id) {
                if(addressModel.winId == 'all') return;
                if(addressModel.isInUseAddress) return;
                
                layer.confirm('确定要使用这个地址吗?', {
                    btn: ['确定', '取消']
                }, function() {
                    addressModel.isInUseAddress = true;
                    httpFactory.fillInAddress(address_id,addressModel.winId,function(re) {
                        re = JSON.parse(re);
                        if (re.code == 0) {
                            layer.msg('地址填写成功')
                            history.back();
                        }else{
                            layer.msg(re.msg)
                        }
                    },function(err) {
                        layer.msg(JSON.parse(err).msg)
                    },function() {
                        addressModel.isInUseAddress = false;
                    })
                });
            }

        })

        function manageAddress(type,data) {
            httpFactory.AddressEdit(type,data, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    layer.msg(addressModel.editBoxTitle + '成功')
                    getAddressList();
                    addressModel.showEditBox = false;
                } else {
                    layer.msg(re.msg)
                }
            },function(err) {
                layer.msg(JSON.parse(err).msg)
            },function() {
                addressModel.isInEdit = false;
            })
        }
        
        function setAddress(province,city,area) {
            addressModel.currentProvince = province;
            addressModel.provinceIndex = $('#provinceNode').find('option:selected').attr('data-index');
            provinceChange(addressModel.provinceIndex)
            addressModel.currentCity = city;
            addressModel.cityIndex = $('#cityNode').find('option:selected').attr('data-index');
            cityChange(addressModel.cityIndex)
            addressModel.currentArea = area;
        }

        function provinceChange(val) {
          if(val == 0) {  //未选
            initCityArea();
          } else {
            val--;
            if (allAreaData[val].sub && allAreaData[val].sub.length > 0) { //有子集
                addressModel.currentCity = '';
                addressModel.cityIndex = 0;
                addressModel.CityData = $.extend(true, [], allAreaData[val].sub);
                initArea();
            } else {
                initCity();
            }
          }
        }

        function cityChange(val) {
          if(val == 0) {  //未选
            initArea();
          } else {
            val--;
            var Pindex = addressModel.provinceIndex - 1;
            if (allAreaData[Pindex].sub[val].sub && allAreaData[Pindex].sub[val].sub.length > 0) { //有子集
                addressModel.currentArea = '';
                addressModel.areaIndex = 0;
                addressModel.AreaData =  $.extend(true, [], allAreaData[Pindex].sub[val].sub);
            } else {
                initArea();
                addressModel.AreaData = [{name:"其他"}];
            }
          }
        }

        function initCityArea () {
            initCity();
            initArea();
        }
        function initProvince () {
            addressModel.currentProvince = '-1';
            addressModel.provinceIndex = 0;
        }
        function initCity () {
            addressModel.currentCity = '-1';
            addressModel.cityIndex = 0;
            addressModel.CityData = [];
        }
        function initArea () {
            addressModel.currentArea = '-1';
            addressModel.areaIndex = 0;
            addressModel.AreaData = [];
        }
        
        $(window).resize(function() {
              if($(".c_address_in").css('display') == 'block') {
                init_address_box();
              }
        });

        function init_address_box() {
           var a = $(window).width(),
           c = $(window).height();
           var bg = $(".js-c_exchange_bg");
           var address_box = $(".c_address_in");
           address_box.hide();
           bg.hide();
           bg.css({ height: c + "px" });
           address_box.css({ left: (a - address_box.outerWidth()) / 2 + "px", top: (c - address_box.outerHeight()) / 2 + "px" });
           address_box.show();
           bg.show();
        }

        //获取收货地址列表
        function getAddressList() {
            var loadIcon = layer.load(1, {shade: false,offset: '400px'}); 
            httpFactory.noParams('getAddressList',function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    addressModel.data = re.data;
                }else{
                    layer.msg(re.msg)
                }
            },function(err) {
                layer.msg(JSON.parse(err).msg)
            },function() {
                layer.close(loadIcon); 
            })
        }

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function(state) {
                avalon.vmodels["headerEidget"].activePage = 'address';
                addressModel.winId = state.params.winId;
                getAddressList();
            }
            $ctrl.$onBeforeUnload = function() {}

            $ctrl.$vmodels = []

        })
    })
