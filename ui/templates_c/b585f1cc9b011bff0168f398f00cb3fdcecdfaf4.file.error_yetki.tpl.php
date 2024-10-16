<?php /* Smarty version Smarty-3.1.12, created on 2024-10-15 12:28:05
         compiled from "/var/www/vhosts/product.360-pms.com/httpdocs/ui/pages/error_yetki.tpl" */ ?>
<?php /*%%SmartyHeaderCode:238850707670e43b52b1cd2-94436877%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b585f1cc9b011bff0168f398f00cb3fdcecdfaf4' => 
    array (
      0 => '/var/www/vhosts/product.360-pms.com/httpdocs/ui/pages/error_yetki.tpl',
      1 => 1723497766,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '238850707670e43b52b1cd2-94436877',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_670e43b52c4486_71343536',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670e43b52c4486_71343536')) {function content_670e43b52c4486_71343536($_smarty_tpl) {?><div class="col-md-12 clearfix" style="margin-top: 50px;">
	<div class="panel panel-danger">
		<div class="panel-heading">
			<b><i class="fa fa-warning"></i> Hata</b>
		</div>
		<div class="panel-body" style="font-size: 14px;">
			<div class='margin-bottom'>
				<p  style='display: inline-block;'>
					Bu sayfayı görme yetkiniz bulunmamaktadır.
				</p>
			</div>
		</div>
	</div>
</div>
<script>
	setTimeout(function () {
		Page.Load(SITE_URL);
	},2000);
</script>
<?php }} ?>