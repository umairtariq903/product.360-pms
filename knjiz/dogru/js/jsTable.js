/* global Jui, EmailExt, SingleFile, TemplateAppFileHtml, Form, Page, TemplateAppFileImageHtml, SingleImage */

JsTableVar = {
	InitNew :
		function (type, id){
			var obj;
			switch (type.toString().toLowerCase()){
				case 'date':
					 obj = new JsTableVar.Date();
					 break;
				case 'datetime':
					 obj = new JsTableVar.DateTime();
					 break;
				case 'time':
					 obj = new JsTableVar.Time();
					 break;
				case 'year':
					 obj = new JsTableVar.Year();
					 break;
				case 'month':
					 obj = new JsTableVar.Month();
					 break;
				case 'lastyears':
					 obj = new JsTableVar.Son10Year();
					 break;
				case 'email':
					 obj = new JsTableVar.Email();
					 break;
				case 'int':
				case 'integer':
					obj = new JsTableVar.Integer();
					break;
				case 'float':
				case 'double':
					obj = new JsTableVar.Float();
					break;
				case 'money':
					obj = new JsTableVar.Money();
					break;
				case 'textarea':
					obj = new JsTableVar.TextArea();
					break;
				case 'richedit':
					obj = new JsTableVar.RichEdit();
					break;
				case 'bool':
					type = {0: 'Hayır', 1:'Evet'};
					obj = new JsTableVar.List(type);
					break;
				case 'percent':
				case 'percentage':
					type = {};
					for(var i=10; i<=100; i+=5)
					{
						if (i < 70 && i % 10 != 0)
							continue;
						type[i] = '% ' + i;
					}
					obj = new JsTableVar.List(type);
					break;
				case 'file':
					obj = new JsTableVar.File();
					break;
				case 'file_image':
					obj = new JsTableVar.FileImage();
					break;
				case 'checkbox':
					obj = new JsTableVar.Checkbox();
					break;
				case 'button':
					obj = new JsTableVar.Button();
					break;
				case 'radio':
				case 'radiobutton':
					obj = new JsTableVar.Radio();
					break;
				case 'inppicker':
					obj = new JsTableVar.InpPicker();
					break;
				default:
					if(typeof type == 'object')
						obj = new JsTableVar.List(type);
					else
						obj = new JsTableVar.Text(id);
			}
			return obj;
		}
};

JsTableVar.InpPicker = function(){
	this.getText = function(val, fieldName, modelName, text){
		if (typeof text == "undefined" || text == null)
			text = '';
		return text;
	};

	this.getInput = function (name, fld){
		var div = $('<div class="inp_picker" text_field="' + fld.Txt + '">');
		$('<input type="hidden" class="id_field"/>').appendTo(div);
		$('<input type="text" class="text_field" disabled="disabled" />')
			.css('width', '200px').appendTo(div);
		$('<button class="jui-button">Seç</button>').appendTo(div);
		Jui.InitButtons(div);
		return div;
	};
};

JsTableVar.Text = function(){
	this.getText = function(val){
		if (typeof val == "undefined")
			val = '';
		return val;
	};

	this.getInput = function (){
		return $('<input/>');
	};
};

JsTableVar.TextArea = function(){
	this.getText = function(val){
		return val;
	};

	this.getInput = function (){
		return $('<textarea>');
	};
};

JsTableVar.RichEdit = function(){
	this.setValue = function(val){
		$('#' + this.id).val(val);
	};

	this.getText = function(val){
		return val;
	};
	this.getInput = function (){
		if (typeof RichEditCount == "undefined")
			RichEditCount = 1;
		this.id = 'richEdit_' + RichEditCount++;
		return $('<textarea rich_edit="1">').attr('id', this.id);
	};
};

JsTableVar.List = function(list){
	var opts = {};
	if($.isArray(list))
		for(var i=0; i<list.length; i++)
			opts[list[i]] = list[i];
	else
		$.extend(opts, list);

	this.getText = function(val){
		if(is_set(opts[val]))
			return opts[val];
		return val;
	};

	this.getInput = function (){
		var sel = $('<select>');
		sel.append($('<option>').attr('value', '').html('Choose...'));
		for(i in opts)
			sel.append($('<option>').attr('value', i).html(opts[i]));
		return sel;
	};
};

JsTableVar.Date = function(){
	this.getText = function(val){
		return val;
	};

	this.getInput = function (){
		return $('<input var_type="date"/>');
	};
};

JsTableVar.DateTime = function(){
	this.getText = function(val){
		return val;
	};

	this.getInput = function (name, fld){
		return $('<input var_type="datetime" step="' + fld.S + '"/>');
	};
};

JsTableVar.Time = function(){
	this.getText = function(val){
		return val;
	};

	this.getInput = function (){
		return $('<input var_type="time"/>');
	};
};

JsTableVar.Time = function(){
	this.getText = function(val){
		return val;
	};

	this.getInput = function (){
		return $('<input var_type="time"/>');
	};
};

JsTableVar.Year = function(){
	this.getText = function(val){
		return val;
	};

	this.getInput = function (){
		return $('<input var_type="year"/>');
	};
};

JsTableVar.Son10Year = function(){
	this.getText = function(val){
		return val;
	};

	this.getInput = function (){
		var select = $('<select/>');
		var buYil = Tarih.GetYear();
		for(var i=0; i <= 10; i++)
			$("<option value='"+ (buYil-i) +"'>").html(buYil-i).appendTo(select);
		return select;
	};
};

