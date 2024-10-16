/* global Page, SavedQueries, dtTableIds, Jui, exceleAktar, customSearchButtons, Tabs */
var queryOptions = {};
var customRenderParams = {};
function Ara(event, customParams, returnValues){
	var params = $.extend({}, Page.Params);
	var operators = {};
	var url = 'index.php?';
	var inputs = $('#advanced_query INPUT[name],#advanced_query SELECT[name],.arama INPUT[name],.arama SELECT[name]');
	var i;
	var excel = 0;
	for(i=0; i< inputs.length; i++)
	{
		var inp = $(inputs.get(i));
		var val = inp.val();
		if (typeof val != "object")
			val = $.trim(val);
		else if (val != null && val.length == 1)
			val = val[0];
		var def = inp.attr('default');
		var name = inp.attr('name');
		var type = inp.attr('var_type');
		var others = $('#advanced_query INPUT[name="' + name + '"]');
		var op = $(inp).closest('TR').find('SELECT[field_name]').val();
		if (name == 'Id')
			op = 'EQ';
		if (op)
			operators[name] = op;
		if (others.length > 1)
		{
			val = [];
			for(var k=0; k<others.length; k++)
			{
				var v = others.get(k).value;
				if (others.eq(k).attr('gorunur') == '0' || v == '')
					continue;
				if(type == 'float' || type == 'money' || type == 'int')
					v = Number.Parse(v);
				val.push(v);
			}
			val = val.join('|');
			if(val == '|')
				val = '';
		}
		if (val != null && val != '' && val != def)
			params[name] = val;
		else
			params[name] = '';
	}
	if (typeof customParams == "object")
		for(var key in customParams)
		{
			if (key == 'excel' && customParams[key] == '1')
				excel = 1;
			params[key] = customParams[key];
		}
	i = 0;
	for(var key in params)
	{
		var val = params[key];
		if (val != '')
			url += '&' + key + '=' + val;
		else
			delete params[key];
	}

	PinCriteria();

	if (returnValues == 1)
		return params;
	else if (returnValues == 2)
		return operators;
	else
	{
		if (typeof dtTableIds != "undefined" &&
			dtTableIds.length > 0 && excel == 0)
		{
			var tblId = dtTableIds[0];
			var dt = $('#' + tblId).dataTable();
			KriterlerGoster(params);
			$('[name="sorgu"]:first').focus();
			return dt.fnDraw();
		}
		var conditions = Ara(null, null, 2);
		Page.Load(url + '&sConditionOperators=' + JSON.stringify(conditions));
		return false;
	}
}

function AraCustom(customParams)
{
	customRenderParams = customParams;
	Ara();
}

function PinCriteria()
{
	var pinnedTds = $('#advanced_query TD.cr-pin.pinned');
	var tbl = $('TABLE.pinned');
	tbl.find('TR[pin]').remove();
	for(var i=0; i<pinnedTds.length; i++)
	{
		var pinned = pinnedTds.eq(i);
		var sourceTr = pinned.closest('TR');
		var input = sourceTr.find('INPUT[name],SELECT[name]');
		var name = input.attr('name');
		var tr = $('<TR pin="' + name + '"><td></td><td></td><td></td></tr>')
				.appendTo(tbl);
		var classes = ['cr-title', 'cr-val', 'cr-op'];
		for(var k=0; k<classes.length; k++)
		{
			var target = tr.find('TD').eq(k);
			var source = sourceTr.find('.' + classes[k]);
			target.html( source.html() );
			target.find('SELECT,INPUT').show().val( source.find('SELECT,INPUT').val() );
			target.find('.chosen-container').remove();
			target.find('SELECT').chosen();
		}
	}
	// İçeriği eşleştir
	tbl.find('SELECT[field_name],INPUT[name],SELECT[name]').change(function(){
		// operator changed
		var fName = $(this).attr('field_name');
		if (fName)
		{
			$('#advanced_query [field_name="' + fName + '"]')
				.val($(this).val())
				.change()
				.trigger("chosen:updated");
			$(this).closest('TR')
				.find('.second_input')
				.toggle($(this).val() == 'BETWEEN');
		}

		// param changed
		var name = $(this).attr('name');
		if (name)
			$('#advanced_query')
				.find('INPUT[name="' + name + '"],SELECT[name="' + name + '"]')
				.val($(this).val())
				.trigger("chosen:updated");
	});
	// Enter
	tbl.find('INPUT[name!="sorgu"]').keypress(function(evt){
		if (evt.keyCode == 13)
		{
			$(this).change(); // #advanced_query bölümünü günceller
			Ara();
		}
		evt.stopPropagation();
	});
	tbl.find('.hasDatepicker').removeClass('hasDatepicker');
	Jui.InitInputs(tbl);

	// Diğer formu kapat veya aç
	var cnt = tbl.find('TR[pin]').length > 0;
	var SearchTbl = $('TABLE.arama');
	var t1 = SearchTbl.is(':visible');
	var t2 = tbl.is(':visible');
	var parent = $('#search-input-div');
	if (parent.length > 0)
		SearchTbl.appendTo(parent);
	SearchTbl.toggle(! cnt);
	tbl.toggle(cnt);
	if ((t1 && cnt) || (t2 && !cnt))
	{
		if (cnt)
			tbl.find('[name=sorgu]').val(SearchTbl.find('[name=sorgu]').val());
		else
			SearchTbl.find('[name=sorgu]').val(tbl.find('[name=sorgu]').val());
	}
}

