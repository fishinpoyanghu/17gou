require.config({
    baseUrl: 'js/',
    waitSeconds: 0,
    urlArgs: "v=201607211731219",
    paths: {
        'avalon'               : 'lib/avalon/avalon',
        'mmState'              : 'lib/avalon/mmState',
        'mmRouter'             : 'lib/avalon/mmRouter',
        'mmPromise'            : 'lib/mmRequest/public/mmPromise',
        'mmHistory'            : 'lib/avalon/mmHistory',
        'domReady'             : 'lib/domReady/domReady',
        'jquery'               : 'lib/jquery/dist/jquery-1.11.3.min',
        'mmRequest'            : 'lib/mmRequest/public/mmRequest',
        'oniui'                : 'lib/oniui/oniui',
        'app'                  : 'app',
        'bootstrap'            : 'lib/bootstrap/dist/js/bootstrap.min',
        'layer'                : 'lib/layer/layer',
        'hm'                   : 'public/hm',
        'uaredirect'           : 'page/index/uaredirect',
        'slider'               : 'page/index/slider',
        'common.min'           : 'public/common.min',
        'jquery.page'          : 'public/jquery.page',
        'ajaxfunctionMain.min' : 'public/ajaxfunctionMain.min',
        'footer_header_new.min': 'public/footer_header_new.min',
        'index.min'            : 'page/index/index.min',
        'lazyload'  : 'public/jquery.lazyload.min',
        'jquery.cookies'       : 'public/jquery.cookies.2.2.0',
        'cartOrder':'page/cartOrder/cartOrder',//xiang 首页增加此js因为前台商品加减需要用到
    },
    shim: {
        avalon: {
            exports: 'avalon' //exports值（输出的变量名），表明这个模块外部调用时的名称；
        },
        mmState: { 
            deps:['avalon'] //deps数组，表明该模块的依赖性。
        },
        mmRouter: { 
            deps:['avalon']
        },
        bootstrap: { 
            deps:['jquery']
        },
        slider: { 
            deps:['jquery']
        },
        app: {
          deps: ['mmState'],
          exports: 'app'
        },
        layer:{
            deps:['jquery']
        },
        lazyload: { 
            deps:['jquery']
        }
    },
    deps: ['app']
});