JsTableVar.Month = function(){
	this.getText = function(val){
		return Tarih.TumAylar[val];
	};

	this.getInput = function (){
		var select = $('<select/>');
		$.each(Tarih.TumAylar,function(key,val){
			$("<option value='"+ key +"'>").html("("+ key +") " + val).appendTo(select);
		});
		return select;
	};
};

JsTableVar.Integer = function(){
	this.align = 'right';
	this.getText = function(val){
		return val;
	};

	this.getInput = function (){
		return $('<input var_type="int"/>');
	};
};

JsTableVar.File = function(){
	this.getText = function(val){
		return typeof val == 'object' ? '<a yol="' + val.Yol + '" onclick="ShowSingleFile(this, event);">' + val.Ad :'</a>';
	};

	this.setValue = function(val){
		SingleFile.UploadComplete(this.Id, null, val);
	};

	this.getInput = function (id){
		if (typeof _jsTable_file_id == "undefined")
			_jsTable_file_id = 1;
		this.Id = id + _jsTable_file_id++;
		return $(TemplateAppFileHtml).attr('id', this.Id);
	};
};

JsTableVar.FileImage = function(){
	this.default_img = 'images/sample_logo.png';
	this.getText = function(val){
		var yol = this.default_img;
		if (typeof val == 'object')
			yol = val.Yol;

		return '<img src="' + yol + '" yol="' + yol + '" alig=center ' +
				(yol != this.default_img ? ' onclick="ShowSingleFile(this, event);" ' : '') +
				' width=64 height=64>';
	};

	this.setValue = function(val){
		if (typeof val == "undefined")
			val = {ad: '', name: '', Yol: this.default_img};
		if (typeof val.Yol != "undefined")
			val.url = val.Yol;
		SingleImage.UploadComplete(this.Id, null, val);
		if (! val.url || val.url == this.default_img)
			$('.btn-del-upload').prop('disabled', true);
	};

	this.getInput = function (id, fld){
		if (typeof _jsTable_file_id == "undefined")
			_jsTable_file_id = 1;
		this.Id = id + _jsTable_file_id++;
		var div = $(TemplateAppFileImageHtml).attr('id', this.Id);
		if (fld.Aspect > 0)
			div.find('input').attr('aspect', fld.Aspect);
		return div;
	};
};

JsTableVar.Float = function(){
	this.align = 'right';
	this.getText = function(val){
		return Number.Format(val);
	};

	this.getInput = function (){
		return $('<input var_type="float"/>');
	};
};

JsTableVar.Money = function(){
	this.align = 'right';
	this.getText = function(val){
		if (typeof JSTableMoneyDecimal != "undefined")
			return Number.Format(val, JSTableMoneyDecimal);
		else
			return Number.Format(val);
	};

	this.getInput = function (){
		if (typeof JSTableMoneyDecimal != "undefined")
			return $('<input var_type="money" digit="'+JSTableMoneyDecimal+'"/>');
		else
			return $('<input var_type="money"/>');
	};
};

JsTableVar.Checkbox = function(){
	this.align = 'center';
	this.width = '30px';
	this.getText = function(val, fieldName, modelName){
		var cb = $('<input type="checkbox">');
		if (parseInt(val) == 1 || val == true)
			cb.attr('checked', 'checked');
		cb.click(function(evt){
			var tr = $(this).closest('TR');
			if (this.checked)
				tr.get(0).Data[fieldName] = 1;
			else
				tr.get(0).Data[fieldName] = 0;
			evt.stopPropagation();
		});
		return cb;
	};

	this.getInput = function (){
		return JsTableVar.InitNew('bool').getInput();
	};
};

JsTableVar.Button = function(){
	this.align = 'center';
	this.width = '30px';
	this.getText = function(val, fieldName, modelName){
		// var cb = $('<input type="button">');
		/*if (parseInt(val) == 1 || val == true)
			cb.attr('checked', 'checked');
		cb.click(function(evt){
			var tr = $(this).closest('TR');
			if (this.checked)
				tr.get(0).Data[fieldName] = 1;
			else
				tr.get(0).Data[fieldName] = 0;
			evt.stopPropagation();
		});*/
		return cb;
	};

	this.getInput = function (id,fld){
		return $('<button class="btn btn-primary btn-xs" onclick="'+fld.Attr.cb+'(this)"><i class="'+fld.Attr.icon+'"></i></button>');
	};
};

JsTableVar.Radio = function(){
	this.align = 'center';
	this.width = '30px';
	this.getText = function(val, fieldName, modelName){
		var cb = $('<input type="radio">');
		if (val)
			cb.attr('checked', 'checked');
		var name = modelName + '_' + fieldName;
		cb.click(function(evt){
			var tr = $(this).closest('TR');
			$('[name="' + name + '"]').each(function(){
				$(this).closest('TR').get(0).Data[fieldName] = 0;
			});
			tr.get(0).Data[fieldName] = 1;
			evt.stopPropagation();
		});
		cb.attr('name', name);
		return cb;
	};

	this.getInput = function (){
		return JsTableVar.InitNew('bool').getInput();
	};
};

JsTable = {
	getParent: function(obj){
		return $(obj).parents('.has_js_table:first').get(0);
	},
	getModel: function(obj){
		return this.getParent(obj).Model;
	}
};

