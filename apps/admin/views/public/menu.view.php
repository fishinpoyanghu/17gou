<aside class="aside" >
    <nav class="aside-nav" style="background: #eef0f6;">
        <h4 class="title" style="margin-left:18px;height:35px;line-height:35px;">
            <span class="tit-txt"><a style="color:#979797;">概况</a></span>
        </h4>
        <h4 class="title">
            <span class="tit-txt"><i class="icon-user gray"></i> 用户管理</span>
        </h4>
        <ul class="links-list links">
          <li>
                <a href="?c=user&a=userList" <?php if($data['menu']=='userList'){?>class="current"<?php }?>><span class="link-txt">用户列表</span><span class="pointing">&gt;</span></a>
            </li>

           <!--  <li>
                <a href="?c=table&tbl=user.list&1459238389" <?php if($data['menu']=='user.list'){?>class="current"<?php }?>><span class="link-txt">普通用户</span><span class="pointing">&gt;</span></a>
            </li>
            <li>
                <a href="?c=table&tbl=user.wx_list&1459238389" <?php if($data['menu']=='user.wx_list'){?>class="current"<?php }?>><span class="link-txt">微信用户</span><span class="pointing">&gt;</span></a>
            </li> -->
        </ul>
        <h4 class="title">
            <span class="tit-txt"><i class="icon-comment-alt gray"></i> 消息管理</span>
        </h4>
        <ul class="links-list links">
            <li>
                <a href="?c=activity&a=sysMsg" <?php if($data['menu']=='sysMsg'){?>class="current"<?php }?>><span class="link-txt">系统消息</span><span class="pointing">&gt;</span></a>
            </li>
            <li>
                <a href="?c=activity&a=msg" <?php if($data['menu']=='msg'){?>class="current"<?php }?>><span class="link-txt">私信消息</span><span class="pointing">&gt;</span></a>
            </li>
            <li>
                <a href="?c=table&tbl=msg.notify&1459238389" <?php if($data['menu']=='msg.notify'){?>class="current"<?php }?>><span class="link-txt">通知消息</span><span class="pointing">&gt;</span></a>
            </li>
        </ul>
        <h4 class="title">
            <span class="tit-txt">商品管理</span>
        </h4>
        <ul class="links-list links">
           <li>
                <a href="?c=team&a=teamgoodsList" <?php if($data['menu']=='teamList'){?>class="current"<?php }?>><span class="link-txt">拼团商品列表</span><span class="pointing">&gt;</span></a>
            </li>
            <li>
                <a href="?c=goods&a=goodsList" <?php if($data['menu']=='goodsList'){?>class="current"<?php }?>><span class="link-txt">商品列表</span><span class="pointing">&gt;</span></a>
            </li>
            <li>
                <a href="?c=goods&a=classify" <?php if($data['menu']=='classify'){?>class="current"<?php }?>><span class="link-txt">商品分类</span><span class="pointing">&gt;</span></a>
            </li>
             <li>
                <a href="?c=team&a=teamclassify" <?php if($data['menu']=='teamclassify'){?>class="current"<?php }?>><span class="link-txt">拼团分类</span><span class="pointing">&gt;</span></a>
            </li>
             
            <li>
                <a href="?c=activity&a=redList" <?php if($data['menu']=='redList'){?>class="current"<?php }?>><span class="link-txt">红包列表</span><span class="pointing">&gt;</span></a>
            </li>
           
        </ul>
        <h4 class="title">
            <span class="tit-txt">营销推广</span>
        </h4>
        <ul class="links-list links">
            <li>
                <a href="?c=goods&a=banner" <?php if($data['menu']=='banner'){?>class="current"<?php }?>><span class="link-txt">首页banner</span><span class="pointing">&gt;</span></a>
            </li>
            <li>
                <a href="?c=goods&a=pcbanner" <?php if($data['menu']=='pcbanner'){?>class="current"<?php }?>><span class="link-txt">pc端banner</span><span class="pointing">&gt;</span></a>
            </li>
            <li>
                <a href="?c=goods&a=showbanner" <?php if($data['menu']=='showbanner'){?>class="current"<?php }?>><span class="link-txt">晒单页banner</span><span class="pointing">&gt;</span></a>
            </li>
             
            <li>
                <a href="?c=goods&a=pointRule" <?php if($data['menu']=='pointRule'){?>class="current"<?php }?>><span class="link-txt">积分规则</span><span class="pointing">&gt;</span></a>
            </li>
            <li>
                <a href="?c=goods&a=lottery" <?php if($data['menu']=='lottery'){?>class="current"<?php }?>><span class="link-txt">抽奖设置</span><span class="pointing">&gt;</span></a>
            </li>
            <li>
                <a href="?c=goods&a=lotteryRecord" <?php if($data['menu']=='lotteryRecord'){?>class="current"<?php }?>><span class="link-txt">抽奖记录</span><span class="pointing">&gt;</span></a>
            </li>
            
        </ul>
         <?php if($data['login_user']['is_super']){?>
        <h4 class="title">
            <span class="tit-txt">活动管理</span>
        </h4>
        <ul class="links-list links">
            
            <li>
                <a href="?c=activity" <?php if($data['menu']=='activity'){?>class="current"<?php }?>><span class="link-txt">活动列表</span><span class="pointing">&gt;</span></a>
            </li>
             <li>
                <a href="?c=team&a=activity" <?php if($data['menu']=='teamactivity'){?>class="current"<?php }?>><span class="link-txt">百团大战</span><span class="pointing">&gt;</span></a>
            </li>
            <li>
                <a href="?c=activity&a=assignList" <?php if($data['menu']=='assignList'){?>class="current"<?php }?>><span class="link-txt">中奖名单</span><span class="pointing">&gt;</span></a>
            </li>
            <li>
                <a href="?c=custom" <?php if($data['menu']=='custom'){?>class="current"<?php }?>><span class="link-txt">订单设置</span><span class="pointing">&gt;</span></a>
            </li>
             
            
        </ul>

            <?php  }?>
        <h4 class="title">
            <span class="tit-txt">订单管理</span>
        </h4>
        <ul class="links-list links">
            <li>
                <a href="?c=activity&a=order" <?php if($data['menu']=='order'){?>class="current"<?php }?>><span class="link-txt">订单列表</span><span class="pointing">&gt;</span></a>
            </li>
            <li>
                <a href="?c=team&a=order" <?php if($data['menu']=='teamorder'){?>class="current"<?php }?>><span class="link-txt">团购订单列表</span><span class="pointing">&gt;</span></a>
            </li>
              <li>
                <a href="?c=activity&a=sharelist" <?php if($data['menu']=='sharelist'){?>class="current"<?php }?>><span class="link-txt">已开奖列表</span><span class="pointing">&gt;</span></a>
            </li>
             <?php if($data['login_user']['is_super']){?>
            <li>
                <a href="?c=team&a=baituanorder" <?php if($data['menu']=='baituan'){?>class="current"<?php }?>><span class="link-txt">旧百团订单列表</span><span class="pointing">&gt;</span></a>
            </li>

            <li>
                <a href="?c=activity&a=show" <?php if($data['menu']=='show'){?>class="current"<?php }?>><span class="link-txt">晒单审核</span><span class="pointing">&gt;</span></a>
            </li>
            <li>
                <a href="?c=activity&a=comment" <?php if($data['menu']=='comment'){?>class="current"<?php }?>><span class="link-txt">评论审核</span><span class="pointing">&gt;</span></a>
            </li> <?php  }?>
        </ul>
       
          <?php if($data['login_user']['is_super']){?>
        <h4 class="title">
            <span class="tit-txt">师徒关系管理</span>
        </h4>
        <ul class="links-list links">
            <li>
                <a href="?c=activity&a=distribution" <?php if($data['menu']=='distribution'){?>class="current"<?php }?>><span class="link-txt">师徒关系</span><span class="pointing">&gt;</span></a>
            </li>
            <li>
                <a href="?c=activity&a=commission" <?php if($data['menu']=='commission'){?>class="current"<?php }?>><span class="link-txt">师徒关系设置</span><span class="pointing">&gt;</span></a>
            </li>
    <!--         <li>
                <a href="?c=activity&a=cash" <?php if($data['menu']=='cash'){?>class="current"<?php }?>><span class="link-txt">提现申请</span><span class="pointing">&gt;</span></a>
            </li> -->

            <li>
                <a href="?c=activity&a=cashlist" <?php if($data['menu']=='cashlist'){?>class="current"<?php }?>><span class="link-txt">提现列表</span><span class="pointing">&gt;</span></a>
            </li>
        </ul>
        <?php } ?>
        <h4 class="title">
            <span class="tit-txt">其他设置</span>
        </h4>
        <ul class="links-list links">
        <li>
                <a href="?c=sys&a=sysset" <?php if($data['menu']=='sysset'){?>class="current"<?php }?>><span class="link-txt">系统设置</span><span class="pointing">&gt;</span></a>
        </li>
            <li>
                <a href="?c=finance&a=notice" <?php if($data['menu']=='notice'){?>class="current"<?php }?>><span class="link-txt">公告管理</span><span class="pointing">&gt;</span></a>
            </li>
            <li>
                <a href="?c=activity&a=rule" <?php if($data['menu']=='rule'){?>class="current"<?php }?>><span class="link-txt">规则设置</span><span class="pointing">&gt;</span></a>
            </li>
            <li>
                <a href="?c=activity&a=question" <?php if($data['menu']=='question'){?>class="current"<?php }?>><span class="link-txt">常见问题</span><span class="pointing">&gt;</span></a>
            </li>
            <li>
                <a href="?c=activity&a=share" <?php if($data['menu']=='share'){?>class="current"<?php }?>><span class="link-txt">分享文案</span><span class="pointing">&gt;</span></a>
            </li>
            <?php if($data['login_user']['is_super']){?>
            <li>
                <a href="?c=finance" <?php if($data['menu']=='finance'){?>class="current"<?php }?>><span class="link-txt">财务信息</span><span class="pointing">&gt;</span></a>
            </li>
             <li>
                <a href="?c=finance&a=refundlist" <?php if($data['menu']=='refundlist'){?>class="current"<?php }?>><span class="link-txt">退款列表</span><span class="pointing">&gt;</span></a>
            </li>
             <li>
                <a href="?c=finance&a=achievement" <?php if($data['menu']=='achievement'){?>class="current"<?php }?>><span class="link-txt">个人金额</span><span class="pointing">&gt;</span></a>
            </li>
             
            <?php }?>
            <li>
                <a href="?c=finance&a=yijian" <?php if($data['menu']=='yijian'){?>class="current"<?php }?>><span class="link-txt">意见反馈</span><span class="pointing">&gt;</span></a>
            </li>
	        <li>
                <a href="?c=finance&a=version" <?php if($data['menu']=='version'){?>class="current"<?php }?>><span class="link-txt">版本控制</span><span class="pointing">&gt;</span></a>
            </li>
        </ul>
    </nav>
</aside>