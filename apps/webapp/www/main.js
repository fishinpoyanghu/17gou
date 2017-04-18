

require.config({
  //baseUrl: '/',
  baseUrl: 'webApp',
  waitSeconds: 0,
  urlArgs: "v=5634891",
  paths: {
    'angular': 'lib/ionic/js/angular/angular.min',
    'angularAnimate':   'lib/ionic/js/angular/angular-animate.min',
    'angularSanitize':  'lib/ionic/js/angular/angular-sanitize.min',
    'angularMessages' : 'lib/angular-messages',
    'uiRouter': 'lib/ionic/js/angular-ui/angular-ui-router.min',
    'ionic.bundle' : 'lib/ionic/js/ionic.bundle.min',
    'ionic' : 'lib/ionic/js/ionic.min',
    'ionicAngular' : 'lib/ionic/js/ionic-angular.min',
    'angular-async-loader': 'lib/async-loaders/angular-async-loader',
    'weChatJs':'lib/jweixin-1.0.0',
    'cordova':'cordova',
    'ngCordova':'lib/ngCordova/ng-cordova',
  },
  shim: {
    'angular' : {exports : 'angular'},
    'angularAnimate' : {deps: ['angular']},
    'angularSanitize' : {deps: ['angular']},
    'angularMessages' : {deps: ['angular']},
    'uiRouter' : {deps: ['angular']},
    'ionic' :  {deps: ['angular'], exports : 'ionic'},
    'ionicAngular': {deps: ['angular', 'ionic', 'uiRouter', 'angularAnimate', 'angularSanitize']},
    'cordova':{deps: ['ngCordova']},
  },
  priority: [
    'angular',
    'ionic'
  ],
  deps: [
    'bootstrap'
  ]
});


//requirejs.onError = function(err){
//  console.error('requirejs.onError：'+err);
//};
