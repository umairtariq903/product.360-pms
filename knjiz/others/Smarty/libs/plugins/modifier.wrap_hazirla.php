<?php
/*
 * Bazı metinlerdeki yanyana virgülle ayrılmış
 * kelimeler html içinde alt satıra geçme işlemini
 * tetiklemiyor, bu yüzden bu virgüllerden sonra birer
 * boşluk koyarak alt satıra geçme işlemine
 * zorlayacağız.
 */
function smarty_modifier_wrap_hazirla($text)
{
	return preg_replace(
		array("/,/", "/\\//", "/\\\\/"),
		array(", ", ", ", ", "),
		$text);
}
?>