<?php /* Smarty version Smarty-3.1.12, created on 2024-10-16 10:09:19
         compiled from "C:\xampp\xampp_7.2\htdocs\PMS_SERVER\ui\pages\admin\ozet\ozet.tpl" */ ?>
<?php /*%%SmartyHeaderCode:851452086670f74af1b8153-46215293%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0390d1674586e9605b3638023211baea148f379b' => 
    array (
      0 => 'C:\\xampp\\xampp_7.2\\htdocs\\PMS_SERVER\\ui\\pages\\admin\\ozet\\ozet.tpl',
      1 => 1723497766,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '851452086670f74af1b8153-46215293',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'KullaniciSayisi' => 0,
    'ToplamGoruntulenme' => 0,
    'VendorCount' => 0,
    'FirmaSayisi' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_670f74af20dd72_45237321',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670f74af20dd72_45237321')) {function content_670f74af20dd72_45237321($_smarty_tpl) {?><div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue" href="javascript:;">
            <div class="visual">
                <i class="fa fa-users"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="<?php echo $_smarty_tpl->tpl_vars['KullaniciSayisi']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['KullaniciSayisi']->value;?>
</span>
                </div>
                <div class="desc"> Total Users </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red" href="javascript:;">
            <div class="visual">
                <i class="fa fa-eye"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="<?php echo $_smarty_tpl->tpl_vars['ToplamGoruntulenme']->value;?>
"><?php echo number_format($_smarty_tpl->tpl_vars['ToplamGoruntulenme']->value);?>
</span>
                </div>
                <div class="desc"> Total  </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green" href="javascript:;">
            <div class="visual">
                <i class="fa fa-address-card-o"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="<?php echo $_smarty_tpl->tpl_vars['VendorCount']->value;?>
"><?php echo number_format($_smarty_tpl->tpl_vars['VendorCount']->value);?>
</span>
                </div>
                <div class="desc"> Vendor Count  </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 purple" href="javascript:;">
            <div class="visual">
                <i class="fa fa-globe"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="<?php echo $_smarty_tpl->tpl_vars['FirmaSayisi']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['FirmaSayisi']->value;?>
</span>
                </div>
                <div class="desc"> Company Count </div>
            </div>
        </a>
    </div>
</div>


<?php }} ?>