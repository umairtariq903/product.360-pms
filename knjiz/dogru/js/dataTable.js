/* global Page, Ara, DetailPage, Jui, USE_BS_UI, pdfMake, Thread, KNJIZ_URL, customRenderParams */

var queryOptions = {};
var dts = {};
var dtTableIds = [];
var DataTableAttr = new Object();
var DataTableTdAttr = new Object();
var DataTableSummary = new Object();
var DataTableRowButtons = new Object();
var lastDataTable = null;
var lastDataTableObj = null;
var refreshDataTable = false;
var SaveState = true;
var StateParams = '';
var PagingFnCallbackDraw = null;

var dtTextTR = {
	// "sProcessing": "Yükleniyor...",
	// "sLengthMenu": "Her sayfada _MENU_ kayıt",
	// "sZeroRecords": "Kayıt bulunamadı.",
	// "sInfo": "Toplam _TOTAL_ kayıttan _START_ - _END_ arası kayıtlar",
	// "sInfoEmpty": "Toplam 0 kayıttan 0 - 0 arası kayıtlar",
	// "sInfoFiltered": "(_MAX_ kayıttan filtrelenen)",
	// "sSearch": "Arama",
	// "oPaginate": {
	// 	"sFirst":    "İlk",
	// 	"sPrevious": "Önceki",
	// 	"sNext":     "Sonraki",
	// 	"sLast":     "Son"
	// }
};

