<?php /* Smarty version Smarty-3.1.12, created on 2024-10-14 02:41:45
         compiled from "/var/www/vhosts/product.360-pms.com/httpdocs/ui/pages/admin/p_attributes/detay/detay.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1100448223670c68c9cad4b7-85690720%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '898d8f0ae5106ab5aac3b0cb8659afa23ab6e25b' => 
    array (
      0 => '/var/www/vhosts/product.360-pms.com/httpdocs/ui/pages/admin/p_attributes/detay/detay.tpl',
      1 => 1723497765,
      2 => 'file',
    ),
    '3fa4a2c3f4eefd5413dd312c220cd41ef520d107' => 
    array (
      0 => '/var/www/vhosts/product.360-pms.com/httpdocs/knjiz/dogru/Templates/bases/edit_form2.tpl',
      1 => 1727293607,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1100448223670c68c9cad4b7-85690720',
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
  'unifunc' => 'content_670c68c9cc7893_10080725',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670c68c9cc7893_10080725')) {function content_670c68c9cc7893_10080725($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['Page']->value->Title){?>
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