/**
 * Created by songmars on 15/12/29.
 */
define(['app'],function(app){
  app.factory('trolleyInfo',[function(){

    var goodsList=[];
    var mylist=getGoodsNumber();

    function getGoodsInfo(){
      return goodsList;
    }
    function  getGoodsNumber(){
      var numberList=[];
      for (var i=0;i<goodsList.length;i++){
        numberList.push(goodsList[i].join_number)
      }
      return numberList;
    }


    function addGoodsItem(goodsItem){

     if(isExisted(goodsItem)!=null){

       if(goodsItem.activity_type==2){
          if(goodsItem.join_number*10+mylist[isExisted(goodsItem)] <= goodsItem.remain_num) {
             mylist[isExisted(goodsItem)]=goodsItem.join_number*10+mylist[isExisted(goodsItem)];
          }
         
       }else if(goodsItem.activity_type==3){
        if(goodsItem.join_number+mylist[isExisted(goodsItem)] <= 10) {
         mylist[isExisted(goodsItem)]=goodsItem.join_number+mylist[isExisted(goodsItem)];
        } else {
          mylist[isExisted(goodsItem)] = 10;
        }
       } else {
          if(goodsItem.join_number+mylist[isExisted(goodsItem)] <= goodsItem.remain_num) {
            mylist[isExisted(goodsItem)]=goodsItem.join_number+mylist[isExisted(goodsItem)];
          } 
          
       }
       
       goodsList[isExisted(goodsItem)].join_number=mylist[isExisted(goodsItem)];
     }

      else{


       if(goodsItem.activity_type==2){
         goodsItem.join_number=goodsItem.join_number*10;
        }

        goodsList.push(goodsItem);
        mylist=getGoodsNumber();
     }

    }
    function removeGoodsItem(deleteList){

      for(var i in deleteList){
        for(var j in goodsList){
          if(deleteList[i]==goodsList[j]){
            goodsList.splice(j,1);
          }
        }
      }
    }

    function clear(){
      //goodsList = goodsList.splice(0,goodsList.length);
      goodsList = [];
    }

    //判断是否存在 存在则返回对应index
    function isExisted(goodsItem){
      for(var index in goodsList){
        //console.log('goodsItem.activity_id'+goodsItem.activity_id)
        if(goodsItem.activity_id==goodsList[index].activity_id){

          return index;
        }
      }
      return null;
    }

    //判断购物车是否为空
    function isTrolleyEmpty() {
      return (typeof(goodsList)=='undefined' || goodsList.length===0);
    }


    return{
      getGoodsInfo:getGoodsInfo,
      addGoodsItem:addGoodsItem,
      removeGoodsItem:removeGoodsItem,
      clear:clear,
      isTrolleyEmpty: isTrolleyEmpty
    }


    }])
});
//var goodsList=[
//  {join_number:0, isSelected:false,activity_id:1,activity_type:1,need_num:6666,remain_num:1999,goods_img:'img/pic1.jpg',goods_title:'Apple iPhone 6s plus64G 玫瑰金色 移动联通电信4G手机'},
//  {join_number:0, isSelected:false,activity_id:2,activity_type:2,need_num:5555,remain_num:3999,goods_img:'img/pic2.jpg',goods_title:'Apple 魅族 移动联通电信4G手机'},
//  {join_number:0, isSelected:false,activity_id:3,activity_type:1,need_num:4444,remain_num:2999,goods_img:'img/pic3.jpg',goods_title:'Apple 华为 荣耀8p 移动联通电信4G手机'},
//  {join_number:0, isSelected:false,activity_id:4,activity_type:2,need_num:3333,remain_num:998, goods_img:'img/pic4.jpg',goods_title:'Apple 小米7 移动联通电信4G手机'}
//
//];