function GelismisAraGoster(hide){
	var criteron = $('#advanced kriter');
	var attributes= ['type', 'value', 'size', 'label', 'name', 'style', 'default'];
	var targetDiv= $('#advanced_query');

	// Kaç tab olduğu hesaplanıyor
	var tabs = [];
	for(var i=0; i<criteron.length; i++)
	{
		var c = $(criteron.get(i));
		var g = c.attr('group') ? c.attr('group') : 'Genel';
		c.attr('group', g);
		if ($.inArray(g, tabs) == -1)
			tabs.push(g);
	}
	if (tabs.length > 1 && ! $('#advanced_query').hasClass('ui-tabs'))
	{
		var tblHtml = targetDiv.html();
		targetDiv.html('');
		for(var i=0; i<tabs.length; i++)
		{
			var div = $('<div id="advanced_tab' + i + '" style="padding:5px 0px;"></div>').html(tblHtml).appendTo(targetDiv);
			div.attr('title', tabs[i]);
		}
		Jui.tabs('#advanced_query');
	}
	var tbl = $('.tbl-query').first();
	for(var i=0; i<criteron.length; i++)
	{
		var c = $(criteron.get(i));
		var name = c.attr('name');
		var group= c.attr('group');
		if (targetDiv.find('[name="' + name + '"]').length > 0)
			break;
		var tr = $('<tr class="kriter">\n\
						<td class="td_input_caption cr-pin"></td>\n\
						<td class="td_input_caption cr-title"></td>\n\
						<td class="td_input_data cr-val td_criteron"></td>\n\
						<td class="td_input_data cr-op"></td>\n\
					</tr>');
		if (tabs.length > 1)
			tbl = $('DIV[tab_title="' + group + '"]').find('TABLE').first();
		tr.appendTo(tbl);
		var inp, opr;
		var ctype = c.attr('type');
		switch (ctype){
			case 'select':
				opr = AddOperatorSelect(c);
				inp = AddCriteronSelect(c);
				break;
			case 'time':
				opr = AddOperatorTime(c);
				inp = AddCriteronTime();
				break;
			case 'date':
				opr = AddOperatorNumeric(c);
				inp = AddCriteronDate();
				break;
			case 'datetime':
				opr = AddOperatorNumeric(c);
				inp = AddCriteronDateTime();
				break;
			case 'int':
			case 'float':
			case 'money':
				opr = AddOperatorNumeric(c);
				inp = AddCriteronFloat(ctype);
				break;
			default:
				opr = AddOperatorString(c);
				inp = $('<input type="text"></input>');
		}
		var opVal = Page.Params['op_' + name];
		if (opVal)
			opr.val(opVal);
		var input = inp;
		for(var k=0; k<attributes.length; k++)
		{
			if (attributes[k].toLowerCase() == 'type')
				continue;
			if (inp.prop('tagName') == 'DIV')
				input = inp.find('INPUT,SELECT');
			if (attributes[k].toLowerCase() == 'value')
				input.val( c.attr( attributes[k] ) );
			else
				input.attr( attributes[k], c.attr( attributes[k] ) );
		}
		opr.appendTo( tr.find('TD.cr-op') );
		inp.appendTo( tr.find('TD.cr-val') );
		var pin = tr.find('TD.cr-pin').prepend( $('<i class="fa fa-thumb-tack">') );
		if (c.attr('pinned') == '1')
			pin.addClass('pinned');
		$(tr).find('TD.cr-title').html(input.attr('label'));
		$(tr).find('TD.cr-val SELECT').attr('multiple', 'multiple').val(null);
		$(tr).find('SELECT').change().chosen();
	}
	if ($('#advanced_query INPUT[name="Id"]').length == 0)
		$('<INPUT type="hidden" name="Id" default="-1" value="-1"></input>').appendTo(tbl);
	$('.tbl-query').find('TD.cr-pin:not([click_handled])')
			.attr('click_handled', '1')
			.click(function(){
		var obj = $(this);
		if (obj.hasClass('pinned'))
			obj.removeClass('pinned');
		else
			obj.addClass('pinned');
	});
	Jui.InitInputs();
	targetDiv.find('INPUT[name]:first').attr('autofocus', 'autofocus');
	if (is_set(hide))
		targetDiv.hide();
	else
	{
		targetDiv.find('.td_criteron INPUT[name], .td_criteron SELECT[name]').each(function(){
			if (this.value == '' ||
				$(this).val() == null ||
				$(this).attr('default') == this.value ||
				$(this).attr('name') == 'Id')
				return;
			$(this).parents('TR').first().find('TD').css('background', 'lightgreen');
		});
		var cb = Ara;
		if (typeof dtTableIds != "undefined" &&	dtTableIds.length > 0)
			cb = function(){
				if (! USE_BS_UI)
					targetDiv.dialog('close');
				Ara();
				return true;
			};
		if (USE_BS_UI)
			Page.ShowDialogBS(targetDiv.attr('id'), 750, 400, cb);
		else
			Page.ShowDialog(targetDiv.attr('id'), 750, 400, cb);
	}
}

