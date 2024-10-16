<?php
require_once 'DataTableBase.php';
/**
 * @property DataGrid $DataGrid
 */
class DataTable extends DataTableBase
{
	public $Id = '';

	/**
	 * @var array[] 2 boyutlu veri dizisi
	 */
	public $Data = null;
	/**
	 *
	 * @var mixed[] 2 boyutlu orijinal veri dizisi
	 */
	public $RealData = null;

	/**
	 * Data ile ilgili sayfalama bilgisi (aktif sayfa no, toplam sayfa no v.b.)
	 * @var PagedData
	 */
	public $DataPageInfo = null;
	/**
	 * Data Alan bilgileri
	 * @var DataColumn[]|DataField[]
	 */
	public $Columns = array();

	/**
	 * Gizli alanlar. table.row a attribute olarak eklenir. <br>
	 * array('Id') => row_Id olarak otomatik adlandırılır.
	 * @var array
	 */
	public $RowAttributes = array('row_id' => 'Id');

	/**
	 * Her satıra özgü button listesi
	 *
	 * @var BtnIslem[]|stdClass[]
	 */
	public $RowButtons = array();

	public $TdsAttributes = array();
	public $Loaded = false;
	/**
	 *
	 * @var DataGrid
	 */
	protected $DataGrid = null;
	/**
	 * Örnek olarak şöyle bir fonksiyon verilmelidir (Sınıf metodu veya doğrudan fonksiyon)
	 * <br>
	 * <br>
	   <b>function <i>RenderCell</i></b>
	 *	(Object <b>$realData</b>,
	 *   Object <b>$rowData</b>,
	 *   Object <b>$attributes</b>,
	 *   DataTable <b>$dataTable</b>)
	 * @var callable Satır ekrana gönderilmeden önce render eden fonksiyon
	 */
	public $CbRenderRow = null;

	/**
	 * Her satır ekrana gönderilmeden önce, o satıra ait menü/işlem listesi
	 * bu callback fonksiyonuna verilerek, menüde, satıra özel değişiklikler (silme, düzenleme v.b.)
	 * yapılabilir
	 *
	 * @var callable
	 */
	public $CbRenderRowMenu = null;

	/**
	 *
	 * @var callable Parametreler veritabanına gönderilmeden önce son müdahale eden
	 *  fonksiyon
	 */
	public $CbProcessParam = null;

	/**
	 * Grid'in Ajax ile dinamik olarak yüklenip yüklenmeyeceğini belirtir
	 * @var int
	 */
	public $StaticGrid = 0;

	/**
	 * Sorgu sonucu dönen özet bilgi
	 * @var array
	 */
	public $Summary = null;

	public function EnableSEOGrid()
	{
		$searchengines = array(
			'Googlebot',
			'Slurp',
			'search.msn.com',
			'nutch',
			'simpy',
			'bot',
			'ASPSeek',
			'crawler',
			'msnbot',
			'Libwww-perl',
			'FAST',
			'Baidu',
		);
		$agent = @$_SERVER['HTTP_USER_AGENT'];
		$flag = false;
		if (! $agent)
			$flag = true;
		else
			foreach($searchengines as $engine)
				if (preg_match("/$engine/i", $agent))
					$flag = true;
		if ($flag || isset($_GET['StaticGrid']) || IfNull($GLOBALS, 'RunAsSEO'))
		{
			$this->StaticGrid = 1;
			$this->DataGrid->RowClickFunc = '';
			$this->DataGrid->PageSize = 20;
		}
		return $this->StaticGrid;
	}

	/**
	 *
	 * @return DataColumnActions
	 */
	public function AddColumnActions()
	{
		return $this->AddColumn('islem', new DataColumnActions());
	}

	/**
	 * @return DataColumnActions
	 */
	public function GetActColumn()
	{
		return $this->Columns['islem'];
	}

	public function RemoveActButton($index)
	{
		unset($this->Columns['islem']->Buttons[$index]);
	}

	public function RemoveActButtonByName($name)
	{
		$name = LowerCase($name);
		for($i = count($this->Columns['islem']->Buttons) - 1; $i >= 0; $i--)
		{
			$btn = $this->Columns['islem']->Buttons[$i];
			/* @var $btn BtnIslem */
			if (LowerCase($btn->text) == $name ||
				LowerCase($btn->CallBackFunc) == $name)
			{
				array_splice($this->Columns['islem']->Buttons, $i, 1);
				return $btn;
			}
		}
		return null;
	}

