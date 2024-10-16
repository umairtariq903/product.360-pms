<body class=" login">
	<!-- BEGIN LOGO -->
	<div class="logo">
		<a href="index.html">
			<img src="images/logo.png" alt="" /> </a>
	</div>
	<!-- END LOGO -->
	<!-- BEGIN LOGIN -->
	<div class="content">
		<!-- BEGIN LOGIN FORM -->
		<h3 class="form-title">KULLANICI GİRİŞİ</h3>
		{*<div class="alert alert-danger">
			<button class="close" data-close="alert"></button>
			<span> Giriş yapılamadı. </span>
		</div>*}
		<div class="form-group kullanici-adi-div">
			<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
			<label class="control-label">Kullanıcı Adı</label>
			<div class="input-icon">
				<i class="fa fa-user"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off"
					   placeholder="Kullanıcı Adı" id="KullaniciAdi" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label">Parola</label>
			<div class="input-icon">
				<i class="fa fa-lock"></i>
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off"
					   placeholder="Parola" id="Parola"/>
			</div>
		</div>
		<div class="form-actions">
			<label class="rememberme mt-checkbox mt-checkbox-outline">
				<input type="checkbox" id="Hatirla" class="styled"/> Beni Hatırla
				<span></span>
			</label>
			<button type="submit" class="btn green pull-right" onclick="GirisYap()"> Giriş </button>
		</div>
		<!-- END LOGIN FORM -->
	</div>
	<!-- END LOGIN -->
</body>