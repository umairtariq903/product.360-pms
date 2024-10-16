<?php
abstract class ProjectBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var string FieldName = name                                 */
	public $Name = "";
}

/**
 * @method Project GetById(int $id, bool $AutoCreate = false)
 * @method Project GetFirst(array|object $params = array())
 * @method Project[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method ProjectDb SetOrderByExp(string $customStr)
 */
abstract class ProjectDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT
		/*fields*/
		  *
		/*fields*/
		FROM projects
		WHERE (1=1)
		ORDER BY (1)
	';
}

class ProjectModelMap extends ModelMap
{
	public $Name = 'projects';
	public $ModelName = 'Project';

	protected $DbFields = array(
		"Id" => array(
			"type"     => VarTypes::INT,
			"name"     => "id",
			"field"    => "projects.id",
			"display"  => "Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Name" => array(
			"type"     => VarTypes::STRING,
			"name"     => "name",
			"field"    => "projects.name",
			"display"  => "Name",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" )
	);
}