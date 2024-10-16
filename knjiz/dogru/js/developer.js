$(function()
{
	var div = $('<div id="Div_DevToolBar">')
		.appendTo('body')
		.addClass('unprintable');
	if (USE_BS_UI)
		div.addClass('panel');
	else
		div.addClass('ui-widget-header').addClass('ui-corner-all');

	var ldebug = window.location.host == 'localhost'
			|| window.location.host == '127.0.0.1'
			|| window.location.host == 'dev.kasatakip.com'
			|| window.location.host == 'dev.trtyonetim.com'
			|| window.location.host.substring(0,4) == 'dev.'
			|| window.location.host == 'dev.dgryazilim.com';
	if(ldebug)
	{
		var ldiv = $('<span>').appendTo(div);
		// Page
		AddActButton(ldiv, 'view', 'Page', OldUrl);
		// Db Model
		url = GetUrl('db_model');
		if (DbModelName)
			url += '&act2=tablo&no_cache=1&tb=' + DbModelName + '&close=0&mode=clear';
		AddActButton(ldiv, 'db_model', 'DB Model', url);
		if (ldiv.find('LABEL.active').length == 0)
			ldiv.find('LABEL:eq(0)').addClass('active').find('INPUT').attr('checked', 'checked');
		ldiv.find('LABEL').click(function(e){
			e.stopPropagation();
			var rd = $('#' + $(this).attr('for'));
			window.location.href = rd.attr('url');
		});
		if (USE_BS_UI)
			ldiv.attr('data-toggle', 'buttons').addClass('btn-group');
		else
			ldiv.buttonset();
		// App Page
		var span = $('<span>').appendTo(div);
		Jui.CreateButton({
			text: "App Page",
			icon: 'ui-icon-gear',
			cb: function(){
				Page.Open('developer.pagetree', {T: 1, W:1000, H:650, path:OldPath});
			}
		}).appendTo(span);
		if (typeof AllThemes == "undefined")
			AllThemes = '';
		if(AllThemes != "")
		{
			Jui.CreateButton({
				text: "Configs",
				icon: 'ui-icon-gear',
				cb: function(){
					Page.Open('developer.configs', {T: 1, W:1000, H:650});
				}
			}).prependTo(span);
			Jui.CreateButton({
				text: "Translates",
				icon: 'ui-icon-gear',
				cb: function(){
					Page.Open('developer.translate', {T: 1, W:1000, H:650});
				}
			}).prependTo(span);
			// Tema autocomplete
			$('<input type="text" placeholder="Tema adı..." id="DevToolBar_ThemeSearch">')
				.addClass(USE_BS_UI ? 'btn btn-default' : 'ui-button')
				.css({width: '100px', color : 'black'})
				.appendTo(span);
		}
		ThemeAutoComplete();
		Jui.InitButtons(span, 1);
	}
	DebugMenu(ldebug).appendTo(div);
	var w = div.width();
	div.css({ marginLeft: -(w/2) + 'px'});
	div.mouseenter(ShowDevTool);
	ShowDevTool();
});

function ThemeAutoComplete()
{
    $.widget( "custom.catcomplete", $.ui.autocomplete, {
      _create: function() {
        this._super();
        this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
		this.widget().addClass('theme-autocomplete-list');
      },
      _renderMenu: function( ul, items ) {
        var that = this,
          currentCategory = "";
        $.each( items, function( index, item ) {
          var li;
          if ( item.category != currentCategory ) {
            ul.append( "<li class='ui-autocomplete-category'><b>" + item.category + "</b></li>" );
            currentCategory = item.category;
          }
          li = that._renderItemData( ul, item );
		  if (item.label == CurrTheme)
			  li.addClass('ui-state-highlight');
          if ( item.category ) {
            li.attr( "aria-label", item.category + " : " + item.label );
          }
        });
      }
    });
	var ThemeListData = [];
	AllThemes = AllThemes.split(';');
	for(var i=0; i<AllThemes.length; i++)
	{
		var parts = AllThemes[i].split('=');
		if (parts.length == 1)
			ThemeListData.push({label: parts[0], category : 'Diğer'});
		else
		{
			var cat = parts[0];
			var temalar = parts[1].split(',');
			for(var k=0; k<temalar.length; k++)
				ThemeListData.push({label: temalar[k], category: cat});
		}
	}

    $( "#DevToolBar_ThemeSearch" ).catcomplete({
      delay: 0,
	  minLength : 0,
      source:  function(req, responseFn) {
			var re = $.ui.autocomplete.escapeRegex(req.term);
			var matcher = new RegExp( re, "i" );
			var a = $.grep( ThemeListData, function(item,index){
				return matcher.test(item.label) || matcher.test(item.category);
			});
			responseFn( a );
		},
	  select: function(evt, ui){
			return Page.Load({theme: ui.item.label});
	  }
    })
	.focus(function(){
		$(this).catcomplete("search", "");
	});
}

function AddActButton(div, act, text, url)
{
	var rd = $('<input type="radio"/>')
		.attr('id', act)
		.attr('name', 'developer-page-selection-radio')
		.attr('url', url)
		.val(act)
		.appendTo(div);
	var label = $('<label>').attr('for', act)
		.html(text);
	if (USE_BS_UI)
	{
		rd.appendTo(label);
		label.addClass('btn btn-info').appendTo(div);
	}
	else
		label.appendTo(div);
	if ((Page.GetParameter('act') == act) ||
		(Page.GetParameter('act') == 'developer' && Page.GetParameter('act2') == act))
	{
		rd.attr('checked', 'checked');
		label.addClass('active');
	}
	return rd;
}

function HideDevTool()
{
	var div = $('#Div_DevToolBar');
	if(div.is(":hover"))
		return;
	div.animate({top: -30}, 'slow').css('box-shadow', '');
}

function ShowDevTool()
{
	$('#Div_DevToolBar').css('box-shadow', '0px 3px 5px black')
		.animate({top: -4}, 'slow');
	$('body').bind('click', HideDevTool).click();
}

function DebugMenu(ldebug)
{
	var span = $('<span>');
	var buttons = [];
	buttons.push({text:'Dogru Menü', icon: 'ui-icon-gear', tool:true});
	buttons.push({
		text: 'Remove temporary files',
		icon: 'ui-icon-trash',
		cb: function(){
			Page.Ajax.Get('cisc').Send('TmpClear', null, Page.Ajax.REFRESH_NO_MSG);
		}
	});
	buttons.push({
		text: 'Open DB Debug window',
		icon: 'ui-icon-wrench',
		cb: function(){
			Page.OpenNewWindow(GetUrl('cisc','clear'), 'cisc_win', 'full', 'full');
		}
	});
	if(ldebug)
	{
		buttons.push({
			text: "App Page (All)",
			icon: 'ui-icon-document',
			cb: function(){
				Page.Load(GetUrl('developer.pagetree', 'clear'));
			}
		});
		buttons.push({
			text: "DB Model (All)",
			icon: 'ui-icon-document',
			cb: function(){
				Page.Load(GetUrl('db_model'));
			}
		});
		if (typeof CurrDbName != "undefined")
			buttons.push({
				text: '[ Active DB: <b>' + CurrDbName + '</b> ]',
				icon: 'ui-icon-info',
				enabled : false,
				separator: 'top'
			});
	}
	Jui.button(span, buttons, Jui.BUTTON_STYLE_DROPDOWN);
	return span;
}

function ChangeTheme(){
	var theme = $(this).find('span').html();
	Page.Load({theme: theme});
}
