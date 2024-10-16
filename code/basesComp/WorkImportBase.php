<?php
abstract class WorkImportBase extends ModelBase
{
	public $Id = 0;
	public $ImportId = 0;
	public $AddedDate = "0000-00-00 00:00:00";
	public $ImportLogId = 0;
}

abstract class WorkImportDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT /*fields*/   * /*fields*/ FROM work_imports WHERE (1=1) ORDER BY (1)';
}

class WorkImportModelMap extends ModelMap
{
	public $Name = 'work_imports';
	public $ModelName = 'WorkImport';
	protected $DbFields = array(
		"Id"=>array(1006,"id","work_imports.id","Id","int",0,1,0,0),
		"ImportId"=>array(1006,"import_id","work_imports.import_id","Import Id","int",0,1,0,0),
		"AddedDate"=>array(1003,"added_date","work_imports.added_date","Added Date","datetime",0,1,0,"0000-00-00 00:00:00"),
		"ImportLogId"=>array(1006,"import_log_id","work_imports.import_log_id","Import Log Id","int",0,1,0,0)
	);
}