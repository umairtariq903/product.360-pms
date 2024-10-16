<?php
class PageRouter
{
	public static $PageDir = '';
	/**
	 * $_GET dizinde act, act2, act3, act4 değerlerine bakarak ilgili sayfaya yönlendirir.
	 * Eğer sayfayı bulamazsa smarty e index.tpl e yönlendirir.
	 */
	public static function Render($page = null)
	{
		if (!$page)
			$page = self::GetPage();
		if (!is_a($page, 'PageController'))
			die("<h2>Varsayılan (Default) sayfa bulunamamıştır</h2>");
		$page->Render();
	}
	/**
	 * $_GET dizinde act, act2, act3, act4 değerlerine bakarak ilgili sayfaya yönlendirir.
	 * Eğer sayfa yoksa default alanı true olduğunda sayfa yok diye hata verecek
	 */
	public static function Render2($page = null, $default = true)
	{
		if (!$page)
			$page = self::GetPage(true,$default);
		if (!is_a($page, 'PageController'))
			die("<h2>Sayfa bulunamadı</h2>");
		$page->Render();
	}

	public static function RenderDefault()
	{
		$smarty = SmartyWrap::Load();
		if ($smarty->templateExists('index.tpl'))
			$smarty->display('index.tpl');
		else
		{
			$smarty->assign('base_dir', dirname(__FILE__));
			$smarty->display(dirname(__FILE__) . '/index.tpl');
		}
	}

	/**
	 * Gönderilen sayfa parametresine göre istenen PageController nesnesini
	 * oluşturur, gerekli çağrıları yapar ve geri döndürür
	 *
	 * @return PageController
	 */
	public static function GetPage($executePage = true, $default = true)
	{
		if ($executePage)
		{
			$aclIns = PageACL::Get();
			if (! $aclIns->Gorme($_GET))
			{
				$controller = new PageController::$AppPageContrClass;
				$controller->ShowError('Bu sayfayı görme yetkiniz bulunmamaktadır');
				return $controller;
			}
		}
		$pattern = '/[^a-zA-Z0-9_-]/';
		$actCode = preg_replace($pattern, '', IfNull($_GET, 'act', 'default'));
		$actCode2 = preg_replace($pattern, '', @$_GET['act2']);
		$actCode3 = preg_replace($pattern, '', @$_GET['act3']);
		$actCode4 = preg_replace($pattern, '', @$_GET['act4']);
		// Sınıfların aranacağı klasör
		$dirs = array();
		$acts = array($actCode, $actCode2, $actCode3, $actCode4);
		// PageController çoğu zaman ui/pages altında bulunacak
		// fakat bazı durumlarda bir modülle ilgili de olabilir
		//
		// O yüzden ilgili modül klasörünü de tarama listesine ekliyoruz
		// (Optimizasyon amaçlı şimdilik sadece "act2" e bakılacak)
		$folders = array(App::$Klasor);
		foreach(\DogRu\AppModules::GetAktifNames() as $module)
			if ($actCode2 == $module)
				$folders[] = FullPath("modules/$module/");

		if (self::$PageDir)
			$folders[] = self::$PageDir;

		// Eldeki tüm klasörleri ilgili act, act2, act3 ve act4 seçeneğine bakarak tarıyoruz
		foreach($folders as $folder)
		{
			if (! self::$PageDir)
				$folder .=  'ui/pages/';
            if ($default)
			    $dirs[] = array('folder' => $folder . 'default/', 'file' => 'default.php', 'acts' => array('default'));
			$acts2 = array();
			$i = 0;
			foreach ($acts as $act) {
				if($act == '')
					break;
				if (! self::$PageDir || $i > 0)
					$folder = $folder. "$act/";
				$acts2[] = $act;
				$dirs[] = array('folder' => $folder, 'file' => "$act.php", 'acts' => $acts2);
				$i++;
			}
		}

		$alternatives = array_reverse($dirs);

		// Tarama sonucu bulunan dosyalarda bir PageController sınıfına ait
		// bir sınıf varsa, controller oradan türetilecek ve tarama tamamlanacak
		$controller = null;
		foreach($alternatives as $alt)
		{
			$path = $alt['folder'] . $alt['file'];
			if (isFile($path) && basename(realpath($path)) === $alt['file'])
				$controller = PageRouter::GetControllerInstance ($actCode, $path, $alt['acts']);

			if ($controller != null)
				break;
		}// foreach

		if ($controller != null && $executePage)
		{
			// Nesnenin başlatılıyor
			$controller->Init();

			// Gönderilen istek türüne göre controller içinde
			// ilgili metotları çalıştır
			$RequestType = 'get';
			if (PageRouter::IsAjaxRequest())
			{
				$RequestType = 'ajax';
				$controller->HandleAjaxRequest();
			}
			else
			{
				SmartyWrap::Load(); // Genel ayarların yüklenmesi için gerekli mi? Genel ayarlar index de zaten yükleniyor.
				if (! $controller->StopExecution)
				{
					if ($_SERVER['REQUEST_METHOD'] == 'POST')
					{
						$RequestType = 'post';
						$controller->FormPosted();
					}
					else
						$controller->Index();
				}
			}
			$controller->RequestComplate($RequestType);
		}
		return $controller;
	}

	/**
	 * Aktif isteğin bir AJAX isteği olup olmadığını döndürür
	 *
	 * @return boolean
	 */
	public static function IsAjaxRequest()
	{
		$ajaxParams = array('act' => 'ajax', 'sent_mode' => 'ajax', 'ajax' => '1');
		foreach($ajaxParams as $name => $value)
			if (isset($_GET[$name]) && trim($_GET[$name]) == $value)
				return true;

		return false;
	}

	/**
	 * İlgili sayfa sınıfını bulur, oluşturur ve döndürür
	 *
	 * @return PageController
	 */
	private static function GetControllerInstance($actCode, $fileUrl, $acts)
	{
		// Beklenen dosya var, bu dosya içinde tek bir sınıf olduğunu
		// ve o sınıfın da PageController'dan türetilmesini bekliyoruz
		$loadedClasses1 = get_declared_classes();
		require_once ($fileUrl);

		$path = pathinfo($fileUrl);
		$dir = $path['dirname'];
		// Sınıf adını deniyoruz
		// (className = fileName + Page)
		// (PHP fonksiyon ve sınıf isimlerinde büyük küçük harf ayrımı yapmıyor!)
		$className = $actCode . 'Page';
		$tpl = $path['filename']. '.tpl';
		if (class_exists($className))
			return new $className($tpl, $dir, $acts);

		// Eğer beklenen sınıf gelmediyse, sınıf adı farklı yazılmış olabileceğini
		// düşünerek, şansımızı bir kere de, en son yüklenen sınıfın PageController'dan türetilip
		// türetilmediğine bakarak deniyoruz
		$loadedClasses2 = get_declared_classes();
		$newClassName = array_diff($loadedClasses2, $loadedClasses1);
		if (count($newClassName) > 0)
		{
			$newClassName = end($newClassName);
			if (is_subclass_of($newClassName, 'PageController'))
				return new $newClassName($tpl, $dir, $acts);
		}
		return null;
	}
}
