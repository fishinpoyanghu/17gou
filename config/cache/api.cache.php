<?php

return array(
		'login' => array('sid'=>'user_cs', 'timeout'=>0), // 永不过期	login
		'mcode'  => array('sid'=>'user_cs', 'timeout'=>600), // 10分钟过期
		'forgotcode' => array('sid'=>'user_cs', 'timeout'=>600), // 10分钟过期
		'forgotsavekey' => array('sid'=>'user_cs', 'timeout'=>600), // 10分钟过期
		'reply-repeat' => array('sid'=>'user_cs', 'timeout'=>600), // 用来记录用户的上一条评论，不允许文字完全一样
		'reply-count' => array('sid'=>'user_cs', 'timeout'=>600), // 每个用户每天最多评论256条评论
		'wxjsapi' => array('sid'=>'user_cs', 'timeout'=>3600), // 微信接口参数
    'goods' => array('sid'=>'user_cs', 'timeout'=>86400), //商品相关
    'xxx' => array('sid'=>'user_cs', 'timeout'=>15), //商品相关
    'code' => array('sid'=>'user_cs', 'timeout'=>120), //商品相关
);
