<?php /* Smarty version Smarty-3.1.12, created on 2024-10-16 10:09:31
         compiled from "c:\xampp\xampp_7.2\htdocs\PMS_SERVER\themes\b-default\index_top.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1179813680670f74bbaa2b05-80807036%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '63d170df5c867c42d3461d9b32a9b3af404dde67' => 
    array (
      0 => 'c:\\xampp\\xampp_7.2\\htdocs\\PMS_SERVER\\themes\\b-default\\index_top.tpl',
      1 => 1723497765,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1179813680670f74bbaa2b05-80807036',
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
  'unifunc' => 'content_670f74bbb2e6b9_75923021',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670f74bbb2e6b9_75923021')) {function content_670f74bbb2e6b9_75923021($_smarty_tpl) {?><!DOCTYPE html>
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