function JqDataTable(tableId, jSon)
{
	dtTableIds.push(tableId);
	var dt = dts[tableId] = JSON.parse(jSon);
	DataTableAttr[tableId] = dt.DataTable.RowAttributes;
	DataTableTdAttr[tableId] = dt.DataTable.TdsAttributes;
	DataTableSummary[tableId] = dt.DataTable.Summary;
	DataTableRowButtons[tableId] = [];
	var tbl = $('#' + tableId)
		.css({ width: '100%', tableLayout: 'fixed', borderCollapse: 'collapse'});
	tbl.find('TBODY TD').css({border: '1px solid'});
	if(! dt.RowClickFunc && typeof DetailPage == 'string')
		dt.RowClickFunc = 'ShowDetailPage';
	if(dt.RowClickFunc)
		tbl.attr('onrowclick', dt.RowClickFunc);

	var cols = [];
	var colDefs = [];
	var v = 0;
	var k = 0;

	if (USE_BS_UI)
	{
		let minWidth = 0;
		for(var i in dt.DataTable.Columns)
		{
			var c = dt.DataTable.Columns[i];
			if (c.Visible)
			{
				if (typeof c.Width == "number")
					minWidth += c.Width;
				else if (typeof c.Width == "string" && c.Width.indexOf("px") !== -1)
					minWidth += parseFloat(c.Width);
				else
					minWidth += 100;
			}
		}
		// let columnCount = Object.keys(dt.DataTable.Columns).length;
		tbl.css({"min-width": minWidth+ "px"})
	}
	for(var i in dt.DataTable.Columns)
	{
		var c = dt.DataTable.Columns[i];
		if(c.GroupName == '')
			c.GroupName = 'Genel';
		var visible = c.Visible == 1 && (v++) < 30;
		var width = 'auto';
		if(c.Width)
			width = c.Width;
		cols.push({
			sTitle: c.DisplayName,
			bSortable : c.Sortable == true,
			bSearchable: c.Searchable,
			sClass : 'DataCell_' + c.Align,
			bVisible : visible,
			GroupName: c.GroupName,
			ColIndex : k,
			sWidth : width,
			sName: i,
			buttons: c.Buttons
		});
		colDefs.push({
			"sName": i,
			"aTargets": [ k++ ]
		});
	}

	var href = Page.GetWindowHref();
	href = Page.UrlChangeParam('grid', 1, href);
	href = Page.UrlChangeParam('table_id', tableId, href);

	var ajaxOptions = {
		"bProcessing": true,
		"sAjaxSource":  href,
		"sServerMethod": "POST",
		"bServerSide": true,
		"bPaginate": true,
		"fnServerParams": function ( aoData ) {
			var queryOptions = {};
			var customParams = null;
			if( typeof GetCustomParams == "function")
				customParams = GetCustomParams();
			if (typeof Ara == "function")
				queryOptions = Ara(null, customParams, 1);
			for(var i in queryOptions)
				aoData.push( { name: i, value: queryOptions[i] } );
			aoData.push( {name: 'table_id', value: $(this).attr('id') });
		}
	};
	var sDom = 'rt';
	var s = (dt.ShowNewButton ? '<"div_new_btn">' : 'l');
	s = (dt.ShowSearch ? 'f' : s);
	var hOpt = 'R<"H"<"toolbar"' + s + '>' + '>';
	var fOpt = '<"F"' + (dt.ShowPaging ? 'ip' : '') + '>';
	if (dt.ShowHeader)
		sDom = hOpt + sDom;
	if (dt.ShowFooter)
		sDom += fOpt;
	var options = {
		"sDom": sDom,
		"bPaginate": false,
		"sPaginationType": "dogru_paging",
		"iDisplayLength": parseInt(dt.PageSize),
		"bJQueryUI": true,
		"bScrollCollapse": true,
		"bScrollAutoCss": false,
		"bSort": dt.Sortable == 1,
		//"aaSorting": dt.Sorting,
		"aaData": dt.DataTable.Data,
		"aoColumns" : cols,
		"aoColumnDefs": colDefs,
		"bAutoWidth": false,
		"bStateSave": true,
		"oLanguage": dtTextTR,
		"fnRowCallback": function(nRow, aData, iDisplayIndex){
			var cells = $(nRow).children('TD');
			var cols = $(this).dataTable().fnSettings().aoColumns;
			var rowButtons = null;
			if (DataTableRowButtons[tableId].length > 0)
				rowButtons = DataTableRowButtons[tableId][iDisplayIndex];
			var names = [];
			for(var i=0; i<cols.length; i++)
				if (cols[i].bVisible)
					names.push(cols[i]);
			// Row attributes
			if (DataTableAttr[tableId])
			{
				var data = DataTableAttr[tableId];
				var rowAttr = data[iDisplayIndex];
				for(var name in rowAttr)
					if (name.toLowerCase() == 'class')
						$(nRow).addClass(rowAttr[name]);
					else
						$(nRow).attr(name, rowAttr[name]);
			}
			// Buttons
			for(var i=0; i<cells.length; i++)
			{
				var col = names[i];
				var cell = cells.eq(i);
				cell.attr('field_name', col.sName);
				if (col.buttons)
				{
					var btnFunc = dts[tableId].GridIslemButton;
					var buttons = [];
					for(var j=0; j<col.buttons.length; j++)
					{
						var b = $.extend({}, col.buttons[j]);
						if (rowButtons != null)
							$.extend(b, rowButtons[j]);
						if (b.deleted == 1)
							continue;
						buttons.push(b);
						if (btnFunc)
							window[btnFunc](b, $(nRow));
						if(! b.CallBackFunc || ! b.enabled )
							continue;
						b.attr = {func: b.CallBackFunc};
						b.cb = function(e){
							var r = $(nRow);
							var func = $(this).attr('func');
							window[func](r.attr('row_id'), r, this);
							Jui.CloseCurrentMenu();
							e.stopPropagation();
						};
					}
					var bs = Jui.BUTTON_STYLE_NORMAL;
					if (! buttons[0].cb && buttons.length == 2) // Dropdown'a gerek yok
						buttons.splice(0, 1);
					if (buttons.length > 1)
						bs = Jui.BUTTON_STYLE_DROPDOWN;
					Jui.button(cell, buttons, bs);
					if (cell.get(0).ul)
						cell.get(0).ul.css({fontSize: '12px', minWidth:'100px'});
				}
			}

		},
		"fnDrawCallback": function (oSettings) {
			var ajax = dt.ShowPaging && !dt.DataTable.StaticGrid;
			if (typeof AttributesLoaded != "undefined" && !ajax)
				return;
			AttributesLoaded = 1;
			Page.CloseProcessingMessage();
			var tblId = $(this).attr('id');
			if (USE_BS_UI)
				$(this).find('>THEAD TH')
					.removeClass('ui-state-default')
					.addClass('info');
			// Td Attribute eklenmesi
			if (DataTableTdAttr[tblId])
			{
				var rows = $(this).find('TBODY TR');
				var data = DataTableTdAttr[tblId];
				for(var i=0; i<rows.length && i<data.length; i++)
				{
					var tds = data[i];
					for(var fname in tds)
					{
						var attrs = tds[fname];
						var td = $(rows).eq(i).find('td[field_name="' + fname + '"]');
						for(var name in attrs)
							if (name.toLowerCase() == 'class')
								td.addClass(attrs[name]);
							else
								td.attr(name, attrs[name]);
					}
				}
			}
			// Row click
			var cb = $(this).attr('onrowclick');
			var trs = $(this).find('> TBODY > TR');
			if(trs.length > 0 && trs.first().attr('row_id'))
			{
				if(cb)
					trs.css('cursor', 'pointer');
				trs.click(function(){
					if(! dt.DataTable.Columns.Sec)
						$(this).parent().children().removeClass('ui-state-highlight');
					$(this).addClass('ui-state-highlight');
					if (cb)
						window[cb]($(this).attr('row_id'), this);
				});
			}

			// Checkbox'lar
			$(this).find('TBODY INPUT[type="checkbox"]').click(function(e){
				$(this).change();
				e.stopPropagation();
			}).change(function(){
				var row = $(this).parents('TR').first();
				var activeClass = USE_BS_UI ? 'success' : 'ui-state-highlight';
				if (this.checked)
					row.addClass(activeClass);
				else
					row.removeClass(activeClass);
			}).closest('td').click(function(e){
				$(this).find('input').click();
				e.stopPropagation();
			});
			Jui.InitCheckboxes($(this).find('TBODY'));
			$(this).find('TBODY td[field_name="islem"]').click(function(e){
				$(this).find('button').first().click();
				e.stopPropagation();
			});

			LoadSummaryInfo(tblId);

			var glf = dts[tableId].GridLoadFunc;
			if(glf  && typeof window[glf] == "function")
				window[glf]();
			// Dosya linkleri
			$(this).find('[upload_type] A').click(function(e){
				e.stopPropagation();
			});
			$(this).find('IMG.grid-image').click(function(e){
				var w = this.naturalWidth;
				var h = this.naturalHeight;
				Page.Download(this.src, w + 10, h + 10);
				e.stopPropagation();
			});
			// Harici handler çağrılıyor (varsa çalışacak)
			$(this).gridLoad();
			var triCheckBox = $('INPUT.tristate[type="checkbox"]');
			if (triCheckBox.length > 0)
				Jui.InitCheckboxes(triCheckBox.parent());
		},
		"fnStateSave": function (oSettings, oData) {
			if(!SaveState || dt.DataTable.StaticGrid)
				return ;
			var obj = {
				url: Page.GetCurrentUrl('', false, StateParams),
				columns: [],
				sort: []
			};
			for(var i=0; i<oSettings.aoColumns.length; i++)
			{
				var col = oSettings.aoColumns[i];
				obj.columns.push({
					sName: col.sName,
					sWidth: col.sWidth,
					bVisible: col.bVisible
				});
			}
			for(var i=0; i<oSettings.aaSorting.length; i++)
			{
				var sort = oSettings.aaSorting[i];
				obj.sort.push([obj.columns[sort[0]].sName, sort[1]]);
			}
			var pinned = $('#advanced_query TD.cr-pin.pinned');
			obj.pinnedCols = [];
			for(var i=0; i<pinned.length; i++)
			{
				var name = pinned.eq(i).closest('TR')
						.find('INPUT[name],SELECT[name]')
						.attr('name');
				obj.pinnedCols.push(name);
			}
			Page.Ajax.Send('TableState_Save', obj, function(){}, '');
		},
		"fnServerData": function ( sSource, aoData, fnCallback ) {
			var dtw = $(this).parents('.dataTables_wrapper:first');
			Page.Loading();
			dtw.find('.dataTables_processing').css('opacity', '0');
			var data = [];
			for(var i=0; i<aoData.length; i++)
				if (! aoData[i].name.match(/(mDataProp|bRegex|bSearchable|bSortable|sSearch)_[0-9]+/i))
					data.push(aoData[i]);
			data.push({name: 'sConditionOperators', value: Ara(null, null, 2)});
			var settings = $(this).dataTable().fnSettings();
			var cols = settings.aoColumns;
			var tableId = settings.sTableId;
			var vColumns = [];
			var customRender = dts[tableId].CustomRenderFunc;
			for(var i=0; i<cols.length; i++)
				if (cols[i].bVisible || typeof customRender == "function")
					vColumns.push(cols[i].sName);
			data.push({name: 'selected_fields', value: vColumns.join(',')});

			// customRenderParams varsa, data içine ekliyoruz
			if (customRenderParams != null)
			{
				// Sıralama sütunu belirlenmişse
				if (dts[tableId].CustomRenderFunc)
				{
					var cols = dts[tableId].DataTable.Columns;
					var i = 0;
					for(var name in cols)
					{
						if (cols[name].OrderBy)
						{
							var p = Array.FindByKey(data, 'name', 'iSortCol_0');
							p.value = i;
							p = Array.FindByKey(data, 'name', 'sSortDir_0');
							p.value = cols[name].OrderBy;
							break;
						}
						i++;
					}
				}
				// Diğer parametreleri işle
				for(var name in customRenderParams){
					var prop = name;
					var val = customRenderParams[name];
					if (name == 'Sort')
					{
						prop = 'iSortCol_0';
						val = $.inArray(val, vColumns);
					}
					else if (name == 'SortDir')
						prop = 'sSortDir_0';
					else if (name == 'sorgu' && val == '')
						continue;
					var obj = Array.FindByKey(data, 'name', prop);
					if (obj)
						obj.value = val;
					else
						data.push({name: name, value: val});
				}
			}
            request = $.ajax({
                "dataType" : "json",
                "type" : "POST",
                "url" : sSource,
                "data" : [{name: 'data', value: JSON.stringify(data)}],
				"error": function ( xhr, textStatus, error ) {
					Page.CloseProcessingMessage();
					if ( textStatus === 'timeout' )
						Page.ShowError('Sunucu çok meşgul. Lütfen daha sonra tekrar deneyiniz');
					else
						Page.ShowError(xhr.responseText);
				},
                "success" : function(e, x, a){
					DataTableAttr[e.tableId] = e.rowAttributes;
					DataTableTdAttr[tableId] = e.tdsAttributes;
					DataTableSummary[e.tableId] = e.summary;
					DataTableRowButtons[e.tableId] = e.rowButtons;
					var customRender = dts[e.tableId].CustomRenderFunc;
					var customPaging = dts[e.tableId].CustomPagingFunc;
					if (customRender && typeof window[customRender] == 'function')
					{
						$('#' + e.tableId).parents('.dataTables_wrapper').hide();
						var rows = [];
						for(var i=0; i<e.aaData.length; i++)
						{
							var obj = {};
							var k = 0;
							for(var name in dts[e.tableId].DataTable.Columns)
								obj[name] = e.aaData[i][k++];
							rows.push(obj);
						}
						window[customRender](rows);
						LoadSummaryInfo(e.tableId);

						if (customPaging && typeof window[customPaging] == "function")
						{
							var settings = $('#' + e.tableId).dataTable().fnSettings();
							var pagingData = GetCustomPagingData(settings, PagingFnCallbackDraw);
							window[customPaging](pagingData);
						}

						return Page.CloseProcessingMessage();
					}

					fnCallback(e, x, a);
					// Satırlarla veriyi ilişkilendiriyoruz
					var tableId = e.tableId;
					var dataList = e.aaData;
					var cols = dts[tableId].DataTable.Columns;
					var rows = $('#' + tableId).find('TBODY TR');
					for(var i=0; i<rows.length; i++)
					{
						var row = rows.get(i);
						var rawData = dataList[i];
						var obj = {Id: row.getAttribute('row_id')};
						if (rawData)
						{
							var k = 0;
							for(var name in cols)
								obj[name] = rawData[k++];
						}
						row.Data = obj;
					}
				}
            });
        }
	};
	if (dt.Sorting && typeof dt.Sorting[0] != "undefined")
		options.aaSorting = dt.Sorting;
	if (dt.ShowPaging && ! dt.DataTable.StaticGrid)
		for(var i in ajaxOptions)
			options[i] = ajaxOptions[i];
	var oTable = lastDataTable = $('#' + tableId).dataTable( options );
	if (dt.CustomRenderFunc && dt.DataTable.StaticGrid != 1)
		$('#' + tableId).parents('.dataTables_wrapper').hide();
	lastDataTableObj = dt;
	if(dt.ShowPaging)
		$("div.dataTables_filter input").unbind('keyup').keyup( function (e) {
			if (e.keyCode == 13) {
				oTable.fnFilter( this.value );
			}
			e.stopPropagation();
		} );
	//if (dt.Sorting && typeof dt.Sorting[0] != "undefined")
	//	lastDataTable.fnSort([ dt.Sorting[0] ]);
	var toolBar = $('#' + tableId).parents('.dataTables_wrapper').find('.toolbar');
	var buttons = [
		{text: 'Özelleştirme Temizle', cb: 'TableStateClear', ftype: '', icon: 'ui-icon-refresh', toolbar: 1},
		{text: translateLib("alanlar"), cb: 'AlanSeciciDialog', ftype: '', icon: 'ui-icon-gear'}
	];
	if(dt.FullScreen)
		buttons.push({text: 'Tam Ekran', cb: 'ShowFullScreen', ftype: '', icon: 'ui-icon-arrowthick-2-se-nw'});
	if(dt.OutputExcel)
		buttons.push({text: 'XLS', cb: 'Converter', ftype: 'xls', icon: 'ui-icon-calculator' });
	if(dt.OutputWord)
		buttons.push({text: 'WORD', cb: 'Converter', ftype: 'doc', icon: 'ui-icon-document'});
	if(dt.OutputPdf)
		buttons.push({text: 'PDF', cb: 'Converter', ftype: 'pdf', icon: 'ui-icon-image'});

	if(dt.ShowToolBar)
		for(var i=0; i<buttons.length; i++)
		{
			var b = buttons[i];
			var btn = $('<button class="jui-button"></button>');
			btn.html('<span class="hidden-xs">' + b.text + '</span>');
			btn.attr('title', b.text);
			btn.attr('ftype', b.ftype);
			btn.attr('cb', b.cb);
			btn.attr('table_id', tableId);
			btn.attr('icon', b.icon);
			btn.attr('toolbar', b.toolbar);
			btn.appendTo(toolBar);
			btn.click(ToolbarItemClicked);
		}
	Jui.InitButtons(toolBar, buttonset=1);
	var newClick = dt.NewButtonClickFunc;
	if ( newClick == '')
		newClick = dt.RowClickFunc;
	var divNewBtn = $('#' + tableId).parents('.dataTables_wrapper').find('.div_new_btn').css('float', 'right');
	Jui.button(divNewBtn, {text: dt.NewButtonText, cb: window[newClick], ftype: '', icon: 'ui-icon-plusthick'});
	if (USE_BS_UI)
		Bootstrapize(tableId);
	else
		$('#' + tableId).parents('.dataTables_wrapper').addClass('jui-table-wrapper');
}

