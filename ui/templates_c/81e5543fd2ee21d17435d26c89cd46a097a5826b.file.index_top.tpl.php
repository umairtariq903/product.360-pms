<?php /* Smarty version Smarty-3.1.12, created on 2024-10-14 09:34:39
         compiled from "/var/www/vhosts/product.360-pms.com/httpdocs/themes/b-default/index_top.tpl" */ ?>
<?php /*%%SmartyHeaderCode:577624389670cc98f191199-05912304%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '81e5543fd2ee21d17435d26c89cd46a097a5826b' => 
    array (
      0 => '/var/www/vhosts/product.360-pms.com/httpdocs/themes/b-default/index_top.tpl',
      1 => 1723497765,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '577624389670cc98f191199-05912304',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'Page' => 0,
    'SITE_URL' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_670cc98f1971e9_64867417',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670cc98f1971e9_64867417')) {function content_670cc98f1971e9_64867417($_smarty_tpl) {?><!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="tr">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
	<head>
		<?php if (!isLocalhost()){?>
			<!-- Google tag (gtag.js) -->
			<script async src="https://www.googletagmanager.com/gtag/js?id=G-7C16VYJ3T0"></script>
			<script>
				window.dataLayer = window.dataLayer || [];
				function gtag(){ dataLayer.push(arguments); }
				gtag('js', new Date());

				gtag('config', 'G-7C16VYJ3T0');
			</script>
		<?php }?>
	<meta http-equiv="Content-Language" content="tr" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<?php if ($_smarty_tpl->tpl_vars['Page']->value->Keywords){?>
		<meta name="keywords" content="<?php echo implode(',',$_smarty_tpl->tpl_vars['Page']->value->Keywords);?>
" />
	<?php }?>

	<base href="<?php echo $_smarty_tpl->tpl_vars['SITE_URL']->value;?>
"/>
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
	<?php echo $_smarty_tpl->tpl_vars['Page']->value->LoadResources("css");?>


	<title><?php echo $_smarty_tpl->tpl_vars['Page']->value->Title;?>
</title>
	</head>
<?php }} ?>