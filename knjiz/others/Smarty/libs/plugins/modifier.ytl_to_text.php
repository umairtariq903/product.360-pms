<?php

function smarty_modifier_ytl_to_text($number)
{
	$ToplamTL = intval($number);
	$ToplamYK = intval(number_format($number - intval($number), 2, '.', '') * 100);

	return StringLib::TLToString($ToplamTL) . ' TL '.
		StringLib::TLToString($ToplamYK) . ' KR';
}
?>