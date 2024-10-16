function DbModelForm_Init()
{
	$('.data_form_item[addon]').each(function(){
		var inp = $(this);
		var div = $('<div class="input-group">');
		if (inp.parent().prop('tagName') == 'TD')
			div.addClass('input-group-xs');
		inp.after(div);
		inp.addClass('form-control normal-input').appendTo(div);

		var addon = JSON.parse(inp.attr('addon'));
		var span = $('<span>').addClass(addon.ClickFunc ? 'input-group-btn' : 'input-group-addon');
		if (addon.Location == '0')
			inp.after(span);
		else
			inp.before(span);

		if (addon.ClickFunc)
			span = $('<button class="btn btn-primary">').click(function(){
				window[addon.ClickFunc](inp, $(this));
			}).appendTo(span);
		if (addon.Text)
			span.html(' ' + addon.Text);
		if (addon.Icon)
			$('<i>').addClass(addon.Icon).prependTo(span);
	});

	$('.DbModelForm-Content').each(function(){
		var target = $($(this).attr('target'));
		var old = target.html();
		var html = $(this).html();
		html = html.replace(/\[\$content\]/ig, old);
		$(target).html(html);
		$(this).remove();
	});
	$('SELECT.data_form_item[multiple="1"]').chosen();
	if($.fn.Watermark)
		$('input[title],textarea[title]').each(function(){
			if ($(this).attr('type') != 'file')
				$(this).Watermark($(this).attr('title'));
		});
}

function DbModelForm_Save(saveFunc, returnValue)
{
		if (typeof returnValue == "undefined")
		returnValue = false;
	if (!returnValue && !Form.CheckRequest('.request_form_item'))
		return false;
	var inputs = $('.hasInputMask');
	for(var i=0; i<inputs.length; i++)
	{
		var inp = inputs.eq(i);
		var isComplete = inp.inputmask('isComplete');
		var unmaskedvalue = inp.inputmask('unmaskedvalue');
		if (! isComplete && unmaskedvalue)
		{
			var nm = Form.GetInputTitle(inp);
			return HighlightField(inp, null, nm + ' alanı için girilen değer geçersizdir');
		}
	}
	if($.Watermark)
		$.Watermark.HideAll();
	var params = Form.GetDataList('data_form_item');

	$.each(params,function(index,value){
		var parts = index.split('.');
		if(parts.length <= 1)
			return;
		var modelName = parts[0];
		if(typeof params[modelName] == 'undefined')
			params[modelName] = {CascadeChanged: true};
		params[modelName][parts[1]] = value;
	});

	if($.Watermark)
		$.Watermark.ShowAll();
	if (is_set(saveFunc) && saveFunc)
	{
		if (typeof window[saveFunc] != "function")
			return Page.ShowError(saveFunc + ' tanımlı değil...');
		var val = window[saveFunc](params, returnValue);
		if (!val)
			return val;
	}
	if (returnValue)
		return params;
	var openerCb = Page.GetParameter('OpenerCallback', '');
	var cb = function(resp){
		if (resp != '1')
			return Page.ShowError(resp);
		if (__unsavedChangesTracker)
			__unsavedChangesTracker.Stop = true;
		if (typeof opener[openerCb] == "function")
			opener[openerCb](resp, params);
		if (opener && Page.GetParameter("mode") == "clear")
			close();
	};
	if (!openerCb || !opener)
		cb = null;
	if(typeof DbModelForm_AfterSave == "function")
		cb = DbModelForm_AfterSave;
	var autoCorrect = true;
	if (typeof params.AutoCorrect != "undefined")
		autoCorrect = params.AutoCorrect;
	Page.Ajax.Send('DbModelForm_Save', params, cb, 'Kaydediliyor...', autoCorrect);
}