function LoadSummaryInfo(tblId)
{
	var div = $('.summary[for=' + tblId + ']');
	if(div.length > 0)
	{
		var sum = DataTableSummary[tblId];
		for(var name in sum)
		{
			var item;
			try {
				item = div.find('.' + name);
			} catch(e) {
				continue;
			}
			if (item.length == 0)
				item = div.find('[field_name="' + name + '"]');
			if (item.length > 0 )
			{
				var d = 2;
				if(item.attr('digit'))
					d = item.attr('digit');
				var tur = ' TL';
				if(item.attr('unit') != null)
					tur = item.attr('unit');
				if(item.attr('auto_format') == '0')
					item.html(sum[name]);
				else if (item.attr('custom_format'))
				{
					var format = item.attr('custom_format');
					var val = format + '("' + sum[name] + '");';
					item.html(eval(val));
				}
				else
					item.html(Number.Format(sum[name], d) + tur);
			}
		}
	}
}

function GetCustomPagingData(settings, fnCallbackDraw)
{
	var pagingData = {};
	var tableId = settings.sTableId;
	settings._iRecordsDisplay = parseInt(DataTableSummary[tableId][0]);

	pagingData.PageSize = settings._iDisplayLength;
	pagingData.RecordCount = settings._iRecordsDisplay;
	pagingData.CurrPage =  Math.floor(settings._iDisplayStart / settings._iDisplayLength) + 1;
	pagingData.PageCount = Math.ceil(settings._iRecordsDisplay / settings._iDisplayLength);

	pagingData.Next = function(){
		settings._iDisplayStart += pagingData.PageSize;
		$(settings.oInstance).trigger('page', settings);
		fnCallbackDraw( settings );
	};
	pagingData.Last = function(){
		settings._iDisplayStart = (pagingData.PageCount - 1) * pagingData.PageSize;
		$(settings.oInstance).trigger('page', settings);
		fnCallbackDraw( settings );
	};
	pagingData.Previous = function(){
		if (settings._iDisplayStart >= pagingData.PageSize)
		{
			settings._iDisplayStart -= pagingData.PageSize;
			$(settings.oInstance).trigger('page', settings);
		}
		fnCallbackDraw( settings );
	};
	pagingData.First = function(){
		settings._iDisplayStart = 0;
		$(settings.oInstance).trigger('page', settings);
		fnCallbackDraw( settings );
	};
	pagingData.GotoPage = function(num){
		settings._iDisplayStart = pagingData.PageSize * (num - 1);
		$(settings.oInstance).trigger('page', settings);
		fnCallbackDraw( settings );
	};

	return pagingData;
}

