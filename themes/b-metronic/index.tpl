<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="tr">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />
		<meta http-equiv="Content-Language" content="tr" />
		<base href='{$SITE_URL}' />
        <title>{IfNull($Page, 'PageTitle', $Title)}</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="robots" content="noindex">
		{$Canonical=$Page->GetCanonicalUrl()}
		{if $Canonical}
		<link rel="canonical" href="{$Canonical}"/>
		{/if}
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<link rel="preload" href="pravi/others/font_awesome/css/font-awesome.min.css" as="style" />
		<link rel="preload" href="pravi/others/font_awesome/css/build.css" as="style" />
		{$Page->LoadResources('css')}
{*		<link rel="shortcut icon" href="{$TEMA_URL}/images/favicon.ico" type="image/x-icon" />*}
		<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
	</head>
    <!-- END HEAD -->
	{if  $girisYapan}
		{include "./index_user.tpl"}
	{else}
		{include file="index_main.tpl"}
	{/if}
	<!--[if lt IE 9]>
		<script src="dodatak/metronic/global/plugins/respond.min.js"></script>
		<script src="dodatak/metronic/global/plugins/excanvas.min.js"></script>
		<script src="dodatak/metronic/global/plugins/ie8.fix.min.js"></script>
	<![endif]-->
	{$Page->LoadResources('js')}
	<script>Page.Params = Page.GetParameters();</script>
</html>

