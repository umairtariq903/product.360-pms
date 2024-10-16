<?php
abstract class PAttributeBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var string FieldName = name_                                */
	public $Name = "";

	/** @var string FieldName = product_field_name                   */
	public $ProductFieldName = "";

	/** @var int FieldName = is_vendor                               */
	public $IsVendor = 0;

	/** @var int FieldName = important                               */
	public $Important = 0;

	/** @var int FieldName = type_                                   */
	public $Type = 0;
}

/**
 * @method PAttribute GetById(int $id, bool $AutoCreate = false)
 * @method PAttribute GetFirst(array|object $params = array())
 * @method PAttribute[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method PAttributeDb SetOrderByExp(string $customStr)
 */
abstract class PAttributeDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT
		/*fields*/
		  *
		/*fields*/
		FROM p_attributes
		WHERE (1=1)
		ORDER BY (1)
	';
}

class PAttributeModelMap extends ModelMap
{
	public $Name = 'p_attributes';
	public $ModelName = 'PAttribute';

	protected $DbFields = array(
		"Id" => array(
			"type"     => VarTypes::INT,
			"name"     => "id",
			"field"    => "p_attributes.id",
			"display"  => "Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Name" => array(
			"type"     => VarTypes::STRING,
			"name"     => "name_",
			"field"    => "p_attributes.name_",
			"display"  => "Name",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"ProductFieldName" => array(
			"type"     => VarTypes::STRING,
			"name"     => "product_field_name",
			"field"    => "p_attributes.product_field_name",
			"display"  => "Product Field Name",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"IsVendor" => array(
			"type"     => VarTypes::INT,
			"name"     => "is_vendor",
			"field"    => "p_attributes.is_vendor",
			"display"  => "Is Vendor",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Important" => array(
			"type"     => VarTypes::INT,
			"name"     => "important",
			"field"    => "p_attributes.important",
			"display"  => "Important",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Type" => array(
			"type"     => VarTypes::INT,
			"name"     => "type_",
			"field"    => "p_attributes.type_",
			"display"  => "Type",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 )
	);
}