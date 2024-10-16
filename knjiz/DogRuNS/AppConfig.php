<?php
namespace DogRu;

class AppConfig
{
	public $Items = array();

	/**
	 * @return \DogRu\AppConfig
	 */
	public static function get()
	{
		static $inst = null;
		if ($inst)
			return $inst;
		return $inst = new AppConfig();
	}

	public function getValue($key)
	{
		$lang = &$this->Items;
		if(isset($lang[$key]))
			return $lang[$key];
		$parts = explode('.', $key);
		if (count($parts) < 2)
			ThrowException ("Hatalı config key isteği: $key");
		$item = array_pop($parts);
		$file = array_pop($parts);
		$file = implode('/', $parts) . $file;
		$file = \App::$Klasor .  "/configs/$file.php";
		// daha önce yüklenmemiş ve dosya varsa yükle
		if (!isset($lang[$file]) && isFile($file))
			$lang[$file] = include $file;
		return IfNull($lang[$file], $item);
	}

	public function setValue($key, $value, $code = '')
	{
		if ($code && $code != $this->Code)
			return $value;
		if (is_array($value))
		{
			foreach($value as $k => $v)
				$this->setValue(($key ? "$key." : '') . $k, $v);
			return $value;
		}
		return $this->Items[$key] = $value;
	}
}
