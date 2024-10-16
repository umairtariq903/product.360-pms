<?php

namespace DogRu;

class SysResource
{
	public $ResName;
	public $Free;
	public $Used;
	public $UsedPercent;
	public $Total;

	public function set($free, $total, $size = null)
	{
		$this->Used = GetShortSize($total - $free, $size, 1);
		$this->Free = GetShortSize($free, $size, 1);
		$this->Total = GetShortSize($total, $size, 1);
		$this->UsedPercent = round(100. * ($total - $free) / $total);
	}

}

class SysInfo
{
	public $HostName;
	public $WebService;
	public $AccessedIP;
	public $UpTime;
	public $CpuName;
	public $CpuLoad;
	public $CpuCount;
	/** @var SysResource */
	public $AppMount;
	/** @var SysResource */
	public $RAMInfo;
	public $Error = '';

	/**
	 * @return \DogRu\SysInfo
	 */
	public static function Get()
	{
		if (OS_IsWindows() && !class_exists("COM"))
			ThrowException ('Windows sunucu için php "php_com_dotnet" uzantısı yüklü olması gerek.
				Lütfen sistem yöneticinize bilgi veriniz.');

		static $inst = null;
		if (!$inst)
		{
			$inst = new SysInfo();
			$inst->AppMount = new SysResource();
			$inst->RAMInfo = new SysResource();
		}
		return $inst;
	}

	public function scan()
	{
		require_once KNJIZ_DIR . 'others/linfo/init.php';
		$baseDir = \App::$Klasor;
		try
		{
			$linfo = new \Linfo();
			$linfo->scan();
			$array = $linfo->getInfo();
			\ObjectLib::SetFromArray($this, $array);
			$this->HostName = $array['OS'];
			$this->WebService = $array['webService'];
			$this->UpTime = $array['UpTime']['text'];
			$this->CpuName = $array['CPU'][0]['Model'];
			$this->CpuLoad = (int) $array['Load'];
			$this->RAMInfo->set($array['RAM']['free'], $array['RAM']['total'], 'GB');
			$this->CpuCount = count($array['CPU']);
			$this->AppMount->set(disk_free_space($baseDir), disk_total_space($baseDir));
			$this->WorkDir = $baseDir;
			$this->RawArray = $array;
		}
		catch(LinfoFatalException $e)
		{
			$this->Error = $e->getMessage();
		}
		return $this;
	}
}
