<body class=" login">
<!-- BEGIN : LOGIN PAGE 5-2 -->
    <div class="user-login-5">
        <div class="row bs-reset">
            <div class="col-md-6 login-container bs-reset">
                <img class="login-logo login-6" src="images/logo.png" style="width: 300px;"/>
                <div class="login-content">
                    <h1>Kullanıcı Girişi</h1>
                    <p> Bosch Aksesuar, Rayno, Osaka, Beybi Eldiven, İzeltaş, ... vb tüm ürünlerimizi görebilir,kampanyalardan faydalanabilir ve ön sipariş oluşturabilirsiniz. </p>
                    <form action="javascript:;" class="login-form" method="post">
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button>
                            <span>Enter any username and password. </span>
                        </div>
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="form-control form-control-solid placeholder-no-fix form-group" type="text" id="KullaniciAdi"
                                       autocomplete="off" placeholder="Email" name="email" required/> </div>
                            <div class="col-xs-6">
                                <input class="form-control form-control-solid placeholder-no-fix form-group" type="password" id="Parola"
                                       autocomplete="off" placeholder="Parola" name="parola" required/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label class="rememberme mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" class="styled" name="remember" id="Hatirla" /> Beni Hatırla
                                    <span></span>
                                </label>
                            </div>
                            <div class="col-sm-8 text-right">
                                {*<div class="forgot-password">
                                    <a href="javascript:;" id="forget-password" class="forget-password">Forgot Password?</a>
                                </div>*}
                                <button class="btn blue" type="submit" onclick="GirisYap()">Giriş Yap</button>
                            </div>
                        </div>
                    </form>
                    <!-- BEGIN FORGOT PASSWORD FORM -->
                    {*<form class="forget-form" action="javascript:;" method="post">
                        <h3>Forgot Password ?</h3>
                        <p> Enter your e-mail address below to reset your password. </p>
                        <div class="form-group">
                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email" /> </div>
                        <div class="form-actions">
                            <button type="button" id="back-btn" class="btn blue btn-outline">Back</button>
                            <button type="submit" class="btn blue uppercase pull-right">Submit</button>
                        </div>
                    </form>*}
                    <!-- END FORGOT PASSWORD FORM -->
                </div>
                <div class="login-footer">
                    <div class="row bs-reset">
                        <div class="col-xs-5 bs-reset">
                            <ul class="login-social">
                                <li>
                                    <a href="https://www.facebook.com/profile.php?id=100064082408448" target="_blank">
                                        <i class="icon-social-facebook"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.instagram.com/ankaracivata/" style="font-size: 30px;">
                                        <img src="images/instagram.png" style="width: 30px;"/>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-xs-7 bs-reset">
                            <div class="login-copyright text-right">
{*                                <p>Copyright &copy; Keenthemes 2015</p>*}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 bs-reset" style="padding: -10px;">
                <div class="login-bg"> </div>
            </div>
        </div>
    </div>
</body>
