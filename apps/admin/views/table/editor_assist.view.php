<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
</head>
<body>
	
	<!-- 
	跨域上传文件时，服务端要返回一个<iframe>，这个<iframe>将引用此页面
	文件服务器和上传客户端可能不是一个域，无法相互访问js方法
	而此页面和客户端是一个域，可以通过 window.parent.parent.fn 访问客户端js方法 
	该页面必须和编辑页面在同一个目录下
	-->

	<script type="text/javascript">
		function consoleLog(info){
			if(window.console && console.log && typeof console.log === 'function'){
				console.log('wangEditor/wangEditor_uploadImg_assist.html提示：', info);
			}
		}
		window.onload = function(){
			//提示页面已经加载
			consoleLog('wangEditor_uploadImg_assist.html加载完成！');
			if(window.parent && window.parent.parent && window.parent.parent.wangEditor_uploadImgCallback){
				//调用 window.parent.parent 的回调函数
				//wangEditor_uploadImgCallback 是上传页面定义的一个全局函数
				if(window.location.hash){
					var hash = window.location.hash.slice(1);
					//提示url
					consoleLog('获取的hash为 “' + hash + '”！');
					//提示开始调用wangEditor_uploadImgCallback方法
					consoleLog('wangEditor_uploadImg_assist.html页面准备开始调用父页面的wangEditor_uploadImgCallback方法！');
					window.parent.parent.wangEditor_uploadImgCallback( hash );	
					//提示已经调用了wangEditor_uploadImgCallback方法
					consoleLog('wangEditor_uploadImg_assist.html页面已经调用完了父页面的wangEditor_uploadImgCallback方法！');
				}else{
					alert('没有通过hash找到图片url网址！');
				}
			}else{
				alert('未找到 window.parent.parent.wangEditor_uploadImgCallback 方法！');
			}
		};
	</script>
</body>
</html>