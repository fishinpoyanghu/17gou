<?php 
$menu_mod = Factory::getMod('menu');
$top_menu_list = $menu_mod->getTopMenuList();
$top_curr = $top_menu_list[$global_cfg['nav']];

$left_menu_list = $menu_mod->getLeftMenuList($global_cfg['nav']);

if (empty($logo_title) && C('appid')) {
    $pub_mod = Factory::getMod('pub');
    $pub_mod->init('admin', 'app', 'appid');
    $app_curr = $pub_mod->getRow(C('appid'));
    if ($app_curr) $logo_title = $app_curr['name'];
}
// dump($left_menu_list);exit;
?>
<header class="header">
    <div class="header-inner inner">
        <h1 class="header-logo">
            <a href="?c=app" class="logo" title="BigH5"><span class="hidetxt"><?php echo $data['login_user']['appname']?></span></a>
        </h1>
        <nav class="header-nav">
            <ul class="clearfix">
                <li class="nbtn current"><a href="?c=goods&a=goodsList">首页</a></li>
            </ul>
        </nav>
        <div class="header-right user">
            <i class="icon-user" style="margin-right:3px;"></i><span class="username"><?php echo $data['login_user']['name']?></span>
            |
            <div class="settings dp-dropdown dropdown">
                <button class="btn" type="button" id="settings_dropdown" data-hover="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="icon-cog" style="margin-right: 3px;"></i>设置<i class="icon-caret-down"></i></button>
                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="settings_dropdown">
                    <?php if($data['login_user']['is_super']){?>
                    <li><a href="?c=index&a=adminList"><i class="dp-icon-signout dp-icon"></i>管理设置</a></li>
                    <?php }?>
                    <li><a href="?c=index&a=password"><i class="dp-icon-signout dp-icon"></i>修改密码</a></li>
                    <li><a href="?c=index&a=logout"><i class="dp-icon-signout dp-icon"></i>退出</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>