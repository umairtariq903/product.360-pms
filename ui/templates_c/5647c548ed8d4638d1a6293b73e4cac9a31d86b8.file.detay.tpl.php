<?php /* Smarty version Smarty-3.1.12, created on 2024-10-16 10:35:46
         compiled from "C:\xampp\xampp_7.2\htdocs\PMS_SERVER\ui\pages\admin\imports\detay\detay.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1647897377670f7ae27f7467-81429574%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5647c548ed8d4638d1a6293b73e4cac9a31d86b8' => 
    array (
      0 => 'C:\\xampp\\xampp_7.2\\htdocs\\PMS_SERVER\\ui\\pages\\admin\\imports\\detay\\detay.tpl',
      1 => 1723497765,
      2 => 'file',
    ),
    '2291b30ee24eab4816018a35d105990b2f0a138e' => 
    array (
      0 => 'C:\\xampp\\xampp_7.2\\htdocs\\PMS_SERVER\\knjiz\\dogru\\Templates\\bases\\edit_form2.tpl',
      1 => 1727293607,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1647897377670f7ae27f7467-81429574',
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
  'unifunc' => 'content_670f7ae2879d08_49286994',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670f7ae2879d08_49286994')) {function content_670f7ae2879d08_49286994($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['Page']->value->Title){?>
	<div class="ers-page-header"><?php echo $_smarty_tpl->tpl_vars['Page']->value->Title;?>
</div>
<?php }?>
<div class="clearfix">
	
	<?php echo $_smarty_tpl->tpl_vars['DbModelForm']->value->GetTableBS();?>

	<div class="form-group form-group-sm">
		<label class="control-label col-sm-3 col-lg-2 text-right" for="PFieldKeys" label_name="PFieldKeys">
			Product Attributes
			<span class="required_field"> * </span>
		</label>
		<div class="col-sm-9 col-lg-10" field_name="PFieldKeys">
			<div id="Attributes"></div>
		</div>
	</div>

</div>
<div class="button_panel ui-corner-all" style="position: relative; margin-top: 5px">
	
	<?php if ($_smarty_tpl->tpl_vars['Data']->value&&$_smarty_tpl->tpl_vars['Data']->value->ImportType==Import::Feed&&$_smarty_tpl->tpl_vars['Data']->value->SpKey!=''){?>
		<button class="btn btn-danger btn-sm pull-left" onclick="ResetAllProducts('<?php echo $_smarty_tpl->tpl_vars['Data']->value->Id;?>
','<?php echo $_smarty_tpl->tpl_vars['Data']->value->SpKey;?>
')">
			Reset All Products (<?php echo $_smarty_tpl->tpl_vars['Data']->value->SpKey;?>
)
		</button>
	<?php }?>
	<button class="jui-button" icon='ui-icon-disk' onclick="DbModelForm_Save('<?php echo $_smarty_tpl->tpl_vars['CustomSaveFunc']->value;?>
')">Kaydet</button>

</div>
<?php }} ?>