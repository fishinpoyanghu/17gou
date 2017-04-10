# cordova-plugin-damaiwechat

A cordova plugin, a JS version of Alipay, Wechat Pay, Umeng share SDK

# Feature

Share title, description, image, and link to different plat(微信朋友圈,QQ,QZone,Sina...)

# Install

1. cordova plugin add https://github.com/wfxiaolong/ionic-plugin-damaiwechat.git

2. change many files by read :https://github.com/wfxiaolong/ionic-plugin-damaiwechat/blob/master/attention.txt

3. ```cordova build ios``` or ```cordova build android```

4. (iOS only) if your cordova version <5.1.1,check the URL Type using XCode

# Usage in ionic:

var dmplugin = window.dmwechat
dmplugin.echo("Hellow World", function(){
    // Todo

}, function(){
    // Todo

});

More Interface By Read:
https://github.com/wfxiaolong/ionic-plugin-damaiwechat/blob/master/www/dmwechat.js

#Result:
<img src="https://github.com/wfxiaolong/ionic-plugin-damaiwechat/blob/master/social-demo.png">

#LICENSE:
<a href="https://opensource.org/licenses/MIT">MIT</a>