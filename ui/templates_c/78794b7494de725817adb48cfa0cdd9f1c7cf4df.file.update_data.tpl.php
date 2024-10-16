<?php /* Smarty version Smarty-3.1.12, created on 2024-10-14 02:33:18
         compiled from "/var/www/vhosts/product.360-pms.com/httpdocs/ui/pages/admin/update_data/update_data.tpl" */ ?>
<?php /*%%SmartyHeaderCode:715633402670c66ce789496-61632614%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '78794b7494de725817adb48cfa0cdd9f1c7cf4df' => 
    array (
      0 => '/var/www/vhosts/product.360-pms.com/httpdocs/ui/pages/admin/update_data/update_data.tpl',
      1 => 1728864066,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '715633402670c66ce789496-61632614',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_670c66ce78abe1_15606413',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670c66ce78abe1_15606413')) {function content_670c66ce78abe1_15606413($_smarty_tpl) {?><div class="col-xs-12 text-center margin-bottom-40">
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