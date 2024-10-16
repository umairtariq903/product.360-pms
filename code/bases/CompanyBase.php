<?php
abstract class CompanyBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var string FieldName = name                                 */
	public $Name = "";
}

/**
 * @method Company GetById(int $id, bool $AutoCreate = false)
 * @method Company GetFirst(array|object $params = array())
 * @method Company[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method CompanyDb SetOrderByExp(string $customStr)
 */
abstract class CompanyDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT
		/*fields*/
		  *
		/*fields*/
		FROM companies
		WHERE (1=1)
		ORDER BY (1)
	';
}

class CompanyModelMap extends ModelMap
{
	public $Name = 'companies';
	public $ModelName = 'Company';

	protected $DbFields = array(
		"Id" => array(
			"type"     => VarTypes::INT,
			"name"     => "id",
			"field"    => "companies.id",
			"display"  => "Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Name" => array(
			"type"     => VarTypes::STRING,
			"name"     => "name",
			"field"    => "companies.name",
			"display"  => "Name",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" )
	);
}