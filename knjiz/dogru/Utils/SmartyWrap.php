<?php
class SmartyWrap
{
	/**
	 * @var function($smarty) smarty yüklendiğinde otomatik olarak çalıştırılması
	 * istenilen fonksiyon adı
	 */
	public static $OnLoadFunction = null;

	/**
	 * Daha önce oluşturulan $smarty instance i verir. Oluşturulmamışsa oluşturur.
	 * Oluşturulma sırasında $OnLoadFunction verilmişse çalıştırır.
	 * @return SmartyBC
	 */
	public static function Load($recreate = false)
	{
		global $smarty;
		if($smarty && $recreate)
			$smarty = NULL;
		if($smarty)
			return $smarty;
		require_once KNJIZ_DIR . '/others/Smarty/libs/SmartyBC.class.php';
		Smarty::$_CHARSET = 'utf-8';
		if (PageController::$CharSet)
			Smarty::$_CHARSET = PageController::$CharSet;

		$smarty = new SmartyBC();
		$smarty->error_reporting = E_ERROR;
		$tdir = FullPath('ui/templates');
		$cdir = FullPath('ui/templates_c');
		$smarty->addTemplateDir($tdir);
		$smarty->addTemplateDir(KNJIZ_DIR . '/dogru/Templates');
		$smarty->addTemplateDir(KNJIZ_DIR . '/dogru/PageController');
		$smarty->compile_dir = $cdir;
		if (! is_dir($tdir))
			mkdir ($tdir, 0777, true);
		if (! is_dir($cdir))
			mkdir ($cdir, 0777, true);

		$func = self::$OnLoadFunction;
		if(function_exists($func))
			$func($smarty);

		return $smarty;
	}

	/**
	 * smarty e değişken ve değerini atar.
	 * @param string $name
	 * @param mixed $value
	 */
	public static function Assign($name, $value)
	{
		SmartyWrap::Load()->assign($name, $value);
	}

	public static function Fetch($tplName)
	{
		return SmartyWrap::Load()->fetch(Tema::TemplateCheck($tplName));
	}
}

