<?php
require_once 'DataTable.php';

class DataTableModelDb extends DataTable
{
	const TYPE_REGULAR = 1;
	const TYPE_GENERIC = 2;

	/**
	 *
	 * @var ModelDb
	 */
	public $ModelDb = null;
	/**
	 *
	 * @var ModelMap
	 */
	public $ModelMap = null;

	/**
	 * - RenderParamObj
	 * - RenderRow
	 * - RenderCell
	 * @var type
	 */
	protected $Callbacks = null;

	public function __construct($page = null)
	{
		$this->Page = $page;
	}

	/**
	 *
	 * @param ModelDb|Model|string|SqlQuery $model
	 * @throws Exception
	 */
	public function Build($model, $visibleColumns = array())
	{
		parent::Build($model, $visibleColumns);
		$this->ModelMap = $this->ModelDb->GetModelMap();
		$this->Callbacks = array();
	}

	public function GetPagedData($queryParams, $orderBy, $orderDir, $start, $pSize, $exporting = false, $withRealData = false)
	{
		$page = $start/$pSize + 1;
		$cols = $this->Columns;
		$fieldNames = array_keys($cols);
		if ($exporting && is_array($exporting))
			foreach($fieldNames as $index => $name)
				if (!in_array($name, $exporting))
					$fieldNames[$index] = ''; // Alanın sorgulanması/çekilmesini engelliyoruz
		$db = $this->GetDb();
		$map= $this->GetModelMap();
		if($orderBy >= 0)
		{
			$field = $fieldNames[$orderBy];
			if(array_key_exists(@$cols[$field]->OrderBy, $map->DbFields))
				$field = $cols[$field]->OrderBy;
			if(isset($map->DbFields[$field]))
			{
				$field = $map->DbFields[$field];
				$field = $db->IsWrappedQuery() ? "`$field->Name`" : $field->FieldName;
				$db->SetOrderByExp("$field $orderDir");
			}
		}

		$list = $db->GetPage(self::GetWithConditionOperators($queryParams, $db), $page, $pSize);
        if ($withRealData)
        {
            $list->RealDataOrj = [];
            foreach ($list->Records as $r)
                $list->RealDataOrj[] = clone $r;
        }
		if ($exporting)
			$db->PreCalculatedSum = $list->Summary;
		$this->Data = array();
		foreach($list->Records as $obj)
		{
			$values = array();
			foreach($fieldNames as $name)
			{
				$value = '';
				$vFields = $this->DataGrid->paramSelectedFiels;
				$inAttrs = in_array($name, $this->RowAttributes);
				if ($name && (in_array($name, $vFields) || !$vFields || $inAttrs))
					$value = $obj->{$name};
				if (is_object($value) && method_exists($value, '__toString'))
					$value = $value->__toString();
				else if (is_object($value) || is_array($value))
					$value = '';
				$values[] = addslashes($value);
			}
			$this->Data[] = $values;
		}
		$this->RealData = $list->Records;
		$this->LoadDisplayText($exporting);
		$list->Records = &$this->Data;
		return $list;
	}

	/**
	 *
	 * @param array $queryParams
	 * @param ModelDb $db
	 * @return array
	 */
	public static function GetWithConditionOperators($queryParams, $db)
	{
		// queryParams değişkenindeki değerleri operatör gönderilmişse
		// değiştiriyoruz
		$ops = IfNull($queryParams, 'sConditionOperators', array());
		$db->CustomParamOperators = array();
		foreach($queryParams as $name => $value)
		{
			$op = @constant('OPRT::' . $ops[$name]);
			$db->CustomParamOperators[$name] = $op;

			if (! isset($ops[$name]) ||
				is_object($value) ||
				! array_key_exists($name, $db->GetModelMap()->DbFields)	||
				$db->CustomParamToWhere($name, $value) !== NULL ||
				(is_string($value) && preg_match("/^([<>!=]{1,2})/", $value)))
				continue;
			if (! $op)
				ThrowException("$name için operator bulunamadı: " . ArrayShortInfo($ops));
			if ($op == OPRT::BETWEEN || (! is_array($value) && preg_match("/[|]/", $value)))
			{
				$parts = explode('|', $value);
				if (!(@$parts[0] . @$parts[1]))
				{
					$queryParams[$name] = null;
					continue;
				}
				if (count($parts) == 1)
				{
					$op = OPRT::EQ;
					$value = $parts[0];
				}
				else if ( ! $parts[1])
				{
					if ($op == OPRT::BETWEEN)
						$op = OPRT::GTE_STR;
					$value = $parts[0];
				}
				else if (! $parts[0])
				{
					if ($op == OPRT::BETWEEN)
						$op = OPRT::LTE_STR;
					$value = $parts[1];
				}
				else
					$value = array($parts[0], $parts[1]);
			}
			else if (in_array($op, array(OPRT::IN, OPRT::NOT_IN)) && !is_array($value))
				$value = array($value);

			$col = $db->GetModelMap()->DbFields[$name];
			if (is_a($col->GetTypeObj(), 'VarDateTime'))
			{
				if (is_array($value))
				{
					$value[0] = Tarih::ToMysqlDate($value[0]);
					$value[1] = Tarih::ToMysqlDate($value[1]);
				}
				else
					$value = Tarih::ToMysqlDate ($value);
			}
			$queryParams[$name] = Condition::Field($op, $value);
		}

		return $queryParams;
	}

	public function GetDb()
	{
		return $this->ModelDb;
	}

	public function GetModelMap()
	{
		return $this->ModelMap;
	}
}
