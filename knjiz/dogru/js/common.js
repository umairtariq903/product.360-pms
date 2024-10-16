/* global EmailExt, tinyMCE, UploadTypeObj, DbModelObj, DbModel_CustomSaveFunc,
 DbModelForm_Init, DATETIME_PICKER_VER, UploadTypeSingle, UploadTypeMulti,
 UploadTypeImage, PageUrlTree, vkThread, dtTableIds, ORIGINAL_URL, UseRewriteAct */
USE_BS_UI = false;
TEL_MASK=1;
PAGE_ICON = '';
Common = {};
Tarih = {};
Page = {};
Table = {};
Jui = {};
BSui = {};
Mask = {};
Thread = {};
var CurrentMenu = null;
var CurrentContainer = null;

Page.GetWindowHref = function()
{
	var url = window.location.href;
	if (typeof ORIGINAL_URL != "undefined" && ORIGINAL_URL != '')
		url = ORIGINAL_URL;
	return url.replace(/#.*$/, '');
};

Page.GetCustomMessage = function(msg){

	if ($.isArray(msg)){
		var args = [];
		for(var i=1; i<msg.length; i++)
			args.push(msg[i]);
		msg = vsprintf(Page.CustomMessages[msg[0]], args);
	}
	return msg;
};

Page.Download = function(url, width, height){
	if (url.match(/[;|]/) || ! url.match(/\.(pdf|jpe?g|png|gif)$/i))
	{
		var ifr = $('IFRAME.download');
		if (ifr.length == 0)
			ifr = $('<iframe class="download">').appendTo('body').hide();
		ifr.get(0).src = url;
		return true;
	}

	Page.OpenNewWindow(url, 'download', width, height);
};

Page.GetParameters = function (url, ignore) {
	if (!is_set(url))
		url = Page.GetWindowHref();
	if (!is_set(ignore))
		ignore = [];
	if (!$.isArray(ignore))
		ignore = [ignore];
	var parts = url.split('?');
	var sonuc = new Object();
	if (parts.length <= 1)
		return sonuc;
	var params = parts[1].split('&');
	for (var i = 0; i < params.length; i++)
	{
		var p = params[i].split('=');
		if (p.length == 2 && p[1] && $.inArray(p[0], ignore) < 0)
			sonuc[p[0]] = unescape(decodeURIComponent(p[1]));
	}
	return sonuc;
};

if (typeof $.browser == "undefined")
	$.browser = {};
$.browser.chrome = /chrom(e|ium)/.test(navigator.userAgent.toLowerCase());
window.oldDecode = decodeURIComponent;
window.decodeURIComponent = function (str) {
	var table = {};
	table['%DE'] = 'Ş';
	table['%FE'] = 'ş';
	table['%DD'] = 'İ';
	table['%FD'] = 'ı';
	table['%D0'] = 'Ğ';
	table['%F0'] = 'ğ';
	table['%DC'] = 'Ü';
	table['%FC'] = 'ü';
	table['%C7'] = 'Ç';
	table['%E7'] = 'ç';
	table['%D6'] = 'Ö';
	table['%F6'] = 'ö';
	for (var code in table)
	{
		var r = new RegExp(code, 'ig');
		str = str.replace(r, table[code]);
	}
	try {
		str = oldDecode(str);
	} catch (e) {
	}
	return str;
};

Page.Params = Page.GetParameters();

var STYLIZE_INPUTS = true;
$(function () {
	console.time('common');
	if (USE_BS_UI)
		$('body').addClass('mode-bs');
	if (typeof DbModelForm_Init == 'function')
		DbModelForm_Init();
	// Sayfa Başlıklarını ayarla
	$('<span style="float:left" class="ui-icon ui-icon-signal-diag"></span>').appendTo($('.ers-page-header'));
	$('.ers-page-header').addClass('ui-widget-header ui-helper-clearfix ui-corner-all');
	$('.yetkisiz').find('INPUT,TEXTAREA,BUTTON').attr('disabled', 'disabled');

	$("[use_default_button] input").keypress(function (e) {
		if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
			$(this).change();
			$($(this).parents('[use_default_button]').get(0)).find('[default_button]').first().click();
			return false;
		} else {
			return true;
		}
	});

	$("[use_visible_default_button] input").keypress(function (e) {
		if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
			$(this).change();
			$($(this).parents('[use_visible_default_button]').get(0)).find('[default_button]:visible').first().click();
			return false;
		} else {
			return true;
		}
	});

	$('SELECT[page-refresh="1"],INPUT[page-refresh="1"]').each(function(){
		var param = $(this).attr('page-param');
		if (!param)
			param = $(this).attr('id');
		var pval = Page.GetParameter(param);
		if (pval)
			$(this).val(pval);
	}).change(Page.RefreshUrl);

	Mask.Telefon();
	Mask.InitMask();
	Jui.InitInputs();
	Jui.InitButtons();
	Jui.InitTables();
	Jui.InitPages();

	$('A.download').click(function(evt){
		var href = $(this).attr('href');
		Page.Download(href);
		evt.stopPropagation();
		return false;
	});

	var sc = Page.GetParameter('scroll');
	if (sc != '')
		$(document).scrollTo(parseInt(sc), 'slow');

	var bsTabs = Page.GetParameter('bs-tabs');
	if (bsTabs)
	{
		bsTabs = bsTabs.split(',');
		var uls = $('UL.nav-ers-tab LI.active').closest('UL');
		for (var i=0; i < bsTabs.length; i++)
			uls.eq(i).find('>LI').eq(bsTabs[i]).find('>a').tab('show');
	}

	// Form default düğme ayarlanması
	var forms = $('FORM');
	for (var i = 0; i < forms.length; i++)
	{
		var f = forms.get(i);
		if (f.action == '')
			f.action = 'index.php';
		if (typeof $(f).attr('use_default_button') === "undefined")
			$(f).attr('use_default_button', '1');
		for (var k = 1; k <= 4; k++)
		{
			var act = 'act' + (k > 1 ? k : '');
			var val = Page.GetParameter(act);
			if (typeof f.elements[act] != "undefined" || !val)
				continue;
			$('<INPUT type="hidden" />').
					attr('name', act).val(val).
					appendTo(f);
		}
	}
	// Disable edilmesi gereken elemanlar ayarlanıyor
	$('.disable_inputs').each(function(){
		$(this).find('INPUT,SELECT,TEXTAREA').attr('disabled', 'disabled');
		$(this).find('.ui-button').button('disable').attr('onclick', '');
	});

	//Dile göre mesajları tekrar ayarla
	Page.SAVING_MSG = translateLib("kaydediliyor") + '...';
	Page.LOADING_MSG = translateLib("yukleniyor") + '...';
	Page.DELETING_MSG = translateLib("siliniyor") + '...';
	Page.SENDING_MSG = translateLib("gonderiliyor") + '...';
	Page.UPDATING_MSG = translateLib("guncelleniyor") + '...';

	window.onbeforeunload = function(){
//		Page.Loading();
	};
	console.timeEnd('common');
});

tabberOptions = {
	onLoad: function (argsObj) {
		var s = Page.GetParameter('tab');
		if (s != '')
			argsObj.tabber.tabShow(parseInt(s));
	}
};

function Id(id)
{
	var obj = document.getElementById(id);
	if (obj)
		return obj;
	return $(id);
}

function GenerateCp()
{
	if (typeof cp == "undefined")
	{
		cp = new cpaint();
		cp.set_transfer_mode('post');
		cp.set_response_type('text');
	}
	return cp;
}

function is_set(val)
{
	return (typeof val != "undefined");
}

function ifEmpty(val, defVal)
{
	return val ? val : defVal;
}

function CheckVar(val, defVal)
{
	if (typeof defVal == "undefined")
		defVal = null;
	if (typeof val == "undefined")
		return defVal;
	return val;
}

/**
 *
 * @param {string} url verilen kıssa adres bilgisini tam adrese çevirir.
 * Örn: "admin.projeler" => "index.php?act=admin&act2=projeler"
 * @param mode
 * @param ajax
 * @returns {string}
 */
function GetUrl(url, mode, ajax, ext) {
	var str = ['act', 'act2', 'act3', 'act4'];
	// Göreceli adres mi?
	if (url[0] == '#')
	{
		var p = '';
		for (var i = 0; i <= 3; i++)
		{
			var a = Page.GetParameter(str[i]);
			if (!a)
				break;
			p += a + '.';
		}
		url = p + url.substr(1);
	}
	var acts = url.split('.');
	var sonuc = [];
	var usePath = UseRewriteAct == '1';
	if (usePath && acts.length > 0 && (acts[0] == 'developer'
			|| acts[0] == 'db_model' || acts[0] == 'cisc') )
		usePath = false;
	var splitter = usePath ? '/' : '&';
	for (var i = 0; i < acts.length; i++)
		if (i <= 3 && acts[i].indexOf('=') < 0)
			sonuc.push((usePath ? '' : str[i] + '=') + acts[i]);
		else
			sonuc.push(acts[i]);
	if (is_set(mode) && mode)
		sonuc.push('mode=' + mode);
	if (is_set(ajax) && ajax)
		sonuc.push('ajax=' + ajax);
	var baseUrl = window.location.pathname;
	if(typeof SITE_URL != 'undefined' && SITE_URL != '')
		baseUrl = SITE_URL;
	var qryPrm = '';
	if (sonuc != '')
		qryPrm = usePath ? 'act/' : '?';
	if (typeof ext == 'string' && ext)
		sonuc.push(ext);
	else if (typeof ext == 'object')
		for (var e in ext)
		{
			if (typeof ext[e] == 'object')
				ext[e] = -1;
			sonuc.push(e + '=' + ext[e]);
		}
	return baseUrl + qryPrm + sonuc.join(usePath ? '/' : '&');
}

PageDeleteRecord = function (id, pagedFuncName, url, params)
{
	if (typeof pagedFuncName != 'string')
		pagedFuncName = 'DeleteRecord';
	if (typeof url != 'string')
		url = '';
	var cb = function (resp) {
		if (resp != 1)
			Page.ShowError(resp);
		else
			Page.Refresh();
	};
	id = params || id;
	if(confirm('Seçili kaydı silmek istediğinize emin misiniz?'))
		Page.Ajax.Get(url).Send(pagedFuncName, id, cb, 'Kayıt siliniyor. Lütfen bekleyiniz...');
};

function HighlightField(obj, parent, msg)
{
	Form.ActiveBsTabs(obj);
	obj = $(obj);
	var div = obj.closest('.ui-tabs');
	if (div.length > 0)
	{
		var index = obj.closest('DIV.ui-tabs-panel').index();
		div.tabs({active: index - 1});
	}
	if (is_set(parent) && typeof parent == "string")
	{
		msg = parent;
		parent = null;
	}
	if (!is_set(parent) || !parent)
		parent = window;
	var fn = function () {
		if (obj.attr('rich_edit') == 1)
		{
			tinymce.execCommand('mceFocus', false, obj.attr('id'));
			var tinObj = tinyMCE.getInstanceById(obj.attr('id'));
			obj = tinObj.contentAreaContainer;
			$(tinObj.getBody()).css({backgroundColor: 'pink'});
			setTimeout(function(){
				$(tinObj.getBody()).css({backgroundColor: 'white'})
			}, 1000);
		}
		else
		{
			if (obj.hasClass('rich_edit_div') || obj.hasClass('textarea_list'))
				obj = obj.parent();
			$(obj).focus();
			$(obj).select();
			$(obj).effect("highlight", {color: 'pink'}, 3000);
		}
		$(parent).scrollTo(obj, 100, {offset: -$(parent).height() / 2});
	};
	if (msg)
		Page.ShowWarning(msg, fn);
	else
		fn();
	return null;
}

function FileSizeStr(bytes)
{
	var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
	if (bytes == 0)
		return 'n/a';
	else if (isNaN(bytes))
		return bytes;
	var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
	return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

function ArrayToObject(arr)
{
	var rv = {};
	for (var i = 0; i < arr.length; ++i)
		rv[i] = arr[i];
	return rv;
}

(function ($)
{
	$.fn.getList = function (attrName)
	{
		return $(this).map(function () {
			return attrName ? $(this).attr(attrName) : $(this).val();
		}).get();
	};

	$.fn.valNum = function (val)
	{
		if (is_set(val))
			return $(this).autoNumeric('set', val);
		return $(this).autoNumeric('get');
	};

	/**
	 * Şarta bağlı olarak obj nin görünümünü değiştirir ve bu bunları birbirine bağlar.
	 * @param {string} cond
	 * @param {bool} slider
	 * @return {jQuery}
	 */
	$.fn.CondVisible = function (cond, slider)
	{
		var patern = />=|<=|!=|==|>|</;
		var parts = cond.split(patern);
		if (parts.length != 2)
			return alert(cond + ' düzgün bir şart değil.');
		var opt = cond.match(patern);
		var obj = $(this);
		$(parts[0]).change(function () {
			var vis = eval('$(this).val() ' + opt + ' parts[1];');
			if (slider)
				vis ? obj.slideDown() : obj.slideUp();
			else
				obj.toggle(vis);
		}).change();
		return this;
	};

	$.fn.FilterInputVal = function (empty){
		return $(this).filter(function(){
			if (empty)
				return !$(this).val();
			else
				return $(this).val();
		})
	}
})(jQuery);

Cookie = {};
Cookie.set = function (cname, cvalue, path)
{
	if(typeof path == "undefined")
		path="";
	else
		path = " path='" + path + "' ";
	var now = new Date();
	var time = now.getTime();
	var expireTime = time + 30 * 24 * 60 * 60 * 1000;
	now.setTime(expireTime);
	document.cookie = cname + "=" + cvalue + ";expires=" + now.toGMTString() + ";" + path;
};

//expireMinute cinsinde olacak
Cookie.setWithTime = function (cname, cvalue, expireMinute, path)
{
	if(typeof path == "undefined")
		path="";
	else
		path = " path='" + path + "' ";
	var now = new Date();
	var time = now.getTime();
	var expireTime = time + expireMinute * 60 * 1000;
	now.setTime(expireTime);
	document.cookie = cname + "=" + cvalue + ";expires=" + now.toGMTString() + ";" + path;
};

Cookie.get = function (cname)
{
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++)
	{
		var c = ca[i].trim();
		if (c.indexOf(name) == 0)
			return c.substring(name.length, c.length);
	}
	return "";
};

Cookie.setElementsValue = function (cname, ids)
{
	var cbx = $('#' + cname).is(':checked') ? 1 : 0;
	ids = cname + ',' + ids;
	ids = ids.split(',');
	var obj = {};
	for (var i = 0; i < ids.length; i++)
	{
		var inp = $('#' + ids[i]);
		obj[ids[i]] = inp.val();
	}
	obj = JSON.stringify(obj);
	if (!cbx)
		obj = '';
	Cookie.set(cname, obj);
};

Cookie.getElementsValue = function (cname)
{
	var obj = Cookie.get(cname);
	if (obj == '')
		return;
	obj = JSON.parse(obj);
	for (var id in obj)
	{
		var inp = $('#' + id);
		if (inp.prop('tagName') == 'INPUT' && inp.attr('type') == 'checkbox')
			inp.attr('checked', obj[id] == 'on' ? 'checked' : '');
		else
			inp.val(obj[id]);
	}
};

/**
 * UI erişimi olmayan bir fonksiyonun ayrı bir thread
 * içinde çalışmasını sağlar
 * @param func function
 * @param funcParams object
 * @param returnCallback function
 * @param importFiles object
 * @returns
 */
Thread.Exec = function(func, funcParams, returnCallback, importFiles){
	if (typeof ssvkthread == "undefined" || typeof Worker == "undefined")
	{
		var resp = func.apply(undefined, funcParams);
		return returnCallback(resp);
	}
	var param = {
		  fn: func,
		  args: funcParams,
		  importFiles : importFiles
		};
	/* run thread */
	vkthread.exec(param).then(returnCallback);
};

Mask.InitMask = function(selector)
{
	if (!is_set(selector))
		selector = document;
	$(selector).find('[var_mask]').each(function () {
		if (!$(this).hasClass('hasMask'))
			$(this).mask($(this).attr('var_mask')).addClass('hasMask');
	});
};

Mask.Telefon = function (selector)
{
	if (!is_set(selector))
		selector = '.Telefon';
	var maskText = '+99 (999)999-9999';
	if(TEL_MASK == 2)
		maskText = '(999)999-9999';
	if(TEL_MASK == 3)
		maskText = '(599)999-9999';
	$(selector).each(function () {
		if (!$(this).hasClass('hasMask'))
			$(this).mask(maskText).addClass('hasMask');
	});
};

Mask.Date = function (selector)
{
	if (!is_set(selector))
		selector = '[date_selector="1"],[var_type=date]';
	$(selector).each(function () {
		if (!$(this).hasClass('hasMask'))
			$(this).mask('99-99-9999').addClass('hasMask');
	});
};

