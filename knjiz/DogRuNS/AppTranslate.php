<?php
namespace DogRu;

class AppTranslate
{
	public static $DefaultCode = 'tr';

	public $Code = 'tr';
	public $ItemsDefault = array();
	public $Items = array();

	/**
	 * @return \DogRu\AppTranslate
	 */
	public static function get()
	{
		static $inst = null;
		if ($inst)
			return $inst;
		$inst = new AppTranslate();
		if(isset($_GET['lang']))
		{
			$lang_code = $_GET['lang'];
			$_SESSION['lang_code'] = $lang_code;
			setcookie('lang_code', $lang_code, time() + (3600 * 24 * 30));
		}
		else if(isset($_SESSION['lang_code']))
			$lang_code = $_SESSION['lang_code'];
		else if(isset($_COOKIE['lang_code']))
			$lang_code = $_COOKIE['lang_code'];
		else
			$lang_code = self::$DefaultCode;
		$inst->Code = $lang_code;
		return $inst;
	}

	public function getValue($key, $code = '')
	{
		if ($code == '')
			$code = $this->Code;
		$lang = &$this->Items;
		if(isset($lang[$key]))
			return $lang[$key];
		if(isset($this->ItemsDefault[$key]))
			return $this->ItemsDefault[$key];
		$parts = explode('.', $key);
		if (count($parts) < 2)
			ThrowException ("Hatalı language key isteği: $key");
		$item = array_pop($parts);
		$file = array_pop($parts);
		$file = implode('/', $parts) . $file;
		$file = \App::$Klasor .  "/ui/langs/$code/$file.php";
		// daha önce yüklenmemiş ve dosya varsa yükle
		if (!isset($lang[$file]) && isFile($file))
			$lang[$file] = include $file;
		// Aranan anahtar dizide yoksa default dizi de ara
		if (!isset($lang[$file][$item]) && $code != self::$DefaultCode)
			return $this->getValue($key, self::$DefaultCode);
		// aranan anahtar varsa gönder
		return IfNull($lang[$file], $item);
	}

	public function setValue($key, $value, $code = '')
	{
		$setTr = $code == self::$DefaultCode && $this->Code != self::$DefaultCode;
		if (!$setTr && $code && $code != $this->Code)
			return $value;
		if (is_array($value))
		{
			foreach($value as $k => $v)
				$this->setValue(($key ? "$key." : '') . $k, $v, $code);
			return $value;
		}
		if ($setTr)
			return $this->ItemsDefault[$key] = $value;
		return $this->Items[$key] = $value;
	}
}
