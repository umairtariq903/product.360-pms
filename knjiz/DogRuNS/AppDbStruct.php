<?php

namespace DogRu;

/**
 * VT tablo alanlarının versiyon ile uymunu kontrol eder
 */
class AppDbStruct
{
	public static $FILE_STR = 'update/bap_structure.sql';
	const FILE_TMP = 'prv/db_struct.sql';

	public $appVersion = '';
	public $fileVersion = '';

	public static function Get()
	{
		static $ints = null;
		if ($ints)
			return $ints;
		if(Config('app.VT_STRUCTURE') == "")
			die("Config'de VT_STRUCTURE belirtilmemiş.");
		self::$FILE_STR = Config('app.VT_STRUCTURE');
		$ints = new AppDbStruct();
		$ints->appVersion = $GLOBALS['version'];
		$struct = file_get_contents(self::$FILE_STR);
		$s1 = explode("\n", $struct);
		$r = array();
		if (preg_match('#/\*([0-9]+)\*/#', $s1[0], $r))
			$ints->fileVersion = $r[1];
		return $ints;
	}

	public static function checkDb($dbVsFile)
	{
		$struct1 = file_get_contents(self::$FILE_STR);
		$struct2 = self::PutDbStruct(self::FILE_TMP);
		if ($dbVsFile)
			Swap($struct1, $struct2);
		$updater = new \dbStructUpdater();
		$resTbl = $updater->compareTablesStruct($struct2, $struct1);
		if (!$resTbl)
			$resTbl = $updater->getUpdates($struct2, $struct1);
		return $resTbl;
	}

	private static function PutDbStruct($fileName)
	{
		$db = \DB::Get();
		$tables = $db->FetchArray("SHOW TABLES FROM `$db->DbName`");
		$vers = self::Get()->appVersion;
		$list = array("/*$vers*/");
		foreach($tables as $t)
		{
			$table = $db->FetchSingle("SHOW CREATE TABLE `$t[0]`");
			$table = preg_replace('/ AUTO_INCREMENT\=[0-9]+/', '', $table[1]);
			$list[] = "/*Table structure for table `$t[0]` */\n$table;";
		}
		$struct = implode("\n", $list);
		file_put_contents($fileName, $struct);
		return $struct;
	}

	public static function CreateDbStruct($crtUpdate)
	{
		if ($crtUpdate)
		{
			global $version;
			$version++;
			$folder = 'update/' . (25 * floor($version / 25));
			\DosyaSistem::KlasorOlustur($folder);
			$file = "$folder/upd-$version-" . date('ymd') . '.sql';
			$upds = self::checkDb(true);
			file_put_contents($file, implode("\n/***/\n", $upds));
			$contents = file_get_contents('index.php');
			$contents = preg_replace('/\$version = ([0-9]*);/', "\$version = $version;", $contents);
			file_put_contents('index.php', $contents);
		}
		self::PutDbStruct(self::$FILE_STR);
		return 1;
	}

	public static function updateDb()
	{
		set_time_limit(0);
		$dbs = self::Get();
		if ($dbs->fileVersion <> $dbs->appVersion)
			return "Şablon dosyası uygula versiyonundan farklı
				(Şablon: $dbs->fileVersion, Uygulama: $dbs->appVersion).";
		$alters = self::checkDb(false);
		\DB::$Updating = TRUE;
		\Transaction::Commit();
		$errors = array();
		foreach($alters as $alt)
		{
			try
			{
				\DB::Execute($alt);
			}
			catch(Exception $e)
			{
				$errors[] = $e->getMessage();
			}
		}
		if ($errors)
			return implode("\n", $errors);
		return 1;
	}

}