Mask.SaatDakika = function (selector)
{
	if (!is_set(selector))
		selector = '.SaatDakika';
	$(selector).each(function () {
		if (!$(this).hasClass('hasMask'))
			$(this).mask('99:99').addClass('hasMask');
	});
};

Tarih.Aylar = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
	'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];

Tarih.AylarKisa = ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz',
	'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'];

Tarih.AylarSayi = ['01', '02', '03', '04', '05', '06',
	'07', '08', '09', '10', '11', '12'];

Tarih.TumAylar = {'1': "Ocak" , '2': "Şubat", '3': "Mart", '4': "Nisan"
	, '5': "Mayıs", '6': "Haziran", '7': "Temmuz", '8': "Ağustos"
	, '9': "Eylül", '10': "Ekim", '11': "Kasım", '12': "Aralık"};

Tarih.Kontrol = function (tarih, icerikKontrol) {
	icerikKontrol = icerikKontrol || false;
	var ifade = /^[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{4}/;
	var bol = tarih.split(" ");
	if (bol.length == 2)
		ifade = /^[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{4}\s[0-9]{1,2}\:[0-9]{1,2}/;
	var format = tarih.match(ifade);
	if (!format || !icerikKontrol)
		return format;
	var date = Tarih.StrToDate(tarih);
	return date && (date.getFullYear() > 0);
};


Tarih.Bugun = function () {
	return Tarih.DateToStr(new Date());
};

Tarih.Simdi = function () {
	return Tarih.DateTimeToStr(new Date());
};

/**
 * @param {string} tarih
 * @returns {Date}
 */
Tarih.StrToDate = function (tarih)
{
	var part = tarih.split(" ");
	tarih = part[0];
	var parts = tarih.split('-');

	// Baştaki "0"lar parseInt sırasında problem çıkarabiliyor
	if (parts[0].charAt(0) == '0')
		parts[0] = parts[0].charAt(1);
	if (parts[1].charAt(0) == '0')
		parts[1] = parts[1].charAt(1);
	if (parseInt(parts[1]) == 0 || parseInt(parts[0]) == 0)
		return null;
	if (part.length == 2)
	{
		var saat = part[1];
		if (!saat.match(/^[0-9]{1,2}\:[0-9]{1,2}$/))
			return null;

		var zaman_part = saat.split(':');
		if (zaman_part[0].charAt(0) == '0')
			zaman_part[0] = zaman_part[0].charAt(1);
		if (zaman_part[1].charAt(0) == '0')
			zaman_part[1] = zaman_part[1].charAt(1);
	} else
	{
		zaman_part = [0, 0];
	}
	if (!tarih.match(/^[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{4}$/))
		return null;

	return new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]),
			parseInt(zaman_part[0]), parseInt(zaman_part[1]), 1);
};


Tarih.DateDiff = function (tarih1, tarih2)
{
	if (typeof tarih1 == "string")
		tarih1 = Tarih.StrToDate(tarih1);
	if (typeof tarih2 == "string")
		tarih2 = Tarih.StrToDate(tarih2);
	var fark = (tarih2 - tarih1) / 1000.0;
	return parseInt(Math.ceil(fark / (60 * 60 * 24)));
};

Tarih.MonthDiff = function (d1, d2)
{
	if (typeof d1 != "object")
		d1 = Tarih.StrToDate(d1);
	if (typeof d2 != "object")
		d2 = Tarih.StrToDate(d2);
    var months;
    months = (d2.getFullYear() - d1.getFullYear()) * 12;
    months -= d1.getMonth();
    months += d2.getMonth();
    return months;
};

Tarih.DateAdd = function (tarih, gun, tur)
{
	if (typeof tarih == "string")
		tarih = Tarih.StrToDate(tarih);
	if (typeof tur == "undefined")
		tur = 1;
	switch (tur)
	{
		case 30	:
			tarih.setMonth(tarih.getMonth() + gun);
			break;
		case 365:
			tarih.setFullYear(tarih.getFullYear() + gun);
			break;
		default:
			tarih.setDate(tarih.getDate() + gun);
	}
	return Tarih.DateToStr(tarih);
};

Tarih.DateToStr = function (dateObj)
{
	dateObj = dateObj || new Date();
	var d = dateObj.getDate();
	var m = dateObj.getMonth() + 1;
	var y = dateObj.getFullYear();
	if (d < 10)
		d = '0' + d;
	if (m < 10)
		m = '0' + m;
	return d + '-' + m + '-' + y;
};

Tarih.DateTimeToStr = function (dateObj)
{
	dateObj = dateObj || new Date();
	var d = dateObj.getDate();
	var m = dateObj.getMonth() + 1;
	var y = dateObj.getFullYear();
	var h = dateObj.getHours();
	var mn = dateObj.getMinutes();
	var s = dateObj.getSeconds();
	if (d < 10)
		d = '0' + d;
	if (m < 10)
		m = '0' + m;
	return d + '-' + m + '-' + y + " " + h + ":" + mn;
};

Tarih.GetYear = function ()
{
	var dateObj = new Date();
	var m = dateObj.getFullYear();
	return m;
};

Tarih.SetDateSelector = function (container) {
	var dateObj = {
		changeMonth: true,
		changeYear: true,
		onSelect: function (date) {
			var secondDate = $(this).attr('second_date');
			var secondDateObj = [];
			if (secondDate) {
				var pr = $(this).parent();
				while (secondDateObj.length == 0)
				{
					secondDateObj = pr.find(secondDate);
					pr = pr.parent();
					if (pr.length == 0)
						break;
				}// while
			}
			if (secondDateObj.length > 0)
			{
				var date2 = Tarih.StrToDate(Tarih.DateAdd(date, 1));
				secondDateObj
						.datepicker('setDate', date2)
						.datepicker('option', 'minDate', date2);
			}
			$(this).change();
		}
	};
	var yearRange = $(container).find('[date_selector="1"],[var_type=date]').attr('year_range');
	if(yearRange != "")
		dateObj.yearRange = yearRange;
	$(container).find('[date_selector="1"],[var_type=date]').each(function () {
		if($(this).attr("max_date") != "")
			dateObj.maxDate = new Date($(this).attr("max_date"));
		$(this).css('width', '10em').datepicker(dateObj);
	});
	Mask.Date();
	$(container).find('[var_type=datetime]').each(function () {
		if ($(this).hasClass('hasDatepicker'))
			return;
		var val = $(this).val();
		if (val)
		{
			var parts = val.split(':');
			if (parts.length == 3)
			{
				parts.splice(2);
				$(this).val(parts.join(':'));
			}
		}
		$(this).css('width', '12em');
		if (typeof DATETIME_PICKER_VER == "undefined" || DATETIME_PICKER_VER == 1)
		{
			var dtpObj = {
				changeMonth: true,
				changeYear: true,
				stepMinute: 10
			};
			if($(this).attr("max_date") != "")
				dtpObj.maxDate = new Date($(this).attr("max_date"));
			$(this).datetimepicker(dtpObj);
		}
		else
		{
			var step = $(this).attr('step') || 60;
			$(this).datetimepicker({
				format: 'd-m-Y H:i',
				step: step
			});
			$.datetimepicker.setLocale('tr');
		}
	});
	$(container).find('[var_type=time]').each(function () {
		$(this).timepicker({
			stepMinute: 5
		});
		var width = '6em';
		if ($(this).attr('width'))
			width = $(this).attr('width');
		$(this).css('width', width);
	});
	$(container).find('[var_type=month]').each(function () {
		$(this).monthpicker({
			pattern: 'mm-yyyy'
		});
		var width = '8em';
		if ($(this).attr('width'))
			width = $(this).attr('width');
		$(this).css('width', width);
	});
};

Tarih.WorkTimeToStr = function (time)
{
	if(time == 'null' || time == '')
		return '';
	var parts = time.split(':');
	var sonuc = '';
	var saat = parseInt(parts[0]);
	var gun = parseInt(saat / 8);
	saat = saat - gun * 8;
	var dk = parseInt(parts[1]);
	if (gun > 0)
		sonuc = gun + ' iş günü, ';
	if (saat > 0)
		sonuc += saat + ' saat, ';
	if (dk > 0)
		sonuc += dk + ' dk.';
	return sonuc;
};

Tarih.NextWeekDay = function (weekDay, date, direction)
{
	if (typeof date == "string")
		date = Tarih.StrToDate(date);
	while (date.getDay() != weekDay)
		date.setDate(date.getDate() + direction);
	return date;
};

jQuery(function ($) {
	$.datepicker.regional['tr'] = {
		closeText: 'kapat',
		prevText: '&#x3c;geri',
		nextText: 'ileri&#x3e',
		currentText: 'bugün',
		monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
			'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
		monthNamesShort: ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz',
			'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'],
		dayNames: ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi'],
		dayNamesShort: ['Pz', 'Pt', 'Sa', 'Ça', 'Pe', 'Cu', 'Ct'],
		dayNamesMin: ['Pz', 'Pt', 'Sa', 'Ça', 'Pe', 'Cu', 'Ct'],
		weekHeader: 'Hf',
		dateFormat: 'dd-mm-yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['tr']);
});

Page.Reload = function (withActiveTabs) {
	var url = location.href;
	if (withActiveTabs)
		url = Page.UrlWithActiveTab();
	Page.Load(url);
};
Page.ReloadWithTabs = function (){
	Page.Reload(true);
};

Page.Refresh = function (win) {
	if (typeof win != 'object' || typeof win.document != 'object')
		win = window;

	if (win.lastDataTableObj && ! win.lastDataTableObj.ShowPaging)
		return win.Page.Load(Page.UrlWithActiveTab(win));

	if (typeof win['RefreshPageFunc'] == 'function')
		return win['RefreshPageFunc']();

	if (win.Page)
		return win.Page.Load(Page.UrlWithActiveTab(win));
	win.location.reload();
};
Page.RefreshAndClose = function () {
	Page.Refresh(opener);
	if (opener && Page.GetParameter("mode") != '')
		close();
};
Page.GoHome = function () {
	Page.Load('index.php');
};

Page.FindNearestPageId = function()
{
	var pid = Page.GetParameter('__pg_id__');
	if (pid)
		return pid;
	var pageParams = Page.Params;
	var mxCnt = 0;
	for(var i in PageUrlTree)
	{
		var pg = PageUrlTree[i];
		var urlParams = Page.GetParameters(pg.url);
		var cnt = 0;
		for (var k in urlParams)
			if (urlParams[k] == pageParams[k])
				cnt++;
		if (cnt > mxCnt)
		{
			pid = pg.id;
			mxCnt = cnt;
		}
	}
	if (! pid || (mxCnt == 1 && pageParams['act'] == 'guest'))
		pid = 'P110';
	return pid;
};

Page.Open = function (pageStr, ext, win) {
	var page = JSON.TryParse(pageStr);
	if (!page)
		page = {U: pageStr, T: 0};
	if (typeof ext == 'string' && ext)
		ext = JSON.TryParse(ext, ext);
	if (typeof ext == 'object')
	{
		page = $.extend(page, ext);
		delete ext.T;
		delete ext.W;
		delete ext.H;
	}
	var url = GetUrl(page.U, page.T == 1 ? 'clear' : '', 0, ext);
	var evnt = window.event;
	if (evnt && evnt.ctrlKey)
		return window.open(url);
	if (evnt && evnt.shiftKey)
		return window.open(url, page.N);
	win = win || window;
	if (page.T == 1)
		win = Page.OpenNewWindow(url, page.N, page.W, page.H);
	else
		win.Page.Load(url);
	win.blur();
	win.focus();
	return win;
};

Page.Close = function () {
	window.close();
};

Page.GetParameter = function (name, defaultVal, href, rewrite) {
	if (!is_set(defaultVal))
		defaultVal = '';
	if (!is_set(rewrite))
		rewrite=false;
	if (!href)
		return ifEmpty(Page.Params[name], defaultVal);
	name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
	var regexS = "[\\?&]" + name + "=([^&#]*)";
	var regex = new RegExp(regexS);
	var results = regex.exec(href);
	var val = '';
	if (results == null)
		val = defaultVal;
	else
		val = decodeURIComponent(results[1].replace(/\+/g, " "));
	if (! rewrite || val)
		return val;
	// rewrite olabilir ve val boş geldi
	var parts = href.split(/[\/&]/);
	var act = true;
	for(var i=0; i<parts.length; i++)
	{
		if (i == 0 && parts[i] == 'act')
		{
			act = true;
			continue;
		}
		var part = parts[i];
		var p2 = part.split('=');
		var varName = '';
		if (act && p2.length == 1)
		{
			varName = 'act' + (i == 1 ? '' : i);
			val = part;
		}
		else if (p2.length == 2)
		{
			varName = p2[0];
			val = p2[1];
		}

		if (varName == name)
			return val;
	}// for

	return defaultVal;
};

Page.GetCurrentUrl = function (add, addPath, acts) {
	if (typeof addPath == "undefined")
		addPath = true;
	if (!is_set(acts))
		acts = ['act', 'act2', 'act3', 'act4', 'mode'];
	var url = addPath ? [window.location.pathname + '?'] : [];
	for (var i = 0; i < acts.length; i++)
	{
		var act = Page.GetParameter(acts[i]);
		if (act != '')
			url.push(acts[i] + '=' + act);
	}
	if (add)
		url.push(add);
	return url.join('&');
};

Page.GetUrlWithParameters = function (important) {
	var location = Page.GetWindowHref();
	var params = Page.Params;
	for (var name in params)
	{
		var isImportant = false;
		for (var i = 0; i < important.length; i++)
		{
			var re = new RegExp(important[i] + "[1-9]?");
			if (re.test(name))
			{
				isImportant = true;
				break;
			}
		}
		if (!isImportant)
			location = Page.UrlChangeParam(name, '', location);
	}
	return location;
};

Page.OpenNewWindow = function (page, wname, wi, he, extra) {
	if (page.indexOf(window.location.pathname) < 0 &&
			page.indexOf('http') < 0 && page[0] != '?' &&
			page.indexOf('index.php') < 0 &&
			!page.match(/\.(jpg|jpeg|png|gif|pdf|doc|xls|docx|xlsx|php|flv)$/i))
		page = GetUrl(page);
	var l = 0;
	var t = 0;
	if (typeof wi == "undefined")
		wi = 500;
	if (typeof he == "undefined")
		he = 500;
	if (wi == 'full' || wi == 0)
	{
		wi = window.screen.availWidth;
		l = 0;
	} else
		l = (window.screen.availWidth - wi) / 2;

	if (he == 'full' || he == 0)
	{
		he = window.screen.availHeight;
		if ($.browser.webkit)
			he -= 70;
		t = 0;
	} else
		t = (window.screen.availHeight - he) / 2;

	if (typeof extra == "undefined")
		extra = "menubar=1,scrollbars=1,resizable=1";
	var wnd = window.open(page, wname,
			'width=' + wi + ',' +
			'height=' + he + ',' +
			'left=' + l + ',' +
			'top=' + t + ',' +
			extra
			);
	wnd.blur();
	wnd.focus();
	return wnd;
};

Page.SAVING_MSG = translateLib("kaydediliyor") + '...';
Page.LOADING_MSG = translateLib("yukleniyor") + '...';
Page.DELETING_MSG = translateLib("siliniyor") + '...';
Page.SENDING_MSG = translateLib("gonderiliyor") + '...';
Page.UPDATING_MSG = translateLib("guncelleniyor") + '...';

Page.Saving = function(){
	Page.Loading(Page.SAVING_MSG);
}

Page.Loading = function(message, progress){
	Page.ShowProcessingMessage('body', message, progress);
};

Page.Loading.Type = 0;

