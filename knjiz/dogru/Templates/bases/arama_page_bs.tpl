{extends file="bases/arama_form_bs.tpl"}
{block "head"}
	{if $Title}
	<div class="ers-page-header">{$Title}</div>
	{/if}
{/block}
{block "sonuc"}
{$dt=reset($Page->DataTables)}
{if $dt->StaticGrid}
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
	{$pageSize=$dt->DataPageInfo->PageSize}
	{$start=($dt->DataPageInfo->PageNo-1)*$pageSize}
	{$finish=$start+$pageSize}
	{if $finish gt $dt->DataPageInfo->RecordCount}
		{$finish=$dt->DataPageInfo->RecordCount}
	{/if}
	<div class="ui-widget-header ui-corner-bottom">
		<table width="100%">
			<tr>
				<td>
					Toplam {$dt->DataPageInfo->RecordCount} kayıttan,
						{$start+1} ile {$finish} arası gösteriliyor
				</td>
				<td align="right">
					{$var="iDisplayStart"}
					{$url=PagedData::SayfaUrlVer($var)}
					{$prevStart=$start-$pageSize}
					{$nextStart=$finish}
					{if $prevStart lt 0}
						<button disabled class="jui-button" icon="ui-icon-seek-prev">
							Önceki sayfa
						</button>
					{else}
						<a class="jui-button" icon="ui-icon-seek-prev"
						   href="{$url}&{$var}={$prevStart}">Önceki sayfa</a>
					{/if}
					{if $nextStart gte $dt->DataPageInfo->RecordCount}
						<button disabled class="jui-button" icon="ui-icon-seek-next">
							Sonraki sayfa
						</button>
					{else}
						<a class="jui-button" icon="ui-icon-seek-next"
						   href="{$url}&{$var}={$nextStart}">Sonraki sayfa</a>
					{/if}
				</td>
			</tr>
		</table>
	</div>
{else}
	<table id="{$DbModelName}"></table>
{/if}
{/block}