JsTable.DefaultModel = {
	Name : '',
	Fields: {},
	Data: [],
	Dialog: {
		C: 'Detay', // Caption
		E: '', // Explanation
		W: 500,		// Width
		H: 300		// Height
	},
	RowAttributes: [],
	Deletable: true,
	Insertable: true,
	Sortable: false,
	DetayForm: true,
	RowNumbers: false,
	ShowTitle : false,
	ShowSumRow: false,
	OnUpdate: null,
	OnBeforeShow: null,
	OnShow : null,
	OnClick: null,
	InlineEdit : false,
	MinRowCount: 0,
	NoRecordsMessage : 'Kayıt bulunamadı',
	ExtraRowButtons : [],
	ExtraButtons : [],
	RowTemplate : null
};

JsTable.DefaultField = {
	D:'',	// Display Text
	T:'',	// Type = int,date,money,float,inpPicker veya ['a','b','c']
	W:'',	// Width
	V: 1,	// Visible
	RQ: 0,	// Required
	CS: 1,	// Colspan = {1, 2}
	R: 0,	// ReadOnly
	G: 0,	// Group
	M: '',	// Mask (Sadece text alan için), örn: '(999)999-9999',
	P: '',	// Eğer type InpPicker seçilmişse, bu özelliğe bakarak, hangi ekrandan
			// veya fonksiyonu kullanarak seçim yapılacağı P ile belirlenir.
			// P -> PAGE_AdminKisi veya KisiSec fonksiyonu tarzı verilmelidir
	PE: {}, // P verildiğinde, sayfa açılırken istenen ekstra parametreler nesne olarak verilir
	Txt:'', // InpPicker kullanıldığında gösterilecek olan metin alan adı
	AC: '', // AutoComplete (window[AC] isimli dizi değişkeni veya fonksiyonu arar),
			// Ör: AC = [{label: '', value:''}, {...}, ..] veya
			//	   AC =[ item1, item2, ...] formatında olmalıdır
	ACL: '',// AutoComplete Label
			// Eğer yukarıdaki dizi ilk formatta verilirse, burada da label için ikinci alan adı verilir
	Sep: ',', // Dizi olarka verilen veriyi string'e çevirirken kullanılacak separator
	F: 0, // Filter
	E: 1, // Enabled
	H: 0, // HTML Enabled
	S: 60, // DateTime için Minute Step miktarı
	EXT: 0,//File inputunda extensionları belirler
	MSZ: 0,//File inputunda maksimum dosya boyutunu belirler(byte olarak)
	SUM: '', // Summary, satır toplam veya kayıt sayısı ('sum','count')
	Aspect: 0, // Type: file_image olduğunda, resme ait Aspect oranı
	MRes: 0, // Type: file_image olduğunda, resme ait boyut 1024X768
	Attr: null, // input' a attribute eklemek için {"unit": 1}
	IsNumeric : function(){
		return $.inArray(this.T, ['int', 'money', 'float']) >= 0;
	},
	FindField : function(fieldName){
		if (! this.Div)
			return null;
		return this.Div.find('[field="' + fieldName + '"]').eq(0);
	},
	toString : function(obj){
		for(var i in obj)
			return obj[i];
		return '';
	},
	OnChange : null
};
/**
 * @param {string} divId
 * @param {object} Model
 * @param {object} data
 */
JsTable.Init = function(divId, Model, data)
{
	if(divId[0] != '#')
		divId = '#' + divId;
	var div = $(divId);
	if(div.length == 0)
		return;
	if(data == null)
		data = [];
	//Model i default ile karşılaştırarak oluştur
	var tbMdl = {};
	$.extend(tbMdl, JsTable.DefaultModel, Model);
	if (! tbMdl.Name)
		tbMdl.Name = divId.substr(1);
	tbMdl.GroupFields = {};
	for(var f in tbMdl.Fields)
	{
		var fld = $.extend({}, JsTable.DefaultField);
		$.extend(fld, tbMdl.Fields[f]);
		fld.R = fld.R || fld.G;
		tbMdl.Fields[f] = fld;
		fld.VarType = JsTableVar.InitNew(fld.T, f);
		if (fld.T == 'richedit')
			fld.CS = 2;
		if(fld.G)
			tbMdl.GroupFields[f] = fld;
	}
	if(! $.isArray(data))
		data = $.map(data, function(value) {return [value];});
	tbMdl.Data = data;
	if(! is_set(Model.Dialog) || ! is_set(Model.Dialog.C))
		tbMdl.Dialog.C = div.attr('title') + ' Detay';
	div.addClass('has_js_table').get(0).Model = tbMdl;
	if (tbMdl.ShowTitle && div.attr('title'))
		$('<div class="ui-widget-header">')
			.css({ padding: '5px'})
			.html(div.attr('title')).appendTo(div);
	var containerDiv = $('<div>').addClass('js_table_container').appendTo(div);
	if (tbMdl.RowTemplate == null)
		JsTable.CreateTable(tbMdl, containerDiv, Model);
	else
		JsTable.CreateRowsFromTemplate(tbMdl, div);
	//Ekle butonu
	if(tbMdl.Insertable)
	{
		var AddBtn = $('<button class="jui-button" icon="ui-icon-plusthick">')
				.css('margin-top', '10px')
				.html('Add').appendTo(containerDiv)
				.attr('div_id', divId);
		if (tbMdl.InlineEdit)
			AddBtn.click(this.AddBtnClickInline);
		else
			AddBtn.click(this.AddBtnClick);
		for(var i=tbMdl.Data.length; i<tbMdl.MinRowCount; i++)
			AddBtn.click();
	}
	if(tbMdl.Data.length > 0 || tbMdl.MinRowCount > 0)
		div.find('.no-record').hide();
	for(var i=0; i<tbMdl.ExtraButtons.length; i++)
		$(tbMdl.ExtraButtons[i])
			.css('margin-top', '10px')
			.css('margin-left', '5px')
			.appendTo(containerDiv);
	Jui.InitTables(divId);
	Jui.InitButtons(divId);
	$('.JsTable-Content').hide();
	JsTable.Filter(divId, Model);
	JsTable.CalcSummaries(divId);
	return div;
};

