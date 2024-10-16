<?php
abstract class ImportAttributeBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var int FieldName = import_id                               */
	public $ImportId = 0;

	/** @var int FieldName = attribute_id                            */
	public $AttributeId = 0;

	/** @var string FieldName = value_                               */
	public $Value = "";

	/** @var string FieldName = condition_                           */
	public $Condition = "";

	/** @var string FieldName = attribute_name                       */
	public $AttributeName = "";

	/** @var string FieldName = attribute_product_field_name         */
	public $AttributeProductFieldName = "";
}

/**
 * @method ImportAttribute GetById(int $id, bool $AutoCreate = false)
 * @method ImportAttribute GetFirst(array|object $params = array())
 * @method ImportAttribute[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method ImportAttributeDb SetOrderByExp(string $customStr)
 */
abstract class ImportAttributeDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT
		/*fields*/
		  T1.*,T2.name_ AS attribute_name,T2.product_field_name AS attribute_product_field_name
		/*fields*/
		FROM import_attributes T1
		INNER JOIN p_attributes T2 ON T1.attribute_id=T2.id
		WHERE (1=1)
		ORDER BY (1)
	';
}

class ImportAttributeModelMap extends ModelMap
{
	public $Name = 'import_attributes';
	public $ModelName = 'ImportAttribute';

	protected $DbFields = array(
		"Id" => array(
			"type"     => VarTypes::INT,
			"name"     => "id",
			"field"    => "T1.id",
			"display"  => "Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"ImportId" => array(
			"type"     => VarTypes::INT,
			"name"     => "import_id",
			"field"    => "T1.import_id",
			"display"  => "Import Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"AttributeId" => array(
			"type"     => VarTypes::INT,
			"name"     => "attribute_id",
			"field"    => "T1.attribute_id",
			"display"  => "Attribute Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Value" => array(
			"type"     => VarTypes::STRING,
			"name"     => "value_",
			"field"    => "T1.value_",
			"display"  => "Value",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Condition" => array(
			"type"     => VarTypes::STRING,
			"name"     => "condition_",
			"field"    => "T1.condition_",
			"display"  => "Condition",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"AttributeName" => array(
			"type"     => VarTypes::STRING,
			"name"     => "attribute_name",
			"field"    => "T2.name_",
			"display"  => "Attribute Name",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"AttributeProductFieldName" => array(
			"type"     => VarTypes::STRING,
			"name"     => "attribute_product_field_name",
			"field"    => "T2.product_field_name",
			"display"  => "Attribute Product Field Name",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" )
	);
}