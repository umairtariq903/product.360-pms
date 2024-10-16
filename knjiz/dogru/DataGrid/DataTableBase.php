<?php
/**
 * @property string $Name
 * @property string $DisplayName
 * @property string $Information
 * @property string $Type
 * @property bool $OrderBy
 * @property string $Align
 * @property string $Width
 * @property string $Height
 * @property bool $Visible
 * @property string $GroupName
 * @property bool $Sizable
 * @property bool $Searchable
 * @property bool $Editable
 * @property bool $FixedColumn
 * @property string $Default
 * @property int $Colspan
 * @property bool $Readonly
 * @property bool $Required
 * @property bool $Disabled
 * @property string $FieldRenderer
 * @property string $TplRenderer
 * @property string $ExtAttributes
 */
class ColumnTemplate
{

}

/**
 * @property PageController $Page
 */
class DataTableBase
{
	const DBFORM_SUB_TITLE = 'DbFormSubTitle';

	public static $ColumnProperties = array(
		'N'  => 'Name',
		'D'  => 'DisplayName',
		'I'	 => 'Information',
		'DE' => array('Dependency', 'VarStr', 'EditProp'),
		'T'  => 'Type',
		'O'  => array('OrderBy', 'VarOrderBy', 'ListProp'),
		'A'  => array('Align', 'VarStr', 'ListProp'),
		'W'  => 'Width',
		'H'  => 'Height',
		'V'  => array('Visible', 'VarBool'),
		'G'  => 'GroupName',
		'SZ' => array('Sizable', 'VarBool', 'ListProp'),
		'SR' => array('Searchable', 'VarBool', 'ListProp'),
		'ST' => array('Sortable', 'VarBool', 'ListProp'),
		'E'	 => array('Editable', 'VarBool', 'ListProp'),
		'FX' => array('FixedColumn', 'VarBool', 'ListProp'),
		'DF' => 'Default',
		'PN' => array('Pinned', 'VarBool', 'ListProp'),
		'CS' => array('Colspan', 'VarInt', 'EditProp'),
		'RO' => array('Readonly', 'VarBool', 'EditProp'),
		'RQ' => array('Required', 'VarBool', 'EditProp'),
		'DS' => array('Disabled', 'VarBool', 'EditProp'),
		'EA' => array('ExtAttributes', 'VarStr', 'EditProp'),
		'AO' => array('Addon', 'VarStr', 'EditProp'),
		'FR' => array('FieldRenderer', 'VarStr', 'EditProp'),
		'TR' => array('TplRenderer', 'VarStr', 'EditProp'),
		'CR' => array('FieldRenderer', 'VarStr', 'ListProp'),
		'TP' => array('TypeProperties', 'VarStr', '')
	);
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
	 *
	 * @var ModelBase Orjinal veri nesnesi
	 */
	public $RealData = null;
	/**
	 * Data Alan bilgileri
	 * @var IVarBase[]
	 */
	public $Columns = array();
	/**
	 *
	 * @var string[] Tablo üzerinden görüntülenecek sütunların adları. Boş
	 *		bırakılırsa nesnedeki tüm değerler otomatik getirilir
	 */
	public $VisibleColumns = null;

	/**
	 * Önceden tanımlı olarak verilmek istenen alan türleri için kullanılır.
	 * @var array
	 */
	public $PreDefinedColumnClass = array();

	/**
	 * @var PageController
	 */
	protected $Page = null;

	public static $ColumnPropertyRenderer = null;
	/**
	 * @param type $type
	 * @return IVarBase
	 */
	public function GetNewColumn($type)
	{
		return new DataColumn($type);
	}

