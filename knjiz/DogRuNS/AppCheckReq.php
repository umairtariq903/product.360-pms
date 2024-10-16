<?php

namespace DogRu;

/**
 * Application Check Requirements
 */
class AppCheckReq
{
	public $PhpVersion;
	public $PhpExts;
	public $PhpIniGet;
	public $Folders;
	public $ExternalPrograms;

	public static function Get()
	{
		static $ints = null;
		if ($ints)
			return $ints;
		$ints = new AppCheckReq();
		$ints->checkPhp();
		$ints->checkFolders();
		$ints->checkExternalPrograms();

		return $ints;
	}

	private function checkPhp()
	{
		$ver = explode('.', phpversion());
		$ver = intval($ver[0] . $ver[1]);
		$this->PhpVersion = array(
			'ver'	 => substr(phpversion(), 0, 5),
			'min'	 => '5.3',
			'best'	 => '7.0',
			'ok'	 => $ver >= 70 ? 'GOOD' : ($ver >= 53 ? 'OK' : 'NA')
		);
		$that = $this;

		$checkExt = function ($ext, $desc, $req = true) use($that)
		{
			static $extensions = null;
			if (! $extensions)
				$extensions = get_loaded_extensions();
			$that->PhpExts[] = array(
				'ext'	 => $ext,
				'desc'	 => $desc,
				'req'	 => $req,
				'ok'	 => in_array($ext, $extensions)
			);
		};

		$checkExt('gd', 'GD (Grafik kütüphanesi)');
		$checkExt('exif', 'EXIF (Resim dosyaları inceleme kütüphanesi)');
		$checkExt('mbstring', 'MB (Karakter seti dönüştürme)');
		$checkExt('imap', 'IMAP (Posta kutusu kimlik doğrulama)');
		$checkExt('mysqli', 'MySQLi (Veritabanı sunucusu işlemleri)');
		$checkExt('json', 'JSON (JSON formatına dönüştürme-okuma)');
		$checkExt('zip', 'ZIP (Zip dosyalarını okuma ve oluşturma)');
		$checkExt('dom', 'DOM (Uzak web sayfası parse etme)');
		$checkExt('libxml', 'LIBXML (Excel aktar ve web sayfası parse etme)');
		$checkExt('pcre', 'PCRE (preg_match ve replace ile düzenli ifadeler)');
		$checkExt('Reflection', 'REFLECTION (Sınıfların özelliklerini sorgulama)');
		$checkExt('session', 'SESSION (Oturum yönetimi)');
		$checkExt('soap', 'SOAP (Web servisleri için örneğin Sms gönderme için gerekli)');
		$checkExt('curl', 'CURL (Sms gönderimi için gerekli)');
		$checkExt('SPL', 'SPL (Iterator v.b. standart sınıflar)');
		$checkExt('xmlreader', 'XMLREADER (Xml formatında yazma için gerekli)');
		$checkExt('zlib', 'ZLIB (Zip işlemleri için gerekli)');
		$checkExt('ldap', 'LDAP(LDAP kimlik doğrulama için gerekli)');
		$checkExt('openssl', 'OPENSSL');
		if (OS_IsWindows())
			$checkExt('com_dotnet', 'COM Window kaynak ölçümü için gerekli', false);

		$checkExt('Zend OPcache', 'OPCACHE Peroformans artırmak için', false);

		usort($that->PhpExts, function($a, $b){
			$sonuc = strcmp($a['ok'], $b['ok']);
			if ($sonuc == 0)
				$sonuc = strcmp($b['req'], $a['req']);
			if ($sonuc == 0)
				$sonuc = strcasecmp($a['ext'], $b['ext']);
			return $sonuc;
		});

		$iniSet = function ($name, $min, $best, $desc = '') use($that)
		{
			$val = ini_get($name);
			$valInt = intval($val);
			$minInt = intval($min);
			$bestInt = intval($best);
			$that->PhpIniGet[] = array(
				'name'	 => $name,
				'val'	 => $val,
				'min'	 => $min,
				'best'	 => $best,
				'ok'	 => $valInt >= $bestInt ? 'GOOD' : ($valInt >= $minInt ? 'OK' : 'NA'),
				'desc'	 => $desc
			);
		};

		$iniSet('post_max_size', '4M', '8M', 'POST metodu için maksimum büyüklük');
		$iniSet('upload_max_filesize', '8M', '32M', 'Yüklenebilecek maksimum dosya büyüklüğü');
	}

	private function checkFolders()
	{
		$appFolders = Config('app.REQ_FOLDERS');
		$folders = array('apli_dat');
		if ($appFolders)
			$folders = array_merge($folders, explode(',', $appFolders));
		$this->Folders = array();
		foreach($folders as $fold)
		{
			$test = "$fold/__test.txt";
			if (!is_dir($fold))
				$this->Folders[$fold] = 'Klasör yok';
			else if (file_put_contents($test, '') === false)
				$this->Folders[$fold] = 'Yazma izni yok';
			else if (!unlink($test))
				$this->Folders[$fold] = 'Silme izni yok';
			else
				$this->Folders[$fold] = 'OK';
		}
	}

	private function checkExternalPrograms()
	{
		$this->ExternalPrograms = array();
		$this->ExternalPrograms[] = array(
				'ext'	 => 'GhostScript',
				'desc'	 => 'PDF dosyalarını birleştirmek için gerekli program',
				'req'	 => false,
				'ok'	 => CheckGhostScriptExists()
			);
		$this->ExternalPrograms[] = array(
				'ext'	 => 'WkHTMLToPdf',
				'desc'	 => "HTML'den PDF'e dönüştürmek için gerekli",
				'req'	 => false,
				'ok'	 => CheckWkHtmlToPdfExists()
			);
	}

}
