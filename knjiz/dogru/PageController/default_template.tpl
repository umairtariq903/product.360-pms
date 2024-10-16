{if $smarty.get.mode neq 'clear'}
	{include file="index.tpl"|CheckOverriddenTemplate}
{else}
	{include file="index_top.tpl"|CheckOverriddenTemplate}
	<body style="background: white; margin: 10px;">
		{include file="index_main.tpl"|CheckOverriddenTemplate}
	</body>
{/if}