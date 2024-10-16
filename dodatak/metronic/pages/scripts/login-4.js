var Login = function () {

	var handleLogin = function() {
		$('.login-form').validate({
	            errorElement: 'span', //default input error message container
	            errorClass: 'help-block', // default input error message class
	            focusInvalid: false, // do not focus the last invalid input
	            rules: {
	                email: {
	                    required: true
	                },
	                password: {
	                    required: true
	                },
	                remember: {
	                    required: false
	                }
	            },

	            messages: {
	                email: {
	                    required: "Email alanını doldurmanız gerekmektedir."
	                },
	                password: {
	                    required: "Parola alanını doldurmanız gerekiyor.."
	                }
	            },

	            invalidHandler: function (event, validator) { //display error alert on form submit
	                $('.alert-danger', $('.login-form')).show();
	            },

	            highlight: function (element) { // hightlight error inputs
	                $(element)
	                    .closest('.form-group').addClass('has-error'); // set error class to the control group
	            },

	            success: function (label) {
	                label.closest('.form-group').removeClass('has-error');
	                label.remove();
	            },

	            errorPlacement: function (error, element) {
	                error.insertAfter(element.closest('.input-icon'));
	            },

	            submitHandler: function (form) {
					if(form.giris_tur.value != 1 && form.giris_tur.value != 2)
						return Page.ShowError("Giriş Türünü Seçiniz");
					var email = form.email.value;
					var password = form.password.value;
					var remember = form.remember.checked;
					var tur = form.giris_tur.value;

					Cookie.set('motokullanici_hatirla', remember);
					if(remember)
					{
						Cookie.set('motokullanici_tip', tur);
						Cookie.set('motokullanici_adi', email);
						Cookie.set('motokullanici_parola', password);
					}
					else
					{
						Cookie.set('motokullanici_tip', '');
						Cookie.set('motokullanici_adi', '');
						Cookie.set('motokullanici_parola', '');
					}
					Page.Ajax.Get('ajax').Send('KimlikDogrula',
						{a:email, b:password,c:remember,d: tur}, FBLoginResponse, 'Doğrulanıyor...');
//					Page.Ajax.Get('ajax').Send('KimlikDogrula',
//						{a:username, b:password,c:remember,d:type}, FBLoginResponse, 'Doğrulanıyor...');
	            }
	        });

	        $('.login-form input').keypress(function (e) {
	            if (e.which == 13) {
	                if ($('.login-form').validate().form()) {
	                    $('.login-form').submit();
	                }
	                return false;
	            }
	        });
	}

	var handleForgetPassword = function () {
		$('.forget-form').validate({
	            errorElement: 'span', //default input error message container
	            errorClass: 'help-block', // default input error message class
	            focusInvalid: false, // do not focus the last invalid input
	            ignore: "",
	            rules: {
	                email: {
	                    required: true,
	                    email: true
	                }
	            },

	            messages: {
	                email: {
	                    required: "Email alanı zorunlu."
	                }
	            },

	            invalidHandler: function (event, validator) { //display error alert on form submit

	            },

	            highlight: function (element) { // hightlight error inputs
	                $(element)
	                    .closest('.form-group').addClass('has-error'); // set error class to the control group
	            },

	            success: function (label) {
	                label.closest('.form-group').removeClass('has-error');
	                label.remove();
	            },

	            errorPlacement: function (error, element) {
	                error.insertAfter(element.closest('.input-icon'));
	            },

	            submitHandler: function (form) {
	                form.submit();
	            }
	        });

	        $('.forget-form input').keypress(function (e) {
	            if (e.which == 13) {
	                if ($('.forget-form').validate().form()) {
	                    $('.forget-form').submit();
	                }
	                return false;
	            }
	        });

	        jQuery('#forget-password').click(function () {
	            jQuery('.login-form').hide();
	            jQuery('.forget-form').show();
	        });

	        jQuery('#back-btn').click(function () {
	            jQuery('.login-form').show();
	            jQuery('.forget-form').hide();
	        });

	}

	var handleRegister = function () {

		        function format(state) {
            if (!state.id) { return state.text; }
            var $state = $(
             '<span><i class="fa fa-map-marker"></i>&nbsp;' + state.text + '</span>'
            );

            return $state;
        }

        if (jQuery().select2 && $('#city_list').size() > 0) {
            $("#city_list").select2({
	            placeholder: '<i class="fa fa-map-marker"></i>&nbsp;Şehir Seçiniz..',
	            templateResult: format,
                templateSelection: format,
                width: 'auto',
	            escapeMarkup: function(m) {
	                return m;
	            }
	        });


	        $('#city_list').change(function() {
	            $('.register-form').validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
	        });
    	}


         $('.register-form').validate({
	            errorElement: 'span', //default input error message container
	            errorClass: 'help-block', // default input error message class
	            focusInvalid: false, // do not focus the last invalid input
	            ignore: "",
	            rules: {
	                email: {
	                    required: true,
	                    email: true
	                },
	                address: {
	                    required: true
	                },
	                city: {
	                    required: true
	                },

	                username: {
	                    required: true
	                },
	                password: {
	                    required: true
	                },
	                rpassword: {
	                    equalTo: "#register_password"
	                },

	                tnc: {
	                    required: true
	                }
	            },

	            messages: { // custom messages for radio buttons and checkboxes
	                tnc: {
	                    required: "Please accept TNC first."
	                }
	            },

	            invalidHandler: function (event, validator) { //display error alert on form submit

	            },

	            highlight: function (element) { // hightlight error inputs
	                $(element)
	                    .closest('.form-group').addClass('has-error'); // set error class to the control group
	            },

	            success: function (label) {
	                label.closest('.form-group').removeClass('has-error');
	                label.remove();
	            },

	            errorPlacement: function (error, element) {
	                if (element.attr("name") == "tnc") { // insert checkbox errors after the container
	                    error.insertAfter($('#register_tnc_error'));
	                } else if (element.closest('.input-icon').size() === 1) {
	                    error.insertAfter(element.closest('.input-icon'));
	                } else {
	                	error.insertAfter(element);
	                }
	            },

	            submitHandler: function (form) {
	                form.submit();
	            }
	        });

			$('#register-submit-btn').keypress(function (e) {
	            if (e.which == 13) {
	                if ($('.register-form').validate().form()) {
	                    $('.register-form').submit();
	                }
	                return false;
	            }
	        });

	        jQuery('#register-btn').click(function () {
	            jQuery('.login-form').hide();
	            jQuery('.register-form').show();
	        });

	        jQuery('#register-back-btn').click(function () {
	            jQuery('.login-form').show();
	            jQuery('.register-form').hide();
	        });
	}

    return {
        //main function to initiate the module
        init: function () {

            handleLogin();
            handleForgetPassword();
            handleRegister();

            // init background slide images
		    $.backstretch([
                "plugins/metronic/pages/media/bg/1.jpg",
                "plugins/metronic/pages/media/bg/2.jpg",
                "plugins/metronic/pages/media/bg/3.jpg",
                "plugins/metronic/pages/media/bg/4.jpg",
                "plugins/metronic/pages/media/bg/5.jpg"
		        ], {
		          fade: 1000,
		          duration: 8000
		    	}
        	);
        }
    };

}();

jQuery(document).ready(function() {
	var type = Cookie.get('motokullanici_tip');
	if(type == Page.GetParameter("type",type))
	{
		var user = Cookie.get('motokullanici_adi');
		$('.login-form select[name="giris_tur"]').val(Cookie.get('motokullanici_tip'));
		$('.login-form input[name="username"]').val(user);
		$('.login-form input[name="password"]').val(Cookie.get('motokullanici_parola'));
		$('.login-form input[name="remember"]').attr('checked', Cookie.get('motokullanici_hatirla') == 'true');
	}
    Login.init();
});