<?php
abstract class VendorBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var string FieldName = code_                                */
	public $Code = "";

	/** @var string FieldName = name_                                */
	public $Name = "";
}

/**
 * @method Vendor GetById(int $id, bool $AutoCreate = false)
 * @method Vendor GetFirst(array|object $params = array())
 * @method Vendor[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method VendorDb SetOrderByExp(string $customStr)
 */
abstract class VendorDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT
		/*fields*/
		  *
		/*fields*/
		FROM vendors
		WHERE (1=1)
		ORDER BY (1)
	';
}

class VendorModelMap extends ModelMap
{
	public $Name = 'vendors';
	public $ModelName = 'Vendor';

	protected $DbFields = array(
		"Id" => array(
			"type"     => VarTypes::INT,
			"name"     => "id",
			"field"    => "vendors.id",
			"display"  => "Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Code" => array(
			"type"     => VarTypes::STRING,
			"name"     => "code_",
			"field"    => "vendors.code_",
			"display"  => "Code",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Name" => array(
			"type"     => VarTypes::STRING,
			"name"     => "name_",
			"field"    => "vendors.name_",
			"display"  => "Name",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" )
	);
}