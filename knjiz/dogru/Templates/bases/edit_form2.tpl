{if $Page->Title}
	<div class="ers-page-header">{$Page->Title}</div>
{/if}
<div class="clearfix">
	{block 'DbModelForm'}
		{$DbModelForm->GetTable()}
	{/block}
</div>
<div class="button_panel ui-corner-all" style="position: relative; margin-top: 5px">
	{block 'Buttons'}
		<button class="jui-button" icon='ui-icon-disk' onclick="DbModelForm_Save('{$CustomSaveFunc}')">Save</button>
	{/block}
</div>