function TableStateClear()
{
	var url = Page.GetCurrentUrl('', false, StateParams);
	Page.Ajax.Send('TableState_Clear', url, function(){ location.reload(); });
}

if (typeof window.RefreshPageFunc == 'undefined')
	window.RefreshPageFunc = function ()
	{
		if(lastDataTable)
			lastDataTable.fnDraw(false);
	};

function ShowDetailPage(id)
{
	Page.Open(window[DetailPage], {id: id});
}

function DataGridDelete(rowId, row)
{
	var tempCls = USE_BS_UI ? 'danger' : 'ui-state-highlight';
	var model = $(row).parents('TABLE').first().attr('id');
	$(row).parent().children().removeClass(tempCls);
	$(row).addClass(tempCls);
	PageDeleteRecord(rowId, 'DataGridDelete', '', {id: rowId, model: model});
}

function ToolbarItemClicked()
{
	this.blur();
	var tableId  = $(this).attr('table_id');
	var oTable = $('#' + tableId ).dataTable();
	var cb = $(this).attr('cb');
	if (cb)
		window[cb](this, tableId, oTable);
}

function ShowFullScreen(obj, tableId, oTable)
{
	var wrapper = $('#' + tableId).parents('.dataTables_wrapper').get(0);
	if (typeof wrapper.fullScreen == "undefined")
		wrapper.fullScreen = false;
	if (typeof wrapper.overlay == "undefined")
		wrapper.overlay = $('<div></div>')
			.appendTo($('body'))
			.hide()
			.css({
				position : 'fixed',
				left: 0,
				right: 0,
				top: 0,
				bottom: 0,
				background: 'white'});

	if (wrapper.fullScreen)
	{
		$(wrapper).css({
			position: 'relative',
			zIndex: 0
		});
		if (wrapper.oldSibling && wrapper.oldSibling.length >0)
			$(wrapper).insertBefore(wrapper.oldSibling);
		else
			$(wrapper).appendTo(wrapper.oldParent);
		$(wrapper.overlay).hide();
	}
	else
	{
		wrapper.oldParent = $(wrapper).parent();
		wrapper.oldSibling= $(wrapper).next();
		$(wrapper.overlay).show();
		$(wrapper).css({
			position: 'absolute',
			left: 0,
			right: 0,
			top: 0,
			zIndex: 99
		}).appendTo('body');
	}
	wrapper.fullScreen = !wrapper.fullScreen;
}

