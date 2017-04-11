define(["avalon",'http/http-factory','jquery'], function(avalon,httpFactory) {


  var timeID ;
  function timeCount() {
      var countNode = $('.js-countdowm');
      if(countNode.length == 0) {
          clearInterval(timeID)
          return;
      }
      countNode.each(function(index, el) {
          el = $(el);
          var endTime = Number(el.attr('end_time'));
          if(endTime == NaN) {
              el.removeClass('js-countdowm');
              return;
          }
          var remainTime = endTime - (+new Date());
          //测试
//        remainTime = 3*60;
          //end 测试
          if(remainTime<=0) {
            el.html('请稍后，正在计算...');
            //倒计时结束
            el.removeClass('js-countdowm');
            countdowm.callback(el.attr('activity_id'))
            return;
          }
          showCurrentTime(el,remainTime);

      });

  }


  function showCurrentTime(el,time) {
    el.html(formatTime(time));
  }

  function formatTime(time) {
    var ms = (time>=0) ? time : 0;
    var s = parseInt(ms/1000);
    var m = parseInt(s/60);
    var h = parseInt(m/60);
    var d = parseInt(h/24);
    var randomNum = 0;
    var strNum = '<p>';
    for(var i = 0; i < 8; i++){
    	randomNum = parseInt(Math.random()*10);
    	strNum += '<b class="scrollNum">' + randomNum + '</b>';
    }
    strNum += '</p>';
    h = h%24;
    m = m%60;
    s = s%60;
    ms = ms%1000;
    var str_d = (d===0) ? '' : (d+'天 ');
    var str_h = (str_d==='' && h===0) ? '' : (addZero(h)+': ');
    var str_m = (str_h==='' && m===0) ? '00: ' : (addZero(m)+': ');
    var str_s = (str_m==='' && s===0) ? '00: ' : (addZero(s)+': ');
    var str_ms = addZero((ms/10).toFixed(0));
    if(Number(str_ms) >= 100) str_ms = '99';
    return '<p><b >'+ str_m.substr(0,1)+'</b><b >'+ str_m.substr(1,1)+'</b><span>:</span><b >'+ str_s.substr(0,1)+'</b><b >'+ str_s.substr(1,1)+'</b><span>:</span><b >'+ str_ms.substr(0,1)+'</b><b >'+ str_ms.substr(1,1)+'</b></p>'
//  			 + strNum;
  }

  function addZero(num) {
    if(num<10) {
      return ('0' + num)
    }else {
      return ('' + num);
    }
  }

  

  var countdowm = {
    start:function(callback) {
      this.callback = callback;
      clearInterval(timeID)
//    timeID = setInterval(timeCount,100);
			timeCount()
    }
   
  }
  return countdowm

})
