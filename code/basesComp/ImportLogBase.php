<?php
abstract class ImportLogBase extends ModelBase
{
	public $Id = 0;
	public $ImportId = 0;
	public $AddedProductCount = 0;
	public $UpdatedProductCount = 0;
	public $SkipProductCount = 0;
	public $EmptyEanCount = 0;
	public $IncorrectEanCount = 0;
	public $StartedTime = "0000-00-00 00:00:00";
	public $FinishedTime = "0000-00-00 00:00:00";
	public $UserId = 0;
	public $CreatedTime = "0000-00-00 00:00:00";
	public $ImportName = "";
	public $ProjectName = "";
	public $CompanyId = 0;
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

abstract class ImportLogDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT /*fields*/   T1.*,T2.name AS import_name,T3.name AS project_name,T2.company_id,T2.project_id /*fields*/ FROM import_logs T1 LEFT JOIN imports T2 ON T1.import_id=T2.id LEFT JOIN projects T3 ON T3.id=T2.project_id WHERE (1=1) ORDER BY (1)';
}

class ImportLogModelMap extends ModelMap
{
	public $Name = 'import_logs';
	public $ModelName = 'ImportLog';
	protected $DbFields = array(
		"Id"=>array(1006,"id","T1.id","Id","int",0,1,0,0),
		"ImportId"=>array(1006,"import_id","T1.import_id","Import Id","int",0,1,0,0),
		"AddedProductCount"=>array(1006,"added_product_count","T1.added_product_count","Added Product Count","int",0,1,0,0),
		"UpdatedProductCount"=>array(1006,"updated_product_count","T1.updated_product_count","Updated Product Count","int",0,1,0,0),
		"SkipProductCount"=>array(1006,"skip_product_count","T1.skip_product_count","Skip Product Count","int",0,1,0,0),
		"EmptyEanCount"=>array(1006,"empty_ean_count","T1.empty_ean_count","Empty Ean Count","int",0,1,0,0),
		"IncorrectEanCount"=>array(1006,"incorrect_ean_count","T1.incorrect_ean_count","Incorrect Ean Count","int",0,1,0,0),
		"StartedTime"=>array(1003,"started_time","T1.started_time","Started Time","datetime",0,1,0,"0000-00-00 00:00:00"),
		"FinishedTime"=>array(1003,"finished_time","T1.finished_time","Finished Time","datetime",0,1,0,"0000-00-00 00:00:00"),
		"UserId"=>array(1006,"user_id","T1.user_id","User Id","int",0,1,0,0),
		"CreatedTime"=>array(1003,"created_time","T1.created_time","Created Time","datetime",0,1,0,"0000-00-00 00:00:00"),
		"ImportName"=>array(1002,"import_name","T2.name","Import Name","string",0,0,0,""),
		"ProjectName"=>array(1002,"project_name","T3.name","Project Name","string",0,0,0,""),
		"CompanyId"=>array(1006,"company_id","T2.company_id","Company Id","int",0,0,0,0),
		"ProjectId"=>array(1006,"project_id","T2.project_id","Project Id","int",0,0,0,0),
		"CsvDosya"=>array(1002,"csv_dosya","T1.csv_dosya","Csv Dosya","AppFile",0,1,0,"")
	);
}