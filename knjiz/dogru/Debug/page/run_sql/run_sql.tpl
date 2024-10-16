{AddCSS("others/jquery/dataTables/demo_page")}
{AddCSS("others/jquery/dataTables/demo_table")}
{AddJS("jquery.dataTables/jquery.dataTables.min")}
<table id="run_sql" class="sonuc" cellspacing="0" cellpadding="2">
	<thead>
		<tr>
		<td>S.No</td>
		{foreach item=field from=$Fields}
			{if $field->name neq 'proje_id' AND $field->name neq 'click_url'}
				<td>{$field->name}</td>
			{/if}
		{/foreach}
		</tr>
	</thead>
	{foreach item=row from=$Rows}
	<tr
	  {if isset($Fields.click_url)}
		class="clickable_row {cycle values='td_sonuc_row,td_sonuc_row2'}"
		click_url="{$row.click_url}"
	  {elseif isset($Fields.proje_id)}
		class = "proje_row {cycle values='td_sonuc_row,td_sonuc_row2'}"
		proje_id="{$row.proje_id}" ptur="{$row.tur}"
	  {else}
		class = "{cycle values='td_sonuc_row,td_sonuc_row2'}"
	  {/if}
	>
		<td class = "int_column">{counter}</td>
		{foreach key=fieldName item=field from=$Fields}
			{if $fieldName neq 'proje_id' AND $fieldName neq 'click_url'}
				<td
					{if $field->type eq "int" or $field->type eq "real"}
						class = "int_column"
					{/if}
				>
					{if $field->type eq "real"}
						{$row[$fieldName]|ytl_format}
					{else}{if $fields[$i]->type eq "date"}
							{$row[$fieldName]|date_format:"%d-%m-%Y"}
						{else}
							{$row[$fieldName]}
						{/if}
					{/if}
				</td>
			{/if}
		{/foreach}
	</tr>
	{/foreach}
</table>
<label>Maliyet : </label> {$Maliyet}
<script>
	$(document).ready(function() {
		$('#run_sql').dataTable();
		$('[name="run_sql_length"]').val(100).change();
	} );
</script>