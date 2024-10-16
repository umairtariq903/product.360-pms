<div style="text-align: right">
	Search : <input type="text" class="input_data" id="search_input">
</div>
<table id="rapor_sonuc" class="sonuc">
	{foreach item=grup from=$SorguList}
		<tr class="td_sonuc_head kategori" kat="{$grup->kod}">
			<td>{$grup->desc}</td>
		</tr>
		{foreach item=rapor from=$grup->List}
			<tr class="{cycle values='td_sonuc_row,td_sonuc_row2'} data_row"
			    {if count($rapor->Params) eq 0}query="1"{/if}
			    kat="{$grup->kod}"
			    kod="{$rapor->kod}">
				<td>
					&nbsp;&nbsp;{$rapor->desc}
				</td>
			</tr>
		{/foreach}
	{/foreach}
</table>
<script>
	function RaporDetay(obj)
	{
		var row = $(obj);
		var kat = row.attr('kat');
		var kod = row.attr('kod');
		var qry = row.attr('query') ? '&query=1' : '';
		if (typeof RaporDetayWin != "undefined")
			RaporDetayWin.close();
		var u = GetUrl('#rapor_detay','clear') + '&kat=' + kat + '&kod=' + kod + qry;
		RaporDetayWin = Page.OpenNewWindow(u, 'rapor_detay', 720, 500);
		RaporDetayWin.blur();
		RaporDetayWin.focus();
	}

	function Search(obj)
	{
		$('#rapor_sonuc tr').show();
		if(obj.value != '')
		{
			$('#rapor_sonuc tr').hide();
			$("#rapor_sonuc .data_row:Contains('" + obj.value + "')")
					.show()
					.each(function(){
						$('TR.kategori[kat="' + this.getAttribute('kat') + '"]').show();
					});
		}
	}
	jQuery.expr[':'].Contains = function(a, i, m) {
		return jQuery(a).text().toUpperCase()
				.indexOf(m[3].toUpperCase()) >= 0;
	};

	$('#rapor_sonuc .data_row').click(function(){
		RaporDetay(this);
	});
	$('#search_input').bind('keyup',
			function (){ Search(this);}
	);
</script>
