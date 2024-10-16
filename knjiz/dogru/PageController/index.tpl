<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="tr" lang="tr" >
{if $smarty.get.mode eq 'clear'}
	{* SITEYE OZEL TASARIM GORUNMEYECEK, SADECE ICERIK GOSTERILIYOR *}
	{include file="index_top.tpl"|CheckOverriddenTemplate}
	<body style="background: white; margin: 10px;">
		{include file="index_main.tpl"|CheckOverriddenTemplate}
	</body>
{else}
	{* SITEYE OZEL TASARIM GORUNECEK *}
	{include file="index_app.tpl"|CheckOverriddenTemplate}
{/if}
</html>