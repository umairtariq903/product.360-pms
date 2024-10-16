{$cls=''}
{if $Page->Title}
	<div class="ers-page-header">
		{block 'PageTitle'}{$Page->Title}{/block}
	</div>
	{$cls='with_top_margin'}
{/if}
<div class="{if $Page->PageInfo->LinkType eq 1}page_edit_form {$cls}{/if}">
	{block 'DbModelForm'}
		{$DbModelForm->GetTable()}
	{/block}
</div>
<div class="button_panel" id="DbFormButtonSet"
	{if $Page->PageInfo->LinkType neq 1}
		style="text-align: right; "
	{/if}>
{block 'Buttons'}
	<button id="DbFormCancelButton" class="jui-button" icon='ui-icon-cancel' onclick="window.close();">İptal</button>
	<button id="DbFormSaveButton" class="jui-button" icon='ui-icon-disk' onclick="DbModelForm_Save('{$CustomSaveFunc}')">Kaydet ve Kapat</button>
{/block}
</div>
