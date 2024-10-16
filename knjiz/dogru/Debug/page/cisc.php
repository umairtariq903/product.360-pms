<?php
class DebugLogPage extends PageController
{
	public function __construct($tpl, $dir, $acts)
	{
		parent::__construct($tpl, $dir, $acts);
		if(@$_GET['act2'] == 'error')
			$this->LoadErrors();
		else
			$this->LoadDbDebug();
	}

	private function LoadDbDebug()
	{
		$debug = Debug::GetInstance();
		$grid = new DataTableArray();
		$grid->Id = 'tb_db_log';
		$grid->VisibleColumns = array(
			'Id',
			'Level;D:Seviye',
			'Index',
			'Description;D:Açıklama',
			'Cost;D:Maliyet',
			'TotalCost;D:Toplam Maliyet',
			'Time;D:Zamam',
			'RowCount;D:Satır Sayısı',
			'Error;D:Hata',
			'Query;V:false'
			);
		$grid->RowAttributes = array('row_id' => 'Id');
		$grid->GetColumns('DebugInfo');
		$grid->RealData = &$debug->Logs;
		$this->DataTables['tb_db_log'] = $grid;
	}

	private function LoadErrors()
	{
		$phpErr = PhpErrorList::Get()->List;
		$grid = new DataTableArray();
		$grid->VisibleColumns = array(
			'ErrNo;D:Hata No',
			'ErrCode;D:Hata Kodu',
			'ErrTime;D:Hata Zamanı',
			'ErrStr;D:Açıklama'
		);
		$grid->RowAttributes = array('row_id' => 'Id');
		$grid->GetColumns('PhpError');
		$grid->RealData = &$phpErr;
		$grid->Id = 'tb_err_log';
		$this->DataTables['tb_err_log'] = $grid;
	}

	/**
	 *
	 * @param PhpError $realData
	 * @param PhpError $rowData
	 * @param object $attributes
	 * @param DataTable $dataTable
	 * @return string
	 */
	public function DataRenderRow($realData, $rowData, $attributes, $dataTable)
	{
		if ($dataTable->Id == 'tb_err_log')
			$rowData->ErrCode = $realData->GetTypeStr();
	}

	public function Index()
	{
		$this->debug = Debug::GetInstance();
		$this->PhpErrors = PhpErrorList::Get();
	}

	public static function ChangeMaxLevel($level)
	{
		$dbg = Debug::GetInstance();
		$dbg->MaxLevel = intval($level);
		return $dbg->Save();
	}

	public static function TmpClear($onlyTmp)
	{
		if(! $onlyTmp)
			DosyaSistem::KlasorSil('ui/templates_c', false);
		$files = DosyaSistem::getDirContents('prv');
		foreach($files as $file)
			if($file != 'cisc.txt' && $file != 'updater.php' && is_file($file))
				unlink($file);
		$debug = Debug::GetInstance();
		$debug->Logs = array();
		$debug->Save();
		return 1;
	}
}
