/**
 * 通用弹窗
 */

define(function(require, exports, module) {
	
	require('bootstrap');
	
	var dialog = function(type, pText, callback, cancel_callback) {
	    var modal_html = modal_footer = '';
	    var text = {
	        title: '温馨提示',
	        msg: ''
	    };

	    $.extend(text, pText);
	    if (type == 'alert') {
	        modal_footer = '<button class="js-confirm btn-orange btn-small btn">确定</button>';
	    }
	    if (type == 'confirm') {
	        modal_footer = '<button class="js-cancel btn-default btn-small btn" data-dismiss="modal">取消</button><button class="js-confirm btn-orange btn-small btn">确定</button>';
	    }
	    modal_html = '<div class="dp-modal dp-modal-common modal fade" id="modal_common">' +
	        '<div class="modal-dialog modal-sm">' +
	        '<div class="modal-content">' +
	        '<div class="modal-header">' +
	        '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
	        '<ul class="nav nav-tabs">' +
	        '<li><h4>' + text.title + '</h4></li>' +
	        '</ul>' +
	        '</div>' +
	        '<div class="modal-body">' +
	        '<p class="msg">' + text.msg + '</p>' +
	        '</div>' +
	        '<div class="modal-footer">' + modal_footer +
	        '</div>' +
	        '</div>' +
	        '</div>' +
	        '</div>';

	    $('body').append(modal_html);
	    $modal = $('#modal_common');
	    $modal.modal('show');

	    $modal.find('.js-confirm').on('click', function (e) {
	        e.stopPropagation();
	        $modal.modal('hide');
	        $modal.on('hidden.bs.modal', function (e) {
	          $modal.remove();
	          if (typeof callback === 'function') {
	              callback();
	          }  
	        });              
	    });

	    $modal.on('hidden.bs.modal', function (e) {
	      $modal.remove();
	      
	      if (typeof cancel_callback === 'function') {
	    	  cancel_callback();
	      }
	    });
	}
	
	module.exports = dialog;
});
