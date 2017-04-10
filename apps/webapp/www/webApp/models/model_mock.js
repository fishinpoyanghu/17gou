/**
 * Created by suiman on 16/1/7.
 */

/**
 *该文件用来存放测试用的假数据
 */

define(['app'], function (app) {
  app.factory('MockService', mockService);

  function mockService() {
    return {
      //商品接口
      getActivityList: getActivityList,
      getGoodsDetail: getGoodsDetail

    }

    function getActivityList(goods_type_id, key_word, order_key, order_type, from, count, status,
                             activity_type, onSuccess, onFailed, onFinal) {
      var list = [d1,d2,d3,d4,d5];
      return list;
    }

    function getGoodsDetail(activityId, onSuccess, onFailed, onFinal) {
      var list = [d1,d2,d3,d4,d5];
      var len = list.length;
      for(var i=0; i<len; i++) {
        var d = list[i];
        if(d.activity_id===activityId) {
          return onSuccess({data:{data:d}});
        }
      }
      return onSuccess({data:{data:d1}});
    }

  }
})

/*商品活动数据*/
var d1 = {
  code:0, msg:'正在进行',
  status:0,
  activity_id:1,
  goods_id:1,
  goods_img:['img/pic2.jpg'],
  goods_title:'Apple iPhone 6s plus64G 玫瑰金色 移动联通电信4G手机',
  need_num:100,
  remain_num:10,
  user_num:0,
  user_lucky_num:[]};

var d2 = {
  code:1,
  msg:'即将揭晓',
  status:1,
  activity_id:2,
  goods_id:2,
  goods_img:[
    "http://res.126.net/p/dbqb/one/140/140/8af2730e7603e7d8feb2a840e868b2bc.png",
    "http://res.126.net/p/dbqb/one/140/140/8af2730e7603e7d8feb2a840e868b2bc.png",
    "http://res.126.net/p/dbqb/one/140/140/8af2730e7603e7d8feb2a840e868b2bc.png"
  ],
  goods_title:'中国黄金 AU9999万足金50g薄片',
  remain_time: 480000,
  user_num:1,
  user_lucky_num:[42384238],
  new_activity_id:4};

var d3 = {
  code:2,
  msg:'已经揭晓',
  status:2,
  activity_id:3,
  goods_id:3,
  goods_img:[
    "http://res.126.net/p/dbqb/one/112/112/6bdd11c50920d89ac51e315c072c40e4.png",
    "http://res.126.net/p/dbqb/one/112/112/04a4d9d3277ddf77a726d2f8a18f3984.png",
    "http://res.126.net/p/dbqb/one/112/112/b246c1f56b1b10de718d21a6aa7349ac.png"
  ],
  goods_title:'Apple MacBook Pro 15.4英寸笔记本',
  lucky_uid:5438,
  lucky_user:'Ikle',
  lucky_user_num:5,
  lucky_num:78878,
  publish_time: '2015-12-17 10：22：31',
  user_num:0,
  user_lucky_num:[],
  new_activity_id:5};

var d4 = {
  code:0,
  msg:'正在进行',
  status:0,
  activity_id:4,
  goods_id:2,
  goods_img:[
    "http://res.126.net/p/dbqb/one/140/140/8af2730e7603e7d8feb2a840e868b2bc.png",
    "http://res.126.net/p/dbqb/one/140/140/d7863842d63b41641d54bc950b9dcc04.png",
    "http://res.126.net/p/dbqb/one/140/140/ea7f0892ce49c332e2280513ee94a439.png"
  ],
  goods_title:'中国黄金 AU9999万足金50g薄片',
  need_num:100,
  remain_num:20,
  user_num:0,
  user_lucky_num:[],
  };

var d5 = {
  code:0,
  msg:'正在进行',
  status:0,
  activity_id:5,
  goods_id:3,
  goods_img:[
    "http://res.126.net/p/dbqb/one/112/112/6bdd11c50920d89ac51e315c072c40e4.png",
    "http://res.126.net/p/dbqb/one/112/112/04a4d9d3277ddf77a726d2f8a18f3984.png",
    "http://res.126.net/p/dbqb/one/112/112/b246c1f56b1b10de718d21a6aa7349ac.png"
  ],
  goods_title:'Apple MacBook Pro 15.4英寸笔记本',
  need_num:100,
  remain_num:30,
  user_num:0,
  user_lucky_num:[],
  };
