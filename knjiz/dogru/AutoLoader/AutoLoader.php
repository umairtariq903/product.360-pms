<?php
spl_autoload_register(array('AutoLoader', 'Load'));

class ClassesFile
{
	public static $FileName = 'prv/ClassPath.pdt';
	/**
	 * @var ClassesFile
	 */
	private static $Instance = null;

	public $folders = array();
	public $ClassesPath = array();

	/**
	 * @return ClassesFile
	 */
	public static function Get()
	{
		if(self::$Instance)
			return self::$Instance;
		$inst = new ClassesFile();
		$tmp = PhpFileReadArray(self::$FileName);
		if($tmp)
		{
			$inst->folders = $tmp['folders'];
			$inst->ClassesPath = $tmp['paths'];
		}
		return self::$Instance = $inst;
	}

	public function ReConstruct($folders)
	{
		$this->folders = array();
		$this->ClassesPath = array();
		$this->CheckFolders($folders);
	}

	public function CheckFolders($folders)
	{
		global $IgnoreCheckDirs;
		$found = FALSE;
		foreach($folders as $dir)
		{
			if(substr($dir, -1) != '/')
				$dir .= '/';
			if(! isset($this->folders[$dir]) && is_dir($dir))
			{
				if($IgnoreCheckDirs && in_array(basename($dir), $IgnoreCheckDirs))
					continue;
				$found = TRUE;
				$this->AddFolder($dir);
			}
		}
		if($found)
			$this->WriteToPdt();
		return $found;
	}

	private function WriteToPdt()
	{
		ksort($this->ClassesPath);
		$dizi = array(
			'folders' => $this->folders,
			'paths' => $this->ClassesPath
		);
		PhpFileWriteArray(self::$FileName, $dizi);
	}

	private function AddFolder($dirName)
	{
		$this->folders[$dirName] = true;
		$dir = opendir($dirName);
		$subdirs = array();
		while($file = readdir($dir)){
			$fileUrl = $dirName . $file;
			if($file == '.' || $file == '..')
				continue;
			if(is_dir($fileUrl))
				$subdirs[] = $fileUrl;
			else if (eregi2("\.php$", $file))
				$this->CheckNewClasses($fileUrl);
		}
		if(count($subdirs) > 0)
			$this->CheckFolders($subdirs);
	}

	private function CheckNewClasses($fileName)
	{
		foreach(self::GetClassesInFile($fileName) as $class)
			$this->ClassesPath[$class] = $fileName;
	}

	public static function GetClassesInFile($fileName)
	{
		if(!file_exists($fileName))
			return array();
		$cont = file_get_contents($fileName);
		$matches = array();
		if(! preg_match_all('/\n\s*(abstract)?\s*class\s+([a-z0-9_]+)/i', $cont, $matches))
			return array();
		return $matches[2];
	}

	public function LoadClassFile($class)
	{
		if(isset($this->ClassesPath[$class]))
			require_once $this->ClassesPath[$class];
	}
}

class AutoLoader
{
	private static $folders = array();
	private static $changedFolders = false;
	public static $classesFileAge = 0;

	public static function Load($class)
	{
		if(class_exists($class, FALSE))
			return FALSE;
		$clases = ClassesFile::Get();
		if(self::$changedFolders)
			$clases->CheckFolders(self::$folders);
		$clases->LoadClassFile($class);
	}

	public static function AddFolder($dir)
	{
		if(substr($dir, -1) != '/')
			$dir .= '/';
		if(! in_array($dir, self::$folders) && is_dir($dir))
		{
			self::$changedFolders = true;
			self::$folders[] = $dir;
		}
	}

	public static function Register($force = false)
	{
		$clases = ClassesFile::Get();
		if($force)
			$clases->ReConstruct(self::$folders);
		else
			$clases->CheckFolders(self::$folders);
		self::$classesFileAge = filemtime(ClassesFile::$FileName);
		if(LibLoader::IsLoaded(LIB_DEBUG))
			Debug::Begin();
	}

	public static function AddCodeFolder($base, $folders = '', $subFolders = '')
	{
		$base = FullPath($base);
		if(substr($base, -1) != '/')
			$base .= '/';
		if($folders === '' && $subFolders === '')
			return self::AddFolder($base);
		if($folders === '')
			$folders = DosyaSistem::getSubDirs($base);
		$folders = (array)$folders;
		$subFolders = (array)$subFolders;
		foreach($folders as $folder)
		{
			if(!$subFolders)
				self::AddFolder(FullPath($folder, $base));
			foreach($subFolders as $sub)
				self::AddFolder(FullPath("$folder/$sub", $base));
		}
	}

	public static function UnlinkClassPath()
	{
		if (file_exists(ClassesFile::$FileName))
			@unlink(ClassesFile::$FileName);
	}
}


