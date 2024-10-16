<?php /* Smarty version Smarty-3.1.12, created on 2024-10-16 10:09:32
         compiled from "C:\xampp\xampp_7.2\htdocs\PMS_SERVER\ui\pages\guest\giris_yap\giris_yap.tpl" */ ?>
<?php /*%%SmartyHeaderCode:95960515670f74bc0d4601-84683625%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '676053c42c5cd6ac88e1ca242f58e75bb33f9d57' => 
    array (
      0 => 'C:\\xampp\\xampp_7.2\\htdocs\\PMS_SERVER\\ui\\pages\\guest\\giris_yap\\giris_yap.tpl',
      1 => 1723497766,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '95960515670f74bc0d4601-84683625',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'SITE_URL' => 0,
    'SITE_ADI' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_670f74bc11f185_85040739',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670f74bc11f185_85040739')) {function content_670f74bc11f185_85040739($_smarty_tpl) {?><body class=" login">
<!-- BEGIN LOGO -->
<div class="logo">
    <a href="<?php echo $_smarty_tpl->tpl_vars['SITE_URL']->value;?>
">
        <img src="images/logo.png" style="width: 150px;" alt="" /> </a>
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">
    <!-- BEGIN LOGIN FORM -->
    <h3 class="form-title text-center"><?php echo $_smarty_tpl->tpl_vars['SITE_ADI']->value;?>
</h3>
    
    <div class="form-group field-div register-field-div">
        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
        <label class="control-label">Ad *</label>
        <div class="input-icon">
            <i class="fa fa-user"></i>
            <input class="form-control placeholder-no-fix" type="text" autocomplete="off"
                   placeholder="Ad" id="Ad" />
        </div>
    </div>
    <div class="form-group field-div register-field-div">
        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
        <label class="control-label">Soyad *</label>
        <div class="input-icon">
            <i class="fa fa-user"></i>
            <input class="form-control placeholder-no-fix" type="text" autocomplete="off"
                   placeholder="Soyad" id="Soyad" />
        </div>
    </div>
    <div class="form-group field-div register-field-div">
        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
        <label class="control-label">Telefon *</label>
        <div class="input-group">
            <div class="input-group-btn" style="font-size: 14px;">
                <input type="text" value="+90" class="form-control" id="AlanKodu" onchange="AlanKoduDegisti(this)"
                       style="width: 70px; border-radius: 4px; border-left: 2px solid #ff9800 !important;"/>
                
            </div>
            <input type="text" class="form-control" placeholder="Telefon" id="Telefon"/>
        </div>
    </div>
    
    <div class="form-group field-div login-field-div register-field-div forget-field-div">
        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
        <label class="control-label">Email *</label>
        <div class="input-icon">
            <i class="fa fa-envelope"></i>
            <input class="form-control placeholder-no-fix" type="email" autocomplete="off"
                   placeholder="Email" id="Email" />
        </div>
    </div>
    <div class="form-group field-div login-field-div register-field-div">
        <label class="control-label">Password *</label>
        <div class="input-icon">
            <i class="fa fa-lock"></i>
            <input class="form-control placeholder-no-fix" type="password" autocomplete="off"
                   placeholder="Password" id="Password"/>
        </div>
    </div>
    <div class="form-group field-div register-field-div">
        <label class="control-label">Parola Tekrar *</label>
        <div class="input-icon">
            <i class="fa fa-lock"></i>
            <input class="form-control placeholder-no-fix" type="password" autocomplete="off"
                   placeholder="Parola Tekrar" id="ParolaTekrar"/>
        </div>
    </div>
    <div class="form-actions field-div login-field-div">
        <label class="rememberme mt-checkbox mt-checkbox-outline">
            <input type="checkbox" id="Hatirla" class="styled"/> Remember me
            <span></span>
        </label>
        <button type="submit" class="btn green pull-right" onclick="GirisYap()">
            <i class="fa fa-sign-in"></i> Login
        </button>
    </div>
    <div class="form-actions field-div forget-field-div">
        <button type="button" id="back-btn" class="btn grey-salsa btn-outline" onclick="FieldGoster('login')"> Back </button>
        <button type="submit" class="btn green pull-right" onclick="ParolaSifirla()">
            <i class="fa fa-send"></i> Send
        </button>
    </div>
    <div class="form-actions field-div register-field-div">
        <button type="button" id="back-btn" class="btn grey-salsa btn-outline" onclick="FieldGoster('login')"> Back </button>
        <button type="submit" class="btn green pull-right" onclick="KayitOl(this)">
            <i class="fa fa-user-plus"></i> Register
        </button>
    </div>
    <div class="forget-password field-div login-field-div">
        <h4>Have you forgotten your password?</h4>
        <p>You can reset your password by  <a href="javascript:;" onclick="FieldGoster('forget')" id="forget-password"> clicking here </a>.</p>
    </div>
    
    <!-- END LOGIN FORM -->
</div>
<!-- END LOGIN -->
</body>
<?php }} ?>