function GetOperatorSelect(c, list)
{
	var sel = $('<select>')
		.attr('field_name', c.attr('name'));
	for (var i in list)
	{
		var text = $.trim(list[i].substr(2));
		var op = $.trim(list[i].substr(0, 2));
		if ($.inArray(i, ['LIKE', 'NOT_LIKE', 'START_WITH', 'END_WITH']) >= 0)
			op = text;
		$('<option>')
			.val(i).html(text)
			.attr('operator', op)
			.appendTo(sel);
	}
	return sel;
}

function AddOperatorString(c)
{
	var oprs = {
		LIKE: '* Contains',
		EQ: '= Equal',
		NOT_LIKE: '! Not Contains',
		START_WITH: '^ Starting with',
		END_WITH: '$ Ending with'
	};
	return GetOperatorSelect(c, oprs);
}

function AddOperatorNumeric(c)
{
	var oprs = {
		EQ: '= Equal',
		NEQ: "\u2260 Not Equal",
		GT_STR: '> Greater',
		GTE_STR:  "\u2265 Greater Equal",
		LT_STR: '< Small',
		LTE_STR: "\u2264 Small or Equal",
		BETWEEN: '<> Between'
	};
	var sel = GetOperatorSelect(c, oprs);
	sel.val('BETWEEN').change(function(){
		var v = $(this).val();
		$(this).closest('TR')
			.find('.td_criteron .second_input')
			.toggle(v == 'BETWEEN')
			.find('INPUT,SELECT')
			.attr('gorunur', v == 'BETWEEN' ? 1 : 0);
	});
	return sel;
}

function AddOperatorSelect(c)
{
	var oprs = {
		IN: '= Equal',
		NOT_IN: "\u2260 Not Equal"
	};
	return GetOperatorSelect(c, oprs);
}

