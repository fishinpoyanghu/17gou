require.config({
	baseUrl:'http://s1.local.example.com/js/',
    urlArgs: "v=2016062717121123",
	paths: {
		jquery: 'jquery.min',
		bootstrap: 'bootstrap.min'
	},
	shim: {
		bootstrap:{
			deps: ['jquery'],
			exports: 'bootstrap'
		}
	}
});

require(['bootstrap'], function() {
	
});