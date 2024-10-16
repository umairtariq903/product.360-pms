<?php

class DirInfo
{
	public $IgnrFile = array();
	public $IgnrDir = array();

	public function __construct($Ignrs = '')
	{
		$this->AddIgnrs($Ignrs);
	}

	public function AddIgnrs($ignr = '')
	{
		$ignr = explode("\n", $ignr);
		foreach($ignr as $ig)
		{
			$tur = '-d';
			$items = $ig = trim($ig);
			if(! $items)
				continue;
			$regs = array();
			preg_match('/(\-f|\-d):(.+)/', $ig, $regs);
			if(count($regs) == 3)
			{
				$tur = $regs[1];
				$items = $regs[2];
			}
			$items = explode(';', $items);
			foreach ($items as $item)
				if($tur == '-d')
				{
					$base = explode('|', $item);
					$ig = array();
					if(count($base) > 1)
						$ig = explode(',', $base[1]);
					$this->IgnrDir[$base[0]] = $ig;
				}
				else
					$this->IgnrFile[] = $item;
		}
	}

	public function InIgnrList($file, $isDir)
	{
		if(in_array($file, $this->IgnrFile))
			return true;
		foreach ($this->IgnrDir as $base => $ig_dirs)
			if(DosyaSistem::IsInDir($file, $base))
			{
				$cnt = count($ig_dirs) == 0;
				// kökteki bir dosya ise silinecek
				if(dirname($file) == $base && ! $isDir)
					return $cnt;

				// İstisna klasörleri kontrol et
				foreach ($ig_dirs as $ig)
					if(DosyaSistem::IsInDir($file, "$base/$ig"))
						return false;

				return ! DosyaSistem::IsSameDir($file, $base) || $cnt;
			}
		return false;
	}

	public function ListFiles($base, $dir = '')
	{
		if(preg_match('#/$#', $base))
			$base = substr ($base, 0, -1);
		if ($dir == '')
			$dir = $base;
		$files = Array();
		$dh = opendir($dir);
		if(! $dh)
			return $files;
		$inner_files = Array();
		while($file = readdir($dh))
		{
			if($file != "." && $file != ".." && $file != '.svn')
			{
				if(is_dir($dir . "/" . $file))
				{
					$subDir = $dir . "/" . $file;
					$rsub = RelativePath($subDir, $base);
					if($this->InIgnrList($rsub, TRUE))
						continue;
					$inner_files = $this->ListFiles($base, $subDir);
					$files = array_merge($files, (array)$rsub, $inner_files);
				}
				else
				{
					$Name = $dir . "/" . $file;
					$Age = filemtime($Name);
					$Size = filesize($Name);
					$Name = RelativePath($Name, $base);
					if($this->InIgnrList($Name, FALSE))
						continue;
					$files[] = "$Name,$Age,$Size";
				}
			}
		}
		closedir($dh);
		return $files;
	} // function

	 public static function IndexFiles($array, $onlySize = false)
	{
		$array2 = array();
		foreach($array as $fileInfo)
		{
			$parts = explode(',' , $fileInfo);
			$key = $parts[0];
			if($onlySize)
				$array2[$key] = count($parts) == 3 ? $parts[2] : '';
			else
				$array2[$key] = $fileInfo;
		}
		return $array2;
	}
}