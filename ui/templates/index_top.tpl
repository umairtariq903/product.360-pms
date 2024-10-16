<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="tr" lang="tr" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset={PageController::$CharSet}" />
<meta http-equiv="Content-Language" content="tr" />
{if $Page->Keywords}
<meta name="keywords" content="{implode(',', $Page->Keywords)}" />
{/if}

<!-- base href="{$SITE_URL}" / -->
{$Page->LoadResources()}

<title>{$Page->Title}</title>
</head>
