<?php /* Smarty version Smarty-3.1.12, created on 2024-10-16 10:09:17
         compiled from "c:\xampp\xampp_7.2\htdocs\PMS_SERVER\ui\templates\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1176739397670f74adf16b65-50781662%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b55505aff1af1fd8cabff0e6aea4eea33241a877' => 
    array (
      0 => 'c:\\xampp\\xampp_7.2\\htdocs\\PMS_SERVER\\ui\\templates\\index.tpl',
      1 => 1723497766,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1176739397670f74adf16b65-50781662',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_670f74ae0c6e51_44538101',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670f74ae0c6e51_44538101')) {function content_670f74ae0c6e51_44538101($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_template_check')) include 'C:\\xampp\\xampp_7.2\\htdocs\\PMS_SERVER\\knjiz\\others\\Smarty\\libs\\plugins\\modifier.template_check.php';
?><?php if ($_GET['act']=='developer'||$_GET['act']=='db_model'||$_GET['act']=='cisc'){?>
		<?php echo $_smarty_tpl->getSubTemplate ("index_top.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<body style="background: white; margin: 10px;">
			<?php echo $_smarty_tpl->getSubTemplate ("index_main.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		</body>
<?php }else{ ?>
	<?php if ($_GET['mode']!='clear'){?>
		<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['TEMA_URL']->value)."/index.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<?php }else{ ?>
		<?php echo $_smarty_tpl->getSubTemplate (smarty_modifier_template_check("index_top.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<body style="background: white; margin: 10px;">
			<?php echo $_smarty_tpl->getSubTemplate ("index_main.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		</body>
	<?php }?>
<?php }?>
<?php }} ?>