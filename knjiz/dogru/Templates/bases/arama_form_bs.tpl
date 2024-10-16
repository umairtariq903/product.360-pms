<style>
	table.arama td{
		white-space: nowrap;
		padding: 0px 5px;
	}
	.tbl-query .td_input_data INPUT,
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

	TD.cr-pin {
		text-align: center;
		color: lightgrey;
		cursor: pointer;
	}

	TD.cr-pin:hover {
		color: blue;
	}

	TD.cr-pin.pinned {
		color : blue;
	}

	TD.cr-pin.pinned .fa{
		transform: rotate(45deg);
	}

	TABLE.pinned{
		table-layout: fixed;
	}

	TABLE.pinned INPUT, TABLE.pinned SELECT {
		width: 99%;
	}

	TABLE.pinned TD:first-child {
		font-weight: bold;
		text-align: right;
		background-color: #ebf5fd;
	}

	TABLE.pinned TBODY TR TD {
		padding: 3px;
	}

	#advanced_query TD.td_input_data {
		overflow: visible;
	}

	#advanced_query .chosen-container {
		width: 100% !important;
	}

	TABLE.pinned .chosen-container {
		display: block !important;
		width: 99% !important;
	}
</style>
{block "head"}
{/block}
<div id="advanced" style="display: none;">
{block "advanced"}
{foreach from=$DtColumns item=c}
{if $c->Searchable}
	<kriter type="{$c->SearchType()}" label="{$c->DisplayName}" name="{$c->Name}"
			group="{$c->GroupName}" default="{$c->Default}" pinned="{$c->Pinned}">
		{$c->SearchOptionsFull}
	</kriter>
{/if}
{/foreach}
{/block}
</div>
{if NOT $HideSearch}
<div id="advanced_query" title="Advanced Search Criteria">
	<div class="table-responsive">
		<table width="100%" class="tbl-query tb_input_base">
			<col width="5%">
			<col width="24%">
			<col width="47%">
			<col width="24%">
		</table>
	</div>
</div>
{block 'ust_arama'}
{/block}
{block 'arama'}
<div id='search-cover-div'>
	<div >
	<table class="pinned" width="100%" cellspacing="1" use_default_button="1" style="display: none;">
		<colgroup>
			<col width="15%">
			<col width="*">
			<col width="20%">
			<col width="130">
		</colgroup>
		<tbody>

		</tbody>
		<tfoot>
		<tr>
			<td>Search</td>
			<td colspan="2">
				<input name="sorgu" label="Içinde %s geçen"	style="width: 99%"
					   onchange="$('TABLE.arama [name=sorgu]').val(this.value);"
					   value="{$smarty.get.sorgu}" type="text" title="Aradığınız kelimeyi giriniz"/>
			</td>
			<td class="buttons arama_button">
			</td>
		</tr>
		</tfoot>
	</table>
	</div>
	<table class="arama" style="width: 100%; display: none;" use_default_button="1">
		<tr>
			<td width="1"></td>
			<td width="20">Search :</td>
			<td>
				<input name="sorgu" label="Içinde %s geçen"	style="width: 99%"
					   value="{$smarty.get.sorgu}" type="text" title="Aradığınız kelimeyi giriniz"/>
			</td>
			<td width="130" class="buttons arama_button">
			</td>
		</tr>
	</table>
</div>
{/block}
<div class="kriter_templates">
<div id="kriter_sablon" style="display: none;">
	<span class="kriter">
	<div style="ui-widget">
		<div name="" class="ui-state-highlight ui-corner-all"  style="cursor: pointer">
			<span class="label" style="font-weight: bold; color: blue; font-size: 1.1em"></span>
			<span class="operator_l" style="font-weight: bold; color: green; font-size: 1.1em"></span>
			<span class="value" style="color:darkred"></span>
			<span class="operator_r" style="font-weight: bold; color: green; font-size: 1.1em"></span>
			<span class="kriter-close">
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
<div id="kriter_kaydet" title="Aramayı yeni sayfa olarak kaydet" style="display: none;">
	Bu sayfadaki arama sorgusunu yeni bir sayfa olarak kaydederek, bu aramaya daha hızlı erişebilirsiniz.
	Lütfen yeni sayfa için bir isim veriniz (maksimum 20 karakter):
	<div style="margin:15px 0px;">
		<input type="text" style="width: 100%;" maxlength="20" class="TabName" placeholder="Arama sayfasının adı...">
	</div>
</div>
</div>
{/if}
{$dt=reset($Page->DataTables)}
{if $dt->StaticGrid}
	{block "sonuc_static"}

	{if $Page->StaticRowTemplate neq ''}
		<div style="text-align: right; margin:5px 0px; font-weight: bold;">
			Toplam {$dt->DataPageInfo->RecordCount} kayıt
		</div>
		<div class="clearfix">
		{foreach item=row from=$dt->Data}
			{include file=$Page->GetPageFileUrl($Page->StaticRowTemplate)
				mode="static" DataRow=$row assign="tpl"}
			{PageController::ParseTemplate($row, $dt->Columns, get_class($dt->ModelDb), $tpl)}
		{/foreach}
		</div>
	{else}
	<table id="{$DbModelName}">
		<thead>
			<tr>
				{foreach item=col from=$dt->Columns}
					<td>{$col->Name}</td>
				{/foreach}
			</tr>
		</thead>
		{foreach item=row from=$dt->Data}
		<tr>
			{foreach item=val from=$row}
			<td>{$val}</td>
			{/foreach}
		</tr>
		{/foreach}
	</table>
	{/if}

	{if $dt->DataPageInfo->PageCount gt 1}
	<div style="text-align: center;">
		<ul class="pagination">
			{foreach item=link key=text from=$Page->GetBSPagingLinks()}
			{$class=""}
			{if $dt->DataPageInfo->PageNo eq $text}
				{$class="active"}
			{elseif $link eq ''}
				{$class="disabled"}
			{/if}
			<li class="page-item {$class}">
				<a class="page-link" href="{$link}">{$text}</a>
			</li>
			{/foreach}
		</ul>
	</div>
	{/if}
	{/block}
{else}
	{block "sonuc"}
		{if $tb_id}
			<table id="{$tb_id}"></table>
		{/if}
	{/block}
{/if}
