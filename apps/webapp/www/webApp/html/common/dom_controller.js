/**
 * Created by luliang on 2016/2/15.
 */
(function(main){

  function setAttr(dom,name,value){
    dom.setAttribute(name,value);
  }

  function getAttr(dom,name){
    return dom.getAttribute(name);
  }

  function pressAddCartBtn(dom){
    dom.className += ' iconfont-shopcart';
    dom.className = dom.className.replace('iconfont-shopcart-outline','');
  }

  function realseAddCartBtn(dom){
    dom.className += ' iconfont-shopcart-outline';
    dom.className = dom.className.replace('iconfont-shopcart','');
  }

  function changeAddCartState(dom,state){
    if(state){
      pressAddCartBtn(dom);
    }else{
      realseAddCartBtn(dom);
    }
  }

  main.domController = {
    'changeAddCartState' : changeAddCartState,
    'setAttr' : setAttr,
    'getAttr' : getAttr
  }
})(window);