JsTable.CreateTable = function(tbMdl, containerDiv, Model)
{
	var tbl = $('<table>').appendTo(containerDiv).addClass('jui-table records');
	var tr = $('<tr>').appendTo($('<thead>').appendTo(tbl));
	var fields = tbMdl.Fields;
	tbMdl.FldCnt = 0;
	tbMdl.SumFields = {};
	if(tbMdl.RowNumbers)
	{
		$('<td>').html('No').css({align: 'center', width: '2em'}).appendTo(tr);
		tbMdl.FldCnt++;
	}
	for (var f in fields)
	{
		var fld = fields[f];
		if(fld.G)
			continue;
		tbMdl.FldCnt++;
		var td = $('<td>').attr('field', f).html(fld.D).appendTo(tr);
		if (fld.SUM)
			tbMdl.SumFields[f] = fld.SUM;
		if (fld.W != '')
			td.css('width', fld.W);
		else if (fld.T == 'date')
			td.css('width', '10em');
		if(fld.V == 0)
			td.css('display','none');
		if (fld.F)
			$('<span>').addClass('spn-filter')
			.html('<button class="jui-button" icon="ui-icon-triangle-1-s">&nbsp;').appendTo(td);
	}
	tr.find('.spn-filter').click(function(){
		var td = $(this).closest('TD').css('position', 'relative');
		var fName = td.attr('field');
		var rows = $(this).closest('TABLE').find('TBODY td[field="' + fName + '"]');
		var sel = $('<select size=2>').appendTo(td).focus().click()
				.on('blur', function(){$(this).remove();});
		$('<option value="|All|">').html('Tümü').appendTo(sel);
		var List = [];
		for (var i=0; i<rows.length; i++)
		{
			var text = rows[i].innerHTML;
			if ($.inArray(text, List) < 0)
			{
				List.push(text);
				$('<option>').html(text).appendTo(sel);
			}
		}
		if(List.length > 10)
			sel.attr('size', 10);
		else
		{
			sel.attr('size', List.length + 1);
			sel.css('overflow', 'hidden');
		}
		sel.click(function(){
			var val = $(this).val();
			var tbl = $(this).closest('TABLE');
			var head = $(this).closest('TD').attr('filter', val);
			tbl.children('TBODY').children('tr').show();
			$(this).remove();
			var cls = 'ui-state-highlight';
			Model.Filter[head.attr('field')] = val;
			if (val != '|All|')
				$(head).find('.jui-button').addClass(cls);
			else
				$(head).find('.jui-button').removeClass(cls);
			JsTable.ApplyFilter(tbl);
		});
		sel.css({
			minWidth: "70px",
			position: "absolute",
			top: "2px",
			right: "2px"
		}).show();
	});
	if(tbMdl.Deletable)
	{
		$('<td>').html('Sil').css('width', '30px').appendTo(tr);
		tbMdl.FldCnt++;
	}
	if(tbMdl.ExtraRowButtons.length > 0)
	{
		$('<td colspan="'+tbMdl.ExtraRowButtons.length+'">').html('İşlemler').css('width', '50px').appendTo(tr);
		tbMdl.FldCnt++;
	}
	var bdy = $('<tbody>').appendTo(tbl);
	if(tbMdl.Sortable)
		bdy.attr('var_type', 'sortable');
	var tfoot = $('<tfoot>').appendTo(tbl);
	if (tbMdl.ShowSumRow)
	{
		var sumTr = $('<tr class="tr-summary">').appendTo(tfoot);
		for (f in fields)
		{
			var fld = fields[f];
			if(fld.G)
				continue;
			var inp = $('<input>').attr('var_type', 'money').prop('disabled', true);
			$('<td>').append(inp).attr('field', f).appendTo(sumTr);
		}
		Jui.InitInputs(sumTr);
	}
	$('<tr class="no-record">').appendTo(tfoot).append(
			$('<td align=center>').attr('colspan', tbMdl.FldCnt).html(tbMdl.NoRecordsMessage));
	var div = containerDiv.closest('.has_js_table');
	for(var i=0; i<tbMdl.Data.length; i++)
		JsTable.AddRow(div.attr('id'), tbMdl.Data[i]);
};

JsTable.CreateRowsFromTemplate = function(tbMdl, div)
{
	var noRec = $('<div class="no-record">').html(tbMdl.NoRecordsMessage);
	div.find('.js_table_container').before(noRec);
	if (tbMdl.Data.length > 0)
		noRec.hide();

	for(var i=0; i<tbMdl.Data.length; i++)
		JsTable.AddCustomRow(div, tbMdl, tbMdl.Data[i], -1);
};


