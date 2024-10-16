<?php
class Tema
{
	public static $Ad = 't-demo';

	public static function FileFullPath($fileName, $isBase = false)
	{
		global $BASE_TEMA;
		if ($isBase)
			return FullPath("themes/$BASE_TEMA/$fileName");
		return FullPath("themes/" . self::$Ad . "/$fileName");
	}

	public static function GetAllThemes()
	{
		$tmp = 'prv/themes.txt';
		if(file_exists($tmp))
			return file_get_contents($tmp);
		$folders = DosyaSistem::getDirContents('themes', 't-.*', false, 'folder');
		$themas = array();
		foreach($folders as $thema)
		{
			$cont = file_get_contents("$thema/t-config.php");
			$regs = array();
			if(preg_match("#BASE_TEMA.*=.*'([a-z\-/]+)'#", $cont, $regs))
				$themas[$regs[1]][] = $thema;
			else if(preg_match("#app.THEMA_BASE.*=>.*'([a-z\-/]+)'#", $cont, $regs))
				$themas[$regs[1]][] = $thema;
			else
				$themas[$thema] = $thema;
		}
		ksort($themas);
		foreach($themas as $key => $value)
			if(is_array($value))
				$themas[$key] = "$key=" . implode(',', $value);
		$folders = implode(';', $themas);
		$folders = str_replace('themes/', '', $folders);
		file_put_contents($tmp, $folders);
		chmod($tmp, 0777);
		return $folders;
	}

	public static function TemplateCheck($template)
	{
		$temp = str_replace(array(App::$Klasor, '\\', 'ui/templates/'), array('', '/', ''), $template);
		$base = self::FileFullPath($temp, TRUE);
		$alternate = self::FileFullPath($temp);
		if (isFile($alternate))
			return $alternate;
		else if (isFile($base))
			return $base;
		else if (isFile($template))
			return $template;
		else
			// Modüllerden de kontrol et
			foreach(\DogRu\AppModules::GetAktifNames() as $moduleName)
			{
				$alternate2 = FullPath('modules/'. $moduleName . '/ui/templates/' . $template);
				if (isFile($alternate2))
					return $alternate2;
			}

		return $template;
	}
}
?>
