<?php
/**
 * Created on 2011-6-16
 *
 * haduo/home缓存类型配置
 * 
 * 缓存类型 => array('sid'=>缓存服务器名称, 'timeout'=>过期时间,0表示永不过期)
 * 
 * @author wangyihuang
 */

return array(
	'login'      => array('sid'=>'user_cs', 'timeout'=>86400), /// 24个小时，正式环境登录缓存必须独立一个缓存服务
	'online'     => array('sid'=>'user_cs', 'timeout'=>600), /// 用户在线   10分钟不活动 就不在线
	'u'          => array('sid'=>'user_cs', 'timeout'=>0), /// 永不过期    用户基本信息
	'ue'         => array('sid'=>'user_cs', 'timeout'=>0), /// 永不过期	用户扩展信息
	'ut'         => array('sid'=>'user_cs', 'timeout'=>0), /// 永不过期	用户交友信息
	'ei'         => array('sid'=>'user_cs', 'timeout'=>0), /// 永不过期	用户Email索引信息
	'wbi'        => array('sid'=>'user_cs', 'timeout'=>0), /// 永不过期	用户weibo索引信息
	'mi'         => array('sid'=>'user_cs', 'timeout'=>0), /// 永不过期	用户Mobile索引信息
	'micode'     => array('sid'=>'user_cs', 'timeout'=>1800), /// 永不过期	用户手机验证码缓存信息
	'uni'        => array('sid'=>'user_cs', 'timeout'=>0), /// 永不过期	用户名称索引信息
	'uv'         => array('sid'=>'user_cs', 'timeout'=>0), /// 永不过期 用户访客信息
	'feed'       => array('sid'=>'user_cs', 'timeout'=>0), /// 永不过期 用户FEED信息
	'feed2'      => array('sid'=>'user_cs', 'timeout'=>0), /// 永不过期 新用户FEED信息
	'feed_group' => array('sid'=>'user_cs', 'timeout'=>0), /// 永不过期 新用户FEED GROUP信息
	'cmt'        => array('sid'=>'user_cs', 'timeout'=>0), /// 永不过期 用户FEED信息

    'remail'     =>array('sid'=>'user_cs','timeout'=>1800), //邮箱注册验证
    'femail'     =>array('sid'=>'user_cs','timeout'=>1800), //找回密码密码验证码
    'vremail'    =>array('sid'=>'user_cs','timeout'=>1800), // 邮箱验证服务


	'msgrp'  => array('sid'=>'user_cs', 'timeout'=>0, 'off'=>true),
	'msg'    => array('sid'=>'user_cs', 'timeout'=>0, 'off'=>true),
	'msgsys' => array('sid'=>'user_cs', 'timeout'=>0, 'off'=>true),
	'sysnew' => array('sid'=>'user_cs', 'timeout'=>600, 'off'=>true), /// 用来保存最新系统消息
	
	'area'     => array('sid'=>'user_cs', 'timeout'=>0, 'off'=>true),
	'city'     => array('sid'=>'user_cs', 'timeout'=>0, 'off'=>true),
);
