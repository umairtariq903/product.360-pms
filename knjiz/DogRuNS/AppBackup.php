<?php

namespace DogRu;

class AppBackupFile
{
	const T_FULL = 0;
	const T_DIFF = 1;
	const T_SQL = 2;

	public $type = self::T_FULL;
	// 2017_02_13_13_10_Otomatik gibi kısa adı
	public $fileName = '';
	public $tarName = '';
	public $filePath = '';
	public $DiffFiles = null;
	public $fileTime = 0;
	public $displayName = '';
	public $url;

	/** @var \Archive_Tar */
	private $tar = null;
	// prv deki dosya adı
	private $tmpTarName = '';

	public function __construct($file, $parent = null)
	{
		static $useRewrite = null;
		if($useRewrite === null)
		{
			$test = @file_get_contents("http://localhost/backups/test_rewrite");
			if(! $test)
				$test = @file_get_contents("$GLOBALS[SITE_URL]/backups/test_rewrite");
			$useRewrite = $test == 'OK';
		}

		if (!$file)
			return;
		$this->filePath = $file;
		$file = FullPath($file);
		$this->fileTime = filemtime($file);

		$this->tarName = basename($file);
		$this->displayName = preg_replace("/(\-[^-.]+\.)/", '.', $this->tarName);
		if ($parent)
			$this->type = self::T_DIFF;
		else if (preg_match('/-SQL-/', $file))
			$this->type = self::T_SQL;
		else
			$this->type = self::T_FULL;


		$dir = $useRewrite ? "backups/" : 'file.php?f=';
		$this->url = $dir . RelativePath($file, $GLOBALS['APP_BACKUP_DIR']);
	}

	public static function InitNew($type, $auto)
	{
		global $version;
		$cid = CustId();
		$inst = new AppBackupFile();
		$inst->type = $type;
		$inst->fileName = date('Y_m_d_H_i_') . ($auto ? 'Otomatik' : 'Manuel');

		$random = substr(md5($cid . microtime()), 5, 10);
		if ($type == self::T_FULL)
			$random = "Tam$random";
		else if ($type == self::T_DIFF)
			$random = "Fark$random";
		else if ($type == self::T_SQL)
			$random = "SQL-$random";

		$inst->tarName = "V$version-$inst->fileName-$random.tar.gz";

		return $inst;
	}

	public function beginTar()
	{
		ini_set('include_path', FullPath(KNJIZ_DIR . '/others/pear/'));
		require_once 'Archive/Tar.php';
		$this->tmpTarName = FullPath("prv/$this->tarName");
		$this->tar = new \Archive_Tar($this->tmpTarName, TRUE);
	}

	public function addTar($files)
	{
		if (is_string($files))
			$files = array($files);
		$this->tar->add($files);
	}

	public function moveBackup($dir)
	{
		$this->tarName = $newname = \FullPath("$dir/$this->tarName");
		rename($this->tmpTarName, $newname);
	}

	/**
	 * Verilen tam yedeğe ait fark yedek klasorünü verir
	 */
	public function getDiffFolderName()
	{
		$tamYedek = $this->filePath;
		if (strstr($tamYedek, '/') !== false)
			return preg_replace("#(.*)/[^/]+\-([^-]+)\.tar#", '$1/$2', $tamYedek);
		return preg_replace("/.*\-([^-]+)\.tar/", '$1', $tamYedek);
	}

	public function loadDiffFiles()
	{
		if ($this->type != self::T_FULL || $this->DiffFiles !== NULL)
			return;
		$dir = $this->getDiffFolderName();
		$this->DiffFiles = array();
		if (is_dir($dir))
		{
			$farklar = \DosyaSistem::getDirContents($dir, '.*\.tar');
			for($i = 0; $i < count($farklar); $i++)
				$this->DiffFiles[filemtime($farklar[$i])] = new AppBackupFile($farklar[$i], $this);
			krsort($this->DiffFiles);
		}
	}

	public function delTmpFile()
	{
		@unlink($this->tmpTarName);
		return 1;
	}

	public function delete()
	{
		if ($this->type == self::T_FULL)
		{
			$folder = $this->getDiffFolderName();
			if (is_dir($folder))
				\DosyaSistem::KlasorSil($folder);
		}
		$durum = unlink($this->filePath);
		if ($durum)
			return 1;
		else
			return "Dosya silinemedi($this->filePath)";
	}

	public function getFileContents()
	{
		$fl = preg_replace("#.tar(\.gz)?#", ".txt", $this->filePath);
		if (!file_exists($fl))
			return null;
		return explode("\r\n", file_get_contents($fl));
	}

