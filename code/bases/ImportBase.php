<?php
/**
 * @property ImportAttribute[] $AttributesInfo
 */
abstract class ImportBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var int FieldName = vendor_id                               */
	public $VendorId = 0;

	/** @var int FieldName = company_id                              */
	public $CompanyId = 0;

	/** @var int FieldName = project_id                              */
	public $ProjectId = 0;

	/** @var int FieldName = import_type                             */
	public $ImportType = 0;

	/** @var string FieldName = name                                 */
	public $Name = "";

	/** @var string FieldName = sp_key                               */
	public $SpKey = "";

	/** @var int FieldName = from_at                                 */
	public $FromAt = 0;

	/** @var string FieldName = p_field_keys                         */
	public $PFieldKeys = "";

	/** @var string FieldName = p_field_where_keys                   */
	public $PFieldWhereKeys = "";

	/** @var string FieldName = ftp_host                             */
	public $FtpHost = "";

	/** @var string FieldName = ftp_user                             */
	public $FtpUser = "";

	/** @var string FieldName = ftp_password                         */
	public $FtpPassword = "";

	/** @var string FieldName = ftp_remote_directory                 */
	public $FtpRemoteDirectory = "";

	/** @var string FieldName = url_link                             */
	public $UrlLink = "";

	/** @var string FieldName = csv_delimeter                        */
	public $CsvDelimeter = "";

	/** @var int FieldName = auto_run                                */
	public $AutoRun = 0;

	/** @var int FieldName = file_type                               */
	public $FileType = 0;

	/** @var int FieldName = min_stock                               */
	public $MinStock = 0;

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
 * @method Import GetById(int $id, bool $AutoCreate = false)
 * @method Import GetFirst(array|object $params = array())
 * @method Import[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method ImportDb SetOrderByExp(string $customStr)
 */
abstract class ImportDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT
		/*fields*/
		  *
		/*fields*/
		FROM imports
		WHERE (1=1)
		ORDER BY (1)
	';
}

class ImportModelMap extends ModelMap
{
	public $Name = 'imports';
	public $ModelName = 'Import';

	protected $DbFields = array(
		"Id" => array(
			"type"     => VarTypes::INT,
			"name"     => "id",
			"field"    => "imports.id",
			"display"  => "Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"VendorId" => array(
			"type"     => VarTypes::INT,
			"name"     => "vendor_id",
			"field"    => "imports.vendor_id",
			"display"  => "Vendor Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"CompanyId" => array(
			"type"     => VarTypes::INT,
			"name"     => "company_id",
			"field"    => "imports.company_id",
			"display"  => "Company Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"ProjectId" => array(
			"type"     => VarTypes::INT,
			"name"     => "project_id",
			"field"    => "imports.project_id",
			"display"  => "Project Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"ImportType" => array(
			"type"     => VarTypes::INT,
			"name"     => "import_type",
			"field"    => "imports.import_type",
			"display"  => "Import Type",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Name" => array(
			"type"     => VarTypes::STRING,
			"name"     => "name",
			"field"    => "imports.name",
			"display"  => "Name",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"SpKey" => array(
			"type"     => VarTypes::STRING,
			"name"     => "sp_key",
			"field"    => "imports.sp_key",
			"display"  => "Sp Key",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"FromAt" => array(
			"type"     => VarTypes::INT,
			"name"     => "from_at",
			"field"    => "imports.from_at",
			"display"  => "From At",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"PFieldKeys" => array(
			"type"     => VarTypes::STRING,
			"name"     => "p_field_keys",
			"field"    => "imports.p_field_keys",
			"display"  => "P Field Keys",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"PFieldWhereKeys" => array(
			"type"     => VarTypes::STRING,
			"name"     => "p_field_where_keys",
			"field"    => "imports.p_field_where_keys",
			"display"  => "P Field Where Keys",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"FtpHost" => array(
			"type"     => VarTypes::STRING,
			"name"     => "ftp_host",
			"field"    => "imports.ftp_host",
			"display"  => "Ftp Host",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"FtpUser" => array(
			"type"     => VarTypes::STRING,
			"name"     => "ftp_user",
			"field"    => "imports.ftp_user",
			"display"  => "Ftp User",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"FtpPassword" => array(
			"type"     => VarTypes::STRING,
			"name"     => "ftp_password",
			"field"    => "imports.ftp_password",
			"display"  => "Ftp Password",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"FtpRemoteDirectory" => array(
			"type"     => VarTypes::STRING,
			"name"     => "ftp_remote_directory",
			"field"    => "imports.ftp_remote_directory",
			"display"  => "Ftp Remote Directory",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"UrlLink" => array(
			"type"     => VarTypes::STRING,
			"name"     => "url_link",
			"field"    => "imports.url_link",
			"display"  => "Url Link",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"CsvDelimeter" => array(
			"type"     => VarTypes::STRING,
			"name"     => "csv_delimeter",
			"field"    => "imports.csv_delimeter",
			"display"  => "Csv Delimeter",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"AutoRun" => array(
			"type"     => VarTypes::INT,
			"name"     => "auto_run",
			"field"    => "imports.auto_run",
			"display"  => "Auto Run",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"FileType" => array(
			"type"     => VarTypes::INT,
			"name"     => "file_type",
			"field"    => "imports.file_type",
			"display"  => "File Type",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"MinStock" => array(
			"type"     => VarTypes::INT,
			"name"     => "min_stock",
			"field"    => "imports.min_stock",
			"display"  => "Min Stock",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 )
	);

	protected $Relationships = array(
		array(
			"access_field" => "AttributesInfo",
			"view_name"    => "",
			"condition"    => "ImportAttribute.ImportId = Id",
			"condition2"   => "",
			"condition3"   => "",
			"type"         => Relation::ONE_TO_MANY,
			"reverse_field"=> "",
			"behaviour"    => RelationBehaviour::CASCADE_DELETE)
	);
}