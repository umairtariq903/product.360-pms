<?php
require_once 'DataTable.php';

class DataTableArray extends DataTable
{
	/**
	 * @var array[] 2 boyutlu veri dizisi
	 */
	public $Data = array(array());

	public function __construct($inst = null)
	{
		if($inst)
			$this->GetColumns($inst);
	}

	public function Init($rows)
	{
		$this->Data = array();
		foreach($rows as $row)
			$this->Data[] = $this->ReadRow($row);
		return $this;
	}

	public function GetColumns($inst=null)
	{
		$this->Columns = array();
		$this->Data = array();
		if(is_string($inst) && class_exists($inst))
			$inst = new $inst();
		if (! is_object($inst))
			ThrowException ("DataTableArray için <$inst> bulunmadı");
		if ($this->VisibleColumns == null || count($this->VisibleColumns) == 0)
		{
			$this->VisibleColumns = array();
			foreach($inst as $key => $value)
				if (! is_array($value) && !is_object($value))
					$this->VisibleColumns[] = $key;
		}
		foreach($this->VisibleColumns as $name)
		{
			$props = self::ExtractColumnProps($name);
			$val = $inst->{$name};
			if (is_array($val) || is_object($val))
				continue;
			$type = VarTypes::GetTypeFromValue($val);
			$this->AddColumn($name, $type, $props);
		}
		$this->VisibleColumns = array_keys($this->Columns);
		return $this->Columns;
	}

	private function ReadRow($row)
	{
		// Alanlar tanımlı değilse otomatik hesapla
		if(count($this->Columns) == 0)
			foreach($row as $key => $value)
			{
				if (is_array($value) || is_object($value))
					continue;
				$type = VarTypes::GetTypeFromValue($value);
				$this->AddColumn($key, $type);
			}
		$data = array();
		foreach(array_keys($this->Columns) as $key)
		{
			$val = null;
			if (is_object($row) && property_exists($row, $key))
				$val = $row->{$key};
			if (is_array($row) && key_exists($key, $row))
				$val = $row[$key];

			if (is_array($val) || is_object($val))
				$data[] = NULL;
			else
				$data[] = $val;
		}
		return $data;
	}

	public function Sort($key, $direction = '')
	{
		SortDataTable::$DataTableArray = $this;
		SortDataTable::$SortColumn = $key;
		SortDataTable::$SortDirection = strtolower($direction) == 'desc' ? -1 : 1;
		usort($this->RealData, array('SortDataTable', 'CompareRow'));
	}

	public function Where($params)
	{
		$data = array();
		// şimdilik tüm alanlarda arasın
		$columns = $this->Columns;
		foreach($this->RealData as $inst)
		{
			$search = @$params['sorgu'];
			$isIn = false;
			foreach($columns as $col)
				if($col->IsIn($inst->{$col->Name}, $search))
				{
					$isIn = TRUE;
					break;
				}
			if(! $isIn)
				continue;
			foreach($columns as $col)
				if(! $col->IsIn($inst->{$col->Name}, @$params[$col->Name]))
				{
					$isIn = FALSE;
					break;
				}
			if($isIn)
				$data[] = $inst;
		}
		$this->RealData = $data;
	}

	public function GetPagedData($params, $sortCol, $sortDir, $start, $length, $exporting = false)
	{
		$this->Where($params);
		$count = count($this->RealData);
		$cols = array_keys($this->Columns);
		if($sortCol >= 0)
		{
			$sortCol = $cols[$sortCol];
			if(is_object($sortCol) && array_key_exists($sortCol->OrderBy, $this->Columns))
				$sortCol = $this->Columns[$sortCol->OrderBy];
			$this->Sort($sortCol, $sortDir);
		}
		$this->RealData = array_slice($this->RealData, $start, $length);
		$this->Data = array();
		foreach($this->RealData as $obj)
		{
			$values = array();
			foreach($cols as $name)
				$values[] = $obj->{$name};
			$this->Data[] = $values;
		}
		$page = parent::GetPagedData($params, $sortCol, $sortDir, $start, $length);
		$page->RecordCount = $count;
		return $page;
	}
}

class SortDataTable
{
	public static $SortColumn = '';
	public static $SortDirection = 1;
	/**
	 * @var DataTableArray
	 */
	public static $DataTableArray = null;

	public static function CompareRow($obj1, $obj2)
	{
		$clm = self::$DataTableArray->Columns[self::$SortColumn];
		$v1 = $obj1->{$clm->Name};
		$v2 = $obj2->{$clm->Name};
		return $clm->Compare($v1, $v2) * self::$SortDirection;
	}
}