	public function putFileContents($files)
	{
		$fl = preg_replace("#.tar(\.gz)?#", ".txt", $this->filePath);
		file_put_contents($fl, implode("\r\n", $files));
	}

}

class AppBackup
{
	private $Dirs = array('apli_dat');
	private $files = null;
	private $filesSQL = null;
	public $BackupDir = '';

	/**
	 * @return \DogRu\AppBackup
	 */
	public static function Get()
	{
		static $inst = null;
		if (!$inst)
		{
			$inst = new AppBackup();
			$ext = Config('app.BACKUP_EXT_DIRS');
			if ($ext)
				$inst->Dirs = array_merge($inst->Dirs, explode(',', $ext));

			// Yedekleme klasörü
			$inst->BackupDir = IfNull($GLOBALS, 'APP_BACKUP_DIR');

			if (!$inst->BackupDir)
				ThrowException('Yedekleme klasörü belirtilmemiş');
			if (!isDir($inst->BackupDir))
				ThrowException("Yedekleme klasörü yok ($inst->BackupDir)");
		}

		return $inst;
	}

	private function writeInfo($desc, $perc)
	{
		$myFile = "prv/backup.txt";
		@file_put_contents($myFile, serialize(array($desc, $perc)));
	}

	private function readInfo()
	{
		$myFile = "prv/backup.txt";
		$contents = mb_unserialize(file_get_contents($myFile));
		if (strval($contents) == '')
		{
			$desc = '';
			$perc = 0;
		}
		else
		{
			$desc = $contents[0];
			$perc = $contents[1];
		}
		return array($desc, $perc);
	}

	public function checkProgress($from)
	{
		session_write_close();
		try
		{
			list($message, $perc) = $this->readInfo();
			if ($from == 1 && $perc != '0' && preg_match('/\.tar(\.gz)?$/', $message))
			{
				$fileSize = $perc;
				$perc = 100;
				$files = \DosyaSistem::getDirContents("prv", "$message");
				if (count($files) > 0)
				{
					$oran = 0.7;
					$perc = (filesize64($files[0]) / $fileSize / $oran) * 100;
					if ($perc > 99.9)
						$perc = 99.9;
					$perc = round($perc, 1);
				}
			}else if ($from == 2)
				$perc = ceil(100 * $perc);
		}
		catch(Exception $ex)
		{
			$message = 'Hata: ' . $ex->getMessage();
			$perc = 0;
		}

		return json_encode(array($message, $perc));
	}

	public function delete($fileName)
	{
		$fileName = str_replace('backups/', "$this->BackupDir/", $fileName);
		$fileName = str_replace('file.php?f=', "$this->BackupDir/", $fileName);
		$file = new AppBackupFile($fileName);
		return $file->delete();
	}

	public function dbBackup($Ajax, $auto)
	{
		global $MYSQLDUMP_PATH;
		session_write_close();
		\DB::Disconnect();
		\DB::Connect(0);
		set_time_limit(0);
		$error = error_reporting();
		error_reporting(E_ERROR);

		@unlink('prv/backup.txt');

		if (!isset($MYSQLDUMP_PATH))
			$MYSQLDUMP_PATH = Config('app.MYSQLDUMP_PATH');

		$db = \DB::Get();
		$host = $db->Host;
		$user = $db->User;
		$pass = $db->Pass;
		$name = $db->DbName;
		$tema = $_SESSION['TEMA'];

		if ($pass == '')
			return 'Yedekleme yapılabilmesi için VT parolası boş olmamalıdır';

		$tar = AppBackupFile::InitNew(AppBackupFile::T_SQL, $auto);
		$sqlFile = "prv/$name"."_$tema" . "_$tar->fileName.sql";
		$command = $MYSQLDUMP_PATH . "mysqldump --single-transaction -h$host -u$user -p$pass $name > " . FullPath($sqlFile);
		$return = -1;
		$this->writeInfo("Veritabanı yedekleniyor...", '0');
		system($command, $return);
		$this->writeInfo("Veritabanı yedeklendi", '1');
		if ($return != 0)
		{
			$output = '';
			exec($MYSQLDUMP_PATH . "mysqldump", $output);
			if ($Ajax == 1)
				return "Veritabanı yedeklemede hata oluştu($$return).\n" . $command;
			else
				return array(0, $return);
		}
		@chmod(FullPath($sqlFile), 0777);
		// Sadece SQL yedeği ayrıyeten alınıyor
		$tar->beginTar();
		$tar->addTar($sqlFile);
		$tar->moveBackup($this->BackupDir);
		error_reporting($error);
		if ($Ajax == 1)
			return $tar->delTmpFile();
		else
			return array(0 => 1, 'sqlFile' => $sqlFile);
	}

