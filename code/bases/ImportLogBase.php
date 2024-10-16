<?php
/**
 * @property AppFile $CsvDosya
 */
abstract class ImportLogBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var int FieldName = import_id                               */
	public $ImportId = 0;

	/** @var int FieldName = added_product_count                     */
	public $AddedProductCount = 0;

	/** @var int FieldName = updated_product_count                   */
	public $UpdatedProductCount = 0;

	/** @var int FieldName = skip_product_count                      */
	public $SkipProductCount = 0;

	/** @var int FieldName = empty_ean_count                         */
	public $EmptyEanCount = 0;

	/** @var int FieldName = incorrect_ean_count                     */
	public $IncorrectEanCount = 0;

	/** @var datetime FieldName = started_time                       */
	public $StartedTime = "0000-00-00 00:00:00";

	/** @var datetime FieldName = finished_time                      */
	public $FinishedTime = "0000-00-00 00:00:00";

	/** @var int FieldName = user_id                                 */
	public $UserId = 0;

	/** @var datetime FieldName = created_time                       */
	public $CreatedTime = "0000-00-00 00:00:00";

	/** @var string FieldName = import_name                          */
	public $ImportName = "";

	/** @var string FieldName = project_name                         */
	public $ProjectName = "";

	/** @var int FieldName = company_id                              */
	public $CompanyId = 0;

	/** @var int FieldName = project_id                              */
	public $ProjectId = 0;

	private $CsvDosya = "";

	public function GetValue($name)
	{
		return @$this->{$name};
	}

	public function SetValue($name, $value)
	{
		$this->{$name} = $value;
	}
}

/**
 * @method ImportLog GetById(int $id, bool $AutoCreate = false)
 * @method ImportLog GetFirst(array|object $params = array())
 * @method ImportLog[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method ImportLogDb SetOrderByExp(string $customStr)
 */
abstract class ImportLogDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT
		/*fields*/
		  T1.*,T2.name AS import_name,T3.name AS project_name,T2.company_id,T2.project_id
		/*fields*/
		FROM import_logs T1
		LEFT JOIN imports T2 ON T1.import_id=T2.id
		LEFT JOIN projects T3 ON T3.id=T2.project_id
		WHERE (1=1)
		ORDER BY (1)
	';
}

class ImportLogModelMap extends ModelMap
{
	public $Name = 'import_logs';
	public $ModelName = 'ImportLog';

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
		"AddedProductCount" => array(
			"type"     => VarTypes::INT,
			"name"     => "added_product_count",
			"field"    => "T1.added_product_count",
			"display"  => "Added Product Count",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"UpdatedProductCount" => array(
			"type"     => VarTypes::INT,
			"name"     => "updated_product_count",
			"field"    => "T1.updated_product_count",
			"display"  => "Updated Product Count",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"SkipProductCount" => array(
			"type"     => VarTypes::INT,
			"name"     => "skip_product_count",
			"field"    => "T1.skip_product_count",
			"display"  => "Skip Product Count",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"EmptyEanCount" => array(
			"type"     => VarTypes::INT,
			"name"     => "empty_ean_count",
			"field"    => "T1.empty_ean_count",
			"display"  => "Empty Ean Count",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"IncorrectEanCount" => array(
			"type"     => VarTypes::INT,
			"name"     => "incorrect_ean_count",
			"field"    => "T1.incorrect_ean_count",
			"display"  => "Incorrect Ean Count",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"StartedTime" => array(
			"type"     => VarTypes::DATETIME,
			"name"     => "started_time",
			"field"    => "T1.started_time",
			"display"  => "Started Time",
			"model"    => "datetime",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "0000-00-00 00:00:00" ),
		"FinishedTime" => array(
			"type"     => VarTypes::DATETIME,
			"name"     => "finished_time",
			"field"    => "T1.finished_time",
			"display"  => "Finished Time",
			"model"    => "datetime",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "0000-00-00 00:00:00" ),
		"UserId" => array(
			"type"     => VarTypes::INT,
			"name"     => "user_id",
			"field"    => "T1.user_id",
			"display"  => "User Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"CreatedTime" => array(
			"type"     => VarTypes::DATETIME,
			"name"     => "created_time",
			"field"    => "T1.created_time",
			"display"  => "Created Time",
			"model"    => "datetime",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "0000-00-00 00:00:00" ),
		"ImportName" => array(
			"type"     => VarTypes::STRING,
			"name"     => "import_name",
			"field"    => "T2.name",
			"display"  => "Import Name",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"ProjectName" => array(
			"type"     => VarTypes::STRING,
			"name"     => "project_name",
			"field"    => "T3.name",
			"display"  => "Project Name",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"CompanyId" => array(
			"type"     => VarTypes::INT,
			"name"     => "company_id",
			"field"    => "T2.company_id",
			"display"  => "Company Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => 0 ),
		"ProjectId" => array(
			"type"     => VarTypes::INT,
			"name"     => "project_id",
			"field"    => "T2.project_id",
			"display"  => "Project Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => 0 ),
		"CsvDosya" => array(
			"type"     => VarTypes::INT,
			"name"     => "csv_dosya",
			"field"    => "T1.csv_dosya",
			"display"  => "Csv Dosya",
			"model"    => "AppFile",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" )
	);
}