JsTable.AddCustomRow = function(div, tbMdl, rowData, index)
{
	div.find('.no-record').hide();

	var template = tbMdl.RowTemplate;
	if (template.charAt(0) != '#')
		template = '#' + template;
	template = $(template).html();

	var dataStr = {};
	for(var f in tbMdl.Fields)
	{
		var fld = tbMdl.Fields[f];
		dataStr[f] = fld.VarType.getText(rowData[f], f, tbMdl.Name, rowData[fld.Txt]);
	}

	var rowDiv = $(Form.ParseTemplate(dataStr, template));
	if (index == -1 || index + 1 > div.find('DIV.data_row').length)
	{
		var dataList = div.find('.data-list');
		if (dataList.length == 0)
			dataList = $('<div class="data-list">').prependTo(div.find('.js_table_container'));
		dataList.append(rowDiv);
		rowDiv.prop('Data', rowData);
	}
	else
	{
		var el = div.find('DIV.data_row').eq(index);
		el.before(rowDiv);
		rowDiv.prop('Data', el.prop('Data'));
		$.extend(rowDiv.get(0).Data, rowData);
		el.remove();
	}
	rowDiv.addClass('data_row');

	for(var i=0; i<tbMdl.RowAttributes.length; i++)
		rowDiv.attr(tbMdl.RowAttributes[i], rowData[tbMdl.RowAttributes[i]]);
	if (tbMdl.Deletable)
	{
		var delBtn = $('<button>')
			.addClass('btn btn-sm btn-danger')
			.html('<i class="fa fa-trash"> Sil')
			.click(JsTable.DelBtnClick);
		if (rowDiv.find('.DelBtn').length > 0)
			delBtn.appendTo(rowDiv.find('.DelBtn'));
		else
			delBtn.css({ margin: '5px'}).appendTo(rowDiv);
	}

	for(var i=0; i<tbMdl.ExtraRowButtons.length; i++)
	{
		var btn = $(tbMdl.ExtraRowButtons[i]);
		btn.css({ margin: '5px'}).appendTo(rowDiv);
	}
	if (tbMdl.DetayForm)
	{
		rowDiv.css('cursor', 'pointer').click(JsTable.AddBtnClick);
		if (rowDiv.find('.EditBtn').length > 0)
			$('<button>')
				.addClass('btn btn-sm btn-info')
				.html('<i class="fa fa-edit"> Düzenle')
				.appendTo(rowDiv.find('.EditBtn'))
				.click(function(evt){
					$(this).closest('.data_row').click();
					evt.stopPropagation();
				});
	}
};

JsTable.CalcSummaries = function(divId)
{
	if(divId[0] != '#')
		divId = '#' + divId;
	var div = $(divId);
	var tbMdl = $(divId).get(0).Model;
	var Summaries = {};
	var Data = JsTable.GetList(divId, false);
	for (var s in tbMdl.SumFields)
	{
		var stur = tbMdl.SumFields[s];
		if (stur == 'count')
		{
			Summaries[s] = Data.length;
			continue;
		}
		var total = 0;
		for (var i=0; i<Data.length; i++)
		{
			var val = Data[i][s];
			if (! isNaN(val) && val)
				total += parseFloat(val);
		}
		var inp = div.find('tfoot td[field="' + s + '"] input');
		if (stur == 'sum')
			inp.value(total);
	}
}

JsTable.ApplyFilter = function(tbl)
{
	$(tbl).find('THEAD TD[filter]').each(function(){
		var val = $(this).attr('filter');
		var fName = $(this).attr('field');
		if (val == '|All|')
			return;
		$(this).find('.jui-button').addClass('ui-state-highlight');
		tbl.find('TBODY TD[field="' + fName + '"]:not(:contains(' + val + '))').closest('TR').hide();
	});
}

JsTable.Filter = function(divId, Model)
{
	if(divId[0] != '#')
		divId = '#' + divId;
	var div = $(divId);
	for (var i in Model.Filter)
		div.find('THEAD td[field="'+ i +'"]').attr('filter', Model.Filter[i]);
	JsTable.ApplyFilter(div.find('TABLE').first());
}

JsTable.UpdateModel = function(divId, NewModel)
{
	if(divId[0] != '#')
		divId = '#' + divId;
	var div = $(divId);
	var Data = JsTable.GetList(divId, false);
	div.find('.js_table_container').html('');
	$('#tpmdiv_' + divId.substr(1)).remove();
	JsTable.Init(divId, NewModel, Data);
};