function AlanSeciciDialog(obj, tableId, oTable)
{
	var cols = oTable.fnSettings().aoColumns;
	var div = $('#DIV_FieldSelector');
	if (div.length == 0)
		div = $('<div title="Alan Seçici"></div>')
				.attr('id', 'DIV_FieldSelector')
				.appendTo(document.body);
	div.attr('table_id', tableId).html('');
	var tbl = $('<table style="height: 100%; border-collapse: collapse;" width="100%" cellspacing=0></table>').appendTo(div);
	var row = $('<tr></tr>').appendTo(tbl);
	var cls = 'odd';
	var dt = dts[tableId];
	for(var i=0; i<cols.length; i++)
	{
		var id = "cb_field_" + i;
		var c = cols[i];
		var grp = c.GroupName;
		var tbl2 = $(tbl).find('TABLE[group="' + grp + '"]');
		if (tbl2.length == 0)
		{

			tbl2 = $('<td class="field_group" valign="top">' +
					'<table width="100%"  cellspacing=2 cellpadding=2 group="' + grp + '"><tr><td><b>' + grp + '</b></table></td>')
					.addClass(cls)
					.appendTo(row).find('TABLE').first();
			cls = cls == 'odd' ? 'even' : 'odd';
		}
		var ibx = $('<tr><td class="field"></td></tr>').appendTo(tbl2)
				.find('TD').first()
				.html("<input type=checkbox id='" +id + "'> <label for='" + id + "'>" + c.sTitle + "</label>")
				.find('INPUT[type=checkbox]')
				.attr('col_index', i);
		if(c.bVisible)
			ibx.attr('checked', 'checked');
	}
	$('TD.field').css({
		whiteSpace: 'nowrap'
	});
	var oddTr = $('TR.odd');
	var evenTr= $('TR.even');
	if (oddTr.length == 0)
		oddTr = $('<tr class="odd"></tr>').hide().appendTo('body');
	if (evenTr.length == 0)
		evenTr = $('<tr class="even"></tr>').hide().appendTo('body');
	var color1 = oddTr.css("background-color");
	var color2 = evenTr.css("background-color");
	$('TD.odd').css('background-color', color1);
	$('TD.even').css('background-color', color2);
	var w = 100/$('TABLE[group]').length;
	$('TABLE[group]').parent().css('width', w + '%');
	Jui.InitCheckboxes(div);
	Page.ShowDialog(div.attr('id'), 500, 400, AlanSecici);
};

