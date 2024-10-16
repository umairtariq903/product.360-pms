<?php /* Smarty version Smarty-3.1.12, created on 2024-10-16 12:00:27
         compiled from "C:\xampp\xampp_7.2\htdocs\product.360-pms\ui\pages\admin\update_data\update_data.tpl" */ ?>
<?php /*%%SmartyHeaderCode:653180234670f8ebb5310a1-95888274%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ce004526a2b3b2ed52d081dbe8158481859aaa07' => 
    array (
      0 => 'C:\\xampp\\xampp_7.2\\htdocs\\product.360-pms\\ui\\pages\\admin\\update_data\\update_data.tpl',
      1 => 1728864066,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '653180234670f8ebb5310a1-95888274',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_670f8ebb54cc57_80089807',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670f8ebb54cc57_80089807')) {function content_670f8ebb54cc57_80089807($_smarty_tpl) {?><div class="col-xs-12 text-center margin-bottom-40">
    <?php echo VarAppFile::Get()->GetFormItem();?>

</div>
<div class="col-xs-12 text-center">
    <label>
        You can proceed with your tasks after uploading your current Excel file.
    </label>
    <br/>
    <button class="btn btn-primary" onclick="UpdateData()">
        <i class="fa fa-refresh"></i> Update Data
    </button>
</div>
<?php }} ?>