
window.UEDITOR_HOME_URL = window.STATIC_LIST + '/js/libs/ueditor/'; // 富文本编辑器组件url
window.UEDITOR_SERVER_URL = location.pathname + '?c=goods&a=editorUpload';  // 富文本编辑器统一请求服务器接口路径
seajs.config({
    base: $dp_global_config.js_domain,
    alias: {
        'jquery' : 'libs/jquery.min',
        'jquery.ui' : 'libs/jquery.ui',
        'jq.uploader' : 'libs/jquery.uploader.js',
        'jq.picPreview' : 'libs/jquery.picPreview.js',
        'bootstrap' : 'libs/bootstrap.min.js',
        'wangEditor' : 'libs/wangEditor.min.js',
		"dialog": "mods/dialog.js",	//通用弹窗
		"common": "components/common",	//通用交互
		"piclib": "components/piclib",  //素材库弹窗
		"pagelib": "components/pagelib",	//页面库弹窗
		"artdialog": "libs/dialog-plus-min.js",// artDialog
		"jq.form": "libs/jquery.form.js", //
        "echarts": "libs/echarts/echarts.common.min.js"//echarts图表
    },
    paths: {
        //'module_form' : '/plugins/modules', // form的目录
        //'page_form': '/plugins/pages',
		//'css': '/system/admin/static/css'
    },
    preload: ['jquery'],
    debug: true
});
seajs.use('common');
