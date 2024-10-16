<?php 
function smarty_modifier_tarih_format($tarih)
{
	return Tarih::ToNormalDate($tarih, '-', false);
}
?>