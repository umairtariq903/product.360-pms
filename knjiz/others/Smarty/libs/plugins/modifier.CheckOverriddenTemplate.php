<?php
function smarty_modifier_CheckOverriddenTemplate ($template)
{
	$smarty = SmartyWrap::Load();
	$url = KNJIZ_DIR . 'dogru/PageController/' . $template;
	if ($smarty->templateExists($template))
		return $template;
	else if (isFile($url))
		return $url;
	else
		return $template;
}

?>