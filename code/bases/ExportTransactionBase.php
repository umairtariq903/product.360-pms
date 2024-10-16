<?php
abstract class ExportTransactionBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var int FieldName = export_rule_id                          */
	public $ExportRuleId = 0;

	/** @var int FieldName = p_attribute_id                          */
	public $PAttributeId = 0;

	/** @var int FieldName = condition_rule                          */
	public $ConditionRule = 0;

	/** @var string FieldName = value_                               */
	public $Value = "";
}

/**
 * @method ExportTransaction GetById(int $id, bool $AutoCreate = false)
 * @method ExportTransaction GetFirst(array|object $params = array())
 * @method ExportTransaction[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method ExportTransactionDb SetOrderByExp(string $customStr)
 */
abstract class ExportTransactionDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT
		/*fields*/
		  *
		/*fields*/
		FROM export_transactions
		WHERE (1=1)
		ORDER BY (1)
	';
}

class ExportTransactionModelMap extends ModelMap
{
	public $Name = 'export_transactions';
	public $ModelName = 'ExportTransaction';

	protected $DbFields = array(
		"Id" => array(
			"type"     => VarTypes::INT,
			"name"     => "id",
			"field"    => "export_transactions.id",
			"display"  => "Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"ExportRuleId" => array(
			"type"     => VarTypes::INT,
			"name"     => "export_rule_id",
			"field"    => "export_transactions.export_rule_id",
			"display"  => "Export Rule Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"PAttributeId" => array(
			"type"     => VarTypes::INT,
			"name"     => "p_attribute_id",
			"field"    => "export_transactions.p_attribute_id",
			"display"  => "P Attribute Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"ConditionRule" => array(
			"type"     => VarTypes::INT,
			"name"     => "condition_rule",
			"field"    => "export_transactions.condition_rule",
			"display"  => "Condition Rule",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Value" => array(
			"type"     => VarTypes::STRING,
			"name"     => "value_",
			"field"    => "export_transactions.value_",
			"display"  => "Value",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" )
	);
}