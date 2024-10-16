{if $smarty.get.act eq 'developer' OR $smarty.get.act eq 'db_model' OR $smarty.get.act eq 'cisc'}
		{include file="index_top.tpl"}
		<body style="background: white; margin: 10px;">
			{include file="index_main.tpl"}
		</body>
{else}
	{if $smarty.get.mode neq 'clear'}
		{include file="$TEMA_URL/index.tpl"}
	{else}
		{include file="index_top.tpl"|template_check}
		<body style="background: white; margin: 10px;">
			{include file="index_main.tpl"}
		</body>
	{/if}
{/if}
