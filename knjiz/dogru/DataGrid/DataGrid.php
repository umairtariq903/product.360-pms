<?php
/**
 * @property bool $MultipleSelect
 */
class DataGrid
{
	const EXPORT_XLS = 'xls';
	const EXPORT_PDF = 'pdf';
	const EXPORT_WORD = 'doc';

	const VERSION_1		= 1;
	const VERSION_1_10	= 2;
	/**
	 * @var DataTable
	 */
	public $DataTable = null;
	public $Sorting = array();
	/**
	 * @var string satır(tr) tıklanması durumunda çalışacak olan JS fonksiyon adı.<br>
	 * tr nin row_id sini ve kendisini parametre olarak fonksiyona otomatik gönderilir.
	 * @example function RowClicked(row_id, tr)
	 */
	public $RowClickFunc = null;

	public $GridLoadFunc = null;
	public $CustomRenderFunc = null;
	public $CustomPagingFunc = null;
	public $GridIslemButton = null;

	public $ShowHeader = true;
	public $ShowFooter = true;
	public $ShowSearch = true;
	public $ShowAdvSearch = true;
	public $ShowPaging = true;
	public $Sortable = true;
	public $ShowNewButton = false;
	public $NewButtonClickFunc = '';
	public $RefreshPageFunc = '';
	public $NewButtonText = 'Yeni Ekle';
	protected $MultipleSelect = false;
	protected $ShowRowNo = false;
	public $ShowToolBar = true;
	public $FullScreen = false;
	public $OutputExcel = true;
	public $OutputPdf	= true;
	public $OutputWord	= true;
	public $PageSize	= 10;
	public $GridVersion = self::VERSION_1;
	public $ExportedDataRender = true;
	public $AutomaticStaticGrid = false;

	/**
	 *
	 * Gönderilen parametreler
	 */
	protected $paramSenderNo = 1;
	protected $paramOrderBy = -1;
	protected $paramOrderDir= 'asc';
	protected $paramStart = 0;
	protected $paramPageSize = 20;
	public $paramSelectedFiels = array();
	protected $paramSearch = '';
	protected $tableId = '';
	protected $paramColSizes = null;

	public function __construct($data)
	{
		$this->DataTable = $data;
	}

	public function GetExternalFile($params, $type = DataGrid::EXPORT_XLS)
	{
		ini_set('memory_limit','1024M');
		set_time_limit(0);
		$params = $this->ProcessParams($params);
		$exportObj = null;
		if ($type == DataGrid::EXPORT_XLS)
			$exportObj = new ExcelWrap();
		else if ($type == DataGrid::EXPORT_PDF)
			$exportObj = new PdfWrap($params['table_id']);
		else if ($type == DataGrid::EXPORT_WORD)
			$exportObj = new DocWrap();
		$selectedFields = null;
		$pageSize = 250;
		$pageCount = -1;
		$page = 1;
		while($page <= $pageCount || $pageCount == -1)
		{
			$start = ($page - 1) * $pageSize;
			$this->DataTable->Loaded = false;
			$list = $this->DataTable->GetPagedData(
				$params, $this->paramOrderBy, $this->paramOrderDir, $start, $pageSize,
				$this->paramSelectedFiels,true);
			$records = $list->RealDataOrj;
			$records2 = $this->DataTable->RealData;
			if ($selectedFields === null)
			{
				$selectedFields = array();
				foreach($this->DataTable->Columns as $name => $col)
					if ($this->paramSelectedFiels == null || in_array($name, $this->paramSelectedFiels))
						$selectedFields[$name] = $col->DisplayName;
				$exportObj->Prepare($selectedFields, $params['table_id'], $this->paramColSizes);
				$pageCount = $list->PageCount;
			}
			foreach($records as $key => $obj)
			{
                $obj2 = clone $records2[$key];
				if ($this->ExportedDataRender && is_callable($this->DataTable->CbRenderRow))
					call_user_func($this->DataTable->CbRenderRow, $obj, $obj2, new stdClass(), $this->DataTable);
				$exportObj->WriteRow($obj2, true);
			}
			// Bir sonraki sayfaya geç
			$page++;
		}
		$exportObj->Finish($params['table_id'], $this->paramColSizes);
	}

