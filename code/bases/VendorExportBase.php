<?php
/**
 * @property ExportRule[] $RulesInfo
 */
abstract class VendorExportBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var string FieldName = name_                                */
	public $Name = "";

	/** @var string FieldName = vendor_ids                           */
	public $VendorIds = "";

	/** @var string FieldName = fields                               */
	public $Fields = '';

	/** @var int FieldName = working_frequency                       */
	public $WorkingFrequency = 0;

	/** @var datetime FieldName = last_working_time                  */
	public $LastWorkingTime = "0000-00-00 00:00:00";

	/** @var string FieldName = file_path                            */
	public $FilePath = "";

	/** @var int FieldName = min_stock                               */
	public $MinStock = 0;

	/** @var int FieldName = type_                                   */
	public $Type = 0;

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
 * @method VendorExport GetById(int $id, bool $AutoCreate = false)
 * @method VendorExport GetFirst(array|object $params = array())
 * @method VendorExport[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method VendorExportDb SetOrderByExp(string $customStr)
 */
abstract class VendorExportDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT
		/*fields*/
		  *
		/*fields*/
		FROM vendor_exports
		WHERE (1=1)
		ORDER BY (1)
	';
}

class VendorExportModelMap extends ModelMap
{
	public $Name = 'vendor_exports';
	public $ModelName = 'VendorExport';

	protected $DbFields = array(
		"Id" => array(
			"type"     => VarTypes::INT,
			"name"     => "id",
			"field"    => "vendor_exports.id",
			"display"  => "Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Name" => array(
			"type"     => VarTypes::STRING,
			"name"     => "name_",
			"field"    => "vendor_exports.name_",
			"display"  => "Name",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"VendorIds" => array(
			"type"     => VarTypes::STRING,
			"name"     => "vendor_ids",
			"field"    => "vendor_exports.vendor_ids",
			"display"  => "Vendor Ids",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Fields" => array(
			"type"     => VarTypes::STRING,
			"name"     => "fields",
			"field"    => "vendor_exports.fields",
			"display"  => "Fields",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => '' ),
		"WorkingFrequency" => array(
			"type"     => VarTypes::INT,
			"name"     => "working_frequency",
			"field"    => "vendor_exports.working_frequency",
			"display"  => "Working Frequency",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"LastWorkingTime" => array(
			"type"     => VarTypes::DATETIME,
			"name"     => "last_working_time",
			"field"    => "vendor_exports.last_working_time",
			"display"  => "Last Working Time",
			"model"    => "datetime",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "0000-00-00 00:00:00" ),
		"FilePath" => array(
			"type"     => VarTypes::STRING,
			"name"     => "file_path",
			"field"    => "vendor_exports.file_path",
			"display"  => "File Path",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"MinStock" => array(
			"type"     => VarTypes::INT,
			"name"     => "min_stock",
			"field"    => "vendor_exports.min_stock",
			"display"  => "Min Stock",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Type" => array(
			"type"     => VarTypes::INT,
			"name"     => "type_",
			"field"    => "vendor_exports.type_",
			"display"  => "Type",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 )
	);

	protected $Relationships = array(
		array(
			"access_field" => "RulesInfo",
			"view_name"    => "",
			"condition"    => "ExportRule.ExportId = Id",
			"condition2"   => "",
			"condition3"   => "",
			"type"         => Relation::ONE_TO_MANY,
			"reverse_field"=> "VendorExportInfo",
			"behaviour"    => RelationBehaviour::CASCADE_DELETE)
	);
}