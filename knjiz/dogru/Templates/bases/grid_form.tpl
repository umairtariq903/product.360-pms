{AddJs("dogru/Templates/bases/arama_form.js")}
<style>
	table.arama td{
		white-space: nowrap;
		padding: 0px 5px;
	}
	.tbl-query .td_input_data > INPUT,
	.tbl-query .td_input_data SELECT {
		width: 100%;
	}
	span.kriter {
		display: inline-block;
		float:left;
		padding: 3px;
	}
	span.kriter span{
		display: table-cell;
		padding: 2px;
		margin: 0px;
		white-space: nowrap;
	}
</style>
<div id="advanced" style="display: none">
{block "advanced"}
	{foreach item=c key=k from=$Columns}
		{$type="text"}
		{if $c.Type eq 3}
			{$type="date"}
		{/if}
		<kriter type="{$type}" name="{$k}" label="{$c.Display}"></kriter>
	{/foreach}
{/block}
</div>
<div id="advanced_query" class="table-responsive" title="Advanced Search Criteria" style="font-size: 0.8em">
	<table width="100%" class="tbl-query tb_input_base">
		<col width="25%">
		<col width="75%">
	</table>
</div>
<table class="arama" style="width: 100%" use_default_button="1">
	<tr>
		<td width="20">Search :</td>
		<td>
			<input name="sorgu" label="içinde %s geçen"	style="width: 100%"
				   title="Aradığınız kelimeyi giriniz"/><br>
		</td>
		<td width="20" class="buttons">
		</td>
	</tr>
</table>
<div id="kriter_sablon" style="display: none;">
	<span class="kriter">
	<div style="ui-widget">
		<div name="" class="ui-state-highlight ui-corner-all"  style="cursor: pointer">
			<span class="label" style="font-weight: bold; color: blue; font-size: 1.1em"></span>
			<span class="value" style="color:darkred"></span>
			<span class="close">
			<button class="jui-button" toolbar="1" icon="ui-icon-close" >Kapat</button>
			</span>
		</div>
	</div>
	</span>
</div>
<div id="kriterler_dialog"
	 style="display: none; position: relative; margin:10px 0px; padding: 5px; font-size: 0.9em">
</div>
<div id="kriterler" title="Search Criteria" style="font-size: 0.9em">
</div>
{block "grid_table"}
	<table width="100%" class="Grid" cellspacing="0" cellpadding=3 style="border-collapse:collapse">
    <thead>
        <tr>
			{$i=0}
			{foreach item=c key=k from=$Columns}
				<td align="{if $c.Type eq 1}right{elseif $c.Type neq 2}center{/if}">
					{$c.Display}
				</td>
			{/foreach}
        </tr>
    </thead>
	</table>
{/block}
<script>
var Columns = JSON.parse('{addslashes(Kodlama::JSON($Columns))}');
</script>
{block "grid_js"}
<script>
var colCount = $('.Grid THEAD TD').length;
var hidden = [];
for(var i=5; i<colCount;i++)
	hidden.push(i);
var dataTable = $('.Grid').dataTable({
	"sDom": '<"H"lp>trC<"F"ip>',
	"bJQueryUI": true,
	"oLanguage": {
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
	},
	"bProcessing": true,
	"sPaginationType": "full_numbers",
    "sAjaxSource": Page.GetCurrentUrl() + '&grid=1',
	"sServerMethod": "POST",
	"bServerSide": true,
	"fnServerParams": function ( aoData ) {
		aoData.push( queryOptions );
	},
	"sScrollY": "400px",
	"bScrollCollapse": true,
	"bScrollAutoCss": false,
	"aoColumnDefs": [
		{ "bVisible": false, "aTargets": hidden }
	],
	"oColVis": {
		"buttonText": "&nbsp;",
		"bRestore": true,
		"sAlign": "right"
	},
	"fnDrawCallback": function (o) {
		/* Position the ColVis button as needed */
		var nColVis = $('div.ColVis', o.nTableWrapper)[0];
		nColVis.style.width = o.oScroll.iBarWidth+"px";
		nColVis.style.top = ($('div.dataTables_scroll', o.nTableWrapper).position().top)+"px";
		nColVis.style.height = ($('div.dataTables_scrollHead table', o.nTableWrapper).height())+"px";
	}
});
</script>
{/block}
