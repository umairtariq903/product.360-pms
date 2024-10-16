<?php
namespace DogRu;

/**
 * Otomasyonlarda olan modüllere ait bilgiler
 */
class AppModule
{
	// Son kullanıcıya görünsün mü?
	public $Visible = true;
	// Sistemde var mı?
	public $Durum = 0;
	// Aktif mi?
	public $Aktif = 1;
	public $Baslik;
	public $Aciklama;
	public $Bagimliliklar = array();

	/**
	 * Modül ayarlamaları için gerekli tanımlamaları , aldığı parametre ile eşitleyen fonksiyondur.
	 */
	public function __construct($baslik, $aciklama, $bagimliliklar = array())
	{
		$this->Baslik = $baslik;
		$this->Aciklama = $aciklama;
		$this->Bagimliliklar = $bagimliliklar;
	}

	/**
	 * Modüllerin aktiflik durumunu kontrol eden fonksiyondur.
	 */
	public function IsAktif()
	{
		return $this->Durum && $this->Aktif;
	}

	public function GetBagimlilikNames()
	{
		$sonuc = array();
		foreach($this->Bagimliliklar as $kod)
			$sonuc[] = AppModules::GetByName($kod)->Baslik;
		return $sonuc;
	}
}

/**
 * Otomasyonda bulunan tüm modülleri, aktifler listesi
 */
class AppModules
{
	private static $Instance = NULL;

	/**
	 * @var AppModule[]
	 */
	public $Modules = array();

	/**
	 * @return AppModules
	 */
	public function Add($kod, $name, $desc, $depancity = array())
	{
		self::Get()->Modules[$kod] = new AppModule($name, $desc, $depancity);
		return $this;
	}

	/**
	 * @return AppModules
	 */
	public function HideModules($modules)
	{
		foreach($modules as $name)
			if (isset($this->Modules[$name]))
				$this->Modules[$name]->Visible = false;
		return $this;
	}

	/**
	 * @return AppModules
	 */
	public static function Get()
	{
		if (! self::$Instance)
			self::$Instance = new AppModules();
		return self::$Instance;
	}

	/**
	 * Aktif edilmiş modüllerin ayarlamasını yapan fonksiyondur.
	 */
	public static function SetModules($modules)
	{
		if (! is_array($modules))
			$modules = explode(',', $modules);
		$demoModuls = Config('app.DEMO_MODULES');
		if ($demoModuls)
			foreach($demoModuls as $modulAdi=>$tarih)
				if(\Tarih::FarkVer(\Tarih::Bugun(), $tarih) > 0)
					$modules[] = $modulAdi;

		$mdls = self::Get();
		foreach($modules as $m)
			if (isDir(FullPath("modules/$m")) && array_key_exists($m, $mdls->Modules))
				$mdls->Modules[$m]->Durum = 1;
	}

	/**
	 * Yetkili kullanıcı tarafından pasif hale getirilmiş olan modülleri setler
	 */
	public static function LoadNotAktif()
	{
		$mdls = self::Get();
		$not_aktif_modules = explode(',', \GenelDb::GetValue('not_aktif_modules', ''));
		foreach($not_aktif_modules as $m)
			if (array_key_exists($m, $mdls->Modules))
				$mdls->Modules[$m]->Aktif = 0;
	}

	/**
	 * Yetkili kullanıcı tarafından pasif hale getirilen modülleri kaydeder
	 */
	public static function SaveNotAktif($modules)
	{
		return \GenelDb::SetValue('not_aktif_modules', $modules);
	}

	/**
	 * @return AppModule[]
	 */
	public static function GetModules()
	{
		return self::Get()->Modules;
	}

	/**
	 * @return AppModule[]
	 */
	public static function GetAllVisibleModules()
	{
		$modules = self::GetModules();
		// Modül listesinde gözükmemesini istediğimiz veya daha hazır olmayan
		// modülleri çıkartıyoruz
		foreach($modules as $name => $mdl)
			if (!$mdl->Visible)
				unset($modules[$name]);
		return $modules;
	}

	/**
	 * Sadece aktif modülleri verir
	 * @return AppModule[]
	 */
	public static function GetVisibleModules()
	{
		$modules = self::GetModules();
		// Modül listesinde gözükmemesini istediğimiz veya daha hazır olmayan
		// modülleri çıkartıyoruz
		foreach($modules as $name => $mdl)
			if (!$mdl->Durum || !$mdl->Visible)
				unset($modules[$name]);
		return $modules;
	}

	/**
	 * Modül aktif durumunun kontrolünü yapan fonksiyondur.
	 */
	public static function IsAktif($name)
	{
		$moduls = self::GetModules();
		return isset($moduls[$name]) && $moduls[$name]->IsAktif();
	}

	/**
	 * @return AppModule
	 */
	public static function GetByName($name)
	{
		$moduls = self::GetModules();
		return $moduls[$name];
	}

	/**
	 * Aktif modül isimlerinin ilgili yerlere gönderilmesini sağlayan fonksiyondur.
	 */
	public static function GetAktifNames()
	{
		$moduls = array();
		foreach(self::GetModules() as $moduleName => $modul)
			if($modul->IsAktif())
				$moduls[] = $moduleName;
		return $moduls;
	}
}
