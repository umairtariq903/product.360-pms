<?php
class AppUpdater
{
	public static $VersionKey = 'version';

	/** @var \DogRu\AppSemaphore */
	private static $fpLock = null;

	public static function Check()
	{
		global $version;

		$dbVersion = intval(GenelDb::GetValue(self::$VersionKey));

		if ($dbVersion == '' || !isset($dbVersion))
			self::UpdateHata ('Genel tablosu ulaşılamadı!', false);
		// Veritabanı kodlardan daha güncel olursa güncelleme yapma
		// (Sadece bizde yaşanabilecek bir durum)
		if ($dbVersion >= $version || ($dbVersion != -1 && $dbVersion < 1000 ))
			return;

		self::Begin();
		if (isLocalhost())
			AutoLoader::Register(true);
		DB::Connect(0);
		$patern = "upd\-([0-9]+)\-(.*)\.(sql|php)";
		$dFiles = DosyaSistem::getDirContents('update', $patern, true);
		$files = array();
		foreach($dFiles as $f)
		{
			$regs = array();
			if (preg_match("/$patern/i", $f, $regs))
			{
				$ver = intval($regs[1]);
				$ext = $regs[3];
				$files[$ver][$ext] = $f;
			}
		}

		// Güncelleme sırasında henüz oluşturulmamış alanları ModelBase
		// üzerinden kaydetme yapmamak için alan kontrolünü aktifleştiriyoruz
		ModelDb::$CHECK_FIELDS_ON_SAVE = true;

		for($i=$dbVersion+1; $i<= $version; $i++)
		{
			Transaction::Commit();
			if(! isset($files[$i]))
				continue;

			// Kaydedilen tablo alanları varsa sıfırla
			ModelDb::$TABLE_FIELDS = array();

			$f = $files[$i];
			if (isset($f['sql']))
				self::RunQuery($f['sql'], $i);
			if (isset($f['php']))
				self::RequirePHPFile($f['php']);
			GenelDb::SetValue(self::$VersionKey, $i);

			Transaction::Begin();
		}

		ModelDb::$CHECK_FIELDS_ON_SAVE = false;
		self::End();
	}

	public static function RequirePHPFile($filePath)
	{
		require_once $filePath;
	}

	public static function RunQuery($file)
	{
		global $SITE_URL;
		if (! file_exists($file))
			self::UpdateHata($file . ' bulunamadı</h1>');

		$content = file_get_contents($file);

		$queries = preg_split('$(/\*\*\*/)$', $content);
		foreach($queries as $query)
		{
			$regs = array();
			if (preg_match("#\[site\:([a-z0-9.\_-]+)\]#", $query, $regs))
			{
				$url = $regs[1];
				if (! eregi2($url, $SITE_URL))
					continue;
			}

			try
			{
				$query = trim($query);
				if ($query != '')
					DB::Execute($query, 'Güncelleme');
			}
			catch(Exception $exc)
			{
				if(isLocalhost())
					echo $exc->getMessage() . "<br>";
				else
					self::UpdateHata($exc->getMessage(), false);
			}
		}
	}

	private static function UpdateHata($mesaj, $exit = false)
	{
		if(isLocalhost())
			echo $mesaj;
		else
		{
			$projeKod = App::$Kod;
			$email = @$GLOBALS['DGR_PROJE_MAIL'];
			Mailer::Send($email, "Güncelleme Hatası [".$GLOBALS['SITE_URL']."] [ proje=$projeKod ]", $mesaj);
		}

		if ($exit)
			die($mesaj);
	}

	public static function Begin()
	{
		// Aynı anda sadece bir istemcinin (thread) güncelleme yapmak için
		// aşağıdaki kod parçasına girmesini sağlıyoruz
		self::$fpLock = \DogRu\AppSemaphore::begin('update');
		if (!self::$fpLock)
			die('<h2>Şu an güncelleme yapılmaktadır. Lütfen daha sonra deneyiniz.</h2>');

		DB::$Updating = TRUE;
		//timeout a düşmemek için
		set_time_limit(0);
		session_write_close();
		// o an çalışan işlerin bitmesi için 3 sn bekliyelim
		sleep(3);
	}

	public static function End()
	{
		DB::$Updating = FALSE;

		if (self::$fpLock)
			self::$fpLock->release();
		return true;
	}
}