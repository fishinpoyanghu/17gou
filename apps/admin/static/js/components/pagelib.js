/* 
* @Desc: 选择页面链接弹窗
*/

define(function(require, exports, module) {
  var template = require('libs/artTemplate');
  var util = require('mods/util');

  var Pagelib = function(options, callback) {
    this.options = {
      page: true,
      article: true,
      community: true,
      form: true
    }
    this.init(options);
    if (typeof callback === 'function') {
        this.callback = callback;
    }
  };

  Pagelib.prototype.init = function(options) {
    this.initElements(options);
    this.initModal();
    this.getLink();
  }

  Pagelib.prototype.initElements = function(options) {
    this.options = $.extend(this.options, options);
    this.$modal = null;
    this.pageType = {
      list: []
    };

    if (this.options.page) {
      this.pageType.list[0] = {
        type: 'page',
         id: '1',
         name: "我的页面"
      }
    }
    if (this.options.article) {
      this.pageType.list[1] = {
        type: 'article',
        id: '3',
        name: "我的文章"
      }
    }
    if (this.options.community) {
      this.pageType.list[2] = {
        type: 'community',
        id: '6',
        name: "我的社区"
      }
    }
    if (this.options.form) {
      this.pageType.list[3] = {
        type: 'form',
        id: '8',
        name: "我的表单"
      }
    }

    this.modalTpl =  '<div class="modal-pagelib dp-modal modal fade" id="modal_pagelib">' +
                        '<div class="modal-dialog">' +
                          '<div class="modal-content">' +
                            '<div class="modal-header">' +
                            '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                            '<ul class="nav nav-tabs">' +
                            '{{each list as item i }}' +
                            '<li><a href="#tab_pane_{{item.type}}" data-toggle="tab" data-type="{{item.type}}">{{item.name}}</a></li>' +
                            '{{/each}}' +
                            '</ul>' +
                            '</div>' +
                            '<div class="tab-content">' +
                            '{{each list as item i }}' +
                            '<div class="tab-pane active" id="tab_pane_{{item.type}}">' +
                              '<div class="modal-body">' +
                                '<div class="table-wrap js-ajax-body">' +
                                '</div>' +
                              '</div>' +
                              '<div class="modal-footer">' +
                                '<div class="dp-page">' +
                                '</div>' +
                              '</div>' +
                            '</div>' +
                            '{{/each}}' +
                            '</div>' +
                          '</div>' +
                        '</div>' +
                      '</div>';
    this.ajaxTpl =  '<table class="dp-table dp-table-lightgray table">' +
                     '<colgroup>' +
                     '<col /><col style="width:200px;" /><col style="width:100px;" />' +
                     '</colgroup>' +
                     '<thead>' +
                     '<tr>' +
                     '<th class="dp-col-align-left" style="padding-left: 10px">标题</th><th>更新时间</th><th></th>' +
                     '</tr>' +
                     '</thead>' +
                     '<tbody>' +
                     '{{each data as item i}}' +
                     '<tr>' +
                     '<td class="dp-col-align-left" style="padding-left: 10px"><a>{{item.name}}</a></td><td>{{item.create_time | dateFormat:\'yyyy-MM-dd hh:mm:ss\'}}</td><td><a class="btn js-getlink" data-name="{{item.name}}" data-url="{{item.url}}">选取</a></td>' +
                     '</tr>' +
                     '{{/each}}' +
                     '</tbody>' +
                     '</table>';
  }

  // 初始化弹窗
  Pagelib.prototype.initModal = function() {
    var self = this;
    var render = template.render(self.modalTpl);
    var modal = render(self.pageType);
    var $modal;

    if($('#modal_pagelib').length < 1) {
        $('body').append(modal);
    }
    $modal = self.$modal = $('#modal_pagelib');
    $modal.find('.nav-tabs li:first-child a').trigger('click');
    $modal.modal('show');
    $modal.on('hidden.bs.modal', function() {
        $modal.remove();
    });

    if (self.options.page) {
      var postData = {
        page: 1,
        type: 1
      }
      var $target = $modal.find('#tab_pane_page .js-ajax-body');
      self.updata(postData, $target);
    }
    if (self.options.article) {
      var postData = {
        page: 1,
        type: 3
      }
      var $target = $modal.find('#tab_pane_article .js-ajax-body');
      self.updata(postData, $target);
    }
    if (self.options.community) {
      var postData = {
        page: 1,
        type: 6
      }
      var $target = $modal.find('#tab_pane_community .js-ajax-body');
      self.updata(postData, $target);
    }
    if (self.options.form) {
      var postData = {
        page: 1,
        type: 8
      }
      var $target = $modal.find('#tab_pane_form .js-ajax-body');
      self.updata(postData, $target);
    }
  }

  // Ajax更新数据
  Pagelib.prototype.updata = function(postData, target) {
    var self = this;
    var $page = target.parents('.tab-pane').find('.dp-page');
    var render = template.compile(self.ajaxTpl);
    // 日期格式化
    template.helper('dateFormat', function (date, format) {
      var date = new Date(Number(date * 1000));

      var map = {
        "M": date.getMonth() + 1, //月份
        "d": date.getDate(), //日
        "h": date.getHours(), //小时
        "m": date.getMinutes(), //分
        "s": date.getSeconds(), //秒
        "q": Math.floor((date.getMonth() + 3) / 3), //季度
        "S": date.getMilliseconds() //毫秒
      };
      format = format.replace(/([yMdhmsqS])+/g, function(all, t){
        var v = map[t];
        if(v !== undefined){
          if(all.length > 1){
            v = '0' + v;
            v = v.substr(v.length-2);
          }
          return v;
        }
        else if(t === 'y'){
          return (date.getFullYear() + '').substr(4 - all.length);
        }
        return all;
      });
      return format;
    });

    $.post('manage.php?ct=page&ac=getPageList&page=' + postData.page + '&type=' + postData.type, function(data) {
      data = JSON.parse(data);
      var _html = render(data);
      target.html(_html);
      util.pageHtml($page, 'pagecurrent', data.page, data.total, data.each, 6, 'dp-page');
      self.bindPageEvent($page, postData, target);
    });
  }

 // 绑定分页按钮点击事件
 Pagelib.prototype.bindPageEvent = function (dom, postData, target) {
    var self = this;

    dom.find('a').click(function () {
      var thisPage = $(this).data('page');
      postData.page = parseInt(thisPage);
      self.updata(postData, target);
    });
  }

  // 选取链接
  Pagelib.prototype.getLink = function() {
    var self = this;
    var $modal = self.$modal;
    var link = {
      name: '',
      url: ''
    };

    $modal.on('click', '.js-getlink', function() {
      var type = $modal.find('.modal-header .active a').data('type');
      link.name = $(this).data('name');
      link.url = $(this).data('url');
      link.type = type;
      $modal.modal('hide');
      self._callback(link);
    });
  }

  Pagelib.prototype._callback = function(link) {
      // 得到link执行回调函数
      if (this.callback) {
          this.callback(link);
      }
  }
  module.exports = Pagelib;
});