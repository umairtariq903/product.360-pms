<style>
	#rapor_sonuc .data_row:hover{
		background-color: #c0dcfd;
	}

	#rapor_sonuc .fa{
		margin-right: 5px;
		color: orange;
	}

	#rapor_sonuc .fa:hover{
		color: green;
	}

	.page-content .fa-search:before{
		line-height: 34px;
	}
</style>
<div class="pull-right">
	<div class="form-group has-feedback">
		<input id="search_input" type="text" class="form-control normal-input" placeholder="Search..." />
		<i class="fa fa-search form-control-feedback"></i>
	</div>
</div>
<div class="pull-left liste-tur">
	<ul class="nav nav-pills">
		<li role="presentation" class="active">
			<a href="#" tur="1" data-toggle="tab"><i class="fa fa-star text-yellow"></i> Favorilerim</a>
		</li>
		<li role="presentation">
			<a href="#" tur="0" data-toggle="tab"><i class="fa fa-star-o text-yellow"></i> Diğerleri</a>
		</li>
	</ul>
</div>
<table id="rapor_sonuc" class="table table-striped table-hover table-bordered">
	{foreach item=grup from=$SorguList}
		<tr class="kategori" kat="{$grup->kod}">
			<td class="td_sonuc_head ">{$grup->desc}</td>
		</tr>
		{foreach item=rapor from=$grup->List}
			<tr class="data_row" tur="{$rapor->tur}"
				{if count($rapor->Params) eq 0}query="1"{/if}
				kat="{$grup->kod}"
				kod="{$rapor->kod}">
				<td>
					<i class="fa fa-star{Bool($rapor->tur eq '0','-o', '')}"></i>
					{$rapor->desc}
				</td>
			</tr>
		{/foreach}
	{/foreach}
</table>
<script>
	function RaporDetay()
	{
		var row = $(this);
		var kat = row.attr('kat');
		var kod = row.attr('kod');
		var qry = row.attr('query') ? '&query=1' : '';
		if (typeof RaporDetayWin != "undefined")
			RaporDetayWin.close();
		var u = GetUrl('#rapor_detay', 'clear') + '&kat=' + kat + '&kod=' + kod + qry;
		RaporDetayWin = Page.OpenNewWindow(u, 'rapor_detay', 720, 500);
		RaporDetayWin.blur();
		RaporDetayWin.focus();
	}

	function Search()
	{
		var val = $('#search_input').val();
		var fav = $('.liste-tur li.active>a').attr('tur');
		$('#rapor_sonuc tr').show();
		$('#rapor_sonuc tr.data_row[tur!="'+fav+'"]').hide();
		if (val != '')
		{
			$('#rapor_sonuc tr').hide();
			$("#rapor_sonuc .data_row[tur='"+fav+"']:Contains('" + val + "')")
					.show()
					.each(function () {
						$('TR.kategori[kat="' + this.getAttribute('kat') + '"]').show();
					});
		}
	}
	jQuery.expr[':'].Contains = function (a, i, m) {
		return jQuery(a).text().toUpperCase()
				.indexOf(m[3].toUpperCase()) >= 0;
	};

	$('#rapor_sonuc .data_row').click(RaporDetay);
	$('#search_input').bind('keyup', Search);
	Search();

	$('#rapor_sonuc .data_row').popover({
		selector: ".fa",
		trigger: "hover",
		placement: "auto right",
		title: 'Favorilerim',
		content: 'Favorilerime Ekle / Çıkart'
	});

	$('.liste-tur a[data-toggle="tab"]').on('shown.bs.tab', Search);

	$('#rapor_sonuc .data_row .fa').click(function(e){
		var tr = $(this).closest('tr');
		var tur = 1 - tr.attr('tur');
		tr.attr('tur', tur);
		$(this).toggleClass('fa-star', tur);
		$(this).toggleClass('fa-star-o', !tur);

		var obj ={ fav: tr.attr('kat') + '|' + tr.attr('kod'), isAdd: tur};
		Page.Ajax.Send('ChangeSqlViewFavorite', obj, Page.Ajax.DO_NOTHING, '');
		e.stopPropagation();
	});
</script>
