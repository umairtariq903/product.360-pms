<?php
/**
 * Uygulamaya özel ayarlar sınıfı
 */
class App
{
	/**
	 * @var string Çalışma klasörü
	 */
	public static $Klasor = '';
	public static $TmpDir = '';
	public static $URL = '';
	public static $WS_URL = '';
	public static $Kod = '';
	public static $Encoding = '';
	public static $QueryParamValueFunc = null;

	public static function Init()
	{
		global $SITE_KLASORU;
		if(isset($SITE_KLASORU))
			$base = $SITE_KLASORU;
		else
		{
			$base = dirname($_SERVER["SCRIPT_FILENAME"]) . '/';
			$base = str_replace('\\', '/', $base);
			// Windows üzerinde c:\, d:\ gibi path tanımları
			// C:\, D:\ şeklinde verilmesi durumunda, bu ifadeyi küçük
			// harfe çeviriyoruz
			if (preg_match("#^([A-Z]{1}):#", $base))
				$base = strtolower($base[0]) . substr($base, 1);
		}
		self::$Klasor = $base;
		self::$TmpDir = "$base/prv/";
		$path = pathinfo($_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
		self::$URL = $path['dirname'];
		self::$WS_URL = "http://" . self::$URL . '/ws.php';
	}

	public static function IsUTF8()
	{
		return strtolower(self::$Encoding) == 'utf-8';
	}

	public static function End($msg = '')
	{
		//if(! $_POST)
		//	echo isFile(NULL);
		FileCache::Save();
		ThrowException($msg);
	}

	/**
	 * Browser'a ekrana gönderilecek yeni birşey kalmadı bilgilisi gönder
	 * ve bağlantıyı kapat
	 */
	public static function EndFlush()
	{
		session_write_close();
		header('Connection: close');

		// flush all output
		@ob_end_flush();
		@ob_flush();
		@flush();
		// Bu aşamadan sonra yavaş sorguları da bildirmesin
		DB::$SEND_HEAVY_QUERY = FALSE;
	}

	public static function IsProduction()
	{
		if (func_num_args() > 0)
			$GLOBALS['STATUS'] = func_get_arg(0) ? 'PRODUCTION' : 'TEST';
		return $GLOBALS['STATUS'] == 'PRODUCTION';
	}

}