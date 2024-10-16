<?php
/**
 * @property AppFileList $PhotoIds
 */
abstract class VendorProductBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var int FieldName = vendor_id                               */
	public $VendorId = 0;

	/** @var int FieldName = product_id                              */
	public $ProductId = 0;

	/** @var string FieldName = ean                                  */
	public $Ean = "";

	/** @var string FieldName = sku                                  */
	public $Sku = "";

	/** @var int FieldName = stock                                   */
	public $Stock = 0;

	/** @var float FieldName = price                                 */
	public $Price = 0;

	/** @var string FieldName = delivery                             */
	public $Delivery = "";

	/** @var string FieldName = title                                */
	public $Title = "";

	/** @var string FieldName = description                          */
	public $Description = "";

	/** @var string FieldName = photo_url                            */
	public $PhotoUrl = "";

	/** @var int FieldName = added_import_id                         */
	public $AddedImportId = 0;

	/** @var datetime FieldName = added_time                         */
	public $AddedTime = 'current_timestamp()';

	/** @var int FieldName = photo_processed                         */
	public $PhotoProcessed = 0;

	/** @var string FieldName = vendor_name                          */
	public $VendorName = "";

	private $PhotoIds = "";

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
 * @method VendorProduct GetById(int $id, bool $AutoCreate = false)
 * @method VendorProduct GetFirst(array|object $params = array())
 * @method VendorProduct[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method VendorProductDb SetOrderByExp(string $customStr)
 */
abstract class VendorProductDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT
		/*fields*/
		  T1.*,T2.name_ AS vendor_name
		/*fields*/
		FROM vendor_products T1
		INNER JOIN vendors T2 ON T1.vendor_id=T2.id
		WHERE (1=1)
		ORDER BY (1)
	';
}

class VendorProductModelMap extends ModelMap
{
	public $Name = 'vendor_products';
	public $ModelName = 'VendorProduct';

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
		"VendorId" => array(
			"type"     => VarTypes::INT,
			"name"     => "vendor_id",
			"field"    => "T1.vendor_id",
			"display"  => "Vendor Id",
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
		"Ean" => array(
			"type"     => VarTypes::STRING,
			"name"     => "ean",
			"field"    => "T1.ean",
			"display"  => "Ean",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Sku" => array(
			"type"     => VarTypes::STRING,
			"name"     => "sku",
			"field"    => "T1.sku",
			"display"  => "Sku",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Stock" => array(
			"type"     => VarTypes::INT,
			"name"     => "stock",
			"field"    => "T1.stock",
			"display"  => "Stock",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Price" => array(
			"type"     => VarTypes::FLOAT,
			"name"     => "price",
			"field"    => "T1.price",
			"display"  => "Price",
			"model"    => "float",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Delivery" => array(
			"type"     => VarTypes::STRING,
			"name"     => "delivery",
			"field"    => "T1.delivery",
			"display"  => "Delivery",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Title" => array(
			"type"     => VarTypes::STRING,
			"name"     => "title",
			"field"    => "T1.title",
			"display"  => "Title",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Description" => array(
			"type"     => VarTypes::STRING,
			"name"     => "description",
			"field"    => "T1.description",
			"display"  => "Description",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"PhotoUrl" => array(
			"type"     => VarTypes::STRING,
			"name"     => "photo_url",
			"field"    => "T1.photo_url",
			"display"  => "Photo Url",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"AddedImportId" => array(
			"type"     => VarTypes::INT,
			"name"     => "added_import_id",
			"field"    => "T1.added_import_id",
			"display"  => "Added Import Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"AddedTime" => array(
			"type"     => VarTypes::DATETIME,
			"name"     => "added_time",
			"field"    => "T1.added_time",
			"display"  => "Added Time",
			"model"    => "datetime",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 'current_timestamp()' ),
		"PhotoProcessed" => array(
			"type"     => VarTypes::INT,
			"name"     => "photo_processed",
			"field"    => "T1.photo_processed",
			"display"  => "Photo Processed",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"VendorName" => array(
			"type"     => VarTypes::STRING,
			"name"     => "vendor_name",
			"field"    => "T2.name_",
			"display"  => "Vendor Name",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"PhotoIds" => array(
			"type"     => VarTypes::STRING,
			"name"     => "photo_ids",
			"field"    => "T1.photo_ids",
			"display"  => "Photo Ids",
			"model"    => "AppFileList",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" )
	);
}