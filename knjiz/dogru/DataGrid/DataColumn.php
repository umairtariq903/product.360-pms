<?php
/**
 * @property ModelBase $DataObj
 * @property mixed $Value Mevcut formatlanmamış ham veri.
 * @property string $DisplayText Mevcut Formatlanmış veri.
 */
class DataColumn extends IVarBase
{
	public $ColIndex = 0;
	public $GroupName = '';
	public $OrderBy = '';
	public $Align = '';
	public $Width = 'auto';
	public $Pinned = 0;

	/**
	 * Arama yapılabilirlik
	 * @var bool
	 */
	public $Searchable = true;
	public $SearchByDisplay = false;
	public $Editable = false;
	public $FixedColumn = false;

	/**
	 * Sıralama yapılabilirlik
	 * @var bool
	 */
	public $Sortable = true;
	/**
	 * Sıralama neye göre yapılacak
	 * @var bool
	 */
	public $SortByDisplay = false;
	/**
	 * Bu alana göre gruplama yapılabilirlik
	 * @var bool
	 */
	public $Groupable = false;
	/**
	 * PageController içinde bu sütunu render eden
	 * metodun adı
	 * @var string
	 */
	public $FieldRenderer = '';

	public $TdAttrs = null;

	protected $DataObj = null;
	protected $Value = null;
	protected $DisplayText = null;

	public function __construct($type)
	{
		parent::__construct($type);
		if(! $this->Align)
			$this->Align = parent::__get('Align');
	}

	public function Compare($v1, $v2)
	{
		if($this->SortByDisplay)
		{
			$v1 = $this->ToStr($v1);
			$v2 = $this->ToStr($v2);
		}
		if ($v1 == $v2)
			return 0;
		if ($v1 < $v2)
			return -1;
		else
			return 1;
	}

	public function SearchCompare($val, $search)
	{
		return mb_stripos($val, $search, null) !== false;
	}

	public function IsIn($val, $search)
	{
		if($search === NULL || $search === '')
			return true;
		if ($this->Searchable && $search)
		{
			if($this->SearchByDisplay)
				$val = $this->ToStr($val);
			return $this->SearchCompare($val, $search);
		}
		return TRUE;
	}

	public function __get($name)
	{
		if ($name == 'DataObj' || $name == 'Value' || $name == 'DisplayText')
			return $this->{$name};
		return parent::__get($name);
	}
	public function __set($name, $value)
	{
		if ($name == 'DataObj' || $name == 'Value' || $name == 'DisplayText')
			return $this->{$name} = $value;
		parent::__set($name, $value);
	}
}

class DataColumnCalculated extends DataColumn
{
	public $DataBound = false;
	public $Sortable = false;
	public $Searchable = false;
	public $DataFieldName = '';
	public function __construct($type = VarTypes::STRING)
	{
		parent::__construct($type);
	}

	public function ToStr($value, $realData = null)
	{
		return '';
	}

	public function ToSet($value)
	{
		return '';
	}

	public function IsValidForWhere($value)
	{
		return false;
	}

	public function ToCondition($value)
	{
		return array($value => '');
	}
}

class DataColumnCheckbox extends DataColumnCalculated
{
	public $Sizable = false;
	public function __construct($type = VarTypes::BOOL)
	{
		parent::__construct($type);
		$this->Align = 'center';
	}
	public function ToStr($value, $realData = null)
	{
		$checked = $value ? 'checked="checked"' : '';
		return "<input type=checkbox name=\"$this->Name\" $checked/>";
	}
}

class DataColumnActions extends DataColumnCalculated
{
	public $Align = 'center';
	/**
	 * @var BtnIslem[] $Buttons Button işlem dizisi
	 */
	public $Buttons = array();
	public $Width = 100;


	public function __construct($type = VarTypes::STRING)
	{
		parent::__construct($type);
		$this->DisplayName = 'İşlemler';
	}
}