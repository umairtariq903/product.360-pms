<?php
class OPRT
{
	const BETWEEN	= "#fld# BETWEEN #val#";
	const EQ_NUM	= "#fld# = #val#";
	const EQ_FIELD	= "(#fld# = #val#)";
	const EQ		= "#fld# = '#val#'";
	const NEQ		= "#fld# <> '#val#'";
	const GT		= "#fld# > #val#";
	const GTE		= "#fld# >= #val#";
	const GT_STR	= "#fld# > '#val#'";
	const GTE_STR	= "#fld# >= '#val#'";
	const LT		= "#fld# < #val#";
	const LTE		= "#fld# <= #val#";
	const LT_STR	= "#fld# < '#val#'";
	const LTE_STR	= "#fld# <= '#val#'";
	const LTE_IFNULL= "IFNULL(#fld#,0) <= #val#";
	const LIKE		= "#fld# LIKE '%#val#%'";
	const NOT_LIKE	= "#fld# NOT LIKE '%#val#%'";
	const START_WITH = "#fld# LIKE '#val#%'";
	const START_NOT_WITH = "#fld# NOT LIKE '#val#%'";
	const END_WITH	= "#fld# LIKE '%#val#'";
	const IN		= "#fld# IN (#val#)";
	const IS_NULL	= "#fld# IS NULL";
	const NOT_IN	= "#fld# NOT IN (#val#)";
	const FIND_INSET = "FIND_IN_SET(#fld# , '#val#')";
	const NOT_FIND_INSET = "NOT FIND_IN_SET(#fld# , '#val#')";
	const STR_LENGTH = "LENGTH(#fld#) = #val#";
	const MD5		= "MD5(#fld#) = '#val#' ";
	const CUSTOM	 = "(#val#)";

	public static function GetFromOperator($op = '=')
	{
		switch($op)
		{
			case '>' : return self::GT;
			case '<' : return self::LT;
			case '>=' : return self::GTE;
			case '<=' : return self::LTE;
			case '<>' :
			case '!=' : return self::NEQ;

		}
		return self::EQ_FIELD;
	}
}

class Condition
{
	public $field = '';
	public $cond = '';
	public $value = '';
	public $ignore_value = null;

	public function __construct($field, $cond, $value, $ignore_value = null)
	{
		$this->field = $field;
		$this->cond = $cond;
		$this->value = $value;
		$this->ignore_value = $ignore_value;
	}

	public function Validate()
	{
		return $this->ignore_value === NULL || $this->value != $this->ignore_value;
	}

	public function __toString()
	{
		return $this->ToString($this->field);
	}

	public function ToString($fieldName = '')
	{
		if(! $this->Validate())
			return '';
		if (! $fieldName)
			$fieldName = $this->field;
		$value = $this->value;
		if(in_array($this->cond, array(OPRT::EQ_NUM, OPRT::GT, OPRT::GTE, OPRT::LT, OPRT::LTE)))
			$value = floatval($value);
		else if(in_array($this->cond, array(OPRT::EQ, OPRT::LIKE, OPRT::START_WITH,OPRT::START_NOT_WITH, OPRT::END_WITH)))
			$value = addslashes(stripslashes(strval($value)));

		return str_replace(array('#fld#', '#val#'), array($fieldName, $value), $this->cond);
	}

	public function Combine($with = 'AND')
	{
		$cond = $this->ToString();
		if($cond)
			$cond = "$with $cond";
		return $cond;
	}

	/**
	 *
	 * @param string $cond Operatör
	 * @param mixed $value Değer
	 * @param mixed $ignore_value Gözardı edilecek değer
	 * @return \Condition
	 */
	public static function Field($cond, $value, $ignore_value = null)
	{
		if ($cond == OPRT::BETWEEN && is_array($value))
		{
			if (! preg_match("/^['\"]/", $value[0]))
				$value[0]= "'$value[0]'";
			if (! preg_match("/^['\"]/", $value[1]))
				$value[1]= "'$value[1]'";
			$value = "$value[0] AND $value[1]";
		}
		else if (in_array($cond, array(OPRT::IN, OPRT::NOT_IN, OPRT::FIND_INSET)) &&
				is_array($value))
		{
			if ($cond == OPRT::FIND_INSET)
				$value = implode(',', $value);
			else
				$value = "'" . implode("', '", $value) . "'";
		}
		return new Condition('', $cond, $value, $ignore_value);
	}

