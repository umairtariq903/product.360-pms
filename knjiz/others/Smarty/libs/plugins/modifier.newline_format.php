<?php 
function smarty_modifier_newline_format($text)
{
	return str_replace(array("\n", "\r"), array("\\n", "\\r"), $text);
}
?>