<?php
class DbField extends IVarBase
{
	public $ModelName	= '';
	public $ModelIsArray= 0;
	public $TableName	= '';
	public $FieldName	= '';
	public $Required	= 0;
	public $IsReal		= 1;
	public $IsSerialized= 0;
	public $DefaultValue= "";

	/**
	 *
	 * @param PhpField $obj
	 * @return DbField
	 */
	public static function InitFromPhpField($obj)
	{
		$f = new DbField($obj->Type);
		$f->Name = $obj->Name;
		$f->DefaultValue = $obj->Default;
		$f->DisplayName = $obj->DispName;
		$f->IsReal = $obj->IsReal;
		$f->FieldName = $obj->Alies;
		return $f;
	}

	public static function Init($array)
	{
		static $fieldIndex = array(
			'1' => 'name',
			'2' => 'field',
			'3' => 'display',
			'4' => 'model',
			'5' => 'required',
			'6' => 'is_real',
			'7' => 'is_serialized',
			'8' => 'default'
		);
		static $fieldNames = array(
			'name' => 'Name',
			'field' => 'FieldName',
			'display' => 'DisplayName',
			'model' => 'ModelName',
			'required' => 'Required',
			'is_real' => 'IsReal',
			'is_serialized' => 'IsSerialized',
			'default' => 'DefaultValue',
			'table' => 'TableName',
		);
		static $VarBaseClasses = array();
		$type = IfNull($array, 'model', @$array[4]);
		if (version_compare(PHP_VERSION, '5.3.9') >= 0)
			if (is_object($type))
				$cond2 = $type instanceof VarBase;
			else
			{
				if (isset($VarBaseClasses[$type]))
					$cond2 = $VarBaseClasses[$type];
				else
					$cond2 = $VarBaseClasses[$type] = is_a($type, 'VarBase', true);
			}
		else if (is_object($type))
			$cond2 = is_a($type, 'VarBase');
		else
			$cond2 = class_exists($type) && is_a(new $type, 'VarBase');
		if (!isset(VarTypes::$CustomVarTypes[$type]) && ! $cond2)
			$type = IfNull($array, 'type', @$array[0]);
		$fld = new DbField($type);
		foreach($array as $name => $value)
		{
			$name = str_replace(array('_name', '_text'), '', $name);
			if (isset($fieldIndex[$name]))
				$name = $fieldIndex[$name];
			$prop = isset($fieldNames[$name]) ? $fieldNames[$name] : '';
			if ($prop == 'ModelName' && ($value == '[]' || $value == 'array'))
			{
				$fld->ModelIsArray = 1;
				$value = str_replace('[]', '', $value);
			}
			if ($prop)
				$fld->{$prop} = $value;
		}
		return $fld;
	}

	/**
	 * @return ModelBase
	 */
	public function GetModel()
	{
		if ($this->ModelName != '' && class_exists($this->ModelName))
			return new $this->ModelName;
		return null;
	}

	public function IsCalculated()
	{
		$cond1 = preg_match("/(GROUP_CONCAT|MAX|MIN|AVG|SUM)\s*\(.*\)/i", $this->FieldName);
		$cond2 = ! ($this->IsReal || preg_match("/[a-z]+[0-9]*\.[a-z]+[0-9]*/i", $this->FieldName));
		return $cond1 || $cond2 ;
	}

	public function IsExpression()
	{
		if (property_exists($this->ModelName, $this->FieldName))
			return false;
		return true;
	}
}