function AddOperatorTime(c)
{
	var oprs = {
		BETWEEN: '<> Between'
	};
	var sel = GetOperatorSelect(c, oprs);
	sel.val('BETWEEN').change(function(){
		var v = $(this).val();
		$(this).closest('TR')
			.find('.td_criteron .second_input')
			.toggle(v == 'BETWEEN')
			.find('INPUT,SELECT')
			.attr('gorunur', v == 'BETWEEN' ? 1 : 0);
	});
	return sel;
}

function AddCriteronSelect(c)
{
	var inp = $('<select></select>');
	if (c.find('OPTION').length > 0)
		inp.html( c.html() );
	else if (c.attr('options'))
	{
		var options = c.attr('options').split(';');
		var sel = inp.get(0);
		for(var j=0; j<options.length; j++)
		{
			var parts = options[j].split('=');
			sel.options[sel.options.length] =
					new Option(parts[0],
						parts.length > 1 ? parts[1]: parts[0]);
		}
	}
	return inp;
}

function GetNumericCriteron()
{
	return $('<div><input type="text"/> \n\
		<span style="display: inline;" class="second_input">to\n\
		<input type="text"/></span> </div>');
}

function AddCriteronDate()
{
	var inp = GetNumericCriteron();
	inp.find('INPUT,SELECT').attr('date_selector', '1');
	return inp;
}

function AddCriteronTime()
{
	var inp = GetNumericCriteron();
	inp.find('INPUT,SELECT').attr('var_type', 'time');
	return inp;
}

function AddCriteronDateTime()
{
	var inp = GetNumericCriteron();
	inp.find('INPUT,SELECT').attr('var_type', 'datetime');
	return inp;
}

function AddCriteronFloat(ctype)
{
	var inp = GetNumericCriteron();
	inp.find('INPUT,SELECT').attr('var_type', ctype).css('width', '8em');
	return inp;
}

function KriterlerGoster(params)
{
	var kriterler = new Object();
	for(var name in params)
	{
		var kriter = $('#advanced_query [name="' + name + '"]');
		if (kriter.length == 0)
			continue;
		if ($('TABLE.pinned TR[pin="' + name + '"]').length > 0)
			continue;
		var val = unescape($.trim(params[name]));
		var op = $(kriter).closest('TR')
				.find('SELECT[field_name] OPTION:selected')
				.attr('operator');
		if (typeof params[name] != "object")
		{
			kriter.attr('value', val);
			kriter.trigger('chosen:updated');
		}

		var def = kriter.attr('default');
		if (val == null || val == '' || def == val)
			continue;
		if (kriter.length > 1)
		{
			var others = kriter.parent().find("[name='" + name + "']");
			var parts = val.split('|');
			for(var k=0; k<others.length; k++)
			{
				if (typeof parts[k] == "undefined")
					continue;
				var oth = others.eq(k);
				var ty = oth.attr('var_type');
				if (parts[k] !== '' && (ty == 'int' || ty == 'float' || ty == 'money'))
					oth.val(Number.Format(parts[k]));
				else
					oth.val(parts[k]);
			}
		}
		if (kriter.length > 0 && kriter.get(0).tagName == 'SELECT')
			val = kriter.find('option:selected').text();
		kriterler[name] = {
			value : val,
			operator : op,
			label : name == 'Id' ? 'Id' : kriter.attr('label')
		};
	}

	if(! $.isEmptyObject(kriterler))
	{
		var div = $('#kriterler').html('');
		$('#kriterler_dialog').show();
		var sablon = $('#kriter_sablon').html();
		for(var name in kriterler)
		{
			var kriter = kriterler[name];
			var span = $(sablon).appendTo(div);
			var operator = '=';
			if (kriter.operator)
				operator = kriter.operator;
			span.attr('name', name);
			span.find('.label').html(kriter.label);
			span.find('.operator_l').html(operator);
			span.find('.operator_r').html('(' + operator + ')');
			span.find('.operator_l').toggle(operator.match(/[a-z]/i) ? false: true);
			span.find('.operator_r').toggle(operator.match(/[a-z]/i) ? true : false);
			span.find('.value').html(kriter.value);
		}

		$('#kriterler .kriter').click(function(){
			var name = $(this).attr('name');
			if (name == 'Id')
				return;
			GelismisAraGoster();
			var div = $('#advanced_query');
			var input= div.find('[name="' + name + '"]').eq(0);
			if (div.hasClass('ui-tabs'))
			{
				var index = input.parents('DIV.ui-tabs-panel').first().index();
				div.tabs({active: index - 1});
			}
			HighlightField(input.closest('TR'), input.parents('DIV.ui-tabs-panel').first());
		});

		$('#kriterler .kriter BUTTON').click(function(event){
			var kriter = $(this).parents('.kriter').first();
			var name = kriter.attr('name');
			var inp = $('#advanced_query [name="' + name + '"]');
			inp.val(inp.attr('default') ? inp.attr('default') : '');
			var kriterler = $('#kriterler');
			if (kriterler.find('SPAN.kriter').length == 1)
			{
				kriter.remove();
				kriterler.dialog('close');
			}
			else
				kriter.hide('scale', 500);
			Ara();
			event.stopPropagation();
		});

	}
	else
		$('#kriterler_dialog').hide();
	return kriterler;
}