	/**
	 * @param int $auto 1=Otomatik ve 0=Manuel yedek
	 * @param int $yedekTur 0=Tam yedek al, 1=Fark yedek almaya çalış, olmuyorsa tam yedek al
	 * @return int
	 */
	public function backup($auto = 0, $yedekTur = 1)
	{
		$time = microtime(true);
		\Debug::$IsAktif = false;
		if ($auto)
		{
			if (GVar('NO_AUTO_BACKUP'))
				return 1;
			$date = \GenelTablo::GetValue('last_auto_backup');
			if ($date >= \Tarih::Bugun(true))
				return 1;
		}
		$lock = AppSemaphore::begin('backup', 3);
		if(! $lock)
			return "Aynı anda sadece bir yedekleme yapılabilinir. Şuan bir başka yedekleme işlemi yapılmaktadır.";
		$error = error_reporting();
		error_reporting(E_ERROR);
		$VtSonuc = $this->dbBackup(0, $auto);
		if ($VtSonuc[0] == 0)
		{
			$lock->release();
			return "Veritabanı yedeklemede hata oluştu($VtSonuc[1]).\n" . $VtSonuc[1];
		}
		$sqlFile = $VtSonuc['sqlFile'];
		// Sistem tüm yedekleri alınıyor
		$tar = AppBackupFile::InitNew($yedekTur, $auto);
		$files = array();
		foreach($this->Dirs as $folder)
			$files = array_merge($files, \DosyaSistem::getDirContents($folder, '.*', true));
		$target = $tar->tarName;
		if ($yedekTur == AppBackupFile::T_DIFF)
			list($target, $files) = $this->FarkYedekKontrol($tar->tarName, $files);
		else
		{
			$txt = "$this->BackupDir/" . preg_replace("#.tar(\.gz)?#", ".txt", $tar->tarName);
			file_put_contents($txt, implode("\r\n", $files));
		}
		if (!in_array($sqlFile, $files))
			$files[] = $sqlFile;
		// Dosya büyüklüklerini topla
		$toplam = 0.0;
		foreach($files as $file)
		{
			$file = FullPath($file);
			if (is_file($file))
				$toplam += filesize($file);
		}
		$this->writeInfo($tar->tarName, $toplam);
		$tar->beginTar();
		$tar->addTar($files);
		$tar->tarName = $target;
		$tar->moveBackup($this->BackupDir);
		if (is_dir("$this->BackupDir/son_yedek"))
			copy($tar->tarName, "$this->BackupDir/son_yedek/son_yedek.tar.gz");

		// Yedeğe ait temp dosyaları siliniyor
		$nodes = \DosyaSistem::getDirContents('prv', '\.(sql|zip|tar|gz)$');
		foreach($nodes as $node)
			@unlink($node);

		$this->deletePrevBackups();
		\DB::Connect(0); // Yedekleme uzun sürünce bağlantı timeout'a düşüyor
		if ($auto)
			\GenelTablo::SetValue('last_auto_backup', \Tarih::Bugun(TRUE));
		error_reporting($error);
		$lock->release();
		file_put_contents('prv/last_backup_time.txt', round(microtime(TRUE) - $time));
		return 1;
	}

	private function deletePrevBackups()
	{
		// Eski otomatik yedekler siliniyor
		$backups = $this->getFullFiles();
		$sqlBackups = $this->getSqlFiles();
		$max = ConfigCheck('app.MAX_BACKUPS_COUNT', 3);
		for($i = $max; $i < count($backups); $i++)
			$backups[$i]->delete();
		for($i = 15; $i < count($sqlBackups); $i++)
			$sqlBackups[$i]->delete();
	}

	/**
	 * @return AppBackupFile[]
	 */
	public function getFullFiles()
	{
		$this->searchFiles();
		return $this->files;
	}

	/**
	 * @return AppBackupFile[]
	 */
	public function getSqlFiles()
	{
		$this->searchFiles();
		return $this->filesSQL;
	}

	private function searchFiles()
	{
		if (is_array($this->files))
			return;

		$nodes = \DosyaSistem::getDirContents($this->BackupDir, '.*\.tar');
		$files = array();
		foreach($nodes as $node)
		{
			$files[] = $file = new AppBackupFile($node);
			$file->loadDiffFiles();
		}
		\ArrayLib::CustomSort($files, 'fileTime', SORT_DESC);
		$this->files = \ArrayLib::SearchObjList($files, array('type' => AppBackupFile::T_FULL));
		$this->filesSQL = \ArrayLib::SearchObjList($files, array('type' => AppBackupFile::T_SQL));
	}

