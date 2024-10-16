<?php
abstract class VendorProductBase extends ModelBase
{
	public $Id = 0;
	public $VendorId = 0;
	public $ProductId = 0;
	public $Ean = "";
	public $Sku = "";
	public $Stock = 0;
	public $Price = 0;
	public $Delivery = "";
	public $Title = "";
	public $Description = "";
	public $PhotoUrl = "";
	public $AddedImportId = 0;
	public $AddedTime = 'current_timestamp()';
	public $PhotoProcessed = 0;
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

abstract class VendorProductDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT /*fields*/   T1.*,T2.name_ AS vendor_name /*fields*/ FROM vendor_products T1 INNER JOIN vendors T2 ON T1.vendor_id=T2.id WHERE (1=1) ORDER BY (1)';
}

class VendorProductModelMap extends ModelMap
{
	public $Name = 'vendor_products';
	public $ModelName = 'VendorProduct';
	protected $DbFields = array(
		"Id"=>array(1006,"id","T1.id","Id","int",0,1,0,0),
		"VendorId"=>array(1006,"vendor_id","T1.vendor_id","Vendor Id","int",0,1,0,0),
		"ProductId"=>array(1006,"product_id","T1.product_id","Product Id","int",0,1,0,0),
		"Ean"=>array(1002,"ean","T1.ean","Ean","string",0,1,0,""),
		"Sku"=>array(1002,"sku","T1.sku","Sku","string",0,1,0,""),
		"Stock"=>array(1006,"stock","T1.stock","Stock","int",0,1,0,0),
		"Price"=>array(1007,"price","T1.price","Price","float",0,1,0,0),
		"Delivery"=>array(1002,"delivery","T1.delivery","Delivery","string",0,1,0,""),
		"Title"=>array(1002,"title","T1.title","Title","string",0,1,0,""),
		"Description"=>array(1002,"description","T1.description","Description","string",0,1,0,""),
		"PhotoUrl"=>array(1002,"photo_url","T1.photo_url","Photo Url","string",0,1,0,""),
		"AddedImportId"=>array(1006,"added_import_id","T1.added_import_id","Added Import Id","int",0,1,0,0),
		"AddedTime"=>array(1003,"added_time","T1.added_time","Added Time","datetime",0,1,0,'current_timestamp()'),
		"PhotoProcessed"=>array(1006,"photo_processed","T1.photo_processed","Photo Processed","int",0,1,0,0),
		"VendorName"=>array(1002,"vendor_name","T2.name_","Vendor Name","string",0,0,0,""),
		"PhotoIds"=>array(1002,"photo_ids","T1.photo_ids","Photo Ids","AppFileList",0,1,0,"")
	);
}