<?php /* Smarty version Smarty-3.1.12, created on 2024-10-14 02:33:16
         compiled from "/var/www/vhosts/product.360-pms.com/httpdocs/ui/templates/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1807320154670c66cca83867-48893053%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '690a91e78628a47aaf8b062b10b8a1cb2a898dc9' => 
    array (
      0 => '/var/www/vhosts/product.360-pms.com/httpdocs/ui/templates/index.tpl',
      1 => 1723497766,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1807320154670c66cca83867-48893053',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_670c66ccaa5981_30571861',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670c66ccaa5981_30571861')) {function content_670c66ccaa5981_30571861($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_template_check')) include '/var/www/vhosts/product.360-pms.com/httpdocs/knjiz/others/Smarty/libs/plugins/modifier.template_check.php';
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