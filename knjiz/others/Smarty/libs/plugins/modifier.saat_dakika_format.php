<?php
function smarty_modifier_saat_dakika_format($saat)
{
	return substr($saat, 0, 5);
}