Page.ShowProcessingMessage = function (jqSelector, message, progress) {
	if (!message)
		message = Page.LOADING_MSG;
	var obj = $(jqSelector);
	if (obj.length == 0 || obj == null)
		obj = $(jqSelector = 'body');

	if (typeof message != 'string')
		message = Page.LOADING_MSG;
	if (obj.hasClass('loading-show'))
	{
		obj.find('.loading-text').contents().first().replaceWith(message);
		var prg = obj.find('.progress').hide();
		if (progress > 0)
		{
			prg.show();
			prg.find('.progress-bar').width(progress + '%');
			var val = prg.find('.progress-val').html(progress + '%').removeClass('half');
			if (progress >= 50)
				val.addClass('half');
		}
		return;
	}
	obj.addClass('loading-show');
	var loader = $('<div>').addClass('loading').appendTo(obj);
	var div1 = $('<div>').addClass('loading-center').appendTo(loader);
	var div2 = $('<div>').addClass('loading-center-absolute').appendTo(div1);
	var obj = $('<div>').addClass('loading-object').appendTo(div2);
	var animates = [
		function(){
			obj.addClass('animate1');
		},
		function(){
			obj.addClass('animate2');
			$('<div>').addClass('object1').appendTo(obj);
			$('<div>').addClass('object2').appendTo(obj);
			$('<div>').addClass('object-logo').appendTo(obj);
		},
		function(){
			obj.addClass('animate3');
			$('<div>').addClass('object').appendTo(obj);
			$('<div>').addClass('object').appendTo(obj);
			$('<div>').addClass('object').appendTo(obj);
			$('<div>').addClass('object').appendTo(obj);
		},
		function(){
			obj.addClass('animate4');
			$('<div>').addClass('object').appendTo(obj);
			$('<div>').addClass('object').appendTo(obj);
			$('<div>').addClass('object-logo').appendTo(obj);
		}
	];
//	var idx = Math.round( (animates.length - 1) * Math.random() );
//	animates[idx]();
	animates[Page.Loading.Type]();
	var txtDiv = $('<div>').html(message).addClass('loading-text').appendTo(div2);
	if (typeof progress != "undefined")
	{
		div2.find('small').remove();
		var prg = $('<div>').addClass('progress').appendTo(txtDiv);
		$('<div>').addClass('progress-bar').width(progress + '%').appendTo(prg);
		var val = $('<div>').addClass('progress-val').html(progress + '%').appendTo(prg);
		if (progress >= 50)
			val.addClass('half');
	}
	else if (div2.find('small').length == 0)
		$('<small>').html(translateLib("lutfen_bekleyiniz")).appendTo(txtDiv);
};

Page.CloseProcessingMessage = function () {
	$('.loading-show').removeClass('loading-show');
	$('div.loading').remove();
	var obj = $('[old_overflow]');
	obj.css('overflow', obj.attr('old_overflow'));
	obj.removeAttr('old_overflow');
	$('#DIV_ProcessingMessage').remove();
};

Page.UrlWithActiveTab = function (wnd) {
	wnd = wnd || window;
	var url = wnd.location.href;
	var tab = '';
	try {
		if ($('.tabberlive', wnd.document).length > 0)
			tab = $('.tabberlive', wnd.document).get(0).tabber.getActiveIndex();
		else
			tab = wnd.$('.ui-tabs').tabs("option", "active");
	} catch (e) {
	}
	var sc = $(wnd).scrollTop();
	if (typeof RefreshScrollSelector != 'undefined')
		sc = $(RefreshScrollSelector).offset().top - 100;
	if (typeof wnd.TabIndex != "undefined")
		for (var i = 0; i < wnd.TabIndex; i++)
		{
			tab = wnd.$('.ui-tabs[tab_index=' + i + ']').tabs('option', 'active');
			url = Page.UrlChangeParam('tab' + (i == 0 ? '' : i + 1), tab, url);
		}
	else if (tab > 0)
		url = Page.UrlChangeParam('tab', tab, url);
	if (sc > 0 || Page.GetParameter('scroll') > 0)
		url = Page.UrlChangeParam('scroll', sc, url);

	var bsTabs = [];
	wnd.$('UL.nav-ers-tab LI.active').each(function(){
		bsTabs.push($(this).index());
	});
	if (bsTabs.length > 0)
		url = Page.UrlChangeParam('bs-tabs', bsTabs.join(','), url);
	if (typeof wnd.Ara == 'function')
	{
		var params = wnd.Ara(null, null, true);
		for (var name in params)
			if (Page.GetParameter(name, null, url) === null)
				url += '&' + name + '=' + params[name];
	}

	return url;
};

/**
 * url string ise verilen linke sayfayı yükler, eğer bir object ise mevcut linkte
 * url nesnesindeki değerleri değiştirerek yükler. Örneğin; url = {id: 2} ise
 * adres satırındaki id değerini 2 yarak tekrar yükler
 * @param {string|object} url
 */
Page.Load = function (url, baseUrl) {
	if (typeof url == 'object')
	{
		var newUrl = baseUrl || Page.UrlWithActiveTab();
		for(var i in url)
			newUrl = Page.UrlChangeParam(i, url[i], newUrl);
		url = newUrl;
	}
	window.location.href = url;
};

Page.RefreshUrl = function ()
{
	var obj = $(this);
	var param = obj.attr('page-param');
	if (!param)
		param = obj.attr('id');
	var newVal = obj.attr('page-param-val');
	if (!newVal)
		newVal = obj.val();
	Page.Load(Page.UrlChangeParam(param, newVal));
	return false;
};

Page.UrlChangeParam = function (param, newVal, url) {
	if (typeof url == "undefined")
		url = Page.GetWindowHref();
	if (UseRewriteAct != 1 && !url.match(/\?/))
		url += '?';
	var re = new RegExp("([^a-z0-9])" + param + "=([^&]+)?", "i");
	if (re.test(url))
		url = url.replace(re, "$1" + (newVal ? param + '=' + newVal : ''));
	else
	{
		let key = '&';
		if(typeof SITE_URL != "undefined" && url == SITE_URL)
			key = '?';
		url = url + key + param + '=' + newVal;
	}
	return url.replace(/&&/g, '&');
};

Page.GetScriptUrl = function(name){
	var arr = 0;
	if (! $.isArray(name))
		arr = 1, name = [name];
	var scripts = $('script[src]');
	var path = Page.GetWindowHref().replace(/[^/]*$/, '');
	if (! path.endsWith('/'))
		path += '/';
	for(var i=0; i<name.length; i++)
	{
		var re = new RegExp('/' + name[i], "i");
		for(var k=0; k<scripts.length; k++)
			if (re.test(scripts.eq(k).attr('src')))
			{
				name[i] = scripts.eq(k).attr('src');
				if (! name[i].startsWith('http'))
					name[i] = path + name[i];
				break;
			}
	}
	if (arr)
		name = name[0];
	return name;
};

Page.Ajax = function () {
	this.AutoCloseProcessingMessage = true;

	this._callback = function (resp) {
		Page.Ajax.ResponseWaiting = false;
		if (this.AutoCloseProcessingMessage && this.MessageShown)
			Page.CloseProcessingMessage();
		this.CallBackFunction(resp, this.RefreshStyle);
	};

	this.GetUrl = function () {
		var url = Page.GetWindowHref();
		return Page.UrlChangeParam('ajax', '1', url);
	};

	/**
	 * @returns {Page.Ajax}
	 */
	this.Send = function (ServerFunc, Params, CallBackFunc, Message, AutoCorrect) {
		if (is_set(AutoCorrect))
			this.AutoCorrect = AutoCorrect;
		if (is_set(CallBackFunc) && CallBackFunc != null)
			this.SetCallback(CallBackFunc);
		if (is_set(Message))
			this.Message = Message;
		if (Page.Ajax.ResponseWaiting && false) {
			Page.ShowError('İşleminiz devam ediyor, lütfen bekleyiniz.');
			return this;
		}
		this.MessageShown = false;
		if (this.Message != null && this.Message != '')
		{
			Page.Loading(this.Message);
			this.MessageShown = true;
		}
		Page.Ajax.ResponseWaiting = true;
		GenerateCp();
		cp.autoCorrectEncoding = this.AutoCorrect;
		cp.call(this.Url, ServerFunc, this, Params);

		return this;
	};

	/**
	 * @returns {Page.Ajax}
	 */
	this.SendBool = function (ServerFunc, CustomParams, CallBackFunction, Message, AutoCorrect) {
		var that = this;
		var cb = function (resp) {
			if (that.MessageShown)
				Page.CloseProcessingMessage();
			if (resp != '1')
				return Page.ShowError(translateLib("hata") + ': ' + resp);
			if (typeof CallBackFunction == 'function')
				CallBackFunction(resp);
		};
		return this.Send(ServerFunc, CustomParams, cb, Message, AutoCorrect);
	};

	/**
	 * @returns {Page.Ajax}
	 */
	this.SendJson = function (ServerFunc, CustomParams, CallBackFunction, Message, AutoCorrect) {
		var that = this;
		var cb = function (resp) {
			if (that.MessageShown)
				Page.CloseProcessingMessage();
			var sonuc = JSON.TryParse(resp, null);
			if (sonuc === null)
				return Page.ShowError(translateLib("hata") + ': ' + resp);
			if (typeof CallBackFunction == 'function')
				CallBackFunction(sonuc, resp);
		}
		return this.Send(ServerFunc, CustomParams, cb, Message, AutoCorrect);
	};

	/**
	 * @returns {Page.Ajax}
	 */
	this.CorrectEncoding = function (correct) {
		if (is_set(correct))
			this.AutoCorrect = correct;
		return this;
	};

	/**
	 * @returns {Page.Ajax}
	 */
	this.SetMessage = function (msg) {
		this.Message = msg;
		return this;
	};

	/**
	 * @returns {Page.Ajax}
	 */
	this.SetCallback = function (callback) {
		if (typeof callback == "function")
			this.CallBackFunction = callback;
		else if (typeof callback == "number")
			this.RefreshStyle = callback;
		return this;
	};

	this.Url = this.GetUrl();
	this.AutoCorrect = true;
	this.Message = Page.SAVING_MSG;
	this.MessageShown = true;
	this.CallBackFunction = Page.Ajax.GuncelleKapat;
	this.RefreshStyle = Page.Ajax.DefaultRefreshStyle;
};

Page.Ajax.ResponseWaiting = false;

/**
 * @returns {Page.Ajax}
 */
Page.Ajax.Get = function (url) {
	var ajx = new Page.Ajax();
	if (is_set(url) && url != '')
		ajx.Url = GetUrl(url, null, 1);
	return ajx;
};

/**
 * @param {string} ServerFunc Server Funciton Name
 * @param {object} CustomParams
 * @param {function} CallBackFunction
 * @param {string} Message
 * @param {bool} AutoCorrect yazım hatalarını otomatik düzelt, varsayılını true
 * @returns {Page.Ajax}
 */
Page.Ajax.Send = function (ServerFunc, CustomParams, CallBackFunction, Message, AutoCorrect) {
	return Page.Ajax.Get().Send(ServerFunc, CustomParams, CallBackFunction, Message, AutoCorrect);
};

/**
 * IRIT = If Response Is True call CallbackFunction
 * Page.Ajax.Send fonksiyonu ile aynı şekilde çalışır, tek fark CallBackFunction'a sadece işlem
 * sonuc true(1) ise çalıştırır, aksi halde Page.ShowError a gönderir.
 * @param {string} ServerFunc Server Funciton Name
 * @param {object} CustomParams
 * @param {function} CallBackFunction
 * @param {string} Message
 * @param {bool} AutoCorrect
 * @returns {Page.Ajax}
 */
Page.Ajax.SendBool = function (ServerFunc, CustomParams, CallBackFunction, Message, AutoCorrect) {
	return Page.Ajax.Get().SendBool(ServerFunc, CustomParams, CallBackFunction, Message, AutoCorrect);
};

/**
 * IRIJ = If Response Is Json call CallbackFunction
 * Page.Ajax.Send fonksiyonu ile aynı şekilde çalışır, tek fark CallBackFunction'a sadece işlem
 * sonuc Json nesnesi ise çalıştırır, aksi halde Page.ShowError a gönderir.
 * @param {string} ServerFunc Server Funciton Name
 * @param {object} CustomParams
 * @param {function} CallBackFunction
 * @param {string} Message
 * @param {bool} AutoCorrect
 * @returns {Page.Ajax}
 */
Page.Ajax.SendJson = function (ServerFunc, CustomParams, CallBackFunction, Message, AutoCorrect) {
	return Page.Ajax.Get().SendJson(ServerFunc, CustomParams, CallBackFunction, Message, AutoCorrect);
};


/**
 * Verilen parametrelere bağlı olarak json nesnesi olarak veriyi getirir,
 * gelen veri json değilse hata mesajı verir, bu işlem sırasında "yükleniyor" mesajı gösterir
 * @param {string} ServerFunc Server Funciton Name
 * @param {object} CustomParams
 * @param {function} CallBackFunction
 * @returns {Page.Ajax}
 */
Page.Ajax.Load = function (ServerFunc, CustomParams, CallBackFunction) {
	return Page.Ajax.SendJson(ServerFunc, CustomParams, CallBackFunction, Page.LOADING_MSG);
};


Page.Ajax.DO_NOTHING = 0;
Page.Ajax.REFRESH = 1;
Page.Ajax.CLOSE = 2;
Page.Ajax.REFRESH_AND_CLOSE = 3;
Page.Ajax.REFRESH_NO_MSG = 4;
Page.Ajax.REFRESH_WITH_MSG = 5;
Page.Ajax.REFRESH_AND_MSG = 6;

Page.Ajax.DefaultRefreshStyle = Page.Ajax.REFRESH_AND_CLOSE;

Page.Ajax.GuncelleKapat = function (yanit, style) {
	if (!is_set(style))
		style = Page.Ajax.REFRESH_AND_CLOSE;
	if (style == Page.Ajax.REFRESH_AND_MSG)
		return Page.ShowSuccess(yanit);
	if (yanit == '1')
	{
		if (style == Page.Ajax.DO_NOTHING)
			return;
		if (__unsavedChangesTracker)
			__unsavedChangesTracker.Stop = true;
		if (style == Page.Ajax.REFRESH_NO_MSG)
			return Page.Refresh();

		if (window.opener == window || window.opener == null || window.opener.closed)
			Page.ShowSuccess('İşleminiz başarıyla tamamlandı', Page.Refresh);
		else
		{
			switch (style)
			{
				case Page.Ajax.REFRESH_WITH_MSG:
				case Page.Ajax.REFRESH:
					var cb = function () {
						Page.Reload(true);
					};
					if (style == Page.Ajax.REFRESH)
						cb();
					else
						Page.ShowSuccess('İşleminiz başarıyla tamamlandı', cb);
					return;
				case Page.Ajax.CLOSE:
					return window.close();
				case Page.Ajax.REFRESH_AND_CLOSE:
					return Page.RefreshAndClose();
			}
		}
	} else
		Page.ShowError(translateLib("hata") + ': ' + yanit);
};

Page.ShowDialogBS = function(divId, width, height, action, actionName, cancelAction)
{
	if(typeof actionName == 'undefined')
		actionName = "Save";
	if (divId.charAt(0) == '#')
		divId = divId.substr(1);
	var div = $('#' + divId);
	var modalId = divId + '_modal';
	var modal = $('#' + modalId);
	if (modal.length == 0)
	{
		modal = $([
			'<div id="' + modalId + '" class="modal fade js-table" role="dialog">',
			'<div class="modal-dialog">',
			'<div class="modal-content">',
			'<div class="modal-header">',
			'<i class="fa fa-close" data-dismiss="modal"></i>',
			'<h4 class="modal-title">' + div.attr('title')  + '</h4>',
			'</div>',
			'<div class="modal-body">',
			'</div>',
			'<div class="modal-footer">',
			'<button type="button" class="btn btn-default cancel" data-dismiss="modal">Cancel</button>',
			'<button type="button" class="btn btn-info save">'+actionName+'</button>',
			'</div>',
			'</div>',
			'</div>',
			'</div>'
		].join("\n")).appendTo('body');

		div.appendTo(modal.find('.modal-body')).show();
		modal.find('BUTTON.save').click(function(){
			var sonuc = true;
			if (typeof action == "function")
				sonuc = action();
			if (sonuc)
				modal.modal('hide');
		});
		if(typeof cancelAction == 'function')
		{
			modal.find('BUTTON.cancel').click(function(){
				var sonuc = true;
				if (typeof cancelAction == "function")
					sonuc = cancelAction();
				if (sonuc)
					modal.modal('hide');
			});
		}
	}
	return modal.modal();
};

Page.ShowInfoDivBS = function(divId, width, height, action, actionName)
{
	// if(typeof actionName == 'undefined')
	// 	actionName = "Kaydet";
	if (divId.charAt(0) == '#')
		divId = divId.substr(1);
	var div = $('#' + divId);
	var modalId = divId + '_modal';
	var modal = $('#' + modalId);
	if (modal.length == 0)
		modal = $([
		'<div id="' + modalId + '" class="modal fade js-table" role="dialog">',
		  '<div class="modal-dialog">',
			'<div class="modal-content">',
			  '<div class="modal-header">',
				'<i class="fa fa-close" data-dismiss="modal"></i>',
				'<h4 class="modal-title">' + div.attr('title')  + '</h4>',
			  '</div>',
			  '<div class="modal-body">',
			  '</div>',
			  '<div class="modal-footer">',
				'<button type="button" class="btn btn-default cancel" data-dismiss="modal">Kapat</button>',
				// '<button type="button" class="btn btn-info save">'+actionName+'</button>',
			  '</div>',
			'</div>',
		  '</div>',
		'</div>'
		].join("\n")).appendTo('body');

	modal.find('.modal-body').html("");
	div.appendTo(modal.find('.modal-body')).show();
	/*modal.find('BUTTON.save').click(function(){
		var sonuc = true;
		if (typeof action == "function")
			sonuc = action();
		if (sonuc)
			modal.modal('hide');
	});*/
	return modal.modal();
};

