<?php /* Smarty version Smarty-3.1.12, created on 2024-10-14 09:34:39
         compiled from "themes/t-demo/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1898363922670cc98f168ed2-71659031%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'feb6c58e7d8f3ad18d027736a71e84b7b8e3b56e' => 
    array (
      0 => 'themes/t-demo/index.tpl',
      1 => 1723497765,
      2 => 'file',
    ),
    'e3071dc916bfc4f712e385fb3ff883ca902dc472' => 
    array (
      0 => 'themes/b-default/index.tpl',
      1 => 1723497765,
      2 => 'file',
    ),
    '3e1d4f18c3628877376269b4f0e94b48e657cc1f' => 
    array (
      0 => '/var/www/vhosts/product.360-pms.com/httpdocs/ui/templates/index_main.tpl',
      1 => 1723497766,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1898363922670cc98f168ed2-71659031',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'Page' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_670cc98f18e476_68586159',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670cc98f18e476_68586159')) {function content_670cc98f18e476_68586159($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_template_check')) include '/var/www/vhosts/product.360-pms.com/httpdocs/knjiz/others/Smarty/libs/plugins/modifier.template_check.php';
?><?php echo $_smarty_tpl->getSubTemplate (smarty_modifier_template_check("index_top.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<body>
		
    <?php /*  Call merged included template "index_main.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate("index_main.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0, '1898363922670cc98f168ed2-71659031');
content_670cc98f18a898_69722385($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "index_main.tpl" */?>

	</body>
	<?php echo $_smarty_tpl->tpl_vars['Page']->value->LoadResources("js");?>

</html>
<?php }} ?><?php /* Smarty version Smarty-3.1.12, created on 2024-10-14 09:34:39
         compiled from "/var/www/vhosts/product.360-pms.com/httpdocs/ui/templates/index_main.tpl" */ ?>
<?php if ($_valid && !is_callable('content_670cc98f18a898_69722385')) {function content_670cc98f18a898_69722385($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['Page']->value->GetTemplateUri(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }} ?>