/**
 * Created by Administrator on 2016/1/7.
 */
define(
  ['app',
    'utils/toastUtil',
    'utils/AreaData',
    'utils/AreaPicker'
  ],function(app){
    'use strict';
    app
      .directive('viewAreaSelector',['ToastUtils',function(ToastUtils){
        return {
          restrict:'E',
          scope : {
            compId:'=',
            dpProvinceModel : '=',
            dpCityModel : '=',
            dpCountyModel : '=',
            dpIsShow : '=',
            dpType : '='
          },
          templateUrl: 'webApp/components/view-area-selector/view_area_selector.html',
          link : function postLink(scope){

            scope.dpIsShow = false;//地区选择器开关
            scope.$watch('dpIsShow', function() {
              if(scope.dpType=='PROVINCE'){
                scope.showProvinceSelector();
              }else if(scope.dpType=='CITY'){
                scope.showCitySelector();
              }else if(scope.dpType=='COUNTY'){
                scope.showCountySelector();
              }
            });
            scope.selectChange = function(){
              scope.dpIsShow = false;
              if(scope.areaData.type==='PROVINCE'){
                if(scope.dpProvinceModel!=scope.areaData.value){
                  scope.dpProvinceModel = scope.areaData.value ;
                  scope.dpCityModel = '' ;
                  scope.dpCountyModel = '' ;
                }

              }else if(scope.areaData.type==='CITY'){
                if(scope.dpCityModel != scope.areaData.value){
                  scope.dpCityModel = scope.areaData.value ;
                  scope.dpCountyModel = '' ;
                }

              }else if(scope.areaData.type==='COUNTY'){
                scope.dpCountyModel = scope.areaData.value ;
              }
            };

            /**
             * 地区数据
             * @type {{type: string, data: Array, value: string}}
             */
            scope.areaData = {
              type : '',
              data : [],
              value : ''
            };

            /**
             * 显示省选择
             */
            scope.showProvinceSelector = function(){
              scope.areaData.data = AreaPicker.getProvinces();
              scope.areaData.type = 'PROVINCE';
            };

            /**
             * 显示市区选择
             */
            scope.showCitySelector = function(){
              if(scope.dpProvinceModel==''){
                ToastUtils.showWarning('请先选择省份！');
                scope.dpIsShow = false;
                return ;
              }
              scope.areaData.data = AreaPicker.getCity(scope.dpProvinceModel);
              scope.areaData.type = 'CITY';
            };

            /**
             * 显示县区选择
             */
            scope.showCountySelector = function(){
              if(scope.dpCityModel==''){
                ToastUtils.showWarning('请先选择市区！');
                scope.dpIsShow = false;
                return ;
              }
              scope.areaData.data = AreaPicker.getCounty(scope.dpProvinceModel,scope.dpCityModel);
              scope.areaData.type = 'COUNTY';
            };

          }
        };
      }]);
  });
