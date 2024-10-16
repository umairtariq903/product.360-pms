<?php /* Smarty version Smarty-3.1.12, created on 2024-10-14 02:39:57
         compiled from "/var/www/vhosts/product.360-pms.com/httpdocs/ui/pages/admin/imports/detay/detay.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1899241285670c685d2ab312-50001843%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '42bd6edc43f816da384fc21eb45b22f8aad8a5bd' => 
    array (
      0 => '/var/www/vhosts/product.360-pms.com/httpdocs/ui/pages/admin/imports/detay/detay.tpl',
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
  'nocache_hash' => '1899241285670c685d2ab312-50001843',
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
  'unifunc' => 'content_670c685d2c8fd2_19632755',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670c685d2c8fd2_19632755')) {function content_670c685d2c8fd2_19632755($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['Page']->value->Title){?>
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