	private function GetColFromArray($_array, $extractKeys = true)
	{
		$array = array();
		$regs = array();
		if ($extractKeys)
			foreach($_array as $a)
			{
				$key = $val = $a;
				if (preg_match("/(.*)=(.*)/", $a, $regs))
				{
					$key = $regs[1];
					$val = $regs[2];
				}
				$array[$key] = $val;
			}
		else
			$array = $_array;

		$type = new VarListItem();
		$type->BuildFromArray($array, TRUE);
		if(in_array('DataTable', class_parents($this)))
			$col = new DataColumn($type);
		else
			$col = new DataField($type);
		return $col;
	}
	/**
	 *
	 * @param type $name
	 * @param type|DataColumn $type
	 * @param int $index Listede kaçıncı sıraya ekleneceği
	 * @return DataColumn
	 */
	public function AddColumn($name, $type, $props = array(), $index = -1)
	{
		$index = $index < 0 ? count($this->Columns) : $index;
		if(isset($props['Type']))
			$type = $props['Type'];
		// Önceden tanımlı bir alan sınıfı varsa ona yönlendir.
		if (key_exists($name, $this->PreDefinedColumnClass))
			$type = $this->PreDefinedColumnClass[$name];
		if ($name == self::DBFORM_SUB_TITLE)
		{
			$type = $name;
			$name = self::DBFORM_SUB_TITLE . (count($this->Columns) + 1);
		}
		$matches = array();
		if (is_array($type))
			$col = $this->GetColFromArray($type);
		else if (is_string($type) && preg_match("/^#(.*)/", $type, $matches))
		{
			$regs = array();
			$matches[1] = str_replace('->', '::', $matches[1]);
			if (preg_match("/\[(.*)\]|(.*::.*)/i", $matches[1], $regs))
			{
				$existingArray = preg_match("/.*::.*/", $matches[1]);
				if ($existingArray)
					eval("\$array=$regs[2];");
				else
					$array = explode(",", $regs[1]);
				$col = $this->GetColFromArray($array, ! $existingArray);
			}
			else
				$col = $GLOBALS[$matches[1]];
		}
		else if (! is_object($type))
			$col = $this->GetNewColumn($type);
		else
			$col = $type;
		$col->ColIndex = $index;
		$col->Name = $name;
		if(! $col->DisplayName)
			$col->DisplayName = $name;
		foreach($props as $key => $value)
			if($key != 'Type')
				$col->{$key} = $value;
		if ($index < -1)
			return $col;
		$newArray = array();
		$i = 0;
		if ($index == count($this->Columns))
			$this->Columns[$name] = $col;
		else
		{
			foreach($this->Columns as $colName => $column)
			{
				if ($index == $i)
				{
					$newArray[$name] = $col;
					$col->ColIndex = $i++;
				}
				$newArray[$colName] = $column;
				$column->ColIndex = $i++;
			}
			$this->Columns = $newArray;
		}
		return $col;
	}

	public static function ExtractColumnProps(&$col, $shortName = false)
	{
		$pNames = self::$ColumnProperties;
		foreach($pNames as  &$pName)
			if (is_array($pName))
				$pName = $pName[0];
		$sonuc = array();
		$parts = Kodlama::JSONTryParse($col);
		if (is_object($parts))
			$sonuc['Name'] = $col = $parts->Name;
		else
		{
			$parts = explode(';', $col);
			$sonuc['Name'] = $col = $parts[0];
		}
		foreach($parts as $kod => $p)
		{
			if (is_object($parts))
				$val = $p;
			else
			{
				$prop = explode(':', trim($p), 2);
				$val = true;
				if(count($prop) > 1)
					$val = $prop[1];
				$kod = $prop[0];
			}
			if(! key_exists($kod, $pNames))
				continue;
			$name = $shortName ? $kod : $pNames[$kod];
			if($val === 'true')
				$val = TRUE;
			elseif($val === 'false')
				$val = FALSE;
			$regs = array();
			if($kod == 'D' && preg_match('/#([a-z0-9]+)/i', $val, $regs))
				$val = str_replace ($regs[0], @$GLOBALS[$regs[1]], $val);
			$sonuc[$name] = $val;
		}
		if (is_callable(self::$ColumnPropertyRenderer))
		{
			$obj = (object)$sonuc;
			call_user_func (self::$ColumnPropertyRenderer, $obj);
			$sonuc = (array)$obj;
		}
		return $sonuc;
	}

