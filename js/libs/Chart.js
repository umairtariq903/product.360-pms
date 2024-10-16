/**
// Bar için
	chart = new Chart();
	chart.SetType('bar');
	chart.GetColDataSeriesFromTable('#TBL_Genel .proje_detay', [1, 3], ['Tamam', 'Devam']);
	chart.DivId = 'chart_adet';
	chart.Draw();

// Line için
	chart = new Chart();
	chart.SetType('line');
	chart.GetRowDataSeriesFromTable('#TBL_BesYil1 .proje_detay');
	chart.DivId = 'chart_bes_yil';
	chart.Draw();

	*/
function Chart(title)
{
	this.DivId = '';
	this.Title = title;
	this.Data = [];
	this.Labels = [];
	this.XAxisLabel = '';
	this.YAxisLabel = '';
	this.Type = $.jqplot.BarRenderer;
	this.TypeStr = 'bar';
	this.Stacked = true;

	$.jqplot.config.enablePlugins = true;
	this.Options = {
		seriesColors: [ "#4bb2c5", "#c5b47f", "#EAA228", "brown", "lightgreen", "#579575",
			"gray", "#839557", "#958c12",
			"#953579", "#4b5de4", "#d8b83f", "#ff5800", "#0085cc"],  // colors that will
		// be assigned to the series.  If there are more series than colors, colors
		// will wrap around and start at the beginning again.

		highlighter: {
			showMarker:false,
			show: true,
			formatString:'%s, %s',
			useAxesFormatters: false

		},
		animate: !$.jqplot.use_excanvas,
		stackSeries: this.Stacked,
		title: title,
		legend: {
			show: true,
			rendererOptions: {
			}
		},
		seriesDefaults:{
			pointLabels: { show: true },
			showMarker: false,
			rendererOptions: {
				padding: 8,
				showDataLabels: true,
				animation: { speed: 1500 }
			}
		},
		axes:{
			xaxis: {  },
			yaxis: { min: 0 }
		},
		axesDefaults: {	 labelRenderer: $.jqplot.CanvasAxisLabelRenderer }
	};
}

Chart.prototype.Draw = function()
{
	var selector = '#' + this.DivId;
	$(selector).html('');
	if (this.TypeStr.match(/line/i))
	{
		var ticks = this.Options.axes.xaxis.ticks;
		if (typeof ticks[0] != "object")
		{
			for(var i=0; i<ticks.length; i++)
				ticks[i] = [i + 1, ticks[i]];
		}
		this.Options.axes.xaxis.ticks = ticks;
	}
	this.Options.title = this.Title;
	this.Options.stackSeries = this.Stacked;
	this.Options.axes.xaxis.label = this.XAxisLabel;
	this.Options.axes.yaxis.label = this.YAxisLabel;

	//Label ları datanın içne ekle, highlight için
	var w = this.Data.length ? this.Data.length : 0,
		h = this.Data[0] instanceof Array ? this.Data[0].length : 0,
		legend = this.Options.legend.labels;

	if(! this.TypeStr.match(/pie/i))
		legend = this.Options.axes.xaxis.ticks;
	if(this.TypeStr.match(/pie/i) && h > 0 && w > 0){
		for(var i=0; i<w; i++)
			for(var j=0; j<h; j++)
				this.Data[i][j] = [legend[j], this.Data[i][j]];
	}
	var plot = $.jqplot(this.DivId, this.Data, this.Options);

	if (! this.TypeStr.match(/pie/i))
	{
		var ClearClass = function() {
			$(selector + ' tr.jqplot-table-legend').removeClass('legend-row-highlighted');
			$(selector + ' tr.jqplot-table-legend').children('.jqplot-table-legend-label').removeClass('legend-text-highlighted');
		};
		$(selector).bind('jqplotDataHighlight', function(ev, seriesIndex, pointIndex, data) {
			var idx = $(selector + ' tr.jqplot-table-legend').length - seriesIndex - 1;
			ClearClass();
			$(selector + ' tr.jqplot-table-legend').eq(idx).addClass('legend-row-highlighted');
			$(selector + ' tr.jqplot-table-legend').eq(idx).children('.jqplot-table-legend-label').addClass('legend-text-highlighted');
		});
		$(selector).bind('jqplotDataUnhighlight', ClearClass);
	}
	$(selector).each(function(){
		var outerDiv = $(document.createElement('div'));
		var header = $(document.createElement('div'));
		var div = $(document.createElement('div'));

		outerDiv.append(header);
		outerDiv.append(div);

		outerDiv.addClass('jqplot-image-container');
		header.addClass('jqplot-image-container-header');
		div.addClass('jqplot-image-container-content');

		header.html('Resmi kaydetmek için sağ tıklayın...');

		$(this).after(outerDiv);
		outerDiv.hide();

		outerDiv = header = div = close = null;

		if (!$.jqplot._noToImageButton) {
			var btn = $(document.createElement('button'));
			btn.text('Grafiği resim olarak göster');
			btn.addClass('jqplot-image-button');
			btn.addClass('non-printable');
			btn.bind('click', { chart: $(this)}, function(evt) {
				var imgelem = evt.data.chart.jqplotToImageElem();
				var div = $(this).nextAll('div.jqplot-image-container').first();
				div.children('div.jqplot-image-container-content').empty();
				div.children('div.jqplot-image-container-content').append(imgelem);
				if(! div.is(':visible')){
					$(this).text('Grafiği gizle');
					$(this).addClass('jqplot-image-button-selected');
					div.show(500);
				}else{
					$(this).text('Grafiği resim olarak göster');
					$(this).removeClass('jqplot-image-button-selected');
					div.hide(500);
				}
				div = null;
			});

			$(this).after(btn);
			btn = null;
		}
	});
}

