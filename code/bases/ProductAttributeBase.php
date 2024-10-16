<?php
abstract class ProductAttributeBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var int FieldName = product_id                              */
	public $ProductId = 0;

	/** @var int FieldName = attribute_id                            */
	public $AttributeId = 0;

	/** @var string FieldName = value_                               */
	public $Value = "";

	/** @var string FieldName = attribute_name                       */
	public $AttributeName = "";
}

/**
 * @method ProductAttribute GetById(int $id, bool $AutoCreate = false)
 * @method ProductAttribute GetFirst(array|object $params = array())
 * @method ProductAttribute[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method ProductAttributeDb SetOrderByExp(string $customStr)
 */
abstract class ProductAttributeDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT
		/*fields*/
		  T1.*,T2.name_ AS attribute_name
		/*fields*/
		FROM product_attributes T1
		INNER JOIN p_attributes T2 ON T1.attribute_id=T2.id
		WHERE (1=1)
		ORDER BY (1)
	';
}

class ProductAttributeModelMap extends ModelMap
{
	public $Name = 'product_attributes';
	public $ModelName = 'ProductAttribute';

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
		"ProductId" => array(
			"type"     => VarTypes::INT,
			"name"     => "product_id",
			"field"    => "T1.product_id",
			"display"  => "Product Id",
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
		"AttributeName" => array(
			"type"     => VarTypes::STRING,
			"name"     => "attribute_name",
			"field"    => "T2.name_",
			"display"  => "Attribute Name",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" )
	);
}