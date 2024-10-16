<?php
/*
* Smarty plugin
* -------------------------------------------------------------
* Type:   modifier
* Name:   number_format
* Purpose:   formats number using number_format
* Install: Drop into the plugin directory
* -------------------------------------------------------------
 * @param type $number Formatlanacak sayı
 * @param type $decimals Noktadan sonra kaç hane göster
 * @param int $xls -1: Ayara bağlı, 0: Binlik ayraç virgül, 1: Binlik ayraç nokta
 * @return type
 */
function smarty_modifier_ytl_format($number, $decimals = 2, $xls = false)
{
	if ($xls == true)
		return number_format($number, $decimals, ',', '.');
	else
		return Number::Format($number, $decimals);
}
?>