$(function(){
	var buttons = [
		{
			text: 'Search',
			cb: Ara,
			icon: 'ui-icon-search'
		},
		{
			text: 'Advanced Search',
			cb: function(){ GelismisAraGoster(); },
			icon: 'ui-icon-gear'
		}
	];
	if (typeof exceleAktar == 'object')
		buttons.push(exceleAktar);
	if (typeof customSearchButtons == 'object')
		for(var i=0; i<customSearchButtons.length; i++)
			buttons.push(customSearchButtons[i]);
	var bs = Jui.BUTTON_STYLE_DROPDOWN_SPLIT;
	if (buttons.length == 2)
	{
		bs = Jui.BUTTON_STYLE_BUTTONSET;
		buttons[1].tool = true;
	}

	var params = Page.Params;
	GelismisAraGoster(1);
	KriterlerGoster(params);

	if ($('#advanced_query TR.kriter').length == 0)
		buttons.splice(1, 1);
	Jui.button('TABLE.arama TD.buttons', buttons, bs);
	Jui.button('TABLE.pinned TD.buttons', buttons, bs);

	$('#kriterler').dialog({
		appendTo : '#kriterler_dialog',
		width: '100%',
		draggable : false,
		resizable : false,
		closeText : 'Tüm kayıtları göster',
		create	  : function(){
			if (Page.GetParameter('customTab') !== '')
				return;
			var saveBtn = $("<button title='Bu arama kriterlerini kaydet'>")
					.html("<i class='fa fa-check'></i> Kaydet")
					.addClass('save_button btn btn-primary btn-xs')
					.css({float: 'right', marginRight : '20px'})
					.click(YeniTabForm);
			$(this).prev('.ui-dialog-titlebar').find('.ui-dialog-title').after(saveBtn);
		},
		beforeClose : function(){
			$('.arama INPUT').val('');
			var kriterler = $('#kriterler .kriter');
			for(var i=0; i<kriterler.length; i++)
			{
				var name = $(kriterler.get(i)).attr('name');
				var inp = $('#advanced_query [name="' + name + '"]');
				inp.val(inp.attr('default') ? inp.attr('default') : '');
			}
			$('#kriterler_dialog').toggle('puff', { percent: 10}, 500 );
			Ara();
			return false;
		}
	}).css('min-height', '')
	.parent().css({
		position: 'relative',
		zIndex: 5,
		top: 0,
		left: 0,
		height: 'auto'
	});
	setTimeout("$('[name=sorgu]:visible').focus();", 500);

	setTimeout('EklenmisTablariGoster();', 100);

	var sorgu = $('.arama [name="sorgu"]');
	sorgu.val(unescape(Page.GetParameter('sorgu')));
	var sorgu2 = $('.pinned [name="sorgu"]');
	sorgu2.val(unescape(Page.GetParameter('sorgu')));
});

