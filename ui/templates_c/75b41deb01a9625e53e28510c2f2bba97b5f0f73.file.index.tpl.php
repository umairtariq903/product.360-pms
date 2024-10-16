<?php /* Smarty version Smarty-3.1.12, created on 2024-10-16 10:09:18
         compiled from "themes\b-metronic\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1819188536670f74ae30e079-43799643%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '75b41deb01a9625e53e28510c2f2bba97b5f0f73' => 
    array (
      0 => 'themes\\b-metronic\\index.tpl',
      1 => 1723497765,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1819188536670f74ae30e079-43799643',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'SITE_URL' => 0,
    'Page' => 0,
    'Title' => 0,
    'Canonical' => 0,
    'girisYapan' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_670f74ae3f1b64_87571798',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670f74ae3f1b64_87571798')) {function content_670f74ae3f1b64_87571798($_smarty_tpl) {?><!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="tr">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />
		<meta http-equiv="Content-Language" content="tr" />
		<base href='<?php echo $_smarty_tpl->tpl_vars['SITE_URL']->value;?>
' />
        <title><?php echo IfNull($_smarty_tpl->tpl_vars['Page']->value,'PageTitle',$_smarty_tpl->tpl_vars['Title']->value);?>
</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="robots" content="noindex">
		<?php if (isset($_smarty_tpl->tpl_vars['Canonical'])) {$_smarty_tpl->tpl_vars['Canonical'] = clone $_smarty_tpl->tpl_vars['Canonical'];
$_smarty_tpl->tpl_vars['Canonical']->value = $_smarty_tpl->tpl_vars['Page']->value->GetCanonicalUrl(); $_smarty_tpl->tpl_vars['Canonical']->nocache = null; $_smarty_tpl->tpl_vars['Canonical']->scope = 0;
} else $_smarty_tpl->tpl_vars['Canonical'] = new Smarty_variable($_smarty_tpl->tpl_vars['Page']->value->GetCanonicalUrl(), null, 0);?>
		<?php if ($_smarty_tpl->tpl_vars['Canonical']->value){?>
		<link rel="canonical" href="<?php echo $_smarty_tpl->tpl_vars['Canonical']->value;?>
"/>
		<?php }?>
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<link rel="preload" href="pravi/others/font_awesome/css/font-awesome.min.css" as="style" />
		<link rel="preload" href="pravi/others/font_awesome/css/build.css" as="style" />
		<?php echo $_smarty_tpl->tpl_vars['Page']->value->LoadResources('css');?>


		<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
	</head>
    <!-- END HEAD -->
	<?php if ($_smarty_tpl->tpl_vars['girisYapan']->value){?>
		<?php echo $_smarty_tpl->getSubTemplate ("./index_user.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<?php }else{ ?>
		<?php echo $_smarty_tpl->getSubTemplate ("index_main.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<?php }?>
	<!--[if lt IE 9]>
		<script src="dodatak/metronic/global/plugins/respond.min.js"></script>
		<script src="dodatak/metronic/global/plugins/excanvas.min.js"></script>
		<script src="dodatak/metronic/global/plugins/ie8.fix.min.js"></script>
	<![endif]-->
	<?php echo $_smarty_tpl->tpl_vars['Page']->value->LoadResources('js');?>

	<script>Page.Params = Page.GetParameters();</script>
</html>

<?php }} ?>