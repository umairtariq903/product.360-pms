<?php /* Smarty version Smarty-3.1.12, created on 2024-10-16 12:21:28
         compiled from "C:\xampp\xampp_7.2\htdocs\product.360-pms\ui\pages\admin\p_attributes\detay\detay.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1802629832670f93a8334b17-07774409%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9c61ca62a356613c715b26ffe20d926ef74bf671' => 
    array (
      0 => 'C:\\xampp\\xampp_7.2\\htdocs\\product.360-pms\\ui\\pages\\admin\\p_attributes\\detay\\detay.tpl',
      1 => 1723497765,
      2 => 'file',
    ),
    'af1725b903eee76cd8aa4b5b8cea4717b025841c' => 
    array (
      0 => 'C:\\xampp\\xampp_7.2\\htdocs\\product.360-pms\\knjiz\\dogru\\Templates\\bases\\edit_form2.tpl',
      1 => 1727293607,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1802629832670f93a8334b17-07774409',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'Page' => 0,
    'DbModelForm' => 0,
    'CustomSaveFunc' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_670f93a83af2f0_83937865',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670f93a83af2f0_83937865')) {function content_670f93a83af2f0_83937865($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['Page']->value->Title){?>
	<div class="ers-page-header"><?php echo $_smarty_tpl->tpl_vars['Page']->value->Title;?>
</div>
<?php }?>
<div class="clearfix">
	
	<?php echo $_smarty_tpl->tpl_vars['DbModelForm']->value->GetTableBS();?>


</div>
<div class="button_panel ui-corner-all" style="position: relative; margin-top: 5px">
	
		<button class="jui-button" icon='ui-icon-disk' onclick="DbModelForm_Save('<?php echo $_smarty_tpl->tpl_vars['CustomSaveFunc']->value;?>
')">Save</button>
	
</div>
<?php }} ?>