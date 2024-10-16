<?php
abstract class ImportBase extends ModelBase
{
	public $Id = 0;
	public $VendorId = 0;
	public $CompanyId = 0;
	public $ProjectId = 0;
	public $ImportType = 0;
	public $Name = "";
	public $SpKey = "";
	public $FromAt = 0;
	public $PFieldKeys = "";
	public $PFieldWhereKeys = "";
	public $FtpHost = "";
	public $FtpUser = "";
	public $FtpPassword = "";
	public $FtpRemoteDirectory = "";
	public $UrlLink = "";
	public $CsvDelimeter = "";
	public $AutoRun = 0;
	public $FileType = 0;
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

abstract class ImportDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT /*fields*/   * /*fields*/ FROM imports WHERE (1=1) ORDER BY (1)';
}

class ImportModelMap extends ModelMap
{
	public $Name = 'imports';
	public $ModelName = 'Import';
	protected $DbFields = array(
		"Id"=>array(1006,"id","imports.id","Id","int",0,1,0,0),
		"VendorId"=>array(1006,"vendor_id","imports.vendor_id","Vendor Id","int",0,1,0,0),
		"CompanyId"=>array(1006,"company_id","imports.company_id","Company Id","int",0,1,0,0),
		"ProjectId"=>array(1006,"project_id","imports.project_id","Project Id","int",0,1,0,0),
		"ImportType"=>array(1006,"import_type","imports.import_type","Import Type","int",0,1,0,0),
		"Name"=>array(1002,"name","imports.name","Name","string",0,1,0,""),
		"SpKey"=>array(1002,"sp_key","imports.sp_key","Sp Key","string",0,1,0,""),
		"FromAt"=>array(1006,"from_at","imports.from_at","From At","int",0,1,0,0),
		"PFieldKeys"=>array(1002,"p_field_keys","imports.p_field_keys","P Field Keys","string",0,1,0,""),
		"PFieldWhereKeys"=>array(1002,"p_field_where_keys","imports.p_field_where_keys","P Field Where Keys","string",0,1,0,""),
		"FtpHost"=>array(1002,"ftp_host","imports.ftp_host","Ftp Host","string",0,1,0,""),
		"FtpUser"=>array(1002,"ftp_user","imports.ftp_user","Ftp User","string",0,1,0,""),
		"FtpPassword"=>array(1002,"ftp_password","imports.ftp_password","Ftp Password","string",0,1,0,""),
		"FtpRemoteDirectory"=>array(1002,"ftp_remote_directory","imports.ftp_remote_directory","Ftp Remote Directory","string",0,1,0,""),
		"UrlLink"=>array(1002,"url_link","imports.url_link","Url Link","string",0,1,0,""),
		"CsvDelimeter"=>array(1002,"csv_delimeter","imports.csv_delimeter","Csv Delimeter","string",0,1,0,""),
		"AutoRun"=>array(1006,"auto_run","imports.auto_run","Auto Run","int",0,1,0,0),
		"FileType"=>array(1006,"file_type","imports.file_type","File Type","int",0,1,0,0),
		"MinStock"=>array(1006,"min_stock","imports.min_stock","Min Stock","int",0,1,0,0)
	);
	protected $Relationships = array(
		array("access_field"=>"AttributesInfo","view_name"=>"","condition"=>"ImportAttribute.ImportId = Id","condition2"=>"","condition3"=>"","type"=>Relation::ONE_TO_MANY,"reverse_field"=>"","behaviour"=>RelationBehaviour::CASCADE_DELETE)
	);
}