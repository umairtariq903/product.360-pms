<?php
abstract class WorkImportBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var int FieldName = import_id                               */
	public $ImportId = 0;

	/** @var datetime FieldName = added_date                         */
	public $AddedDate = "0000-00-00 00:00:00";

	/** @var int FieldName = import_log_id                           */
	public $ImportLogId = 0;
}

/**
 * @method WorkImport GetById(int $id, bool $AutoCreate = false)
 * @method WorkImport GetFirst(array|object $params = array())
 * @method WorkImport[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method WorkImportDb SetOrderByExp(string $customStr)
 */
abstract class WorkImportDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT
		/*fields*/
		  *
		/*fields*/
		FROM work_imports
		WHERE (1=1)
		ORDER BY (1)
	';
}

class WorkImportModelMap extends ModelMap
{
	public $Name = 'work_imports';
	public $ModelName = 'WorkImport';

	protected $DbFields = array(
		"Id" => array(
			"type"     => VarTypes::INT,
			"name"     => "id",
			"field"    => "work_imports.id",
			"display"  => "Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"ImportId" => array(
			"type"     => VarTypes::INT,
			"name"     => "import_id",
			"field"    => "work_imports.import_id",
			"display"  => "Import Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"AddedDate" => array(
			"type"     => VarTypes::DATETIME,
			"name"     => "added_date",
			"field"    => "work_imports.added_date",
			"display"  => "Added Date",
			"model"    => "datetime",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "0000-00-00 00:00:00" ),
		"ImportLogId" => array(
			"type"     => VarTypes::INT,
			"name"     => "import_log_id",
			"field"    => "work_imports.import_log_id",
			"display"  => "Import Log Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 )
	);
}