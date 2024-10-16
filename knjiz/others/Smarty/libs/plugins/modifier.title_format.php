<?php
/*
* Smarty plugin
* -------------------------------------------------------------
* Type:   modifier
* Name:   title_format
* Purpose:   formats title using mb_convert_case ($title, MB_CASE_TITLE);
* Install: Drop into the plugin directory
* -------------------------------------------------------------
*/
function smarty_modifier_title_format($title)
{
	return TitleCase($title, MB_CASE_TITLE);
}