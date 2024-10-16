<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="tr">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
	<head>
		{if NOT isLocalhost()}
			<!-- Google tag (gtag.js) -->
			<script async src="https://www.googletagmanager.com/gtag/js?id=G-7C16VYJ3T0"></script>
			<script>
				window.dataLayer = window.dataLayer || [];
				function gtag(){ dataLayer.push(arguments); }
				gtag('js', new Date());

				gtag('config', 'G-7C16VYJ3T0');
			</script>
		{/if}
	<meta http-equiv="Content-Language" content="tr" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	{if $Page->Keywords}
		<meta name="keywords" content="{implode(',', $Page->Keywords)}" />
	{/if}

	<base href="{$SITE_URL}"/>
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
	{$Page->LoadResources("css")}

	<title>{$Page->Title}</title>
	</head>
