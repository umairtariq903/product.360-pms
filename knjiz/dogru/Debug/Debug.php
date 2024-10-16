<?php
require_once 'DebugInfo.php';
//require_once KNJIZ_DIR . 'dogru/Utils/DataTable.php';
class Debug
{
	const FILENAME = 'prv/cisc.txt';

	public static $IsAktif = false;
	public static $WriteMySqlLogs = true;


	public $MaxLevel = 2;
	/**
	 *
	 * @var DebugInfo[]
	 */
	public $Logs = array();
	public $ClientId = '';

	/**
	 * @return Debug
	 */
	public static function GetInstance()
	{
		static $inst = NULL;
		if (!$inst)
		{
			$file = FullPath(self::FILENAME);
			if (file_exists($file))
				$inst = unserialize(file_get_contents($file));
			else
				$inst = new Debug();
		}
		return $inst;
	}

	public function Save()
	{
		$file = FullPath(self::FILENAME);
		// Fazla kayıtları sil
		sort($this->Logs);
		for($i = count($this->Logs) - 1; $i >= 0; $i--)
			if($this->Logs[$i]->Level - $this->Logs[0]->Level >= $this->MaxLevel)
				array_shift($this->Logs);
			else
				break;
		if(@$_GET['act'] != 'cisc' &&  ! self::$IsAktif)
			@unlink ($file);
		elseif(! file_put_contents($file, serialize($this)))
			return "$file dosyasına yazılamadı. Klasör olmayabilir veya yazma izni yoktur.";
		chmod($file, 0777);
		return 1;
	}

	public static function AddDbLog($query, $desc, $cost, $long = '')
	{
		if(! self::$IsAktif)
			return;

		global $LoadBeginTime, $totalCost;
		static $CalledFirst = TRUE, $index = 1;
		if (!isset($_SESSION))
			return;
		if(!isset($_SESSION['DebugLevelId']))
			$_SESSION['DebugLevelId'] = 0;
		if($CalledFirst){
			$_SESSION['DebugLevelId']++;
			$CalledFirst = FALSE;
		}
		@$_SESSION['DebugLogId']++;
		$info = new DebugInfo();
		$info->Id = $_SESSION['DebugLogId'];
		$info->Level = $_SESSION['DebugLevelId'];
		$info->Index = $index++;
		$info->Cost = $cost;
		$info->Description = $desc;
		$info->LongDesc = $long;
		$info->Query = $query;
		if($query){
			$info->Error = DB::Error();
			if (!$info->Error)
				$info->RowCount = DB::LastAffectedRowCount();
		}
		$info->Time = 1000 * (microtime(true) - $LoadBeginTime);
		$info->TotalCost = $totalCost;
		$info->Memory = memory_get_usage();
		$info->MemoryPeak = memory_get_peak_usage();
		$info->BackTree = PhpErrorList::CallStackArray();

		$dbg = self::GetInstance();
		array_push($dbg->Logs, $info);
	}

	public static function AddLog($message = '')
	{
		self::AddDbLog('', $message, 0);
	}

	public static function Begin()
	{
		if(@$_POST['cpaint_function'] == 'OnIdle' || @$_GET['act'] == 'cisc')
			self::$IsAktif = false;
		else if (isset($_SERVER['HTTP_USER_AGENT']) && isset($_SERVER['REMOTE_ADDR']))
		{
			$uid = md5($_SERVER['HTTP_USER_AGENT'] .  $_SERVER['REMOTE_ADDR']);
			$dbg = Debug::GetInstance();
			if(isset($_GET['cisc'])){
				self::$IsAktif = $_GET['cisc'] == 'dev' && @$GLOBALS['DebugIsActive'];
				$dbg->ClientId = $uid;
				$dbg->Save();
			}elseif($dbg->ClientId == $uid)
				self::$IsAktif = true;
		}
		if (self::$IsAktif)
		{
			error_reporting(E_ALL^E_DEPRECATED);
			ini_set('display_errors', 'On');
		}
		else
			error_reporting(E_ERROR);

		$info['gets'] = $_GET;
		$info['posts'] = $_POST;
		$info = serialize($info);
		self::AddDbLog('', 'Başlangıç', 0, $info);
	}

	public static function End()
	{
		self::GetInstance()->Save();
	}
}

if(@$_GET['act'] == 'cisc')
{
	if(IfNull($GLOBALS, 'DebugIsActive') != true)
		die('Debug sayfasına ulaşılamadı');
	LibLoader::Load(LIB_PAGECONTROLLER);
	PageRouter::$PageDir = dirname(__FILE__) . '/page/';
}