	/**
	 * Verilen yedek dosya ve içeriğine bakarak, daha önceki bir
	 * tam yedeğin (varsa) devamı olup olamayacağına karar verir
	 * @param string $zipFileName
	 * @param string[] $files
	 * @return array
	 */
	private function FarkYedekKontrol($zipFileName, $files)
	{
		$backupFiles = $this->getFullFiles();
		$tamYedek = null;
		$tamYedekDosyalar = array();
		foreach($backupFiles as $backupFile)
		{
			// 1 haftadan daha eski olan yedek dosyaları ile ilgilenmiyoruz
			$backupDate = date('d-m-Y', $backupFile->fileTime);
			if (\Tarih::FarkVer($backupDate, \Tarih::Bugun()) > 7)
				continue;
			// Yedeğe ait txt dosyası olması gerekiyor
			$contents = $backupFile->getFileContents();
			if ($contents)
			{
				$tamYedekDosyalar = $contents;
				$tamYedek = $backupFile;
				break;
			}
		}

		// Önceden alınmış tam yedek yoksa veya bugün Pazar ise
		// fark yedek alınmayacak
		if (!$tamYedek || date('N') == 7)
		{
			$zipFileName = str_replace('Fark', 'Tam', $zipFileName);
			$tamYedek = new AppBackupFile("$this->BackupDir/$zipFileName");
			$tamYedek->putFileContents($files);
			return array($zipFileName, $files);
		}

		// Tam yedek var, fark yedekleri alarak, gerekli ekleme ve
		// çıkarmaları hesaplayalım
		$folder = $tamYedek->getDiffFolderName();
		if (!is_dir($folder))
			\DosyaSistem::KlasorOlustur($folder);
		// Elimizde tamYedekDosyalar ve files dizisi var, bu iki dizi
		// arasındaki farkı hesaplayıp yeni yedek dosyasını üretiyoruz
		$deleted = array_diff($tamYedekDosyalar, $files);
		$added = array_diff($files, $tamYedekDosyalar);
		$lines = array();
		foreach($added as $file)
			$lines[] = "+:$file";
		foreach($deleted as $file)
			$lines[] = "-:$file";
		$zipFileName = "$folder/$zipFileName";
		$txt = preg_replace("#.tar(\.gz)?#", ".txt", $zipFileName);
		file_put_contents($txt, implode("\r\n", $lines));
		return array(RelativePath($zipFileName, $this->BackupDir), $added);
	}

	public function backupArchive($all = false)
	{
		$lock = AppSemaphore::begin('backup', 3);
		if(! $lock)
			return "Aynı anda sadece bir yedekleme yapılabilinir. Şuan bir başka yedekleme işlemi yapılmaktadır.";
		set_time_limit(0);
		// Tüm yıl/ay klasörlerini al
		$dirs = array();
		$ydirs = \DosyaSistem::getDirContents('apli_dat');
		foreach($ydirs as $yd)
			if (substr($yd, -3) != 'prv')
				$dirs = array_merge($dirs, \DosyaSistem::getDirContents($yd));
		$thisMonth = date('Y-m');
		$prevMonth = date('Y-m', strtotime('last month'));
		// Bunlara ait arşiv yoksa oluştur
		foreach($dirs as $dir)
		{
			$mnt = str_replace('/', '-', substr($dir, -7));
			$activeDir = $mnt == $thisMonth || $mnt == $prevMonth;
			$random = substr(md5($mnt), -8);
			$tarFile = "$this->BackupDir/Archives/BapAppFiles-$mnt-$random.tar.gz";
			if (!($all || $activeDir || !file_exists($tarFile)))
				continue;

			$tmpTarName = FullPath("prv/" . basename($tarFile));
			if (file_exists($tmpTarName))
				unlink($tmpTarName);
			// Dosya büyüklüklerini topla
			$files = \DosyaSistem::getDirContents($dir);
			$toplam = 0.0;
			foreach($files as $fl)
			{
				$fl = FullPath($fl);
				if (is_file($fl))
					$toplam += filesize($fl);
			}
			$this->writeInfo(basename($tarFile), $toplam);
			ini_set('include_path', FullPath(KNJIZ_DIR . '/others/pear/'));
			require_once 'Archive/Tar.php';
			$tar = new \Archive_Tar($tmpTarName, TRUE);
			$tar->add($files);
			\DosyaSistem::KlasorOlustur(dirname($tarFile));
			rename($tmpTarName, $tarFile);
		}
	}

}
