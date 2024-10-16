/* global REHDOTCOM_SITE_URL, Page, PAGE_MemberMesajlarimSohbet, PageNotifications */
$('.page-content-inner').addClass('use-thema-font');

$(function(){
	var pageLink = window.location.href.substr($('BASE').attr('href').length);
	// Üst navigasyonda seçili
	var pg = $('DIV.hor-menu a.nav-link[href^="' + pageLink + '"]');
	if (pg.length > 0)
		pg.parents('LI.menu-dropdown').addClass('active');
	else
	{
		var links = $('DIV.hor-menu a.nav-link[href]');
		for(var i=0; i<links.length; i++)
		{
			var href = links.eq(i).attr('href');
			if (pageLink.startsWith(href))
			{
				links.eq(i).parents('LI.menu-dropdown').addClass('active');
				break;
			}
		}
	}
	// Üst menüde seçili
	pg = $('DIV.top-menu a[href^="' + pageLink + '"]');
	if (pg.length > 0)
		pg.closest('LI').addClass('active');

	var breads = $('LI.menu-dropdown.active > a');
	var breadcrumb = $('UL.page-breadcrumb');
	for(var i=0; i<breads.length; i++)
	{
		var li = $('<LI>').appendTo(breadcrumb);
		var a = $(i == breads.length - 1 ? '<SPAN>' : '<A>');
		a.html($(breads[i]).html()).appendTo(li);
	}
	if (breads.length == 0)
		breadcrumb.remove();
	var head = $('.ers-page-header');
	var h1 = $('.page-head .page-title h1');
	if (head.length > 0)
	{
		h1.text(head.text());
		var icon = head.attr('page-icon');
		if (icon)
			h1.prepend('<i class="' + icon + '"></i> ');
		h1.closest('.page-head').show();
	}
	else
			h1.closest('.page-head').remove();
	head.remove();
	Layout.init();
	Demo.init();
	$('input[maxlength]').maxlength({alwaysShow: true});

	if (typeof PageNotifications != "undefined")
	{
		var unread = $('.dropdown-inbox').attr('unread_count');
		if (parseInt(unread) > 0 || PageNotifications.length == 0)
		{
			toastr.options = {
				"closeButton": true,
				"debug": false,
				"newestOnTop": false,
				"progressBar": true,
				"positionClass": "toast-top-right",
				"preventDuplicates": false,
				"showDuration": "300",
				"hideDuration": "1000",
				"timeOut": "15000",
				"extendedTimeOut": "15000",
				"showEasing": "swing",
				"hideEasing": "linear",
				"showMethod": "fadeIn",
				"hideMethod": "fadeOut"
			};
			var titles = $('.unread-message');
			var shown = [];
			for(var i=0; i<titles.length; i++){
				var title = titles.eq(i).attr('title');
				var isNotified = titles.eq(i).attr('is_notified');
				if ($.inArray(title, shown) >= 0 || isNotified == '1')
					continue;
				shown.push(title);
				var rowId = titles.eq(i).attr('row_id');
				var obj = toastr['info'](title);
				$(obj).attr('row_id', rowId).click(function(){
					Page.Open(PAGE_MemberMesajlarimSohbet, {id: $(this).attr('row_id')});
				});
			}

			for(var i=0; i<PageNotifications.length; i++)
			{
				var notification = PageNotifications[i];
				var obj = toastr['success'](notification.Title);
				$(obj).attr('url', notification.Url).click(function(){
					window.location.href = $(this).attr('url');
				});
			}
		}
	}

	var div = $('[modal-div="1"]');
	if (div.length > 0)
	{
		div = div.eq(0);
		var evt = div.attr('modal-click');
		var act = '';
		if (typeof window[evt] == "function")
			act = window[evt];
		var modal = Page.ShowDialogBS(div.attr('id'), null, null, act);
		if (div.attr('modal-buttons') == 0)
			modal.find('.modal-footer BUTTON').hide();
		var close = div.attr('modal-button-close');
		if (close)
			modal.find('.modal-footer BUTTON.cancel').show().html('Kapat');
		var width = div.attr('modal-width');
		if (width)
			modal.find('.modal-dialog').css('max-width', '400px');
	}

	if ((typeof KisiSehir == "undefined" || KisiSehir == '') && typeof google == "object")
		navigator.geolocation.getCurrentPosition(function(pos){
			var location = {lat: pos.coords.latitude, lng: pos.coords.longitude};
			var geocoder = new google.maps.Geocoder();
			geocoder.geocode({location: location}, function(res, status) {
				if (status != 'OK')
					return;
				var sehir = '';
				for(var i=0; i<res.length; i++)
				{
					var obj = res[i];
					if (obj.types[0] == "administrative_area_level_1")
						sehir = obj.address_components[0].long_name;
				}
				if (sehir)
					Page.Ajax.Get('ajax').Send('SaveVisitorCity', sehir, function(){}, '');
			});
		});
});

function PMSCompanyChange()
{
	Page.Ajax.Get("ajax").SendBool("PMSCompanyChange",$("#PMSCompanyId").val(),Page.Refresh);
}

function PMSProjectChange()
{
	Page.Ajax.Get("ajax").SendBool("PMSProjectChange",$("#PMSProjectId").val(),Page.Refresh);
}

function SaveNewEmail()
{
	var email = $('#newValidEmail').val();
	if (! email.match(/^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z0-9._-]+$/i))
		return Page.ShowError("Yazılan e-posta adresi geçerli görünmüyor...");
	var cb = function(){
		Page.ShowInfo(email + " adresine aktivasyon kodu gönderildi, " +
				"Lütfen gelen e-posta içindeki linke tıklayarak yeni " +
				"e-posta adresininizi aktifleştiriniz");
		$('#DIV_InvalidEmail').parents('.modal').first().modal('hide');
	};
	Page.Ajax.Get('act=ajax').SendBool('EmailDegistir', email, cb, 'Kaydediliyor...');
	return false;
}
