<?php
class DosyaSistem
{
	public static $BASE_DIR = '';
	public static $IGNR_DIRS = array('.svn');

	public static function KlasorDegistir($file, $newDir, $chmod = -1)
	{
		$fileName = basename($file);
		self::Tasi($file, "$newDir/$fileName", $chmod);
		return "$newDir/$fileName";
	}

	public static function Tasi($from, $to, $chmod = 0777)
	{
		if($from == $to)
			return TRUE;
		$trans = Transaction::GetCurrent();
		if(file_exists($to))
			self::Sil($to);
		if (! is_dir(dirname($to)))
			DosyaSistem::KlasorOlustur(dirname($to), 0777, true);
		if(! rename($from, $to) && $trans)
			ThrowException("($from => $to) dosya taşıma sırasında bir hata oluştu.");
		if($chmod > 0)
			@chmod($to, $chmod);
		if($trans)
			$trans->FS_AddRollback("@rename('$to', '$from');");
		return TRUE;
	}

	public static function Kopyala($from, $to, $chmod = -1)
	{
		$trans = Transaction::GetCurrent();
		if(file_exists($to))
			self::Sil($to);
		if(! copy($from, $to) && $trans)
			ThrowException("$from dosyası kopyalama sırasında bir hata oluştu.");
		if($chmod > 0)
			chmod($to, $chmod);
		if($trans)
			$trans->FS_AddRollback("@unlink('$to');");
		return TRUE;
	}

	public static function KlasorOlustur($dir, $mode = 0777, $recursive = false)
	{
		if(is_dir($dir))
			return TRUE;
		$trans = Transaction::GetCurrent();
		if(! mkdir($dir, $mode, $recursive) && $trans)
			ThrowException("$dir klasörü oluşturulamadı.");
		if($trans)
			$trans->FS_AddRollback("@unlink('$dir');");
		$perm = substr(sprintf('%o', fileperms($dir)), -4);
		if($perm == $mode)
			return TRUE;
		self::Chmod($dir, $mode);
		return TRUE;
	}

	public static function Sil($file)
	{
		if(! file_exists($file))
			return FALSE;
		$trans = Transaction::GetCurrent();
		if(! $trans)
			return unlink($file);
		// Transaction varsa silme yerine tmp klasörüne taşıyoruz
		$TmpFolder = FullPath('prv/');
		$fileName = $orjName = basename($file);
		$i = 1;
		while(file_exists($TmpFolder . $fileName))
			$fileName = $orjName . '_' . $i++;
		if(rename($file, $TmpFolder . $fileName))
		{
			$trans->FS_AddRollback("@rename('" . $TmpFolder . $fileName . "', '$file');");
			$trans->FS_AddCommit("@unlink('".$TmpFolder . $fileName."');");
		}
	}

	/**
	 *
	 * @param type $dirName
	 * @param type $pattern
	 * @param type $withSubs
	 * @param type $type İstenen içeriğin türü (all, file, folder)
	 * @return array
	 */
	public static function getDirContents($dirName, $pattern = '.*', $withSubs = false, $type = 'all')
	{
		$dir = opendir(FullPath($dirName, self::$BASE_DIR));
		$a = array();
		$rpf = RelativePath($dirName, self::$BASE_DIR);
		if(substr($rpf, -1, 1) != '/')
			$rpf .= '/';
		if (!$dir || in_array($rpf, self::$IGNR_DIRS))
			return $a;
		if(substr($dirName, -1, 1) == '/')
			$dirName = substr($dirName, 0, -1);
		while($f = readdir($dir))
		{
			$path = "$dirName/$f";
			if (($type == 'file' && !is_file($path)) ||
				($type == 'folder' && !is_dir($path)))
				continue;
			$rpf = RelativePath("$path", self::$BASE_DIR);
			if ($f != '.' && $f != '..')
				if(is_dir($path) && $withSubs)
					$a = array_merge($a, self::getDirContents($path, $pattern, TRUE, $type));
				elseif(preg_match("/$pattern/i", $f))
					$a[] = $path;
		}
		closedir($dir);
		return $a;
	}

	public static function getSubDirs($dirName, $all = false)
	{
		if(substr($dirName, -1) == '/')
			$dirName = substr($dirName, 0, -1);
		$dir = opendir($dirName);
		$a = array();
		while($f = readdir($dir))
		{
			$path = "$dirName/$f";
			if ($f != '.' && $f != '..' && is_dir($path))
			{
				$a[] = $path;
				if($all)
					$a = array_merge($a, self::getsubDirs($path, $all));
			}
		}
		closedir($dir);
		return $a;
	}

	/**
	 * Belirtilen klasörü alt klasörü temizler ve istenirse kendisini de siler
	 */
	public static function KlasorSil($dirname, $with_base = true, $with_subs = true) {
		if (!$dirname || ! is_dir($dirname))
			return true;
		$files = self::getDirContents($dirname);
		foreach($files as $file) {
			if ($file != "." && $file != "..") {
				if (is_file($file))
					unlink($file);
				else if ($with_subs)
					self::KlasorSil($file);
			  }
		}
		if($with_base)
			rmdir($dirname);
		return true;
	}

	public static function RealPath($file)
	{
		$file = str_replace('\\', '/', $file);
		$parts = explode('/', $file);
		$f = array();
		foreach($parts as $d)
		{
			if($d === '..'){
				if(count($f) > 0)
					array_pop($f);
			}elseif($d)
				$f[] = $d;
		}
		return implode('/', $f);
	}

	/**
	 * Verile $file, $baseDir klasörünün içinde mi?
	 * @return true|false
	 */
	public static function IsInDir($file, $baseDir)
	{
		$file = self::RealPath($file);
		$baseDir = self::RealPath($baseDir);
		return preg_match("#^$baseDir(/.*)?$#i", $file);
	}

	public static function IsSameDir($dir1, $dir2)
	{
		$dir1 = self::RealPath($dir1);
		$dir2 = self::RealPath($dir2);
		return $dir1 == $dir2;
	}

	public static function Chmod($path, $filemode = 0777, $includeFiles = FALSE)
	{
		if (stristr(PHP_OS, "WIN"))
			return;
		chmod($path, $filemode);
		$dh = opendir($path);
		while (($file = readdir($dh)) !== false)
			if($file != '.' && $file != '..')
			{
				$fullpath = $path.'/'.$file;
				if(is_dir($fullpath))
					self::Chmod($fullpath, $filemode);
				else if($includeFiles)
					chmod($fullpath, $filemode);
			}
		closedir($dh);
	}

	public static function GetExt($file)
	{
		$parts = explode('.', $file);
		return end($parts);
	}
}