Chart.prototype.SetType = function(type){
	// Options
	this.TypeStr = type;
	if (type.match(/pie/i))
		this.Type = $.jqplot.PieRenderer;
	else if (type.match(/line/i))
		this.Type = $.jqplot.LineRenderer;
	else
		this.Type = $.jqplot.BarRenderer;

	this.Options.seriesDefaults.renderer = this.Type;
	this.Options.legend.placement = 'outsideGrid';
	if (this.TypeStr.match(/pie/i))
	{
		this.Options.seriesDefaults.rendererOptions.showDataLabels = true;
	}
	else if(this.TypeStr.match(/line/i))
	{
		this.Options.seriesDefaults.fill = this.Stacked;
		this.Options.seriesDefaults.pointLabels.show = false;
	}
	else if (this.TypeStr.match(/bar/i))
		this.Options.axes.xaxis.renderer = $.jqplot.CategoryAxisRenderer;

	return this;
}

Chart.prototype.GetRowDataArray = function(row, scale, colStartIndex, numericVals){
	colStartIndex = (typeof colStartIndex == "undefined") ? 0 : colStartIndex;
	numericVals = (typeof numericVals == "undefined") ? true : numericVals;
	var a = [];
	row = $(row).get(0);
	for(var i=colStartIndex; i< row.cells.length; i++)
	{
		var val = row.cells[i].innerHTML;
		if (numericVals)
		{
			val = val.replace(/[^0-9.]/g, '');
			val = (val == '' ? 0 : parseInt(val) * scale);
		}
		a.push(val);
	}
	return a;
}

Chart.prototype.GetColDataArray = function(rowSelector, colIndex, scale, rowStartIndex, numericVals){
	rowStartIndex = (typeof rowStartIndex == "undefined") ? 0 : rowStartIndex;
	numericVals = (typeof numericVals == "undefined") ? true : numericVals;
	var a = [];
	var rows = $(rowSelector);
	for(var i=0; i<rows.length; i++)
	{
		var val = jQuery.trim(rows.get(i).cells[colIndex].innerHTML);
		if (numericVals)
		{
			val = val.replace(/[^0-9.]/g, '');
			val = (val == '' ? 0 : parseInt(val) * scale);
		}
		a.push(val);
	}
	return a;
}