function YeniTabForm()
{
	var cb = function(){
		var ad = $.trim($('#kriter_kaydet .TabName').val());
		if (!ad)
			return Page.ShowError('Lütfen yeni sayfa adını doldurunuz...');
		var cb = function(resp){
			if (resp == '1')
				return Page.ShowSuccess("Yeni sayfa kaydedildi. " +
					"Sayfa yenilendikten sonra oluşan yeni sekmeye tıklayarak sayfanıza erişebilirsiniz",
					Page.Reload);
			Page.ShowError("Sayfa kaydedilemedi.<br><br>Alınan hata: " + resp);
		};
		var params = Ara(null, null, 1);
		var operators = Ara(null, null, 2);
		var ignored = ['p', 'tab', 'tab2', 'tab3', 'act', 'act2', 'act3'];
		for(var name in params)
			if ($.inArray(name, ignored) >= 0)
				delete(params[name]);
		for(var name in operators)
			if (typeof params[name] == "undefined")
				delete(operators[name]);
		var data = {
			Ad : ad,
			Url: Page.GetCurrentUrl('', false),
			Params: {Params: params, Operators: operators}
		};

		Page.Ajax.Send('SaveQueryAs', data, cb);
	};
	Page.ShowDialog('#kriter_kaydet', 500, 200, cb);
}

function EklenmisTablariGoster()
{
	if (typeof SavedQueries == "undefined" ||
		!$.isArray(SavedQueries) ||
		SavedQueries.length == 0)
		return false;
	if (typeof SavedQueriesCompleted != "undefined")
		return false;
	SavedQueriesCompleted = true;
	var existing = $('TABLE.arama').parents('DIV.grid-tab').first();
	if (existing.length == 0)
		existing = GenerateNewTabPane();
	var tabParam = Page.GetParameter(existing.prop('TabParam'));
	var selectedTab = null;
	for(var i=0; i<SavedQueries.length; i++)
	{
		var query = SavedQueries[i];
		var savedParams = query.Params;
		var savedOperators = {};
		if (typeof savedParams.Params == "object")
		{
			savedOperators = savedParams.Operators;
			savedParams = savedParams.Params;

		}
		var params = [];
		for(var name in savedParams)
		{
			params.push(name + "=" + savedParams[name]);
			if (typeof savedOperators[name] != "undefined")
				params.push('op_' + name + '=' + savedOperators[name]);
		}
		params.push('customTab=' + i );
		var html = query.Ad + '<i class="delete-link fa fa-close" customTab="' + i + '"></i>';
		var li = $('<li data=\'' + JSON.stringify(query) + '\'><a href="#customPage_' + i + '">' +
					html +'</a>' +
				'</li>')
				.appendTo(existing.find('ul').first());
		$( "<div id='customPage_" + i + "' url='" + params.join('&') + "'><p></p></div>" )
				.appendTo( existing.tabs() );
		if (li.index() == tabParam)
			selectedTab = li;
		li.find('.delete-link')
			.click(function(e){
				e.preventDefault();
				e.stopPropagation();
				var data = JSON.parse($(this).closest('LI').attr('data'));
				var a = this;
				Page.ShowConfirm(
						"<b>" + data.Ad + "</b> adlı sorgulama sayfasını silmek istediğinize emin misiniz?",
						function(){
							var cb = function(resp){
								if (resp != '1')
									return Page.ShowError(resp);
								if (Page.GetParameter('customTab') == $(a).attr('customTab'))
									Page.Load(Page.GetCurrentUrl());
								else
									Page.Reload();
							};
							Page.Ajax.Send('DeleteSavedQuery', data.Key, cb);
						}
				);
			});
	}
	existing.tabs('refresh');
	if (selectedTab !== null)
	{
		Tabs.EnableBeforeActivate = false;
		existing.tabs({active : selectedTab.index()});
		var src = existing.find('li:first a').attr('href');
		var matches = src.match(/(#.*)/);
		if (matches)
			$(matches[1]).show();
		Tabs.EnableBeforeActivate = true;
	}
}

function GenerateNewTabPane()
{
	var tbl = $('TABLE.arama');
	var kriter = $('DIV.kriter_templates');
	var grid= $('.dataTables_wrapper').css('margin-top', '10px');
	var tabPage = $('<div>').addClass('grid-tab');
	var newTab = $('<div id="customTabFirstPage">')
			.attr('url', 'change=1')
			.attr('title', 'Tümü').appendTo(tabPage);
	tbl.before(tabPage);
	tbl.appendTo(newTab);
	kriter.appendTo(newTab);
	grid.appendTo(newTab);
	Jui.tabs(tabPage);
	return tabPage;
}
