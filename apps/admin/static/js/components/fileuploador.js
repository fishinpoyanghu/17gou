define(function (require, exports, module) {
	
	var events = require('mods/events');
	var counter = 0;
	function Uploador(opts){
		this.init(opts);
	}
	Uploador.prototype = events();
	Uploador.prototype.init = function (opts){
		this.initElements(opts);
		if(window.FormData){
			this.mode = 'xhr2';
			this.initXhr();
		} else {
			this.mode = 'iframe';
			this.initIframe();
		}
		this.initFileInput();
		this.bindEvents();
	};
	Uploador.prototype.initElements = function (opts){
		this.count = counter++;
		this.submitUrl = opts.submitUrl;
		this.callback = opts.callback && top.window[opts.callback];
		this.name = opts.name || 'file';
		this.accept = opts.accept || '';
		this.maxSize = opts.maxSize || 3; // Mb
		this.state = '';
	};
	Uploador.prototype.bindEvents = function (){
		var self = this;
		this.fileInput.onchange = function (){ // 一次只上传一个 多文件上传兼容性不好
			if(self.mode === 'xhr2'){
				var file = this.files[0];
				if(file.size > self.maxSize * 1000000){
					self.emit('sizeError', file);
					return;
				}
				if(file.type.indexOf('image') === -1){
					self.emit('typeError', file);
					return;
				}
			} else if(window.ActiveXObject){
				try{
					var temp = new ActiveXObject('Scripting.FileSystemObject');  
					var size = parseInt(temp.getFile(this.value).size);
					if(size > self.maxSize * 1000000){
						self.emit('sizeError', file);
						return;
					}
				} catch(e) {}
			}
			self.upload();
		};
		this.on('sizeError', function (){
			this.emit('faild');
		});
		this.on('uploadstart', function (){
			this.state = 'uploading';
		});
		this.on('finish', function (){
			this.state = 'finish';
		});
	};
	Uploador.prototype.initFileInput = function (){
		var submitForm = document.createElement('form');
		submitForm.method = 'POST';
		submitForm.action = this.submitUrl;
		submitForm.target = 'uploador' + this.count;
		submitForm.enctype = 'multipart/form-data';
		submitForm.encoding = 'multipart/form-data'; // ie

		var fileInput = document.createElement('input');
		// fileInput.multiple = 'multiple';
		fileInput.type = 'file';
		fileInput.name = this.name;
		fileInput.id = 'file' + this.count;
		fileInput.accept = this.accept || '';
		fileInput.style.cssText = 'display: block;width: 100%;height: 100%;opacity: 0;filter: alpha(opacity=0);position: absolute;right: 0;top: 0;';
		submitForm.appendChild(fileInput);

		this.fileInput = fileInput;
		this.submitForm = submitForm;
	};
	Uploador.prototype.initIframe = function (){
		var self = this;
		var iframe = document.createElement('iframe');
		// iframe.src = 'javascript:void((function(){document.open();document.domain="'+ document.domain + '";document.close()})())';
		iframe.name = 'uploador' + this.count;
		iframe.style.display = 'none';
		function onloadCallback(){
			// var iw = self.iframe.contentWindow || self.iframe.contentDocument.window;
			if(self.state === 'uploading'){
				self.callback && seclf.callback.call(top);
				self.emit('finish');
			}
		}
		if(iframe.attachEvent){ // ie中直接设置onload无效
			iframe.attachEvent('onload', onloadCallback);
		} else {
			iframe.onload = onloadCallback;
		}
		document.body.appendChild(iframe);
		this.iframe = iframe;
	};
	Uploador.prototype.initXhr = function (){
		var self = this;
		var xhr = new XMLHttpRequest();

		xhr.onload = function (){
			if(xhr.status === 200){
				self.callback && self.callback.call(top);
				self.emit('finish', this.responseText);
			} else {
				self.emit('faild');
			}
		};
		xhr.onerror = function (){
			self.emit('faild');
		};
		xhr.onabort = function (){
			self.emit('abort');
		};
		xhr.upload.onprogress = function (e){
			if(e.lengthComputable){
				self.emit('progress', event.loaded / event.total * 100 | 0);
			}
		}
		this.xhr = xhr;
	};
	Uploador.prototype.iframeUp = function (){
		// submit时插入iframe 为表单添加target属性，指向动态插入的iframe
		this.submitForm.submit();
	};
	Uploador.prototype.xhr2Up = function (){
		var file = this.fileInput.files[0];
		if(!file){
			return;
		}
		var formData = new FormData();
		formData.append('filename', file); // todo :: multiple
		this.xhr.open('POST', this.submitUrl);
		this.xhr.send(formData);
	};
	Uploador.prototype.upload = function (){
		this.emit('uploadstart');
		this[this.mode + 'Up']();
	};
	module.exports = Uploador;
});