Page.ShowDialog = function (divId, width, height, action, ekDiv)
{
	if (typeof divId == 'string' && divId.charAt(0) != '#')
		divId = '#' + divId;
	var div = $(divId).show();
	var o = new Object();
	o.width = width;
	o.height = height;
	o.modal = true;
	o.closeText = "Kapat";
	o.resize = function(event, ui){
		div.width( div.parent().width() - 30);
	};
	o.buttons = new Object();
	if ($.isArray(action))
	{
		o.buttons = action;
		for (var i = 0; i < o.buttons.length; i++)
		{
			var btn = o.buttons[i];
			if (btn.icon)
				btn.icons = {primary: btn.icon};
			if (typeof btn.cb == "function")
				btn.click = btn.cb;
		}
	}
	else
	{
		o.buttons[translateLib("tamam")] = function () {
			var ret = true;
			if (typeof action == "function")
				ret = action(div);
			if (ret)
				$(this).dialog('close');
		};
		o.buttons[translateLib("iptal")] = function () {
			$(this).dialog("close");
		};
	}
	o.dialogClass = 'fixed-dialog';
	div.dialog(o).dialogExtend({ maximizable: true });
	if (is_set(ekDiv) && !div.attr('ekDiv_eklendi'))
	{
		if (typeof ekDiv == 'string' && ekDiv.charAt(0) == '#' && $(ekDiv).length > 0)
			ekDiv = $(ekDiv);
		else
			ekDiv = $('<DIV>').append(ekDiv);
		ekDiv.css({float: 'left', paddingTop: '5px'});
		$('.ui-dialog[aria-describedby="' + divId.replace(/#/g, '') + '"] .ui-dialog-buttonset')
				.before(ekDiv);
		div.attr('ekDiv_eklendi', '1');
	}
	return div;
};

Page.CustomMessages = {};

var _AlertLast = null;
Page._Alert = function (msg, title, imgName, buttons, okCallback)
{
	msg = Page.GetCustomMessage(msg);
	Page.CloseProcessingMessage();
	var lib = KNJIZ_URL ? KNJIZ_URL : 'pravi/';
//	if(imgName)
//		imgName = '<img src="'+lib+'/dogru/images/' + imgName
//			+ '" style="float:left; padding-right: 10px;">';
	var div = $('<div></div>')
			.addClass('ers-alert-dialog')
			.html(msg.replace(new RegExp('\n', 'g'), '<br/>'));
	if (imgName)
		div.css({
			'background': 'url(' + lib + 'dogru/images/' + imgName + ') no-repeat 5px 5px',
			'padding-left': '60px'
		});
	var o = new Object();
	o.modal = true;
	o.width = $(window).width() > 500 ? 500 : $(window).width();
	o.closeText = "Kapat";
	o.title = title;
	if ($.browser.chrome)
		o.draggable = false;
	if (is_set(buttons) && buttons)
		o.buttons = buttons;
	else
	{
		let nme = translateLib("tamam");
		o.buttons = {nme: function () {
				$(this).dialog("close");
				if (is_set(okCallback))
					okCallback();
			}
		};
	}
	$('body').addClass('loading-show');
	o.close = function(){
		$('body').removeClass('loading-show');
	};
	return _AlertLast = div.dialog(o);
};

//old_alert = window.alert;
//window.alert =
Page.ShowInfo = function (msg, okCallback)
{
	msg = Page.GetCustomMessage(msg);
	if (typeof swal == 'function')
		return swal( {
			title: 'Bilgilendirme',
			text: msg,
			html: true,
			type: 'info',
			confirmButtonText: translateLib("tamam")
		}, okCallback);

	Page._Alert(msg, "Bilgilendirme", "info.png", null, okCallback);
};


Page.ShowInfoWithTitle = function (msg, okCallback, title)
{
	msg = Page.GetCustomMessage(msg);
	if (typeof swal == 'function')
		return swal( {
			title: title,
			text: msg,
			html: true,
			type: 'info',
			confirmButtonText: translateLib("tamam")
		}, okCallback);

	Page._Alert(msg, "Bilgilendirme", "info.png", null, okCallback);
};

Page.ShowSuccess = function (msg, okCallback)
{
	msg = Page.GetCustomMessage(msg);

	if (typeof swal == 'function')
		return setTimeout(function () {
			swal( {
				title: translateLib("basarili"),
				text: msg,
				type: 'success',
				confirmButtonText: translateLib("tamam")
			}, okCallback);
		},100);

	Page._Alert(msg, translateLib("basarili"), "success.png", null, okCallback);
	return true;
};

Page.ShowWarning = function (msg, okCallback)
{
	msg = Page.GetCustomMessage(msg);
	if (typeof swal == 'function')
		return swal( {
			title: 'Uyarı',
			text: msg,
			type: 'warning',
			html: true,
			confirmButtonText: translateLib("tamam")
		}, okCallback);

	Page._Alert(msg, "Uyarı", "warning.png", null, okCallback);
	return false;
};

Page.ShowError = function (msg, okCallback)
{
	msg = Page.GetCustomMessage(msg);
	if (typeof swal == 'function')
		return setTimeout(function (){
			swal( {
				title: translateLib("hata"),
				text: msg,
				type: 'error',
				html: true,
				confirmButtonText: translateLib("tamam")
			}, okCallback)
		},100);

	Page._Alert(msg, translateLib("hata"), "error.png", null, okCallback)
			.parent().find('.ui-dialog-titlebar').addClass('ui-state-error');
	return false;
};

Page.ShowFieldError = function (msg, inputSelector)
{
	msg = Page.GetCustomMessage(msg);
	var cb = function () {
		HighlightField(inputSelector);
	};
	return Page.ShowError(msg, cb);
};

Page.ShowConfirm = function (msg, okCallback, cancelCallback)
{
	msg = Page.GetCustomMessage(msg);
	if (typeof swal == 'function')
		return swal( {
			title: 'Onaylama',
			text: msg,
			type: 'warning',
			html: true,
			confirmButtonText: "Evet",
			cancelButtonText: "Hayır",
			showCancelButton: true
		}, function(isConfirm){
			if (isConfirm && typeof okCallback == 'function')
				okCallback();
			if (!isConfirm && typeof cancelCallback == 'function')
				cancelCallback();
		});

	var buttons = {
		"Evet": function () {
			$(this).dialog("close");
			if (typeof okCallback == 'function')
				okCallback();
			else
				Page.Open(okCallback);
		},
		"Hayır": function () {
			if (typeof cancelCallback == "function")
				cancelCallback();
			$(this).dialog("close");
		}
	};
	Page._Alert(msg, "Onaylama", "confirm.png", buttons);
};

Page.ShowPrompt = function (name, dflt, okCallback)
{
	if(typeof swal == 'function')
		return swal({
			title: name,
			type: "input",
			showCancelButton: true,
			closeOnConfirm: false,
			animation: "slide-from-top",
			confirmButtonText: translateLib("tamam"),
			cancelButtonText: translateLib("iptal")
		  },
		  function(inputValue){
			if (inputValue === false) return false;

			if (inputValue === "") {
			  swal.showInputError("Lütfen "+dflt+" alanına geçerli bir değer giriniz.");
			  return false;
			}
			var sonuc = okCallback(inputValue);
			if(typeof sonuc == "undefined" || (typeof sonuc != "undefined" && sonuc != 0))
			swal.close();
		  });
	var div = $('#ShowPromptDiv');
	if (div.length == 0)
	{
		div = $('<div>').attr('id', 'ShowPromptDiv').attr('title', name).appendTo('body');
		$('<input>').appendTo(div);
	}
	var inp = div.find('INPUT').val(dflt || '');
	Page.ShowDialog('ShowPromptDiv', 300, 120, function(){
		return okCallback(inp.val()); });
	setTimeout("$('#ShowPromptDiv').find('INPUT:first').focus();", 100);
};

/**
 * Verilen div elementini modal form olarak gösterir.
 * @param {string} divId
 * @param {int} width
 * @param {int} height
 * @param {string} okTitle (Seçimlilik)
 */
Page.ShowInfoDiv = function (divId, width, height, okTitle, okCallBack)
{
	if (divId.charAt(0) != '#')
		divId = '#' + divId;
	if (! okTitle)
		okTitle = translateLib("tamam");
	var div = $(divId);
	var title = div.attr('title');
	var o = {
			modal: true,
			width: width,
			height: height,
			closeText: "Kapat",
			title: title,
			draggable: false,
			close: function(){
				$('body').css('overflow', oldFlw).css('padding-right', '');
				div.attr('title', title);
			},
			buttons: {}
		};
	var oldFlw = $('body').css('overflow');
	o.buttons[okTitle] = function () {
		var sonuc = 1;
		if(typeof okCallBack == "function")
			sonuc = okCallBack();
		if(sonuc == 1)
			$(this).dialog("close");
	};
	o.buttons["cb"]
	if ($('body').height() > $(window).height())
		$('body').css('padding-right', '18px');
	$('body').css('overflow', 'hidden');
	return _AlertLast = div.dialog(o);
};

Page.FileDownloadMessage = function (msg) {
	if (!is_set(msg))
		msg = 'Dosya oluşturuluyor...';
	Page.Loading(msg);
	$(window).unbind('blur')
			.bind('blur', function () {
				Page.CloseProcessingMessage();
				$(window).unbind('blur');
			});
};

String.isEmail = function (str) {
	return str.match(/[a-z0-9._-]+@[a-z0-9._-]+\.[a-z0-9._-]+/i);
};

String.padEnd = function (str, length) {
	str = "" + str; // String türüne çevir
	for(var i=str.length; i<length; i++)
		str += " ";
	return str;
};

String.CleanMsWordChars = function (text, htmlEntities) {
	var swapCodes = new Array(8211, 8212, 8216, 8217, 8220, 8221, 8226, 8230); // dec codes from char at
	var swapStrings = new Array("--", "--", "'", "'", '"', '"', "*", "...");
	if (typeof htmlEntities != "undefined" && htmlEntities == true)
	{
		swapStrings[2] = "&#39;";
		swapStrings[3] = "&#39;";
		swapStrings[4] = "&quot;";
		swapStrings[5] = "&quot;";
	}
	for (var i = 0; i < swapCodes.length; i++)
	{
		var swapper = new RegExp("\\u" + swapCodes[i].toString(16), "g"); // hex codes
		text = text.replace(swapper, swapStrings[i]);
	}
	var entities = [
		/[\u20A0-\u219F]/gim,	// Currency symbols
		/[\u2200-\u22FF]/gim,	// Math operators
		/[\u2000-\u20FF]/gim,  // Weird punctuation
		/[\u0000-\u001F\u0090-\u009F]/gim	// String literal control characters
	];
	for (var i = 0; i < entities.length; i++)
	{
		var re = new RegExp(entities[i]);
		text = text.replace(re, function (c) {
			if (i == entities.length - 1)
				return '';
			return '&#' + c.charCodeAt(0) + ';';
		});
	}
	return $.trim(text);
};

String.DecodeEntities = (function () {
	// this prevents any overhead from creating the object each time
	var element = document.createElement('div');
	function decodeHTMLEntities(str) {
		if (!str)
			return str;
		if ($.isArray(str))
			for (var i = 0; i < str.length; i++)
				str[i] = String.DecodeEntities(str[i]);
		else if (typeof str === "object")
			for (var i in str)
				str[i] = String.DecodeEntities(str[i]);
		else if (typeof str === 'string') {
			// strip script/html tags
			if ($.isNumeric(str))
				return parseFloat(str);
			str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
			str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
			element.innerHTML = str;
			str = element.textContent;
			element.textContent = '';
		}
		return str;
	}
	return decodeHTMLEntities;
})();

String.ReverseQuoteEntities = function (str) {
	if (str && str != '')
	{
		str = str.replace(/\&\#39;/ig, "'");
		str = str.replace(/\&quot;/ig, '"');
		return str;
	} else
		return '';
};

String.Pad = function (n, width, z) {
	z = z || '0';
	n = n + '';
	return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
};

Table.MoveRow = function (btn, dir, changeRowIndex, callback, className)
{
	var selector = "TR";
	if ((typeof className != "undefined") && className != "")
		selector = "TR." + className;
	var row = $(btn).parents(selector).get(0);
	var tbl = $(row).parents('TABLE').get(0);

	if ((row.rowIndex == 1 && dir == -1) || (row.rowIndex == tbl.rows.length - 1 && dir == 1))
		return;

	var row2 = tbl.rows[ row.rowIndex + dir];

	$(tbl).find('TD').css({fontWeight: 'normal', backgroundColor: ''});
	$(row).find('TD').css({fontWeight: 'bold', backgroundColor: 'lightYellow'});
	$(row).fadeOut(400, function () {
		var bef = null;
		var aft = null;
		if (dir == -1)
		{
			bef = row;
			aft = row2;
		} else
		{
			bef = row2;
			aft = row;
		}
		bef.parentNode.insertBefore(bef, aft);
		$(this).show();

		// İlk hücreyi eski haline getir
		if (typeof changeRowIndex == "undefined" || changeRowIndex)
		{
			row.cells[0].innerHTML = row.rowIndex;
			row2.cells[0].innerHTML = row2.rowIndex;
		}

		if (typeof callback == "function")
			callback();
	});
};
/**
 * @param {string} tblSel
 * @param {string} templateOrCounts
 * @param {array} data
 * @param {function} callback
 * @param {int} rowIndex
 * @returns {TR}
 */
Table.AddNewRow = function (tblSel, templateOrCounts, data, callback, rowIndex)
{
	var templateSel = null;
	var cellCount = null;
	if (typeof templateOrCounts == "number")
		cellCount = templateOrCounts;
	else
		templateSel = templateOrCounts;

	var tbl = $(tblSel).find('TBODY').get(0);
	if (!is_set(rowIndex) || rowIndex === null)
		rowIndex = tbl.rows.length;
	var row = tbl.insertRow(rowIndex);
	if (templateSel)
	{
		var temp = $(templateSel);
		// İçerik
		$(row).html(temp.html());
		// Attributes
		var attrIgnoreList = ['style', 'class', 'id', 'new_class'];
		for (var i = 0; i < temp.get(0).attributes.length; i++)
		{
			var attr = temp.get(0).attributes[i];
			if ($.inArray(attr.name, attrIgnoreList) >= 0)
				continue;
			var val = attr.value;
			var match = val.match(/\$(.*)/i);
			if (match)
			{
				// Table.UpdateRow kısmında güncellenecek
				// o yüzden döngüde bir sonraki adıma atlıyoruz
				$(row).attr('_' + attr.name, val);
				continue;
			}
			$(row).attr(attr.name, val);
		}
		var cls = temp.get(0).getAttribute('new_class');
		if (cls)
			$(row).addClass(cls);
	} else
		for (var i = 0; i < cellCount; i++)
			row.insertCell(i);
	var rows = $(tbl).find("TR");
	for(var i=0; i<rows.length; i++)
		rows.eq(i).find('TD.SiraNo').html( i + 1);
	$(row).find(EmptyValSel('[id!=]')).removeAttr('id');
	$(row).find('.hasDatepicker').removeClass('hasDatepicker');
	Jui.InitInputs(row);
	// Veri
	if (is_set(data) && data)
		Table.UpdateRow(row, data);
	$(row).click(function () {
		var tbl = $(this).parents('TABLE').first();
		tbl.find('TR.selected-row')
				.removeClass('selected-row')
				.find('TD').first().css('border', '');
		var border = '3px solid green';
		$(this).addClass('selected-row')
				.find('TD').first()
				.css({borderLeft: border});
	});
	if (is_set(callback) && typeof callback == "function")
		callback(row);
	return row;
};

Table.DeleteSelectedRow = function (tbl)
{
	var sel = $(tbl).first().find('TR.selected-row');
	if (sel.length == 0 || !confirm('Seçili satırı silmek istediğinize emin misiniz?'))
		return;
	$(tbl).get(0).deleteRow(sel.get(0).rowIndex);
	var rows = $(tbl).find("TBODY TR");
	for(var i=0; i<rows.length; i++)
		rows.eq(i).find('TD.SiraNo').html( i + 1);
};

Table.DeleteAllRows = function (tbl)
{
	var rows = $(tbl).find('TBODY TR');
	for (var i = rows.length - 1; i >= 0; i--)
		$(tbl).get(0).deleteRow(rows.eq(i).rowIndex);
};

Table.UpdateRow = function (row, data)
{
	row = $(row).get(0);
	for (var i = 0; i < row.attributes.length; i++)
	{
		var attr = row.attributes[i];
		if (attr.name.charAt(0) != '_')
			continue;
		var val = attr.value;
		var match = val.match(/\$(.*)/i);
		if (match && typeof data[match[1]] != "undefined")
			val = data[match[1]];
		else if (match && !data)
			val = '';
		$(row).attr(attr.name.substring(1), val);
	}

	if (typeof data.length == "number")
	{
		for (var i = 0; i < data.length; i++)
			if (row.cells[i])
				row.cells[i].innerHTML = data[i];
	} else
		for (var i in data)
		{
			var el = $(row).find('.' + i);
			if (el)
			{
				var format = el.attr('format');
				if (format)
				{
					var matches = format.match(/(.*)\.(.*)/);
					if (matches)
						format = window[matches[1]][matches[2]];
					else
						format = window[format];
					if (typeof format == "function")
						data[i] = format(data[i]);
				}

				if (el.prop('tagName') == 'SELECT')
					el.val(data[i]).change();
				else{
					el.html(data[i]);
					Form.SetValue(el, data[i]);
				}
			}
		}
};

Table.GetSelectedIds = function (tblSel, idAttr)
{
	if (!is_set(idAttr))
		idAttr = 'row_id';
	var ids = [];
	$(tblSel).find('TBODY TR').each(function () {
		var cb = $(this).find('INPUT[type="checkbox"]').get(0);
		if (!cb.checked)
			return;
		ids.push($(this).attr(idAttr));
	});
	return ids;
};

Table.DelParentRow = function (obj)
{
	$(obj).parents('TR').first().remove();
};

/**
 *
 * @param {array} items item dizisi,
 *			her item nesnesi
 *			{text: '', cb: func|string, icon: '', target = '', sub: array}
 *			şeklinde bir nesnedir
 * @param isSub
 * @returns {JQuery}
 */
Jui.ul = function (items, isSub)
{
	var ul = $('<ul></ul>').appendTo('body');
	if (!isSub)
		ul.css('z-index', '1000');
	for (var i = 0; i < items.length; i++)
	{
		var item = items[i];
		var li = $('<li><a></a></li>');
		var a = li.find('a');
		if (item.icon && item.icon.substr(0,2) == 'ui')
			$('<span class="ui-icon">').addClass(item.icon)
				.css('float', 'left').appendTo(a);
		if (item.icon && item.icon.substr(0,2) == 'fa')
			$('<i class="fa">').addClass(item.icon).appendTo(a);
		if (item.text)
			$('<span>' + item.text + '</span>').appendTo(a);
		if (item.sub && item.sub.length > 0)
			li.append(Jui.ul(item.sub, true));
		if (typeof item.enabled != "undefined" &&
				!item.enabled)
			li.addClass('ui-state-disabled');
		if (typeof item.separator && item.separator)
			li.css('border-' + item.separator + '-style', 'solid')
					.css('border-' + item.separator + '-width', '1px');
		if (typeof item.cb == 'function')
		{
			a.attr('href', 'javascript:void(0)');
			a.click(item.cb);
		} else if (item.cb)
		{
			a.attr('href', item.cb);
			if (item.target)
				a.attr('target', item.target);
		}
		if (item.attr)
			for (var name in item.attr)
				a.attr(name, item.attr[name]);
		li.appendTo(ul);
	}
	return ul;
};

Jui.CreateButton = function(props){
	// Özellikleri kontrol et
	var btn = {text: '', cb: null, icon: '', tool: false, enabled: 1, visible: 1, separator: null};
	for (var prop in props)
		btn[prop] = props[prop];
	if (btn.enabled == '0' || btn.enabled == '' || btn.enabled == false)
		btn.enabled = 0;
	if (btn.visible == '0' || btn.visible == '' || btn.visible == false)
		btn.visible = 0;
	if (!btn.enabled)
		btn.cb = null;
	if (btn.cb && typeof window[btn.cb] == 'function')
		btn.cb = window[btn.cb];
	// BUTTON etiketini oluştur ve özellikleri yansıt
	var button = $('<button class="jui-button"></button>');
	var options = {icons: {}};
	if (btn.icon)
		options.icons = {primary: btn.icon};
	button.attr('default_button', "1");
	button.attr('icon', btn.icon);
	if (btn.text)
		button.html(btn.text);
	if (!btn.enabled)
		options.disabled = true;
	if (btn.tool)
		button.attr('toolbar', 1);
	if (btn.attr)
		button.attr(btn.attr);
	if (btn.cb)
		button.click(btn.cb);
	else
		button.click(function (e) {
			e.stopPropagation();
		});
	return button;
};

/**
 *
 * @param {string} parentSelector
 * @param {array|object} buttons Buton veya dizisi,
 *			her buton nesnesi
 *			{text: '', cb: func, icon: '', tool: false, enabled: 1, visible: 1}
 *			şeklinde bir nesnedir
 * @returns {jQuery}
 */
Jui.BUTTON_STYLE_NORMAL			= 1;
Jui.BUTTON_STYLE_BUTTONSET		= 2;
Jui.BUTTON_STYLE_DROPDOWN		= 3;
Jui.BUTTON_STYLE_DROPDOWN_SPLIT = 4;
Jui.button = function (parentSelector, buttons, style)
{
	if (!$.isArray(buttons))
		buttons = [buttons];
	if (typeof style == "undefined")
		style = Jui.BUTTON_STYLE_BUTTONSET;
	buttons = Array.Remove(buttons, function(obj) {
		if (typeof obj.visible == "undefined")
			return 0;
		return ! obj.visible;
	});
	if (buttons.length == 0)
		return;
	var p = $(parentSelector);
	if (p.hasClass('hasJuiButtons'))
		return p;
	if (p.length > 1)
	{
		for (var i = 0; i < p.length; i++)
			Jui.button(p[i], buttons, style);
		return p;
	}
	p.addClass('hasJuiButtons');
	var button = null;
	if (style == Jui.BUTTON_STYLE_NORMAL || style == Jui.BUTTON_STYLE_BUTTONSET)
	{
		var div = $('<div style="text-align: right; display: inline"></div>').appendTo(p);
		for(var i=0; i<buttons.length; i++)
		{
			button = Jui.CreateButton(buttons[i]);
			button.appendTo(div);
		}
		return Jui.InitButtons(div, style == Jui.BUTTON_STYLE_BUTTONSET);
	}

	if (style == Jui.BUTTON_STYLE_DROPDOWN || style == Jui.BUTTON_STYLE_DROPDOWN_SPLIT)
	{
		var div = $('<div style="text-align: right; display: inline"></div>').appendTo(p);
		// Menüyü açacak olan ana düğme ve gerekiyorsa split düğme üretiliyor
		var button = Jui.CreateButton(buttons[0]);
		var subButton = button; // Menü için click yapılacak button
		button.appendTo(div);
		if (style == Jui.BUTTON_STYLE_DROPDOWN_SPLIT)
		{
			subButton = Jui.CreateButton({text : buttons[1].text, tool : 1});
			subButton.appendTo(div);
		}
		else
			buttons[0].cb = null; // Bu düğmenin bir callback'i olmaması gerekiyor
		subButton.attr('icon', 'ui-icon-triangle-1-s');
		subButton.attr('icon_pos', 'right');
		Jui.InitButtons(div, 1);

		// Birinci düğmeyi diziden çıkar ve diziyi menü yap
		buttons.splice(0, 1);
		var ul = Jui.ul(buttons).css('position', 'absolute')
				.css('display', 'inline-block')
				.css('white-space', 'nowrap')
				.hide().menu();
		if (USE_BS_UI)
			ul.each(function(){
				var u = $(this);
				if (u.find('a[aria-haspopup]').length > 0)
					return;
				u.addClass('dropdown-menu')
				.removeClass('ui-menu ui-widget ui-widget-content ui-corner-all ui-menu-icons')
				.find('LI').removeClass('ui-menu-item')
				.find('A').each(function(){
					// Yeni ikonu ayarla
					var icon = $(this).find('SPAN:eq(0)');
					icon.css({float: 'none'});
					if (! icon.attr('class'))
						return;
					var classes = icon.attr('class').split(/\s+/);
					var newIcon = '';
					for(var i=0; i<classes.length; i++)
						if (classes[i] != 'ui-icon'){
							newIcon = BSui.IconMap(classes[i]);
							break;
						}
					icon.attr('class', 'fa fa-' + newIcon);
					// Metni ayarla
					var sp = $(this).find('SPAN:eq(1)');
					var text = sp.html();
					sp.remove();
					$(this).append(text);
				});
			});
		ul.css('font-size', subButton.css('font-size'));
		subButton.click(function () {
			CurrentContainer = $(this).parent();
			return Jui.ShowMenu(ul, this);
		});
		var width = button.width();
		if (subButton != button)
			width += subButton.width();
		ul.css('minWidth', width);
		ul.css('text-align', 'left');
		p.eq(0).ul = ul;
		return p;
	}

	return p;
};

Jui.buttonFromDiv = function(parentSelector, style){
	var buttons = $(parentSelector).find('BUTTON');
	var objects = [];
	for(var i=0; i<buttons.length; i++)
	{
		var button = buttons.eq(i);
		var btn = {text: '', cb: null, icon: '', tool: false, enabled: 1, visible: 1, separator: null};
		button.find('img,.fa').remove();
		btn.text = button.html();
		btn.cb = button.attr('onclick');
		if (btn.cb)
			btn.cb = btn.cb.replace(/(\(.*\)[; ]*)/, '');
		var events = button.data('events');
		if (typeof events != "undefined" &&
			typeof events.click != "undefined")
		{
			var last = events.click.length - 1;
			btn.cb = events.click[last].handler;
		}
		btn.icon = button.attr('icon');
		btn.enabled = ! button.prop('disabled');
		objects.push(btn);
	}
	buttons.remove();

	return Jui.button(parentSelector, objects, style);
};

Jui.ShowMenu = function (menu, item, toLeft) {
	if (Jui.CloseCurrentMenu(menu))
		return false;
	Jui.CloseCurrentMenu();
	var to = toLeft ? 'left' : 'right';
	CurrentMenu = menu.show().position({
		my: to + " top",
		at: to + " bottom",
		of: item
	});
	$(document).one("click", function () {
		Jui.CloseCurrentMenu();
	});
	return false;
};

Jui.CloseCurrentMenu = function (menu)
{
	if (!is_set(menu))
		menu = null;
	if (typeof CurrentMenu != 'undefined' && CurrentMenu
			&& (!menu || CurrentMenu == menu))
	{
		CurrentMenu.hide();
		CurrentMenu = null;
		return true;
	}
	return false;
};

Jui.tabs = function (selector)
{
	if (typeof window.TabIndex == "undefined")
		window.TabIndex = 0;
	$(selector).each(function () {
		var tabPane = $(this);
		if (tabPane.hasClass('hasJuiTabs'))
			return;
		tabPane.attr('tab_index', window.TabIndex);
		tabPane.addClass('hasJuiTabs');
		var tabs = tabPane.children();
		var items = [];
		var prefix = '';
		if ($('BASE').length > 0 && $('BASE').attr('href'))
			prefix = Page.GetWindowHref();
		var activeTab = 0;
		var ids = [];
		for (var i = 0; i < tabs.length; i++)
		{
			var tab = $(tabs.get(i));
			var id = tab.attr('id');
			if (!id)
			{
				id = 'tab' + i;
				tab.attr('id', id);
			}
			ids.push(id);
			if (tab.attr('active-tab'))
				activeTab = i;
			var opt = {text: tab.attr('title'), cb: prefix + '#' + id};
			tab.attr('tab_title', tab.attr('title'));
			tab.removeAttr('title');
			if (tab.attr('icon'))
				opt.icon = tab.attr('icon');
			items[items.length] = opt;
		}
		var ul = Jui.ul(items);
		ul.insertBefore(tabs.get(0));
		var tabParamName = 'tab';
		var tabIndex = window.TabIndex;
		if (tabIndex > 0)
			tabParamName += (window.TabIndex + 1);
		var urlParam = Page.GetParameter(tabParamName);
		if (urlParam)
		{
			var did = $.inArray(urlParam, ids);
			if (did >= 0)
				activeTab = did;
			else
				activeTab = parseInt(urlParam);
		}
//		if (USE_BS_UI)
//		{
//			ul.addClass('nav nav-tabs').attr('role', 'tablist');
//			ul.find('>li').attr('role', 'presentation');
//			var contDiv = $('<div class="tab-content">');
//			tabPane.find('>DIV').attr('role', 'tabpanel').addClass('tab-pane').appendTo(contDiv);
//			contDiv.appendTo(tabPane).find('DIV[role="tabpanel"]:first').addClass('active');
//			return;
//		}
		tabPane.tabs({
			active: activeTab,
			activate: function (event, ui) {
				ui.newTab.context.blur();
			},
			beforeActivate: function (event, ui) {
				if (!Tabs.EnableBeforeActivate)
					return;
				var url = ui.newPanel.attr('url');
				if (url && url.match(/^https?:\/\//i))
					Page.Load(url);
				else if (url)
				{
					var location = Page.GetWindowHref();
					if (url.match(/customTab=/))
					{
						location = Page.GetCurrentUrl();
						for (var i = 0; i < 4; i++)
						{
							var n = i == 0 ? 'tab' : 'tab' + i;
							if (Page.GetParameter(n))
								location += "&" + n + "=" + Page.GetParameter(n);
						}
					} else
						location = Page.GetUrlWithParameters(['act', 'tab', 'mode']);
					var params = url.split('&');
					for (var i = 0; i < params.length; i++)
					{
						var parts = params[i].split('=');
						location = Page.UrlChangeParam(parts[0], parts[1], location);
					}
					// Üstte bir tab'a tıklanmışsa, alt tab'a ait parametreler silinmeli
					var index = 1;
					var matches = tabParamName.match(/([0-9]+$)/);
					if (matches)
						index = matches[1];
					for (var i = index + 1; i <= 4; i++)
						location = Page.UrlChangeParam('tab' + (i <= 1 ? '' : i), '', location);
					location = Page.UrlChangeParam(tabParamName, ui.newTab.index(), location);
					if (! location.match(/__pg_id__/) && Page.GetParameter('__pg_id__'))
					{
						var last = location.charAt(location.length - 1);
						if (last != '&')
							location += '&';
						location += '__pg_id__=' + Page.GetParameter('__pg_id__');
					}
					Page.Load(location);
					return false;
				}
				else
				{
					var oTable = $('div.dataTables_scrollBody>table.dataTable', ui.newPanel);
					if (oTable.length > 0)
					{
						oTable = oTable.dataTable();
						oTable.fnAdjustColumnSizing();
					}
				}
			}
		}).show();
		tabPane.prop('TabParam', tabParamName);
		window.TabIndex++;
	}); // each
};

Jui.float = function (selector, options)
{
	if (typeof DECIMAL_SEPARATOR == "undefined")
		DECIMAL_SEPARATOR = '.';
	if (typeof THOUSAND_SEPARATOR == "undefined")
		THOUSAND_SEPARATOR = ',';
	options = options || {};
	// thousand
	if (typeof options.aSep == 'undefined')
		options.aSep = options.aSep || THOUSAND_SEPARATOR;
	// Decimal
	options.aDec = options.aDec || DECIMAL_SEPARATOR;
	if (!is_set(options.mDec))
		options.mDec = 2;
	// Sign
	if (!is_set(options.aSign))
		options.aSign = '';
	options.pSign = options.pSign || 's';
	if (!is_set(options.vMax))
		options.vMax = 1e20;
	if (!is_set(options.vMin))
		options.vMin = -1e20;
	return $(selector).css('text-align', 'right')
			.autoNumeric('destroy')
			.autoNumeric(options)
			.addClass('autoNumeric');
};

Jui.double = function (selector, options)
{
	options = options || {};
	if (!is_set(options.mDec))
		options.mDec = 4;
	return Jui.float(selector, options);
};

Jui.integer = function (selector, options)
{
	options = options || {};
	options.mDec = 0;
	return Jui.float(selector, options);
};

Jui.year = function (selector, options)
{
	options = options || {};
	options.vMax = 2500;
	options.aSep = '';
	$(selector).css('width', '5em');
	return Jui.integer(selector, options);
};

Jui.money = function (selector, options)
{
	options = options || {};
	if (!is_set(options.mDec))
		options.mDec = 2;
	options.aSign = options.aSign || ' TL';
	return Jui.float(selector, options);
};

Jui.moneysade = function (selector, options)
{
	options = options || {};
	if (!is_set(options.mDec))
		options.mDec = 2;
	options.aSign = options.aSign || '';
	return Jui.float(selector, options);
};

Jui.email = function (selector, options)
{
	$(selector).inputmask('email').addClass('hasInputMask');
};

Jui.emailExt = function (selector, options)
{
	$(selector).each(function(){
		var ext = $(this).attr('email_ext').replace(/([a-z])/ig, '\\$1');
		$(this).addClass('hasInputMask').inputmask({
            mask: "*{1,64}[.*{1,64}][.*{1,64}][.*{1,63}]" + ext,
            greedy: !1,
            definitions: {
                "*": {
                    validator: "[0-9A-Za-z!#$%&'*+/=?^_`{|}~-]",
                    cardinality: 1,
                    casing: "lower"
                },
                "-": {
                    validator: "[0-9A-Za-z-]",
                    cardinality: 1,
                    casing: "lower"
                }
            }
		});
	});
};

Jui.percentage = function (selector, options)
{
	options = options || {};
	if (!is_set(options.mDec))
		options.mDec = 0;
	options.aSign = options.aSign || ' %';
	options.vMin = 0;
	options.vMax = 100;
	return Jui.float(selector, options);
};
var __autoCompleteListed = {};
$.widget("custom.catautocomplete", $.ui.autocomplete, {
	_create: function() {
		this._super();
		this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
	},
	_renderMenu: function( ul, items ) {
		var that = this;
		$.each( items, function( index, item ) {
			var li;
			if (item.value == '')
			{
				ul.append( "<li class='ui-autocomplete-category' style='font-weight: bold;'>" + item.label + "</li>" );
				return;
			}
			li = that._renderItemData( ul, item );
		});
	}
});

Jui.InitAutoComplete = function(selector)
{
	$(selector).find('[auto_complete="1"]').catautocomplete({
		source: function(request, response){
			var data = { ad : request.term };
			var obj = $(this.element);
			var method = obj.attr('method');
			var act = '';
			var parts = method.split(':');
			if (parts.length > 1)
			{
				act = parts[0];
				method = parts[1];
			}
			var params = obj.attr('method_params');
			if (params)
				$.extend(data, JSON.parse(params));
			data.method = method;
			var key = JSON.stringify(data);
			if (__autoCompleteListed[key])
				response(__autoCompleteListed[key]);
			else
			{
				var cb = function(respText){
					var resp = JSON.TryParse(respText, null);
					if (resp == null)
					{
						response([]);
						console.log("Autocomplete çalışmadı: " + respText);
					}
					else
					{
						__autoCompleteListed[key] = resp;
						response(resp);
					}
				};

				Page.Ajax.Get(act).Send(method, data, cb, null);
			}
		},
		select: function (event, ui) {
			var obj = $(this);
			var methodCB = obj.attr('method_cb');
			if (typeof methodCB == 'string' && typeof window[methodCB] === 'function')
				window[methodCB](ui.item);
		},
		autoFocus: false,
		minLength : 0,
		delay: 200,
		open: function(event, ui) {
			$(this).catautocomplete("widget").css({
				"minWidth": ($(this).width() + "px")
			});
		}
	})
	.focus(function(){
		$(this).catautocomplete('search', $(this).val());
	});
};

Jui.InitInputs = function (selector, autoStylize)
{
	if (typeof autoStylize == "undefined")
		autoStylize = true;
	selector = selector || 'body';
	// Tüm inputları ui e göre düzenle
	if (STYLIZE_INPUTS && autoStylize)
	{
		$(selector).find('input:text,input:password').not('.normal-input').addClass('ui-widget').addClass('ui-widget-content').addClass('ui-corner-all');
		$(selector).find('select:not(.normal-input)').addClass('ui-widget').addClass('ui-widget-content').addClass('ui-corner-left');
	}
	Jui.InitCheckboxes(selector);
	$(selector).find('[var_type]').each(function () {
		var fnc = $(this).attr('var_type');
		var o = {mDec: $(this).attr('digit'), aSign: $(this).attr('unit'), pSign: $(this).attr('unit_dir')};
		if (fnc == 'int' || fnc == 'integer')
			Jui.integer(this, o);
		else if (fnc == 'year')
			Jui.year(this, o);
		else if (fnc == 'float')
			Jui.float(this, o);
		else if (fnc == 'double')
			Jui.double(this, o);
		else if (fnc == 'money')
			Jui.money(this, o);
		else if (fnc == 'moneysade')
			Jui.moneysade(this, o);
		else if (fnc == 'email')
			Jui.email(this, o);
		else if (fnc == 'email_ext')
			Jui.emailExt(this, o);
		else if (fnc == 'percentage')
			Jui.percentage(this, o);
	});
	Tarih.SetDateSelector(selector);
	RichEdit.Init(selector);

	$(selector).find('input[auto_list]').each(function () {
		var list = $(this).attr('auto_list').split('#|#');
		$(this).autocomplete({
			source: list, minLength: 0, delay: 0
		}).focus(function () {
			var inp = $(this);
			setTimeout(function () {
				inp.select();
			}, 50);
			$(this).autocomplete("search", "");
		});
	});
	$(selector).find('.data_form_item[dependency]').each(function () {
		var obj = $(this);
		var dep = obj.attr('dependency');
		var matches = dep.match(/^(.*):(.*)$/i);
		if (matches)
			dep = obj.parents(matches[1]).first().find(matches[2]);
		else
			dep = '#' + dep;
		var otherId = 'Backup_' + obj.attr('ListType');
		var backupSel = $('#' + otherId);
		var val = obj.val();
		if (backupSel.length == 0)
			backupSel = $('<SELECT></SELECT>')
					.attr('id', otherId)
					.appendTo('body')
					.html(obj.html())
					.hide();
		$(dep).change(function () {
			obj.val('');
			var items = $(this).find('OPTION:selected').attr('sub_items');
			if (!items)
				items = [];
			else
				items = JSON.parse(items);
			obj.find('OPTION[value!=""]').remove();
			for (var i = 0; i < items.length; i++)
			{
				var id = items[i];
				if (typeof items[i] == "object")
					id = items[i].Id;
				var options = backupSel.find('OPTION[value="' + id + '"]');
				for (var k = 0; k < options.length; k++)
				{
					var opt = options.get(k).outerHTML;
					$(opt).appendTo(obj);
				}
			}
			obj.change();
		}).change();
		obj.val(val).change();
	});

	$(selector).find('SELECT.buttonset').each(function () {
		var parent = $(this).parent();
		var sel = $(this).hide();
		if (sel.val() == '')
			sel.val($(this).find('OPTION[value!=""]').first().val());
		var div = $('<div>').attr('id', this.id + '_div').appendTo(parent);
		for (var i = 0; i < this.options.length; i++)
		{
			var opt = this.options[i];
			if (opt.value == '')
				continue;
			var id = this.id + '_opt_' + i;
			var checked = opt.selected ? 'checked' : '';
			$('<input type="radio" ' + checked + ' id="' + id + '" name="' + this.id + '" ' +
					'value="' + opt.value + '"><label for="' + id + '">' + opt.text + '</label>').appendTo(div);
		}
		var cb = function (inp) {
			inp.closest('DIV').find('INPUT')
					.button("option", "icons", {primary: 'ui-icon-radio-off'});
			inp.button("option", "icons", {primary: 'ui-icon-circle-check'});
		};
		div.buttonset();
		div.find('INPUT')
				.button("option", "icons", {primary: 'ui-icon-radio-off'})
				.change(function () {
					sel.val(this.value).change();
					cb($(this));
				});
		div.find('INPUT:checked').change();
		sel.change(function () {
			var inp = div.find('INPUT[value="' + this.value + '"]')
					.attr('checked', 'checked');
			cb(inp);
			div.buttonset();
		});
	});
	$(selector).find('TEXTAREA.textarea_list').each(function(){
		var parent = $(this).parent();
		var div = parent.find('DIV.textarea_list_div');
		if (div.length > 0)
			return;
		div = $('<div>').addClass('textarea_list_div clearfix').appendTo(parent);
		div.attr('list_for', $(this).attr('id'));
		var val = $(this).val() || '';
		var items = val.split("\n");
		for(var i=0; i<items.length; i++)
		{
			if (items[i] == '')
				continue;
			var del = $('<i class="fa fa-times">').click(TextAreaListItemDel);
			var sort = $('<i class="fa fa-ellipsis-v">');
			$('<span>').addClass('btn btn-default btn-xs')
				.appendTo(div).append(items[i]).append(sort).append(del).click(TextAreaListItemEdit);
		}
		$('<i class="btn btn-success fa fa-plus">').appendTo(div).click(TextAreaListItemAdd);
		div.sortable({items: 'span', helper: 'clone', handle: ".fa-ellipsis-v", stop: function(){ TextAreaListItemsSave(div); }});
	});

	var promptCb = function(evt){
		if ($(this).val() == 'Diğer')
		{
			$(this).find('OPTION:first').attr('selected', 'selected');
			$(this).trigger("chosen:updated");
			var select = this;
			var id = '#' + $(this).attr('id').replace(/\./, "\\.");
			Page.ShowPrompt(Form.GetInputTitle(id), '', function(val){
				if (! val)
					return false;
				$('<option>').html(val).appendTo(select);
				$(select).val(val).trigger("chosen:updated").change();
				return true;
			});
		}
	};
	if($('SELECT[editable_list=1]').length > 0)
		$('SELECT[editable_list=1]').each(function(){
			var obj = $(this);
			if (this.attributes['multiple'] && this.attributes['multiple'].value == "1")
				return;
			var val = this.attributes['value'].value;
			if (val && val != 0 && obj.find('OPTION[value="' + val + '"]').length == 0)
			{
				$('<option>').html(val).appendTo(obj);
				obj.val(val);
			}
			obj.chosen().change(promptCb);
		});
};

function TextAreaListItemsSave(div)
{
	var txt = div.parent().find('TEXTAREA');
	var items = [];
	div.find('SPAN').each(function(){
		items.push($(this).text());
	});
	txt.val(items.join("\n"));
}

function TextAreaListItemDel()
{
	var div = $(this).closest('.textarea_list_div');
	$(this).parent().remove();
	TextAreaListItemsSave(div);
}

function TextAreaListItemAdd()
{
	var that = $(this);
	var div = that.closest('.textarea_list_div');
	Page.ShowPrompt("Ekle", '', function(item){
		if (!item)
			return Page.ShowError('Lütfen bir değer giriniz');
		var del = $('<i class="fa fa-times">').click(TextAreaListItemDel);
		var sort = $('<i class="fa fa-ellipsis-v">');
		$('<span>').addClass('btn btn-default btn-xs')
			.appendTo(div).append(item).append(sort).append(del).click(TextAreaListItemEdit);
		TextAreaListItemsSave(div);
		that.appendTo(div);
		return true;
	});
}

function TextAreaListItemEdit()
{
	var that = $(this);
	var div = that.closest('.textarea_list_div');
	Page.ShowPrompt("Değiştir", $(this).text(), function(item){
		if (!item)
			return Page.ShowError('Lütfen bir değer giriniz');
		var del = $('<i class="fa fa-times">').click(TextAreaListItemDel);
		var sort = $('<i class="fa fa-ellipsis-v">');
		that.html('').append(item).append(sort).append(del);
		TextAreaListItemsSave(div);
		return true;
	});
}

Jui.InitTables = function (selector)
{
	selector = selector || 'body';
	$(selector).find('.jui-table THEAD TD').addClass('ui-state-focus').css('font-weight', 'bold');
	$(selector).find('.jui-table').each(function () {
		if ($(this).hasClass('header'))
			$(this).children('THEAD').find('TD')
					.removeClass('ui-state-focus')
					.addClass('ui-widget-header');
	});
	var bclr = Jui.GetCssValue('ui-state-focus', 'background-color');
	$(selector).find('.jui-table').each(function () {
		if ($(this).hasClass('dark-border-color'))
			bclr = 'gray';
		$(this).find('TD').css('border', '1px solid ' + bclr);
	});
	$(selector).find('TBODY[var_type="sortable"]').each(function () {
		$(this).sortable();
		var tbl = $(this).parent();
		var foot = $(tbl).find('tfoot');
		if (foot.length == 0)
			foot = $('<tfoot>').appendTo(tbl);
		if (!foot.attr('sortable_info'))
		{
			var tr = $('<tr><td colspan="' + tbl.find('TR').first().find('TD').length + '">' +
					'</td></tr>').appendTo(foot);
			var lib = KNJIZ_URL ? KNJIZ_URL : 'pravi';
			var img = lib + '/dogru/images/sortable.png';
			foot.attr('sortable_info', 1);
			tr.find('TD').addClass('ui-state-focus')
					.html('<small>Bu tablodaki veriler sürükle-bırak ile sıralanabilinmektedir</small>')
					.css({paddingLeft: '20px', background: 'url(' + img + ') no-repeat scroll 2px 3px'});
		}
	});
};

BSui.InitButtons = function (selector, buttonSet)
{
	$('.button_panel').each(function(){
		$(this).removeClass('button_panel').addClass('button_panel_bs');
		var div = $('<div>').addClass('panel panel-default');
		var div2 = $('<div>').addClass('panel-heading ui-helper-clearfix').appendTo(div);
		$(this).children().appendTo(div2);
		div.appendTo(this);
		if ($(this).css('position') == 'fixed')
		{
			$(this).find('.panel').css('border-radius', '0');
			$(this).find('.panel-heading').css('border-radius', '0');
			$(this).addClass('');
		}
	});
	selector = selector || 'body';
	var selObj = $(selector);
	var btnClassMap = {
		'btn_ui_save'  : ['btn-success', 'fa-check'],
		'btn_ui_add'   : ['btn-info', 'fa-plus-circle'],
		'btn_ui_person': ['btn-success', 'fa-user'],
		'btn_ui_cancel': ['btn-danger', 'fa-ban'],
		'btn_ui_search': ['btn-success', 'fa-search'],
		'btn_ui_print' : ['btn-info', 'fa-print'],
		'btn_ui_delete' :['btn-danger', 'fa-remove'],
		'btn_ui_default': ['btn-default', '']
	};
	for(var cls in btnClassMap)
	{
		var cls2 = btnClassMap[cls][0];
		var icon = btnClassMap[cls][1];
		selObj.find('BUTTON.' + cls + ',INPUT.' + cls).each(function(){
			if ($(this).hasClass('init-button'))
				return;
			$(this).addClass('init-button bs-button btn ' + cls2);
			if ($(this).attr('tool') || $(this).attr('toolbar'))
				$(this).html('');
			$(this).prepend($('<i>').addClass('fa ' + icon));
		});
	}

	selObj.find('.jui-button,.bsui-button').each(function () {
		BSui.InitSingleButton(this);
	});
	selObj.find('.bs-button')
			.addClass('btn-sm')
			.removeClass('ui-state-error')
			.removeClass('ui-state-focus');
	if (buttonSet)
	{
		var group = $('<div class="btn-group" role="group">').appendTo(selObj);
		selObj.children('.btn').appendTo(group);
	}
	else
		selObj.find('[toolbar]').removeClass('btn-sm').addClass('btn-xs');
};

BSui.IconMap = function(juiIcon){
	var map = {
		'trash': 'trash',
		'clipboard' : 'clipboard',
		'close' : 'window-close',
		'cancel': 'ban',
		'disk': 'check',
		'plus': 'plus-circle',
		'plusthick' : 'plus',
		'arrowthick-2-se-nw' : 'arrows-alt',
		'calculator' : 'calculator',
		'document' : 'file-word-o',
		'image' : 'file-pdf-o',
		'refresh' : 'refresh',
		'gear' : 'cog',
		'seek-first': 'fast-backward',
		'seek-prev': 'step-backward',
		'seek-next' : 'step-forward',
		'seek-end' : 'fast-forward',
		'search' : 'search',
		'triangle-1-s': 'chevron-down',
		'wrench' : 'wrench',
		'script' : 'pencil-square-o',
		'info' : 'info-circle',
		'copy' : 'files-o',
		'calendar' : 'calendar',
		'circle-close' : 'times-circle',
		'circle-plus' : 'plus-circle',
		'carat-2-e-w' : 'exchange',
		'key' : 'key',
		'print' : 'print',
		'check' : 'check',
		'power' : 'power-off',
		'newwin' : 'external-link',
		'locked': 'lock',
		'unlocked' : 'unlock',
		'mail-closed' : 'envelope',
		'comment' : 'comment',
		'folder-collapsed' : 'file',
		'pencil' : 'edit',
		'extlink' : 'external-link'
	};
	if (juiIcon.match(/ui\-icon\-/i))
		juiIcon = juiIcon.substring('ui-icon-'.length)
	var icon = map[juiIcon];
	if (! icon)
	{
		console.log('ui-icon-' + juiIcon + ' bulunamadi...');
		return 'check-circle';
	}
	return icon;
};

BSui.InitSingleButton = function(btn){

	if ($(btn).hasClass('init-button'))
		return;
	var icon = $(btn).attr('icon');
	var pos = $(btn).attr('icon_pos');
	var toolbar = $(btn).attr('toolbar');
	$(btn).addClass('init-button bs-button btn');
	if ($(btn).parents('.button_panel_bs').length > 0)
		$(btn).addClass('btn-primary');
	else
		$(btn).addClass('btn-info');
	if (toolbar)
		$(btn).html('');
	if (icon)
	{
		var icon2 = BSui.IconMap(icon);
		if (icon2 == 'ban')
			$(btn)
				.removeClass('btn-primary')
				.removeClass('btn-info')
				.addClass('btn-danger');
		var i = $('<i>').addClass('fa fa-' + icon2);
		if (pos == 'right')
			i.appendTo(btn);
		else
			$(btn).prepend(i);
	}
};
var _cbIdIndex = 1;
BSui.InitCheckboxes = function(parentSelector)
{
	$(parentSelector).find('INPUT[type="checkbox"]:not(.styled):not(.make-switch)').each(function(){
		var defStyle = 'success';
		if ($(this).parents('.dataTables_wrapper').length > 0)
			defStyle = 'primary';
		var div = $('<div class="checkbox checkbox-' + defStyle + '">');
		var cb = $(this);
		cb.before(div);
		cb.addClass('styled');
		var par = cb.parent();
		if (! cb.attr('id'))
		{
			cb.attr('id', '_cb_' + _cbIdIndex++);
			var label = par.find('LABEL');
			if (label.length > 0)
				label.attr('for', cb.attr('id'));
		}
		var id = cb.attr('id');
		cb.appendTo(div);
		var label = par.find('LABEL[for="' + id + '"]');
		if (label.length == 0)
			label = $('<label>').attr('for', id);
		label.appendTo(div);
	});
};

Jui.InitCheckboxes = function(parentSelector){
	$(parentSelector).find('INPUT.tristate[type="checkbox"]').each(function(){
		var el = $(this);
		if (el.hasClass('tristate-handled') || dtTableIds.length == 0)
			return true;
		el.data('checked', 0).addClass('tristate-handled');
		var clickCb = el.attr('onclick');
		var table = $('#' + dtTableIds[0]);
		el.attr('onclick', '').attr('_onclick', clickCb);
		el.click(function(e){
			switch(el.data('checked')) {
				case 0:
					el.data('checked',1);
					el.prop('indeterminate',true);
					break;
				case 1:
					el.data('checked',2);
					el.prop('indeterminate',false);
					el.prop('checked',true);
					break;
				default:
					el.data('checked',0);
					el.prop('indeterminate',false);
					el.prop('checked',false);
			}
			table.checkAllRows(el.prop('checked'));
			eval(clickCb);
		});
		table.gridLoad(function(e){
			$(this).find('[field_name="Sec"] INPUT[type="checkbox"]').click(function(){
				eval(clickCb);
			});
			if (el.prop('checked') && ! el.prop('indeterminate'))
				$(this).checkAllRows(true);
			else
				el.prop('checked', false).prop('indeterminate', false).data('checked', 0);
			eval(clickCb);
		});
	});
	if (USE_BS_UI)
		return BSui.InitCheckboxes(parentSelector);

};

Jui.DisableButton = function(obj, disable)
{
	obj = $(obj);
	obj.prop('disabled', disable);
	if (obj.hasClass('ui-button'))
		obj.button(disable ? 'disable' : 'enable');
}

Jui.InitButtons = function (selector, buttonSet)
{
	if (USE_BS_UI)
		return BSui.InitButtons(selector, buttonSet);
	$('.button_panel').addClass('ui-widget-header');
	selector = selector || 'body';
	$(selector).find('BUTTON.btn_ui_save,INPUT.btn_ui_save')
			.attr('icon', 'ui-icon-disk')
			.addClass('jui-button');
	$(selector).find('BUTTON.btn_ui_add,INPUT.btn_ui_add')
			.attr('icon', 'ui-icon-circle-plus')
			.addClass('jui-button');
	$(selector).find('BUTTON.btn_ui_person,INPUT.btn_ui_add')
			.attr('icon', 'ui-icon-person')
			.addClass('jui-button');
	$(selector).find('BUTTON.btn_ui_cancel,INPUT.btn_ui_cancel')
			.attr('icon', 'ui-icon-cancel')
			.addClass('jui-button')
			.addClass('ui-state-error-text');
	$(selector).find('BUTTON.btn_ui_delete,INPUT.btn_ui_delete')
			.attr('icon', 'ui-icon-closethick')
			.addClass('ui-state-error-text jui-button');
	$(selector).find('BUTTON.btn_ui_search,INPUT.btn_ui_search')
			.attr('icon', 'ui-icon-search')
			.addClass('jui-button');
	$(selector).find('BUTTON.btn_ui_print')
			.attr('icon', 'ui-icon-print')
			.addClass('jui-button');

	$(selector).find('.jui-button:not(.init-button)').each(function () {
		var options = {};
		if ($(this).attr('icon') && $(this).attr('icon_pos') == 'right')
			options.icons = {secondary: $(this).attr('icon')};
		else if ($(this).attr('icon'))
			options.icons = {primary: $(this).attr('icon')};
		if ($(this).attr('toolbar') || $(this).attr('tool'))
			options.text = false;
		$(this).button(options).addClass('init-button');
	});
	if (buttonSet)
		$(selector).buttonset();
	return $(selector);
};

Jui.InitPages = function ()
{
	var pages = $('[page_link]');
	pages.css('cursor', 'pointer');
	$('[page_link]').click(function () {
		var pg = $(this).attr('onclick');
		var id = $(this).attr('row_id');
		var obj = {};
		if (id)
			obj.id = id;
		Page.Open(window[pg], obj);
	});
};

Jui.GetCssValue = function (className, cssName)
{
	var element = $('.'.className).first();
	if (element.length == 0)
		element = $('<div>').html('&nbsp;').addClass(className)
				.appendTo('body').css('display', 'none');
	return element.css(cssName);
};

Jui.HtmlTooltip = function (selector)
{
	selector = selector || 'body';
//	$(selector).tooltip({
//		content: function () {
//			return $(this).prop('title');
//		}
//	});
};

RichEdit = {};
RichEdit.DefaultConf = {
	theme: "advanced",
	plugins: "pagebreak,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,"
			+ "preview,media,searchreplace,print,paste,directionality,fullscreen,noneditable,visualchars,"
			+ "nonbreaking,xhtmlxtras,template",
	paste_auto_cleanup_on_paste: true,
	theme_advanced_buttons1:
			"formatselect,fontselect,fontsizeselect,forecolor,backcolor,|,bold,italic,underline,strikethrough,"
			+ "justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,link,unlink",
	theme_advanced_buttons2:
			"cut,copy,paste,|,outdent,indent,blockquote,|,link,unlink,anchor,image,code," +
			"|,tablecontrols,|,",
	theme_advanced_buttons3:
			"hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,media,advhr,|,print,|,ltr,rtl,|,fullscreen,"
			+ "pasteword",
	theme_advanced_toolbar_location: "top",
	theme_advanced_toolbar_align: "left",
	theme_advanced_statusbar_location: "bottom",
	editor_selector: "rich_edit",
	convert_urls: false,
	valid_children: "+body[style]"
};

RichEdit.GetBasicConf = function(){
	var conf = $.extend({}, RichEdit.DefaultConf);

	delete conf.theme_advanced_buttons2;
	delete conf.theme_advanced_buttons3;
	conf.theme_advanced_buttons1 = "forecolor,backcolor,|,bold,italic,underline,strikethrough,"
		+ "justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,link,unlink";

	return conf;
};

RichEdit.Init = function (selector)
{
	selector = selector || 'body';
	var edits = $(selector).find('[rich_edit="1"]');
	var divEdits = $(selector).find('TEXTAREA.rich_edit_div');
	if (edits.length > 0 || divEdits.length > 0)
	{
		tinyMCE.init(RichEdit.DefaultConf);
		tinyMCEInited = 1;

		for (var i = 0; i < edits.length; i++)
		{
			var simple = edits.eq(i).attr('basic_toolbar');
			if (simple != null)
			{
				var conf = RichEdit.GetBasicConf();
				conf.editor_selector = '#' + edits.eq(i).attr('id');
				tinyMCE.init(conf);
			}
			else
				tinyMCE.init(RichEdit.DefaultConf);
			tinyMCE.execCommand('mceAddControl', false, edits.eq(i).attr('id'));
		}
	}

	divEdits.each(function(){
		var parent = $(this).parent();
		if (parent.find('DIV.rich_edit_div').length > 0)
			return;
		var val = $(this).val();
		$(this).hide();
		$('<div>').addClass('rich_edit_div').html(val).attr('title', $(this).attr('title'))
				.appendTo(parent).click(RichEditDivClick);
	});
};

function RichEditDivClick()
{
	var that = this;
	var inp = $(this).parent().find('textarea');
	var div = $('#rich_edit_div_form');
	var txtId = 'rich_edit_div_txt';
	if (div.length == 0)
	{
		div = $('<div>').attr('id', 'rich_edit_div_form').appendTo('body');
		$('<textarea>').attr('id', txtId).appendTo(div).val(inp.val());
		if (inp.attr('basic_toolbar') != null)
		{
			var conf = RichEdit.GetBasicConf();
			conf.editor_selector = '#' + txtId;
			tinyMCE.init(conf);
		}
		else
			tinyMCE.init(RichEdit.DefaultConf);
		tinyMCE.execCommand('mceAddControl', false, txtId);
	}
	else
		tinyMCE.get(txtId).setContent(inp.val());
	var cb = function(){
		var val = tinyMCE.get(txtId).getContent();
		$(that).html(val);
		inp.val(val);
		return true;
	};
	Page.ShowDialog('rich_edit_div_form', 700, 400, cb);
	$('.ui-dialog-title').html(Form.GetInputTitle(inp));
}

Form = {};
/**
 *
 * @param {string} selector belli bir kurala uyan veya sayfadaki tüm inputların
 * value değerlerini sayfa parametrelerinden alır.
 * @returns {undefined}
 */
Form.SetValueFromUrl = function (selector)
{
	var items;
	var types = 'input,select';
	if (is_set(selector))
		items = $(selector).find(types);
	else
		items = $(types);
	for (var i = 0; i < items.length; i++)
	{
		var item = $(items[i]);
		var key = item.attr('name');
		if (!key)
			key = item.attr('id');
		if (!key)
			continue;
		var val = Page.GetParameter(key, null);
		if (val === null)
			continue;
		if (item.attr('type') == 'checkbox')
			item.prop('checked', val.match(/^1|on$/g));
		else
			item.val(val);
	}
};

Form.SetFromObj = function (obj, parent, ignoreId)
{
	if (typeof obj == "string")
		obj = JSON.parse(obj);
	parent = parent || 'body';
	parent = $(parent);
	if (typeof ignoreId == "undefined")
		ignoreId = false;
	for (var name in obj)
	{
		if (! name || typeof obj[name] == "function")
			continue;
		try {
			var el = $('#' + name);
			if (el.length == 0 || ignoreId)
				el = $(parent).find('[name="' + name + '"]');
			if (el.length == 0)
				el = $(parent).find('.' + name);
			if (el.length == 0)
				continue;
			Form.SetValue(el, obj[name]);
		} catch (ex) {
			console.log ('Name=' + name + ', Value=' + obj[name]);
		}
	}
};

Form.ParseTemplate = function (obj, temp)
{
	temp = String(temp);
	for (var name in obj)
	{
		if (! name || typeof obj[name] == "function")
			continue;
		var reg = new RegExp('#' + name, 'g');
		temp = temp.replace(reg, obj[name]);
	}
	temp = temp.replace(/data\-src\=/g, 'src=');
	return temp;
};

Form.ActiveBsTabs = function (inp) {
	$(inp).parents('.tab-pane').each(function () {
		$('a[href="#' + $(this).attr('id') + '"]').tab('show');
	});
};

// verilen sınıfdaki inputların doldurulup doldurulmadığını verir
Form.CheckRequest = function (selector, parent)
{
	if(typeof parent == "undefined")
		parent = "body";
	var inputs = $(parent).find(selector);
	for (var i = 0; i < inputs.length; i++) {
		var richEdit = null;
		var inp = inputs[i];
		var val = Form.GetValue(inp);
		var upload_type = $(inp).attr('upload_type');
		if(typeof upload_type != 'undefined')
		{
			if (upload_type == UploadTypeSingle)
				val = $(inp).find('.upload input').attr('file_name');
			if(upload_type == UploadTypeImage)
			{
				var img = $(inp).find('img');
				val = '';
				if(img.attr('default_src') != img.attr('src'))
					val = img.attr('src');
			}
			if (upload_type == UploadTypeMulti)
				val = $(inp).find('TR.file').attr('data');
			if (upload_type == UploadTypeImageMulti)
			{
				val = "";
				if($(inp).find('.multi-image-gallery').html() != "")
					val = $(inp).find('.multi-image-gallery img').attr('src');
			}
		}
		var errorMsg = '';
		if ($(inp).hasClass('hasDatepicker') && !Tarih.Kontrol(val, true))
			errorMsg = ' alanına geçerli bir tarih yazmanız gerekmektedir';
		else if (typeof $(inp).inputmask == 'function' && ! $(inp).inputmask('isComplete'))
			errorMsg = ' alanına geçerli bir değer girmeniz gerekmektedir';
		else if (val == '' || typeof val == 'undefined')
			errorMsg = ' alanını doldurmak zorunludur.';
		if (errorMsg != '') {
			var nm = Form.GetInputTitle(inp);
			if( $(inp).attr('rich_edit') == '1')
				richEdit = $(inp);

			Page.ShowError(nm + errorMsg, function () {
				Form.ActiveBsTabs(inp);
				if (richEdit)
				{
					tinyMCE.get(richEdit.attr('id')).getBody().style.backgroundColor = 'pink';
					setTimeout(function(){
						tinyMCE.get(richEdit.attr('id')).getBody().style.backgroundColor = 'white';
					},2000);
				}
				else
				{
					var chz = $('#'+ inp.id + '_chosen');
					if (chz.length > 0)
						selectedInp = chz.parent();
					else
						selectedInp = inp
					setTimeout("HighlightField(selectedInp);", 100);
				}
			});
			return false;
		}
	}
	return true;
};

Form.GetInputTitle = function(inp)
{
	inp = $(inp);
	var nm = inp.closest('TR').find('LABEL[for="' + inp.attr('id') + '"]').text();

	if (!nm)
		nm = inp.closest('.form-group').find('LABEL[for="' + inp.attr('id') + '"]').text();
	if (!nm)
	{
		var attrs = ['display_name','title', 'placeholder', 'name', 'id'];
		for (var k = 0; k < attrs.length; k++)
		{
			var title = inp.attr(attrs[k]);
			if (title != null && title != '')
			{
				nm = title;
				break;
			}
		}
	}
	return nm;
};
// verilen sınıfdaki inputların değerlerini dizi olarak verir
Form.GetDataList = function (clsName, parent)
{
	if (typeof parent == 'undefined')
		parent = 'body';
	var inputs = $(parent).find('.' + clsName);
	var sonuc = new Object();
	for (var i = 0; i < inputs.length; i++)
	{
		var inp = inputs[i];
		var fName = $(inp).attr('field');
		var name = inp.id;
		if (fName)
			name = fName;
		if ($(inp).attr('upload_type') && typeof UploadTypeObj != "undefined")
		{
			var type = parseInt($(inp).attr('upload_type'));
			if (name)
				sonuc[name] = UploadTypeObj[type].GetData(inp);
			continue;
		}
		if ($(inp).hasClass('inp_picker'))
		{
			name = $(inp).attr('field');
			var txtField = $(inp).attr('text_field');
			if (name)
			{
				sonuc[name] = $(inp).find('.id_field').val();
				sonuc[txtField] = $(inp).find('.text_field').val();
			}
			continue;
		}
		if ($(inp).attr("var_type") == "money" || $(inp).attr("var_type") == "int")
		{
			if (name)
			{
				sonuc[name] = $(inp).value();
			}
			continue;
		}
		if (inp.getAttribute('field'))
			name = inp.getAttribute('field');
		if (name)
			sonuc[name] = Form.GetValue(inp);
	}

	return sonuc;
};

Form.GetValue = function (inp, wnd)
{
	if (typeof wnd != "undefined")
		inp = wnd.$(inp, wnd.document);
	else
		inp = $(inp);
	if (inp.attr('rich_edit') == '1')
	{
		var richEdit = tinyMCE.getInstanceById(inp.attr('id'));
		if (richEdit)
			inp.val(richEdit.getContent());
	}
	var val = null;
	if (inp.is('TEXTAREA,INPUT,SELECT'))
		val = inp.val();
	else
		val = inp.html();
	if (inp.hasClass('textarea_list'))
	{
		val = new Array();
		var inpVal = inp.val();
		if(inpVal != '');
			val=inpVal.split('\n');
	}
	if (inp.hasClass('autoNumeric'))
	{
		try {
			val = inp.autoNumeric('get');
		} catch(e) {
			val = Number.Parse(inp.val());
		}
		if (! isNaN(val))
			val = parseFloat(val);
		else
			val = 0;
	}
	if (inp.prop('type') == 'checkbox')
		val = inp.prop('checked') ? 1 : 0;
	if (inp.attr('custom_format') == 'number')
	{
		if (val == '')
			val = 0;
		else
			val = Number.Parse(val);
	}
	return val;
};

Form.SetValue = function (inp, val, wnd)
{
	if (typeof wnd != "undefined")
		inp = wnd.$(inp, wnd.document);
	else
		inp = $(inp);
	if (inp.length == 0)
		return inp;
	if ($(inp).attr('rich_edit') == '1')
	{
		var richEdit = tinyMCE.getInstanceById(inp.attr('id'));
		if (richEdit)
			richEdit.setContent(val);
	}
	else if (inp.prop('tagName') == 'SELECT' && val)
	{
		var opt = inp.find('OPTION').filter(function(){
			if ($(this).val() == val)
				return true;
			return false;
		});
		if (opt.length == 0)
			opt = $('<option></option>').val(val).html(val).appendTo(inp);
		opt.attr('selected', 'selected');
	}
	else if (inp.hasClass('autoNumeric'))
	{
		try {
			var numVal = val;
			if (isNaN(numVal))
				numVal = Number.Parse(numVal);
			inp.autoNumeric('set', val == '' || val == null ? 0 : numVal);
		} catch(e) {
			inp.val(parseFloat(val));
		}
	}
	else if (typeof inp.get(0).type != "undefined")
		inp.val(String.DecodeEntities(val));
	else
		inp.html(val);
	if (inp.attr('type') == 'checkbox')
		inp.prop('checked', val ? 1 : 0);
	return inp;
};

/**
 <pre>
 Verilen satırlardaki attribute ve/veya satır içi selector değerlerini
 dizi dizisi olarak döndürür

 Ör:
 Form.GetTrDataList(
 '.cihaz-degerlendirme',
 ['UrunId', 'FirmaId', 'Tur', 'Uygun=TD.uygun SELECT', 'Degerlendirme=TEXTAREA'])

 veya
 Form.GetTrDataList('TR.field-row',
 ['Field=.Field', 'Properties=.Properties'], 'Field!=""')
 veya
 Form.GetTrDataList(selector, 'ad,soyad') <==> Form.GetTrDataList(selector, ['ad=.ad', 'soyad=.soyad'])
 </pre>
 * @param {type} selector
 * @param {type} AttrNames
 * @param {type} validationCondition
 * @param {type} errorCondition
 * @param {type} errorMessage
 * @returns {Array|Form.GetTrDataList.records}
 *
 */
Form.GetTrDataList = function (selector, AttrNames, validationCondition, errorCondition, errorMessage)
{
	var rows = $(selector);
	var records = [];
	if (typeof AttrNames == "string")
	{
		var names = AttrNames.split(',');
		AttrNames = [];
		for (var i = 0; i < names.length; i++)
			AttrNames.push(names[i] + '=.' + names[i]);
	}
	for (var i = 0; i < rows.length; i++)
	{
		var row = rows.get(i);
		var r = {Id: -1};
		if ($(row).attr('row_id'))
			r.Id = $(row).attr('row_id');
		for (var a = 0; a < AttrNames.length; a++) {
			var name = AttrNames[a];
			var eslestirme = name.match(/([a-z_0-9]+)=([^:]*)(:.*)?/i);
			if (eslestirme)
			{
				name = eslestirme[1];
				selector = eslestirme[2];
				var attr = eslestirme[3];
				var obj = $(row).find(selector);
				if (obj.length == 0)
					continue;
				if (attr)
					r[name] = obj.attr(attr.substring(1));
				else if (obj.hasClass('autoNumeric'))
					r[name] = $(obj).value();
				else
				{
					if (obj.is('INPUT[type="checkbox"]'))
						r[name] = obj.attr('checked') ? 1 : 0;
					else if (obj.is('TEXTAREA,INPUT,SELECT'))
						r[name] = obj.val();
					else
						r[name] = obj.html();
					if (obj.attr('custom_format') == 'number')
						r[name] = Number.Parse(r[name]);
				}
			} else
				r[name] = row.getAttribute(name);
		}
		if (typeof validationCondition != "undefined" && validationCondition)
			with (r) {
				var s = eval(validationCondition);
				if (!s)
					continue;
			}
		if (typeof errorCondition != "undefined" && errorCondition)
			with (r) {
				var s = eval(errorCondition);
				if (s)
				{
					var els = $(row).find('INPUT:visible,SELECT:visible');
					if (els.length == 0)
						els = row;
					if (typeof errorMessage == "undefined")
						errorMessage = "Hatalı bilgi girişi bulunmaktadır, lütfen düzeltiniz";
					Page.ShowError(errorMessage, function () {
						HighlightField(els);
					});
					return null;
				}
			}
		records[records.length] = r;
	}
	return records;
};

// Verilen selector listesinin html/value'sunu döndürür. Eğer attr verilirse
// istenen attr değerleri listesi döndürülür
Form.GetValueList = function (selector, attr)
{
	var list = [];
	$(selector).each(function () {
		var val = null;
		if (is_set(attr))
			val = $(this).attr(attr);
		else
			val = $(this).is('TEXTAREA,INPUT,SELECT') != '' ? $(this).val() : $(this).html();
		list.push(val);
	});
	return list;
};

// Verilen nesne, içindeki değerlere bakarak,
// sayfa içindeki verilere göre güncellenir
Form.UpdateObj = function (obj, parentSelector, isGlobal, checkVal)
{
	if (!is_set(parentSelector) || !parentSelector)
		parentSelector = document;
	if (!is_set(isGlobal))
		isGlobal = false;
	if (!is_set(checkVal))
		checkVal = false;
	for (var name in obj)
	{
		var el = null;
		if (isGlobal && $('#' + name).length > 0)
			el = $('#' + name);
		else if (!isGlobal)
		{
			var els = [
				$(parentSelector).find('[name="' + name + '"]'),
				$(parentSelector).find('.' + name)];
			for (var i = 0; i < els.length; i++)
				if (els[i].length > 0)
				{
					el = els[i];
					break;
				}
		}
		if (!el)
			continue;
		var val = Form.GetValue(el);
		var valid = val;
		if (el.attr('custom_format') == 'number' || el.hasClass('autoNumeric'))
			valid = val > 0;
		obj[name] = val;
		if (checkVal && !valid && !el.hasClass('optional'))
		{
			Page.ShowError('Doldurulması zorunlu bazı alanlar boş bırakılmıştır',
					function () {
						HighlightField(el);
					});
			return null;
		}
	}
	return obj;
};

if (typeof Number == 'undefined')
	Number = {};
Number.Format = function (a, b, c, d)
{
	if (typeof b == "undefined")
		b = 2;
	if (typeof c == "undefined")
		c = DECIMAL_SEPARATOR;
	if (typeof d == "undefined")
		d = THOUSAND_SEPARATOR;

	a = Math.round(a * Math.pow(10, b)) / Math.pow(10, b);
	var e = a + '';
	var f = e.split('.');
	if (!f[0])
		f[0] = '0';
	if (!f[1])
		f[1] = '';
	if (f[1].length < b)
	{
		var g = f[1];
		for (i = f[1].length + 1; i <= b; i++) {
			g += '0';
		}
		f[1] = g;
	}

	if (d != '' && f[0].length > 3)
	{
		var h = f[0];
		f[0] = '';
		for (var j = 3; j < h.length; j += 3) {
			var i = h.slice(h.length - j, h.length - j + 3);
			f[0] = d + i + f[0] + '';
		}
		j = h.substr(0, (h.length % 3 == 0) ? 3 : (h.length % 3));
		f[0] = j + f[0];
	}
	c = (b <= 0) ? '' : c;
	return f[0] + c + f[1];
};

Number.Parse = function (value, decimal)
{
	if (value == '')
		return value;
	if (!decimal)
		decimal = -1;
	if (typeof value == "number")
		return decimal >= 0 ? value.toFixed(decimal) : value;
	value = String(value)
			.replace(new RegExp("[^0-9" + DECIMAL_SEPARATOR + "-]", "g"), '')
			.replace(new RegExp("[" + DECIMAL_SEPARATOR + "]", "g"), '.');
	value = parseFloat(value);
	if (decimal >= 0)
		value = value.toFixed(decimal);
	return value;
};

Number.TryParse = function(value, defaultValue){
	value = Number.Parse(value);
	if (typeof defaultValue == "undefined")
		defaultValue = 0;
	if(isNaN(value))
		value = defaultValue;
	return value;
};

Number.Round = function (value, n)
{
	if (! is_set(n))
		n = 2;
	n = Math.pow(10, n);
	return Math.round(n * value) / n;
}

Tabs = {};
Tabs.EnableBeforeActivate = true;
Tabs.GetIndex = function (tabObj) {
	tabObj = tabObj || $('.ui-tabs');
	return $(tabObj).tabs('option', 'active');
};

Tabs.SetIndex = function (index, tabObj) {
	tabObj = tabObj || $('.ui-tabs');
	return $(tabObj).tabs({active: index});
};

Tabs.GetIndexByName = function (name, tabObj) {
	tabObj = tabObj || $('.ui-tabs');
	return $(tabObj).find('UL.ui-tabs-nav LI:contains("' + name + '")').index();
};

Tabs.SetIndexByName = function (name, tabObj) {
	tabObj = tabObj || $('.ui-tabs');
	var index = Tabs.GetIndexByName(name, tabObj);
	if (index > 0)
		return Tabs.SetIndex(index, tabObj);
};

Tabs.HideTab = function (index, tabObj) {
	tabObj = tabObj || $('.ui-tabs');
	$(tabObj).find('UL.ui-tabs-nav LI:eq(' + index + ')').hide();
};

Tabs.ShowTab = function (index, tabObj) {
	tabObj = tabObj || $('.ui-tabs');
	$(tabObj).find('UL.ui-tabs-nav LI:eq(' + index + ')').show();
};

Tabs.ToggleTab = function (index, condition, tabObj) {
	if (condition)
		Tabs.ShowTab(index, tabObj);
	else
		Tabs.HideTab(index, tabObj);
};
if (typeof JSON == "undefined")
	JSON = {};
JSON.TryParse = function (str, defaultObj) {
	try {
		str = String.CleanMsWordChars(str);
		obj = JSON.parse(str);
		return obj;
	} catch (e) {
		if (typeof defaultObj == "undefined")
			defaultObj = null;
		return defaultObj;
	}
};

Array.FindByKey = function (array, key, value)
{
	if ($.isArray(array))
		for (var i = 0; i < array.length; i++)
		{
			if (array[i][key] == value)
				return array[i];
		}
	else if ($.isPlainObject(array))
		for (var i in array)
		{
			if (array[i][key] == value)
				return array[i];
		}
	return null;
};

Array.RemoveByKey = function(array, key, value){
	var cb = function(obj){
		return obj[key] == value;
	};
	return Array.Remove(array, cb);
};

Array.Remove = function(array, callBack){
	var newArray = [];
	for (var i=0; i<array.length; i++)
		if(! callBack(array[i]))
			newArray.push(array[i]);
	return newArray;
};

Array.GetPropertyValues = function (array, propName)
{
	var propValues = [];
	if ($.isArray(array))
		for (var i = 0; i < array.length; i++)
			propValues.push(array[i][propName]);
	else
		for (var i in array)
			propValues.push(array[i][propName]);
	return propValues;
};

var __unsavedChangesTracker = null;
var __stopUnsavedTracking = false;
function UnsavedTracker(parentSelector)
{
	this.Stop = false;
	this.Data = null;
	this.DbModel = typeof DbModelObj == "undefined" ? 0 : 1;
	this.DataFunction = null;
	var inputs = null;
	if (typeof parentSelector == "function")
		this.DataFunction = parentSelector;
	if (!this.DbModel && !this.DataFunction)
	{
		parentSelector = parentSelector || 'body';
		inputs = $(parentSelector).find('INPUT[type="text"],SELECT,TEXTAREA');
	}
	__unsavedChangesTracker = this;
	// Sayfa yüklendikten biraz sonra değerleri alıyoruz
	setTimeout('__unsavedChangesTracker.setInitialValues();', 750);
	this.setInitialValues = function () {
		if (this.DataFunction)
			this.Data = this.DataFunction();
		else if (this.DbModel)
			this.Data = DbModelForm_Save(DbModel_CustomSaveFunc, true);
		else
			for (var i = 0; i < inputs.length; i++)
				inputs.eq(i).attr('initial_data', inputs.eq(i).val());
	};
	this.getChanged = function () {
		if (this.DbModel || this.DataFunction)
		{
			var o1 = this.Data;
			var o2 = null;
			if (this.DataFunction)
				o2 = this.DataFunction();
			else
				o2 = DbModelForm_Save(DbModel_CustomSaveFunc, true);
			if (JSON.stringify(o1) != JSON.stringify(o2))
				return true;
		} else
			for (var i = 0; i < inputs.length; i++)
				if (inputs.eq(i).val() != inputs.eq(i).attr('initial_data'))
					return true;
		return false;
	};

	window.onbeforeunload = function (e) {
		var obj = __unsavedChangesTracker;
		if (obj.Stop || !obj.getChanged() || __stopUnsavedTracking)
			return;
		if (!e)
			e = window.event;
		e.returnValue = 'Sayfadan ayrıldığınızda ' +
				'yapılan değişiklikler kaybedilecektir';
	};

	$(window).unload(function(){
		Page.Loading();
	});
}

jQuery.expr[':'].Contains = function (a, i, m) {
	var a = jQuery(a).text().replace(/İ|ı/gi, 'I').toUpperCase();
	var b = m[3].replace(/İ|ı/gi, 'I').toUpperCase();
	return a.indexOf(b) >= 0;
};
// Bootstrap temasında üretilen button'ları
// .button() fonksiyonuyla çağrıldığında çıkacak
// hataları engellemek için
var oldButtonFunc = jQuery.fn.button;
jQuery.fn.button = function(){
	if (USE_BS_UI &&
		! $(this).hasClass('ui-button') &&
		typeof arguments[0] == "string" &&
		arguments[0].match(/(disable|enable)/))
	{
		if (arguments[0] == 'disable')
			$(this).attr('disabled', 'disabled')
		else
			$(this).removeAttr('disabled');
		return $(this);
	}
	return oldButtonFunc.apply(this, arguments);
};
function EmptyValSel(selector)
{
	if (parseInt($.fn.jquery) >= 2)
		selector = selector.replace(/\=\]/g, '=""]');
	return selector;
}

function copyToClipboard(text) {
	if (window.clipboardData && window.clipboardData.setData) {
		// IE specific code path to prevent textarea being shown while dialog is visible.
		return clipboardData.setData("Text", text);
	} else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
		var textarea = document.createElement("textarea");
		textarea.textContent = text;
		textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in MS Edge.
		document.body.appendChild(textarea);
		textarea.select();
		try {
			return document.execCommand("copy");  // Security exception may be thrown by some browsers.
		} catch (ex) {
			console.warn("Copy to clipboard failed.", ex);
			return false;
		} finally {
			document.body.removeChild(textarea);
		}
	}
}

function translateLib(key)
{
	let words = {
		"tr" :
			{
				"basarili" 	: "Başarılı",
				"tamam" 	: "Tamam",
				"hata" 		: "Hata",
				"iptal" 	: "İptal",
				"kaydediliyor" 	: "Kaydediliyor",
				"yukleniyor" 	: "Yükleniyor",
				"siliniyor" 	: "Siliniyor",
				"gonderiliyor" 	: "Gönderiliyor",
				"guncelleniyor" : "Güncelleniyor",
				"lutfen_bekleyiniz" : "Lütfen Bekleyiniz",
				"alanlar" : "Alanlar",
			},
		"en" :
			{
				"basarili" 	: "Successful",
				"tamam" 	: "Ok",
				"hata"	 	: "Error",
				"iptal" 	: "Cancel",
				"kaydediliyor" 	: "Recording",
				"yukleniyor" 	: "Loading",
				"siliniyor" 	: "Deleting",
				"gonderiliyor" 	: "Sending",
				"guncelleniyor" : "Updating",
				"lutfen_bekleyiniz" : "Please Wait",
				"alanlar" : "Fields",
			}
	};

	if (typeof LANGUAGE == "undefined")
		LANGUAGE = "tr";

	return words[LANGUAGE][key];
}

jQuery.fn.value = function(){
	var arg = arguments;
	if (arg.length == 0)
	{
		var val = Form.GetValue(this);
		if ($(this).attr('var_type') == 'money' || $(this).attr('var_type') == 'moneysade' || $(this).attr('var_type') == 'int')
		{
			val = parseFloat(val);
			if (isNaN(val))
				val = 0;
		}
		return val;
	}
	return Form.SetValue(this, arg[0]);
};

(function(old) {
  $.fn.attr = function() {
    if(arguments.length === 0) {
      if(this.length === 0) {
        return null;
      }

      var obj = {};
      $.each(this[0].attributes, function() {
        if(this.specified) {
          obj[this.name] = this.value;
        }
      });
      return obj;
    }

    return old.apply(this, arguments);
  };
})($.fn.attr);

/* İstenildiği gibi çalışmadığından şimdilik kapatıldı
document.onkeydown = function(e){
	window.event = e;
}
*/
