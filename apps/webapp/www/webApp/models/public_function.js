/**
 * Created by suiman on 17/2/14.
 */

define(['app', 'utils/httpRequest'], function(app) {
	app.factory('publicFunction', ['httpRequest', function(httpRequest) {
		return {
			formatTime: formatTime,
            unique: unique
		}

        /**
         * 数组去重的方法
         * @param arr   需要被去重的数组
         * @param keyword   如果数组的元素是对象，需要传入的键名，通过这个键名来去重
         */
        function unique(arr, keyword) {
            var newArr = [],
                newObj = {};
            for(var i = 0; i < arr.length; i++) {
                keyword?objArr():simpleArr();
            }
            function simpleArr() {
                if(!newObj[arr[i]]) {
                    newArr.push(arr[i]);
                    newObj[arr[i]] = 1;
                }
            }
            function objArr() {
                if(!newObj[arr[i][keyword]]) {
                    newArr.push(arr[i]);
                    newObj[arr[i][keyword]] = 1;
                }
            }
            return newArr;
        }

		/**
		 * 获取首页广告
		 *
		 * 获取活动信息
		 * @param time   数字，单位:秒
		 * @param fmt    "yyyy-MM-dd"  "yyyy-MM-dd HH:mm:ss"
		 */
		function formatTime(time, fmt) {
			time*=1000;
			var day = new Date(time);
			var o = {
				"M+": day.getMonth() + 1, //月份 
				"d+": day.getDate(), //日 
				"h+": day.getHours(), //小时 
				"m+": day.getMinutes(), //分 
				"s+": day.getSeconds(), //秒 
				"q+": Math.floor((day.getMonth() + 3) / 3), //季度 
				"S": day.getMilliseconds() //毫秒 
			};
			if(/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (day.getFullYear() + "").substr(4 - RegExp.$1.length));
			for(var k in o)
				if(new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
			return fmt;
		}

	}]);

})