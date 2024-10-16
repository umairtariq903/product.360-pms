<?php /* Smarty version Smarty-3.1.12, created on 2024-10-16 10:09:31
         compiled from "themes\t-demo\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:203247057670f74bb850012-65752868%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5243ef3f5468821c0dcb2dfad9920562d2957125' => 
    array (
      0 => 'themes\\t-demo\\index.tpl',
      1 => 1723497765,
      2 => 'file',
    ),
    '3eaec22d6509607a7087c2b317870055807bc09b' => 
    array (
      0 => 'themes\\b-default\\index.tpl',
      1 => 1723497765,
      2 => 'file',
    ),
    'f7cdbb694533fd88e65ee9d146317b2d5a3abb52' => 
    array (
      0 => 'c:\\xampp\\xampp_7.2\\htdocs\\PMS_SERVER\\ui\\templates\\index_main.tpl',
      1 => 1723497766,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '203247057670f74bb850012-65752868',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'Page' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_670f74bb8f1f42_43939394',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670f74bb8f1f42_43939394')) {function content_670f74bb8f1f42_43939394($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_template_check')) include 'C:\\xampp\\xampp_7.2\\htdocs\\PMS_SERVER\\knjiz\\others\\Smarty\\libs\\plugins\\modifier.template_check.php';
?><?php echo $_smarty_tpl->getSubTemplate (smarty_modifier_template_check("index_top.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<body>
		
    <?php /*  Call merged included template "index_main.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate("index_main.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0, '203247057670f74bb850012-65752868');
content_670f74bb8e88d1_93384256($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "index_main.tpl" */?>

	</body>
	<?php echo $_smarty_tpl->tpl_vars['Page']->value->LoadResources("js");?>

</html>
<?php }} ?><?php /* Smarty version Smarty-3.1.12, created on 2024-10-16 10:09:31
         compiled from "c:\xampp\xampp_7.2\htdocs\PMS_SERVER\ui\templates\index_main.tpl" */ ?>
<?php if ($_valid && !is_callable('content_670f74bb8e88d1_93384256')) {function content_670f74bb8e88d1_93384256($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['Page']->value->GetTemplateUri(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }} ?>