
//angular.module('starter.directives')
define(function(require){
  var app = require('app');
  app.directive('viewInput',function(){
    return {
      restrict:'E',
      scope : {
        compId:'=',
        dpInputModel : '=',
        dpInputHint : '=',
        dpInputMinLength : '=',
        dpInputMaxLength : '=',
        dpInputVerifyTxt : '=',
        dpReadOnly : '@',
        dpInputVerifyClick : '&',
        dpSexCallback : '&',
        dpClick : '&',
        dpSrc: '='
      },
      templateUrl: function(elem, attr) {
        var path = "webApp/components/view-input/";
        var fileName = "view_input_" + attr.type + ".html";
        return path + fileName;
      },
      link : function postLink(scope){
        if(scope.dpInputHint==='' || scope.dpInputHint===null || angular.isUndefined(scope.dpInputHint)){
          scope.inputHintPhone = '请输入手机号码';
          scope.inputHintPassword = '请输入密码';
          scope.inputHintNickname = '昵称';
          scope.inputHintVerify = '请输入手机验证码';
          scope.inputHintTextArea = '';
        }else{
          scope.inputHintPhone = scope.dpInputHint ;
          scope.inputHintPassword = scope.dpInputHint ;
          scope.inputHintNickname = scope.dpInputHint ;
          scope.inputHintVerify =  scope.dpInputHint ;
          scope.inputHintTextArea = scope.dpInputHint ;
        }


        function setInputFocus(){
          var oInput = document.getElementById("inputContain");
          oInput.focus();
        }

        /**
         * 清空输入框
         */
        scope.clearInput = function(){
          scope.dpInputModel = '' ;
          setInputFocus();
        };


        /**
         * 点击密码显示或者加密
         * @type {boolean}
         */
        scope.passwordType = false;
        scope.showPassWord = function(){
          scope.passwordType = !scope.passwordType;
        };

        scope.getPasswordType = function(){
          return scope.passwordType;
        };

        /**
         * 点击切换性别
         * @param {Boolean} sex true:man false:woman
         */
          //默认男
        scope.sex = true;
        scope.clickSex = function(sex){
          scope.sex = sex;
          scope.dpSexCallback({Sex : {type : sex?1:2}});
        };


        //编辑区域框
        scope.remainSize = scope.dpInputMaxLength ;
        scope.updateSize = function(){
          return scope.dpInputMaxLength - scope.dpInputModel.value.length;
        };

        scope.isNotEmpty = function(){
          return (scope.dpInputModel.length > 0);
        };


        /**
         * 获取头像
         * @returns {string}
         */
        scope.getHeadIcon = function(){
          var iconUrl = 'img/upload_touxiang.png';
          if(scope.dpSrc!==undefined && scope.dpSrc!=='' && scope.dpSrc!==null){
            iconUrl = scope.dpSrc ;
          }
          return iconUrl ;
        }

      }
    };
  });
});
