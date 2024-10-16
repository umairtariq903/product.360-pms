<?php
abstract class ExportFilterBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var int FieldName = export_rule_id                          */
	public $ExportRuleId = 0;

	/** @var string FieldName = operator                             */
	public $Operator = "";

	/** @var int FieldName = p_attribute_id                          */
	public $PAttributeId = 0;

	/** @var int FieldName = condition_rule                          */
	public $ConditionRule = 0;

	/** @var string FieldName = value_                               */
	public $Value = "";

	/** @var int FieldName = filter_sort                             */
	public $FilterSort = 0;
}

/**
 * @method ExportFilter GetById(int $id, bool $AutoCreate = false)
 * @method ExportFilter GetFirst(array|object $params = array())
 * @method ExportFilter[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method ExportFilterDb SetOrderByExp(string $customStr)
 */
abstract class ExportFilterDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT
		/*fields*/
		  *
		/*fields*/
		FROM export_filters
		WHERE (1=1)
		ORDER BY filter_sort,(1)
	';
}

class ExportFilterModelMap extends ModelMap
{
	public $Name = 'export_filters';
	public $ModelName = 'ExportFilter';

	protected $DbFields = array(
		"Id" => array(
			"type"     => VarTypes::INT,
			"name"     => "id",
			"field"    => "export_filters.id",
			"display"  => "Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"ExportRuleId" => array(
			"type"     => VarTypes::INT,
			"name"     => "export_rule_id",
			"field"    => "export_filters.export_rule_id",
			"display"  => "Export Rule Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Operator" => array(
			"type"     => VarTypes::STRING,
			"name"     => "operator",
			"field"    => "export_filters.operator",
			"display"  => "Operator",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"PAttributeId" => array(
			"type"     => VarTypes::INT,
			"name"     => "p_attribute_id",
			"field"    => "export_filters.p_attribute_id",
			"display"  => "P Attribute Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"ConditionRule" => array(
			"type"     => VarTypes::INT,
			"name"     => "condition_rule",
			"field"    => "export_filters.condition_rule",
			"display"  => "Condition Rule",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Value" => array(
			"type"     => VarTypes::STRING,
			"name"     => "value_",
			"field"    => "export_filters.value_",
			"display"  => "Value",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"FilterSort" => array(
			"type"     => VarTypes::INT,
			"name"     => "filter_sort",
			"field"    => "export_filters.filter_sort",
			"display"  => "Filter Sort",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 )
	);
}