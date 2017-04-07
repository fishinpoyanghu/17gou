<title><?php echo isset($title) && $title ? $title : '亿七购后台';?></title>
<meta name="keywords" content="微信第三方平台，微信营销平台，免费微信平台，微信代运营，微信托管，微营销，微信定制开发，微商城，微信定制，微信运营，手机运营活动" />
<meta name="description" content="BH是最早从事微信公众号开发的免费微信第三方平台，致力于提供基于H5的全套移动互联网营销推广方案，解决企业和个人产品的流量问题和技术开发难题，目前已经拥有超过几十万的企业和个人用户，为客户提供微信公众号托管，微调查问卷，微投票，微商城，游戏，母婴等更行业专业运营推广活动" />
<link rel="shortcut icon" href="/favicon.ico">
<?php echo_css('bootstrap.min.css');?>
<?php echo_css('jquery-ui.min.css');?>
<?php echo_css('pc.css');?>
<?php echo_css('font-awesome.min.css');?>
<?php echo_css('ui-dialog.css');?>

<?php echo_js('libs/config.js');?>
<script type="text/javascript">
dp_global_init('<?php echo C('JS_DOMAIN');?>', '<?php echo C('CSS_DOMAIN');?>', '<?php echo C('IMG_DOMAIN');?>', '<?php echo C('UPLOAD_DOMAIN');?>', '<?php echo C('API_DOMAIN');?>', '<?php echo C('SITE_DOMAIN');?>');
</script>

<!--[if lt IE 9]>
<?php echo_js('libs/html5shiv.min.js');?>
<![endif]-->