function AlanSecici()
{
	var div = $('#DIV_FieldSelector');
	var inpts = div.find('input[col_index]');
	var oTable = $('#' + div.attr('table_id')).dataTable();
	for(var i = 0; i < inpts.length; i++)
	{
		var idx = inpts.eq(i).attr('col_index');
		var vis = inpts.eq(i).attr('checked') == "checked";
		oTable.fnSetColumnVis( idx, vis);
	}
	return true;
}

function Converter(obj, tblId, oTable)
{
	var count = parseInt(DataTableSummary[tblId][0]);
	if (count > 1000 && !confirm(
		'Çok fazla kayıt için (' + Number.Format(count, 0) + ' kayıt) dışa aktarma yaptığınızda, bu işlem sunucuda yük oluşmasına ve yavaşlamaya sebep olacaktır\n\nDevam ediyor musunuz?'))
		return false;
	var fileType = $(obj).attr('ftype');
	var dt = oTable.fnSettings();
	var params = oTable.oApi._fnAjaxParameters(oTable.dataTable().fnSettings());
	if (typeof Ara == "function")
	{
		var searchParams = Ara(null, null, 1);
		for(var i in searchParams)
			params.push({name: i, value: searchParams[i]});
	}
	var visibleCols = [];
	for(var i=0; i<dt.aoColumns.length; i++)
		if (dt.aoColumns[i].bVisible)
			visibleCols.push(dt.aoColumns[i].sName);
	params.push({ name: 'sConditionOperators', value: Ara(null, null, 2)});
	var colSize = [];
	var tblWidth = $('#' + tblId).width();
	var cells = $('#' + tblId).find('TBODY TR').eq(0).children();
	var total = 0;
	var val = 0;
	for(var i=0; i<cells.length; i++)
	{
		total += val = parseInt(100 * (cells.eq(i).width() / tblWidth));
		colSize.push( val );
	}
	if (total < 100)
		colSize[0] += 100 - total;

	var href = window.location.href.replace(/#.*$/, '');
	if(UseRewriteAct == "1")
		href = href.replace(/\/?$/, '');
	else
	{
		if (! href.match(/[?]/))
			href += '?';
	}
	var splitter = UseRewriteAct == "1" ? "/" : "&";
	href = Page.UrlChangeParam("table_id",tblId,href);
	href = Page.UrlChangeParam("grid",1,href);
	// href += splitter + 'table_id=' + tblId + splitter +'grid=1';
	params.push({name: 'table_id', value: tblId});
	params.push({name: 'selected_fields', value: visibleCols.join(',')});
	params.push({name: 'field_sizes', value: colSize.join(';')});
	if (fileType == 'pdf' && ($.browser.chrome || count <= 150))
	{
		var param = Array.FindByKey(params, 'name', 'iDisplayLength');
		if (! param)
			params.push({name: 'iDisplayLength', value: 1e6});
		else
			param.value = 1e6;
		Page.ShowProcessingMessage(null, 'PDF Üretiliyor...');
		$.post(href = Page.UrlChangeParam("onlyData",1,href), params, function(data){
			var resp = JSON.TryParse(data);
			if (! resp)
				Page.ShowError('PDF dönüşümü sırasında bir hata oluştu :\n\n' + data, Page.CloseProcessingMessage);
			else {
				var lib = KNJIZ_URL;
				if (lib == '')
					lib = 'knjiz';
				var js = [lib + '/others/pdfmake/pdfmake.min.js', lib + '/others/pdfmake/vfs_fonts.js'];
				if ($.browser.chrome)
				{
					var t = js[0];
					js[0] = js[1];
					js[1] = t;
				}
				requirejs(js, function() {
					GeneratePdf(resp, dt.aoColumns, visibleCols, colSize);
				});
			}
			Page.CloseProcessingMessage();
		});
	}
	else
	{
		var f = $('#JQueryForm_' + tblId);
		if (f.length == 0)
			f = $('<form></form>').attr('id', '#JQueryForm_' + tblId)
				.appendTo(document.body).hide();
		else
			f = $(f);
		f.html('');
		f.attr('action', href = Page.UrlChangeParam("export",fileType,href));
		// f.attr('action', href + splitter + 'export=' + fileType);
		f.attr('method', 'POST');
		$('<input type="hidden"></input>')
			.attr('name', 'data')
			.val(JSON.stringify(params))
			.appendTo(f);
		f.show().submit();
		Page.FileDownloadMessage();
	}
}

function Bootstrapize(tableId)
{
	var table = $('#' + tableId).addClass('table table-striped table-hover').removeAttr('border');
	var panel = table.parents('.dataTables_wrapper').addClass('bsui-table-wrapper panel panel-primary');
	var toolbars = panel.find('.fg-toolbar').attr('class', '');
	toolbars.eq(0).addClass('panel-heading clearfix').appendTo(panel);
	$('<div class="panel-body no-padding table-responsive">').append(table).appendTo(panel);
	toolbars.eq(1).addClass('panel-footer clearfix').appendTo(panel);
}

function GeneratePdf(data, cols, visibleColNames, colSizes)
{
	var inArray = function(a, b){
		for(var i=0; i<b.length; i++)
			if (b[i] == a)
				return i;
		return -1;
	};
	var headers = [];
	var indexes = [];
	for(var i=0; i<cols.length; i++)
	{
		var name = cols[i].sName;
		var title = cols[i].sTitle;
		if (inArray(name, visibleColNames) >= 0 && name != "Sec" && name != "islem")
		{
			headers.push({ text: title, style: 'tableHeader' });
			indexes.push(i);
		}
	}
	var rows = [headers];
	for(var i=0; i<data.length; i++)
	{
		var row = [];
		for(var k=0; k<data[0].length; k++)
			if (inArray(k, indexes) >= 0)
				row.push(data[i][k]);
		rows.push(row);
	}
	// Gereksiz sütunları sil
	for(var name in {'Sec': 1, 'islem' : 1}){
		var i = inArray(name, visibleColNames);
		if (i >= 0)
		{
			visibleColNames.splice(i, 1);
			colSizes.splice(i, 1);
		}
	}
	// Sütunları yeniden boyutlandır
	var total = 0;
	colSizes.map(function(v){ total+= v; });
	colSizes = colSizes.map(function(v){ return Math.round(100 * v / total) + '%'; });
	// İçeriği hazırla ve üret
	var d = {
		content: [
			{ text: window.document.title, style: 'header' },
			{
				style: 'tableExample',
				table: {
						headerRows: 1,
						widths: colSizes,
						keepWithHeaderRows: 1,
						body: rows
					}
			}
		],
		styles: {
			header: {
				fontSize: 15,
				bold: true,
				margin: [-15, -20, -15, 10]
			},
			tableExample: {
				margin: [-15, 0, -15, 0],
				fontSize: 9,
				color: 'black'
			},
			tableHeader: {
				bold: true,
				fontSize: 10
			}
		}
	};
	return pdfMake.createPdf(d).download(dtTableIds[0] + '.pdf');
};

$(function(){
	$.fn.dataTableExt.oPagination.dogru_paging = {
		"fnInit": function ( oSettings, nPaging, fnCallbackDraw )
		{
			PagingFnCallbackDraw = fnCallbackDraw;

			var buttons = [
				{cls: 'first', title: 'İlk Sayfa', icon : 'seek-first'},
				{cls: 'previous', title: 'Bir Önceki Sayfa', icon : 'seek-prev'},
				{cls: 'next', title: 'Sonraki Sayfa', icon : 'seek-next'},
				{cls: 'last', title: 'Son Sayfa', icon : 'seek-end'}
			];
			for(var i=0; i<buttons.length; i++)
			{
				var btnDom = '<button>&nbsp;</button>';
				var b = buttons[i];
				var cmd = b.cls;
				var btn = $(btnDom)
					.addClass(b.cls + ' jui-button')
					.attr('title', b.title)
					.attr('cmd', cmd)
					.attr('icon', 'ui-icon-' + b.icon)
					.attr('toolbar', 1)
					.appendTo(nPaging);
				if (cmd != 'page')
					btn.click(function(){
						oSettings.oApi._fnPageChange( oSettings, $(this).attr('cmd'));
						fnCallbackDraw( oSettings );
					});
			}
			Jui.InitButtons(nPaging, 1);
			var page = $('<input type="text" class="page_number normal-input" var_type="int">');
			var pageCnt = $('<input type="text" readOnly="readOnly" class="page_count normal-input">');
			$(nPaging).find('.next').before(page);
			$(nPaging).find('.next').before(pageCnt);
			if (USE_BS_UI)
			{
				page.addClass('btn btn-sm');
				pageCnt.addClass('btn btn-sm');
				$(nPaging).find('.btn').removeClass('btn-xs').addClass('btn-sm');
			}
			else
			{
				page.addClass('ui-button ui-button-icon-only');
				pageCnt.addClass('ui-button ui-button-icon-only');
			}

			Jui.InitInputs(nPaging, false);
			page.change(function(event){
				var pg = $(this).val();
				var pg = parseInt(pg);
				var pgCount = parseInt(pageCnt.attr('page_count'));
				if( pg > pgCount || pg <= 0)
					return $(this).css('color', 'red');
				$(this).val(pg);
				oSettings.oApi._fnPageChange( oSettings, pg - 1 );
				fnCallbackDraw( oSettings );
			});
		},

		"fnUpdate": function ( oSettings, fnCallbackDraw )
		{
			if ( !oSettings.aanFeatures.p )
			{
				return;
			}

			var tableId = oSettings.sTableId;
			var customPaging = dts[tableId].CustomPagingFunc;
			if (customPaging && typeof window[customPaging] == "function")
			{
				var pagingData = GetCustomPagingData(oSettings, fnCallbackDraw);
				return window[customPaging](pagingData);
			}

			/* Loop over each instance of the pager */
			var an = oSettings.aanFeatures.p;
			var pg = Math.floor(oSettings._iDisplayStart / oSettings._iDisplayLength) + 1;
			var pgCnt = Math.ceil(oSettings._iRecordsDisplay / oSettings._iDisplayLength);
			for ( var i=0, iLen=an.length ; i<iLen ; i++ )
			{
				$(an[i]).find('INPUT.page_number').val(pg).css({color: ''});
				$(an[i]).find('INPUT.page_count').val('/ ' + pgCnt).attr('page_count', pgCnt);
				if ( oSettings._iDisplayStart === 0 )
					$(an[i]).find('.first,.previous').button('disable');
				else
					$(an[i]).find('.first,.previous').button('enable');

				if ( oSettings.fnDisplayEnd() == oSettings.fnRecordsDisplay() )
					$(an[i]).find('.next,.last').button('disable');
				else
					$(an[i]).find('.next,.last').button('enable');
			}
		}
	};
});

$.fn.gridLoad = function(handler){
	if (typeof handler == "function")
		$(this).on('gridLoad', handler);
	else
		$(this).trigger('gridLoad');
};

$.fn.getCheckedIds = function(){
	var checked = [];
	$(this).find('TBODY TR INPUT[type="checkbox"]:checked').each(function(){
		var row = $(this).parents('TR').first();
		checked.push( $(row).attr('row_id') );
	});
	return checked;
};

$.fn.getCheckedRows = function(){
	var checked = [];
	$(this).find('TBODY TR INPUT[type="checkbox"]:checked').each(function(){
		var row = $(this).parents('TR').first();
		checked.push( row );
	});
	return checked;
};

$.fn.checkAllRows = function( checked ){
	var cbs = $(this).find('TBODY TR INPUT[type="checkbox"]');
	if (checked)
		cbs.attr('checked', 'checked');
	else
		cbs.removeAttr('checked');
	cbs.change();
};
