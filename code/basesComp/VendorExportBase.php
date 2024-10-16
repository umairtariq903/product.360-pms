<?php
abstract class VendorExportBase extends ModelBase
{
	public $Id = 0;
	public $Name = "";
	public $VendorIds = "";
	public $Fields = '';
	public $WorkingFrequency = 0;
	public $LastWorkingTime = "0000-00-00 00:00:00";
	public $FilePath = "";
	public $MinStock = 0;
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

abstract class VendorExportDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT /*fields*/   * /*fields*/ FROM vendor_exports WHERE (1=1) ORDER BY (1)';
}

class VendorExportModelMap extends ModelMap
{
	public $Name = 'vendor_exports';
	public $ModelName = 'VendorExport';
	protected $DbFields = array(
		"Id"=>array(1006,"id","vendor_exports.id","Id","int",0,1,0,0),
		"Name"=>array(1002,"name_","vendor_exports.name_","Name","string",0,1,0,""),
		"VendorIds"=>array(1002,"vendor_ids","vendor_exports.vendor_ids","Vendor Ids","string",0,1,0,""),
		"Fields"=>array(1002,"fields","vendor_exports.fields","Fields","string",0,1,0,''),
		"WorkingFrequency"=>array(1006,"working_frequency","vendor_exports.working_frequency","Working Frequency","int",0,1,0,0),
		"LastWorkingTime"=>array(1003,"last_working_time","vendor_exports.last_working_time","Last Working Time","datetime",0,1,0,"0000-00-00 00:00:00"),
		"FilePath"=>array(1002,"file_path","vendor_exports.file_path","File Path","string",0,1,0,""),
		"MinStock"=>array(1006,"min_stock","vendor_exports.min_stock","Min Stock","int",0,1,0,0),
		"Type"=>array(1006,"type_","vendor_exports.type_","Type","int",0,1,0,0)
	);
	protected $Relationships = array(
		array("access_field"=>"RulesInfo","view_name"=>"","condition"=>"ExportRule.ExportId = Id","condition2"=>"","condition3"=>"","type"=>Relation::ONE_TO_MANY,"reverse_field"=>"VendorExportInfo","behaviour"=>RelationBehaviour::CASCADE_DELETE)
	);
}