	public static function InList($value, $ignore_value = null)
	{
		return self::Field(OPRT::IN, $value, $ignore_value);
	}

	public static function NotInList($value, $ignore_value = null)
	{
		return self::Field(OPRT::NOT_IN, $value, $ignore_value);
	}

	public static function FindInSet($value, $ignore_value = null)
	{
		return self::Field(OPRT::FIND_INSET, $value, $ignore_value);
	}

	public static function EQ($value, $ignore_value = null)
	{
		$value = addslashes($value);
		return self::Field(OPRT::EQ, $value, $ignore_value);
	}

	public static function EQBool($value, $ignore_value = null)
	{
		return self::Field(OPRT::EQ_NUM, $value ? 1 : 0, $ignore_value);
	}

	public static function NotEQ($value, $ignore_value = null)
	{
		return self::Field(OPRT::NEQ, $value, $ignore_value);
	}

	public static function GT($value)
	{
		return self::Field(OPRT::GT, $value);
	}

	public static function GTE($value)
	{
		return self::Field(OPRT::GTE, $value);
	}

	public static function LTE($value)
	{
		return self::Field(OPRT::LTE, $value);
	}

	public static function LT($value)
	{
		return self::Field(OPRT::LT, $value);
	}

	public static function Between($low, $high, $ignore_value = null)
	{
		return self::Field(OPRT::BETWEEN, array($low, $high), $ignore_value);
	}

	public static function BetweenDate($bgn, $end, $ignore_value = null)
	{
		$bgn = Tarih::ToMysqlDate($bgn);
		$end = Tarih::ToMysqlDate($end);
		return self::Field(OPRT::BETWEEN, array("'$bgn'", "'$end'"), $ignore_value);
	}

	public static function IsNull()
	{
		return self::Field(OPRT::IS_NULL, NULL);
	}

	public static function MD5($value, $ignore_value = null)
	{
		$value = addslashes($value);
		return self::Field(OPRT::MD5, $value, $ignore_value);
	}

	public static function Custom($condition = '1=1')
	{
		return self::Field(OPRT::CUSTOM, $condition);
	}
}

class ConditionList
{
	/**
	 * @var Condition[]
	 */
	public $Conds = array();
	public $MergeOprt;

	public function __construct($mergeOperator = " AND ")
	{
		$this->MergeOprt = $mergeOperator;
	}

	/**
	 * @param type $mergeOperator
	 * @return ConditionList
	 */
	public static function Get($mergeOperator = " AND ")
	{
		return new ConditionList($mergeOperator);
	}

	public function __toString()
	{
		return $this->ToString();
	}

	public function ToString($fieldNames = array())
	{
		$dizi = array();
		foreach($this->Conds as $cond)
			if((is_string($cond) && $cond != '') ||
				($cond instanceof Condition && $cond->Validate())){
					if (is_string($fieldNames))
						$dizi[] = $cond->ToString($fieldNames);
					else if (is_array($fieldNames) && array_key_exists($cond->field, $fieldNames))
						$dizi[] = $cond->ToString($fieldNames[$cond->field]);
					else
						$dizi[] = "$cond";
				}
		$conds = implode($this->MergeOprt, $dizi);
		if($conds)
			$conds = "($conds)";
		else
			$conds = "(1=1)";
		return $conds;
	}

	/**
	 * @return ConditionList
	 */
	public function Add($field, $cond, $value, $ignore_value = null)
	{
		$this->Conds[] = new Condition($field, $cond, $value, $ignore_value);
		return $this;
	}

	/**
	 * @return ConditionList
	 * @param Condition|ConditionList $cond
	 */
	public function AddCond($cond)
	{
		$this->Conds[] = "$cond";
		return $this;
	}
}
