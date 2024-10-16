<body class=" login">
<!-- BEGIN LOGO -->
<div class="logo">
    <a href="{$SITE_URL}">
        <img src="images/logo.png" style="width: 150px;" alt="" /> </a>
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">
    <!-- BEGIN LOGIN FORM -->
    <h3 class="form-title text-center">{$SITE_ADI}</h3>
    {*<div class="alert alert-danger">
        <button class="close" data-close="alert"></button>
        <span> Giriş yapılamadı. </span>
    </div>*}
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
                {*<button type="button" class="btn default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
                    <i class="fa fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li>
                        <a href="javascript:;"> Action </a>
                    </li>
                    <li>
                        <a href="javascript:;"> Another action </a>
                    </li>
                    <li>
                        <a href="javascript:;"> Something else here </a>
                    </li>
                    <li class="divider"> </li>
                    <li>
                        <a href="javascript:;"> Separated link </a>
                    </li>
                </ul>*}
            </div>
            <input type="text" class="form-control" placeholder="Telefon" id="Telefon"/>
        </div>
    </div>
    {*<div class="form-group field-div register-field-div">
        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
        <label class="control-label">Tanıtım Metni</label>
        <div class="input-icon">
            <i class="fa fa-envelope"></i>
            <input class="form-control placeholder-no-fix" type="text"
                   placeholder="Şirket Adı/Meslek" id="Meslek" />
        </div>
        <span style="font-size: 11px;">Var ise şirket adınızı, yok ise mesleğinizi belirtebilirsiniz</span>
    </div>*}
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
    {*<div class="create-account field-div login-field-div">
        <p> Henüz hesabınız yok mu?&nbsp;
            <a href="javascript:;" onclick="FieldGoster('register')" id="register-btn"> Hemen Hesap oluştur </a>
        </p>
    </div>*}
    <!-- END LOGIN FORM -->
</div>
<!-- END LOGIN -->
</body>