	protected function ProcessParams($params)
	{
		// Gönderilenler
		Kodlama::KarakterKodlamaDuzelt($params);
		$this->paramSenderNo  = intval(@$params['sEcho']);
		if (!$this->paramSenderNo)
			$this->paramSenderNo = 1;
		$this->paramStart = intval(@$params['iDisplayStart']);
		$this->paramPageSize = intval(@$params['iDisplayLength']);
		if ($this->paramPageSize <= 0)
			$this->paramPageSize = $this->PageSize;
		$this->tableId = @$params['table_id'];
		if(intval(@$params['iSortingCols']) > 0)
			$this->paramOrderBy  = intval(@$params['iSortCol_0']);
		$this->paramOrderDir = @$params['sSortDir_0'];
		$sColumns = @$params['sColumns'];
		if(@$params['sSearch'] != '')
			$params['sorgu'] = $this->paramSearch = $params['sSearch'];
		if (is_callable($this->DataTable->CbProcessParam))
		{
			$params = ModelParam::Get($params);
			call_user_func ($this->DataTable->CbProcessParam, $params);
			$params = (array)$params;
		}

		if ($sColumns)
		{
			$sColumns = explode(',', $sColumns);
			$cols = array();
			$i = 0;
			foreach($sColumns as $colName)
			{
				$cols[$colName] = $this->DataTable->Columns[$colName];
				$cols[$colName]->ColIndex = $i++;
			}
			$this->DataTable->Columns = $cols;
		}
		$visibleCols = @$params['selected_fields'];
		if ($visibleCols)
			$this->paramSelectedFiels = explode(',', $visibleCols);
		$colSizes = @$params['field_sizes'];
		if ($colSizes)
			$this->paramColSizes = explode(';', $colSizes);
		return $params;
	}

	public function GetJSON($params)
	{
		$params = $this->ProcessParams($params);
		$list = $this->DataTable->GetPagedData($params,
			$this->paramOrderBy, $this->paramOrderDir,
			$this->paramStart, $this->paramPageSize);
		$sonuc = new stdClass();
		$sonuc->sEcho = $this->paramSenderNo;
		$sonuc->iTotalRecords = $list->RecordCount;
		$sonuc->iTotalDisplayRecords = $list->RecordCount;
		$sonuc->aaData = $list->Records;
		$sonuc->rowAttributes = $this->DataTable->RowAttributes;
		$sonuc->rowButtons = $this->DataTable->RowButtons;
		$sonuc->tdsAttributes = $this->DataTable->TdsAttributes;
		$sonuc->tableId = $this->tableId;
		$sonuc->summary = $list->Summary;
		if (@$_GET['onlyData'] == '1')
			return json_encode ($list->Records);
		return Kodlama::JSON($sonuc, false);
	}
	/**
	 * @param string $tableId verinin aktarılacağı table id
	 */
	public function Render(PageController $pageCtrl, $tableId)
	{
		$dt = $this->DataTable;
		$dt->Page = $pageCtrl;
		$dataCopy = null;
		if ($dt->StaticGrid)
		{
			$params = $this->ProcessParams($_GET);
			$list = $dt->GetPagedData($params,
				$this->paramOrderBy, $this->paramOrderDir,
				$this->paramStart, $dt->DataGrid->PageSize);
			$dataCopy = $dt->Data;
			$dt->DataGrid->ShowToolBar = false;
			$dt->DataGrid->ShowFooter = false;
			$dt->DataPageInfo = $list;
			$dt->DataPageInfo->Data = null;
		}
		$dt->Data = null;
		if (! $this->ShowPaging)
		{
			if($dt->CbRenderRow == NULL)
				$dt->CbRenderRow = array($pageCtrl, 'DataRenderRow');
			if ($dt->CbProcessParam == NULL)
				$dt->CbProcessParam = array($pageCtrl, 'DataProcessParam');
			$params = ModelParam::Get($_GET);
			call_user_func ($this->DataTable->CbProcessParam, $params);
			$params = (array)$params;
			$i = 0;
			foreach($dt->Columns as $col)
				$col->ColIndex = $i++;
			$list = $dt->GetPagedData($params, -1, '', 0, 1e10);
			$dt->Data = $list->Records;
			$dt->Summary = $list->Summary;
		}
		$dt->RealData = null;
		$dt->CbRenderRow = null;
		$dt->CbProcessParam = null;
		$islemler = @$dt->Columns['islem'];
		if($islemler && isset($islemler->Buttons))
			$islemler->Buttons = array_values($islemler->Buttons);
		$this->LoadState($pageCtrl);
		if ($this->GridVersion == self::VERSION_1 || @$_GET['GridVersion'] == 1)
			$pageCtrl->AddResource(JS_DATATABLES);
		else
			$pageCtrl->AddResource(JS_DATATABLESV1_10);
		$pageCtrl->AddJsOnloadFunc("JqDataTable('$tableId', \"".
			addslashes(Kodlama::JSON($this, false))."\");");
		if ($dt->StaticGrid)
			$dt->Data = $dataCopy;
	}

