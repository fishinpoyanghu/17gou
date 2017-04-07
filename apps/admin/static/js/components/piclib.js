/* 
* @Desc: 图片库弹窗
*/

define(function(require, exports, module) {
  var Uploador = require('components/fileuploador.js');
  var template = require('libs/artTemplate');
  var util = require('mods/util');

  var Piclib = function(opts, callback) {
    this.popType = {
      userPic: true,  // 有我的图片
      iconLib: '',    // 有图标库 material/app_nav
      fontLib: '',    // icon_font
      newPic: true,   // 有新图片
      uploadNum: '1', // 上传图片数量，'multi'表示可上传多张
      size: '',        // 尺寸限制
      tipsnew: '上传的图片小于1MB，格式为jpg，png。', // 新图片上传地方的提示文字
      tbl: 'pic'
    }
    this.init(opts);
    if (typeof callback === 'function') {
        this.callback = callback;
    }
  };

  Piclib.prototype.init = function(opts) {
    this.initElements(opts);
    this.initTemplate();
    this.usePic();
  };

  Piclib.prototype.initElements = function(opts) {
    this.popType = $.extend(this.popType, opts);
    this.$modal = null;
    
    var tplUserPic = '<div class="tab-pane active" id="tabpane_userpic">' +
                        '<div class="modal-body userpic js-ajax-body">' +
                        '</div>' +
                        '<div class="modal-footer">' +
                          '<div class="dp-page">' +
                          '</div>' +
                          '<button class="btn-large btn-orange btn js-confirm-use">确定使用</button>' +
                        '</div>' +
                      '</div>';
    var tplIconLib = '<div class="tab-pane" id="tabpane_iconlib">' +
                        '<div class="modal-body iconlib js-ajax-body">' +
                        '</div>' +
                        '<div class="modal-footer">' +
                          '<div class="dp-page">' +
                          '</div>' +
                          '<button class="btn-large btn-orange btn js-confirm-use">确定使用</button>' +
                        '</div>' +
                      '</div>';
    var tplFontLib = '<div class="tab-pane" id="tabpane_fontlib">' +
					    '<div class="modal-body fontlib js-ajax-body">' +
					    '</div>' +
					    '<div class="modal-footer">' +
					      '<div class="dp-page">' +
					      '</div>' +
					      '<button class="btn-large btn-orange btn js-confirm-use">确定使用</button>' +
					    '</div>' +
					  '</div>';
    var tplNewPic = '<div class="tab-pane" id="tabpane_newpic">' +
                      '<div class="modal-body upload-newpic">' +
                        '<h5>本地图片</h5>' +
                        '<div class="add-pic">' +
                          '<div class="item">' +
                            '<a class="btn-add js-upload"><i class="icon-plus dp-icon" style="margin-top:15px;"></i></a>' +
                          '</div>' +
                          '<p class="tips help-block" style="font-size:14px;">'+this.popType.tipsnew+'</p>' +
                        '</div>' +
                        '<h5 class="dn">网络图片</h5>' +
                        '<div class="dn">' +
                          '<span class="dp-input dib vam"><input type="text" class="form-control ipt-width-long" placeholder="请贴入网络图片地址" /></span>&nbsp;&nbsp;<button class="btn-default btn-small btn">提取</button>' +
                        '</div>' +
                      '</div>' +
                      '<div class="modal-footer">' +
                        '<button class="btn-large btn-orange btn js-confirm-upload">确定上传</button>' +
                      '</div>' +
                    '</div>';
    this.modalHtml = '<div class="modal-piclib dp-modal modal fade" id="modal_piclib">' +
                      '<div class="modal-dialog">' +
                        '<div class="modal-content">' +
                          '<div class="modal-header">' +
                            '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                            '<ul class="nav nav-tabs">' +
                              '{{if userPic}}' +
                                  '<li><a href="#tabpane_userpic" data-toggle="tab">我的图片</a></li>' +
                              '{{/if}}' +
                              '{{if iconLib}}' +
                                  '<li><a href="#tabpane_iconlib" data-toggle="tab">素材库</a></li>' +
                              '{{/if}}' +
                              '{{if fontLib}}' +
	                              '<li><a href="#tabpane_fontlib" data-toggle="tab">字体图标库</a></li>' +
	                          '{{/if}}' +
                              '{{if newPic}}' +
                                  '<li><a href="#tabpane_newpic" data-toggle="tab">新图片</a></li>' +
                              '{{/if}}' +
                            '</ul>' +
                          '</div>' +
                          '<div class="tab-content">' +
                            '{{if userPic}}' +
                                tplUserPic +
                            '{{/if}}' +
                            '{{if iconLib}}' +
                                tplIconLib +
                            '{{/if}}' +
                            '{{if fontLib}}' +
                            	tplFontLib +
	                        '{{/if}}' +
                            '{{if newPic}}' +
                                tplNewPic +
                            '{{/if}}' +
                          '</div>' +
                        '</div>' +
                      '</div>' +
                    '</div>';

   this.ajaxTpl = '<div class="tags-filter-list">' +
                     '{{if active.style != null}}' +
                        '<div class="tags-filter-item">' +
                        '<strong class="tags-label">风格：</strong>' +
                        '<div class="tags-content">' +
                          '{{each style as value i}}' +
                          '<span {{if active.style == i}} class="active" {{/if}} data-name="style" data-value="{{i}}">{{value}}</span>' +
                          '{{/each}}' +
                        '</div>' +
                        '</div>' +
                     '{{/if}}' +
                      '{{if active.color != null}}' +
                          '<div class="tags-filter-item">' +
                          '<strong class="tags-label">颜色：</strong>' +
                          '<div class="tags-content">' +
                            '{{each color as value i}}' +
                            '<span {{if active.color == i}} class="active" {{/if}} data-name="color" data-value="{{i}}">{{value}}</span>' +
                            '{{/each}}' +
                          '</div>' +
                          '</div>' +
                      '{{/if}}' +
                      '{{if active.classify != null}}' +
                          '<div class="tags-filter-item">' +
                          '<strong class="tags-label">类型：</strong>' +
                          '<div class="tags-content">' +
                            '{{each classify as value i}}' +
                            '<span {{if active.classify == i}} class="active" {{/if}} data-name="classify" data-value="{{i}}">{{value}}</span>' +
                            '{{/each}}' +
                          '</div>' +
                          '</div>' +
                      '{{/if}}' +
                    '</div>' +
                    '<div class="iconlib-list">' +
                    '<ul class="list">' +
                    '{{each pics as value i}}' +
                    '{{if value.is_font}}'+
                    '<li class="js-choice" data-url="{{value.url}}"><i class="{{value.url}}" style="font-size:48px;margin-top:6px;"></i>' +
                    '{{else}}'+
                    '<li class="js-choice" data-url="{{value.url}}"><img src="{{value.url}}"  />' +
                    '{{/if}}'+
                     '{{if value.size}}' +
                     '<p class="size">{{value.size}}</p>' +
                     '{{/if}}' +
                    '</li>' +
                    '{{/each}}' +
                    '</ul>' +
                    '</div>';
  };

  // 初始化弹窗
  Piclib.prototype.initTemplate = function() {
    var self = this;
    var render = template.render(self.modalHtml);
    var modal = render(self.popType);
    var $modal;

    if($('#modal_piclib').length < 1) {
        $('body').append(modal);
    }
    $modal = self.$modal = $('#modal_piclib');
    if (self.popType.userPic) {
      self.initUserPic();
    }
    if (self.popType.iconLib != '') {
      self.initIconLib();
    }
    if (self.popType.fontLib != '') {
        self.initFontLib();
      }
    if (self.popType.newPic) {
      self.uploadNewPic();
    }
    $modal.find('.nav-tabs li:first-child a').trigger('click');
    $modal.modal('show');
    $modal.on('hidden.bs.modal', function() {
        $modal.remove();
    });
  }

  // 上传新图片
  Piclib.prototype.uploadNewPic = function () {
        var self = this;
        var $modal = self.$modal;
        var $uploadBtn = $modal.find('.js-upload');
        var $uploadBtnWraper = $uploadBtn.parent('.item');
        var $confirmUpload = $modal.find('.js-confirm-upload');
        var uploador = new Uploador({
            accept: "image/jpeg,image/png,image/gif",
            submitUrl: '?c=pic&a=get_url&size=' + self.popType.size+'&uploadpath='+($('#upload_path').val()) 
        });

        $uploadBtn.css("position", "relative").append($(uploador.submitForm)), $(uploador.fileInput).addClass("input-file");
        // 添加要上传的图片
        uploador.on("uploadstart", function () {
            var tips = '<div class="item tmptips" style="position: relative;"><p class="tips" style="position:absolute;left: 0;bottom: 0;font-size: 14px;">上传中…</p></div>';
            $uploadBtnWraper.after(tips);
        }).on('finish', function (re) {
            re = JSON.parse(re);
            if (re.code == 0) {
                var preview = '<div class="picbox item">' +
                    '<div class="dp-thumbpic">' +
                    '<img src="' + re.data.base64 +'" data-id="' + re.data.id + '">' +
                    '</div>' +
                    '<a class="delete"><i class="g-icon-close g-icon"></i></a>' +
                    '</div>';

                $uploadBtnWraper.before(preview);   // 显示预览图
                if (self.popType.uploadNum == '1') {
                    $uploadBtnWraper.hide();
                }
                var $preview = $uploadBtnWraper.prev('.picbox');
                $preview.on('click', '.delete', function () {
                    // 删除预览图
                    $preview.remove();
                    if ($modal.find('.dp-thumbpic').length < 1) {
                        $uploadBtnWraper.show();
                    }
                })
                $uploadBtnWraper.next('.tmptips').remove();
            } else {
              $uploadBtnWraper.next('.tmptips').remove();
              alert(re.msg);
            }
        });

        // 确定上传
        $confirmUpload.on('click', function () {
            var ids = [];
            $modal.find('.dp-thumbpic img').each( function() {
                ids.push($(this).data('id'));
            })
            ids = ids.join(',');
            //截取类型
            var url = window.location.href;
            var cids = "";
            var exists  = url.indexOf('&cids=');
            if (exists  > 0) {
              var url = url.substring(exists);
              cids = url.substring(url.indexOf("=")+1);
            }
            if (ids.length > 0) {
                $.getJSON('?c=pic&a=save_pic', {"ids":ids,"cids":cids, "tbl":self.popType.tbl}, function(re) {
                     if (re.code == 0) {
                        if (re.data.length == 1) {
                          self._callback(re.data[0]);
                        } else {
                          self._callback(re.data);
                        }
                        $modal.modal('hide');
                     } else {
                        alert(re.msg);
                     }
                });
            } else {
                alert('请先选择图片!');
            }
        })
    }

  // 初始化图标库
  Piclib.prototype.initIconLib = function() {
    var self = this;
    var $modal = self.$modal;
    var $iconLibBody =  $modal.find('.iconlib');
    var postData = {
      name: self.popType.iconLib,
      active: {
        style: -1,
        color: -1,
        classify: -1
      },
      page: 1
    };
    
    if (postData.name == 'app_nav') {
    	postData.active.style = null;
    	postData.active.color = null;
    }

    self.updataPic(postData, $iconLibBody);
    $iconLibBody.on('click', '.tags-content span', function() {
      var $self = $(this);

      $self.addClass('active').siblings().removeClass('active');
      $iconLibBody.find('.tags-content .active').each(function (index, ele) {
        postData.active[$(ele).data('name')] = $(ele).data('value');
      });
      self.updataPic(postData, $iconLibBody);
    });
  }
  
  // 初始化字体图标库
  Piclib.prototype.initFontLib = function() {
	  var self = this;
	    var $modal = self.$modal;
	    var $fontLibBody =  $modal.find('.fontlib');
	    var postData = {
	      name: self.popType.fontLib,
	      active: {
	        classify: -1
	      },
	      page: 1
	    };

	    self.updataPic(postData, $fontLibBody);
	    $fontLibBody.on('click', '.tags-content span', function() {
	      var $self = $(this);

	      $self.addClass('active').siblings().removeClass('active');
	      $fontLibBody.find('.tags-content .active').each(function (index, ele) {
	        postData.active[$(ele).data('name')] = $(ele).data('value');
	      });
	      self.updataPic(postData, $fontLibBody);
	    });
  }

  // 初始化我的图片
  Piclib.prototype.initUserPic = function() {
    var self = this;
    var $modal = self.$modal;
    var $iconLibBody =  $modal.find('.userpic');
    var postData = {
      name: 'mypic',
      active: {
        classify: -1
      },
      page: 1
    };

    self.updataPic(postData, $iconLibBody);
    $iconLibBody.on('click', '.tags-content span', function() {
      var $self = $(this);

      $self.addClass('active').siblings().removeClass('active');
      $iconLibBody.find('.tags-content .active').each(function (index, ele) {
        postData.active[$(ele).data('name')] = $(ele).data('value');
      });
      self.updataPic(postData, $iconLibBody);
    });
  }

  // Ajax更新数据
  Piclib.prototype.updataPic = function(postData, target) {
    var self = this;
    var $page = target.parents('.tab-pane').find('.dp-page');
    var render = template.compile(self.ajaxTpl);
    postData = JSON.stringify(postData);

   $.post('?c=pic&a=get_pic_list&tbl='+self.popType.tbl, {'data':postData}, function(re) {
      re = JSON.parse(re);
      if (re.code == 0) {
        var data = re.data;
        var _html = render(data);
        var page = parseInt(data.page);
        var pageCount = parseInt(data.picnum);
        target.html(_html);
        util.pageHtml($page, 'pagecurrent', page, pageCount, 24, 6, 'dp-page');
        self.bindPageEvent($page, postData, target);
      } else {
//        alert(re.msg);
      }
    });
  }

  // 绑定分页按钮点击事件
  Piclib.prototype.bindPageEvent = function (dom, postData, target) {
    var self = this;

    dom.find('a').click(function () {
      var thisPage = $(this).data('page');
      postData = JSON.parse(postData);
      postData.page = parseInt(thisPage);
      self.updataPic(postData, target);
    });
  }

  // 确定使用图片
  Piclib.prototype.usePic = function() {
    var self = this;
    var $modal = self.$modal;
    var $confirmUse = $modal.find('.js-confirm-use');
    var picUrl = '';

    $modal.on('click', '.js-choice', function() {
      var $self = $(this);
      picUrl = $self.data('url');
      $self.addClass('checked').siblings('.js-choice').removeClass('checked');
    });
    $confirmUse.on('click', function() {
      $modal.modal('hide');
      self._callback(picUrl);
    });
  }

  Piclib.prototype._callback = function(url) {
      // 得到url执行回调函数
      if (this.callback) {
          this.callback(url);
      }
  }
  module.exports = Piclib;
});