Chart.prototype.GetColDataSeriesFromTable = function(rowSelector, columns, labels, scale)
{
	var tbl = $(rowSelector).first().parents('TABLE').first().get(0);
	scale = (typeof scale == "undefined") ? 1 : scale;
	if (typeof columns == "undefined")
	{
		columns = [];
		var colNum = 0;
		for(var i=0; i<tbl.rows.length;i++)
			if (colNum < tbl.rows[i].cells.length)
				colNum = tbl.rows[i].cells.length;
		for(var i=1; i<colNum; i++)
			columns.push(i);
	}

	this.Data = [];
	for(var k=0; k<columns.length; k++)
		this.Data.push(this.GetColDataArray(rowSelector, columns[k], scale));

	var ticks = [];
	var rows = $(rowSelector);
	for(var i=0; i<rows.length; i++)
	{

		var tick = '';
		if(rows.eq(i).attr('noTrim') == 1)
		{
			tick = rows.get(i).cells[0].innerHTML;
			ticks.push(tick);
		}
		else
		{
			tick = jQuery.trim(rows.get(i).cells[0].innerHTML).split(' ');
			ticks.push(tick[0]);
		}
	}

	if (this.TypeStr.match(/pie/i)){
		this.Options.axes.xaxis.ticks = labels;
		this.Options.legend.labels = ticks;
	}else{
		this.Options.axes.xaxis.ticks = ticks;
		this.Options.legend.labels = labels;
	}
}

Chart.prototype.GetRowDataSeriesFromTable = function(rowSelector, scale)
{
	var rows = $(rowSelector);
	scale = (typeof scale == "undefined") ? 1 : scale;
	this.Data = [];
	var ticks = [];
	var labels = [];
	for(var i=0; i<rows.length; i++)
	{
		var row = rows.get(i);
		if (i == 0)
		{
			var tbl = $(row).parents('TABLE').get(0);
			if (row.rowIndex > 0)
			{
				var header = this.GetRowDataArray(tbl.rows[row.rowIndex-1], scale, 0, false);
				if (header.length == row.cells.length )
					header.splice(1);
				ticks=header;
			}
		}
		var series = this.GetRowDataArray(row, scale, 1);
		this.Data.push(series);

		var label = jQuery.trim(row.cells[0].innerHTML.split(' ')[0]);
		labels.push(label);
	}// for i

	if (this.TypeStr.match(/line/i))
	{
		this.Data.reverse();
		labels.reverse();
	}

	if (this.TypeStr.match(/pie/i)){
		this.Options.legend.labels = ticks;
		this.Options.axes.xaxis.ticks = labels;
	}else{
		this.Options.legend.labels = labels;
		this.Options.axes.xaxis.ticks = ticks;
	}
	return this;
}
Chart.prototype.Transpose = function()
{
	this.Data = TransposeMatrix(this.Data);
	var a = this.Options.axes.xaxis.ticks.slice();
	this.Options.axes.xaxis.ticks = this.Options.legend.labels.slice();
	this.Options.legend.labels = a.slice();
	return this;
}
Chart.prototype.Sum = function()
{
	this.Data = SumMatrix(this.Data);
	if(! this.TypeStr.match(/pie/i))
		this.Options.legend.labels = ['Toplam'];
	return this;
}
function SumMatrix(a)
{
	// Calculate the width and height of the Array
	var w = a.length ? a.length : 0,
		h = a[0] instanceof Array ? a[0].length : 0;

	if(h === 0 || w === 0)
		return [];
	var i, j, t = [];
	t[0] = [];
	for(i=0; i<h; i++) {
		t[0][i] = 0;
		for(j=0; j<w; j++)
			t[0][i] += a[j][i];
	}

	return t;
}
function TransposeMatrix(a)
{
	// Calculate the width and height of the Array
	var w = a.length ? a.length : 0,
		h = a[0] instanceof Array ? a[0].length : 0;

	if(h === 0 || w === 0)
		return [];
	var i, j, t = [];
	for(i=0; i<h; i++) {
		t[i] = [];
		for(j=0; j<w; j++)
			t[i][j] = a[j][i];
	}

	return t;
}
