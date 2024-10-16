/* global Page, DetailPage, Ara, Jui, Form */

var queryOptions = {};
var dts = {};
var dtTableIds = [];
var DataTableAttr = new Object();
var DataTableTdAttr = new Object();
var DataTableSummary = new Object();
var lastDataTable = null;
var refreshDataTable = false;
var SaveState = true;
var StateParams = '';

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
	var nonReorderableLeft = 0;
	var nonReorderableRight = 0;
	var fixedCols = [];
	var counter = 0;
	for(var i in dt.DataTable.Columns)
	{
		var c = dt.DataTable.Columns[i];
		if(c.GroupName == '')
			c.GroupName = 'Genel';
		debugger;
		var visible = c.Visible == 1 && (v++) < 30;
		var width = 'auto';
		if(c.Width)
			width = c.Width;
		cols.push({
			sTitle: c.DisplayName,
			bSortable : c.Sortable,
			bSearchable: c.Searchable,
			sClass : 'DataCell_' + c.Align,
			bVisible : visible,
			GroupName: c.GroupName,
			ColIndex : k,
			sWidth : width,
			sName: i,
			Editable: c.Editable,
			buttons: c.Buttons
		});
		colDefs.push({
			"sName": i,
			"aTargets": [ k++ ]
		});
		if (i == 'Sec')
			nonReorderableLeft = 1;
		if (c.Buttons && c.Buttons.length > 0)
			nonReorderableRight++;
		if (c.FixedColumn)
			fixedCols.push(counter);
		counter++;
	}

	// Fixed sütunları başa alıyoruz
	for(var i=nonReorderableLeft; i<fixedCols.length; i++)
	{
		var index = fixedCols[i];
		var tmp = cols[i];
		cols[i] = cols[index];
		cols[index] = tmp;
		tmp = colDefs[i];
		colDefs[i] = colDefs[index];
		colDefs[index] = tmp;
	}

	var ajaxOptions = {
		"bProcessing": true,
		"sAjaxSource": window.location.href + '&grid=1&table_id=' + tableId,
		"sServerMethod": "POST",
		"bServerSide": true,
		"bPaginate": true,
		"fnServerParams": function ( aoData ) {
			queryOptions = {};
			var customParams = null;
			if( typeof GetCustomParams == "function")
				customParams = GetCustomParams();
			if (typeof Ara == "function")
				queryOptions = Ara(null, customParams, true);
			for(var i in queryOptions)
				aoData.push( { name: i, value: queryOptions[i] } );
			aoData.push( {name: 'table_id', value: $(this).attr('id') });
		}
	};
	var sDom = 'rt';
	// H: Header
	// F: Footer
	// R: ColReorder plug-in
	var s = (dt.ShowNewButton ? '<"div_new_btn">' : 'l');
	s = (dt.ShowSearch ? 'f' : s);
	var hOpt = '<"H"<"toolbar">' + s + '>';
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
		"aaData": dt.DataTable.Data,
		"aoColumns" : cols,
		"aoColumnDefs": colDefs,
		"bAutoWidth": false,
		"bStateSave": true,
		// "responsive": true,
		"serverSide" : true,
		"oLanguage": dtTextTR,
		"colReorder": {
			fixedColumnsLeft: nonReorderableLeft,
			fixedColumnsRight: nonReorderableRight
		},
		"fnRowCallback": function(nRow, aData, iDisplayIndex){
			RenderRow(nRow, $(this));
		},
		"drawCallback": function (oSettings) {
			var tblId = $(this).attr('id');
			if (fixedCols.length == 0)
				DrawCallback(tblId);
		},
		"stateLoadParams": function (settings, data) {
			 return false;
		},
		"stateSaveCallback": function (oSettings, oData) {
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
			Page.Ajax.Send('TableState_Save', obj, function(){}, '');
		},
		"fnServerData": function ( sSource, aoData, fnCallback ) {
			Page.Loading();
			$(this).parents('.dataTables_wrapper').first()
					.find('.dataTables_processing')
					.css('opacity', '0');
			var data = [];
			for(var i=0; i<aoData.length; i++)
				if (! aoData[i].name.match(/(mDataProp|bRegex|bSearchable|bSortable|sSearch)_[0-9]+/i))
					data.push(aoData[i]);
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
	if (fixedCols.length > 0)
	{
		var fixOptions = {
			"scrollX": true,
			"scrollY": false,
			"scrollCollapse": false
		};
		options = $.extend(options, fixOptions);
	}
	if (dt.ShowPaging && ! dt.DataTable.StaticGrid)
		for(var i in ajaxOptions)
			options[i] = ajaxOptions[i];
	var oTable = lastDataTable = $('#' + tableId).dataTable( options );
	oTable.DataTable().on('column-reorder', function(e, settings, details){
		var rows = $(this).find('TBODY>TR');
		for(var i=0; i<rows.length; i++)
			RenderRow(rows.eq(i), this);
		$(this).gridLoad();
	});
	if (fixedCols.length > 0)
		new $.fn.dataTable.FixedColumns( oTable, {
			"leftColumns" : fixedCols.length + nonReorderableLeft,
			"drawCallback" : function(){
				var tblId = lastDataTable.DataTable.tables()[0].id;
				DrawCallback(tblId);
		}});
	if(dt.ShowPaging)
		$("div.dataTables_filter input").unbind('keyup').keyup( function (e) {
			if (e.keyCode == 13) {
				oTable.fnFilter( this.value );
			}
			e.stopPropagation();
		} );
	if (dt.Sorting && typeof dt.Sorting[0] != "undefined")
		lastDataTable.fnSort([ dt.Sorting[0] ]);
	var toolBar = $('#' + tableId).parents('.dataTables_wrapper').find('.toolbar').css('float', 'left');
	var buttons = [
		{text: 'Özelleştirme Temizle', cb: 'TableStateClear', ftype: '', icon: 'ui-icon-refresh', visible: false},
		{text: 'Alanlar', cb: 'AlanSeciciDialog', ftype: '', icon: 'ui-icon-gear'}
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
			$('<button></button>').html(b.text)
			.attr('ftype', b.ftype)
			.attr('cb', b.cb)
			.attr('table_id', tableId)
			.appendTo(toolBar)
			.click(ToolbarItemClicked)
			.button({
				icons: {
					primary: b.icon
				},
				text: is_set(b.visible) ? b.visible : true
			});
		}
	toolBar.buttonset();
	var newClick = dt.NewButtonClickFunc;
	if ( newClick == '')
		newClick = dt.RowClickFunc;
	var divNewBtn = $('#' + tableId).parents('.dataTables_wrapper').find('.div_new_btn').css('float', 'right');
	Jui.button(divNewBtn, {text: dt.NewButtonText, cb: window[newClick], ftype: '', icon: 'ui-icon-plusthick'});

	setTimeout(RefreshDataTableFunc, 500);
}

