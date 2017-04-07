/* 
* @Desc: 通用交互
*/

define(function(require, exports, module) {
  require('bootstrap'); 

  $('[data-hover="dropdown"]').on('mouseenter', function() {
    $(this).dropdown('toggle');
  }).parents('.dropdown').on('mouseleave', function() {
      $(this).find('[data-hover="dropdown"]').dropdown('toggle');
    });
});