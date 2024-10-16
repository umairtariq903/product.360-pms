{extends 'bases/arama_page.tpl'}
{block 'arama'}
	<table class="jui-table arama" style="width: 100%" use_default_button="1">
	{foreach item=param from=$rapor->Params}
		<tr><td width="150">{$param->Adi}</td>
		<td>
			{if $param->Tur eq AppSqlViewParam::T_DATE}
				<input type=text name="{$param->Kodu}" id="{$param->Kodu}" value="{$param->Value}" size=10 date_selector="1">
			{elseif $param->Tur eq AppSqlViewParam::T_LIST}
				<select name="{$param->Kodu}" dont_hide="1" style="max-width: 500px">
					{foreach item=item from=$param->Items}
						<option {if $param->Value eq $item}selected{/if}>{$item}</option>
					{/foreach}
				</select>
			{elseif $param->Tur eq AppSqlViewParam::T_ARRAY}
				<select name="{$param->Kodu}" dont_hide="1" style="max-width: 500px">
					{foreach item=item key=key from=$param->Items}
						<option value='{$key}' {if $param->Value eq $key}selected{/if}>{$item}</option>
					{/foreach}
				</select>
			{elseif $param->Tur eq AppSqlViewParam::T_SUB_QUERY}
				<select name="{$param->Kodu}" dont_hide="1" style="max-width: 500px">
					{foreach item=item from=$param->Items}
						<option value="{$item.id}" {if $param->Value eq $item.id}selected{/if}>{$item.value}</option>
					{/foreach}
				</select>
			{elseif $param->Tur eq AppSqlViewParam::T_STRING}
				<input type=text name="{$param->Kodu}" id="{$param->Kodu}" value="{$param->Value}" size=20>
			{/if}
		</td></tr>
	{/foreach}
	<tr>
		<td width="20">Search</td>
		<td>
			<input name="sorgu" label="İçinde %s geçen"	style="width: 100%"
				   type="text" title="Aradığınız kelimeyi giriniz"/><br>
		</td>
	</tr>
	<tr>
		<td width="20" colspan="2" class="buttons arama_button" style="text-align: right; background-color: white; padding: 5px">
		</td>
	</tr>
	</table>
	<br>
{/block}
{block "sonuc"}
	<br>
	<table id='Rapor'></table>
	<br>
	<div style="float:right; {if !$rapor->AltRow}display: none;{/if}">
		<table class="summary sonuc" for="Rapor" style="width: 400px;">
			<colgroup>
				<col width=40%>
				<col width=60%>
			</colgroup>
			<tr>
				<td colspan="2" class="ui-widget-header" align='center'>
					Listelenen Kayıtların Özeti
				</td>
			</tr>
			{$first=1}
			{foreach from=$rapor->AltRow[0] key=ad item=deger}
				{if !$first}
				<tr>
					<td class="ui-state-focus">{$ad}</td>
					<td class="odd" align="right"><div field_name="{$ad}"></div></td>
				</tr>
				{/if}
				{$first=0}
			{/foreach}
		</table>
	</div>
{/block}
