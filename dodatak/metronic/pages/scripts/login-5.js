var Login = function() {

    var handleLogin = function() {

        $('.login-form').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                username: {
                    required: true
                },
                password: {
                    required: true
                },
                logintypesel: {
                    required: true
                },
                remember: {
                    required: false
                }
            },

            messages: {
                username: {
                    required: "Kullanıcı adı zorunlu."
                },
                password: {
                    required: "Parola zorunlu."
                }
            },

            invalidHandler: function(event, validator) { //display error alert on form submit
				$('.login-type-error,.login-ers-error').hide();
				if($('#login-type').val() == "")
					$('.login-type-error', $('.login-form')).show();
				else
					$('.login-ers-error', $('.login-form')).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
				var username = form.username.value;
				var password = form.password.value;
				var remember = form.remember.checked;
				var type = $("#login-type option:selected").val();

				Cookie.set('kullanici_hatirla', remember);
				if(remember)
				{
					Cookie.set('kullanici_tip', type);
					Cookie.set('kullanici_adi', username);
					Cookie.set('kullanici_parola', password);
				}
				else
				{
					Cookie.set('kullanici_tip', '');
					Cookie.set('kullanici_adi', '');
					Cookie.set('kullanici_parola', '');
				}
				Page.Ajax.Get('ajax').Send('KimlikDogrula',
					{a:username, b:password,c:remember,d:type}, LoginResponse, 'Doğrulanıyor...');
            }
        });

        $('.login-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('.login-form').validate().form()) {
                    $('.login-form').submit(); //form validation success, call ajax form submit
                }
                return false;
            }
        });

        $('.forget-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('.forget-form').validate().form()) {
                    $('.forget-form').submit();
                }
                return false;
            }
        });

        $('#forget-password').click(function(){
            $('.login-form').hide();
            $('.forget-form').show();
        });

        $('#activation-code').click(function(){
            $('.login-form').hide();
            $('.activation-form').show();
        });

        $('#back-btn').click(function(){
            $('.login-form').show();
            $('.forget-form').hide();
        });

        $('#facebook-back-btn').click(function(){
            $('.login-form').show();
            $('.facebook-form').hide();
        });

        $('#activation-back-btn').click(function(){
            $('.login-form').show();
            $('.activation-form').hide();
        });

        $('#login-type').change(function(){
			if($(this).val() == 1)
			{
				$('#facebook-login').hide();
				$('#activation-code').closest('div.forgot-password').hide();
			}
			else
			{
				$('#facebook-login').show();
				$('#activation-code').closest('div.forgot-password').show();
			}
        });
    }




    return {
        //main function to initiate the module
        init: function() {

            handleLogin();

            // init background slide images
            $('.login-bg').backstretch([
                "plugins/metronic/pages/img/login/bg1.jpg",
                "plugins/metronic/pages/img/login/bg2.jpg",
                "plugins/metronic/pages/img/login/bg3.jpg",
                "plugins/metronic/pages/img/login/bg4.jpg"
                ], {
                  fade: 1000,
                  duration: 3000
                }
            );
        }

    };

}();
jQuery(document).ready(function() {
    Login.init();
	if(Page.GetParameter('activation') == 1)
	{
		$('.login-form').hide();
		$('.activation-form').show();
		$('.activation-form input:eq(0)').focus();
	}
	var appId = "970751093027551";
	if(isLocalhost())
		appId = "151407672062533";


	//Facebook JS SDK yükle
	FacebookSdkLoad = true;
	window.fbAsyncInit = function() {
		FB.init({
		  appId      : appId,
		  xfbml      : true,
		  cookie	 : true,
		  version    : 'v2.5'
		});
		FB.getLoginStatus(function(response) {
			$('#facebook-login').removeClass("disabled");
			if (response.status === 'connected')
			{
				FBLoginScreen();
			} else
			{
				$('#facebook-login').click(function(){
					FBLogin();
				});

			}
		});
	};

	var type = Cookie.get('kullanici_tip');
	if(type == Page.GetParameter("type",type))
	{
		var user = Cookie.get('kullanici_adi');
		$('.login-form select[name="giris_tur"]').val(Cookie.get('kullanici_tip'));
		$('.login-form input[name="username"]').val(user);
		$('.login-form input[name="password"]').val(Cookie.get('kullanici_parola'));
		$('.login-form input[name="remember"]').attr('checked', Cookie.get('kullanici_hatirla') == 'true');
	}
	$('#login-type').change();
});
//Eğer tarayıcıda facebook açıksa ve kullanıcıya hesabı göster ve bekle
function FBLoginScreen()
{
	FB.api('/me', 'GET', { fields: 'first_name,last_name,name,id,location,email'}, function(response) {
		$(".facebook-form img").attr("src",'https://graph.facebook.com/'+response.id+'/picture?width=200');
		$(".facebook-form .profile-usertitle-name").html(response.name);
		var sehir = "(Şehir belirtilmemiş)";
		if(response.location)
			sehir = response.location.name;
		$(".facebook-form .profile-usertitle-job").html(sehir);
        $('#facebook-login').click(function(){
            $('.login-form').hide();
            $('.facebook-form').show();
        });
        $('#facebook-resume-btn').click(function(){
            FBLogin();
        });
	});
}
//Eğer tanımlı bir facebook hesabı yok ise popupda face i açar
//Cevap geldiğinde doğrulama yapar ve otomasyona giriş sağlar
function FBLogin()
{
	FB.login(function(response) {
		if (response.status === 'connected')
		{
			FB.api('/me', 'GET', { fields: 'first_name,last_name,name,id,location,gender,education,birthday,email'}, function(response) {
				if(! response.email)
					return Page.ShowWarning("Facebook hesabınıza kayıtlı email doğrulanamadı.Lütfen açılan ekranda email bilgilerinizi doldurunuz.",function(){
						Page.Ajax.Send("Yonlendir",{response: response},function(newResp){
							Page.Open(PAGE_GuestUzmanUyelik,{staff: newResp});
						});
					});
				Page.Ajax.Get("ajax").Send("FBKimlikDogrula",response,LoginResponse,"Yönlendiriliyorsunuz...");
			});
		}
	}, { scope: 'email,user_location,user_birthday,user_education_history'});
}
function LoginResponse(response)
{
	if (response != 1)
	{
		if(response[0] == '-')
			Page.ShowError(response);
		else
			Page.ShowError("Kullanıcı adı veya parola yanlış...");
	}
	else
	{
		var opener = window.opener;
		var mode = Page.GetParameter('mode', 'page');
		if(mode == 'clear')
		{
			var openerCb = Page.GetParameter('OpenerCallback', '');
			if (typeof opener[openerCb] == "function")
				opener[openerCb]();
			Page.Close();
		}
		else
		{
			var oldURL = '';
			if (Page.GetParameter('rlocate'))
				oldURL = GetUrl(Page.GetParameter('rlocate'));
			oldURL = oldURL.replace(SITE_URL,'');
			var oldAct = Page.GetParameter('act', '', oldURL,1);
			if (oldURL && oldAct && oldAct != 'guest' && oldAct != 'default')
				Page.Load(oldURL);
			else if(document.referrer.match(SITE_URL) && Page.GetParameter('mode', '', document.referrer,true) != 'clear')
				window.location.href = document.referrer;
			else
				Page.Load(SITE_URL);
		}
	}
}