<style>
	.sp_param{
		min-width: 100px;
		display: inline-block;
		float: left;
		padding: 2px !important;
	}
	.sp_key{
		font-weight: bold;
		color: blue;
		padding: 1px !important;
	}
	.sp_val{
		font-weight: bold;
		color: green;
		padding: 1px !important;
	}
</style>
{AddCSS("others/codemirror/codemirror")}
{AddCSS("others/codemirror/neat")}
{AddJS("others/codemirror/codemirror")}
{AddJS("others/codemirror/sql")}
<script>
var init = function() {
    sql_editor = window.editor = CodeMirror.fromTextArea(document.getElementById('code'), {
        mode: 'text/x-mysql',
		theme: "neat",
        indentWithTabs: true,
        smartIndent: true,
        lineNumbers: true,
        matchBrackets : true,
        autofocus: true
    });
};

function RunSQL()
{
	var sql = sql_editor.getValue();
	Page.OpenNewWindow(GetUrl('cisc.run_sql','clear')+'&sql=' + escape(sql), 'debug_sql', 700, 400);
}

function ShowCode(code)
{
	Page.OpenNewWindow(GetUrl('cisc.show_code','clear')+'&code=' + escape(code), 'debug_code', 700, 400);
}
</script>
<style>
	.CodeMirror {
	  border: 1px solid #eee;
	  height: auto;
	}
	.CodeMirror-scroll {
	  overflow-y: hidden;
	  overflow-x: auto;
	}
</style>
<table class="sonuc" style="table-layout: fixed; word-break: break-all">
	<tr>
		<td class="td_input_caption">Açıklama</td>
		<td class="td_input_data">{$detay->Description}</td>
		<td class="td_input_caption">Maliyet</td>
		<td class="td_input_data">{$detay->Cost|number_format:2} ms / {$detay->TotalCost|number_format:2} ms</td>
	</tr>
	<tr>
		<td class="td_input_caption">Zaman</td>
		<td class="td_input_data">{$detay->Time|number_format:2} ms</td>
		<td class="td_input_caption">Bellek</td>
		<td class="td_input_data">{GetShortSize($detay->Memory)} / {GetShortSize($detay->MemoryPeak)}</td>
	</tr>
	{if $detay->Query neq ''}
	<tr>
		<td class="td_input_caption">Sonuç</td>
		<td class="td_input_data" colspan="3">
			{if $detay->Error eq ''}
				{$detay->RowCount} satır
			{else}
				Hata : {$detay->Error}
			{/if}
		</td>
	</tr>
	{/if}
	{if $params}
	<tr>
		<td class="td_input_caption">Ayrıntı</td>
		<td class="td_input_data" colspan="3" style="white-space: normal">
			<div style="padding: 3px; background-color: orange; color: white; font-weight: bold">GETS</div>
			{foreach from=$params.gets item="val" key="key"}
				<span class="sp_param">
					<span class="sp_key">{$key}</span>=<span class="sp_val">{$val}</span>
				</span>
			{/foreach}
			{if $params.posts}
			<div style="padding: 3px; background-color: yellowgreen; color: white; font-weight: bold; clear: both">POSTS</div>
			{foreach from=$params.posts item="val" key="key"}
					{if is_array($val)}
						<span class="sp_param" style="clear: both; width: 100%">
							<span class="sp_key" style="display: block">{$key}</span>
							<pre style="background-color: white">{DebugInfo::PrintR($val)}</pre>
						</span>
					{else}
						<span class="sp_param">
							<span class="sp_key">{$key}</span>=<span class="sp_val">{$val}</span>
						</span>
					{/if}
			{/foreach}
			{/if}
		</td>
	</tr>
	{/if}
</table>
{if $detay->Query neq ''}
<h2 style="height: 20px">Sorgu
<button style="padding: 3px; position: absolute;
   right: 15px;
   font-weight: bold; "
   onclick="RunSQL();"
   > > Çalıştır </button>
</h2>
<textarea id="code" name="code">{$detay->Query}</textarea>
{/if}
<table class="sonuc">
	<tr>
		<td class="td_input_top_caption" colspan="4">Call Stack</td>
	</tr>
	<tr>
		<td colspan="4">
			<table class="sonuc">
				<tr class="td_sonuc_head">
					<td>Dosya Adı ve Satır No</td>
					<td>Fonksiyon</td>
				</tr>
				{foreach from=$detay->BackTree item="stack"}
					<tr class="{cycle values="td_sonuc_row, td_sonuc_row2"}">
						<td>
							<a href="ertp://nbns?prj={App::$Kod}&file={$stack.dosya}">
								<img src="{GetImgUrl('dogru/Debug/images/netbeans.png')}" width="16" align="absmiddle">
							</a>
							<a href="Javascript:void(0);" onclick="ShowCode('{$stack.dosya}')">
								<acronym title="{$stack.dosya}">
									{if strlen($stack.dosya) gt 50}...{/if}{substr($stack.dosya, -50)}
								</acronym>
							</a>
						</td>
						<td>{$stack.func}</td>
					</tr>
				{/foreach}
			</table>
		</td>
	</tr>
</table>
{if $detay->Query neq ''}
<script>
	$(document).ready(function (){
		init();
	});
</script>
{/if}