function DrawCallback(tblId)
{
	var dt = dts[tblId];
	var ajax = dt.ShowPaging && !dt.DataTable.StaticGrid;
	if (typeof AttributesLoaded != "undefined" && !ajax)
		return;
	AttributesLoaded = 1;
	Page.CloseProcessingMessage();
	MakeRowsClickable(tblId);
	// Attribute eklenmesi
	var rows = $('#' + tblId).parents('DIV.dataTables_wrapper').find('TABLE.dataTable TBODY TR');
	if (DataTableAttr[tblId])
	{
		var data = DataTableAttr[tblId];
		for(var i=0; i<rows.length && i<data.length; i++)
		{
			var rowAttr = data[i];
			var row = $(rows.get(i)).add(rows.get(i).OtherRow);
			for(var name in rowAttr)
				if (name.toLowerCase() == 'class')
					row.addClass(rowAttr[name]);
				else
					row.attr(name, rowAttr[name]);
		}
	}
	// Td Attribute eklenmesi
	if (DataTableTdAttr[tblId])
	{
		var data = DataTableTdAttr[tblId];
		for(var i=0; i<rows.length && i<data.length; i++)
		{
			var tds = data[i];
			for(var fname in tds)
			{
				var attrs = tds[fname];
				var row = $(rows.get(i)).add(rows.get(i).OtherRow);
				var td = row.find('td[field_name="' + fname + '"]');
				for(var name in attrs)
					if (name.toLowerCase() == 'class')
						td.addClass(attrs[name]);
					else
						td.attr(name, attrs[name]);
			}
		}
	}

	rows.find('td[field_name="islem"]').click(function(e){
		$(this).find('button').first().click();
		e.stopPropagation();
	});

	var div = $('.summary[for=' + tblId + ']');
	if(div.length > 0)
	{
		var sum = DataTableSummary[tblId];
		for(var name in sum)
		{
			var item = div.find('.' + name);
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
	var glf = dt.GridLoadFunc;
	if(glf)
		window[glf]();
	// Harici handler çağrılıyor (varsa çalışacak)
	$('#' + tblId).gridLoad();
}

function MakeRowsClickable(tblId)
{
	var dt = dts[tblId];
	$('#' + tblId).closest('DIV.dataTables_wrapper')
		.find('TABLE.dataTable TBODY TR')
		.css('cursor', 'pointer')
		.each(function(){
			if (this.OtherRow)
				return;
			var rowIndex = $(this).index();
			var tables = $(this).closest('DIV.dataTables_wrapper').find('TABLE.dataTable TBODY');
			var thisTable = $(this).closest('TABLE.dataTable').find('TBODY').get(0);
			var otherTable = tables.eq(0);
			if (tables.length > 1 && thisTable == tables.get(0))
				otherTable = tables.eq(1);
			var otherRow = otherTable.find('TR').get(rowIndex);
			if (! otherRow)
				return;
			otherRow.OtherRow = this;
			otherRow.OtherTable = thisTable;
			this.OtherRow = otherRow;
			this.OtherTable = otherTable.get(0);
		})
		.click(function(){
			var cb = $(this).closest('TABLE').attr('onrowclick');
			if (! cb || !$(this).attr('row_id'))
				return;
			if(! dt.DataTable.Columns.Sec)
			{
				$(this.OtherTable).find('TBODY TR').removeClass('ui-state-highlight');
				$(this.OtherRow.OtherTable).find('TBODY TR').removeClass('ui-state-highlight');
			}
			$(this).addClass('ui-state-highlight');
			$(this.OtherRow).addClass('ui-state-highlight');
			window[cb]($(this).attr('row_id'), this);
		});
}

function RenderRow(nRow, nTable)
{
	var cells = $(nRow).children('TD');
	var cols = $(nTable).dataTable().fnSettings().aoColumns;
	var names = [];
	for(var i=0; i<cols.length; i++)
		if (cols[i].bVisible)
			names.push(cols[i]);

	for(var i=0; i<cells.length; i++)
	{
		var col = names[i];
		var cell = cells.eq(i);
		cell.attr('field_name', col.sName);
		// Editable için
		if (col.Editable && col.sName != 'Sec' && ! col.buttons)
		{
			var val = cell.find('INPUT,SELECT').val();
			if (cell.attr('edit_value'))
				val = cell.attr('edit_value');
			cell.attr('edit_value', val)
				.find('INPUT,SELECT')
				.addClass('dataTable_edit')
				.val(val)
				.click(function(e){
					e.stopPropagation();
				})
				.change(function(){
					$(this).closest('TD').attr('edit_value', $(this).val());
				});
		}
		// Buttons için
		if (! col.buttons)
			continue;
		for(var j=0; j<col.buttons.length; j++)
		{
			var b = col.buttons[j];
			if(! b.CallBackFunc)
				continue;
			b.attr = {func: b.CallBackFunc};
			col.buttons[j].cb = function(e){
				var r = $(nRow);
				var func = $(this).attr('func');
				window[func](r.attr('row_id'), r, this);
				Jui.CloseCurrentMenu();
				e.stopPropagation();
			};
		} // for
		if (cell.find('BUTTON.ui-button').length == 0)
			cell.removeClass('hasJuiButtons');
		var bs = Jui.BUTTON_STYLE_NORMAL;
		if (col.buttons.length > 1)
			bs = Jui.BUTTON_STYLE_DROPDOWN;
		Jui.button(cell, col.buttons, bs);
	}// for

	// Checkbox'lar
	$(nRow).find('INPUT[type="checkbox"]').click(function(e){
		e.stopPropagation();
	}).change(function(){
		var row = $(this).closest('TR').get(0);
		row = $(row).add(row.OtherRow);
		if (this.checked)
			row.addClass('ui-state-highlight').css('font-weight', 'bold');
		else
			row.removeClass('ui-state-highlight').css('font-weight', '');
	}).closest('td').click(function(e){
		$(this).find('input').click();
		e.stopPropagation();
	});

	Jui.InitInputs(nRow);
}

function TableStateClear()
{
	var url = Page.GetCurrentUrl('', false, StateParams);
	Page.Ajax.Send('TableState_Clear', url, function(){location.reload();});
}

function RefreshDataTableFunc()
{
	setTimeout(RefreshDataTableFunc, 500);
	if(refreshDataTable && lastDataTable)
	{
		lastDataTable.fnDraw(false);
		refreshDataTable = false;
	}
}

function ShowDetailPage(id)
{
	Page.Open(window[DetailPage], {id: id});
}

function DataGridDelete(rowId, row)
{
	var model = $(row).parents('TABLE').first().attr('id');
	$(row).parent().children().removeClass('ui-state-highlight');
	$(row).addClass('ui-state-highlight');
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
			zIndex: 50
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
	var fileType = $(obj).attr('ftype');
	var dt = oTable.fnSettings();
	var params = oTable.oApi._fnAjaxParameters(oTable.dataTable().fnSettings());
	if (typeof Ara == "function")
	{
		var searchParams = Ara(null, null, true);
		for(var i in searchParams)
			params.push({name: i, value: searchParams[i]});
	}
	var visibleCols = [];
	for(var i=0; i<dt.aoColumns.length; i++)
		if (dt.aoColumns[i].bVisible)
			visibleCols.push(dt.aoColumns[i].sName);
	var colSize = [];
	var tblWidth = $('#' + tblId).width();
	var cells = $('#' + tblId).find('TBODY TR').eq(0).children();
	var total = 0;
	for(var i=0; i<cells.length; i++)
	{
		total += val = parseInt(100 * (cells.eq(i).width() / tblWidth));
		colSize.push( val );
	}
	if (total < 100)
		colSize[0] += 100 - total;

	var f = $('#JQueryForm_' + tblId);
	if (f.length == 0)
		f = $('<form></form>').attr('id', '#JQueryForm_' + tblId)
			.appendTo(document.body).hide();
	else
		f = $(f);
	f.html('');
	f.attr('action', window.location.href + '&grid=1&export=' + fileType);
	f.attr('method', 'POST');
	params.push({name: 'table_id', value: tblId});
	params.push({name: 'selected_fields', value: visibleCols.join(',')});
	params.push({name: 'field_sizes', value: colSize.join(';')});
	$('<input type="hidden"></input>')
		.attr('name', 'data')
		.val(JSON.stringify(params))
		.appendTo(f);
	f.show().submit();
	Page.FileDownloadMessage();
}

$(function(){
	$.fn.dataTableExt.oPagination.dogru_paging = {
		"fnInit": function ( oSettings, nPaging, fnCallbackDraw )
		{
			var btnDom = '<button>&nbsp;</button>';
			var nFirst = $(btnDom).addClass('first').html('İlk Sayfa').appendTo(nPaging);
			var nPrevious = $(btnDom).addClass('previous').html('Bir Önceki Sayfa').appendTo(nPaging);
			var nPage = $(btnDom).addClass('page').html('&nbsp;').appendTo(nPaging);
			var nNext = $(btnDom).addClass('next').html('Sonraki').appendTo(nPaging);
			var nLast = $(btnDom).addClass('last').html('Son').appendTo(nPaging);

			nFirst.button({text: false, icons: {primary: 'ui-icon-seek-first'}});
			nPrevious.button({text: false, icons: {primary: 'ui-icon-seek-prev'}});
			nNext.button({text: false, icons: {primary: 'ui-icon-seek-next'}});
			nLast.button({text: false, icons: {primary: 'ui-icon-seek-end'}});

			$(nPaging).buttonset().find('span,input').css('font-size', '0.8em');
			var inp = $('<input style="position:absolute;left:0.2em;right:0.2em;top:0.25em;bottom:0.4em;width:48%;height:70%;border:0;text-align:right"/>');
			inp.appendTo(nPage.find('span'));
			nPage.css('background', 'white').find('span').css('width', '70');
			$('<span class="total_page" style="float:right;position:absolute;width:50%;right:0.2em;"></span>')
				.appendTo(nPage.find('span')).css('font-size', '1em');

			$(nFirst).click( function () {
				oSettings.oApi._fnPageChange( oSettings, "first" );
				fnCallbackDraw( oSettings );
			} );

			$(nPrevious).click( function() {
				oSettings.oApi._fnPageChange( oSettings, "previous" );
				fnCallbackDraw( oSettings );
			} );

			$(nPage).focus( function () {
				$(this).find('input').focus();
			} );

			$(nPage).find('INPUT').keypress(function(event){
				if (event.charCode > 0 && isNaN(String.fromCharCode(event.charCode)))
					return false;
				if (event.keyCode == 13)
				{
					var pg = parseInt($(this).val());
					if(! pg)
						pg = 0;
					$(this).val(pg);
					var pgCnt = Math.ceil(oSettings._iRecordsDisplay / oSettings._iDisplayLength);
					if (pg > pgCnt || pg <= 0)
						return Page.ShowError('Yazılan sayfa numarası ' +
								'1-' + pgCnt + ' arasında olmalıdır');
					oSettings.oApi._fnPageChange( oSettings, pg - 1 );
					fnCallbackDraw( oSettings );
				}
				return true;
			});

			$(nNext).click( function() {
				oSettings.oApi._fnPageChange( oSettings, "next" );
				fnCallbackDraw( oSettings );
			} );

			$(nLast).click( function() {
				oSettings.oApi._fnPageChange( oSettings, "last" );
				fnCallbackDraw( oSettings );
			} );
		},

		"fnUpdate": function ( oSettings, fnCallbackDraw )
		{
			if ( !oSettings.aanFeatures.p )
			{
				return;
			}

			/* Loop over each instance of the pager */
			var an = oSettings.aanFeatures.p;
			var pg = Math.floor(oSettings._iDisplayStart / oSettings._iDisplayLength) + 1;
			var pgCnt = Math.ceil(oSettings._iRecordsDisplay / oSettings._iDisplayLength);
			for ( var i=0, iLen=an.length ; i<iLen ; i++ )
			{
				$(an[i]).find('input').val(pg);
				$(an[i]).find('.total_page').html(' / ' + pgCnt);
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
	var parent = $(this).closest('DIV.dataTables_wrapper');
	$(parent).find('TABLE.dataTable TBODY TR INPUT[type="checkbox"]:checked')
			.each(function(){
				var row = $(this).parents('TR').first();
				checked.push( $(row).attr('row_id') );
			});
	return checked;
};

$.fn.getCheckedRows = function(){
	var checked = [];
	var parent = $(this).closest('DIV.dataTables_wrapper');
	$(parent).find('TABLE.dataTable TBODY TR INPUT[type="checkbox"]:checked')
			.each(function(){
				var row = $(this).parents('TR').first();
				checked.push( row );
			});
	return checked;
};

$.fn.checkAllRows = function( checked ){
	var parent = $(this).closest('DIV.dataTables_wrapper');
	var cbs = $(parent).find('TABLE.dataTable TBODY TR INPUT[type="checkbox"]');
	if (checked)
		cbs.attr('checked', 'checked');
	else
		cbs.removeAttr('checked');
	cbs.change();
};

$.fn.getListData = function() {
	var rows = $(this).parents('DIV.dataTables_wrapper').find('TABLE.dataTable TBODY TR');
	var data = [];
	for(var i=0; i<rows.length; i++)
	{
		var row = rows.eq(i);
		var rowId = row.attr('row_id');
		var d = {};
		var exists = true;
		if (! (d = Array.FindByKey(data, 'Id', rowId)))
		{
			d = {Id : rowId};
			exists = false;
		}
		var cells = row.find('TD[field_name]');
		for (var k=0; k<cells.length; k++)
		{
			var cell = cells.eq(k);
			var name = cell.attr('field_name');
			if (name == 'islem')
				continue;
			var inp = cell.find('INPUT,SELECT,TEXTAREA');
			if (inp.length > 0)
				d[name] = Form.GetValue(inp);
			else
				d[name] = cell.html();
		}
		if (!exists)
			data.push(d);
	}
	return data;
};