	public function GetColumns($inst = null)
	{
		if (! $this->Columns)
		{
			if (is_a($this->ModelDb, 'ModelDb'))
			{
				$this->ModelMap = $this->ModelDb->GetModelMap();
				if ($this->VisibleColumns == null || count($this->VisibleColumns) == 0)
					$this->VisibleColumns = array_keys($this->ModelMap->DbFields);
			}
			foreach($this->VisibleColumns as $name)
				$this->ColumnFromName($name);
			$this->VisibleColumns = array_keys($this->Columns);
		}
		return $this->Columns;
	}

	public function ColumnFromName($name, $index = -1)
	{
		$props = self::ExtractColumnProps($name);
		$fieldName = $name;
		if (is_a($this->ModelDb, 'ModelDb'))
			$dbFields = $this->ModelMap->DbFields;
		else if (is_object($this->ModelDb))
			$dbFields = get_object_vars($this->ModelDb);
		else
			$dbFields = array_keys($this->ModelDb);
		if (strpos($name, '.') !== false)
		{
			$parts = explode('.', $name);
			$rels = $this->ModelMap->Relationships;
			$rel = IfNull($rels, $parts[0], null);
			if ($rel)
			{
				/* @var $rel Relation */
				$dbFields = $rel->GetChildModel()->GetModelMap()->DbFields;
				$fieldName = $parts[1];
			}
		}
		if (array_key_exists($fieldName, $dbFields))
		{
			$def = $dbFields[$fieldName];

			// Id alanı ve serileştirilmiş alanlar gözardı
			if ($def->IsSerialized || $def->ModelIsArray)
				return $this->AddColumn($name, VarTypes::STRING, $props, $index);

			$field = $this->AddColumn($name, $def->GetTypeClassName(), $props, $index);
			if(! isset($props['DisplayName']))
				$field->DisplayName = $def->DisplayName;
		}
		else
			$field = $this->AddColumn($name, VarTypes::STRING, $props, $index);
		if (isset($props['TypeProperties']) && is_object($props['TypeProperties']))
			ObjectLib::SetFromObj ($field->GetTypeObj(), $props['TypeProperties']);
		return $field;
	}

	public function Build($model, $visibleColumns = array())
	{
		$cols = array();
		if(! is_object($visibleColumns))
			$cols = $visibleColumns;
		else
			foreach($visibleColumns as $key => $columns)
				foreach($columns as $col)
					$cols[] = $col . ";G:$key";
		if(!$model)
			$this->ModelDb = GenericModelDb::GetFromQuery('SELECT 1 as id');
		else if (is_string($model) && preg_match("/SELECT.*FROM/is", $model))
			$this->ModelDb = GenericModelDb::GetFromQuery($model);
		else if (is_object($model) && is_a($model, 'ModelDb'))
			$this->ModelDb = $model;
		else if (is_object($model) && is_a($model, 'ModelBase'))
			$this->ModelDb = $model->GetDb();
		else if (is_string($model) && class_exists($model))
		{
			$parents = class_parents($model);
			if (in_array('ModelDb', $parents))
				$this->ModelDb = call_user_func(array($model, 'Get'));
			else if (in_array('ModelBase', $parents))
			{
				$obj = new $model;
				$this->ModelDb = $obj->GetDb();
			}
		}
		else if (is_callable($model))
			$this->ModelDb = call_user_func($model);
		if (! $this->ModelDb)
			ThrowException("Model parametresi düzgün verilmemiştir");
		if($this->Page && is_a($this->ModelDb, 'ModelDb'))
			$this->Page->DbModelName = $this->ModelDb->GetModelName();
		$this->VisibleColumns = $cols;
		$this->GetColumns();
		return $this;
	}

	public function SetColumnsProps($names, $prop, $val)
	{
		if(is_string($names))
			$names = explode (',', $names);
		foreach($names as $col)
			$this->Columns[$col]->{$prop} = $val;
	}

	public function __get($name)
	{
		return @$this->{$name};
	}

	public function __set($name, $value)
	{
		$this->{$name} = $value;
	}
}