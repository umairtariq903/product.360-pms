{if $Page->Title}
	<div class="ers-page-header" page-icon="{$Page->pageIcon}">{$Page->Title}</div>
{/if}
{block 'DbModelForm'}
	{$DbModelForm->GetTable()}
{/block}