JsTable.AddRow = function (divId, data, tr){
	if(divId[0] != '#')
		divId = '#' + divId;
	var tbMdl = $(divId).get(0).Model;
	var bdy = $(divId).find('table.records tbody').first();
	if(! tr)
	{
		bdy.parent().find('tr.no-record').hide();
		// Grup satırlarını ekle
		if(! $.isEmptyObject(tbMdl.GroupFields))
		{
			var level = 1;
			for(var f in tbMdl.GroupFields)
			{
				var gkey = (data[f] + '').replace(/"/g, '');
				if(bdy.find('tr.group_row[level="' + level + '"]:contains("' + gkey + '")').length == 0)
				{
					tr = $('<tr>').appendTo(bdy).attr('level', level).addClass('group_row');
					$('<td>').attr('colspan', tbMdl.FldCnt).html(gkey).appendTo(tr);
				}
				level++;
			}
		}

		tr = $('<tr class="data_row">').appendTo(bdy);
		for(var i=0; i<tbMdl.RowAttributes.length; i++)
			tr.attr(tbMdl.RowAttributes[i], data[tbMdl.RowAttributes[i]]);
		if(tbMdl.DetayForm && !tbMdl.InlineEdit)
			tr.css('cursor', 'pointer').click(JsTable.AddBtnClick);
		tr.get(0).Data = data;
	}
	tr = $(tr);

	if(tbMdl.OnClick)
		$(tr).click(tbMdl.OnClick).addClass('clickable_row');
	if(tbMdl.RowNumbers && tr.find('TD.SiraNo').length == 0)
		$('<td class="SiraNo">').html(tr.prop('rowIndex'))
			.css('align', 'center').appendTo(tr);
	for (var f in tbMdl.Fields)
	{
		var fld = tbMdl.Fields[f];
		if(! is_set(data[f]) && fld.IsNumeric())
			data[f] = 0;
		if(fld.G)
			continue;
		var td = $(tr).find('td[field="'+ f +'"]');
		if(td.length == 0)
		{
			td = $('<td>').attr('field', f);
			if(fld.V == 0)
				td.css('display','none');
			td.appendTo(tr);
			if (tbMdl.InlineEdit)
			{
				var inp = JsTable.InitField(fld, f, tr.get(0)).appendTo(td);
				if (is_set(data[f]))
				{
					if (fld.T == 'file')
						SingleFile.UploadComplete($(inp).attr('id'), null, data[f]);
					else if (fld.T == 'file_image')
						SingleImage.UploadComplete($(inp).attr('id'), null, data[f]);
					else
						Form.SetValue(inp, data[f]);
				}
				$(inp).change(function(){
					var row = $(this).closest('TR');
					var obj = Form.GetDataList('jstable_field', row);
					var d = row.get(0).Data;
					$.extend(true, d, obj);
				});
			}
		}
		if (fld.SUM)
			td.attr('sum', fld.SUM);
		if(fld.T == "file" && fld.EXT)
			td.find("[type='file']").attr('allowed_ext', fld.EXT);
		if(is_set(fld.VarType.align))
			td.attr('align', fld.VarType.align);
		if (! tbMdl.InlineEdit)
		{
			var content;
			if (typeof fld.T == 'function')
				content = fld.T(data, f);
			else
				content = fld.VarType.getText(data[f], f, tbMdl.Name, data[fld.Txt]);
			if (typeof content == 'string' || typeof content == "number")
				td.html('' + content);
			else if (content)
			{
				if (typeof content.appendTo == "function")
				{
					td.html('');
					content.appendTo(td);
				}
				if ($.isArray(content) && fld.toString)
				{
					var list = [];
					for(var i=0; i<content.length; i++)
						list.push(fld.toString(content[i]));
					td.html(list.join(fld.Sep));
				}
			}
		}
	}
	if(tbMdl.Deletable && tr.find('.jui-table-del-row').length == 0)
		$('<td>')
			.addClass('jui-table-del-row')
			.html('<i class="fa fa-times">')
			.appendTo(tr).click(this.DelBtnClick);

	if(tr.find('.jui-table-ekstra-buttons-row').length == 0)
		for(var i=0; i<tbMdl.ExtraRowButtons.length; i++)
		{
			var td = $('<td>');
			var btn = $(tbMdl.ExtraRowButtons[i]);
			btn.css({ margin: '5px'}).appendTo(td);
			td
				.addClass('jui-table-ekstra-buttons-row')
				.appendTo(tr);
		}
	if (tbMdl.InlineEdit)
	{
//		alert(tr.find("[allowed_ext]").length);
		$(tr).find('.jstable_field').change();
		Jui.InitInputs(tr);
		Jui.InitButtons(tr);
		if (typeof InitFileUpload != 'undefined')
			InitFileUpload(tr);
		$(tr).find('td[sum]').change(function(){
			JsTable.CalcSummaries(divId);
		});
	}
	else
		$.extend(true, tr.get(0).Data, data);
	return tr;
};

JsTable.DelBtnClick = function (e)
{
	var tr = $(this).parents('.data_row:first');
	var bdy = tr.parent();
	tr.find('td').addClass('ui-state-highlight');
	if(confirm('Kaydı silmek istediğinize emin misiniz?'))
		tr.remove();
	else
		tr.find('td').removeClass('ui-state-highlight');
	if(bdy.find('.data_row').length == 0)
		bdy.parent().find('.no-record').show();
	else
		bdy.find('tr').each(function(){
			$(this).find('TD.SiraNo').html(this.rowIndex);
		});
	e.stopPropagation();
};

JsTable.DeleteAllRow = function (divId)
{
	if(divId[0] != '#')
		divId = '#' + divId;
	var bdy = $(divId).find('TABLE.records tbody');
	bdy.find('TR').remove();
	bdy.parent().find('tr.no-record').show();
};

JsTable.AddBtnClick = function()
{
	var parentId = $(JsTable.getParent(this)).attr('id');
	var tmp = 'tpmdiv_' + parentId;
	$('#' + tmp).remove();
	$('#' + tmp + '_modal').remove();
	var model = $('#' + parentId).get(0).Model;
	var div = JsTable.CreateDetailDiv(parentId, tmp, model);
	div.get(0).Ref = this.Data && $(this).hasClass('data_row') ? this : null;
	this.Data = {  };
	if (typeof model.OnBeforeShow == "function")
		this.Data = model.OnBeforeShow(this.Data, div);
	for (var f in model.Fields)
	{
		var inp = $(div).find('[field="' + f + '"]');
		var field = model.Fields[f];
		var val = this.Data ? this.Data[f] : '';
		if(typeof field.T == 'function' && field.R)
			$(inp).closest('tr').hide();
		if (inp.hasClass('inp_picker'))
		{
			inp.find('.id_field').val(val);
			var txtField = inp.attr('text_field');
			inp.find('.text_field').val( this.Data ? this.Data[txtField] : '');
		}
		else if(field.R)
			inp.html('' + field.VarType.getText(val))
				.css('text-align', field.VarType.align);
		else if(field.VarType.setValue)
			field.VarType.setValue(val);
		else if (field.H)
			inp.val(String.ReverseQuoteEntities(val));
		else
			Form.SetValue(inp, val);
		var altContent = $('.JsTable-Content[target="' + f + '"]');
		if (altContent.length > 0)
			inp.closest('[field]').html(altContent.html());
	}
	$(this).closest('tbody').find('td').removeClass('ui-state-highlight');
	$(this).find('td').addClass('ui-state-highlight');

	var tmpDiv = $('#' + tmp);
	var modalFnc = 'ShowDialogBS';
	if (typeof $.fn.modal != 'function')
		modalFnc = 'ShowDialog';
	var modal = Page[modalFnc](tmp, model.Dialog.W, model.Dialog.H, function(){
		return JsTable.Save(tmpDiv);
	});
	modal.find('.modal-title').html(model.Dialog.C);

	if (typeof model.OnShow == "function")
		model.OnShow(this.Data ? this.Data : null, div);
	Jui.InitInputs(tmpDiv);
	tmpDiv.find('INPUT,SELECT').first().focus();
};

JsTable.AddBtnClickInline = function()
{
	var parentId = $(JsTable.getParent(this)).attr('id');
	var model = $('#' + parentId).get(0).Model;
	this.Data = {  };
	if (typeof model.OnBeforeShow == "function")
		this.Data = model.OnBeforeShow(this.Data, $(tr));
	var tr = JsTable.AddRow(parentId, this.Data);
	for (var f in model.Fields)
	{
		var inp = $(tr).find('[field="' + f + '"]');
		var field = model.Fields[f];
		var val = this.Data ? this.Data[f] : '';
		if(field.R)
			inp.html('' + field.VarType.getText(val))
				.css('text-align', field.VarType.align);
		else if(field.VarType.setValue)
			field.VarType.setValue(val);
		else
			inp.val(val);
	}
};

JsTable.InitField = function(fld, name, div){
	fld.Div = div;
	fld.Name = name;
	var inp = fld.VarType.getInput(name, fld).attr('field', name).addClass('jstable_field');
	inp.get(0).Fld = fld;
	if (typeof fld.OnChange == "function" && ! fld.R)
	{
		inp.get(0).fld = fld;
		inp.change(function(){
			var val = Form.GetValue(this);
			this.fld.OnChange(val);
		});
	}
	if(fld.Attr != null)
		$.each(fld.Attr,function (index, value) {
			inp.attr(index,value);
		});
	if(fld.W != '')
		inp.css('width', fld.W);
	else if(fld.T == 'date')
		inp.css('width', '10em');
	else
		inp.css('width', '99%');
	if (fld.M && !inp.hasClass('hasMask') && $.mask)
		inp.mask(fld.M).addClass('hasMask');
	if (fld.T && fld.P && fld.T.toLowerCase() == 'inppicker')
	{
		var func = window['ReturnFunc_' + fld.Name];
		if (typeof func == "undefined")
		window['ReturnFunc_' + fld.Name] = function(obj){
			o = obj;
			_CurrentDiv.find('.id_field').val(obj.Id);
			_CurrentDiv.find('.text_field').val(obj.Text);
		};
		inp.find('BUTTON').click(function(){
			var div = $(this).closest('DIV');
			var fld = div.get(0).Fld;
			if (typeof fld.P == "function")
				return window[fld.P](div);
			var params = {T:1, ReturnFunc: 'ReturnFunc_' + fld.Name};
			for(var ex in fld.PE)
				params[ex] = fld.PE[ex];
			Page.Open(fld.P, params);
			_CurrentDiv = div;
		});
	}
	if (fld.AC && (typeof $.isArray(window[fld.AC])) || typeof window[fld.AC] == "function"){
		var source = window[fld.AC];
		if (typeof source == "function")
			source =  function( request, response ) {
				window[fld.AC]( this.element, request.term, response);
			};
		inp.autocomplete({
			source: source,
			next : fld.ACL,
			minLength : 0,
			open: function(event, ui) {
				$(this).autocomplete("widget").css({
					"minWidth": ($(this).width() + "px")
				});
			},
			change : function(event, ui){
				if (! ui.item)
				{
					var fName = $(this).autocomplete('option', 'next');
					if (fName)
					{
						var td = $(this).closest('TR').find('TD[field="' + fName + '"]');
						if (td.find('INPUT').length > 0)
							td.find('INPUT').val('');
						else
							td.html('');
					}
				}
				$(this).change();
			},
			select: function (event, ui){
				var obj = ui.item;
				if (typeof obj.label == "undefined")
					return true;
				var fName = $(this).autocomplete('option', 'next');
				if (fName)
				{
					var td = $(this).closest('TR').find('TD[field="' + fName + '"]');
					if (td.find('INPUT').length > 0)
						td.find('INPUT').val(obj.label);
					else
						td.html(obj.label);
				}
				$(this).change();
			}
		})
		.autocomplete("option", "appendTo", 'BODY')
		.focus(function(){
			$(this).autocomplete('search', $(this).val());
		});
	}
	return inp;
};

JsTable.CreateDetailDiv = function(parentId, tmp, model)
{
	var fields = model.Fields;
	var dialog = model.Dialog;
	var div = $('<div title="' + dialog.C + '">').attr('id', tmp)
			.attr('parent_id', parentId).appendTo('body');
	div.get(0).Model = model;
	if (dialog.E)
		$('<div class="ui-state-highlight ui-corner-all">')
			.html(dialog.E)
			.css({padding: '10px', fontWeight: 'bold', marginBottom: '10px'})
			.appendTo(div);
	var tbl = $('<div class="form-horizontal">').appendTo(div);
	for (var f in fields)
	{
		var fld = fields[f];
		var inp = JsTable.InitField(fld, f, div);
		inp.addClass('form-control').attr('placeholder', fld.D);
		var cls = fld.R ? 'jstable_field' : '';
		var cap = fld.D;
		if(fld.RQ)
			cap += '<span class="required_field"> * </span>';
		if (fld.CS != 2)
		{
			var tr = $('<div class="form-group">').appendTo(tbl);
			if(fld.V == 0)
				tr.css('display','none');
			var td1 = $('<label class="col-sm-3 control-label">')
					.attr('for', f)
					.html(cap + ' :');
			var td2 = $('<div class="col-sm-9 input-container ' + cls + '">').html(inp);
			td1.appendTo(tr);
			td2.appendTo(tr);
		}
		else
		{
			var tr1 = $('<div class="form-group">').appendTo(tbl);
			var td1 = $('<label class="col-sm-12 control-label">')
					.attr('for', f)
					.css('text-align', 'left')
					.html(cap + ' :');
			var td2 = $('<div class="col-sm-12 input-container">').html(inp);
			td1.appendTo(tr1);
			td2.appendTo(tr1);
		}
		if(fld.R)
			td2.attr('field', f).css('white-space', 'normal');
		if(fld.E == 0)
			td2.find('INPUT,BUTTON,SELECT').attr('disabled', 'disabled').css('background', 'lightgray');
		if(fld.EXT != 0)
			td2.find('INPUT,BUTTON').attr('allowed_ext', fld.EXT);
		if(fld.MSZ != 0)
			td2.find('INPUT,BUTTON').attr('max_file_size', fld.MSZ);
		if(fld.MRes != 0)
			td2.find('INPUT,BUTTON').attr('max_resolution', fld.MRes);
	}
	InitFileUpload();
	$('body').trigger('ers_evnt_jstable_on_created_div', [div]);
	return div;
};

JsTable.Save = function(div){
	var model = div.get(0).Model;
	var tr = div.get(0).Ref;
	var parentId = div.attr('parent_id');
	var divId = '#' + div.attr('id');
	var obj = Form.GetDataList('jstable_field', divId);
	for(var f in model.Fields)
	{
		var field = model.Fields[f];
		if (field.R && typeof tr.Data != "undefined")
		{
			obj[f] = tr.Data[f];
		}
		if(field.RQ && obj[f] == '' )
			return Page.ShowWarning('<b>"' + field.D + '"</b> alanını doldurunuz'
					, function(){ HighlightField($(divId).find('[field="' + f + '"]')); });
	}
	if (typeof model.OnValidate == 'function')
	{
		var retVal = model.OnValidate(obj, tr);
		if (retVal !== 1 && retVal !== true)
			return Page.ShowWarning(retVal);
	}
	if (model.RowTemplate == null)
		tr = JsTable.AddRow(parentId, obj, tr);
	else
		tr = JsTable.AddCustomRow($('#' + parentId), model, obj, $(tr).index());
	Jui.InitButtons('#' + parentId);
	Jui.InitTables('#' + parentId);
	if(typeof model.OnUpdate == 'function')
		model.OnUpdate(tr, $(divId));
	return 1;
};

JsTable.GetList = function(divId, showErrors)
{
	if (typeof showErrors == "undefined")
		showErrors = true;
	if(divId[0] != '#')
		divId = '#' + divId;
	var div = $(divId);
	var model = div.get(0).Model;
	var sonuc = [];
	var error = 0;
	var selector = 'table.records>tbody>tr.data_row';
	if (model && model.RowTemplate != null)
		selector = 'div.data_row';
	$(divId).find(selector).each(function(){
		if (error)
			return;
		var obj = this.Data;
		var row = this;
		if (!obj)
			return;
		sonuc.push(obj);
		if (! model.InlineEdit)
			return;
		for(var f in model.Fields)
		{
			var field = model.Fields[f];
			if(field.RQ && obj[f] == '' && showErrors)
			{
				Page.ShowWarning(row.rowIndex + ' numaralı kaydın <b>"' + field.D + '"</b> alanını doldurunuz'
						, function(){
							HighlightField($(row).find('[field="' + f + '"]'));
						});
				error = 1;
				break;
			}
		}

	});
	if (error)
		return null;
	return sonuc;
};

function ShowSingleFile(target, event)
{
	var url = $(target).attr('yol');
	Page.OpenNewWindow(url,'Dosya',500,400);
	event.stopPropagation();
}
