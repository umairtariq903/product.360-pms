<?php /* Smarty version Smarty-3.1.12, created on 2024-10-16 10:13:50
         compiled from "C:\xampp\xampp_7.2\htdocs\PMS_SERVER\ui\pages\admin\update_data\update_data.tpl" */ ?>
<?php /*%%SmartyHeaderCode:385447398670f75bec04030-67551728%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dd86a931763e2b4d1776df2cf17e579430a34b2e' => 
    array (
      0 => 'C:\\xampp\\xampp_7.2\\htdocs\\PMS_SERVER\\ui\\pages\\admin\\update_data\\update_data.tpl',
      1 => 1728864066,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '385447398670f75bec04030-67551728',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_670f75bec70e60_22146353',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670f75bec70e60_22146353')) {function content_670f75bec70e60_22146353($_smarty_tpl) {?><div class="col-xs-12 text-center margin-bottom-40">
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