	private function LoadState(PageController $pageCtrl)
	{
		$key = @$_SESSION['UserUniqueKey'];
		if(!$key || $this->CustomRenderFunc)
			return;
		$key .= addslashes('?' . $pageCtrl->GetStateURL());
		$query = "SELECT deger FROM user_storage WHERE id='$key'";
		$state = DB::FetchScalar($query);
		if(! $state)
		{
			// Sıralama için bir sütun seçilmişse, AppPage'de seçilen sütunu al
			$idx = 0;
			foreach($this->DataTable->Columns as $col)
				if ($idx++ >= 0 && in_array($col->OrderBy, array('ASC', 'DESC')))
					$this->Sorting[] = array($idx-1, $col->OrderBy);
			return;
		}
		try
		{
			$state = json_decode($state);
			$cols = array();
			$keys = array_keys($this->DataTable->Columns);
			$pinned = array();
			if (isset($state->pinnedCols))
				$pinned = $state->pinnedCols;
			foreach($state->columns as $col)
			{
				$colName = Kodlama::KodlamaDuzelt($col->sName);
				$dcol = @$this->DataTable->Columns[$colName];
				if($dcol)
				{
					$cols[$colName] = $dcol;
					unset($this->DataTable->Columns[$colName]);
					$dcol->Width = $col->sWidth;
					$dcol->Visible = $col->bVisible;
				}
			}

			foreach($this->DataTable->Columns as $name => $col)
			{
				$idx = array_search($name, $keys);
				$cols = array_slice($cols, 0, $idx, true) +
					array($name => $col) +
					array_slice($cols, $idx, count($cols) - 1, true) ;
			}

			foreach($cols as $name => $col)
				$col->Pinned = in_array($name, $pinned) ? 1 : 0;

			$this->DataTable->Columns = $cols;
			$sortList = (array)$state->sort;
			foreach($sortList as $sort)
			{
				if (! is_array($sort))
					continue;
				$colName = $sort[0];
				if (! is_string($colName))
					continue;
				$idx = array_search($colName, array_keys($this->DataTable->Columns));
				if($idx !== false)
					$this->Sorting[] = array($idx, $sort[1]);
			}
		}catch(Exception $ex){$ex = '';}
	}

	public function __get($name)
	{
		return $this->{$name};
	}

	public function __set($name, $value)
	{
		if ($name == 'MultipleSelect' && $value)
		{
			$col = $this->DataTable->AddColumn('Sec', new DataColumnCheckbox(), array(), 0);
			$col->DisplayName = 'Seç';
			$col->Align = 'center';
			$col->Width = '30px';
			$col->Searchable = false;
			$col->Sortable = false;
			$col->Align = 'center';
			$this->MultipleSelect = true;
		}
		if ($name == 'ShowRowNo' && ! $this->ShowRowNo)
		{
			$col = $this->DataTable->AddColumn('SiraNo', 'VarInt', array(), 0);
			$col->DisplayName = 'S.No';
			$col->Width = '30px';
			$col->Searchable = false;
			$col->Sortable = false;
			$col->Align = 'center';
			$this->ShowRowNo = true;
		}
	}
}

