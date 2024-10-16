<?php
class FileCache
{
	public static $FileName = 'prv/file_cache.pdt';
	private static $List = null;
	private static $Changed = false;

	public static function isFile($fileName)
	{
		return self::getMTime($fileName, FALSE) > 0;
	}

	public static function isDir($dirName)
	{
		return self::getMTime($dirName, TRUE) == 1;
	}

	public static function getMTime($file, $isDir = false)
	{
		if(self::$List === NULL)
			self::Init();
		$file = addslashes($file);
		$tm = @self::$List[$file];
		if($tm === NULL)
			$tm = self::Add($file, $isDir);
		return $tm;
	}

	public static function Save()
	{
		if(! @$GLOBALS['useFileCache'] || ! self::$Changed)
			return 0;
		ksort(self::$List);
		$List = array();
		foreach(self::$List as $key => $value)
			$List[] = "'$key'=>$value";
		$content = '<?php $List=array('. implode("\n,", $List) .');';
		return file_put_contents(self::$FileName, $content);
	}

	private static function Init()
	{
		$List = array();
		if(@$GLOBALS['useFileCache'] && is_file(self::$FileName))
			include self::$FileName;
		self::$List = $List;
	}

	private static function Add($file, $isDir = false)
	{
		if($isDir)
			$fileAge = is_dir($file) ? 1 : 0;
		else
			$fileAge = is_file($file) ? filemtime($file) : 0;
		self::$Changed = TRUE;
		return self::$List[$file] = $fileAge;
	}
}