	public function LoadDisplayText($changeRealData = false)
	{
		if($this->Loaded || !$this->Data)
			return;
		$rowAttr = array();
		$rowButtons = array();
		$tdsAttr = array();
		foreach($this->Data as $index => $row)
		{
			$realData = $this->RealData[$index];
			$attr = $this->RowAttributes;
			if (!array_key_exists('row_id', $attr) &&
				property_exists($realData, 'Id'))
				$attr['row_id'] = 'Id';
			foreach($attr as $name => $propName)
				if (is_string($propName) && property_exists($realData, $propName))
					$attr[$name] = $realData->{$propName};
			$colsAttr = array();
			foreach($this->Columns as $col)
			{
				$val = '';
				if (isset($row[$col->ColIndex]))
					$val = $row[$col->ColIndex];
				if ($col->DataBound == false &&
					property_exists($realData, $col->DataFieldName))
					$val = $realData->{$col->DataFieldName};
				if ($col->Editable)
					$row2[$col->Name] = $col->GetFormItem()->val($val)->html();
				else
					$row2[$col->Name] = $col->ToStr($val, $realData, $col->Name);
				$cb = array($this->Page, $col->FieldRenderer);
				$col->DataObj = $realData;
				$col->Value = $val;
				$col->DisplayText = $row2[$col->Name];
				if (is_callable($cb))
				{
					call_user_func($cb, $col);
					$row2[$col->Name] = $col->DisplayText;
					if(is_array($col->TdAttrs))
						$colsAttr[$col->Name] = $col->TdAttrs;
				}
				if ($changeRealData)
					$realData->{$col->Name} = $col->ToExcelValue($val);
			}
			if (is_callable($this->CbRenderRow) && !$changeRealData && @$_GET['onlyData'] == '')
			{
				$attr = (object)$attr;
				$row2 = (object)$row2;
				call_user_func($this->CbRenderRow, $realData, $row2, $attr, $this);
				$row2 = (array)$row2;
				$attr = (array)$attr;
			}

			$buttons = null;
			if (is_callable($this->CbRenderRowMenu))
			{
				$col = null;
				foreach($this->Columns as $c)
					if ($c instanceof DataColumnActions)
						$col = $c;
				if ($col)
				{
					$buttons = ObjectLib::GetStdObj($col->Buttons, null);
					/* @var $buttons BtnIslem[]|stdClass */
					$obj1 = new ArrayObject($buttons);
					call_user_func($this->CbRenderRowMenu, $realData, $obj1);
					$buttons2 = $obj1->getArrayCopy();
					// Silinenleri tespit et
					$deleted = array_diff(array_keys($buttons), array_keys($buttons2));
					foreach($deleted as $i)
						$buttons[$i]->deleted = 1;
				}
			}

			$rowAttr[] = $attr;
			$tdsAttr[] = $colsAttr;
			if ($buttons !== null)
				$rowButtons[] = $buttons;
			$this->Data[$index] = array_values($row2);
		}
		$this->RowAttributes = $rowAttr;
		$this->TdsAttributes = $tdsAttr;
		$this->RowButtons = $rowButtons;
		$this->Loaded = true;
	}

	/**
	 * @return PagedData
	 */
	public function GetPagedData($params, $sortCol, $sortDir, $start, $length, $exporting = false)
	{
		$page = new PagedData();
		$this->LoadDisplayText($exporting);
		$page->PageSize = $length;
		$page->RecordCount = count($this->Data);
		$page->Records = &$this->Data;
		return $page;
	}

	public function DeleteActButton($idx)
	{
		unset($this->Columns['islem']->Buttons[$idx]);
	}

	public function __get($name)
	{
		if ($name == 'DataGrid')
		{
			if(! $this->DataGrid)
				$this->DataGrid = new DataGrid($this);
			return $this->DataGrid;
		}
		return parent::__get($name);
	}

	public function __set($name, $value)
	{
		if ($name == 'Page')
			return $this->Page = $value;
		$this->__get('DataGrid');
		$this->DataGrid->{$name} = $value;
	}
}
