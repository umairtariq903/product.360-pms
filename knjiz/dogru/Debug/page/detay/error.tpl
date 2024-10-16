<script>
function ShowCode(code)
{
	Page.OpenNewWindow(GetUrl('cisc.show_code','clear')+'&code=' + escape(code), 'debug_code', 700, 400);
}
</script>
<table class="sonuc">
	<tr>
		<td class="td_input_caption">Hata Kodu</td>
		<td class="td_input_data">{$err->GetTypeStr()}</td>
		<td class="td_input_caption">Zaman</td>
		<td class="td_input_data">{$err->ErrTime}</td>
	</tr>
	<tr>
		<td class="td_input_caption">Hata</td>
		<td class="td_input_data" colspan="3">{$err->ErrStr}</td>
	</tr>
	<tr>
		<td class="td_input_caption">Dosya</td>
		<td class="td_input_data" colspan="3">
			<a href="ertp://nbns?prj={App::$Kod}&file={$err->ErrFile}:{$err->ErrLine}"><img
					src="{GetImgUrl('dogru/Debug/images/netbeans.png')}" width="16" align="absmiddle"></a>&nbsp;
			<a href="Javascript:void(0);" onclick="ShowCode('{$err->ErrFile}:{$err->ErrLine}')">
				<acronym title="{$err->ErrFile}:{$err->ErrLine}">
					{if strlen($err->ErrFile) gt 50}...{/if}{substr($err->ErrFile, -50)}
				</acronym>
			</a>
		</td>
	</tr>
</table>
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
				{foreach from=$err->BackTree item="stack"}
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
